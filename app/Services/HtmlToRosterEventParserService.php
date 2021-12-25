<?php

namespace App\Services;

use App\Enums\RosterEventHeaders;
use App\Models\ExtraActivities;
use App\Models\RosterEvent;
use DateTime;
use DOMDocument;
use DOMXPath;

class HtmlToRosterEventParserService
{

    /**
     * @param string $fileName
     * @return bool|null
     * @throws \Exception
     */
    public function parseHtmlToRosterEvent(string $fileName): ?bool
    {
        if (!file_exists($fileName) || !is_readable($fileName)) {
            return null;
        }

        $rosterEventsArray = $this->htmlTableInColumnArray($fileName);
        $this->saveArrayToRosterEvent($rosterEventsArray);
        return true;
    }

    /**
     * Read the html file and make in into an array. Each row contains the content to make a RosterEvent.
     *
     * @param string $htmlFile
     * @return array
     * @throws \Exception
     */
    private function htmlTableInColumnArray(string $htmlFile): array
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTMLFile($htmlFile);
        $content = new DOMXpath($dom);

        $headers = [];
        $columns = [];
        $date = "";

        foreach ($content->query('//tr') as $rowKey => $tr) {
            $trDom = new DOMDocument();
            $trDom->loadHTML($tr->C14N());
            $tr = new DOMXpath($trDom);

            // Set the headers
            foreach ($tr->query('//td') as $columnKey => $td) {
                if($rowKey !== 0){
                    continue;
                }

                $value = strtolower($td->nodeValue);

                if(!$this->findCorrectColumn($value)){
                    continue;
                }

                if($value === RosterEventHeaders::STDZ){
                    $value = RosterEventHeaders::DEPARTURE_TIME;
                }

                if($value === RosterEventHeaders::STAZ){
                    $value = RosterEventHeaders::ARRIVAL_TIME;
                }

                $headers[$columnKey] = $value;
            }

            // Set the body
            foreach ($tr->query('//td') as $columnKey => $td) {
                if($rowKey === 0){
                    continue;
                }

                if(!isset($headers[$columnKey])){
                    continue;
                }

                // Save the day till another day appears.
                if($headers[$columnKey] === RosterEventHeaders::DATE && $td->nodeValue !== ""){
                    $date = $td->nodeValue;
                }

                // Set the date at the date column.
                if($headers[$columnKey] === RosterEventHeaders::DATE){
                    $datetime = $this->dayToDateTime($date);
                    if($datetime === null){
                        continue;
                    }

                    $columns[$rowKey][$headers[$columnKey]] = $datetime;
                    continue;
                }

                $columns[$rowKey][$headers[$columnKey]] = $td->nodeValue;
            }
        }

        // Filter only the events that have date key.
        return array_filter($columns, function($item){
            return isset($item[RosterEventHeaders::DATE]);
        });
    }

    /**
     * Get the constants of RosterEventHeaders to get the correct headers.
     *
     * @param $value
     * @return bool
     */
    private function findCorrectColumn($value): bool
    {
        $reflectionClass = new \ReflectionClass(RosterEventHeaders::class);
        return in_array($value, $reflectionClass->getConstants());
    }

    /**
     * @param string $date
     * @return DateTime|null
     * @throws \Exception
     */
    private function dayToDateTime(string $date): ?DateTime
    {
        // Remove everything in string expect the numbers
        $day = preg_replace('/[^0-9]/', '', $date);

        // Check if there are only 2 numbers
        if(!preg_match('/^[0-3][0-9]$/', $day)){
            return null;
        }

        return new DateTime("2022-01-" . $day);
    }

    /**
     * @param array $rosterEventsArray
     */
    private function saveArrayToRosterEvent(array $rosterEventsArray)
    {
        $rosterEvents = [];
        $extraActivities = [];
        foreach ($rosterEventsArray as $key => $rosterEvent) {
            $rosterEvents[$key] = new RosterEvent($rosterEvent);
            $extraActivities[$key] = $this->extractActivities($rosterEvent);
        }

        $rosterEvents = collect($rosterEvents);

        $rosterEvents->each(function($item, $key) use ($extraActivities) {
            $item->save();
            foreach ($extraActivities[$key] as $extraActivity){
                $item->extraActivities()->save(new ExtraActivities(['name' => $extraActivity]));
            }
        });
    }

    /**
     * Get an array of extra activities
     *
     * @param array $event
     * @return array|string[]
     */
    private function extractActivities(array $event): array
    {
        $extractActivities = [];
        if($event[RosterEventHeaders::ACTIVITY] == "OFF"){
            $extractActivities[] = "DO";
        }

        if($event[RosterEventHeaders::ACTIVITY] == "SBY"){
            $extractActivities[] = "SBY";
        }

        if($this->checkFlightActivity($event[RosterEventHeaders::ACTIVITY])){
            $extractActivities[] = "FLT";
        }

        if($this->checkTime($event[RosterEventHeaders::CHECK_IN_TIME])){
            $extractActivities[] = "CI";
        }

        if($this->checkTime($event[RosterEventHeaders::CHECK_OUT_TIME])){
            $extractActivities[] = "CO";
        }

        if(!empty($extractActivities)){
            return $extractActivities;
        }

        return ["UNK"];
    }

    /**
     * Regex to see if the activity is a flight
     * Criteria: DX + at least 2 numbers
     *
     * @param string $event
     * @return false|int
     */
    private function checkFlightActivity(string $event)
    {
        return preg_match('/^DX[0-9]{2,}$/', $event);
    }

    /**
     * Check if the time is correct
     * Criteria: Needs to be 4 numbers.
     *
     * @param string $event
     * @return false|int
     */
    private function checkTime(string $event)
    {
        return preg_match('/^[0-9]{4}$/', $event);
    }

}

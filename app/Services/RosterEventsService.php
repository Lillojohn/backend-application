<?php

namespace App\Services;

use App\Models\RosterEvent;
use App\Repository\RosterEventsRepository;
use \Illuminate\Support\Collection;

class RosterEventsService
{
    /**
     * @var RosterEventsRepository
     */
    private RosterEventsRepository $rosterEventsRepository;

    public function __construct(RosterEventsRepository $rosterEventsRepository){

        $this->rosterEventsRepository = $rosterEventsRepository;
    }

    public function getAll(): Collection
    {
        return RosterEvent::with('extraActivities')->get();
    }

    /**
     * Get all Roster events between two dates. Check if the $startDate and $endDate is correct.
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection|null
     */
    public function getAllEventsBetweenDates(string $startDate, string $endDate): ?Collection
    {
        if(!$this->checkDate($startDate)){
            return null;
        }

        if(!$this->checkDate($endDate)){
            return null;
        }

        $rosterEvents = $this->rosterEventsRepository->getAllEventsBetweenDates($startDate, $endDate);

        return $this->addExtraActivities($rosterEvents);
    }

    /**
     * @param string $startDate
     * @return Collection
     */
    public function getFlightsForNextWeek(string $startDate): Collection
    {
        return $this->rosterEventsRepository->getFlightsForNextWeek($startDate);
    }

    /**
     * @param string $startDate
     * @return Collection
     */
    public function getStandbyEventsForNextWeek(string $startDate): Collection
    {
        return $this->rosterEventsRepository->getStandbyEventsForNextWeek($startDate);
    }

    /**
     * @param string $location
     * @return Collection
     */
    public function getFlightsByLocation(string $location): Collection
    {
        $rosterEvents = $this->rosterEventsRepository->getFlightsByLocation($location);

        return $this->addExtraActivities($rosterEvents);
    }

    public function generateRosterByHtmlFile(string $location){

    }

    /**
     *
     * Check if date is in format yyyy-mm-dd
     *
     * @param string $date
     * @return false|int
     */
    private function checkDate(string $date): bool|int
    {
        return preg_match('/^\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$/', $date);
    }

    /**
     *
     * Add the extra activities to the roster event.
     *
     * @param Collection $rosterEvents
     * @return Collection
     */
    private function addExtraActivities(Collection $rosterEvents): Collection
    {
        return $rosterEvents->map(function($item, $key){
            return RosterEvent::with('extraActivities')->find($item->id);
        });
    }
}

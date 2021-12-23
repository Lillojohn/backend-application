<?php

namespace App\Repository;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RosterEventsRepository
{
    const TABLE = 'roster_events';

    /**
     * Get the roster events between two dates.
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getAllEventsBetweenDates(string $startDate, string $endDate): Collection
    {
        return DB::table(self::TABLE)
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->get();
    }

    /**
     * @param string $startDate
     * @return Collection
     */
    public function getFlightsForNextWeek(string $startDate): Collection
    {
        return $this->getQueryFoNextWeek($startDate, 'FLT');
    }

    /**
     * @param string $startDate
     * @return Collection
     */
    public function getStandbyEventsForNextWeek(string $startDate): Collection
    {
        return $this->getQueryFoNextWeek($startDate, 'SBY');
    }

    /**
     * Query to get the next week with a where statement for the extra activity
     *
     * @param string $startDate
     * @param string $extraActivityName
     * @return Collection
     */
    private function getQueryFoNextWeek(string $startDate, string $extraActivityName): Collection
    {
        $date = strtotime($startDate);
        $endDate = strtotime("+7 day", $date);
        $endDate = date('Y-m-d', $endDate);
        return DB::table(self::TABLE)
            ->leftJoin('extra_activities', 'roster_events.id', '=', 'extra_activities.roster_event_id')
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->where('extra_activities.name', '=', $extraActivityName)
            ->get();
    }

    /**
     * @param string $location
     * @return Collection
     */
    public function getFlightsByLocation(string $location): Collection
    {
        return DB::table(self::TABLE)
            ->where('from', '=', $location)
            ->get();
    }
}

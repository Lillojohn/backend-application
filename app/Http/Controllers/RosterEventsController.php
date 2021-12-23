<?php

namespace App\Http\Controllers;

use App\Services\RosterEventsService;
use Illuminate\Support\Collection;

class RosterEventsController extends Controller
{
    /**
     * @var RosterEventsService
     */
    private RosterEventsService $rosterEventsService;

    public function __construct(RosterEventsService $rosterEventsService)
    {
        $this->rosterEventsService = $rosterEventsService;
    }

    /**
     * @return Collection
     */
    public function show(): Collection
    {
        return $this->rosterEventsService->getAll();
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @return Collection|string
     */
    public function getAllEventsBetweenDates(string $startDate, string $endDate): string|Collection
    {
        $rosterEvents = $this->rosterEventsService->getAllEventsBetweenDates($startDate, $endDate);
        if($rosterEvents === null)
        {
            return "Wrong format it should be : /dates/2022-01-11/2022-01-13";
        }

        return $rosterEvents;
    }

    /**
     * @param string|null $startDate
     * @return Collection
     */
    public function getFlightNextWeek(?string $startDate = '2022-01-14'): Collection
    {
        return $this->rosterEventsService->getFlightsForNextWeek($startDate);
    }

    /**
     * @param string|null $startDate
     * @return Collection
     */
    public function getStandbyEventsForNextWeek(?string $startDate = '2022-01-14'): Collection
    {
        return $this->rosterEventsService->getStandbyEventsForNextWeek($startDate);
    }

    /**
     * @param string $location
     * @return Collection
     */
    public function getFlightsByLocation(string $location): Collection
    {
        return $this->rosterEventsService->getFlightsByLocation($location);
    }

}

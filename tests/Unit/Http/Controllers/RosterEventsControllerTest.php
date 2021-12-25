<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\RosterEventsController;
use App\Services\RosterEventsService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class RosterEventsControllerTest extends TestCase
{
    private MockObject $rosterEventsService;

    public function setUp(): void
    {
        $this->rosterEventsService = $this->createMock(RosterEventsService::class);
    }

    public function testShow()
    {
        $rosterEventsController = new RosterEventsController($this->rosterEventsService);

        $rosterEvents = $this->createCollectionOfRosterEvent();

        $this->rosterEventsService->expects($this->once())
            ->method('getAll')
            ->willReturn($rosterEvents)
            ;

       $this->assertSame($rosterEventsController->show(), $rosterEvents);
    }

    /**
     * @dataProvider GetAllEventsBetweenDatesDataProvider
     */
    public function testGetAllEventsBetweenDates(?Collection $rosterEvents, string|Collection $expected){
        $rosterEventsController = new RosterEventsController($this->rosterEventsService);

        $this->rosterEventsService->expects($this->once())
            ->method('getAllEventsBetweenDates')
            ->with('foo', 'bar')
            ->willReturn($rosterEvents)
        ;

        $this->assertSame($rosterEventsController->getAllEventsBetweenDates('foo', 'bar'), $expected);
    }

    /**
     * @return array
     */
    private function GetAllEventsBetweenDatesDataProvider(): array
    {
        $rosterEvent = $this->createCollectionOfRosterEvent();
        return [
            [null, 'Wrong format it should be : /dates/2022-01-11/2022-01-13'],
            [$rosterEvent, $rosterEvent],
        ];
    }

    public function testGetFlightNextWeek()
    {
        $rosterEventsController = new RosterEventsController($this->rosterEventsService);

        $rosterEvents = $this->createCollectionOfRosterEvent();

        $this->rosterEventsService->expects($this->once())
            ->method('getFlightsForNextWeek')
            ->willReturn($rosterEvents)
        ;

       $this->assertSame($rosterEventsController->getFlightNextWeek(), $rosterEvents);
    }

    public function testGetStandbyEventsForNextWeek()
    {
        $rosterEventsController = new RosterEventsController($this->rosterEventsService);

        $rosterEvents = $this->createCollectionOfRosterEvent();

        $this->rosterEventsService->expects($this->once())
            ->method('getStandbyEventsForNextWeek')
            ->willReturn($rosterEvents)
        ;

       $this->assertSame($rosterEventsController->getStandbyEventsForNextWeek(), $rosterEvents);
    }

    public function testGetFlightsByLocation()
    {
        $rosterEventsController = new RosterEventsController($this->rosterEventsService);

        $rosterEvents = $this->createCollectionOfRosterEvent();

        $this->rosterEventsService->expects($this->once())
            ->method('getFlightsByLocation')
            ->willReturn($rosterEvents)
        ;

        $this->assertSame($rosterEventsController->getFlightsByLocation('SCR'), $rosterEvents);
    }

    private function createCollectionOfRosterEvent(): Collection
    {
        return collect([
            "id"=> 2,
            "date"=> "2022-01-10 00:00:00",
            "activity"=> "DX80",
            "from"=> "CPH",
            "to"=> "KRP",
            "arrival_time"=> "1435",
            "departure_time"=> "1345",
            "created_at"=> "2021-12-23T06:26:49.000000Z",
            "updated_at"=> "2021-12-23T06:26:49.000000Z"
        ]);
    }
}

<?php

namespace Tests\Unit\Services;

use App\Models\RosterEvent;
use App\Repository\RosterEventsRepository;
use App\Services\RosterEventsService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;
use voku\helper\ASCII;

class RosterEventsServiceTest extends TestCase
{
    private MockObject $rosterEventsRepository;
    private RosterEventsService $rosterEventsService;

    public function setUp(): void
    {
        parent::SetUp();
        $this->rosterEventsRepository = $this->createMock(RosterEventsRepository::class);
        $this->rosterEventsService = new RosterEventsService($this->rosterEventsRepository);
    }

    public function testGetAll(){
        $this->assertInstanceOf(Collection::class, $this->rosterEventsService->getAll());
    }

    /**
     * @dataProvider GetAlLEventsBetweenDatesDataProvider
     */
    public function testGetAllEventsBetweenDates(string $startDate, string $endDate, int $invoked, ?string $expected){
        $collection = $this->createCollectionOfRosterEvent();

        $this->rosterEventsRepository->expects($this->exactly($invoked))
            ->method('getAllEventsBetweenDates')
            ->with('2022-01-01', '2022-01-14')
            ->willReturn($collection)
        ;

        $this->assertTrue(
            gettype($this->rosterEventsService->getAllEventsBetweenDates($startDate, $endDate)) === $expected
        );
    }

    private function GetAlLEventsBetweenDatesDataProvider(){
        return [
            ['foo', '2022-01-01', 0, "NULL"],
            ['2022-01-01', 'foo', 0, "NULL"],
            ['2022-01-01', '2022-01-14', 1, "object"],
        ];
    }

    public function testGetFlightsForNextWeek(){
        $collection = $this->createCollectionOfRosterEvent();

        $this->rosterEventsRepository->expects($this->once())
            ->method('getFlightsForNextWeek')
            ->with('2022-01-14')
            ->willReturn($collection)
            ;

        $this->assertSame($collection, $this->rosterEventsService->getFlightsForNextWeek('2022-01-14'));
    }

    public function testGetStandbyEventsForNextWeek(){
        $collection = $this->createCollectionOfRosterEvent();

        $this->rosterEventsRepository->expects($this->once())
            ->method('getStandbyEventsForNextWeek')
            ->with('2022-01-14')
            ->willReturn($collection)
        ;

        $this->assertSame($collection, $this->rosterEventsService->getStandbyEventsForNextWeek('2022-01-14'));
    }

    public function testGetFlightsByLocation(){
        $collection = $this->createCollectionOfRosterEvent();

        $this->rosterEventsRepository->expects($this->once())
            ->method('getFlightsByLocation')
            ->with('2022-01-14')
            ->willReturn($collection)
        ;

        $this->rosterEventsService->getFlightsByLocation('2022-01-14');
    }

    private function createCollectionOfRosterEvent(): Collection
    {
        return collect([
            (object) [
                "id"=> 1,
                "date"=> "2022-01-10 00:00:00",
                "activity"=> "DX77",
                "from"=> "KRP",
                "to"=> "CPH",
                "arrival_time"=> "0935",
                "departure_time"=> "0845",
                "created_at"=> "2021-12-23T06:26:49.000000Z",
                "updated_at"=> "2021-12-23T06:26:49.000000Z"
            ]
        ]);
    }
}

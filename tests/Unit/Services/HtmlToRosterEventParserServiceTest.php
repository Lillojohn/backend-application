<?php

namespace Tests\Unit\Services;

use App\Services\HtmlToRosterEventParserService;
use App\Services\RosterEventsService;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class HtmlToRosterEventParserServiceTest extends TestCase
{
    /**
     * @dataProvider ParseHtmlToRosterEventDataProvider
     */
    public function testParseHtmlToRosterEventWithWrongFilename(string $filename, ?bool $expected)
    {
        $htmlToRosterEventParserService = new htmlToRosterEventParserService();

        $this->assertSame($expected, $htmlToRosterEventParserService->parseHtmlToRosterEvent($filename));
    }

    private function  ParseHtmlToRosterEventDataProvider(): array
    {
        return [
            ["./afefeaeaefa", null],
            ["./Roster - CrewConnex.html", true],
            ["./TestRoster.html", true],
        ];
    }
}

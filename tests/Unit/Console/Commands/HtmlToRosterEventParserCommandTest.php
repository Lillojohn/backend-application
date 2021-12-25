<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

class HtmlToRosterEventParserCommandTest extends TestCase
{
    public function test_no_argument_is_given()
    {
        $this->expectException(RuntimeException::class);
        $this->artisan('command:html-to-roster-event-parser');
    }

    public function test_with_correct_file_name()
    {
        $this->artisan('command:html-to-roster-event-parser ./TestRoster.html')
            ->assertExitCode(0);
    }

    public function test_with_wrong_file_name()
    {
        $this->artisan('command:html-to-roster-event-parser ./bbbb')
            ->assertExitCode(1);
    }
}

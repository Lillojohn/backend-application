<?php

namespace App\Console\Commands;

use App\Services\HtmlToRosterEventParserService;
use Illuminate\Console\Command;

class HtmlToRosterEventParserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:html-to-roster-event-parser {fileName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch a Html file and parse it to Roster Event';

    /**
     * @var HtmlToRosterEventParserService
     */
    private HtmlToRosterEventParserService $htmlToRosterEventParserService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(HtmlToRosterEventParserService $htmlToRosterEventParserService)
    {
        parent::__construct();
        $this->htmlToRosterEventParserService = $htmlToRosterEventParserService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle(): int
    {
        $roster = $this->htmlToRosterEventParserService->parseHtmlToRosterEvent($this->argument('fileName'));

        if ($roster === null){
            $this->error("file not found");
            return 1;
        }
        $this->info("Added new roster events");
        return 0;
    }
}

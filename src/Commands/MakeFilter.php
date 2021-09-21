<?php

namespace Backfron\LaravelFinder\Commands;

use Illuminate\Console\Command;

class MakeFilter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:finder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new finder.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        return 0;
    }
}

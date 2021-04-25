<?php

namespace App\Console\Commands;

use App\Facades\Extension;
use Illuminate\Console\Command;

class ExtensionInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extension:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extension init';

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
     * @return mixed
     */
    public function handle()
    {
        Extension::init();
        $this->info('extension init successful');
    }
}

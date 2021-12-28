<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PrepareForTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prepare-for-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rest wopi test files to the original state';

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
        $this->info('Delete sushi cache');
        unlink(storage_path('framework/cache/sushi-app-models-file.sqlite'));

        $this->info('Delete .wopitest files');
        unlink(storage_path('app/public/test.wopitest'));

        $this->info('create new .wopitest files');
        touch(storage_path('app/public/test.wopitest'));


        return Command::SUCCESS;
    }
}

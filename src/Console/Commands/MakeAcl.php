<?php

namespace Reinforcement\Console\Commands;

use Illuminate\Console\Command;

class MakeAcl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:acl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold ACL resources and routes';

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
        copy(__DIR__.'/../../acl/config/acl.php', config_path('acl.php'));
        $this->copyMigrations();
    }

    protected function copyMigrations()
    {
        $sourceDir = __DIR__.'/../../acl/database/migrations/';
        $destinationDir = database_path('migrations');

        $files = scandir($sourceDir);

        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                if (!file_exists("$destinationDir/$file")) {
                    copy("$sourceDir/$file", "$destinationDir/$file");
                }
            }
        }
    }
}

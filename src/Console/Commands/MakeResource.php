<?php

namespace Reinforcement\Console\Commands;

class MakeResource extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:resource {resources*} {--module=*} {--migration=*} {--new-fields=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default scaffolding for resources in compliance with the Support module';

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
        $resources = $this->argument('resources');
        $module = $this->option('module');
        $migration = $this->option('migration');
        $newFields = $this->option('new-fields');

        if ($module) {
            $module = is_array($module) ? $module[0] : $module;
            $moduleDirectory = config('support.module.directory') . DIRECTORY_SEPARATOR . ucfirst($module);
            if (!file_exists($moduleDirectory)) {
                $this->error("The requested module '$moduleDirectory' does not exists!");
                return;
            }
        }


        $resources = !is_array($resources) ? [$resources] : $resources;

        foreach ($resources as $resource) {

            if (empty($newFields)) {
                $this->info('Updating resource: ' . $resource);
                $this->info('=================');

                $this->call('reinforcement:resource:model', ['resources' => $resource, '--module' => $module, '--migration' => $migration]);
                $this->call('reinforcement:resource:repository', ['resources' => $resource, '--module' => $module, '--migration' => $migration]);
                // $this->call('reinforcement:resource:schema', ['resources' => $resource, '--module' => $module]);
                $this->call('reinforcement:resource:validator', ['resources' => $resource, '--module' => $module, '--migration' => $migration]);
                $this->call('reinforcement:resource:request', ['resources' => $resource, '--module' => $module, '--migration' => $migration]);
                $this->call('reinforcement:resource:controller', ['resources' => $resource, '--module' => $module]);
                $this->call('reinforcement:resource:route', ['resources' => $resource, '--module' => $module]);
                $this->call('reinforcement:resource:seeder', ['resources' => $resource, '--module' => $module, '--migration' => $migration]);

                if (empty($migration)) {
                    $this->call('reinforcement:resource:migration', ['resources' => $resource, '--module' => $module]);
                }
                $this->info('');

            } else {
                $this->info('Creating resource: ' . $resource);
                $this->info('=================');

                $this->call('reinforcement:resource:model', ['resources' => $resource, '--module' => $module, '--new-fields' => $newFields]);
                $this->call('reinforcement:resource:repository', ['resources' => $resource, '--module' => $module, '--new-fields' => $newFields]);

                $this->call('reinforcement:resource:validator', ['resources' => $resource, '--module' => $module, '--new-fields' => $newFields]);

                $this->call('reinforcement:resource:request', ['resources' => $resource, '--module' => $module, '--new-fields' => $newFields]);

                $this->call('reinforcement:resource:migration', ['resources' => $resource, '--module' => $module, '--new-fields' => $newFields]);
                $this->info('');

            }

        }

    }
}

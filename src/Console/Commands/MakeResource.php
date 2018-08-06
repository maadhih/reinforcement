<?php

namespace Reinforcement\Console\Commands;

class MakeResource extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinforcement:resource {resources*} {--migration=*} {--new-fields=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default scaffolding for resources';

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
        $migration = $this->option('migration');
        $newFields = $this->option('new-fields');

        $resources = (array) $resources;

        foreach ($resources as $resource) {

            if (empty($newFields)) {
                $this->info('Creating resource: ' . $resource);
                $this->info('=================');

                $options = [
                    'resources'   => $resource,
                    '--migration' => $migration,
                ];

                $this->call('reinforcement:controller', ['resources' => $resource]);
                $this->call('reinforcement:route', ['resources' => $resource]);

                $this->call('reinforcement:seeder', $options);

                // if (empty($migration)) {
                //     $this->call('reinforcement:migration',
                //         [
                //             'resources' => $resource
                //         ]);
                // }

            } else {
                $this->info('Updating resource: ' . $resource);
                $this->info('=================');

                $options = [
                    'resources'    => $resource,
                    '--new-fields' => $newFields,
                ];

                $this->call('reinforcement:migration', $options);
            }

            $this->call('reinforcement:model', $options);

            $this->call('reinforcement:repository', $options);

            $this->call('reinforcement:validator', $options);

            $this->call('reinforcement:request', $options);


            $this->info('Done');

        }

    }
}

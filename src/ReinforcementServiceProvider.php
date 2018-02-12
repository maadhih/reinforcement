<?php

namespace Reinforcement;

use Illuminate\Support\ServiceProvider;

/**
 *
 * Reinforcement ServiceProvider
 *
 * @category   Reinforcement
 * @package    acnox/reinforcement
 * @copyright  Copyright (c) 2018 - 2018 Acnox (http://www.gilab.com/acnox)
 * @author     Acnox <acnox@gmail.com>
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class ReinforcementServiceProvider extends ServiceProvider
{
    private $commandPath = 'reinforcement.';

    private $packagePath = 'Reinforcement\Console\Commands\\';

    protected $commands = [
        \Reinforcement\Console\Commands\MakeResource::class,
        \Reinforcement\Console\Commands\MakeResourceController::class,
        \Reinforcement\Console\Commands\MakeResourceRepository::class,
        \Reinforcement\Console\Commands\MakeResourceRequest::class,
        \Reinforcement\Console\Commands\MakeResourceRoute::class,
        \Reinforcement\Console\Commands\MakeResourceSchema::class,
        \Reinforcement\Console\Commands\MakeResourceValidator::class,
        \Reinforcement\Console\Commands\MakeResourceModel::class,
        \Reinforcement\Console\Commands\MakeResourceSeeder::class,
        \Reinforcement\Console\Commands\MakeResourceMigration::class
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}

<?php

namespace Reinforcement\Acl\Tests;

use Reinforcement\Acl\Models\Permission;
use Reinforcement\Acl\Models\Role;
use Reinforcement\Acl\Models\User as AclUser;
use Reinforcement\Acl\Tests\Models\User as AuthUser;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Str;
use Monolog\Handler\TestHandler;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public $runDatabaseMigrations = true;
    public $seedDatabase = true;

    protected function setUp()
    {
        parent::setUp();

        if ($this->runDatabaseMigrations) {
            $this->runDatabaseMigrations();
        }

        if ($this->seedDatabase) {
            $this->seedDatabase();
        }
    }

    protected function runDatabaseMigrations()
    {
        $this->loadSqliteMigrations();
        // $this->loadSqliteMigrationsFromPath();
        $this->artisan('migrate');


        $this->app[Kernel::class]->setArtisan(null);

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback');

            RefreshDatabaseState::$migrated = false;
        });
    }

    protected function loadSqliteMigrationsFromPath()
    {
        // $paths = realpath(__DIR__.'/../src/database/migrations');
        $paths = realpath(__DIR__.'/database/sqlitemigrations');

        $this->app->afterResolving('migrator', function ($migrator) use ($paths) {
            foreach ((array) $paths as $path) {
                $migrator->path($path);
            }
        });
    }

    protected function loadSqliteMigrations()
    {
        $schemaBuilder = $this->app['db']->connection()->getSchemaBuilder();

        $schemaBuilder->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        $schemaBuilder->create('permissions', function (Blueprint $table) {
            $table->string('slug')->unique();
            $table->string('name');
            $table->timestamps();
        });

        $schemaBuilder->create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45);
            $table->string('slug', 45);
            $table->timestamps();
        });

        $schemaBuilder->create('role_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('role_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('role_id')->references('id')->on('roles');
        });

        $schemaBuilder->create('permission_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('permission_slug');
            $table->unsignedInteger('role_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('permission_slug')->references('slug')->on('permissions');
        });

        $schemaBuilder->create('permission_role', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('role_id');
            $table->string('permission_slug');
            $table->timestamps();

            $table->foreign('permission_slug')->references('slug')->on('permissions');
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    protected function seedDatabase()
    {
        $registeredRole = $this->createUser();
        $registeredRole = $this->createRole();
        $registeredRole = $this->permissionSync();
    }

    protected function permissionSync()
    {
        $this->artisan('permission:sync');
    }


    /**
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Builder|\Reinforcement\Acl\Models\Role
     */
    protected function createRole($role = null)
    {
        $role = $role ?: str_random(10);

        return Role::query()->create([
            'name'        => Str::title($role),
            'slug'        => str_slug($role)
        ]);
    }

    /**
     * @param string $user
     * @return \Illuminate\Database\Eloquent\Builder|\Reinforcement\Acl\Models\User
     */
    protected function createUser($user = null)
    {
        $user = $user ?: str_random(10);

        return AuthUser::query()->forceCreate([
            'name'  => Str::title($user),
            'email' => $user . '@example.com',
            'is_active' => true,
        ]);

    }

    /**
     * @param string $permission
     * @return \Illuminate\Database\Eloquent\Builder|Reinforcement\Acl\Models\Permission
     */
    protected function createPermission($slug = null)
    {
        $slug = $slug ?: str_random(10);

        return Permission::query()->create([
            'name'        => ucwords($slug),
            'slug'        => $slug
        ]);
    }

    /**
     * Resolve application HTTP Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Http\Kernel', 'Reinforcement\Acl\Tests\Http\Kernel');
    }

    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.debug', true);
        $app['config']->set('cache.default', 'array');
        $app['config']->set('session.driver', 'file');
        $app['config']->set('session.expire_on_close', false);

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('view.paths', [__DIR__ . '/resources/views']);
        $app['config']->set('auth.providers.users.model', AuthUser::class);

        $app['config']->set('acl.user', AclUser::class);
        $app['config']->set('acl.disabled', $this->isAclDisabled());

        $app['log']->channel()->getLogger()->pushHandler(new TestHandler());
    }

    public function isAclDisabled()
    {
        return false;
    }

    protected function getPackageProviders($app)
    {
        return [
            \Reinforcement\Acl\AclServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [];
    }
}
<?php
namespace Reinforcement\Acl\Tests;

use Reinforcement\Acl\Models\Permission;
use Reinforcement\Acl\Tests\TestCase;
use Illuminate\Routing\Router;

class PermissionSyncTest extends TestCase
{
    /** @test */
    public function can_seed_permissions_from_routes()
    {

        $router = $this->app[Router::class];
        $routes = $router->getRoutes();
        $this->artisan('permission:sync');

        $routesNames = [];
        $permissions = Permission::all();
        foreach ($routes as $key => $route) {
            $routesNames[] = $route->getName();
        }


        $permissions->each(function($permission) use ($routesNames) {
            $this->assertContains($permission->slug, $routesNames);
        });

        // dd('Permission', $permissions->pluck('slug'), 'Routes', $routesNames);

        // check whether user controller is loaded in routes for user related stuffs
        $this->assertEquals(
            $permissions->count(),
            count($routesNames),
            'routes count should be equal to permissions count'
        );
    }


    protected function setUp()
    {
        $this->runDatabaseMigrations = true;
        $this->seedDatabase = false;

        parent::setUp();
    }

}
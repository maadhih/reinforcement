<?php

namespace Reinforcement\Acl\Tests;

use Reinforcement\Acl\Middlewares\Acl as AclMiddleware;
use Reinforcement\Acl\Models\Permission;
use Reinforcement\Acl\Repositories\UserRepository;

class AclTest extends TestCase
{
    /** @test */
    public function it_can_access_permission_protected_routes()
    {
        $user = $this->createUser();
        $permission = $this->createPermission('something.home');

        $user->permissions()->attach($permission->slug);

        $this->actingAs($user)->get('/')->assertSee('Ok');
    }

    /** @test */
    public function it_can_access_role_protected_routes()
    {
        $user = $this->createUser();
        $role = $this->createRole('somerole');

        $permission2 = $this->createPermission('another.home');
        $permission = $this->createPermission('something.home');
        $permission3 = $this->createPermission('next.home');

        $role->permissions()->attach($permission2->slug);
        $role->permissions()->attach($permission->slug);
        $role->permissions()->attach($permission3->slug);

        UserRepository::addRoleWithPermissions($user,$role);

        $this->actingAs($user)->get('/')->assertSee('Ok');
    }

    /** @test */
    public function a_user_cannot_access_admin_routes()
    {
        $this->actingAs($this->createUser())
             ->get('/')
             ->assertStatus(401)
             ->assertSee('You are not authorized to access this resource.');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->app['router']->get('/', function () {
            return 'Ok';
        })->name('something.home')->middleware(AclMiddleware::class);
    }
}
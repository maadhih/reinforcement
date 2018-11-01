<?php
namespace Reinforcement\Acl\Tests\Resources;

use Reinforcement\Acl\Tests\Resources\ResourceTestCase;

class PermissionsTest extends ResourceTestCase
{

    /** @test */
    public function can_get_permissions()
    {
        $permission = $this->createPermission('roles.show');
        $permission = $this->createPermission('users.roles.index');
        $permission = $this->createPermission('roles.permissions');

        $this->get('/api/permissions')
            ->assertSee('roles.show')
            ->assertSee('users.roles.index')
            ->assertSee('roles.permissions');
    }

    /** @test */
    public function can_get_a_permission()
    {
        $permission = $this->createPermission('roles.show');
        $permission = $this->createPermission('users.roles.index');
        $permission = $this->createPermission('roles.permissions');

        $this->get('/api/permissions/users.roles.index')
            ->assertSee('users.roles.index')
            ->assertDontSee('roles.permissions');
    }
}
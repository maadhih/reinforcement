<?php
namespace Reinforcement\Acl\Tests\Resources;

use Reinforcement\Acl\Tests\Resources\ResourceTestCase;

class RolePermissionTest extends ResourceTestCase
{

    /** @test */
    public function can_get_role_permissions()
    {
        $role = $this->createRole('admin');

        $permission1 = $this->createPermission('roles.show');
        $permission2 = $this->createPermission('users.roles.index');
        $permission3 = $this->createPermission('roles.permissions');

        $role->permissions()->attach($permission1->slug);
        $role->permissions()->attach($permission3->slug);

        $this->get('/api/roles/1/permissions')
            ->assertSee('roles.show')
            ->assertDontSee('users.roles.index')
            ->assertSee('roles.permissions');
    }
}
<?php
namespace Reinforcement\Acl\Tests\Resources;

use Reinforcement\Acl\Tests\Resources\ResourceTestCase;

class UserPermissionTest extends ResourceTestCase
{

    /** @test */
    public function can_get_role_permissions()
    {
        $user = $this->createUser('ahmed');

        $permission1 = $this->createPermission('roles.show');
        $permission2 = $this->createPermission('users.roles.index');
        $permission3 = $this->createPermission('roles.permissions');

        $user->permissions()->attach($permission1->slug);
        $user->permissions()->attach($permission3->slug);

        $this->get('/api/users/1/permissions')
            ->assertSee('roles.show')
            ->assertDontSee('users.roles.index')
            ->assertSee('roles.permissions');
    }
}
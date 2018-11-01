<?php

namespace Reinforcement\Acl\Tests\Repositories;

use Reinforcement\Acl\Repositories\UserRepository;
use Reinforcement\Acl\Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    /** @test */
    public function it_can_add_role_to_a_user_with_roles_permissions()
    {
        $user = $this->createUser();
        $role = $this->createRole();
        for($i = 0; $i < 4; $i++) {
            $role->permissions()->attach($this->createPermission()->slug);
        }

        UserRepository::addRoleWithPermissions($user,$role);

        $this->assertEquals(4, $user->permissions->count(),
            'count of permissions attached to user does not match'
        );

        $this->assertEquals($role->permissions->count(), $user->permissions->count(),
            'number of permissions attached to the user is not equal to role attached permissions.'
        );
    }

    /** @test */
    public function it_can_remove_role_to_a_user_with_roles_permissions()
    {
        $user = $this->createUser();
        $role1 = $this->createRole();
        $role2 = $this->createRole();
        for($i = 0; $i < 4; $i++) {
            $role1->permissions()->attach($this->createPermission()->slug);
        }

        for($i = 0; $i < 3; $i++) {
            $role2->permissions()->attach($this->createPermission()->slug);
        }

        UserRepository::addRoleWithPermissions($user,$role1);
        UserRepository::addRoleWithPermissions($user,$role2);

        $this->assertEquals($role1->permissions->count(), 4);
        $this->assertEquals($role2->permissions->count(), 3);
        $this->assertEquals($user->permissions->count(), 7);

        $user = $user->fresh();
        $role1 = $role1->fresh();

        UserRepository::removeRoleWithPermissions($user,$role1);

        $this->assertEquals($role1->permissions->count(), 4);
        $this->assertEquals($user->permissions->count(), 3);
    }
}
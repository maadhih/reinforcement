<?php
namespace Reinforcement\Acl\Tests\Resources;

use Reinforcement\Acl\Tests\Resources\ResourceTestCase;

class UserRoleTest extends ResourceTestCase
{

    /** @test */
    public function can_get_user_roles()
    {
        $user = $this->createUser('ahmedz');

        $role1 = $this->createRole('admin');
        $role2 = $this->createRole('moderator');
        $role3 = $this->createRole('guest');

        $user->roles()->attach($role1->id);
        $user->roles()->attach($role3->id);

        $this->get('/api/users/1/roles')
            ->assertSee('admin')
            ->assertDontSee('moderator')
            ->assertSee('guest');
    }
}
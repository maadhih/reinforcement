<?php
namespace Reinforcement\Acl\Tests\Resources;

use Reinforcement\Acl\Tests\Resources\ResourceTestCase;

class UsersTest extends ResourceTestCase
{

    /** @test */
    public function can_get_users()
    {
        $user = $this->createUser('ahmed');
        $user = $this->createUser('ali');
        $user = $this->createUser('mohamed');

        $this->get('/api/users')
            ->assertSee('ahmed')
            ->assertSee('ali')
            ->assertSee('mohamed');
    }

    /** @test */
    public function can_get_a_user()
    {
        $user = $this->createUser('ahmed');
        $user = $this->createUser('ali');
        $user = $this->createUser('mohamed');

        $this->get('/api/users/2')
            ->assertDontSee('ahmed')
            ->assertSee('ali')
            ->assertDontSee('mohamed');
    }
}
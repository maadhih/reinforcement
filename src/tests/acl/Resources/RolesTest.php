<?php

namespace Reinforcement\Acl\Tests\Resources;

use Reinforcement\Acl\Tests\Resources\ResourceTestCase;

class RolesTest extends ResourceTestCase
{
    /** @test */
    public function can_get_roles()
    {
        $role = $this->createRole('admin');
        $role = $this->createRole('moderator');
        $role = $this->createRole('guest');

        $this->get('/api/roles')
            ->assertSee('admin')
            ->assertSee('moderator')
            ->assertSee('guest');
    }

    /** @test */
    public function can_get_a_role()
    {
        $role = $this->createRole('admin');
        $role = $this->createRole('moderator');
        $role = $this->createRole('guest');

        $this->get('/api/roles/2')
            ->assertSee('moderator')
            ->assertDontSee('admin');
    }
}
<?php
namespace Reinforcement\Acl\Tests\Resources;

use Reinforcement\Acl\Models\UserInterface;
use Reinforcement\Acl\Tests\Models\User;
use Reinforcement\Acl\Tests\TestCase;
use Illuminate\Http\Request;

class ResourceTestCase extends TestCase
{

    protected function setUp()
    {
        $this->runDatabaseMigrations = true;
        $this->seedDatabase = false;

        parent::setUp();
    }

    public function isAclDisabled()
    {
        return true;
    }

}
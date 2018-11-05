<?php

namespace Reinforcement\Acl\Models;

use Reinforcement\Database\Eloquent\ModelInterface;

interface UserInterface extends ModelInterface
{

    public function roles();

    public function permissions();

    public function hasPermission(string $slug) : bool;
}
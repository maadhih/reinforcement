<?php

namespace Reinforcement\Acl\Models;

use Reinforcement\Acl\Models\UserInterface;
use Reinforcement\Acl\Models\UserTrait;
use Reinforcement\Database\Eloquent\Model;

class User extends Model implements UserInterface
{
    use UserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'is_active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['remember_token'];

    /**
     * The attributes to be casted to the given types
     *
     * @var array
     */
    protected $casts = ['is_active' => 'bool'];

    /**
     * mapping for the fields
     *
     * @return array mapping array
     */
    public static function mappings() : array
    {
        return [];
    }
}

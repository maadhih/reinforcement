<?php
/**
 * @author Mohamed Aiman mohamed.aiman@Reinforcement.mv
 */
return [
    /**
     * after it's being published,
     * if you want to use your own user migration file change this to false
     * before migrating
     */
    'use_acl_user_migration' => true,
    /**
     * user should implement Reinforcement\Acl\Models\UserInterface,
     * see Reinforcement\Acl\Models\User details, used traits, that could be of use to you
     */
    'user' => Reinforcement\Acl\Models\User::class,
    /**
     * set this to true if you want to test requests without authorizing
     * this doesn't effect middleware method
     * only works when using PermissionChecker::check() method is used
     */
    'disabled' => false
];
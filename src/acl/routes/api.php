<?php

Route::resource('users', 'UserController');
Route::resource('roles', 'RoleController');
Route::resource('permissions', 'PermissionController');

Route::resource('users.roles', 'UserRoleController');
Route::resource('users.permissions', 'UserPermissionController');
Route::resource('roles.permissions', 'RolePermissionController');
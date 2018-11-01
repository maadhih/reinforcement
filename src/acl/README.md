1 - to publish migrations and config file

run

```sh
php artisan vendor:publish --provider="Reinforcement\Acl\AclServiceProvider" --tag=config --tag=migrations
```
2 - if you don't want to use the users migration set ```use_acl_user_migration``` to ```false``` in published config file (config\acl.php)

3 - if you want to use your own User model as Acl User change the config acl.user value to the desired model.
The model should implement ```Reinforcement\Acl\Models\UserInterface``` and could use ```Reinforcement\Acl\Models\UserTrait``` to implement the methods.

4 - run the migrations.

5 - add Acl::routes() to service provider boot method

You could check for permissions either

- using ```$user->hasPermission($request->route()->getName())```

- using ```Reinforcement\Acl\Middlewares\Acl```
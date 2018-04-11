# Laravel 5.5+ Reinforcements

This package includes artisan generators out of the box:

- `reinforcement:migration`
- `reinforcement:resource`
- `reinforcement:controller`
- `reinforcement:model`
- `reinforcement:repository`
- `reinforcement:request`
- `reinforcement:route`
- `reinforcement:validator`
- `reinforcement:seeder`

## Usage

### Step 1: Install Through Composer

```
composer require maadhih/reinforcement --dev
```

### Step 2: Laravel Package Discovery

Run `php artisan package:discover` to make sure the package is discoverd by Laravel.



### Step 3: Run Artisan!

You're all set. Run `php artisan` from the console, and you'll see the new commands in the `reinforcement:*` namespace section.
*Note: The commands will only be available when `APP_ENV` is set to local

## Examples

- [Creating full resource bundles](#creating-full-resource-bundle)
- [Creating individual elements of resource](#creating-individual-elements-of-resource)

### Creating full resource bundles

```
php artisan reinforcement:resource ResourceOne ResourceTwo ResourceThree ...
```

Notice the format that we use, when giving the command more than 1 resource to create, we separate them with spaces

This would create the whole bundle required for the reinforcement module to work. This bundle includes:

- `Resource migration`
- `Resource controller`
- `Resource model`
- `Resource repository`
- `Resource request`
- `Adding the Resource route to the routes file`
- `Resource validator`
- `Resource seeder`

### Creating individual elements of resource

```
php artisan reinforcement:controller ResourceOne ResourceTwo ResourceThree ...
```

This would create the controllers for the required resources. This could be used with all the other available artisan commands.

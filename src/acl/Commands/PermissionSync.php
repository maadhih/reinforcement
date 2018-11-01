<?php

namespace Reinforcement\Acl\Commands;

use Reinforcement\Acl\Models\Permission;
use Reinforcement\Acl\Models\Resource;
use Illuminate\Console\Command;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PermissionSync extends Command
{
    /**
     * router
     * @var Router
     */
    protected $router;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'permission:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize system permission table.';


    protected $resourceCollection;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Router $router, Collection $collection)
    {
        $this->router = $router;
        $this->resourceCollection = $collection;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Collecting the resources and permissions.');
        $defaultPermissions = ['index', 'show', 'store', 'update', 'destroy'];
        foreach($this->router->getRoutes() as $key => $route) {
            $name = $route->getName();
            try {
                $controller = $route->getController();
            } catch (\Exception $e) {
                dump($e->getMessage() . " for route " . trim($name));
                // dd($e->getMessage());
                continue;
            }

            preg_match_all('(\w+)', get_class($controller), $matches);
            $matches = array_shift($matches);
            if (empty($matches)) {
                $this->error("invalid route '$name' found! No module name or app name found!");
                exit;
            }
            $resourceName = substr($name, 0, strrpos($name, '.'));
            $module = (count($matches) == 5 ? $matches[1] : $matches[0]);
            $resources[$resourceName]['module'] = $module;
            if (empty($name)) {
                $this->error("The route [{$route->uri()}] does not have a name, route names are necessary for the permission.");
                exit;
            }
            if ($resourceName && !isset($resources[$resourceName])) {
                $resources[$resourceName]['defaults'] = array();
                $resources[$resourceName]['nondefaults'] = array();
            }
            if ($resourceName && isset($resources[$resourceName])) {
                $permission = substr($name, (strrpos($name, '.') + 1), strlen($name));
                if (!$permission) {
                    throw new Exception("Route name not set properly.");
                }

                if (in_array($permission, $defaultPermissions)) {
                    $resources[$resourceName]['defaults'][] = $permission;
                } else {
                    $resources[$resourceName]['nondefaults'][] = $permission;
                }
            }
        }

        $this->info('Creating permissions.');
        foreach ($resources as $resourceName => $permissions) {
            $name = preg_replace('/(-|\.)/', ' ', $resourceName);
            $this->createPermissions($name, $resources, $resourceName);
        }
        $this->info('Permissions synchronizing finished.');
    }

    public function createPermissions($resourceName, $resources, $name, $update = false)
    {
        foreach ($resources[$name] as $permissionsArrays) {
            if (!is_array($permissionsArrays)) continue;
            foreach ($permissionsArrays as $permission) {
                $permissionName = $this->getPermissionDescription($resourceName, $permission);
                $slug = $name.'.'.$permission;
                $permissionModel = Permission::where('slug', $slug)->first();
                //there might not be permission added, while updating the permission. in case add the permission
                if (empty($permissionModel)) {
                    $this->createPermission($permissionName, $slug);
                } else {
                    $permissionModel->name = $permissionName;
                    $permissionModel->slug = $slug;
                    $permissionModel->save();
                }
            }
        }
    }

    public function createPermission($name, $slug)
    {
        return Permission::create([ 'name' => $name, 'slug' => $slug]);
    }

    public function createResource($name, $module)
    {
        $resourceName = preg_replace('/(-|\.)/', ' ', $name);
        $resource = Resource::create(['name' => ucfirst($resourceName), 'module' => $module ]);
        $this->resourceCollection->push($resource);
        return $resource;
    }

    public function getPermissionDescription($resourceName, $action)
    {
        $resourceName = str_replace('-', ' ', $resourceName);
        switch ($action) {
            case 'index':
                $permissionName = "List {$resourceName}";
                break;
            case 'show':
                $permissionName = "View {$resourceName}";
                break;
            case 'store':
                $permissionName = "Create {$resourceName}";
                break;
            case 'update':
                $permissionName = "Edit {$resourceName}";
                break;
            case 'destroy':
                $permissionName = "Remove {$resourceName}";
                break;
            default:
                $permissionName = $action . " " . $resourceName;
                break;
        }

        $permission = ucfirst(preg_replace('/(-|\.)/', ' ', $permissionName));
        return $permission;
    }
}

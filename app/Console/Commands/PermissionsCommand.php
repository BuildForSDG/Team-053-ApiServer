<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class PermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:make {name}
        {--T|table= : Permission Table name }
        {--B|bread= : Generate Browse, Read, Edit, Add & Delete permissions}
        {--A|actions= : Bread Actions separated by Comma e.g browse,read,edit,delete}
        {--D|delete= : Delete permission}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Permission(s)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $table = $this->option('table');

        // dd($this->options());

        if ($this->option('delete')) {
            $this->info("Deleting Permissions");
            return $this->deletePermission($this->argument('name'));
        }

        if ($table) {
            $this->info("Generating Permissions for {$table} table");
        } else {
            $this->info("Generating Permissions");
        }
        $this->generatePermission($this->argument('name'));
    }

    /**
     * Generate Permissions
     *
     * @param string $name // Permission Name
     *
     * @return void
     */
    public function generatePermission($name)
    {
        $table = $this->option('table');
        $bread = $this->option('bread');

        $actions = ['browse', 'read', 'edit', 'add', 'delete'];

        if ($this->option('actions')) {
            $actions = explode(',', $this->option('actions'));
        }

        if (!$bread) {
            $display_name = Str::title(
                str_replace(
                    ['-', '_', '.'],
                    ' ',
                    Str::plural($name, 2)
                )
            );

            $this->createPermission($name, $display_name, $table);
            return;
        }

        collect($actions)->each(
            function ($action) use ($name, $table) {
                $name = trim($action) . '-' . Str::plural($name, 2);

                $display_name = Str::title(
                    str_replace(
                        ['-', '_', '.'],
                        ' ',
                        Str::plural($name, 2)
                    )
                );

                $this->createPermission($name, $display_name, $table);
            }
        );
    }

    /**
     * Create Permission Record
     *
     * @param string $name         // Permission Name
     * @param string $display_name // Display Name
     * @param string $table        // Table Name
     *
     * @return void
     */
    public function createPermission($name, $display_name, $table)
    {
        $data = [
            'name' => $name,
            'display_name' => $display_name
        ];

        if ($table) {
            $data['table_name'] = $table;
        }

        $permission = Permission::firstOrCreate(
            [
                'name' => $name
            ],
            $data
        );

        $this->info('Permission Created: ' . $display_name);

        if ($permission) {
            if (config('binary.roles.attach_permission')) {
                $roles = Role::where(
                    'name',
                    config('binary.roles.default', 'root')
                )->get();

                if ($roles) {
                    $role = $roles->first();

                    if (!$role->hasPermission($permission->name)) {
                        $role->permissions()->attach($permission);

                        $this->info('Permission Attached to ' . $role->display_name);
                    }
                }
            }
        }
    }

    /**
     * Delete Permission
     *
     * @param string $name // Permission Name
     *
     * @return void
     */
    public function deletePermission($name)
    {
        $permissions = Permission::where('name', $name)->get();

        if ($this->option('bread')) {
            $permissions = Permission::where('name', 'LIKE', '%'. $name . '%')
                ->get();
        }

        if (!sizeof($permissions)) {
            $this->info('Permission(s) ' . $name . ' Not Found!');
        }

        foreach ($permissions as $permission) {
            $name = $permission->display_name;

            $permission->roles()->detach();
            $permission->delete();

            $this->info('Permission ' . $name . ' deleted successfully');
        }
    }
}

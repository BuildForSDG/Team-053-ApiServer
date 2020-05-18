<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ApiModuleGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api-module {name}
        {--S|section=Admin : Section name e.g Admin, Customer etc default - Admin }
        {--M|model= : Model name}
        {--C|controller= : Controller name}
        {--R|resource= : Resources name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to Generate API Module';

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
        $name = Str::title($this->argument('name'));

        $section =  Str::title($this->option('section')) ?? 'Admin';
        $controller = $this->option('controller') ?? $name.'Controller';
        $model = $this->option('model') ?? $name;
        $resource =  Str::title($this->option('resource')) ?? $name.'Resource';

        $section = $this->getFormat($section, true);

        $this->info("========================================================");
        $this->info("Generating Api Module {$name}");

        $controller = $this->getFormat($controller, true);
        $model = $this->getFormat($model, true);
        $resource = $this->getFormat($resource, true);

        $this->info("Generating Controller");
        Artisan::call("make:controller Api/{$section}/{$controller} --resource");

        $this->info("Generating Model");
        Artisan::call("make:model {$model}");

        $this->info("Generating Request");
        Artisan::call("make:request Api/{$section}/{$name}Request");

        $this->info("Generating Resource");
        Artisan::call("make:resource Api/{$section}/{$resource}");

        $this->info("Api Module {$name} Generated");
        $this->info("========================================================");
    }

    public function getFormat($value, $singular = false, $plural = false)
    {
        // $value = Str::title($value);

        if ($singular) {
            $value = Str::singular($value);
        }

        if ($plural) {
            $value = Str::plural($value);
        }

        return str_replace(' ', '', $value);
    }
}

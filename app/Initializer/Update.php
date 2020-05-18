<?php

namespace App\Initializer;

use MadWeb\Initializer\Contracts\Runner;

class Update
{
    public function production(Runner $run)
    {
        $run->external('composer', 'install', '--no-dev', '--prefer-dist', '--optimize-autoloader')
            ->external('npm', 'install', '--production')
            ->external('npm', 'run', 'production')
            ->artisan('route:cache')
            ->artisan('config:cache')
            ->artisan('event:cache')
            ->artisan('migrate', ['--force' => true])
            ->artisan('cache:clear')
            ->artisan('queue:restart'); // ->artisan('horizon:terminate');
    }

    public function local(Runner $run)
    {
        $run->external('composer', 'install')
            ->external('npm', 'install')
            ->external('npm', 'run', 'development')
            ->artisan('migrate')
            ->artisan('cache:clear');
    }
}

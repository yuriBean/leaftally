<?php

namespace App\Http\Middleware;

use App\Models\Utility;
use Closure;

class XSS
{
    use \RachidLaasri\LaravelInstaller\Helpers\MigrationsHelper;

    public function handle($request, Closure $next)
    {
        
        if(\Auth::check())
        {
   
            \App::setLocale(\Auth::user()->lang);

            if(\Auth::user()->type == 'super admin')
            {
                $migrations             = $this->getMigrations();
                $dbMigrations           = $this->getExecutedMigrations();
                $numberOfUpdatesPending = count($migrations) - count($dbMigrations);

                if($numberOfUpdatesPending > 0)
                {
                    Utility::addNewData();
                    return redirect()->route('LaravelUpdater::welcome');
                }
            }
        }

        $input = $request->all();
        $request->merge($input);

        return $next($request);
    }
}
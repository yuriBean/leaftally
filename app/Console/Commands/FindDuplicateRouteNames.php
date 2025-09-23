<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use ReflectionFunction;
use ReflectionMethod;

class FindDuplicateRouteNames extends Command
{
    protected $signature = 'route:find-duplicates';
    protected $description = 'Find duplicate route names along with their file paths';

    public function handle()
    {
        $routes = collect(Route::getRoutes())->map(function ($route) {
            $routeName = $route->getName();
            $action = $route->getAction()['uses'] ?? null;

            $filePath = 'N/A';

            if ($action instanceof \Closure) {
                $reflection = new ReflectionFunction($action);
                $filePath = $reflection->getFileName();
            } elseif (is_string($action) && strpos($action, '@') !== false) {
                [$class, $method] = explode('@', $action);
                if (class_exists($class) && method_exists($class, $method)) {
                    $reflection = new ReflectionMethod($class, $method);
                    $filePath = $reflection->getFileName();
                }
            }

            return ['name' => $routeName, 'path' => $filePath];
        })->filter(function ($route) {
            return !is_null($route['name']);
        });

        $duplicates = $routes->groupBy('name')->filter(function ($group) {
            return $group->count() > 1;
        });

        if ($duplicates->isEmpty()) {
            $this->info('No duplicate route names found.');
        } else {
            $this->warn('Duplicate route names found:');
            foreach ($duplicates as $name => $routeGroup) {
                $this->line("Route name: $name");
                foreach ($routeGroup as $route) {
                    $this->line("  File path: " . $route['path']);
                }
            }
        }
    }
}

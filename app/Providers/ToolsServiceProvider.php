<?php
// app/Providers/ToolsServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Tools\SomeExampleClass;

class ToolsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('SomeExampleClass', function () {
            return new SomeExampleClass;
        });
    }
}
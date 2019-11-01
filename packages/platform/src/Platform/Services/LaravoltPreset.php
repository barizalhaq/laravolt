<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\Presets\Preset;
use Illuminate\Support\Facades\Artisan;
use Laravolt\Platform\Commands\LinkCommand;

class LaravoltPreset extends Preset
{
    /**
     * Install the preset.
     *
     * @return void
     */
    public static function install()
    {
        Artisan::call(LinkCommand::class);
        static::replaceFiles();
    }

    protected static function replaceFiles()
    {
        $directories = [
            resource_path('lang/id') => platform_path('stubs/lang/id'),
        ];

        foreach ($directories as $destination => $source) {
            (new Filesystem())->copyDirectory($source, $destination);
        }

        $files = [
            app_path('User.php') => platform_path('stubs/User.php'),
            app_path('Http/Middleware/Authenticate.php') => platform_path('stubs/Authenticate.php'),
            app_path('Http/Middleware/RedirectIfAuthenticated.php') => platform_path('stubs/RedirectIfAuthenticated.php'),
            app_path('Exceptions/Handler.php') => platform_path('stubs/Handler.php'),
            app_path('Http/Controllers/Home.php') => platform_path('stubs/Home.php'),
            app_path('Http/Controllers/Dashboard.php') => platform_path('stubs/Dashboard.php'),
            resource_path('views/dashboard.blade.php') => platform_path('stubs/dashboard.blade.php'),
        ];

        foreach ($files as $original => $new) {
            (new Filesystem())->delete($original);
            copy($new, $original);
        }

        $entries = [
            base_path('routes/web.php') => [
                "Route::get('/', 'Home')->name('home');",
                "Route::get('/dashboard', 'Dashboard')->name('dashboard');",
            ],
            base_path('.gitignore') => [
                "/public/laravolt"
            ],
        ];

        foreach ($entries as $file => $lines) {
            foreach ($lines as $line) {
                file_put_contents($file, $line."\n", FILE_APPEND);
            }
        }
    }
}

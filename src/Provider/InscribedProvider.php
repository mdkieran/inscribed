<?php

namespace Mdkieran\Inscribed\Provider;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class InscribedProvider extends ServiceProvider
{
    /**
     * Bootstrap certain services.
     */
    public function boot() : void
    {
        if ($this->app->runningInConsole()) {
            $this->bootCommands();
            $this->bootPublishes();
        }

        $this->bootBladeDirective();
    }

    /**
     * Tell Laravel about the artisan commands.
     */
    private function bootCommands() : void
    {
        $this->commands([
            \Mdkieran\Inscribed\Command\CacheCommand::class,
            \Mdkieran\Inscribed\Command\ClearCommand::class,
        ]);

        $this->optimizes(
            optimize: 'inscribed:cache',
            clear: 'inscribed:clear',
        );
    }

    /**
     * Tell Laravel about our stubs.
     */
    private function bootPublishes() : void
    {
        $startdir = __DIR__.'/../../stub';

        $this->publishes([
            $startdir.'/ExampleInscribed.php.stub' => app_path('Inscribed/ExampleInscribed.php'),
        ], 'mdkieran-inscribed-php');

        // Optional: A helper function for retrieving inscribed data.
        $this->publishes([
            $startdir.'/inscribed.js.stub' => resource_path('js/inscribed.js')
        ], 'mdkieran-inscribed-js');
    }

    /**
     * Bootstrap a blade directive to either include JavaScript assets
     * or write inline JavaScript.
     */
    private function bootBladeDirective() : void
    {
        Blade::directive('inscribed', function () {
            $pathname = base_path('bootstrap/cache/inscribed.php');

            // Cached.
            if (File::exists($pathname)) {
                return collect(include $pathname)
                    ->map(fn($tail) => '<script src="'.asset($tail).'"></script>')
                    ->implode('');
            }

            // Generated every time.
            return "<?php if (app()->bound('inscribed.fqns')) { ".
                "echo collect(app('inscribed.fqns'))".
                "->map(fn(\$fqn) => app(\$fqn)->inline())".
                "->implode(''); } ?>";
        });
    }
}

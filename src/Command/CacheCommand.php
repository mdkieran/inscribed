<?php

namespace Mdkieran\Inscribed\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inscribed:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compile data to browser cachable JavaScript files.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!app()->bound('inscribed.fqns')) {
            $this->components->error('No Inscribed classes found.');

            return;
        }

        $fqns = app('inscribed.fqns');

        $tails = [];

        foreach ($fqns as $fqn) {
            $tails[] = app($fqn)->save()->tail();
        }

        File::put(
            base_path('bootstrap/cache/inscribed.php'),
            '<?php return ' . var_export($tails, true) . ';',
        );

        $this->components->info('Inscribed assets cached successfully.');
    }
}

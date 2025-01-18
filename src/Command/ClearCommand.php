<?php

namespace Mdkieran\Inscribed\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inscribed:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear compiled files and fallback to inline JavaScript.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fqns = app('inscribed.fqns');

        foreach ($fqns as $fqn) {
            app($fqn)->drop();
        }

        File::delete(base_path('bootstrap/cache/inscribed.php'));

        $this->components->info('Inscribed assets cleared successfully.');
    }
}

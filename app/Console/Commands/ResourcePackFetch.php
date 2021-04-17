<?php

namespace App\Console\Commands;

use Artisan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ResourcePackFetch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resourcepack:fetch
                            {name : Filename of resource pack located on GitHub}
                            {--use= : Whether the resource pack should be used}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch resource pack, and optionally apply it as currently used textures';

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
     * @return int
     */
    public function handle()
    {
        $this->info(sprintf("Downloading '%s'", $this->argument('name')));

        // Download resource pack
        $resourcePack = file_get_contents(
            'https://github.com/melkypie/resource-packs/archive/' . $this->argument('name') . '.zip'
        );

        // Put resource pack file to download directory
        Storage::disk('public')->put(
            'resource-packs-downloaded/' . $this->argument('name') . '.zip',
            $resourcePack
        );

        if ($this->option('use') == "yes") {
            $this->info(sprintf("Applying new textures"));

            Artisan::call("resourcepack:switch " . $this->argument('name'));
        }

        $this->info(sprintf("Finished!"));

        return 0;
    }
}

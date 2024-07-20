<?php

namespace App\Console\Commands;

use App\Helpers\ItemHelper;
use App\Models\Category;
use App\Models\Collection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command as CommandAlias;

class HiscoreCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hiscore:create
                            {type : Category type of new hiscore}
                            {name : Name of new hiscore (must be in snake case)}
                            {slug : Slug of new hiscore (must be in studly case)}
                            {uniques?* : Unique columns for new hiscore}';
//                            {--migrate= : Whether the hiscore should be migrated immediately or not}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates model, migration, collection and image directory for new hiscore based on type. Should be used when a new hiscore entry is added to the official hiscores';

    /**
     * Execute the console command.
     * @throws \Throwable
     */
    public function handle(): int
    {
        $hiscoreType = $this->argument('type');
        $hiscoreName = $this->argument('name');
        $hiscoreSlug = $this->argument('slug');

        try {
            $makeModel = sprintf("make:model %s/%s", ucfirst($hiscoreType), Str::studly($hiscoreSlug));

            Artisan::call($makeModel);
        } catch (\Exception $e) {
            $this->fail(sprintf("Could not create model: '%s'. Message: %s", Str::studly($hiscoreSlug), $e->getMessage()));
        }

        try {
            $migrationName = str_replace('-', '_', Str::snake(strtolower($hiscoreSlug)));

            if ($this->argument('unique')[0] === "drops" && sizeof($this->argument('unique')) === 1) {
//                $handle = curl_init('https://api.osrsbox.com/monsters?where=' . urlencode('{"name":"' . ucfirst(str_replace("_",
//                            " ", str_replace("-",
//                                " ", $this->argument('npc')))) . '"}') . '');

                $itemHelper = new ItemHelper();

                $drops = $itemHelper->apiMonsterDrops($hiscoreName);

                $uniques = array_map(
                    function ($drop) {
                        return $drop['name'];
                    },
                    $drops
                );

                if (empty($json["_items"][0]["drops"])) {
                    $this->info(sprintf("Could not find any drops for '%s'. Try to manually type the items you wish to track instead", $this->argument('npc')));

                    return COMMAND::FAILURE;
//                        foreach ($json["_items"][0]["drops"] as $drop) {
//                            $uniques[] = $drop["name"];
//                        }
                }
            } else {
                $uniques = $this->argument('unique');
            }

            $uniques = array_unique($uniques);
    dd($uniques);
            $migrationUniques = implode(
                ' ',
                array_map(
                    function ($unique) {
                        return (str_replace("'", "", str_replace("-", "_",
                                Str::snake(strtolower($unique))))) . ':integer:default(0):unsigned,'; // abyssal_whip
                    },
                    $uniques
                )
            );

            $command = 'make:migration:schema create_' . $migrationName . '_table --schema="account_id:integer:unsigned:unique, kill_count:integer:default(0):unsigned, obtained:integer:default(0):unsigned, ' . substr($migrationUniques,
                    0, -1) . '"';


            $makeMigration = sprintf("make:migration:schema create_%s_table --schema=\"account_id:integer:unsigned:unique, kill_count:integer:default(0):unsigned, obtained:integer:default(0):unsigned\"", $migrationName);
//            $makeMigration = sprintf("make:migration create_%s_table", $migrationName);


            Artisan::call($makeMigration);
        } catch (\Exception $e) {
            $this->fail(sprintf("Could not create migration: '%s'. Message: %s", Str::snake($hiscoreName), $e->getMessage()));
        }

        $categoryId = Category::whereCategory(strtolower($hiscoreType))->pluck('id')->first();
        if (!$categoryId) {
            $this->fail(sprintf("Could not find category: '%s'", strtolower($hiscoreType)));
        }

        $newestCollection = Collection::whereCategoryId($categoryId)->orderByDesc('order')->pluck('order')->first();

        if ($newestCollection) {
            $order = ++$newestCollection;
        } else {
            $order = $categoryId * 1000;
        }

        $collection = new Collection();

        $collection->category_id = $categoryId;
        $collection->order = $order;
        $collection->name = Str::title(str_replace('_', ' ', $hiscoreName));
        $collection->slug = Str::slug(($hiscoreName));
        $collection->model = sprintf("App\Models\%s\%s", ucfirst($hiscoreType), str_replace(':', '', Str::of($hiscoreName)->studly()));

        $collection->save();

        $imageDirectoryPath = sprintf("%s/images/%s/%s", public_path(), strtolower($hiscoreType), Str::slug($hiscoreName));
        if (!File::exists($imageDirectoryPath)) {
            File::makeDirectory($imageDirectoryPath, 0755, true, true);
        }

        if ($this->option('migrate') == 'yes') {
            Artisan::call('migrate');
        }

        $this->info(sprintf("Successfully created model, migration, collection and image directory for %s hiscore: '%s'", $hiscoreType, Str::title(str_replace('_', ' ', $hiscoreName))));

        return CommandAlias::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Account;
use App\Helpers\Helper;
use App\Http\Controllers\Api\AccountController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AccountUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches up-to-date data (account level, xp and skill) from Old School RuneScape hiscores';

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
     * @return mixed
     */
    public function handle()
    {
        foreach (Account::get() as $account) {
            if ($account->online !== 0) {
                $this->info(sprintf("%s is logged in to the game! Not updating", $account->username));

                continue;
            }

            $playerDataUrl = 'https://secure.runescape.com/m=hiscore_oldschool/index_lite.ws?player=' . str_replace(
                    ' ',
                    '%20',
                    $account->username
                );

            /* Get the $playerDataUrl file content. */
            $playerData = Helper::getPlayerData($playerDataUrl);

            if (!$playerData) {
                $this->info(
                    sprintf("Could not fetch player data for %s from hiscores! Not updating", $account->username)
                );

                continue;
            }

            if ($account->xp == $playerData[0][2] && $account->xp != 4600000000) {
                $this->info(sprintf("No outdated data for %s! Not updating", $account->username));

                continue;
            }

            $this->info(sprintf("Found outdated data for %s!", $account->username));

            DB::beginTransaction();

            $account->rank = $playerData[0][0];
            $account->level = $playerData[0][1];
            $account->xp = $playerData[0][2];

            try {
                $account->update();
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

            try {
                $accountController = new AccountController();

                $accountController->createOrUpdateAccountHiscores(
                    $account,
                    $playerData,
                    true
                );
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

            $this->info(sprintf("Updated %s", $account->username));

            DB::commit();
        }

        return 0;
    }
}

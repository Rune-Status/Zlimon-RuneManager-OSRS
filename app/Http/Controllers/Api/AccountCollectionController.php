<?php

namespace App\Http\Controllers\Api;

use App\Collection;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccountCollectionResource;
use App\Http\Resources\CollectionResource;
use App\Log;
use Illuminate\Http\Request;

class AccountCollectionController extends Controller
{
    public function index($accountUsername)
    {
        return new AccountCollectionResource(Helper::getAccountFromUsername($accountUsername));
    }

    public function show($accountUsername, $collectionName)
    {
        $collection = Collection::where('name', $collectionName)->firstOrFail();

        return new CollectionResource($collection->model::where('account_id', Helper::getAccountIdFromUsername($accountUsername))->first());
    }

    public function update($accountUsername, $collectionName, Request $request)
    {
        $account = Helper::checkIfUserOwnsAccount($accountUsername);

        $collection = Collection::where('alias', $collectionName)->first();
        if (!$collection) {
            return response($collectionName . " is not currently supported", 406);
        }

        $collectionLog = $collection->model::where('account_id', $account->id)->first();

        // If account has no collection entry, create it
        if (is_null($collectionLog)) {
            $collectionLog = new $collection->model;

            $collectionLog->getAttributes();

            foreach ($collectionLog->getFillable() as $fillable) {
                $collectionLog->{$fillable} = 0;
            }

            $collectionLog->account_id = $account->id;

            $collectionLog->save();
        }

        if (!$collectionLog) {
            return response($accountUsername . " does not have any registered collction log for " . $collection->alias, 404);
        }

        foreach ($request->all()["collectionLogItems"] as $lootItem) {
            if (!in_array($lootItem["name"], ["kill_count", "rank", "obtained"])) {
                $lootValues[$lootItem["name"]] = $lootItem["quantity"];
            }
        }

        $collectionLog->kill_count = (int)$request->kill_count;

        $collectionLog->obtained = (int)$request->obtained;

        $collectionLog->update($lootValues);

        $logData = [
            "user_id" => auth()->user()->id,
            "account_id" => $account->id,
            "category_id" => 8,
            "action" => $request->route()->getName(),
            "data" => $request->all()
        ];

        $log = Log::create($logData);

        return response("Submitted " . $collection->alias . " collection log", 200);
    }
}

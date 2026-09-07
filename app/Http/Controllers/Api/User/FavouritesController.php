<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Compound;
use App\Models\Favourite;
use App\Models\Uptown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavouritesController extends Controller
{
    public function getFavourites(Request $request)
    {
        $unitIds = Favourite::query()
            ->where('user_id', $request->user()->id)
            ->where('type', 'unit')
            ->pluck('uptown_id');

        $units = Uptown::with('unitimages')
            ->whereIn('id', $unitIds)
            ->get();

        foreach ($units as $unit) {
            foreach ($unit->unitimages as $image) {
                $image->image = url('storage/'.$image->image);
            }

            if ($unit->master_plan_image) {
                $unit->master_plan_image = url('storage/'.$unit->master_plan_image);
            }

            if ($unit->floor_plan_image) {
                $unit->floor_plan_image = url('storage/'.$unit->floor_plan_image);
            }
        }

        $compoundIds = Favourite::query()
            ->where('user_id', $request->user()->id)
            ->where('type', 'compound')
            ->pluck('compound_id');

        $compounds = Compound::whereIn('id', $compoundIds)->get();

        return response()->json([
            'units' => $units,
            'compounds' => $compounds,
        ]);
    }

    public function unitFavourite(Request $request, $id)
    {
        $uptown = Uptown::find($id);
        if (! $uptown) {
            return response()->json(['message' => 'Unit not found'], 404);
        }

        $validation = Validator::make($request->all(), [
            'favourite' => 'required|between:0,1',
        ]);
        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        }

        if (! $uptown->compound_id) {
            return response()->json(['message' => 'Unit has no compound'], 422);
        }

        $this->syncUnitFavourite($request->user()->id, $uptown, (int) $request->favourite === 1);

        return response()->json(['message' => 'Unit Favourite Successfully']);
    }

    public function compoundFavourite(Request $request, $id)
    {
        $compound = Compound::find($id);
        if (! $compound) {
            return response()->json(['message' => 'Compound not found'], 404);
        }

        $validation = Validator::make($request->all(), [
            'favourite' => 'required|between:0,1',
        ]);
        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        }

        $this->syncCompoundFavourite($request->user()->id, $compound, (int) $request->favourite === 1);

        return response()->json(['message' => 'Compound Favourite Successfully']);
    }

    private function syncUnitFavourite(int $userId, Uptown $uptown, bool $favourite): void
    {
        $keys = [
            'user_id' => $userId,
            'uptown_id' => $uptown->id,
            'type' => 'unit',
        ];

        if ($favourite) {
            Favourite::updateOrCreate($keys, [
                'compound_id' => $uptown->compound_id,
            ]);

            return;
        }

        Favourite::query()->where($keys)->delete();
    }

    private function syncCompoundFavourite(int $userId, Compound $compound, bool $favourite): void
    {
        $keys = [
            'user_id' => $userId,
            'compound_id' => $compound->id,
            'type' => 'compound',
        ];

        if ($favourite) {
            Favourite::updateOrCreate($keys, [
                'uptown_id' => null,
            ]);

            return;
        }

        Favourite::query()->where($keys)->delete();
    }
}

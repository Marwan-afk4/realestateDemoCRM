<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Compound;
use App\Models\Developer;
use App\Models\UnitsImage;
use App\Models\Uptown;
use App\trait\image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    use image;

    protected $updateUptown =['name_en','name_ar','description_en','description_ar','apparment','space','bathroom','bed','strat_price','delivery_date','type','cash','installment','installment_years','installment_plan','installment_price','master_plan_image','floor_plan_image'];

    public function unitDeveloper($compound_id)
{
    $compound = Compound::find($compound_id);
    $compoundUptownCount = Uptown::where('compound_id', $compound_id)->count();
    $units = Uptown::where('compound_id', $compound_id)
        ->with('unitImages') // Load the unitImages relationship
        ->get();


        foreach ($units as $unit) {
            if($unit->master_plan_image){
                $unit->master_plan_image = url('storage/' . $unit->master_plan_image);
            }
        }

        foreach ($units as $unit) {
            if($unit->floor_plan_image){
                $unit->floor_plan_image = url('storage/' . $unit->floor_plan_image);
            }
        }


    foreach ($units as $unit) {
        foreach ($unit->unitImages as $image) {
            $image->image = url('storage/' . $image->image);
        }
    }

    $compound->update(['units' => $compoundUptownCount]);

    $data = [
        'units' => $compoundUptownCount,
        'units_data' => $units,
    ];

    return response()->json($data);
}



    public function addUptown(Request $request,$compound_id){
        $validation = Validator::make($request->all(), [
            'name_en' => 'required',
            'name_ar' => 'required',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'apparment' => 'required',
            'space' => 'required|integer',
            'bathroom' => 'required|integer',
            'bed' => 'required|integer',
            'strat_price' => 'required|integer',
            'delivery_date' => 'nullable|date',
            'master_plan_image' => 'nullable',
            'floor_plan_image' => 'nullable',
            'cash'=> 'nullable|between:0,1',
            'installment' => 'nullable|between:0,1',
            'installment_years'=>'nullable|integer',
            'installment_plan' => 'nullable|in:monthly,yearly',
            'installment_price' => 'nullable|numeric|min:0',
            'type' => 'required|in:rent,buy',
            'images' => 'nullable|array',
            'images.*.image' => 'nullable|string'
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        }

        $compound = Compound::find($compound_id);
        $commission_percentage = $compound->commission_percentage;
        $uptown = Uptown::create([
            'compound_id' => $compound_id,
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'description_en' => $request->description_en,
            'description_ar' => $request->description_ar,
            'apparment' => $request->apparment,
            'space' => $request->space,
            'bathroom' => $request->bathroom,
            'bed' => $request->bed,
            'strat_price' => $request->strat_price,
            'commission_price'=>$request->strat_price*($commission_percentage/100),
            'delivery_date' => $request->delivery_date,
            'cash' => $request->cash ?? 0,
            'installment' => $request->installment ?? 0,
            'installment_years'=>$request->installment_years ?? 0,
            'installment_plan' => $request->installment_plan,
            'installment_price' => $request->installment_price,
            'type' => $request->type,
            'master_plan_image' => $request->master_plan_image,
            'floor_plan_image' => $request->floor_plan_image
        ]);

        if ($request->has('master_plan_image')) {
            $imagePath = $this->storeBase64Image($request->master_plan_image, 'admin/unit/master_plan');
            $uptown->update(['master_plan_image' => $imagePath]);
        }

        if ($request->has('floor_plan_image')) {
            $imagePath = $this->storeBase64Image($request->floor_plan_image, 'admin/unit/floor_plan');
            $uptown->update(['floor_plan_image' => $imagePath]);
        }

        if ($request->has('images')) {
            foreach ($request->images as $imageData) {
                if (isset($imageData['image'])) {
                    $imagePath = $this->storeBase64Image($imageData['image'], 'admin/unit/images');
                    UnitsImage::create([
                        'uptown_id' => $uptown->id,
                        'image' => $imagePath,
                    ]);
                }
            }
        }
        $this->updatecompoundUnit($compound_id);

        return response()->json(['message' => 'Unit added successfully', 'uptown_id' => $uptown]);

    }

    public function updatecompoundUnit($compound_id){
        $unitcount=Uptown::where('compound_id', $compound_id)->count();
        $compound=Compound::find($compound_id);
        $compound->update(['units'=>$unitcount]);
    }

    public function deleteUptown($id){
        $uptown = Uptown::find($id);
        $uptown->delete();
        return response()->json(['message'=>'Unit Deleted Successfully']);
    }

    public function updateUptown(Request $request,$id){
        $uptown = Uptown::findOrFail($id);

        $validation = Validator::make($request->all(), [
            'name_en' => 'nullable',
            'name_ar' => 'nullable',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'apparment' => 'nullable',
            'space' => 'nullable|integer',
            'bathroom' => 'nullable|integer',
            'bed' => 'nullable|integer',
            'strat_price' => 'nullable|integer',
            'delivery_date' => 'nullable|date',
            'type' => 'nullable|in:rent,buy',
            'cash' => 'nullable|between:0,1',
            'installment' => 'nullable|between:0,1',
            'installment_years' => 'nullable|integer',
            'installment_plan' => 'nullable|in:monthly,yearly',
            'installment_price' => 'nullable|numeric|min:0',
            'master_plan_image' => 'nullable',
            'floor_plan_image' => 'nullable',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        }

        $uptown->update($request->only($this->updateUptown));
        return response()->json(['message'=>'Unit Updated Successfully']);
    }

    public function DeleteUptownImage($id){
        $uptownImage = UnitsImage::find($id);
        if ($uptownImage) {
            $this->deleteImage($uptownImage->image);
            $uptownImage->delete();
            return response()->json(['message' => 'Image deleted successfully']);
        }
        return response()->json(['message' => 'Image not found'], 404);
    }

}

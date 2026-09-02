<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContractController extends Controller
{
    public function getContracts()
    {
        $contracts = Contract::all();
        return response()->json(['contracts' => $contracts]);
    }

    public function createContract(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'pages' => 'required|array|min:1',
            'pages.*' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 401);
        }

        $contract = Contract::create([
            'title' => $request->title,
            'pages' => $request->pages,
        ]);

        return response()->json(['message' => 'Contract Created Successfully', 'contract' => $contract]);
    }

    public function updateContract(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'pages' => 'required|array|min:1',
            'pages.*' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 401);
        }

        $contract = Contract::findOrFail($id);
        $contract->update([
            'title' => $request->title,
            'pages' => $request->pages,
        ]);

        return response()->json(['message' => 'Contract Updated Successfully', 'contract' => $contract]);
    }

    public function deleteContract($id)
    {
        $contract = Contract::find($id);
        if (!$contract) {
            return response()->json(['message' => 'Contract not found'], 404);
        }
        $contract->delete();
        return response()->json(['message' => 'Contract deleted successfully']);
    }
}

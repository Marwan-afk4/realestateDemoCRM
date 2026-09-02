<?php

namespace App\Http\Controllers;

use App\Models\HomeName;


use Illuminate\Http\Request;
use App\Http\Requests\StoreHomeNameRequest;
use App\Http\Requests\UpdateHomeNameRequest;
use App\Http\Controllers\Controller;

class HomeNameController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');
        $homeNames = HomeName::orderBy($sortField, $sortOrder)->paginate(30);

        
        return view('home-names.index', compact('homeNames','sortField','sortOrder'));
    }

    public function create()
    {
        if (HomeName::count() >= 6) {
            return redirect()->route('home-names.index')->with('error', 'You can only have 6 home names.');
        }
        return view('home-names.create');
    }

    public function store(StoreHomeNameRequest $request)
    {
        if (HomeName::count() >= 6) {
            return redirect()->route('home-names.index')->with('error', 'You can only have 6 home names.');
        }
        HomeName::create($request->validated());
        return redirect()->route('home-names.index')->with('success', 'Created successfully');
    }

    public function show(HomeName $homeName)
    {
        return view('home-names.show', compact('homeName'));
    }

    public function edit(HomeName $homeName)
    {
        return view('home-names.edit', compact('homeName'));
    }

    public function update(UpdateHomeNameRequest $request, HomeName $homeName)
    {
        $homeName->update($request->validated());
        return redirect()->route('home-names.index')->with('success', 'Updated successfully.');
    }

    public function destroy(HomeName $homeName)
    {
        $homeName->delete();
        return redirect()->route('home-names.index')->with('success', 'Deleted successfully.');
    }
}

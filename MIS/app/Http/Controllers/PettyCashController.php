<?php

namespace App\Http\Controllers;

use App\Models\PettyCash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PettyCashController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pettyCashes = PettyCash::all();
        return view('pages.petty-cash', compact('pettyCashes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        PettyCash::create($request->only(['title', 'amount', 'type']));
        return redirect()->route('page.pettyCash')->with('success', 'Petty cash created.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:petty_cashes,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $pettyCash = PettyCash::find($validator->validated()['id']);
        $pettyCash->delete();

        return redirect()->route('page.pettyCash')->with('success', 'Petty cash deleted.');
    }
















    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('petty_cash.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(PettyCash $pettyCash)
    {
        return view('petty_cash.show', compact('pettyCash'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PettyCash $pettyCash)
    {
        return view('petty_cash.edit', compact('pettyCash'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PettyCash $pettyCash)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $pettyCash->update($request->only(['title', 'amount', 'type']));
        return redirect()->route('petty_cash.index')->with('success', 'Petty cash updated.');
    }

}

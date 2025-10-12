<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameCategoryRequest;
use App\Http\Requests\UpdateGameCategoryRequest;
use App\Models\GameCategory;

class GameCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGameCategoryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(GameCategory $gameCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GameCategory $gameCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameCategoryRequest $request, GameCategory $gameCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GameCategory $gameCategory)
    {
        //
    }
}

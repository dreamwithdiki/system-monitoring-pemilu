<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDataCalegRequest;
use App\Http\Requests\UpdateDataCalegRequest;
use App\Models\DataCaleg;

class DataCalegController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDataCalegRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDataCalegRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DataCaleg  $dataCaleg
     * @return \Illuminate\Http\Response
     */
    public function show(DataCaleg $dataCaleg)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DataCaleg  $dataCaleg
     * @return \Illuminate\Http\Response
     */
    public function edit(DataCaleg $dataCaleg)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDataCalegRequest  $request
     * @param  \App\Models\DataCaleg  $dataCaleg
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDataCalegRequest $request, DataCaleg $dataCaleg)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DataCaleg  $dataCaleg
     * @return \Illuminate\Http\Response
     */
    public function destroy(DataCaleg $dataCaleg)
    {
        //
    }
}

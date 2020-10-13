<?php

namespace App\Http\Controllers;

use App\Store;
use App\TransferStore;
use Illuminate\Http\Request;

class TransferStoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transfers = TransferStore::all();
        return view('dashboard.transferstore.index', compact('transfers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stores = Store::all();
        return view('dashboard.transferstore.create', compact('stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TransferStore  $transferStore
     * @return \Illuminate\Http\Response
     */
    public function show(TransferStore $transferStore)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TransferStore  $transferStore
     * @return \Illuminate\Http\Response
     */
    public function edit(TransferStore $transferStore)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TransferStore  $transferStore
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TransferStore $transferStore)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TransferStore  $transferStore
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransferStore $transferStore)
    {
        //
    }
}

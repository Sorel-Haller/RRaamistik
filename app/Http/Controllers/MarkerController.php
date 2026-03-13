<?php

namespace App\Http\Controllers;

use App\Models\Marker;
use Illuminate\Http\Request;

class MarkerController extends Controller
{

    public function index()
    {
        return Marker::all();
    }

    public function store(Request $request)
    {
        return Marker::create($request->all());
    }

    public function destroy($id)
    {
        Marker::destroy($id);
        return response()->json(['success'=>true]);
    }

}

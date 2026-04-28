<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marker;

class MarkerController extends Controller
{
    public function store(Request $request)
    {
        Marker::create([
            'name' => $request->name,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'description' => $request->description,
            'added' => now(),
            'edited' => now(),
        ]);

     return redirect()->back();
    }

    public function update(Request $request, \App\Models\Marker $marker)
    {
        $marker->update([
            'name' => $request->name,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'description' => $request->description,
            'edited' => now(),
        ]);

    return redirect()->back();
    }

    public function destroy($id)
    {
    \App\Models\Marker::findOrFail($id)->delete();

    return redirect()->back();
    }
}
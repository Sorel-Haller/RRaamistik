<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marker;

class MarkerController extends Controller
{
    public function store(Request $request)
    {
        Marker::create([
            'name'        => $request->name,
            'lat'         => $request->lat,
            'lng'         => $request->lng,
            'description' => $request->description,
            'added'       => now(),
            'edited'      => now(),
        ]);

        return redirect()->route('dashboard');
    }

    public function update(Request $request, Marker $marker)
    {
        $marker->update([
            'name'        => $request->name,
            'lat'         => $request->lat,
            'lng'         => $request->lng,
            'description' => $request->description,
            'edited'      => now(),
        ]);

        return back()->with('success', 'Marker updated');
    }

    public function destroy(Marker $marker)
    {
        $marker->delete();

        return redirect()->route('dashboard');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marker;

class MarkerController extends Controller
{
    public function index()
    {
        return response()->json(Marker::all());
    }

    public function store(Request $request)
    {
        $marker = Marker::create([
            'name'        => $request->name,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'description' => $request->description,
        ]);

        return response()->json($marker, 201);
    }

    public function update(Request $request, Marker $marker)
    {
        $marker->update([
            'name'        => $request->name,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'description' => $request->description,
        ]);

        return response()->json($marker);
    }

    public function destroy(Marker $marker)
    {
        $marker->delete();

        return response()->json(['deleted' => true]);
    }
}
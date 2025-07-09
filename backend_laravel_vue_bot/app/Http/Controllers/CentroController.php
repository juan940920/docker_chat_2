<?php

namespace App\Http\Controllers;

use App\Models\Centro;
use Illuminate\Http\Request;

class CentroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $centros = Centro::get();

        return response()->json($centros, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "nombre" => "required|unique:centros",
        ]);
        $centro = new Centro();
        $centro->nombre = $request->nombre;
        $centro->telefono = $request->telefono;
        $centro->interno = $request->interno;
        $centro->save();

        return response()->json(["mensaje" => "Centro registrado"], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $centro = Centro::findOrFail($id);

        return response()->json($centro, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "nombre" => "required",
        ]);
        $centro = Centro::find($id);
        $centro->nombre = $request->nombre;
        $centro->telefono = $request->telefono;
        $centro->interno = $request->interno;
        $centro->update();

        return response()->json(["mensaje" => "Centro modificado"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $centro = Centro::find($id);
        $centro->delete();

        return response()->json(["mensaje" => "Centro eliminado"]);
    }
}

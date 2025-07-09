<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unidades = Unidad::with('centro')->get();

        return response()->json($unidades, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "nombre" => "required",
            "centro_id" => "required"
        ]);
        $unidad = new Unidad();
        $unidad->nombre = $request->nombre;
        $unidad->telefono = $request->telefono;
        $unidad->interno = $request->interno;
        $unidad->centro_id = $request->centro_id;
        $unidad->save();

        return response()->json(["mensaje" => "Unidad registrado"], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $unidad = Unidad::findOrFail($id);

        return response()->json($unidad, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "nombre" => "required",
            "centro_id" => "required"
        ]);

        $unidad = Unidad::find($id);
        $unidad->nombre = $request->nombre;
        $unidad->telefono = $request->telefono;
        $unidad->interno = $request->interno;
        $unidad->centro_id = $request->centro_id;
        $unidad->update();

        return response()->json(["mensaje" => "Unidad modificado"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $unidad = Unidad::find($id);
        $unidad->delete();

        return response()->json(["mensaje" => "Unidad eliminado"]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Administrativo;
use Illuminate\Http\Request;

class AdministrativoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $administrativos = Administrativo::get();

        return response()->json($administrativos, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "nombres" => "required",
            "apellidos" => "required",
            "unidad_id" => "required",
            "user_id" => "required"
        ]);
        $administrativo = new Administrativo();
        $administrativo->nombres = $request->nombres;
        $administrativo->apellidos = $request->apellidos;
        $administrativo->matricula = $request->matricula;
        $administrativo->cosultorio = $request->cosultorio;
        $administrativo->unidad_id = $request->unidad_id;
        $administrativo->user_id = $request->user_id;
        $administrativo->save();

        return response()->json(["mensaje" => "Administrativo registrado"], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $administrativo = Administrativo::findOrFail($id);

        return response()->json($administrativo, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "nombres" => "required",
            "apellidos" => "required",
            "unidad_id" => "required",
            "user_id" => "required"
        ]);
        $administrativo = Administrativo::find($id);
        $administrativo->nombres = $request->nombres;
        $administrativo->apellidos = $request->apellidos;
        $administrativo->matricula = $request->matricula;
        $administrativo->cosultorio = $request->cosultorio;
        $administrativo->unidad_id = $request->unidad_id;
        $administrativo->user_id = $request->user_id;
        $administrativo->update();

        return response()->json(["mensaje" => "Administrativo modificado"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $administrativo = Administrativo::find($id);
        $administrativo->delete();

        return response()->json(["mensaje" => "administrativo eliminado"]);
    }
}

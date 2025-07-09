<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PermisoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('index_permiso');

        $permisos = Permiso::get();
        return response()->json($permisos, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Gate::authorize('create_permiso');

        $permiso = new Permiso();
        $permiso->name = $request->name;
        $permiso->action = $request->action;
        $permiso->subject = $request->subject;
        $permiso->permiso = $request->permiso;
        $permiso->descripcion = $request->descripcion;
        $permiso->save();

        return response()->json(["mensaje" => "Permiso registrado...."]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        Gate::authorize('show_permiso');

        $permiso = Permiso::find($id);

        return response()->json($permiso);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Gate::authorize('edit_permiso');

        $permiso = Permiso::find($id);
        $permiso->name = $request->name;
        $permiso->action = $request->action;
        $permiso->subject = $request->subject;
        $permiso->permiso = $request->permiso;
        $permiso->descripcion = $request->descripcion;
        $permiso->update();

        return response()->json(["mensaje" => "Permiso actualizado...."]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Gate::authorize('delete_permiso');
    }
}

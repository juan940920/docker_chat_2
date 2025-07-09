<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('index_role');

        $roles = Role::with('permisos')->get();
        $permisos = Permiso::get();

        return response()->json(['roles' => $roles, 'permisos' => $permisos]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create_role');

        $request->validate([
            "name" => "required|unique:roles"
        ]);

        $role = new Role();
        $role->name = $request->name;
        $role->detalle = $request->detalle;
        $role->save();

        return response()->json([ "mensaje" => "El rol se ha registrado..."]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        Gate::authorize('show_role');

        $role = Role::find($id);

        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Gate::authorize('edit_role');

        $role = Role::find($id);
        $role->name = $request->name;
        $role->detalle = $request->detalle;
        $role->update();

        return response()->json([ "mensaje" => "El rol se ha actualizado..."]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function actualizarPermisos($id, Request $request) {
        $role = Role::findOrFail($id);

        $role->permisos()->sync([]);

        foreach ($request->permisos as $permiso) {
            $role->permisos()->attach($permiso['id']);
        }

        return response()->json(["mensaje" => "Permisos actualizados"]);

    }
}

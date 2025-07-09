<?php

namespace App\Http\Controllers;

use App\Models\Administrativo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('index_user');

        // /api/usuario?page=1&limit=10&q=teclado
        $limit = isset($request->limit)?$request->limit:10;
        $q = $request->q;
        if ($q) {
            $usuarios = User::where("name", "like", "%$q%")
                                ->orWhere("email", "like", "%$q%")
                                ->with(["administrativo", "roles"])
                                ->orderBy('id', 'desc')
                                ->paginate($limit);
        } else {
            $usuarios = User::with(['administrativo', 'roles'])->orderBy('id', 'desc')->paginate($limit);
        }
        return response()->json($usuarios, 200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create_user');

        $request->validate([
            "name" => "required|string",
            "email" => "required|email|unique:users",
            "password" => "required",
        ]);

        $usuario = new User();
        $usuario ->name = $request->name;
        $usuario ->email = $request->email;
        $usuario ->password = Hash::make($request->password);
        $usuario->save();

        return response()->json(["mensaje" => "Usuario registrado"]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        Gate::authorize('show_user');

        $user = User::findOrFail($id);

        return response()->json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Gate::authorize('edit_user');

        $request->validate([
            "name" => "required|string",
            "email" => "required|email|unique:users,email,$id",
            "password" => "required",
        ]);

        $usuario = User::find($id);
        $usuario ->name = $request->name;
        $usuario ->email = $request->email;
        $usuario ->password = Hash::make($request->password);
        $usuario->update();

        return response()->json(["mensaje" => "Usuario modificado"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Gate::authorize('delete_user');

        $usuario = User::find($id);
        $usuario->delete();

        return response()->json(["mensaje" => "Usuario eliminado"]);
    }

    public function actualizarRoles($id, Request $request) {
        $usuario = User::find($id);
        $usuario->roles()->sync($request["roles_id"]);

        return response()->json(["mensaje" => "Roles actualizados"]);
    }

    public function reportePDFUsuarios(Request $request) {
        
        $usuarios = User::get();
        
        $pdf = Pdf::loadView('pdf.usuarios', ["usuarios" => $usuarios]);
        // return $pdf->download('usuarios.pdf');
        return $pdf->stream("lista_usuarios.pdf");
    }

    public function asignarDatosPersonales($id, Request $request) {
        
        $request->validate([
            "nombres" => "required",
            "apellidos" => "required",
            "unidad_id" => "required",
            "user_id" => "required"
        ]);

        $user = User::findOrFail($id);

        $administrativo = new Administrativo();
        $administrativo->nombres = $request->nombres;
        $administrativo->apellidos = $request->apellidos;
        $administrativo->matricula = $request->matricula;
        $administrativo->cosultorio = $request->cosultorio;
        $administrativo->unidad_id = $request->unidad_id;
        $administrativo->user_id = $user->id;
        $administrativo->save();

        return response()->json(["mensaje" => "Datos de administrativos actualizados"], 201);
    }
}

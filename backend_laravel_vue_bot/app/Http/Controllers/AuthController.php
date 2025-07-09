<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function funIngresar(Request $request) {

        $credenciales = $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        if(!Auth::attempt($credenciales)){
            return response()->json(["mensaje" => "Credenciales Incorrectas"], 401);
        }

        //generar token (sanctum)
        $usuario = $request->user();
        $token = $usuario->createToken('Token auth')->plainTextToken;

        $array_permisos = [];
        if(count($usuario->roles) > 0){
            $array_permisos = $usuario->roles()
            ->with('permisos')
            ->get()
            ->pluck('permisos')
            ->flatten() // lo une en un solo array toda la repuesta
            ->map(function($permiso){
                return array('action' => $permiso->action, 'subject' => $permiso->subject ,'name' => $permiso->name);
            })
            ->unique();
        }

        $aux = [];
        foreach ($array_permisos as $per){
            array_push($aux, $per);
        }

        return response()->json([
            "access_token" => $token,
            "usuario" => $usuario,
            "permisos" => $aux
        ], 201);

    }

    public function funRegistro(Request $request) {
        //validar
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|same:c_password",
        ]);

        // registrar
        $usuario = new User();
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->password = Hash::make( $request->password);
        $usuario->save();

        //responder
        return response()->json(["mensaje" => "Usuario Registrado"]);
    }

    public function funPerfil(Request $request) {

        $usuario = User::with('roles')->find(Auth::id());

        $array_permisos = [];
        if(count($usuario->roles) > 0){
            $array_permisos = $usuario
            ->roles()
            ->with('permisos')
            ->get()
            ->pluck('permisos')
            ->flatten() // lo une en un solo array toda la repuesta
            ->map(function($permiso){
                return array('action' => $permiso->action, 'subject' => $permiso->subject ,'name' => $permiso->name);
            })->unique();
        }

        $usuario->permisos = $array_permisos;

        return response()->json($usuario, 200);
    }

    public function funSalir(Request $request) {

        $usuario = $request->user();
        $usuario->tokens()->delete();
        
        return response(["mensaje" => "Logout"], 200);

    }
}

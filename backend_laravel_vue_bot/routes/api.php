<?php

use App\Http\Controllers\AdministrativoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CentroController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use App\Models\Categoria;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1/auth')->group(function(){

    Route::post("login", [AuthController::class, "funIngresar"]);
    Route::post("register", [AuthController::class, "funRegistro"]);

    Route::middleware('auth:sanctum')->group(function(){
        
        Route::get("profile", [AuthController::class, "funPerfil"]);
        Route::post("logout", [AuthController::class, "funSalir"]);

    });

});

Route::get("/venta/reporte-pdf/{id}", [VentaController::class, "reportePDFVentas"]);

Route::middleware('auth:sanctum')->group(function(){

    //actualizar permisos
    Route::post("/roles/{id}/permisos", [RoleController::class, "actualizarPermisos"]);
    Route::post("usuario/{id}/asignar-roles", [UserController::class, "actualizarRoles"]);

    // asignar datos personales a User
    Route::post("/usuario/{id}/asignar-datos-personales", [UserController::class, "asignarDatosPersonales"]);


    // routes CRUD api Rest
    Route::apiResource("usuario", UserController::class);
    Route::apiResource("roles", RoleController::class);
    Route::apiResource("permiso", PermisoController::class);
    Route::apiResource("unidad", UnidadController::class);
    Route::apiResource("centro", CentroController::class);
    Route::apiResource("administrativo", AdministrativoController::class);
    Route::apiResource("contacto", ContactoController::class);
    Route::apiResource("categoria", CategoriaController::class);
    Route::apiResource("producto", ProductoController::class);
    Route::apiResource("cliente", ClienteController::class);
    Route::apiResource("venta", VentaController::class);

});

Route::get("/no-autorizado", function(){
    return ["mensaje" => "No Autorizado"];
})->name("login");

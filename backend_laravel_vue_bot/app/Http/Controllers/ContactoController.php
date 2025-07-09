<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use Illuminate\Http\Request;

class ContactoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // /api/contacto?page=1&limit=10&q=teclado
        $limit = isset($request->limit)?$request->limit:10;
        $q = $request->q;
        if ($q) {
            $contactos = Contacto::where("nro_whatsapp", "like", "%$q%")
                                ->orWhere("nombre_whatsapp", "like", "%$q%")
                                ->orderBy('id', 'desc')
                                ->paginate($limit);
        } else {
            $contactos = Contacto::orderBy('id', 'desc')->paginate($limit);
        }

        return response()->json($contactos, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $contacto = new Contacto();
        $contacto->nro_whatsapp = $request->nro_whatsapp;
        $contacto->nombre_whatsapp = $request->nombre_whatsapp;
        $contacto->save();

        return response()->json(["mensaje" => "contacto registrado"], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $contacto = Contacto::where('nro_whatsapp', $id)->firstOrFail();

        return response()->json($contacto, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $contacto = Contacto::find($id);
        $contacto->nro_whatsapp = $request->nro_whatsapp;
        $contacto->nombre_whatsapp = $request->nombre_whatsapp;
        $contacto->update();

        return response()->json(["mensaje" => "Contacto modificado"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contacto = Contacto::find($id);
        $contacto->delete();

        return response()->json(["mensaje" => "contacto eliminado"]);
    }
}

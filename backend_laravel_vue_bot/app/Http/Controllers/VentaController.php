<?php

namespace App\Http\Controllers;

use App\Models\DetallesVenta;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->limit ?? 10;
        $q = $request->q;

        if ($q) {
            $ventas = Venta::whereHas('contacto', function ($query) use ($q) {
                                $query->where('nro_whatsapp', 'like', "%$q%")
                                    ->orWhere('nombre_whatsapp', 'like', "%$q%");
                            })
                            ->with(['contacto', 'detalle_ventas.producto'])
                            ->orderBy('id', 'desc')
                            ->paginate($limit);
        } else {
            $ventas = Venta::with(['contacto', 'detalle_ventas.producto'])->orderBy('id', 'desc')->paginate($limit);
        }

        return response()->json($ventas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            DB::transaction(function () use ($request, &$venta) {
                // ✅ $venta ahora es pasada por referencia (&)

                $venta = Venta::create([
                    'contacto_id' => $request->contacto_id,
                    'total' => 0,
                ]);

                $total = 0;

                foreach ($request->items as $item) {
                    $producto = Producto::findOrFail($item['producto_id']);

                    if ($producto->stock < $item['cantidad_vendida']) {
                        throw new \Exception("Stock insuficiente para {$producto->nombre}");
                    }

                    $subtotal = $producto->precio * $item['cantidad_vendida'];
                    $total += $subtotal;

                    DetallesVenta::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $producto->id,
                        'cantidad_vendida' => $item['cantidad_vendida'],
                        'precio_unidad' => $producto->precio,
                        'subtotal' => $subtotal,
                    ]);

                    $producto->decrement('stock', $item['cantidad_vendida']);
                }

                $venta->update(['total' => $total]);
            });

            // ✅ Esto ya funcionará bien, porque $venta tiene valor
            $venta->load('detalle_ventas.producto', 'contacto');

            return response()->json([
                'message' => 'Venta registrada correctamente',
                'venta' => $venta
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al registrar la venta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $venta = Venta::findOrFail($id);

        return response()->json($venta, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $venta = Venta::find($id);
        $venta->delete();

        return response()->json(["mensaje" => "venta eliminado"]);
    }

    public function reportePDFVentas(string $id) {
        
        $ventas = Venta::with(['detalle_ventas.producto', 'contacto'])->findOrFail($id);

        // return response()->json($ventas, 200);
        
        $pdf = Pdf::loadView('pdf.ventas', ["venta" => $ventas]);
        // return $pdf->download('ventas.pdf');
        return $pdf->stream("lista_ventas.pdf");
    }
}

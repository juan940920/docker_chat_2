<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallesVenta extends Model
{
    protected $fillable = [
        'cantidad_vendida',
        'precio_unidad',
        'subtotal',
        'venta_id',
        'producto_id',
    ];
    
    public $timestamps = false;

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}

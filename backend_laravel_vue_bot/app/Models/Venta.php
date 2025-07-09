<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        'contacto_id',
        'total',
    ];
    
    public function contacto()
    {
        return $this->belongsTo(Contacto::class);
    }

    public function detalle_ventas(){
        return $this->hasMany(DetallesVenta::class);
    }
}

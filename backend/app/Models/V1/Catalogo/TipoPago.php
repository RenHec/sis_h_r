<?php

namespace App\Models\V1\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoPago extends Model
{
    use HasFactory;
    //use SoftDeletes;

    public const CREDITO = 3;
    public const PRODUCTO = 6;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tipos_pagos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'movimientos_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a',
        'updated_at' => 'datetime:d/m/Y h:i:s a',
        'deleted_at' => 'datetime:d/m/Y h:i:s a'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the movimientos associated with the configuracion.
     *
     * @return array
     */
    public function movimientos()
    {
        return $this->hasOne(Movimiento::class, 'movimientos_id', 'id');
    }
}

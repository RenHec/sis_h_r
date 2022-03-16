<?php

namespace App\Models\V1\Hotel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HHabitacionFoto extends Model
{
    use HasFactory;

    protected $table = 'h_habitaciones_fotos';

    protected $fillable = ['foto', 'h_habitaciones_id'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['picture'];

    /**
     * Get the user's link base64 foto.
     *
     * @return string
     */
    public function getPictureAttribute()
    {
        return Storage::disk('habitacion')->exists($this->foto) ? Storage::disk('habitacion')->url($this->foto) : null;
    }
}

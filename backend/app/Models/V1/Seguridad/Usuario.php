<?php

namespace App\Models\V1\Seguridad;

use Laravel\Passport\HasApiTokens;
use App\Models\V1\Seguridad\UsuarioRol;
use App\Models\V1\Principal\Empleado;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    const USUARIO_ADMINISTRADOR = 'ADMINISTRADOR';
    const USUARIO_REGULAR = 'REGULAR';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cui',
        'empleado_id',
        'admin',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'email_verified_at',
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime:d-m-Y',
        'created_at' => 'datetime:d-m-Y',
        'updated_at' => 'datetime:d-m-Y',
        'deleted_at' => 'datetime:d-m-Y'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Search user passport.
     *
     * @return object
     */
    public function findForPassport($username)
    {
        return $this->where('cui', $username)->first();
    }

    /**
     * Set the user's password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Scope a query to only include softDelted
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTodos($query)
    {
        return $query->with('roles.rol', 'empleado.municipio')->withTrashed()->where('cui', '<>', '0000000000001')->orderByDesc('id');
    }

    /**
     * Get the empleado associated with the user.
     *
     * @return object
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id', 'id');
    }

    /**
     * Get the roles associated with the user.
     *
     * @return array
     */
    public function roles()
    {
        return $this->hasMany(UsuarioRol::class, 'usuario_id', 'id');
    }
}

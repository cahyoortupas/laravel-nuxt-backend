<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;                 // <-- import JWTSubject
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // <-- import Auth Laravel

class Customer extends Authenticatable implements JWTSubject // <-- tambahkan "Authenticatable" dan "JWTSubject

{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'email', 'email_verified_at', 'password', 'remember_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * invoice
     *
     * @return void
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * reviews
     *
     * @return void
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * getCreatedAtAttribute
     *
     * @param  mixed $date
     * @return void
     */
    public function getCreatedAtAttribute($date)
    {   
        $value = Carbon::parse($date);
        $parse = $value->locale('id');
        return $parse->translatedFormat('l, d F Y');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}

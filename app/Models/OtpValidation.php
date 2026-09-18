<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpValidation extends Model
{
    protected $fillable = [
    'phone',
    'otp',
    'type',
    'is_verified',
    'attempts',
    'expire_at'
];
    protected $dates = ['expire_at'];
    public $timestamps = true;
}

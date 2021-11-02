<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class wdp_User extends Model
{
    public $timestamps = false;
    protected $fillable=['user_login','user_pass','user_email','user_url','user_registered','user_activation_key'];
    protected $table='wdp_users';
    use HasFactory;
}

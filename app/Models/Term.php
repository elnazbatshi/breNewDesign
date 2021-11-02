<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    public $timestamps = false;
    protected $fillable=['term_id','name'];
    protected $table='wdp_terms';
    use HasFactory;
}

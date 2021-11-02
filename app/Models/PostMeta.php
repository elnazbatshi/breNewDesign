<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostMeta extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable=['meta_id','post_id','meta_key','meta_value'];
    protected $table='wdp_postmeta';


}

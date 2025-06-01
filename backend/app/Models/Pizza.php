<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pizza extends Model
{
    protected $fillable = [
        'type_id',
        'slug',
        'size',
        'price',
    ];

    public function type(){

        return $this->belongsTo(Type::class);
    
    }

    public function details(){

        return $this->hasMany(Detail::class);
    
    }

}

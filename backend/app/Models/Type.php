<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'category_id',
        'ingredient_id',
    ];

    public function category(){

        return $this->belongsTo(\App\Models\Category::class);
    
    }

    public function ingredients(){

        return $this->belongsToMany(Ingredient::class);
    
    }

    public function pizzas(){

        return $this->hasMany(Pizza::class);
    
    }
}

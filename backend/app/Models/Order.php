<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'id',
        'created_at',
    ];

    public function details()
    {
        return $this->hasMany(Detail::class);
    }

}

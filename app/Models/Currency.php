<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = "currencies";
    protected $fillable = ['product_id', 'price', 'name', 'code', 'symbol'];
  
    public function product(){
        return $this->belongsTo(Product::class);
    }
}

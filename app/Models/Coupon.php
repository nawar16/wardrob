<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable=['code','type','value','shop_id','status'];

    public static function findByCode($code){
        return self::where('code',$code)->first();
    }
    public function discount($total){
        if($this->type=="fixed"){
            return $this->value;
        }
        elseif($this->type=="percent"){
            return ($this->value /100)*$total;
        }
        else{
            return 0;
        }
    }
    public function shop()
    {
        return $this->belongsTo('App\Shop', 'shop_id');
    }
}

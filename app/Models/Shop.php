<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable=['name','short_des','description','photo',
    'address','phone','email','logo','user_id','product_id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function coupons(){
        return $this->hasMany('App\Models\Coupon');
    }
    public function products()
    {
        return $this->belongsToMany('App\Models\Product')
        ->withPivot('stock', 'condition')
        ->with(['cat_info','sub_cat_info'])
    	->withTimestamps();
    }
    public function product_by_id($id)
    {
        return Product::where('id', $id)->with(['shops'])->first();
    }
    public static function getShopBySlug($slug){
        return Shop::with(['products','user'])->where('name',$slug)->where('status','active')->first();
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use DB;
use Illuminate\Support\Str;
use App\Http\Controllers\RecommendationController as RecommendationController;

class ProductController extends Controller
{
    public function index()
    {
        dd();
        if(auth()->user()->role == 'admin')
        {
            $products=Product::getAllProduct();
            return view('backend.product.index')->with('products', $products);
        }
        $shop = auth()->user()->shops;
        $products = $shop->products()->get();
        $pivot =  $shop->products();
        return view('backend.product.index')->with('products', $products)->with('shop', $shop);
    }

    public function create()
    {
        $brand=Brand::get();
        $category=Category::where('is_parent',1)->get();
        if(auth()->user()->role == 'admin')
        {
            return view('backend.product.create')->with('categories',$category)->with('brands',$brand);
        }
        return view('backend.product.create')->with('categories',$category)->with('brands',$brand);
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'title'=>'string|required',
            'summary'=>'string|required',
            'description'=>'string|nullable',
            'photo'=>'string|required',
            'size'=>'nullable',
            'stock'=>"required|numeric",
            'cat_id'=>'required|exists:categories,id',
            'brand_id'=>'nullable|exists:brands,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'is_featured'=>'sometimes|in:1',
            'status'=>'required|in:active,inactive',
            'condition'=>'required|in:default,new,hot',
            'price'=>'required|numeric',
            'discount'=>'nullable|numeric'
        ]);

        $data=$request->all();
        $slug=Str::slug($request->title);
        $count=Product::where('slug',$slug)->count();
        if($count>0){
            $slug=$slug.'-'.date('ymdis').'-'.rand(0,999);
        }
        $data['slug']=$slug;
        $data['is_featured']=$request->input('is_featured',0);
        $size=$request->input('size');
        if($size){
            $data['size']=implode(',',$size);
        }
        else{
            $data['size']='';
        }

        $shop = auth()->user()->shops;
        $product = new Product([
            'title' => $data['title'],
            'summary' => $data['summary'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'photo' => $data['photo'],
            'size' => $data['size'],
            'cat_id' => $data['cat_id'],
            'brand_id' => $data['brand_id'],
            'child_cat_id' => $data['child_cat_id'],
            'is_featured' => $data['is_featured'],
            'status' => $data['status'],
            'price' => $data['price'],
            'discount' => $data['discount'],
        ]);
        $status = $shop->products()->save($product,[
            'stock' => $data['stock'],
            'condition' => $data['condition'],
        ]);
        $rec = new RecommendationController();
        $res = $rec->add_product($product);
        if($res[1] == "insert ok") $product->lite_id = $res[0];
        if($status){
            request()->session()->flash('success','Product Successfully added');
        }
        else{
            request()->session()->flash('error','Please try again!!');
        }
        return redirect()->route('product.index');

    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $shop = auth()->user()->shops;
        $brand = Brand::get();
        $product = Product::findOrFail($id);
        $category = Category::where('is_parent',1)->get();
        $items = Product::where('id',$id)->get();

        if(auth()->user()->role == 'admin')
        {
            return view('backend.product.edit')->with('product',$product)
            ->with('brands',$brand)
            ->with('categories',$category)
            ->with('items',$items)
            ->with('shop',$shop);
        }
        return view('backend.product.edit')->with('product',$product)
                    ->with('brands',$brand)
                    ->with('categories',$category)
                    ->with('items',$items)
                    ->with('shop',$shop);
    }

    public function update(Request $request, $id)
    {
        $product=Product::findOrFail($id);
        $this->validate($request,[
            'title'=>'string|required',
            'summary'=>'string|required',
            'description'=>'string|nullable',
            'photo'=>'string|required',
            'size'=>'nullable',
            'stock'=>"required|numeric",
            'cat_id'=>'required|exists:categories,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'is_featured'=>'sometimes|in:1',
            'brand_id'=>'nullable|exists:brands,id',
            'status'=>'required|in:active,inactive',
            'condition'=>'required|in:default,new,hot',
            'price'=>'required|numeric',
            'discount'=>'nullable|numeric'
        ]);

        $data=$request->all();
        $data['is_featured']=$request->input('is_featured',0);
        $size=$request->input('size');
        if($size){
            $data['size']=implode(',',$size);
        }
        else{
            $data['size']='';
        }

        $shop = auth()->user()->shops;
        $product->update([
            'title' => $data['title'],
            'summary' => $data['summary'],
            'description' => $data['description'],
            'photo' => $data['photo'],
            'size' => $data['size'],
            'cat_id' => $data['cat_id'],
            'brand_id' => $data['brand_id'],
            'child_cat_id' => $data['child_cat_id'],
            'is_featured' => $data['is_featured'],
            'status' => $data['status'],
            'price' => $data['price'],
            'discount' => $data['discount'],
        ]);
        //////////////////////////////////////////////////
        $status = \DB::table('product_shop')
            ->where('product_id', $product->id)
            ->update([
                'stock' =>$data['stock'],
                'condition' =>$data['condition']
            ]);
        if($status){
            request()->session()->flash('success','Product Successfully updated');
        }
        else{
            request()->session()->flash('error','Please try again!!');
        }
        return redirect()->route('product.index');
    }

    public function destroy($id)
    {
        $product=Product::findOrFail($id);
        $shop = auth()->user()->shops;
        $shop->products()->detach($id);
        $status=$product->delete();
        
        if($status){
            request()->session()->flash('success','Product successfully deleted');
        }
        else{
            request()->session()->flash('error','Error while deleting product');
        }
        return redirect()->route('product.index');
    }
}

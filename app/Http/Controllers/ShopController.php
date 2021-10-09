<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use Illuminate\Support\Str;
class ShopController extends Controller
{
    public function index()
    {
        $shop=Shop::orderBy('id','DESC')->paginate(10);
        return view('backend.shop.index')->with('shops',$shop);
    }

    public function create()
    {
        return view('backend.shop.create');
    }

    public function edit($id)
    {
        $shop=Shop::findOrFail($id);
        return view('backend.shop.edit')->with('shop',$shop);
    }

    public function update(Request $request, $id)
    {
        $shop=Shop::findOrFail($id);
        $this->validate($request,[
            'name'=>'string|required|max:50',
            'description'=>'string|nullable',
            'photo'=>'string|required',
            'status'=>'required|in:active,inactive',
        ]);
        $data=$request->all();
        $status=$shop->fill($data)->save();
        if($status){
            request()->session()->flash('success','shop successfully updated');
        }
        else{
            request()->session()->flash('error','Error occurred while updating shop');
        }
        return redirect()->route('shop.index');
    }


    public function destroy($id)
    {
        $shop=Shop::findOrFail($id);
        $status=$shop->delete();
        if($status){
            request()->session()->flash('success','Shop successfully deleted');
        }
        else{
            request()->session()->flash('error','Error occurred while deleting shop');
        }
        return redirect()->route('shop.index');
    }
}

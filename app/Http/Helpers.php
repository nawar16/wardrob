<?php
namespace App\Http;
use App\Models\Message;
use App\Models\Category;
use App\Models\PostTag;
use App\Models\PostCategory;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\Shipping;
use App\Models\Cart;
use Illuminate\Http\Request;
use AshAllenDesign\LaravelExchangeRates\ExchangeRate;
use Guzzle\Http\Exception\ClientErrorResponseException;
use App\Http\controllers\RecommendationController;
use carbon\Carbon;
use Auth;

class Helper{
    public static function messageList()
    {
        return Message::whereNull('read_at')->orderBy('created_at', 'desc')->get();
    } 
    public static function getAllCategory(){
        $category=new Category();
        $menu=$category->getAllParentWithChild();
        return $menu;
    } 
    public static function getAllCurrency(){
        $currencies = collect(['USD', 'SYP']);
        return $currencies;
    } 
    public static function getHeaderCategory(){
        $category = new Category();
        // dd($category);
        $menu=$category->getAllParentWithChild();

        if($menu){
            ?>
            
            <li>
            <a href="javascript:void(0);">Category<i class="ti-angle-down"></i></a>
                <ul class="dropdown border-0 shadow">
                <?php
                    foreach($menu as $cat_info){
                        if($cat_info->child_cat->count()>0){
                            ?>
                            <li><a href="<?php echo route('product-cat',$cat_info->slug); ?>"><?php echo $cat_info->title; ?></a>
                                <ul class="dropdown sub-dropdown border-0 shadow">
                                    <?php
                                    foreach($cat_info->child_cat as $sub_menu){
                                        ?>
                                        <li><a href="<?php echo route('product-sub-cat',[$cat_info->slug,$sub_menu->slug]); ?>"><?php echo $sub_menu->title; ?></a></li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </li>
                            <?php
                        }
                        else{
                            ?>
                                <li><a href="<?php echo route('product-cat',$cat_info->slug);?>"><?php echo $cat_info->title; ?></a></li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </li>
        <?php
        }
    }
    public static function getHeaderCurrency(){

        $menu = collect(['USD', 'SYP']);
        if($menu){
            ?>
            
            <li>
            <a href="javascript:void(0);">Currency<i class="ti-angle-down"></i></a>
                <ul class="dropdown border-0 shadow">
                <?php
                    foreach($menu as $c){
                        ?>
                            <li><a href="<?php echo route('change_currency',$c);?>"><?php echo $c; ?></a></li>
                        <?php
                    }
                    ?>
                </ul>
            </li>
        <?php
        }
    }
    public static function getHeaderWishlist(){
        $total_prod=0;
        $total_amount=0;
        if(session('wishlist'))
        {
            foreach(session('wishlist') as $wishlist_items)
            {
                $total_prod+=$wishlist_items['quantity'];
            }
        }
        ?>  
            <a href="{{route('wishlist')}}" class="single-icon"><i class="fa fa-heart-o"></i> <span class="total-count">{{Helper::wishlistCount()}}</span></a>
       <?php
    }

    public static function productCategoryList($option='all'){
        if($option='all'){
            return Category::orderBy('id','DESC')->get();
        }
        return Category::has('products')->orderBy('id','DESC')->get();
    }

    public static function postTagList($option='all'){
        if($option='all'){
            return PostTag::orderBy('id','desc')->get();
        }
        return PostTag::has('posts')->orderBy('id','desc')->get();
    }

    public static function postCategoryList($option="all"){
        if($option='all'){
            return PostCategory::orderBy('id','DESC')->get();
        }
        return PostCategory::has('posts')->orderBy('id','DESC')->get();
    }
    // Cart Count
    public static function cartCount($user_id=''){
       
        if(Auth::check()){
            if($user_id=="") $user_id=auth()->user()->id;
            return Cart::where('user_id',$user_id)->where('order_id',null)->sum('quantity');
        }
        else{
            return 0;
        }
    }
    // relationship cart with product
    public function product(){
        return $this->hasOne('App\Models\Product','id','product_id');
    }

    public static function getAllProductFromCart($user_id=''){
        if(Auth::check()){
            if($user_id=="") $user_id=auth()->user()->id;
            return Cart::with('product')->where('user_id',$user_id)->where('order_id',null)->get();
        }
        else{
            return 0;
        }
    }
    // Total amount cart
    public static function totalCartPrice($user_id=''){
        if(Auth::check()){
            if($user_id=="") $user_id=auth()->user()->id;
            return Cart::where('user_id',$user_id)->where('order_id',null)->sum('amount');
        }
        else{
            return 0;
        }
    }
    // Wishlist Count
    public static function wishlistCount($user_id=''){
       
        if(Auth::check()){
            if($user_id=="") $user_id=auth()->user()->id;
            return Wishlist::where('user_id',$user_id)->where('cart_id',null)->sum('quantity');
        }
        else{
            return 0;
        }
    }
    public static function getAllProductFromWishlist($user_id=''){
        if(Auth::check()){
            if($user_id=="") $user_id=auth()->user()->id;
            return Wishlist::with('product')->where('user_id',$user_id)->where('cart_id',null)->get();
        }
        else{
            return 0;
        }
    }
    public static function totalWishlistPrice($user_id=''){
        if(Auth::check()){
            if($user_id=="") $user_id=auth()->user()->id;
            return Wishlist::where('user_id',$user_id)->where('cart_id',null)->sum('amount');
        }
        else{
            return 0;
        }
    }

    // Total price with shipping and coupon
    public static function grandPrice($id,$user_id){
        $order=Order::find($id);
        dd($id);
        if($order){
            $shipping_price=(float)$order->shipping->price;
            $order_price=self::orderPrice($id,$user_id);
            return number_format((float)($order_price+$shipping_price),2,'.','');
        }else{
            return 0;
        }
    }


    // Admin home
    public static function earningPerMonth(){
        $month_data=Order::where('status','delivered')->get();
        // return $month_data;
        $price=0;
        foreach($month_data as $data){
            $price = $data->cart_info->sum('price');
        }
        return number_format((float)($price),2,'.','');
    }

    public static function shipping(){
        return Shipping::orderBy('id','DESC')->get();
    }

    public static function exchangeCurrency(Request $request) 
    {
        $amount = ($request->amount)?($request->amount):(1);
        $apikey = '1cbe0bae9a9c9c6a251b';
    
        $from_Currency = urlencode($request->from_currency);
        $to_Currency = urlencode($request->to_currency);
        $query =  "{$from_Currency}_{$to_Currency}";
        $json = file_get_contents("http://free.currencyconverterapi.com/api/v5/convert?q={$query}&compact=y&apiKey={$apikey}");
        $obj = json_decode($json, true);
        $val = $obj["$query"];
        $total = $val['val'] * 1;
    
        $formatValue = number_format($total, 2, '.', '');
        return $formatValue;
        $data = "$amount $from_Currency = $to_Currency $formatValue";   
    }

    public static function format_price($price_data) 
    {
        $amount = $price_data['amount'];
        $apikey = '1cbe0bae9a9c9c6a251b';
        $from_Currency = urlencode($price_data['from_currency']);
        $to_Currency = urlencode($price_data['to_currency']);
        $query =  "{$from_Currency}_{$to_Currency}";
        $json = file_get_contents("http://free.currencyconverterapi.com/api/v5/convert?q={$query}&compact=y&apiKey={$apikey}");
        $obj = json_decode($json, true);
        $val = $obj["$query"];
        $total = $val['val'] * 1;
        $formatValue = number_format($total, 2, '.', '');
        return $formatValue;  
    }

    public static function price($product)
    {
        if(\Session::get('currency') == null || \Session::get('currency') == 'Currency')
        {
            \Session::put('currency', 'SYP');
        }
        $currency = \Session::get('currency');
        return $product->currency($currency)->price;
    }

    ///////////////////////////////////////--RECOMMENDATION--///////////////////////////////////////

    public static function customer_recommend($customer_name)
    {
        $client = new \GuzzleHttp\Client([
            'headers' => [ 'Content-Type' => 'application/json', 'X-CSRF-Token' => csrf_token() ]
        ]);
        //
        $response = $client->post('127.0.0.1:5000/api/predict',
            ['body' => json_encode(
                [
                    "customer_name" => $customer_name
                ]
            )]
        );
        dd($response);
        return (json_decode($response->getBody())->response);
    }
    public static function get_recommendation()
    {
        $rec = new RecommendationController();
        $res = $rec->customer_recommend(auth()->user()->name);
        $res = substr($res,1,strlen($res)-2);
        $products = explode( ',', $res);
        $recommendations = array();
        foreach($products as $product)
        {
            $item_name = explode( '\'', $product);
            $item_name = $item_name[1];
            $item = \App\Models\Product::where('title', $item_name)->first();
            if(!is_null($item)) array_push($recommendations, $item);
        }
        return $recommendations;
    } 
    public static function product_recommend($product_id)
    {
        try{
            $client = new \GuzzleHttp\Client([
                'headers' => [ 'Content-Type' => 'application/json', 'X-CSRF-Token' => csrf_token() ]
            ]);
            $response = $client->post('127.0.0.1:5000/api/predict/product',
                ['body' => json_encode(
                    [
                        "product_id" => $product_id
                    ]
                )]
            );
            $response_message = json_decode($response->getBody(), true);
            if(array_key_exists('response',$response_message)) {
                $response_message = $response_message['response'];
                $products = array_map(function($response_message_item){
                    $item = \App\Models\Product::where('title', $response_message_item)->first();
                    return $item;
                }, $response_message);
                return $products;     
            } else{
                $response_message = $response_message['error'];
                return $response_message; 
            }
        } catch(\Exception $ex){
            throw new \Exception('Error found', 500);
        }
    }  
}

?>
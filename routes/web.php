<?php

use Illuminate\Support\Facades\Route;
use App\Http\controllers\RecommendationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register'=>false]);

Route::get('user/login','FrontendController@login')->name('login.form');
Route::post('user/login','FrontendController@loginSubmit')->name('login.submit');
Route::get('user/logout','FrontendController@logout')->name('user.logout');

Route::get('user/register','FrontendController@register')->name('register.form');
Route::post('user/register','FrontendController@registerSubmit')->name('register.submit');
// Reset password
Route::get('password-reset', 'FrontendController@showResetForm')->name('password.reset'); 
// Socialite 
Route::get('login/{provider}/', 'Auth\LoginController@redirect')->name('login.redirect');
Route::get('login/{provider}/callback/', 'Auth\LoginController@Callback')->name('login.callback');

Route::get('/','FrontendController@home')->name('home');

// Frontend Routes
Route::get('/home', 'FrontendController@index');
Route::get('/about-us','FrontendController@aboutUs')->name('about-us');
Route::get('/contact','FrontendController@contact')->name('contact');
Route::post('/contact/message','MessageController@store')->name('contact.store');
Route::get('product-detail/{slug}','FrontendController@productDetail')->name('product-detail');
Route::post('/product/search','FrontendController@productSearch')->name('product.search');
Route::get('/product-cat/{slug}','FrontendController@productCat')->name('product-cat');
Route::get('/product-sub-cat/{slug}/{sub_slug}','FrontendController@productSubCat')->name('product-sub-cat');
Route::get('/product-brand/{slug}','FrontendController@productBrand')->name('product-brand');
// Cart section
Route::get('/add-to-cart/{slug}','CartController@addToCart')->name('add-to-cart')->middleware('customer');
Route::post('/add-to-cart','CartController@singleAddToCart')->name('single-add-to-cart')->middleware('customer');
Route::get('cart-delete/{id}','CartController@cartDelete')->name('cart-delete');
Route::post('cart-update','CartController@cartUpdate')->name('cart.update');

Route::get('/cart',function(){
    return view('frontend.pages.cart');
})->name('cart');
Route::get('/checkout','CartController@checkout')->name('checkout')->middleware('customer');
// Wishlist
Route::get('/wishlist',function(){
    return view('frontend.pages.wishlist');
})->name('wishlist');
Route::get('/wishlist/{slug}','WishlistController@wishlist')->name('add-to-wishlist')->middleware('customer');
Route::get('wishlist-delete/{id}','WishlistController@wishlistDelete')->name('wishlist-delete');
Route::post('cart/order','OrderController@store')->name('cart.order');
Route::get('order/pdf/{id}','OrderController@pdf')->name('order.pdf');
Route::get('/income','OrderController@incomeChart')->name('product.order.income');
Route::get('/user/chart','AdminController@userPieChart')->name('user.piechart');
Route::get('/product-grids','FrontendController@productGrids')->name('product-grids');
Route::get('/product-lists','FrontendController@productLists')->name('product-lists');
Route::match(['get','post'],'/filter','FrontendController@productFilter')->name('shop.filter');
// Order Track
Route::get('/product/track','OrderController@orderTrack')->name('order.track');
Route::post('product/track/order','OrderController@productTrackOrder')->name('product.track.order');
// Blog
Route::get('/blog','FrontendController@blog')->name('blog');
Route::get('/blog-detail/{slug}','FrontendController@blogDetail')->name('blog.detail');
Route::get('/blog/search','FrontendController@blogSearch')->name('blog.search');
Route::post('/blog/filter','FrontendController@blogFilter')->name('blog.filter');
Route::get('blog-cat/{slug}','FrontendController@blogByCategory')->name('blog.category');
Route::get('blog-tag/{slug}','FrontendController@blogByTag')->name('blog.tag');
// Shop
Route::get('/shop','FrontendController@shop')->name('shop');
Route::get('/shop-detail/{slug}','FrontendController@shopDetail')->name('shop.detail');
// NewsLetter
Route::post('/subscribe','FrontendController@subscribe')->name('subscribe');

// Product Review
Route::resource('/review','ProductReviewController');
Route::post('product/{slug}/review','ProductReviewController@store')->name('review.store');

// Post Comment 
Route::post('post/{slug}/comment','PostCommentController@store')->name('post-comment.store');
Route::resource('/comment','PostCommentController');
// Coupon
Route::post('/coupon-store','CouponController@couponStore')->name('coupon-store');
// Payment
Route::get('payment', 'PayPalController@payment')->name('payment');
Route::get('cancel', 'PayPalController@cancel')->name('payment.cancel');
Route::get('payment/success', 'PayPalController@success')->name('payment.success');


// Backend section start
Route::group(['prefix'=>'/admin','middleware'=>['auth','admin']],function(){
    Route::get('/','AdminController@index')->name('admin');
    Route::get('/file-manager',function(){
        return view('backend.layouts.file-manager');
    })->name('file-manager');
    // user route
    Route::resource('users','UsersController');
    // Banner
    Route::resource('banner','BannerController');
    // Shop
    Route::resource('shop','ShopController');
    // Brand
    Route::resource('brand','BrandController');
    // Profile
    Route::get('/profile','AdminController@profile')->name('admin-profile');
    Route::post('/profile/{id}','AdminController@profileUpdate')->name('profile-update');
    // Category
    Route::resource('/category','CategoryController');
    // Product
    Route::resource('/product','ProductController');
    // Ajax for sub category
    Route::post('/category/{id}/child','CategoryController@getChildByParent');
    // POST category
    Route::resource('/post-category','PostCategoryController');
    // Post tag
    Route::resource('/post-tag','PostTagController');
    // Post
    Route::resource('/post','PostController');
    // Message
    Route::resource('/message','MessageController');
    Route::get('/message/five','MessageController@messageFive')->name('messages.five');

    // Order
    Route::resource('/order','OrderController');
    // Shipping
    Route::resource('/shipping','ShippingController');
    // Coupon
    Route::resource('/coupon','CouponController');
    // Settings
    Route::get('settings','AdminController@settings')->name('settings');
    Route::post('setting/update','AdminController@settingsUpdate')->name('settings.update');

    // Notification
    Route::get('/notification/{id}','NotificationController@show')->name('admin.notification');
    Route::get('/notifications','NotificationController@index')->name('all.notification');
    Route::delete('/notification/{id}','NotificationController@delete')->name('notification.delete');
    // Password Change
    Route::get('change-password', 'AdminController@changePassword')->name('change.password.form');
    Route::post('change-password', 'AdminController@changPasswordStore')->name('change.password');

});


// User section start
Route::group(['namespace'=>'Seller','prefix'=>'/user','middleware'=>['user']],function(){
    Route::get('/','HomeController@index')->name('user');
    Route::get('/income','HomeController@incomeChart')->name('user.product.order.income');
     // Profile
     Route::get('/profile','HomeController@profile')->name('user-profile');
     Route::post('/profile/{id}','HomeController@profileUpdate')->name('user-profile-update');
    //  Order
    Route::get('/order',"HomeController@orderIndex")->name('user.order.index');
    Route::get('/order/show/{id}',"HomeController@orderShow")->name('user.order.show');
    Route::delete('/order/delete/{id}','HomeController@userOrderDelete')->name('user.order.delete');
    // Product Review
    Route::get('/user-review','HomeController@productReviewIndex')->name('user.productreview.index');
    Route::delete('/user-review/delete/{id}','HomeController@productReviewDelete')->name('user.productreview.delete');
    Route::get('/user-review/edit/{id}','HomeController@productReviewEdit')->name('user.productreview.edit');
    Route::patch('/user-review/update/{id}','HomeController@productReviewUpdate')->name('user.productreview.update');
    
    // Post comment
    Route::get('user-post/comment','HomeController@userComment')->name('user.post-comment.index');
    Route::delete('user-post/comment/delete/{id}','HomeController@userCommentDelete')->name('user.post-comment.delete');
    Route::get('user-post/comment/edit/{id}','HomeController@userCommentEdit')->name('user.post-comment.edit');
    Route::patch('user-post/comment/udpate/{id}','HomeController@userCommentUpdate')->name('user.post-comment.update');
    
    // Password Change
    Route::get('change-password', 'HomeController@changePassword')->name('user.change.password.form');
    Route::post('change-password', 'HomeController@changPasswordStore')->name('user.change.password');

    //--ADDED--//
    // Product
    Route::resource('/product','ProductController');
    // Coupon
    Route::resource('/coupon','CouponController');
    // Settings
    Route::get('settings','HomeController@settings')->name('user.settings');
    Route::post('setting/update','HomeController@settingsUpdate')->name('user.settings.update');

});


// Customer section start
Route::group(['namespace'=>'Customer','prefix'=>'/customer','middleware'=>['customer']],function(){
    Route::get('/','HomeController@index')->name('customer');
    // Profile
     Route::get('/profile','HomeController@profile')->name('customer-profile');
     Route::post('/profile/{id}','HomeController@profileUpdate')->name('customer-profile-update');
    // Order
    Route::get('/order',"HomeController@orderIndex")->name('customer.order.index');
    Route::get('/order/show/{id}',"HomeController@orderShow")->name('customer.order.show');
    Route::delete('/order/delete/{id}','HomeController@userOrderDelete')->name('customer.order.delete');
    // Product Review
    Route::get('/customer-review','HomeController@productReviewIndex')->name('customer.productreview.index');
    Route::delete('/customer-review/delete/{id}','HomeController@productReviewDelete')->name('customer.productreview.delete');
    Route::get('/customer-review/edit/{id}','HomeController@productReviewEdit')->name('customer.productreview.edit');
    Route::patch('/customer-review/update/{id}','HomeController@productReviewUpdate')->name('customer.productreview.update');
    
    // Post comment
    Route::get('customer-post/comment','HomeController@userComment')->name('customer.post-comment.index');
    Route::delete('customer-post/comment/delete/{id}','HomeController@userCommentDelete')->name('customer.post-comment.delete');
    Route::get('customer-post/comment/edit/{id}','HomeController@userCommentEdit')->name('customer.post-comment.edit');
    Route::patch('customer-post/comment/udpate/{id}','HomeController@userCommentUpdate')->name('customer.post-comment.update');
    
    // Password Change
    Route::get('change-password', 'HomeController@changePassword')->name('customer.change.password.form');
    Route::post('change-password', 'HomeController@changPasswordStore')->name('customer.change.password');

});

/////////////////////////////////////////////////////////////////////////////////////////////////////////
Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

Route::get('linkstorage', function(){
    Artisan::call('storage:link');
});

Route::get('currency','CurrencyController@index');
Route::post('currency','CurrencyController@exchangeCurrency');
Route::get('change_currency/{c}','CurrencyController@change_currency')->name('change_currency');

Route::get('token', function(){
    return csrf_token();
});

Route::get('/rec', 'RecommendationController@get_test');
Route::post('/add_item', 'RecommendationController@add_item');
Route::post('/add_customer', 'RecommendationController@add_customer');
Route::post('/add_purchase', 'RecommendationController@add_purchase');
Route::post('/customer_recommend', 'RecommendationController@customer_recommend');

Route::get('/hat', function(){
    $product = \App\Models\Product::find(1);
    
    $rec = new RecommendationController();
    $res = $rec->product_recommend(1);
    return $res;
    if($res[1] == "insert ok") return "ok";
    return $res[1];

    //return $recommendations;
    /*$rec = new RecommendationController();
    $res = $rec->customer_recommend('riccardo');
    $res = substr($res,1,strlen($res)-2);
    $products = explode( ',', $res);
    $recommendations = array();
    foreach($products as $product)
    {
        $item_name = explode( '\'', $product);
        $item_name = $item_name[1];
        $item = App\Models\Product::where('title', $item_name)->first();
        if(!is_null($item)) array_push($recommendations, $item->title);
    }
    return $recommendations;*/
});

Route::get('add_rec', function(){
    $product = \App\Models\Product::find(11);
    $rec = new RecommendationController();
    $res = $rec->add_product($product);
    if($res[1] == "insert ok") $product->lite_id = $res[0];
    /*$products = \App\Models\Product::all();
    foreach($products as $product)
    {
        $rec = new RecommendationController();
        $res = $rec->add_product($product);
        if($res[1] == "insert ok") $product->lite_id = $res[0];
    }*/
    return 'ok';
});
Route::get('dd', function(){
        $rec = new RecommendationController();
        $res = $rec->customer_recommend('customer');


    return $res;
});

//F7941D  #f6931d;
//8abaae 
//5ca08e 


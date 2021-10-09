@extends('frontend.layouts.master')

@section('title','Shop Detail page')

@section('main-content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li><a href="{{route('shop')}}">Shops<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Shop Single Sidebar</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->
        
    <!-- Start Shop Single -->
    <section class="blog-single section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-12">
                    <div class="blog-single-main">
                        <div class="row">
                            <div class="col-12">
                                <div class="blog-detail">
                                    <h2 class="blog-title">{{$shop->name}}</h2>
                                    <div class="blog-meta">
                                        <span class="author"><a href="javascript:void(0);"><i class="fa fa-user"></i>By {{$shop->user['name']}}</a><a href="javascript:void(0);"><i class="fa fa-calendar"></i>{{$shop->created_at->format('M d, Y')}}</a><a href="javascript:void(0);"><i class="fa fa-comments"></i></a></span>
                                    </div>
                                    <div class="content">
                                        @if($shop->short_des)
                                        <blockquote> <i class="fa fa-quote-left"></i> {!! ($shop->short_des) !!}</blockquote>
                                        @endif
                                        <p>{!! ($shop->description) !!}</p>
                                    </div>
                                </div>
                            </div>

	
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ End Shop Single -->
    		<!-- Start Shop Product -->
	<div class="product-area most-popular related-product section">
        <div class="container">
            <div class="row">
				<div class="col-12">
					<div class="section-title">
						<h2>Shop's Products</h2>
					</div>
				</div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="owl-carousel popular-slider">
					@foreach($shop->products as $data)
					    @if(!is_null($data))  
                                <!-- Start Single Product -->
                                <div class="single-product">
                                    <div class="product-img">
										<a href="{{route('product-detail',$data->slug)}}">
											@php 
												$photo=explode(',',$data->photo);
											@endphp
                                            <img class="default-img" src="{{$photo[0]}}" alt="{{$photo[0]}}">
                                            <img class="hover-img" src="{{$photo[0]}}" alt="{{$photo[0]}}">
                                            <span class="price-dec">{{$data->discount}} % Off</span>
                                                                    {{-- <span class="out-of-stock">Hot</span> --}}
                                        </a>
                                        <div class="button-head">
                                            <div class="product-action">
                                                <a data-toggle="modal" data-target="#modelExample" title="Quick View" href="#"><i class=" ti-eye"></i><span>Quick Shop</span></a>
                                                <a title="Wishlist" href="#"><i class=" ti-heart "></i><span>Add to Wishlist</span></a>
                                                <a title="Compare" href="#"><i class="ti-bar-chart-alt"></i><span>Add to Compare</span></a>
                                            </div>
                                            <div class="product-action-2">
                                                <a title="Add to cart" href="#">Add to cart</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="product-content">
                                        <h3><a href="{{route('product-detail',$data->slug)}}">{{$data->title}}</a></h3>
                                        <div class="product-price">
                                            @php 
                                                $after_discount=(Helper::price($data)-(($data->discount*Helper::price($data))/100));
                                            @endphp
                                            <span class="old">${{number_format(Helper::price($data),2)}}</span>
                                            <span>${{number_format($after_discount,2)}}</span>
                                        </div>
                                      
                                    </div>
                                </div>
                                <!-- End Single Product -->
						@endif
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
	<!-- End Shop Product Area -->
	
@endsection
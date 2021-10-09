@extends('frontend.layouts.master')

@section('title','Shops Page')

@section('main-content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Shop Grid Sidebar</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->
        
    <!-- Start Blog Single -->
    <section class="blog-single shop-blog grid section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-12">
                    <div class="row">
                        @foreach($shops as $shop)
                        {{-- {{$shop}} --}}
                            <div class="col-lg-6 col-md-6 col-12">
                                <!-- Start Single Blog  -->
                                <div class="shop-single-blog">
                                <a href="{{route('shop.detail',$shop->name)}}" class="title">
                                <img src="{{$shop->photo}}" alt="{{$shop->photo}}">
                                    <div class="content">
                                        @php 
                                            $owner_info=DB::table('users')->select('name')->where('id',$shop->user_id)->get();
                                        @endphp
                                        <p class="date"><i class="fa fa-calendar" aria-hidden="true"></i> {{$shop->created_at->format('d M, Y. D')}}
                                            <span class="float-right">
                                                <i class="fa fa-user" aria-hidden="true"></i> 
                                                @foreach($owner_info as $data)
                                                    @if($data->name)
                                                        {{$data->name}}
                                                    @else
                                                        Anonymous
                                                    @endif
                                                @endforeach
                                            </span>
                                        </p>
                                        {{$shop->name}}</a>
                                        <p>{!! html_entity_decode($shop->short_des) !!}</p>
                                        <a href="{{route('shop.detail',$shop->name)}}" class="more-btn">More Information</a>
                                    </div>
                                </div>
                                <!-- End Single Blog  -->
                            </div>
                        @endforeach
                        <div class="col-12">
                            <!-- Pagination -->
                            {{-- {{$shops->appends($_GET)->links()}} --}}
                            <!--/ End Pagination -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ End Blog Single -->
@endsection
@push('styles')
    <style>
        .pagination{
            display:inline-flex;
        }
    </style>

@endpush
<!DOCTYPE html>
<html>
<head>
  <title>Order @if($order)- {{$order->order_number}} @endif</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>

@if($order)
<style type="text/css">
  .invoice-header {
    background: #f7f7f7;
    padding: 10px 20px 10px 20px;
    border-bottom: 1px solid gray;
  }
  .site-logo {
    margin-top: 20px;
  }
  .invoice-right-top h3 {
    padding-right: 20px;
    margin-top: 20px;
    color: black;
    font-size: 30px!important;
    font-family: serif;
  }
  .invoice-left-top {
    border-left: 4px solid black;
    padding-left: 20px;
    padding-top: 20px;
  }
  .invoice-left-top p {
    margin: 0;
    line-height: 20px;
    font-size: 16px;
    margin-bottom: 3px;
  }
  thead {
    background: black;
    color: #FFF;
  }
  .authority h5 {
    margin-top: -10px;
    color: black;
  }
  .thanks h4 {
    color: black;
    font-size: 25px;
    font-weight: normal;
    font-family: serif;
    margin-top: 20px;
  }
  .site-address p {
    line-height: 6px;
    font-weight: 300;
  }
  .table tfoot .empty {
    border: none;
  }
  .table-bordered {
    border: none;
  }
  .table-header {
    padding: .75rem 1.25rem;
    margin-bottom: 0;
    background-color: rgba(0,0,0,.03);
    border-bottom: 1px solid rgba(0,0,0,.125);
  }
  .table td, .table th {
    padding: .30rem;
  }
</style>
  <div class="invoice-header">
    <div class="float-left site-logo">
    <h4>{{env('APP_NAME')}}</h4>
    <p>{{env('APP_ADDRESS')}}</p>
    </div>
    <div class="float-right site-address">
    </div>
    <div class="clearfix"></div>
  </div>
  <div class="invoice-description">
    <div class="invoice-left-top float-left">
      <h6>Invoice to</h6>
       <h3>{{$order->first_name}} {{$order->last_name}}</h3>
       <div class="address">
        <p>
          <strong>Country: </strong>
          {{$order->country}}
        </p>
        <p>
          <strong>Address: </strong>
          {{ $order->address1 }} OR {{ $order->address2}}
        </p>
         <p><strong>Phone:</strong> {{ $order->phone }}</p>
         <p><strong>Email:</strong> {{ $order->email }}</p>
       </div>
    </div>
    <div class="invoice-right-top float-right" class="text-right">
      <h3>Invoice #{{$order->order_number}}</h3>
      <p>{{ $order->created_at->format('D d m Y') }}</p>
      {{-- <img class="img-responsive" src="data:image/png;base64, {{ base64_encode(QrCode::format('png')->size(150)->generate(route('order.show', $order->id )))}}"> --}}
    </div>
    <div class="clearfix"></div>
  </div>
  <section class="order_details pt-3">
    <div class="table-header">
    </div>
    <table class="table table-bordered table-stripe">
      <thead>
        <tr>
          <th scope="col" class="col-6">Product</th>
          <th scope="col" class="col-3">Quantity</th>
          <th scope="col" class="col-3">Total</th>
        </tr>
      </thead>
      <tbody>
      @foreach($order->cart_info as $cart)
      @php 
        $product=DB::table('products')->select('title')->where('id',$cart->product_id)->get();
      @endphp
        <tr>
          <td><span>
              @foreach($product as $pro)
                {{$pro->title}}
              @endforeach
            </span></td>
          <td>x{{$cart->quantity}}</td>
          <td><span>${{number_format($cart->price,2)}}</span></td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </section>

  <section class="order_details pt-3">
    <div class="table-header">
    </div>
    <table class="table table-bordered table-stripe">
      <thead>
        <tr>
          <th scope="col" class="col-6">Subtotal</th>
          <th scope="col" class="col-3">Shipping</th>
          <th scope="col" class="col-3">Total</th>
        </tr>
      </thead>
      <tbody>
      <tr>
          <td> <span>${{number_format($order->sub_total,2)}}</span></td>
      {{-- @if(!empty($order->coupon))
          <td><span>-{{$order->coupon->discount(Helper::orderPrice($order->id, $order->user->id))}}{{Helper::base_currency()}}</span></td>
          @else <td>NoN</td>
      @endif --}}
      @php
        $shipping_charge=DB::table('shippings')->where('id',$order->shipping_id)->pluck('price');
      @endphp
          <td><span>${{number_format($shipping_charge[0],2)}}</span></td>
          <td>
            <span>
                ${{number_format($order->total_amount,2)}}
            </span>
          </td>
      </tr>
      </tbody>
    </table>
  </section>


  <div class="thanks mt-3">
    <h4>Thank you for your order !!</h4>
  </div>
  <div class="clearfix"></div>
  <p>Phone: <a href="tel:{{env('APP_PHONE')}}">{{env('APP_PHONE')}}</a></p>
  <p>Email: <a href="mailto:{{env('APP_EMAIL')}}">{{env('APP_EMAIL')}}</a></p>
@else
  <h5 class="text-danger">Invalid</h5>
@endif
</body>
</html>
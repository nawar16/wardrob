<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class RecommendationController extends Controller
{


    public function add_item($name)
    {
        
        $client = new \GuzzleHttp\Client([
            'headers' => [ 'Content-Type' => 'application/json', 'X-CSRF-Token' => csrf_token() ]
        ]);
        $response = $client->post('127.0.0.1:5000/api/new/item',
            ['body' => json_encode(
                [
                    "id" => 900,
                    'name' => $name
                ]
            )]
        );
        return (json_decode($response->getBody())->status);
        //return json_decode($response->getBody()->getContents());
    }  
    public function add_customer($name)
    {
        $client = new \GuzzleHttp\Client([
            'headers' => [ 'Content-Type' => 'application/json', 'X-CSRF-Token' => csrf_token() ]
        ]);
        $customer = array( 
                "id" => 900,
                'name' => $name
        );
        $response = $client->post('127.0.0.1:5000/api/new/customer',
            ['body' => json_encode($customer)]
        );
        return (json_decode($response->getBody())->status);
    } 
    public function add_purchase($customer_name, $item_name)
    {
        $client = new \GuzzleHttp\Client([
            'headers' => [ 'Content-Type' => 'application/json', 'X-CSRF-Token' => csrf_token() ]
        ]);
        $response = $client->post('127.0.0.1:5000/api/new/purchcase',
            ['body' => json_encode(
                [
                    "customer_name" => $customer_name,
                    "item_name" => $item_name
                ]
            )]
        );

        $response_message = json_decode($response->getBody(), true);
        if(array_key_exists('status',$response_message)) {
            $response_message = $response_message['status'];
            return $response_message;     
        } else{
            $response_message = $response_message['error'];
            return $response_message; 
        }       
        //return (json_decode($response->getBody())->status);
    } 
    public function customer_recommend($customer_name)
    {
        $client = new \GuzzleHttp\Client([
            'headers' => [ 'Content-Type' => 'application/json', 'X-CSRF-Token' => csrf_token() ]
        ]);
        $response = $client->post('127.0.0.1:5000/api/predict',
            ['body' => json_encode(
                [
                    "customer_name" => $customer_name
                ]
            )]
        );
        $response_message = json_decode($response->getBody(), true);
        if(array_key_exists('response',$response_message)) {
            $response_message = $response_message['response'];
            return $response_message;     
        } else{
            $response_message = $response_message['error'];
            return $response_message; 
        }
        //return (json_decode($response->getBody())->response);
    } 

    
    public function add_product($product)
    {
        
        $client = new \GuzzleHttp\Client([
            'headers' => [ 'Content-Type' => 'application/json', 'X-CSRF-Token' => csrf_token() ]
        ]);
        $brand = $product->brand->title ?? "brand";
        $tags = $product->cat_info->title ?? "tag";
        $response = $client->post('http://127.0.0.1:5000/api/new/product',
            ['body' => json_encode(
                [
                    "id" => $product->id,
                    "title" => $product->title,
                    "description" => $product->slug,
                    "brand" => $brand,
                    "tags" => $tags,
                    "combined_tags" => $tags,
                    "combined_features" => '"'.$product->title.' '.$brand.' '.$tags.'"'
                ]
            )]
        );
        return [json_decode($response->getBody())->id, json_decode($response->getBody())->status];
    }
    public function product_recommend($product_id)
    {
        try{
            $client = new \GuzzleHttp\Client([
                'headers' => [ 'Content-Type' => 'application/json', 'X-CSRF-Token' => csrf_token() ]
            ]);
            $response = $client->post('http://127.0.0.1:5000/api/predict/product',
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

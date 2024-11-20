<?php

namespace App\Http\Controllers;

use App\Http\Requests\Address\CreateAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Http\Responses\Response;
use App\Services\OrderService;
use Exception;

class OrderController extends Controller
{
    public $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    public function save_address(CreateAddressRequest $request)
    {
        $data = [];
        try{
            $data = $this->orderService->save_address($request->validated());
            return Response::Success($data['address'] , $data['message']);
        }catch(Exception $e){
            $message  = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function make_primary($address_id)
    {
        $data = [];
        try{
            $data = $this->orderService->make_primary($address_id);
            return Response::Success($data['new_primary'] , $data['message']);
        }catch(Exception $e){
            $message  = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
//    public function show_addresses()
//    {
//        $data = [];
//        try{
//            $data = $this->orderService->show_addresses();
//            return Response::Success($data['addresses'] , $data['message']);
//        }catch(Exception $e){
//            $message  = $e->getMessage();
//            return Response::Error($data,$message);
//        }
//    }
    public function edit_address($address_id,UpdateAddressRequest $request)
    {
        $data = [];
        try{
            $data = $this->orderService->edit_address($address_id,$request->validated());
            return Response::Success($data['address'] , $data['message']);
        }catch(Exception $e){
            $message  = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function delete_address($address_id)
    {
        $data = [];
        try{
            $data = $this->orderService->delete_address($address_id);
            return Response::Success($data['address'] , $data['message']);
        }catch(Exception $e){
            $message  = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function deliver_to_my_address($cart_id)
    {
        $data = [];
        try{
            $data = $this->orderService->deliver_to_my_address($cart_id);
            return response()->json([
                'addresses' => $data['addresses'],
                'products' => $data['products'],
                'shipping_methods' => $data['shipping_methods'],
                'payment_methods' => $data['payment_methods'],
                'message' => $data['message']
            ]);
        }catch(Exception $e){
            $message  = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
}

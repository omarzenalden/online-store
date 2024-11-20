<?php
namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrderService{

    /*
     * 1-deliver to my address
     * information:
     * country , full name , phone , city , street name , building name , apartment name
     * 2- make more than one address to the user and save it in the table, the address can edit, delete and make primary location
     * 3- add payment method:
     * information:
     * card number , expires date , CVV , name on card
     * 4-there are chooses to the payment method
     * use valid credit card
     * credit card , pay on delivery
     * 5- there is coupon discount
     * 5-there are order summary with subtotal price for each product and total price for all and the price after coupon discount.
     * 6-place the order in the all tables and show order with the status
     * 7- the statuses can be updated by admin
     */
    /*
     * 2-pick it up myself
     * information:
     * the store location , full name , phone number
     * also payment methods
     * use valid credit card
     */


    public function save_address($request)
    {
        $primary = Address::query()->where('user_id' , Auth::id())
            ->where('primary' , '=' , 1)
            ->first();

        $request['user_id'] = Auth::id();
        $address = Address::query()->create($request);
        if (!$primary){
            $address['primary'] = 1;
            $address->save();
        }else {
            $address['primary'] = 0;
            $address->save();
        }
        return [
            'address' => $address,
            'message' => 'address saved successfully',
        ];
    }

//    public function show_addresses()
//    {
//        $addresses = Address::query()
//            ->where('user_id',Auth::id())
//            ->orderBy('primary' , 'desc')
//            ->get();
//        if ($addresses){
//            $message = 'getting addresses successfully';
//        }else{
//            $addresses = null;
//            $message = 'not found';
//        }
//        return [
//            'addresses' => $addresses,
//            'message' => $message
//        ];
//    }

    public function make_primary($address_id)
    {
        $new_primary = Address::query()
            ->where('user_id' , Auth::id())
            ->where('id' , $address_id)
            ->first();
        if ($new_primary){
            $current_primary = Address::query()
            ->where('user_id' , Auth::id())
            ->where('primary' , '=' , 1)
            ->first();
            if ($current_primary){
                $current_primary->primary = 0;
                $current_primary->save();
            }
            $new_primary->primary = 1;
            $new_primary->save();
            $message = 'primary address switched successfully';
        }else{
            $new_primary = null;
            $message = 'not found';
        }

        return [
            'new_primary' => $new_primary,
            'message' => $message
        ];

    }
    public function edit_address($address_id,$request)
    {
        $address = Address::query()
            ->where('id' , $address_id)
            ->update($request);
        $address = Address::find($address_id);
        return [
            'address' => $address,
            'message' => 'address updated successfully'
        ];
    }
    public function delete_address($address_id)
    {
        $address = Address::query()
            ->where('id' , $address_id)
            ->delete();
        return [
            'address' => '',
            'message' => 'address deleted successfully'
        ];
    }

    public function deliver_to_my_address($cart_id)
    {
        //show the address immediately because there is at least one address before go to place order page
        $addresses = Address::query()
            ->where('user_id',Auth::id())
            ->orderBy('primary' , 'desc')
            ->get();

        $cart_items = Cart::with(['products' => function ($query){
            $query->select('cart_items.*');
        }])
            ->where('user_id', Auth::id())
            ->first();
        $cart_items->products()
            ->where('cart_items.cart_id' , $cart_id)
            ->get();

        //$shipping_methods will be local or external delivery
        $shipping_method = 'local delivery';

        $payment_method = PaymentMethod::all();

        $addresses || $payment_method  ? $message = 'getting all data successfully' : $message = 'not found';

        return [
            'addresses' => $addresses,
            'products' => $cart_items,
            'shipping_methods' => $shipping_method,
            'payment_methods' => $payment_method,
            'message' => $message
        ];
    }

    public function place_order($request)
    {
        //handle payment methods
        //if($payment_method == ''){
        //}
    }
}


/*
 * shipping system:
 * shipping method : internal => if the country = UAE , external otherwise
 * shipping cost input by admin
 *
 * */

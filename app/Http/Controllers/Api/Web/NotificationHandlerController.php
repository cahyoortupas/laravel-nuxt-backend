<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationHandlerController extends Controller
{    
    /**
     * index
     *
     * @param  mixed $request
     * @return void
     */
    public function index(Request $request)
    {
        $payload      = $request->getContent();
        $notification = json_decode($payload);
      
        $validSignatureKey = hash("sha512", $notification->order_id . $notification->status_code . $notification->gross_amount . config('services.midtrans.serverKey'));

        if ($notification->signature_key != $validSignatureKey) {
            return response(['message' => 'Invalid signature'], 403);
        }

        $transaction  = $notification->transaction_status;
        $type         = $notification->payment_type;
        $orderId      = $notification->order_id;

        //data tranaction
        $data_transaction = Invoice::where('invoice', $orderId)->first();

        if ($transaction == 'capture') {
 
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {

                //nothing

            }

        } elseif ($transaction == 'settlement') {

            /**
            *   update invoice to success
            */
            $data_transaction->update([
                'status' => 'success'
            ]);

            //update stock product 
            foreach($data_transaction->orders()->get() as $order) {

                $product = Product::whereId($order->product_id)->first();
                $product->update([
                    'stock' => $product->stock - $order->qty
                ]);
            }


        } elseif($transaction == 'pending'){

            
            /**
            *   update invoice to pending
            */
            $data_transaction->update([
                'status' => 'pending'
            ]);


        } elseif ($transaction == 'deny') {

            
            /**
            *   update invoice to failed
            */
            $data_transaction->update([
                'status' => 'failed'
            ]);


        } elseif ($transaction == 'expire') {

            
            /**
            *   update invoice to expired
            */
            $data_transaction->update([
                'status' => 'expired'
            ]);


        } elseif ($transaction == 'cancel') {

            /**
            *   update invoice to failed
            */
            $data_transaction->update([
                'status' => 'failed'
            ]);

        }
    }
}

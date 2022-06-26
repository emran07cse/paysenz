<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'client_id'                     => 'required|Integer',
            'order_id_of_merchant'          => 'required|String',
            'amount'                        => 'required|String',
            'currency_of_transaction'       => 'required|String',
            'buyer_name'                    => 'required|String',
            'buyer_email'                   => 'required|String',
            'buyer_address'                 => 'required|String',
            'buyer_contact_number'          => 'required|String',
            'ship_to'                       => 'required|String',
            'shipping_email'                => 'required|String',
            'shipping_address'              => 'required|String',
            'shipping_contact_number'       => 'required|String',
            'order_details'                 => 'required|String',
            'callback_url'                  => 'required|String',
            'comma_separated_references'    => 'required|String',
            'expected_response_type'        => 'required|String',
        ];
    }
}

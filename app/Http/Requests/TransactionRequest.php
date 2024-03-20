<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
   * @return array<string, mixed>
   */
  public function rules()
  {
    return [
      'type' => 'required|in:pembelian,penjualan',
      'date' => 'required|date',
      'qty' => 'required|integer',
      'cost' => 'integer',
      'price' => 'required|integer',
      'total_cost' => 'integer',
      'qty_balance' => 'integer',
      'value_balance' => 'integer',
      'hpp' => 'integer',
    ];
  }
}

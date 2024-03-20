<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\TransactionRequest;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $transactions = Transaction::all();

    return response()->json([
      'status' => 'success',
      'data' => $transactions
    ]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $rules = [
      'type' => 'required|in:pembelian,penjualan',
      'date' => 'required|date',
      'qty' => 'required|integer|min:0',
      'cost' => 'integer',
      'price' => 'required|integer',
      'total_cost' => 'integer',
      'qty_balance' => 'integer|min:0',
      'value_balance' => 'integer',
      'hpp' => 'integer',
    ];

    $data = $request->all();

    // dd($data);

    $validator = Validator::make($data, $rules);

    if ($validator->fails()) {
      return response()->json([
        'status' => 'error',
        'errors' => $validator->errors()
      ], 400);
    }

    $lastData = Transaction::latest()->first();

    // return response()->json([
    //   'data' => $lastData
    // ]);

    if ($lastData['qty_balance'] == 0 || $lastData['qty_balance'] < $data['qty']) {
      return response()->json([
        'status' => 'error',
        'message' => 'Stock kosong'
      ]);
    }

    if ($data['type'] == 'pembelian') {
      $data['cost'] = $data['price'];
    } else {
      $data['qty'] = - ($data['qty']);
      $data['cost'] = $lastData['hpp'];
    }

    $data['total_cost'] = $data['qty'] * $data['cost'];

    if (!$lastData) {
      $data['qty_balance'] = $data['qty'];
      $data['value_balance'] = $data['total_cost'];
    } else {
      $data['qty_balance'] = $lastData['qty_balance'] + $data['qty'];
      $data['value_balance'] = $lastData['value_balance'] + $data['total_cost'];
    }

    $data['hpp'] = $data['value_balance'] / $data['qty_balance'];

    $transaction = Transaction::create($data);

    return response()->json([
      'status' => 'success',
      'data' => $transaction
    ]);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $rules = [
      'type' => 'required|in:pembelian,penjualan',
      'date' => 'required|date',
      'qty' => 'required|integer|min:0',
      'cost' => 'integer',
      'price' => 'required|integer',
      'total_cost' => 'integer',
      'qty_balance' => 'integer',
      'value_balance' => 'integer',
      'hpp' => 'integer',
      'stock' => 'integer|min:0'
    ];

    $data = $request->all();

    $validator = Validator::make($data, $rules);

    if ($validator->fails()) {
      return response()->json([
        'status' => 'error',
        'errors' => $validator->errors()
      ], 400);
    }

    $transaction = Transaction::find($id);

    // Preivous Data

    $previousData = Transaction::where('id', '<',  $transaction['id'])->latest()->first();

    if ($previousData['qty_balance'] == 0 || $previousData['qty_balance'] < $data['qty']) {
      return response()->json([
        'status' => 'error',
        'message' => 'Stock kosong'
      ]);
    }

    if ($data['type'] == 'pembelian') {
      $data['cost'] = $data['price'];
    } else {
      $data['qty'] = - ($data['qty']);
      $data['cost'] = $previousData['hpp'];
    }

    $data['total_cost'] = $data['qty'] * $data['cost'];

    if (!$previousData) {
      $data['qty_balance'] = $data['qty'];
      $data['value_balance'] = $data['total_cost'];
    } else {
      $data['qty_balance'] = $previousData['qty_balance'] + $data['qty'];
      $data['value_balance'] = $previousData['value_balance'] + $data['total_cost'];
    }

    $data['hpp'] = $data['value_balance'] / $data['qty_balance'];

    // AfterData

    $afterData = Transaction::where('id', '>', $transaction['id'])->first();

    $afterData['cost'] = $data['hpp'];
    $afterData['qty_balance'] = $afterData['qty'] + $data['qty_balance'];
    $afterData['value_balance'] = $afterData['total_cost'] + $data['value_balance'];
    $afterData['hpp'] = $afterData['value_balance'] / $afterData['qty_balance'];;

    $transaction->fill($data);
    $transaction->save();

    $afterData->update();

    return response()->json([
      'status' => 'success',
      'data' => $transaction
    ]);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $transaction = Transaction::find($id);

    if (!$transaction) {
      return response()->json([
        'status' => 'error',
        'message' => 'Transaction not found'
      ], 404);
    }

    // $transaction->delete();

    return response()->json([
      'status' => 'success',
      'message' => 'course delete',
      'data' => $transaction
    ]);
  }
}

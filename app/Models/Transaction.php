<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
  use HasFactory;

  protected $table = 'transactions';

  protected $fillable = [
    'type',
    'date',
    'qty',
    'cost',
    'price',
    'total_cost',
    'qty_balance',
    'value_balance',
    'hpp',
  ];
}

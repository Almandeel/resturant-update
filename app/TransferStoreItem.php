<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferStoreItem extends Model
{
    protected $fillable = ['item_id', 'transfer_store_id', 'quantity'];
}

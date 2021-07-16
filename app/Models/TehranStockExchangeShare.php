<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TehranStockExchangeShare extends \Eloquent
{
    protected $fillable = [
        'stock_code',
        'group_code',
        'symbol',
        'group_name',
        'instId',
        'insCode',
        'title',
        'group_name',
        'sectorPe',
        'shareCount',
        'estimatedEps',
    ];
}

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

    const stock_code = 'sid';
    const instrument_id = 'instid';
    const symbol = 's';
    const symbol_name = 'sn';
    const end_price = 'ep';
    const yesterday_end_price = 'ydep';
    const first_price = 'o';
    const price = 'c';
    const min_price = 'l';
    const max_price = 'h';
    const transactions_count = 'tc';
    const transactions_volume = 'v';
    const transactions_value = 'tv';
    const estimated_eps = 'eps';
    const group_code = 'gp';
    const group_name = 'gpn';
    const day_max_price = 'dmxp';
    const day_min_price = 'dmnp';
    const share_count = 'sc';
    const update_at = 'ut';
    const value = 'val';
    const change_from_yesterday = 'chfyd';
    const change_percentage = 'chp';
    const change_state_from_yesterday = 'chsfyd';

    const FIELD_TITLES = [
        self::stock_code => 'stock_code',
        self::instrument_id => 'instrument_id',
        self::symbol => 'symbol',
        self::symbol_name => 'symbol_name',
        self::end_price => 'end_price',
        self::yesterday_end_price => 'yesterday_price',
        self::first_price => 'first_price',
        self::price => 'price',
        self::min_price => 'min_price',
        self::max_price => 'max_price',
        self::transactions_count => 'transactions_count',
        self::transactions_volume => 'transactions_volume',
        self::transactions_value => 'transactions_value',
        self::estimated_eps => 'estimated_eps',
        self::group_code => 'group_code',
        self::group_name => 'group_name',
        self::day_max_price => 'day_max_price',
        self::day_min_price => 'day_min_price',
        self::share_count => 'share_count',
        self::update_at => 'update_at',
        self::value => 'value',
        self::change_from_yesterday => 'change_from_yesterday',
        self::change_percentage => 'change_percentage',
        self::change_state_from_yesterday => 'change_state_from_yesterday',
    ];

    public static function getTitle($abbr) {
        return self::FIELD_TITLES[$abbr];
    }
}

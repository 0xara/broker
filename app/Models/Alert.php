<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends \Eloquent
{
    protected $fillable = [
        'broker_id',
        'symbol',
        'operator',
        'current_position',
        'price',
        'active',
    ];

    const GT = 'GT';
    const GTE = 'GTE';
    const LT = 'LT';
    const LTE = 'LTE';
    const CROSS = 'CROSS';

    const OPERATOR_TITLES = [
        self::GT => 'Greater Than',
        self::LT => 'Lower Than',
        self::GTE => 'Greater Than Or Equal',
        self::LTE => 'Lower Than Or Equal',
        self::CROSS => 'Cross',
    ];

    const OPERATOR_FA_TITLES = [
        self::GT => 'بزرگتر',
        self::LT => 'کوچکتر',
        self::GTE => 'بزرگتر یا مساوی',
        self::LTE => 'کوچکتر یا مساوی',
        self::CROSS => 'عبور',
    ];

    const UP_POSITION = 'UP';
    const DOWN_POSITION = 'DOWN';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function broker()
    {
        return $this->belongsTo(Broker::class, 'broker_id');
    }
}

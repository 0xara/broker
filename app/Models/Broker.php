<?php

namespace App\Models;

use App\Models\Alert;
use Illuminate\Database\Eloquent\Model;

class Broker extends \Eloquent
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function alerts()
    {
        return $this->hasMany(Alert::class,'exchange_id');
    }
}

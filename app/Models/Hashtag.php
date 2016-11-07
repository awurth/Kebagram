<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{

    protected $table = 'hashtag';

    protected $primaryKey = 'id';

    public function pictures()
    {
        return $this->belongsToMany('App\Models\Picture');
    }
}

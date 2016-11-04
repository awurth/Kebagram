<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comment';

    protected $primaryKey = 'id';

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function picture()
    {
        return $this->belongsTo('App\Models\Picture');
    }
}

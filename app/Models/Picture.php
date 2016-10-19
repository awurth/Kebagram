<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model {

    protected $table = 'picture';

    protected $primaryKey = 'id';

    public function user()
    {
		$this->belongsTo('App\User');
	}
}

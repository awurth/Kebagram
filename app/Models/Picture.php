<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model {

    protected $table = 'picture';

    protected $primaryKey = 'id';

    public function getWebPath()
    {
        return 'uploads/images/kebabs/' . $this->id . '.jpg';
    }

    public function user()
    {
		return $this->belongsTo('App\Models\User');
	}
}

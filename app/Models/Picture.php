<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PictureRating;

class Picture extends Model
{

    protected $table = 'picture';

    protected $primaryKey = 'id';

    public function getWebPath()
    {
        return 'uploads/images/kebabs/' . $this->id . '.jpg';
    }

    public function getRate()
    {
       $this->PictureRating()->count();
    }

    public function user()
    {
		return $this->belongsTo('App\Models\User');
	}

    public function PictureRating()
    {
        return $this->hasMany('App\Models\PictureRating');
    }
}

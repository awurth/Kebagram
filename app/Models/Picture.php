<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Auth;

class Photo extends Model {

    protected $table = 'photos';
    protected $primaryKey = 'photo_id';

    /**
    * Change the description
    * @param string $description
    */

    public function setDescription($description) {
        $this->update([
            'description' => $description
        ]);
    }

    public function user(){
		$this->belongsTo('App\User');
	}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_email',
        'user_name',
        'user_slug',
        'user_password_hash',
    ];

/**
* Change the password
*
* @param string $password
*
*/
    public function setPassword($password)
    {
        $this->update([
            'user_password_hash' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    public function following()
    {
        return $this->belongsToMany('App\Models\User', 'subscription', 'follower_id', 'followed_id');
    }

    public function followers()
    {
        return $this->belongsToMany('App\Models\User', 'subscription', 'followed_id', 'follower_id');
    }

    /**
    * The user has one picture.
    *
    * @return mixed
    */
    public function pictures()
    {
        return $this->hasMany('App\Models\Picture');
    }
}

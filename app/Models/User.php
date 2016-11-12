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

    public function getAvatarPath()
    {
        return file_exists(__DIR__ . '/../../public/uploads/images/users/' . $this->user_id . '.jpg') ? 'uploads/images/users/' . $this->user_id . '.jpg' : 'images/default.png';
    }

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

    public function pictures()
    {
        return $this->hasMany('App\Models\Picture');
    }

    public function pictureRating()
    {
        return $this->hasMany('App\Models\PictureRating');
    }
}

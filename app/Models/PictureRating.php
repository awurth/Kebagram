<?php
/**
 * Created by PhpStorm.
 * User: Xavier
 * Date: 02/11/2016
 * Time: 09:08
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PictureRating extends Model
{
    protected $table = 'picture_rating';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function liker($idUser,$idPhoto){
        $this->rate = '1';
        $this->user_id = $idUser;
        $this->picture_id = $idPhoto;
        $this->save();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function Picture()
    {
        return $this->hasOne('App\Models\Picture');
    }



}
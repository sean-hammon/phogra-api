<?php

namespace App\Phogra\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = array('parent_id','title','slug','description');

    public function photos()
    {
        return $this->belongsToMany('Photo', 'gallery_photos');
    }
}
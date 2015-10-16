<?php

namespace App\Phogra\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = array('parent_id', 'title', 'slug', 'description', 'node');

    public function photos()
    {
        return $this->belongsToMany('App\Phogra\Eloquent\Photo', 'gallery_photos');
    }
}
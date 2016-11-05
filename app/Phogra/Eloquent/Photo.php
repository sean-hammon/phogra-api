<?php

namespace App\Phogra\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Photo
 *
 * Still trying to decide if this is how I want to handle this.
 */
class Photo extends Model
{
    protected $fillable = array('title', 'slug', 'short_desc', 'long_desc', 'canonical_gallery_id');

    public function galleries()
    {
        return $this->belongsToMany('App\Phogra\Eloquent\Gallery', 'gallery_photos');
    }

    public function files()
    {
        return $this->hasMany('App\Phogra\Eloquent\File');
    }

    public function tags()
    {
	    return $this->hasMany('App\Phogra\Eloquent\Tag');
    }
}
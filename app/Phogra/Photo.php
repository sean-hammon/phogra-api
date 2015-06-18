<?php

class Photo extends Eloquent
{
    protected $fillable = array('title','slug','description');

    public function galleries()
    {
        return $this->belongsToMany('Gallery', 'gallery_photos');
    }

    public function files()
    {
        return $this->belongsToMany('Files', 'photo_files');
    }
}
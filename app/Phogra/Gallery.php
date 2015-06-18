<?php

class Gallery extends Eloquent
{
    protected $fillable = array('parent_id','title','slug','description');

    public function photos()
    {
        return $this->belongsToMany('Photo', 'gallery_photos');
    }
}
<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sean
 * Date: 10/9/2016
 * Time: 8:51 PM
 */

namespace app\Phogra\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
	protected $fillable = array('name');

	public function photos()
	{
		return $this->belongsToMany('App\Phogra\Eloquent\Photo', 'photo_tags');
	}
}
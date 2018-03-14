<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{

    protected $fillable = ['project_id', 'user_id'];

    public function user(){

        return $this->belongsTo('App\User');

    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    
    protected $fillable = ['title', 'description'];

    public function projectLogs(){

        return $this->hasMany('App\Log');

    }

    public function users(){

        return $this->belongsToMany('App\User', 'project_users');

    }

    
}

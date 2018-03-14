<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{

    protected $fillable = [
        'project_id', 'user_id', 'log_message', 'status'
    ];

    public static function addLog($project_id, $log_message, $user_id){

       return Log::create([
            "project_id" => $project_id,
            "user_id" => $user_id,
            "log_message" => $log_message,
            "status" => "pending"
        ]);

    }

    public function user(){

        return $this->belongsTo('App\User');

    }
}

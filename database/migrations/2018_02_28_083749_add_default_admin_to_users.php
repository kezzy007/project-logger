<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\User;

class AddDefaultAdminToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        User::firstOrCreate([
            "name" => "Admin",
            "role" => "admin",
            "skill" => "M.D",
            "profile_pic" => "",
            "email" => "admin@admin.com",
            "password" => bcrypt("admin"),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

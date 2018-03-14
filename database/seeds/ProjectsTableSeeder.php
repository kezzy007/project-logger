<?php

use Illuminate\Database\Seeder;
use App\Project;
use App\ProjectUser;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $id1 = Project::firstOrCreate([
            "title" => "BASE STATION",
            "description"  =>  "Complete mast development"
        ])->id;

        $id2 = Project::firstOrCreate([
            "title" => "INTERNET CONFIGURATION",
            "description"  =>  "Configure internet routers"
        ])->id;

        $id3 = Project::firstOrCreate([
            "title" => "SERVER ADDRESSING",
            "description"  =>  "Configure server ip addresses"
        ])->id;

        ProjectUser::firstOrCreate(
            [
                "project_id" => $id1,
                "user_id"   =>  1,

            ]
            );
        
        ProjectUser::firstOrCreate(
            [
                "project_id" => $id2,
                "user_id"   =>  1,

            ]
            );

        ProjectUser::firstOrCreate(
            [
                "project_id" => $id3,
                "user_id"   =>  1,

            ]
            );
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Log;
use App\Project;
use Storage;
use App\Http\Requests\UserRequest;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // auth()->logout(request());
        
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user();

        return view('home',compact('user'));
    }

    public function getUserId(){
        return \Auth::user()->id;
    }

    public function getUsers(){

        return User::all();

    }

    public function saveAdminLogReview(Request $request){

        $logId = $request['logId'];

        $log = Log::find($logId);

        $logStatus = $request['response'];

        $log->status = trim(strtolower($logStatus));

        $result = $log->update();

        return response()->json(["error" => $result ? false : true, 
                                 "message" => $result ? "Log marked as ".$logStatus : "Operation failed" ]);

    }

    public function saveAssignedUsersForProject(Request $request){

        $validator = \Validator::make($request->all(),[
                        "assignedUsers" => "nullable",
                        "projectId" => "required"
                    ]);

        if($validator->fails()){

            return response()->json(["error"=>true, "message" => "Operation failed"]);

        }

        // Get parameters from request
        $assignedUsers = $request->assignedUsers;
        $project_id = $request->projectId;

        $savedUsers = [];

        // Remove all assigned users to project on pivot table
        \App\Project::find($project_id)->users()->detach();


        // Add all selected users
        foreach($assignedUsers as $assignedUser){
            
            $savedUsers[] = \App\ProjectUser::create([
                                "project_id" => $project_id,
                                "user_id" => $assignedUser['id']
                            ]);

        }
            
        return response()->json([  "error"=>false, "message" => "Users assigned"]);

    }

    public function projects(Request $request){

        
        $user_id = $this->getUserId();

        if( $this->isUser()){

            // Load all projects using the user_id from the pivot table
            $user_assigned_projects = User::find($user_id)->projects;
            
            $projectAndLogs = array();

            //  Loop through each project and fetch logs for each project
            if( count($user_assigned_projects) > 0 ){

                foreach($user_assigned_projects as $user_assigned_project){

                    $user_assigned_project->project_logs =  $this->getLogsForAssignedProject($user_assigned_project->id, $user_id);

                }
            }

            return response()->json(['allProjects' => $user_assigned_projects]);

        }
        else{
            
            // User is admin

            $allProjectsAndLogs = Project::with('projectLogs.user')->get();
            
            $allProjectsAssignedUsers = \App\ProjectUser::with('user')->get();

            return response()->json(['allProjects' => $allProjectsAndLogs, "allProjectsAssignedUsers" => $allProjectsAssignedUsers]);

        }


    }

    public function isUser(){

        return trim(\Auth::user()->role) == 'user';

    }

    public function getLogsForAssignedProject($assigned_project_id, $user_id){
        
        return Log::where('project_id', $assigned_project_id)
                            ->where('user_id', $user_id)->get();
    }

    public function saveLog(Request $request){

        $project_id = $request->projectId;
       
        $validator = \Validator::make($request->all(),
                                [
                                    'log_message' => 'required:string'
                                ]);
        
        if($validator->fails()){

            return response()->json(["error"=>false, "message"=>"No log message specified"]);

        }

        $log_message = $request->log_message;

        $user_id = \Auth::user()->id;

        $createdLog = Log::addLog($project_id, $log_message, $user_id);

        if($createdLog){
            
            $createdLog->user;  // Initializes the user details that created the log

            return response()->json(["error"=>false, "project_log" => $createdLog, "message" => "Log saved" ]);

        }
        else{
            return response()->json(["error"=>true, "message" => "Operation failed" ]);
        }
        
    }

    public function updateLog(Request $request){
       
        $validator = \Validator::make($request->all(),
                                        [
                                            'log_message' => 'required:string'
                                        ]);
        
        if($validator->fails()){

            return response()->json(["error"=>false, "message"=>"No log message specified"]);

        }

        $log_message = $request->log_message; //    Get log message

        $log = Log::where('id', '=', $request['logId'])->first(); // Get log from db

        $log->log_message = $log_message; // Update message with new message

        $updatedLog = $log->update();

        if($updatedLog){
            
            return response()->json(["error"=>false, "message" => "Log updated" ]);

        }
        else{
            return response()->json(["error"=>true, "message" => "Operation failed" ]);
        }
        
    }

    public function deleteLog(Request $request){

        if(!$request->logId) return response()->json(["error"=>true, "message" => "Missing required parameters " ]);

        $logDeleted = Log::where('id',$request->logId)->delete();

        if($logDeleted){
            return response()->json(["error"=> false, "message" => "Log deleted" ]);
        }

        return response()->json(["error"=>true, "message" => "Operation failed" ]);
    }

    public function addProject(Request $request){

        $validator = \Validator::make($request->all(),[
            "title" => "required|string",
            "description" => "required|string"
        ]);

        if($validator->fails()){
            response()->json(["error" => "Operation failed"]);
        }

        $project = Project::firstOrCreate([
            "title" => $request->title,
            "description" => $request->description
        ]);

        return $project ? response()->json(["project" => $project, "error" => false, "message" => "Project created"]) :
                          response()->json(["error" => true, "message" => "Failed to create project"]);

    }

    public function registerUser(UserRequest $request){

        // Check if is user, If yes prevent further action
        if( $this->isUser() )
            return response()->json(['error'=> true, 'message'=> 'Requires administrative priviledges']);


        if( $this->userWithEmailExists( $request['email']) )
            return response()->json(['error'=> true, 'message'=> 'A user with provided email exists']);


        $user = User::create([
            'name' => $request['name'],
            'password' => bcrypt($request['password']),
            'role' => $request['role'],
            'email' => $request['email'],
            'skill' => $request['skill']
        ]);

        
        if($user){
            return response()->json(['error'=> false, 'message'=> 'User registered', 'user' => $user]);
        }
        else{
            return response()->json(['error'=> true, 'message'=> 'Operation failed']);
        }

    }

    public function userWithEmailExists($email){

        return User::whereRaw('LCASE("'.$email.'") = LCASE(email)')->get()->count() > 0;

    }

    public function adminUpdateUserRecord(Request $request){

        // Check if is user, If yes prevent further action
        if( $this->isUser() )
            return response()->json(['error'=> true, 'message'=> 'Requires administrative priviledges']);

        $user = User::find($request->id)->first();

        $user->name = $request->name ? $request->name : $user->name;
        $user->password = $request->password ? bcrypt($request->password) : $user->password;
        $user->role = $request->role ? $request->role : $user->role;
        $user->email = $request->email ? $request->email : $user->email;
        $user->skill = $request->skill ? $request->skill : $user->skill;

        $result = $user->update();

        if($result){
            return response()->json(['error'=> false, 'message'=> 'Record update successful', 'user' => $user]);
        }
        else{
            return response()->json(['error'=> true, 'message'=> 'Update failed']);
        }

    }

    public function deleteUser(Request $request){

        if( $this->isUser() )
            return response()->json(['error'=> true, 'message'=> 'Requires administrative priviledges']);

        $result = User::find($request['id'])->delete();

        if($result){
            return response()->json(['error'=> false, 'message'=> 'User deleted']);
        }
        else{
            return response()->json(['error'=> true, 'message'=> 'Operation failed']);
        }
    }

    public function updateProfile(Request $request){

        $userRecord = \Auth::user();
        
        $profilePicPath = "";

        if($request->hasFile("file")){
            
            $file = $request->file("file");
            
            $filename = $file->getClientOriginalName();

            if($this->validateFile($filename, 'image')){

                // New file name
                $filename = 'profpic'.$this->randomNumber(10, 100).trim($filename);

                //Save file/document to the Server
                $path = Storage::putFileAs("public", $file, $filename);
                
                if($path){

                    // Delete existing profile picture if existing
                    $oldPicturePath = storage_path()."/app/public/".substr($userRecord->profile_pic, 8);

                    if(is_file($oldPicturePath)){

                        unlink($oldPicturePath);

                    }

                    //  Save new profile pic path to database
                    $newFilePath = "storage".substr(trim($path), 6 );

                    $userRecord->profile_pic = $newFilePath; 

                    $userRecord->update();

                    return response()->json(["error" => false, 
                                             "message" => "Update successful", 
                                             "profile_pic_path" => $newFilePath,
                                             "old_picture_path" => $oldPicturePath
                                             ]);

                }
                else{
                    return response()->json(["error" => true, "message" => "upload failed"]);
                }
            }
            else{  return response()
                            ->json([
                                    "error" => true,
                                    "message" => 
                                        "Image format not supported.
                                        Supported formats include Jpg, png, bmp"
                                    ]);     
            }

        }

        $validator = \Validator::make($request->all(), [
                        "name"=>"required|string",
                        "password"=>"nullable|min:4",
                        "email"=>"required|email",
                    ]);

        if($validator->fails()){

            return response()->json(["error"=>"Update failed"]);

        }
        

        $userRecord->name = $request->name;
        $userRecord->email = $request->email;
        $userRecord->password = empty($request->password) ? $userRecord->password : bcrypt($request->password);

        return $userRecord->update() ? 
                        response()->json(['error'=>false, 'user'=>$userRecord, 'message'=>'Record updated']) 
                        : response()->json(["error" => true, "message" => "Update failed"]);

    }

    public function validateFile($filename, $type){

        $extension = $this->getFileExtension( $filename);
        
        if($type == 'image'){

            $valid_image_extensions = ['jpeg','jpg','bmp','png'];

            if( (!in_array($extension,$valid_image_extensions)) ){
               return false;
            }

            return true;

        }

    }

    public function getFileExtension($filename){

        $tempArray = explode(".", $filename);
        $extension = end($tempArray);

        return $extension;
        
    }

    public function randomNumber($min, $max){

        return mt_rand($min, $max)."".mt_rand($min, $max);

    }
}

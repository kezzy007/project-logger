<template>

    <div class='pt-5 col-md-12'>

        <div class="card w-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <h4 class="card-title">Projects</h4>
                        <h6 class="card-subtitle mb-2 text-muted">Admin assigned</h6>
                    </div>

                    <div class="pl-3 col-6" v-if="isAdmin()">
                        <button type="button" class="btn btn-success h-75 mr-5 float-right" @click="addProject($event)">
                            <i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp; New project
                        </button>
                    </div>
                </div>
                <p class="card-text">

                    <!--Accordion wrapper-->
                    <div class="accordion" id="accordionEx" v-if="allProjects.length > 0" 
                        role="tablist" aria-multiselectable="true">

                        <!-- Accordion card -->
                        <div class="card" v-for="(project, count) in allProjects" :key="count">

                            <!-- Card header -->
                            <div class="card-header" role="tab" :id="'headingOne' + count">
                                <div class="row">

                                    <div class="col-md-6">
                                        <a class="collapsed" role="button" data-toggle="collapse" 
                                            v-bind:href="'#collapse' + count " aria-expanded="false" v-bind:aria-controls="'collapse'+ count ">
                                          
                                            <!-- Project Title and add user button -->
                                            <h5 class="mb-0">
                                                
                                                {{ project.title }} <i class="fa fa-angle-down rotate-icon pull-right"></i>

                                                

                                            </h5>
                                            <span class="text-muted">{{ project.description }}</span>
                                                
                                        </a>
                                    </div>

                                    <!-- Assign users button -->
                                    <div class="col-md-3">
                                        <!-- <span class="badge badge-pill orange" v-if="isAdmin()" @click="assignUsers(project)"> 
                                                        <i class="fa fa-user-plus" aria-hidden="true"></i> Assign users
                                        </span> -->
                                        <button class="btn btn-warning" v-if="isAdmin()" @click="assignUsers(project)">
                                            <i class="fa fa-user-plus" aria-hidden="true"></i> Assign users
                                        </button>
                                    </div>

                                    <!-- Add log button -->
                                    <div class="col-lg-3">
                                        <button type="button" class="btn btn-info h-75" @click="logFor(project)">
                                            <i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp; Log
                                        </button>
                                    </div>


                                </div>
                            </div>

                            <!-- Card body -->
                            <div v-bind:id="'collapse' + count " class="collapse" role="tabpanel" v-bind:aria-labelledby="'headingOne' + count" data-parent="#accordionEx">
                                <div class="card-body">
                                    <!-- All user logs for this project are listed here -->

                                    <div class="list-group" v-if="logsPresent(project.project_logs)">
                                        
                                        <a href="#" 
                                            v-for="(log, logIndex) in project.project_logs" 
                                            :key="logIndex"
                                            class="list-group-item list-group-item-action waves-effect"
                                            @click="viewLog(project, log)">
                                            <!-- "count" here symbolises the project Index  -->

                                            {{ log.log_message }} 
                                            
                                            <b class="pull-right"> 
                                                
                                                <div v-if="isAdmin()">
                                                by {{ log.user.name }} 
                                                </div>
                                                <!-- Displays the time log was published from now -->
                                                <small> {{ getDatePublished(log) }}</small> 

                                                <!-- This displays an icon to mark the status of the project -->
                                                <h6 class="badge badge-pill green" v-if="logStatuses.SEEN == log.status">
                                                    <i class="fa fa-check mr-1 text-white" aria-hidden="true"></i>
                                                    {{ logStatuses.SEEN }}
                                                </h6>

                                                <!-- This displays an icon to indicate the log needs to be reviewed by the user-->
                                                <h6 class="badge badge-pill red" v-if="logStatuses.REVIEW == log.status">
                                                    <i class="fa fa-edit mr-1 text-white" aria-hidden="true"></i>
                                                     {{ logStatuses.REVIEW }}
                                                </h6>

                                                <!-- This displays an icon to indicate the log is pending and needs approval by admin -->
                                                <h6 class="badge badge-pill warning-color" v-if="logStatuses.PENDING == log.status">
                                                    <i class="fa fa-thumb-tack mr-1 text-white" aria-hidden="true"></i>
                                                     {{ logStatuses.PENDING }}
                                                </h6>
                                                
                                                <!-- Button to delete log-->
                                                <button type="button" class="btn btn-danger btn-sm" @click="deleteLog($event, project, log)">
                                                    <i class="fa fa-trash mr-2" aria-hidden="true"></i>
                                                    Delete                            
                                                </button>
                                            </b>

                                        </a>

                                        

                                    </div>
                
                                </div>
                            </div>
                        </div>

                     </div>
                </p>
              
            </div>
        </div>

    </div>

</template>

<script>

    import { EventBus } from '../event-bus.js';

    var moment = require('../../../../node_modules/moment/moment');

    var form  = require('../form.js');

    export default {

        mounted(){
            
            this.InitializeUserRole();

            // Fetch all projects associated with this user id
            this.fetchProjectsAndLogs();

            this.registerGlobalEventListeners(); 

        },
        destroyed(){
            this.deactivateListeners();
        },
        data: function(){

            return {

                userRole: '',
                serverRequest: new form(null),
                allProjects:[],
                allProjectsAssignedUsers: null,
                currentProject: null,
                currentViewingLog: null,
                allUsers: null,
                logStatuses:{
                    REVIEW: "review",
                    PENDING: "pending",
                    SEEN: "seen"
                },
                listenersRegistered: false,
            }

        },
        methods:{

            InitializeUserRole(){

                var user = JSON.parse(window.localStorage.getItem('user'));

                this.userRole = user.role.trim();

                if(this.isAdmin()){

                    // Fetch all users from database
                    this.fetchAllUsersFromDb();

                }

            },

            displayLoadingIcon(){

            },

            fetchAllUsersFromDb(){

                this.serverRequest.post('/users',null)
                    .then((response) => {

                        this.allUsers = response;

                    });

            },

            deleteLog($event, project, log){

                $event.stopPropagation();
                
                this.serverRequest.post('/delete-log',{ 'logId': log.id })
                        .then((response) => {

                            if(!response.error){

                                this.removeLogFromProject(project, log);

                                this.displayToast(response.message, TOAST_OPTIONS.SUCCESS);

                            }
                            else{
                                this.displayToast(response.message, TOAST_OPTIONS.FAILURE);
                            }

                        });

            },

            removeLogFromProject(project, log){

                var projectIndex = this.allProjects.indexOf(project);

                var project = this.allProjects[projectIndex];

                project.project_logs.splice(project.project_logs.indexOf(log), 1);

            },

            saveAssignedUsersForProject(assignedUsers){

                // //console.log({assignedUsers: assignedUsers, projectId: this.currentProject.id});

                this.serverRequest.post('/save-assigned-users', {assignedUsers: assignedUsers, projectId: this.currentProject.id})
                    .then((response) => {
                        
                        // //console.log(response);
                        
                        if(!response.error){
                            this.displayToast(response.message, TOAST_OPTIONS.SUCCESS);   
                        }
                        else{
                            this.displayToast(response.message, TOAST_OPTIONS.FAILURE);   
                        }
                        
                        // EventBus.$emit('modalNotification', response.message);
                    });

            },

            assignUsers(project){

                this.currentProject = project;

                // Display loading icon 
                this.displayLoadingIcon();

                var projectAssignedUsers = this.getProjectAssignedUsers(this.currentProject);

                // //console.log(projectAssignedUsers);

                EventBus.$emit('displayUsersListInModal', 
                                {   
                                    project_title: project.title, 
                                    allUsers: this.allUsers, 
                                    projectAssignedUsers: projectAssignedUsers,
                                });

            },

            getProjectAssignedUsers(project){

                var result = [];
               
                for(var i = 0; i < this.allProjectsAssignedUsers.length; i++){
                     
                    if(project.id === this.allProjectsAssignedUsers[i].project_id){
                        
                        result.push(this.allProjectsAssignedUsers[i].user);

                    }

                }
                
                return result;
            },

            addProject($event){

                $event.stopPropagation();

                EventBus.$emit("addProject");

            },
            
            viewLog(project, log){

                // Save log being currently viewed
                this.currentViewingLog = {'project': project, 
                                          'log': log,
                                          'userRole': this.userRole }

                // Send the log to modal
                EventBus.$emit('viewLog', this.currentViewingLog);
                
            },

            fetchProjectsAndLogs(){

                let currentComponent = this;

                this.serverRequest.post("/projects/" , null )
                .then((response) => {
              
                    //console.log(response);
                    
                    this.allProjects = response.allProjects;

                    // Only the admin can assign users to project
                    if(this.isAdmin())
                        this.allProjectsAssignedUsers = response.allProjectsAssignedUsers;

                });

            },

            getProjectDescription(project){
                return project.description;
            },

            logsPresent(logs){
                return ( (logs != undefined) && (logs.length > 0) );
            },

            logFor(project){

                this.currentProject = project;

                EventBus.$emit('displayModal', this.currentProject.title);

            },

            getLogMessageSummary(log){
                return log.log_message.substring(0,35) + '...';
            },

            getDatePublished(log){

                var date = log.created_at.split(" ")[0].replace('-','');

                return moment(date, "YYYYMMDD").fromNow();

            },
            
            saveLog(logMessage){
                
                this.serverRequest
                    .post("/log-project/", 
                        { 
                            log_message: logMessage, 
                            projectId: this.currentProject.id
                            
                        }).then((response) => {

                            if(!response.error){
                                
                                //console.log('response',response, this.currentProject.project_logs);

                                if(!response.error){
                                    
                                    this.currentProject.project_logs.push(response.project_log);

                                    this.displayToast(response.message, TOAST_OPTIONS.SUCCESS);
                                }
                                else{
                                    this.displayToast(response.message, TOAST_OPTIONS.FAILURE);    
                                }
                                
                            }
                            else{

                                this.displayToast(response.message, TOAST_OPTIONS.FAILURE);

                            }
                            
                        },
                        (error) => {

                            //console.log('error', error.message);

                        });
            },

            updateLog(logMessage){
                
                //console.log('log id',this.currentViewingLog.log.id);

                this.serverRequest
                    .post("/update-log/", 
                        { 
                            log_message: logMessage, 
                            logId:  this.currentViewingLog.log.id
                            
                        }).then((response) => {

                            //console.log('received id', response);
                            if(!response.error){
                                
                                // //console.log('response',response, this.currentProject.project_logs);

                                this.currentViewingLog.log.log_message = logMessage;

                                this.displayToast(response.message, TOAST_OPTIONS.SUCCESS);
                               
                                
                            }
                            else{

                                this.displayToast(response.message, TOAST_OPTIONS.FAILURE);

                            }
                            
                        },
                        (error) => {

                            //console.log('error', error.message);
                            this.displayToast(response.message, TOAST_OPTIONS.FAILURE);

                        });
            },

            saveProject(projectDetails){
                
                this.serverRequest
                        .post("/add-project", projectDetails)
                        .then((response) => {

                                // //console.log(response.project);

                                if(response.error !== true){
                                    
                                    this.allProjects.push(response.project);
                                    
                                    this.displayToast(response.message, TOAST_OPTIONS.SUCCESS);
                                }
                                else{

                                    this.displayToast(response.message, TOAST_OPTIONS.FAILURE);

                                }

                            },
                            (error) => {

                                //console.log('error', error.message);

                            });
            },

            saveAdminLogReview(adminResponse){
                
                this.serverRequest.post('save-admin-log-review', 
                                        { 
                                            logId: this.currentViewingLog.log.id, 
                                            response: adminResponse
                                        })
                                        .then((response) => {

                                            if(!response.error){
                                             
                                                this.displayToast(response.message, TOAST_OPTIONS.SUCCESS);
                                                this.currentViewingLog.log.status = adminResponse.toLowerCase();

                                            }
                                            else{
                                                this.displayToast(response.message, TOAST_OPTIONS.FAILURE);
                                            }

                                        });

            },

            displayToast(message, options){

                Vue.toasted.show(message, options);
            },

            registerGlobalEventListeners(){

                const projectVueInstance = this;

                EventBus.$on('saveLog', ($message) => {

                    projectVueInstance.saveLog($message);

                });

                EventBus.$on('updateLog', ($receivedObject) => {

                    projectVueInstance.updateLog($receivedObject.log.log_message);

                });

                EventBus.$on('saveProject', ($projectDetails) => {
                    
                    // //console.log($projectDetails.title);
                    
                    projectVueInstance.saveProject($projectDetails);

                });

                EventBus.$on('saveAssignedUsers', ($selectedUsersList) => {

                    //console.log('selected users list', $selectedUsersList);

                    projectVueInstance.saveAssignedUsersForProject($selectedUsersList);

                });

                EventBus.$on('logViewedByAdmin', ($response) => {

                    //console.log('admin response', $response);

                    projectVueInstance.saveAdminLogReview($response);

                });

            },

            deactivateListeners(){

                const eventList = ['logViewedByAdmin', 'saveAssignedUsers', 'saveProject',
                                     'updateLog', 'saveLog'];

                eventList.forEach((event) => { EventBus.$off(event); });
                
            },

            isAdmin(){
                return this.userRole == "admin";
            }

        },
        computed:{

        }

    }

</script>


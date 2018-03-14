<template>
    
<!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="false">
        <div class="modal-dialog w-50" style="max-width:none;" role="document">
                <div class="modal-content">
                  <div class="modal-header"> 
                      <!-- Title displayed here -->
                      <h5 class="modal-title" id="exampleModalLabel" v-if="displayTitle != null" v-text="displayTitle"></h5>
                      
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  
                  <div class="modal-body">

                          <!-- Log message input here -->
                          <textarea class="form-control" rows="6" cols="70" v-model="log_text" v-if="isAddLog()"></textarea>

                          <!-- Display following input fields if creating a new project -->
                          <form v-if="isAddNewProject()">
                            <div class="md-form">
                                <i class="fa fa-file prefix grey-text"></i>
                                <input type="text" id="projectTitle" class="form-control text-indent-2" v-model="newProjectDetails.title">
                                <label for="projectTitle">Project Title</label>
                            </div>

                            <div class="md-form">
                                <i class="fa fa-clipboard prefix grey-text"></i>

                                <textarea id="projectDescription" rows="5" cols="70" 
                                          class="form-control text-indent-2" v-model="newProjectDetails.description"></textarea>

                                <label for="projectDescription">Project Description</label>
                            </div>
                          </form>

                          <!-- Display the following body if viewing logs -->
                          <div class="card w-100" v-if="isViewLog()">
                              <div class="card-body" style="overflow-y:scroll;">
                                
                                <!-- If Admin displays the log message -->
                                  <p class="card-text" v-if="isAdmin()">
                                      {{ receivedParam.log.log_message }}
                                  </p>

                                  <!-- If User ddisplays the log message in a textarea and allow for editing -->
                                <textarea class="form-control" 
                                            rows="6" 
                                            cols="70" 
                                            v-model="receivedParam.log.log_message" 
                                            v-if="!isAdmin()"
                                            :disabled="logEditingDisabled"></textarea>

                                
                              </div>
                          </div>

                          <!-- Displays the following if assigning users for a project -->
                          <div class="card w-100" v-if="isAssignUsers()">
                              <div class="card-body" style="overflow-y:scroll;">
                                
                                  <p class="card-text">
                                      
                                      <!-- Loop through all users and place a checkbox in front of each user -->
                                        <div class="list-group">
                                            
                                            <a href="#" 
                                                class="list-group-item waves-effect"
                                                v-for="(user, index) in usersList" :key="index">

                                                <div class="form-check">
                                                
                                                    <input class="form-check-input filled-in" 
                                                            type="checkbox" 
                                                            value=""
                                                            v-once
                                                            :checked="userAssignedForProject(user)" 
                                                            @change="toggleUserSelectionState(user)">
                                                    
                                                    {{ user.name }} 
                                                
                                                    <span v-if="user.role == 'admin'" class="badge badge-pill red ml-5"> Admin</span>

                                                    <span v-if="user.role == 'user'" class="badge badge-pill ml-5 primary-color-dark"> {{ user.skill }}</span>

                                                </div>
                                                
                                            </a>

                                        </div>
                                  </p>
                                
                              </div>
                          </div>

                          <!-- Displays the form below if adding new users -->
                           <div v-if="isAddUser() || isEditUser()">

                            <form>
                                <p class="h4 text-center mb-4">User Details</p>

                                <!-- Input name -->
                                <div class="md-form">
                                    <i class="fa fa-id-card prefix grey-text"></i>
                                    <input type="text" id="fullName" class="form-control" v-model="receivedParam.name">
                                    <label for="fullName" :class="{ active: (receivedParam.name != null) }">Full name</label>
                                </div>

                                <!-- New password -->
                                <div class="md-form">
                                    <i class="fa fa-lock prefix grey-text"></i>
                                    
                                    <input type="password" id="password" class="form-control col-md-11"
                                             @keyup="confirmPasswordsMatch()" v-model="receivedParam.password">
                                    
                                    <i class="fa fa-eye grey-text" 
                                        style="position: absolute; top:.25rem; font-size: 1.75rem; right: 0;"
                                        @click="togglePasswordVisibility($event,'password')"></i>
                                    
                                    <label for="password">Password</label>
                                </div>

                                <!-- Confirm password -->
                                <div class="md-form">
                                    <i class="fa fa-lock prefix grey-text"></i>
                                    
                                    <input type="password" id="confirm-password" class="form-control" 
                                                @keyup="confirmPasswordsMatch()" v-model="receivedParam.confirm_password">
                                    
                                    <i class="fa fa-eye grey-text"
                                        style="position: absolute; top:.25rem; font-size: 1.75rem; right: 0;"
                                        @click="togglePasswordVisibility($event, 'confirm-password')"></i>
                                    
                                    <label for="confirm-password">Confirm Password</label>
                                </div>

                                <div class="md-form" v-if="!passwordsMatch">
                                    <span class="alert alert-sm alert-danger ml-5">Passwords do not match</span>
                                </div>

                                <!-- Displays the buttons to select user role -->
                                <div class="row pl-3 pt-2 mt-2" style="flex-direction: column;">
                                    <h4 class="pb-2 grey-text">
                                    <i class="fa fa-group prefix grey-text"></i>&nbsp;&nbsp;  Role 
                                    </h4>

                                    <div class="form-check mb-4 pl-5">
                                        <input class="" type="radio" name="roleRadio" 
                                                id="userRadio" value="option1" checked 
                                                @change="updateUserRole($event, 'user')">

                                        <label class="form-check-label" style="position:inherit" for="userRadio">
                                            User
                                        </label>
                                    </div>

                                    <div class="form-check mb-4 pl-5">
                                        <input class="" type="radio" name="roleRadio" 
                                        id="adminRadio" value="option2"
                                        @change="updateUserRole($event, 'admin')">

                                        <label class="form-check-label" style="position:inherit" for="adminRadio">
                                            Admin
                                        </label>
                                    </div>
                                </div>

                                <!-- User's Skill -->
                                <div class="md-form">
                                    <i class="fa fa-lock prefix grey-text"></i>
                                    <input type="text" id="skill" class="form-control" v-model="receivedParam.skill">
                                    <label :class="{ active : (receivedParam.skill != null)}" for="password">Skill</label>
                                </div>

                                <!-- Email -->
                                <div class="md-form">
                                    <i class="fa fa-envelope prefix grey-text"></i>
                                    <input type="email" id="email" class="form-control" v-model="receivedParam.email">
                                    <label :class="{ active : (receivedParam.email != null)}" for="email">Email</label>
                                </div>

                            </form>

                        </div>
                  </div>

                  <div class="modal-footer">
                        <!-- Modal Notification messages are displayed here -->
                            
                        <!-- <span v-if="displayNotification" :class="{'badge badge-danger': modalNotification.error, 'badge badge-success': !modalNotification.error }">
                            {{ modalNotification.message }}
                        </span> -->

                        <!-- Displays button if updating user's record -->
                        <button class="btn btn-success" v-if="isEditUser()" type="button" @click="EventBus.$emit('updateUserRecord', receivedParam)">
                            <i class="fa fa-edit prefix"></i>&nbsp;  Update Profile
                        </button>

                        <!-- Displays button if creating a new user -->
                        <button class="btn btn-success" v-if="isAddUser()" type="button" @click="EventBus.$emit('registerUser', receivedParam)">
                            <i class="fa fa-plus prefix"></i>&nbsp;   Register User
                        </button>


                        <button type="button" class="btn btn-success" v-if="isAddLog()" @click="saveLog()">
                            <i class="fa fa-check mr-2" aria-hidden="true"></i>
                            Save Log
                        </button>

                        <button type="button" class="btn btn-success" v-if="isAddNewProject()" @click="saveProject()">
                            <i class="fa fa-check mr-2" aria-hidden="true"></i>
                            Save
                        </button>
                        
                        <button type="button" class="btn btn-success" v-if="isViewLog() && isAdmin()" @click="markLogAs('Seen')">
                            <i class="fa fa-check mr-2" aria-hidden="true"></i>
                            Seen
                        </button>

                        <button type="button" class="btn btn-warning" v-if="isViewLog() && isAdmin()" @click="markLogAs('Review')">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                            Review
                        </button>

                        <button type="button" class="btn btn-success" v-if="isAssignUsers()" @click="saveAssignedUsers()">
                            <i class="fa fa-check mr-2" aria-hidden="true"></i>
                            Save                            
                        </button>

                        <!-- Displays button if user is viewing log provides button to edit-->
                        <button type="button" class="btn btn-primary" v-if="isViewLog() && !isAdmin()" @click="logEditingDisabled = !logEditingDisabled">
                            <i class="fa fa-pencil mr-2" aria-hidden="true"></i>
                            Edit                          
                        </button>

                        <!-- Displays button if user is editing log provides button to update log-->
                        <button type="button" class="btn btn-success" v-if="isViewLog() && !logEditingDisabled && !isAdmin()" @click="EventBus.$emit('updateLog', receivedParam)">
                            <i class="fa fa-check mr-2" aria-hidden="true"></i>
                            Save                            
                        </button>

                         <!-- Resets the params passed into the modal component -->
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="resetParams()">Close</button>
                          
                  </div>
              </div>
        </div>
    </div>

</template>

<script>

import { EventBus } from "../event-bus.js";

    export default {

      mounted(){
        
            this.registerGlobalEventListeners(); 

      },
      data(){
          return {
              displayTitle: null,
              log_text: '',
              op_type:null,
              allOpTypes:{
                            addLog: "addLog",
                            addProject: "addProject",
                            viewLog: "viewLog",
                            assignUsers: "assignUsers",
                            addUser: "addUser",
                            editUser: "editUser",
                         },
              newProjectDetails:{
                                    title: '', 
                                    description: ''
                                },
              usersList:null,  
              selectedUsersList: [],
              displayNotification: false,
              modalNotification: null,
              projectAssignedUsers: null,
              receivedParam: {},
              EventBus:EventBus,
              passwordsMatch:true,
              logEditingDisabled: true,
          }
      },
      methods:{
        
        confirmPasswordsMatch(){
            this.passwordsMatch = this.receivedParam.password == this.receivedParam.confirm_password;
        },

        togglePasswordVisibility($event, fieldId){

            var passwordField = document.getElementById(fieldId);

            const toggledType = passwordField.getAttribute('type') == "password" ? "text" : "password";        

            passwordField.setAttribute('type', toggledType);

            // Toggle the eye icon
            $event.target.classList = $event.target.classList.contains('fa-eye')  ? 
                                        'fa fa-eye-slash grey-text' :  'fa fa-eye grey-text';

        },

        saveLog(){
          
          EventBus.$emit('saveLog', this.log_text);

          this.log_text = '';

        },

        updateUserRole($event, roleType){

            //console.log($event.target.checked, roleType);

            if($event.target.checked){

                this.receivedParam.role = roleType; 

            }
        },

        saveProject(){

          EventBus.$emit('saveProject', this.newProjectDetails);

        },

        userAssignedForProject(user){
            
            var result = '';

            if(this.projectAssignedUsers !== undefined){

                for(var i = 0; i < this.projectAssignedUsers.length; i++){

                    var assignedUser = this.projectAssignedUsers[i];
                
                    // //console.log(assignedUser.id+ "---" +assignedUser.name);

                    if(user.id === assignedUser.id){
                        
                        this.projectAssignedUsers.splice(i, 1);

                        this.selectedUsersList.push(user);

                        result = "checked";
                        
                        return result;

                    }

                }

                return result;

            }else{

                return result;

            }

            
            
        },

        toggleUserSelectionState(user){

            // //console.log('sdfsf', this.selectedUsersList, this.selectedUsersList.indexOf(user));

            if(this.userSelected(user)){

                this.removeUserFromSelection(user);

            }
            else{

                this.addUserToSelection(user);

            }

        },

        userSelected(user){

            // returns true if user's index is !== -1
            return !(this.selectedUsersList.indexOf(user) == -1);
        },

        addUserToSelection(user){

            
            if( !this.userSelected(user) )
                this.selectedUsersList.push(user);
            
        },

        removeUserFromSelection(user){
            this.selectedUsersList.splice(this.selectedUsersList.indexOf(user),1);
        },

        saveAssignedUsers(){
        
            EventBus.$emit("saveAssignedUsers", this.selectedUsersList);

        },

        showModal(){

          $("#exampleModal").modal('show');

        },
        
        hideModal(){
            $("#exampleModal").modal('hide');
        },
        
        resetParams(){
          this.log_text = '';
          this.op_type = null;
          this.displayTitle = null;
          this.newProjectDetails = { title: '', description: ''};
          this.usersList = null;
          this.selectedUsersList = [];
          this.receivedParam = null;
        },

        markLogAs(adminResponse){

          EventBus.$emit('logViewedByAdmin', adminResponse);

        },

        isAdmin(){
            return this.receivedParam.userRole.toLowerCase() == 'admin';
        },

        registerGlobalEventListeners(){
            
          const modalInstance = this;

          // Displays modal to log a new project
          EventBus.$on('displayModal', ($receivedData) => {
              
              modalInstance.displayTitle = $receivedData;
              
              modalInstance.op_type = modalInstance.allOpTypes.addLog;

              modalInstance.showModal();

          });

          // Displays modal to create new project. Specify ( title and description )
          EventBus.$on('addProject', () => {

              modalInstance.displayTitle = "Add project";
              
              modalInstance.op_type = this.allOpTypes.addProject;

              modalInstance.showModal();

          });

          // Displays single log for viewing by the admin
          EventBus.$on('viewLog', (currentViewingLog) => {

              modalInstance.logEditingDisabled = true;

              modalInstance.receivedParam = currentViewingLog;

              modalInstance.displayTitle = currentViewingLog.project.title;

              modalInstance.op_type = modalInstance.allOpTypes.viewLog;

              modalInstance.showModal();

          });

          //  Displays users list in modal
          EventBus.$on('displayUsersListInModal', ($parameters) => {

              modalInstance.displayTitle = 'Assign Users for project (' + $parameters.project_title + ')';

              modalInstance.op_type = modalInstance.allOpTypes.assignUsers;

              modalInstance.usersList = $parameters.allUsers;

              modalInstance.projectAssignedUsers = $parameters.projectAssignedUsers;
              
            //   //console.log(modalInstance.projectAssignedUsers);

              modalInstance.showModal();

          });

          EventBus.$on("displayAddUserForm",($parameter) => {

              modalInstance.displayTitle = 'Add new user';

              modalInstance.op_type = modalInstance.allOpTypes.addUser;

              modalInstance.receivedParam = $parameter;

              modalInstance.showModal();
          });

          EventBus.$on('editUserRecord', (user) => {

              modalInstance.displayTitle = 'Edit user\'s record';

              modalInstance.op_type = modalInstance.allOpTypes.editUser;

              modalInstance.receivedParam = user;

              modalInstance.showModal();

          });

        },
        
        deactivateListeners(){

            const eventList = ['displayModal', 'addProject', 'viewLog', 'displayUsersListInModal',
                                 'displayAddUserForm', 'editUserRecord'];

            eventList.forEach((event) => { EventBus.$off(event); });
            
        },

        isAddNewProject(){
          return this.op_type == this.allOpTypes.addProject;
        },

        isAddLog(){
            return this.op_type == this.allOpTypes.addLog;
        },

        isViewLog(){
          return this.op_type == this.allOpTypes.viewLog;
        },

        isAssignUsers(){
            return this.op_type == this.allOpTypes.assignUsers;
        },

        isAddUser(){
            return this.op_type == this.allOpTypes.addUser;
        },

        isEditUser(){
            return this.op_type == this.allOpTypes.editUser;

        },

      }

    }

</script>

<style>
    .text-indent-2{
        text-indent: 2px;
    }

    .modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, .5);
    display: table;
    transition: opacity .3s ease;
    }

    .modal-wrapper {
    display: table-cell;
    vertical-align: middle;
    }

    .modal-container {
    width: 300px;
    margin: 0px auto;
    padding: 20px 30px;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
    transition: all .3s ease;
    font-family: Helvetica, Arial, sans-serif;
    }

    .modal-header h3 {
    margin-top: 0;
    color: #42b983;
    }

    .modal-body {
    margin: 20px 0;
    }

    .modal-default-button {
    float: right;
    }

    /*
    * The following styles are auto-applied to elements with
    * transition="modal" when their visibility is toggled
    * by Vue.js.
    *
    * You can easily play with the modal transition by editing
    * these styles.
    */

    .modal-enter {
    opacity: 0;
    }

    .modal-leave-active {
    opacity: 0;
    }

    .modal-enter .modal-container,
    .modal-leave-active .modal-container {
    -webkit-transform: scale(1.1);
    transform: scale(1.1);
    }

</style>
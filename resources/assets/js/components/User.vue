<template>

    <div class="pt-3">
        <!-- Display add user button if admin -->
        <div class="pull-right" v-if="isAdmin()">
            <button class="btn btn-info" @click="displayAddUserForm()">
                <i class="fa fa-plus" aria-hidden="true"></i> &nbsp;
                Add User
            </button>
        </div>

        <table class="table">

            <!--Table head-->
            <thead class="mdb-color darken-3">
                <tr class="text-white">
                    <th>#</th>
                    <th>Name</th>
                    <th>Skill</th>
                    <th>Role</th>
                    <th rowspan="2">Action</th>
                </tr>
            </thead>
            <!--Table head-->

            <!--Table body-->
            <tbody>
                <tr v-for="(user, index) in allUsers" :key="index">
                    <th scope="row">{{ index + 1 }}</th>
                    <td> {{ user.name }} </td>
                    <td> {{ user.skill }} </td>
                    <td> {{ user.role }} </td>
                    <!-- Here -->
                    <td class="p-0"> 
                        <div class="row">
                            <!-- Edit user -->
                            <div class="text-center">
                                <button class="btn btn-default" type="button" @click="editUser(user)">
                                <i class="fa fa-edit prefix  text-white"></i>&nbsp;  Edit
                                </button>
                            </div>

                            <!-- Delete user -->
                            <div class="text-center">
                                <button class="btn btn-danger" type="button" @click="deleteUser(user)">
                                <i class="fa fa-trash-o" aria-hidden="true"></i>&nbsp; Delete
                                </button>
                            </div>
                        </div>
                    </td>
            
                </tr>
            </tbody>
            <!--Table body-->

        </table>

    </div>

</template>

<script>

    import {EventBus} from '../event-bus';
    
    var Form = require('../form');

    var User = require('../user');

    export default {

        mounted(){

            //console.log('user component mounted');

            this.fetchAllUsersRecord();

            if(!this.listenersRegistered){
                this.registerGlobalEventListeners(); 
                this.listenersRegistered = true;
            }

        },

        destroyed(){
            this.deactivateListeners();
        },

        data: function(){
            return {

                serverRequest: new Form(null),
                allUsers: null,
                currentEditingUserRecord: null,
                listenersRegistered: false,
            }
        },

        methods:{

            fetchAllUsersRecord(){

                this.serverRequest.post('users', null)
                    .then((response) => {

                        this.allUsers = response;

                    });

            },

            displayAddUserForm(){

                EventBus.$emit("displayAddUserForm", new User({role:'user'}));

            },

            editUser: function(user){

                this.currentEditingUserRecord = user;

                EventBus.$emit('editUserRecord', user );

            },

            isAdmin(){

                return JSON.parse(window.localStorage.getItem('user')).role.trim().toLowerCase() == 'admin';

            },
            
            deleteUser: function(user){

                var response = confirm("Are you sure you want to delete user " + user.name );

                if(response == true){

                    // Delete user from database
                    this.serverRequest.post('delete-user', user)
                        .then((response) => {
                            

                            if(!response.error){

                                this.displayToast(response.message, TOAST_OPTIONS.SUCCESS);

                                // Remove from the list of users in the component
                                this.allUsers.splice(this.allUsers.indexOf(user), 1);

                            }
                            else{

                                // Failed to delete user
                                this.displayToast(response.message, TOAST_OPTIONS.FAILURE);

                            }
                                
                        });

                }

            },

            updateUserRecord: function(user){

                this.serverRequest.post('/update-user-record', user)
                    .then((response) => {

                         if(!response.error){

                            this.displayToast(response.message, TOAST_OPTIONS.SUCCESS);

                            this.updateUserRecordInListOfUsers(response.user);
                         }
                         else{

                            this.displayToast(response.message, TOAST_OPTIONS.FAILURE);                             

                         }

                        
                    })

            },

            updateUserRecordInListOfUsers(user){

                // removes the user from the allUsers variable in this component
                this.allUsers.splice(this.allUsers.indexOf(this.currentEditingUserRecord), 1);

                this.allUsers.push(user);

            },

            registerUser(user){
                
                //console.log('registering user');

                this.serverRequest.post('/register-user', user)
                        .then((response) => {
                            
                            if(!response.error){

                                this.displayToast(response.message, TOAST_OPTIONS.SUCCESS);

                                this.allUsers.push(response.user);

                            }
                            else{

                                this.displayToast(response.message, TOAST_OPTIONS.FAILURE);

                            }

                        },
                        (err) => {

                            const allErrors = Object.keys(err.errors);
                            let errorResponse = "";

                            allErrors.forEach((error) => {
                                errorResponse += err.errors[error][0] + '</br>';
                            });

                            this.displayToast(errorResponse, TOAST_OPTIONS.FAILURE);

                        });
            },

            displayToast(message, options){

                Vue.toasted.show(message, options);
                
            },

            registerGlobalEventListeners(){

                EventBus.$on('registerUser', (user) => {
                    
                    this.registerUser(user);

                });

                EventBus.$on('updateUserRecord', (user) => {
                    
                    this.updateUserRecord(user);

                });

            },


            deactivateListeners(){

                const eventList = ['registerUser', 'updateUserRecord'];

                eventList.forEach((event) => { EventBus.$off(event); });
                
            },

        }

    }

</script>


<template>
    <div class="pt-5">

        <form method="POST" v-if="userRecord != null" id="profile-form"  enctype="multipart/form-data">
            <p class="h4 text-center mb-4">My Profile</p>

            <!-- Input name -->
            <div class="md-form">
                <i class="fa fa-id-card prefix grey-text"></i>
                <input type="text" id="fullName" class="form-control" v-model="userRecord.name">
                <label for="fullName" :class="{ active: (userRecord.name != null) }">Your full name</label>
            </div>

            <!-- Display current profile pic -->
            <!-- <div class="md-form">
                <img :src="userRecord.profile_pic" alt="placeholder" class="img-thumbnail h-50">
            </div> -->

            <!-- Profile Pic upload -->
            <div class="md-form">
                    <!-- <div class="btn btn-primary btn-sm float-left">
                        <span>Select picture</span>
                        <input name="profile-picture" id="profile-picture" type="file">
                    </div>
                    <div class="file-path-wrapper">
                        <button type="button" class="btn btn-primary" @click="updateProfilePicture()"><i class="fa fa-upload"></i>&nbsp; Upload</button>
                    </div> -->
                    <vue-uploader 
                        :dictDefaultMessage="fileUploaderConfigs.dictDefaultMessage" 
                        :url="fileUploaderConfigs.updateUrl"
                        :uploadMultiple="fileUploaderConfigs.uploadMultiple"
                        :maxFilesize="fileUploaderConfigs.maxFilesize"
                        :acceptedFiles="fileUploaderConfigs.acceptedFiles"
                        :addRemoveLinks="fileUploaderConfigs.addRemoveLinks"
                        >
                    </vue-uploader>

            </div>

            <!-- New password -->
            <div class="md-form">
               <i class="fa fa-lock prefix grey-text"></i>
               <input type="text" id="password" class="form-control" 
                        @keyup="confirmPasswordsMatch()" v-model="userRecord.password">
               <label for="password">Password</label>
            </div>

            <!-- Confirm password -->
            <div class="md-form">
               <i class="fa fa-lock prefix grey-text"></i>
               <input type="text" id="confirm-password" class="form-control" 
                        @keyup="confirmPasswordsMatch()" v-model="userRecord.confirm_password">
               <label for="confirm-
               password">Confirm Password</label>
            </div>

            <div class="md-form" v-if="!passwordsMatch">
                <span class="alert alert-danger">Passwords do not match</span>
            </div>

            <!-- Email -->
            <div class="md-form">
               <i class="fa fa-envelope prefix grey-text"></i>
               <input type="text" id="email" class="form-control" v-model="userRecord.email">
               <label :class="{ active : (userRecord.email != null)}" for="email">Email</label>
            </div>

            <!-- Update button -->
            <div class="text-center mt-4">
                <button class="btn btn-default" type="button" @click="updateUsersProfile()">
                  <i class="fa fa-edit prefix grey-text"></i>&nbsp;  Update Profile
                </button>
            </div>

        </form>

    </div>
</template>

<script> 

    import VueUploader from './Upload.vue';
    import { EventBus } from '../event-bus';

    var Form  = require('../form.js');
    var User = require('../user.js');

    export default{

        mounted(){

            // Load the users full details into form
            this.InitializeFormFields();

            this.registerGlobalEventListeners();   

        },
        destroyed(){
            this.deactivateListeners();
        },
        components:{ VueUploader },
        data: function(){

            return  {
                
                userRecord: null,
                serverRequest: new Form(null),
                passwordsMatch: true,
                fileUploaderConfigs:{
                    dictDefaultMessage: "Click here to upload profile picture",
                    updateUrl: "/update-profile",
                    uploadMultiple: false,
                    maxFilesize: 1,
                    acceptedFiles: "image/*",
                    addRemoveLinks: true,
                },

            }

        },
        methods:{

            InitializeFormFields(){

                var user = window.localStorage.getItem('user');

                // The serverRequest is used inside the user class to update(send to server)
                // the user info.

                this.userRecord = JSON.parse(user);

            },

            onVueUploaderResponse($event){

                //console.log($event);

                if( ($event.profile_pic_path != undefined) || ($event.profile_pic_path != null) ){
                
                    this.displayToast("Profile picture updated", TOAST_OPTIONS.SUCCESS);

                    //  Update the pic path in the user object
                    this.userRecord.profile_pic = $event.profile_pic_path;

                    this.updateUserRecordInLocalStorage();

                }
                else{
                    
                    this.displayToast("Update failed", TOAST_OPTIONS.FAILURE);

                }
                

            },

            updateUserRecordInLocalStorage(){
                
                window.localStorage.removeItem('user');

                window.localStorage.setItem('user', JSON.stringify(this.userRecord));
            },

            updateUsersProfile(){

                this.serverRequest.post('update-profile', this.userRecord)
                    .then((response) => {
                    
                            if(!response.error){    
                                

                                this.displayToast(response.message, TOAST_OPTIONS.SUCCESS);   
                                
                                var user = new User(response.user);
                                
                                this.userRecord = user;
                                
                                window.localStorage.setItem('user', JSON.stringify(user));
                            }
                            else{
                                this.displayToast(response.message, TOAST_OPTIONS.FAILURE);   
                            }
                        
                        });

            },

            updateProfilePicture(){

                var form = document.getElementById("profile-form");

                var formData = new FormData(form);

                //formData.append("profile-picture", form);

                //console.log(formData);

                this.serverRequest.post('/update-profile', formData)
                    .then((response) => {

                        //console.log(response);

                    });

            },
            
            confirmPasswordsMatch(){

                if(this.userRecord.confirm_password != '')
                 return this.passwordsMatch = (this.userRecord.password == this.userRecord.confirm_password);

                return this.passwordsMatch;
            },

            displayToast(message, options){

                Vue.toasted.show(message, options);
            },

            registerGlobalEventListeners(){

                EventBus.$on('vueUploaderResponseEvent', ($event) => {

                    this.userRecord.profile_pic = $event.profile_pic_path;

                });

            },

            deactivateListeners(){

                const eventList = ['vueUploaderResponseEvent'];

                eventList.forEach((event) => { EventBus.$off(event); });
                
            },

        }

    }

</script>
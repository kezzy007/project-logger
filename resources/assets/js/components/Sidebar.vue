<template>

    <div class="col-lg-3 col-md-4 p-0 pt-5 h-100 fixed stylish-color-dark" style="overflow-x:none">
        <!-- User's profile pic -->
        <div class="media pl-3 pr-3">
            <img class="d-flex ml-1 avatar border-0" :src="userRecord.profile_pic" alt="user picture">
            <div class="media-body text-white ml-3 pr-3">
                <h5 class="mt-4 font-weight-bold">{{ userRecord.name }}</h5>
                <h6 class="mt-2 font-weight-light">{{ userRecord.skill }}</h6>
            </div>
        </div>

        <div class="card card-cascade narrower mb-5 pt-5 stylish-color-dark border-0">

            <!--Card image-->
            <div style="border-bottom:1px solid grey" 
                class="view gradient-card-header narrower py-2 mx-4 mb-3 d-flex justify-content-center align-items-center">

                <h4><a href="" class="text-white font-weight-normal">Menu</a></h4>


            </div>
            <!--/Card image-->

            <div class="px-4">

                <div class="table-wrapper">
                    <!--Table-->
                    <table class="table table-hover mb-0 text-white">
                        <!--Table body-->
                        <tbody>
                            <tr>
                                <router-link to="projects" class="text-white">
                                
                                 <td class="border-0 border-dark w-300">
                                     <th scope="row" class="border-0 border-dark ">
                                        <i class="fa fa-clipboard mr-5" aria-hidden="true"></i>
                                        Projects
                                     </th>
                                 </td>
                                </router-link>
                            </tr>
                            <tr>
                                <router-link to="profile" class="text-white">
                                
                                 <td class="border-0 border-dark w-300">
                                     <th scope="row" class="border-0 border-dark ">
                                        <i class="fa fa-id-card mr-5" aria-hidden="true"></i>
                                        Profile
                                     </th>
                                 </td>
                                </router-link>
                            </tr>
                            <tr v-if="isAdmin">
                                <router-link to="users" class="text-white">
                                
                                 <td class="border-0 border-dark w-300">
                                     <th scope="row" class="border-0 border-dark ">
                                        <i class="fa fa-users mr-5" aria-hidden="true"></i>
                                        Users
                                     </th>
                                 </td>
                                </router-link>
                            </tr>
                        </tbody>
                        <!--Table body-->
                    </table>
                    <!--Table-->
                </div>


            </div>
        </div>
    </div>
</template>

<script>

    import Projects from './Projects.vue';
    import Profile from './Profile.vue'; 
    import {EventBus} from '../event-bus';

    var form = require("../form.js");
    var User = require('../user.js');

    export default {
        mounted() {
            //console.log('Component mounted.')

            //Get the user id from the dom and remove
            this.getAndRegisterUserInfoFromDom();

            this.registerGlobalEventListeners();
        },

        components:{

            Projects,
            Profile

        },
        
        data:function(){
            return {
                isAdmin:false,
                userRecord:null,
            }
        },
        methods:{
            
            getAndRegisterUserInfoFromDom(){

                var elem = document.getElementById('userdetails');

                let userInfoAll = elem.textContent;

                let userInfoArray = userInfoAll.split('||');

                let userInfo = new User({  
                                            name: userInfoArray[0],
                                            skill: userInfoArray[1],
                                            email: userInfoArray[2], 
                                            profile_pic: userInfoArray[3],
                                            role: userInfoArray[4]
                                        });

                this.userRecord = userInfo;
                //console.log(userInfo.role);

                this.isAdmin = this.confirmAdmin(userInfo.role);
                
                this.storeUserInfo();

                //window.localStorage.getItem('user')
                elem.parentNode.removeChild(elem);

            },

            confirmAdmin(role){

                return role.trim().toLowerCase() == 'admin';

            },

            storeUserInfo(){

                window.localStorage.removeItem('user');

                window.localStorage.setItem('user', JSON.stringify(this.userRecord));

            },
            
            registerGlobalEventListeners(){

                EventBus.$on('vueUploaderResponseEvent', ($event) => {

                    this.userRecord.profile_pic = $event.profile_pic_path;

                });

            }

        }
    }
</script>

<style>
    .w-300{
        width:300px;
    }
</style>
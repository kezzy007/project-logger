
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import VueRouter from "vue-router";
import VueToasted from 'vue-toasted';

import Project from './components/Projects.vue';
import Profile from './components/Profile.vue';
import User from './components/User.vue';

require('./bootstrap.js');

window.Vue = require('vue');

 /**
  *     Application routes 
  */  
const routes = [
    {path:"/projects", component:Project},
    {path:"/profile", component:Profile},
    {path:"/users", component:User}
];

var router = new VueRouter({
    routes
});

/**
 *  Register route with vue
 */
Vue.use(VueRouter);

// Use toast
Vue.use(VueToasted, { iconPack: 'material' });

window.TOAST_OPTIONS = {
    SUCCESS:{
        text: 'CLOSE',
        duration: 5000,
        type: 'success',
    },
    FAILURE:{
        text: 'CLOSE',
        duration: 5000,
        type: 'error',
    }
};

/**
 *  Components
 */

Vue.component('Sidebar', require('./components/Sidebar.vue'));
Vue.component('Mainview', require('./components/Mainview.vue'));
Vue.component('modal', require('./components/Modal.vue'));

/**
 *  Vue Initialization
 */

const app = new Vue({
    el: '#app',
    mounted(){

        
        
    },
    router,
    template:`  
        <div class="row pt-5" style="">
            <sidebar></sidebar>
            <mainview></mainview>
            <modal>
                <!--
                you can use custom content here to overwrite
                default content
                -->
                <h3 slot="header">custom header</h3>
            </modal>
        </div>`,
    data:{
    },
    methods:{
        
    }
});

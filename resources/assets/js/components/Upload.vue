<template>
    <vue-dropzone 
        ref="myVueDropzone" 
        id="dropzone" 
        :options="dropzoneOptions"
        @vdropzone-success="onUploadSuccessful($event)">

    </vue-dropzone>
    
</template>

<script>
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.css'
import {EventBus} from '../event-bus';

export default{

    mounted(){
        console.log('Upload component mounted');

        this.registerEventListeners();
    },

    props:['dictDefaultMessage', 'url', 'uploadMultiple', 'maxFilesize', 'acceptedFiles'],

    components: {
        vueDropzone: vue2Dropzone
    },
    data: function () {
        return {
            dropzoneOptions: {
                url: this.url,
                thumbnailWidth: 150,
                headers: { "X-CSRF_TOKEN": $("meta[name='csrf-token']").attr("content") },
                dictDefaultMessage: this.dictDefaultMessage,
                uploadMultiple: this.uploadMultiple,
                maxFilesize: this.maxFilesize,
                acceptedFiles: this.acceptedFiles
            }
        }
    },
    methods:{

        onUploadSuccessful($event){

            console.log($event.old_picture_path);

            const serverResponse = JSON.parse($event.xhr.responseText);
            
            console.log(serverResponse);

            EventBus.$emit('vueUploaderResponseEvent', serverResponse );

        },

        registerEventListeners(){

            // this.$on('vdropzone-success', ($event) => {
            //     console.log($event);
            // });
        }

    },

}

</script>
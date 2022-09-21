<template>
    <div class="input-group">
        <input type="text" v-model="VideoName" class="form-control ij-input-upload-file" readonly v-on:click="$refs.file.click()">
        <input type="file" v-on:change="onImageChange" ref="file" class="form-control" style="display: none;">
        <div class="input-group-prepend ij-icon-attach-file">
            <i class="fa fa-paperclip"></i>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'IjcoreUploadVideo',
        components: {
        },
        data () {
            return {
                VideoName: ''
            }
        },
        created(){
            this.VideoName = this.value.VideoName;
        },
        props:{
            value:{
                default: ""
            },
        },
        methods: {
            onImageChange(e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                let file = files[0];
                this.value.VideoUpload = file;
                this.value.VideoName = file.name;
                this.value.DateModified = __.convertDateTimeToString(new Date());
                this.value.VideoType = file.name.split('.').pop();
                this.value.VideoSize = file.size;
                this.value.changeVideo = 1;
                if(!this.value.Description){
                    this.value.Description = file.name;
                }
                this.VideoName = this.value.VideoName;
                this.changeData  = 1;
            },
        },
        watch: {
        }
    }
</script>
<style>
    .ij-icon-attach-file{
        display: flex;
        width: 20px;
        margin-top: auto;
        margin-bottom: auto;
    }
    .ij-input-upload-file{
        background: none !important;
    }
</style>
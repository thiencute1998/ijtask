<template>
    <b-input-group>
        <input type="text" v-model="value.Description" class="form-control ij-input-upload-file" style="padding-right: 30px;">
        <input type="hidden" v-model="VideoName" class="form-control ij-input-upload-file" readonly>
        <input type="file" v-on:change="onVideoChange" ref="file" class="form-control" style="display: none;">
        <div class="input-group-prepend ij-icon-attach-file" v-on:click="$refs.file.click()" style="z-index:999;position: absolute;right: 0px;padding-top: 9px;width: 30px;padding-left: 10px;cursor: pointer;height: 100%;" title="Chọn tệp">
            <i class="fa fa-paperclip"></i>
        </div>
    </b-input-group>
</template>

<script>
    export default {
        name: 'IjcoreUploadVideoDescription',
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
            onVideoChange(e) {
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
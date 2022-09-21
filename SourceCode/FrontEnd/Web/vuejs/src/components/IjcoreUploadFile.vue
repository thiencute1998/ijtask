<template>
    <div class="input-group">
        <input type="text" v-model="FileName" class="form-control ij-input-upload-file" readonly v-on:click="$refs.file.click()">
        <input type="file" v-on:change="onImageChange" ref="file" class="form-control" style="display: none;">
        <div class="input-group-prepend ij-icon-attach-file">
            <i class="fa fa-paperclip"></i>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'IjcoreUploadFile',
        components: {
        },
        data () {
            return {
                FileName: ''
            }
        },
        created(){
            this.FileName = this.value.FileName;
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
                this.value.FileUpload = file;
                this.value.FileName = file.name;
                this.value.DateModified = __.convertDateTimeToString(new Date());
                this.value.FileType = file.name.split('.').pop();
                this.value.FileSize = file.size;
                this.value.changeFile = 1;
                if(!this.value.Description){
                    this.value.Description = file.name;
                }
                this.FileName = this.value.FileName;
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
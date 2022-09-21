<template>
    <b-input-group>
        <input type="text" v-model="value.Description" class="form-control ij-input-upload-file" style="padding-right: 30px;">
        <input type="hidden" v-model="FileName" class="form-control ij-input-upload-file" readonly>
        <input type="file" v-on:change="onFileChange" ref="file" class="form-control" style="display: none;">
        <div class="input-group-prepend ij-icon-attach-file" v-on:click="$refs.file.click()" style="z-index:999;position: absolute;right: 0px;padding-top: 9px;width: 30px;padding-left: 10px;cursor: pointer;height: 100%;" title="Chọn tệp">
            <i class="fa fa-paperclip"></i>
        </div>
    </b-input-group>
</template>

<script>
    export default {
        name: 'IjcoreUploadFileDescription',
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
            onFileChange(e) {
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
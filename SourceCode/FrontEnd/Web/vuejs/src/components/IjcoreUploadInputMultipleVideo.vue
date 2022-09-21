<template>
    <div>
        <button class="btn float-left mr-2 btn-primary btn-md" v-on:click="$refs.file.click()">{{title}}</button>
        <input type="file" v-on:change="onImageChange" ref="file" class="form-control" style="display: none;" multiple>
    </div>
</template>

<script>
    export default {
        name: 'IjcoreUploadInputMultipleVideo',
        components: {
        },
        data () {
            return {
            }
        },
        props:{
            value:{
                default: ""
            },
            title:{
                type: String,
                default: 'Tải lên và nhập'
            }
        },
        methods: {
            clickUpload(){

            },
            onImageChange(e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createImage(files[0]);
                this.$emit('changed', files);
            },
            createImage(file) {
                let reader = new FileReader();
                let vm = this;
                reader.onload = (e) => {
                    vm.image = e.target.result;
                };
                reader.readAsDataURL(file);
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
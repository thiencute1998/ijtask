<template>
    <a>
        <button class="btn float-left mr-2 btn-primary btn-md" v-on:click="$refs.file.click()" v-if="!isIcon">{{title}}</button>
        <i class="cui-cloud-upload icons ij-icon" style="cursor: pointer" aria-hidden="true" title="Tải lên" v-on:click="$refs.file.click()" v-if="isIcon" ></i>
        <input type="file" v-on:change="onImageChange" ref="file" class="form-control" style="display: none;" multiple>
    </a>
</template>

<script>
    export default {
        name: 'IjcoreUploadMultipleFile',

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
                default: 'Tải lên'
            },
            isIcon:{
                type: Boolean,
                default: false
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

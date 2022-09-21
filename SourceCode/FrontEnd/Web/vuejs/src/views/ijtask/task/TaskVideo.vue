<template>
    <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
        <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
        <b-modal ref="modal" scrollable id="modal-form-input-task-video" size="xl">
            <template slot="modal-title">
                {{this.title}}: {{Task.TaskName}}
            </template>
            <TaskVideoContent v-model="value" v-if="!isForm" :Task="Task">
            </TaskVideoContent>
            <TaskVideoContent v-model="value" :isForm="true" v-if="isForm" :Task="Task">
            </TaskVideoContent>
            <template v-slot:modal-footer>
                <div class="w-100 left">
                    <!--<b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()" v-if="!isForm"-->
                    <!--&gt;-->
                    <!--Sửa-->
                    <!--</b-button>-->
                    <IjcoreUploadMultipleVideo v-on:changed="changeVideo" v-if="!isForm"></IjcoreUploadMultipleVideo>
                    <!--<IjcoreUploadInputMultipleVideo v-on:changed="changeVideoAndInput" v-if="!isForm"></IjcoreUploadInputMultipleVideo>-->
                    <b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdate()" v-if="isForm">
                        Lưu
                    </b-button>
                    <b-button variant="primary" size="md" class="float-left mr-2" @click="" v-if="isForm">
                        Hủy
                    </b-button>
                    <b-button
                            variant="primary"
                            size="md"
                            class="float-left mr-2"
                            @click="onHideModal()"
                    >
                        Đóng
                    </b-button>
                </div>
            </template>

        </b-modal>
        <!--<b-modal ref="modal-input" id="modal-form-input-task-video" size="md">-->
        <!--<template slot="modal-title">-->
        <!--{{this.title}}-->
        <!--</template>-->

        <!--<div class="table-responsive">-->
        <!--<table class="table b-table table-sm table-bordered table-editable">-->
        <!--<thead>-->
        <!--<tr class="text-center">-->
        <!--<th>Tên</th>-->
        <!--<th>Tài liệu</th>-->
        <!--<th></th>-->
        <!--</tr>-->
        <!--</thead>-->
        <!--<tbody>-->
        <!--<tr v-for="(item, key) in dataVideo">-->
        <!--<td><input v-model="dataVideo[key].Description" class="form-control" @change="changeDescription(key)"/></td>-->
        <!--<td><input v-model="dataVideo[key].DocID" class="form-control"/></td>-->
        <!--<td style="text-align: center;width: 50px;"><i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o" style="font-size: 18px; cursor: pointer;"></i></td>-->
        <!--</tr>-->
        <!--</tbody>-->
        <!--</table>-->
        <!--</div>-->
        <!--<template v-slot:modal-footer>-->
        <!--<div class="w-100 left">-->
        <!--<b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdateInput()">-->
        <!--Lưu-->
        <!--</b-button>-->
        <!--<b-button variant="primary" size="md" class="float-left mr-2" @click="">-->
        <!--Hủy-->
        <!--</b-button>-->
        <!--<b-button-->
        <!--variant="primary"-->
        <!--size="md"-->
        <!--class="float-left mr-2"-->
        <!--@click="onHideModal()"-->
        <!--&gt;-->
        <!--Đóng-->
        <!--</b-button>-->
        <!--</div>-->
        <!--</template>-->

        <!--</b-modal>-->
    </a>

</template>

<script>
    import ApiService from '@/services/api.service';
    import mixinLists from '@/mixins/lists';
    import TaskVideoContent from "./partials/TaskVideoContent";
    import IjcoreUploadMultipleVideo from "../../../components/IjcoreUploadMultipleVideo";
    import IjcoreUploadInputMultipleVideo from "../../../components/IjcoreUploadInputMultipleVideo";
    export default {
        name: 'TaskVideo',
        mixins: [mixinLists],
        components: {
            IjcoreUploadInputMultipleVideo,
            IjcoreUploadMultipleVideo,
            TaskVideoContent
        },
        computed: {
            rows() {
                return this.totalRows
            },
        },
        data () {
            return {
                isForm: false,
                listtable: [
                ],
                tableName: '',
                search:'',
                lenghNo: 0,
                dataVideo:[],
            }
        },
        created() {

        },
        mounted() {
        },
        methods: {
            changeVideo(videos){
                let self = this;
                let dateC = __.convertDateTimeToString(new Date());
                for (var i = 0; i < videos.length; i++){
                    self.$store.commit('isLoading', true);
                    var video = videos[i];
                    let formData = new FormData();
                    formData.append('LineID', '');
                    formData.append('VideoUpload', video);
                    formData.append('TaskID', self.Task.TaskID);
                    formData.append('VideoName', video.name);
                    formData.append('Description', video.name);
                    formData.append('DocID', '');
                    formData.append('DocNo', '');
                    formData.append('DocName', '');
                    formData.append('VideoType', video.name.split('.').pop());
                    formData.append('VideoSize', video.size);
                    formData.append('DateModified', dateC);
                    formData.append('UserModified', '');
                    formData.append('changeVideo', 1);
                    formData.append('changeData', 1);

                    let currentObj = this;
                    const config = {
                        headers: {
                            'content-type': 'multipart/form-data',
                        }
                    }

                    // send upload request
                    axios.post('task/api/task/task-upload-video/' + self.Task.TaskID, formData, config)
                        .then(function (response) {
                            currentObj.success = response.data.success;
                            currentObj.videoname = "";
                            let dataR = response.data.data;
                            self.value.push({
                                LineID: dataR.LineID,
                                VideoUpload: video,
                                TaskID : dataR.TaskID,
                                FileID : dataR.FileID,
                                VideoName : video.name,
                                Description : video.name,
                                VideoType : video.name.split('.').pop(),
                                VideoSize : video.size,
                                DateModified :dateC,
                                UserModified : dataR.UserModified,
                                Link : dataR.Link,
                                DateModifiedRoot : '',
                                FileNameRoot : '',
                                DocID : '',
                                DocNo : '',
                                DocName : '',
                                changeVideo : 0,//Đã thay đổi video
                                changeData : 0
                            });
                            self.$store.commit('isLoading', false);
                        })
                        .catch(function (error) {
                            // currentObj.output = error;
                        });
                }

            },
            changeVideoAndInput(videos){
                let self = this;
                self.dataVideo = [];
                let dateC = __.convertDateTimeToString(new Date());
                for (var i = 0; i < videos.length; i++){
                    var video = videos[i];
                    self.dataVideo.push({
                        VideoUpload: video,
                        TaskID : '',
                        FileID : '',
                        VideoName : video.name,
                        Description : video.name,
                        VideoType : video.name.split('.').pop(),
                        VideoSize : video.size,
                        DateModified :dateC,
                        UserModified : '',
                        Link : '',
                        DateModifiedRoot : '',
                        FileNameRoot : '',
                        DocID : '',
                        DocNo : '',
                        DocName : '',
                        changeVideo : 1,//Đã thay đổi video
                        changeData : 1
                    });
                }
                this.$refs['modal-input'].show();
            },
            formatDate(data){
                data = data.split(' ');
                data = data[0];
                data = data.split('-');
                let dd = data[2];
                let mm = data[1];
                let yyyy = data[0];
                data = dd + '/' + mm + '/' + yyyy;
                return data;
            },
            fetchData(){


            },
            onSaveModal(){
                this.$bvToast.toast('Đã lưu ràng buộc', {
                    variant: 'success',
                    solid: true
                });
            },
            onCancelModal(e){
                this.onResetModal();
                e.preventDefault();
            },
            onCancelModal(e){
                this.onResetModal();
                e.preventDefault();
            },
            onHideModal(){
                this.$refs['modal'].hide();
                this.isForm = false;
            },
            onToggleModal(){
                let self = this;
                this.currentPage = 1;
                this.$refs['modal'].show();
                this.fetchData();
            },
            onResetModal(){
            },
            onEdit(){
                this.isForm = true;
            },
            onUpdate(){
                let self = this;
                _.forEach(this.value, function (val, key) {
                    if(self.value[key].changeData == 1){
                        let formData = new FormData();
                        formData.append('LineID', self.value[key].LineID);
                        formData.append('VideoUpload', self.value[key].VideoUpload);
                        formData.append('TaskID', self.Task.TaskID);
                        formData.append('VideoName', self.value[key].VideoName);
                        formData.append('Description', self.value[key].Description);
                        formData.append('DocID', self.value[key].DocID);
                        formData.append('DocNo', self.value[key].DocNo);
                        formData.append('DocName', self.value[key].DocName);
                        formData.append('VideoType', self.value[key].VideoType);
                        formData.append('VideoSize', self.value[key].VideoSize);
                        formData.append('DateModified', self.value[key].DateModified);
                        formData.append('UserModified', self.value[key].UserModified);
                        formData.append('changeVideo', self.value[key].changeVideo);

                        let currentObj = this;
                        const config = {
                            headers: {
                                'content-type': 'multipart/form-data',
                            }
                        }

                        // send upload request
                        axios.post('task/api/task/task-upload-video/' + self.Task.TaskID, formData, config)
                            .then(function (response) {
                                currentObj.success = response.data.success;
                                currentObj.videoname = "";
                              if ($('.component-dataflow').length) {
                                self.$_storeTaskDataflowNotice(self.Task.TaskID, 'updateVideo');
                              }else {
                                self.$_storeTaskNotice(self.Task.TaskID, 'updateVideo');
                              }
                            })
                            .catch(function (error) {
                                // currentObj.output = error;
                            });
                    }

                });
            },
            onUpdateInput(){
                let self = this;
                let dateC = __.convertDateTimeToString(new Date());
                for (var i = 0; i < this.dataVideo.length; i++){
                    self.$store.commit('isLoading', true);
                    let formData = new FormData();
                    formData.append('LineID', '');
                    formData.append('VideoUpload', self.dataVideo[i].VideoUpload);
                    formData.append('TaskID', self.Task.TaskID);
                    formData.append('VideoName', self.dataVideo[i].VideoName);
                    formData.append('Description', self.dataVideo[i].Description);
                    formData.append('DocID', self.dataVideo[i].DocID);
                    formData.append('DocNo', self.dataVideo[i].DocNo);
                    formData.append('DocName', self.dataVideo[i].DocName);
                    formData.append('VideoType', self.dataVideo[i].VideoType);
                    formData.append('VideoSize', self.dataVideo[i].VideoSize);
                    formData.append('DateModified', dateC);
                    formData.append('UserModified', '');
                    formData.append('changeVideo', 1);
                    formData.append('changeData', 1);

                    let currentObj = this;
                    const config = {
                        headers: {
                            'content-type': 'multipart/form-data',
                        }
                    }
                    // send upload request
                    axios.post('task/api/task/task-upload-video/' + self.Task.TaskID, formData, config)
                        .then(function (response) {
                            currentObj.success = response.data.success;
                            currentObj.videoname = "";
                            let dataR = response.data.data;
                            self.value.push({
                                TaskID : dataR.TaskID,
                                FileID : dataR.FileID,
                                VideoName : dataR.VideoName,
                                Description : dataR.Description,
                                VideoType : dataR.VideoType,
                                VideoSize : dataR.VideoSize,
                                DateModified :dateC,
                                UserModified : dataR.UserModified,
                                Link : dataR.Link,
                                DateModifiedRoot : '',
                                FileNameRoot : '',
                                DocID : dataR.DocID,
                                DocNo : dataR.DocNo,
                                DocName : dataR.DocName,
                                changeVideo : 0,//Đã thay đổi video
                                changeData : 0
                            });
                            self.$store.commit('isLoading', false);
                        })
                        .catch(function (error) {
                            console.log(error);
                            // currentObj.output = error;
                        });
                }
                this.$refs['modal-input'].hide();

            }
        },
        watch: {
            currentPage() {
                this.fetchData();
            }
        },
        props: {
            value: {},
            title:{},
            name:{},
            api: {},
            Task: {},
            table: {}
        },
    }
</script>
<style>
    .readonly{
        background-color: #fff !important;
    }
    .table th, .table td{
        border-bottom: 1px solid #c8ced3;
    }
    .modal-footer ol,.modal-footer ul,.modal-footer dl{
        margin-bottom: 0px;
    }
    #modal-form-input-task-video .modal-lg .modal-content{
        width: 1024px;
        margin: auto;
    }
    @media (max-width: 1024px){
        #modal-form-input-task-video .modal-lg {
            max-width: 100%;
        }
        #modal-form-input-task-video .modal-lg .modal-content{
            width: 90%;
            margin: auto;
        }
    }
    @media (min-width: 992px){
        #modal-form-input-task-video .modal-lg {
            max-width: 100%;
        }
    }
</style>

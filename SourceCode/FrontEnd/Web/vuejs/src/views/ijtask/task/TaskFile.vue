<template>
    <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
        <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
        <b-modal scrollable ref="modal" id="modal-form-input-task-file" size="xl">
            <template slot="modal-title">
                {{this.title}}
            </template>
            <TaskFileContent v-model="value" v-if="!isForm" :Task="Task">
            </TaskFileContent>
            <TaskFileContent v-model="value" :isForm="true" v-if="isForm" :Task="Task">
            </TaskFileContent>
            <template v-slot:modal-footer>
                <div class="w-100 left">
                    <!--<b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()" v-if="!isForm"-->
                    <!--&gt;-->
                    <!--Sửa-->
                    <!--</b-button>-->
                    <IjcoreUploadMultipleFile v-on:changed="changeFile" v-if="!isForm"></IjcoreUploadMultipleFile>
                    <!--<IjcoreUploadInputMultipleFile v-on:changed="changeFileAndInput" v-if="!isForm"></IjcoreUploadInputMultipleFile>-->
                    <b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdate()" v-if="isForm">
                        Lưu
                    </b-button>
                    <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideModal" v-if="isForm">
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
        <!--<b-modal ref="modal-input" id="modal-form-input-task-file" size="md">-->
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
                    <!--<tr v-for="(item, key) in dataFile">-->
                        <!--<td><input v-model="dataFile[key].Description" class="form-control" @change="changeDescription(key)"/></td>-->
                        <!--<td><input v-model="dataFile[key].DocID" class="form-control"/></td>-->
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
    import TaskFileContent from "./partials/TaskFileContent";
    import IjcoreUploadMultipleFile from "../../../components/IjcoreUploadMultipleFile";
    import IjcoreUploadInputMultipleFile from "../../../components/IjcoreUploadInputMultipleFile";
    export default {
        name: 'TaskFile',
        mixins: [mixinLists],
        components: {
            IjcoreUploadInputMultipleFile,
            IjcoreUploadMultipleFile,
            TaskFileContent
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
                dataFile:[],
            }
        },
        created() {

        },
        mounted() {
        },
        methods: {
            changeFile(files){
                let self = this;
                let dateC = __.convertDateTimeToString(new Date());
                for (var i = 0; i < files.length; i++){
                    self.$store.commit('isLoading', true);
                    var file = files[i];
                    let formData = new FormData();
                    formData.append('LineID', '');
                    formData.append('FileUpload', file);
                    formData.append('TaskID', self.Task.TaskID);
                    formData.append('FileName', file.name);
                    formData.append('Description', file.name);
                    formData.append('DocID', '');
                    formData.append('DocNo', '');
                    formData.append('DocName', '');
                    formData.append('FileType', file.name.split('.').pop());
                    formData.append('FileSize', file.size);
                    formData.append('DateModified', dateC);
                    formData.append('UserModified', '');
                    formData.append('changeFile', 1);
                    formData.append('changeData', 1);

                    let currentObj = this;
                    const config = {
                        headers: {
                            'content-type': 'multipart/form-data',
                        }
                    }

                    // send upload request
                    axios.post('task/api/task/task-upload-file/' + self.Task.TaskID, formData, config)
                        .then(function (response) {
                            currentObj.success = response.data.success;
                            currentObj.filename = "";
                            let dataR = response.data.data;
                            self.value.push({
                                LineID: dataR.LineID,
                                FileUpload: file,
                                TaskID : dataR.TaskID,
                                FileID : dataR.FileID,
                                FileName : file.name,
                                Description : file.name,
                                FileType : file.name.split('.').pop(),
                                FileSize : file.size,
                                DateModified :dateC,
                                UserModified : dataR.UserModified,
                                Link : dataR.Link,
                                DateModifiedRoot : '',
                                FileNameRoot : '',
                                DocID : '',
                                DocNo : '',
                                DocName : '',
                                changeFile : 0,//Đã thay đổi file
                                changeData : 0
                            });
                            self.$store.commit('isLoading', false);
                        })
                        .catch(function (error) {
                            // currentObj.output = error;
                        });
                }

            },
            changeFileAndInput(files){
                let self = this;
                self.dataFile = [];
                let dateC = __.convertDateTimeToString(new Date());
                for (var i = 0; i < files.length; i++){
                    var file = files[i];
                    self.dataFile.push({
                        FileUpload: file,
                        TaskID : '',
                        FileID : '',
                        FileName : file.name,
                        Description : file.name,
                        FileType : file.name.split('.').pop(),
                        FileSize : file.size,
                        DateModified :dateC,
                        UserModified : '',
                        Link : '',
                        DateModifiedRoot : '',
                        FileNameRoot : '',
                        DocID : '',
                        DocNo : '',
                        DocName : '',
                        changeFile : 1,//Đã thay đổi file
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
                      formData.append('FileUpload', self.value[key].FileUpload);
                      formData.append('TaskID', self.Task.TaskID);
                      formData.append('FileName', self.value[key].FileName);
                      formData.append('Description', self.value[key].Description);
                      formData.append('DocID', self.value[key].DocID);
                      formData.append('DocNo', self.value[key].DocNo);
                      formData.append('DocName', self.value[key].DocName);
                      formData.append('FileType', self.value[key].FileType);
                      formData.append('FileSize', self.value[key].FileSize);
                      formData.append('DateModified', self.value[key].DateModified);
                      formData.append('UserModified', self.value[key].UserModified);
                      formData.append('changeFile', self.value[key].changeFile);

                      let currentObj = this;
                      const config = {
                        headers: {
                          'content-type': 'multipart/form-data',
                        }
                      };

                        // send upload request
                      axios.post('task/api/task/task-upload-file/' + self.Task.TaskID, formData, config)
                          .then(function (response) {
                            currentObj.success = response.data.success;
                            currentObj.filename = "";
                            if ($('.component-dataflow').length) {
                              self.$_storeTaskDataflowNotice(self.Task.TaskID, 'updateFile');
                            }else {
                              self.$_storeTaskNotice(self.Task.TaskID, 'updateFile');
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
                for (var i = 0; i < this.dataFile.length; i++){
                    self.$store.commit('isLoading', true);
                    let formData = new FormData();
                    formData.append('LineID', '');
                    formData.append('FileUpload', self.dataFile[i].FileUpload);
                    formData.append('TaskID', self.Task.TaskID);
                    formData.append('FileName', self.dataFile[i].FileName);
                    formData.append('Description', self.dataFile[i].Description);
                    formData.append('DocID', self.dataFile[i].DocID);
                    formData.append('DocNo', self.dataFile[i].DocNo);
                    formData.append('DocName', self.dataFile[i].DocName);
                    formData.append('FileType', self.dataFile[i].FileType);
                    formData.append('FileSize', self.dataFile[i].FileSize);
                    formData.append('DateModified', dateC);
                    formData.append('UserModified', '');
                    formData.append('changeFile', 1);
                    formData.append('changeData', 1);

                    let currentObj = this;
                    const config = {
                        headers: {
                            'content-type': 'multipart/form-data',
                        }
                    }
                    // send upload request
                    axios.post('task/api/task/task-upload-file/' + self.Task.TaskID, formData, config)
                        .then(function (response) {
                            currentObj.success = response.data.success;
                            currentObj.filename = "";
                            let dataR = response.data.data;
                            self.value.push({
                                TaskID : dataR.TaskID,
                                FileID : dataR.FileID,
                                FileName : dataR.FileName,
                                Description : dataR.Description,
                                FileType : dataR.FileType,
                                FileSize : dataR.FileSize,
                                DateModified :dateC,
                                UserModified : dataR.UserModified,
                                Link : dataR.Link,
                                DateModifiedRoot : '',
                                FileNameRoot : '',
                                DocID : dataR.DocID,
                                DocNo : dataR.DocNo,
                                DocName : dataR.DocName,
                                changeFile : 0,//Đã thay đổi file
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
    #modal-form-input-task-file .modal-lg .modal-content{
        width: 1024px;
        margin: auto;
    }
    @media (max-width: 1024px){
        #modal-form-input-task-file .modal-lg {
            max-width: 100%;
        }
        #modal-form-input-task-file .modal-lg .modal-content{
            width: 90%;
            margin: auto;
        }
    }
    @media (min-width: 992px){
        #modal-form-input-task-file .modal-lg {
            max-width: 100%;
        }
    }
</style>

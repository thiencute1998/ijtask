<template>
    <div>
        <b-modal ref="modal-update-status" id="modal-update-status" ok-title="Lưu" cancel-title="Đóng" size="xl" @hide="onHideModal($event)" @ok="handleSubmitForm($event)">
            <template slot="modal-title">
                <div v-if="stage.displayScreen === 2">Cập nhật tình hình thực hiện</div>
                <div v-if="stage.displayScreen === 1">Tình hình thực hiện</div>
            </template>

            <template v-slot:modal-footer="{ ok, cancel, hide }">
                <!-- Emulate built in modal footer ok and cancel button actions -->
                <b-button v-if="stage.displayScreen === 2" class="mr-2" variant="primary" @click="ok()">
                    Lưu
<!--                    <task-execution-form v-model="model.TaskExecutionTrans" :title="'Thực hiện'" :Task="taskChild" :TaskStatus="[]" :addnew="true"></task-execution-form>-->
                </b-button>
                <b-button v-if="stage.displayScreen === 1" class="mr-2" variant="primary" @click="onShowUpdateStatus($event)">
                    Cập nhật
                </b-button>
                <b-button variant="primary" @click="cancel()">
                    Đóng
                </b-button>
            </template>

            <div class="row" v-if="task && taskChild">
                <div class="col-lg-2">Luồng CV</div>
                <div class="col-lg-20 mb-2">{{task.TaskName}}</div>
                <div class="col-lg-2">Chức năng</div>
                <div class="col-lg-20 mb-2">{{taskChild.WFItemName}}</div>
                <div class="col-lg-4 mb-2 d-flex align-items-center">Thời gian</div>
                <div class="col-lg-8 mb-2 offset-right-6">
                    <ijcore-date-time-picker
                        v-if="stage.displayScreen === 2"
                        v-model="model.FeatureStatusDatetime"
                        :allow-empty-clear="false"></ijcore-date-time-picker>
                    <span v-if="stage.displayScreen === 1">{{model.FeatureStatusDatetime}}</span>
                </div>
                <div class="col-lg-4 mb-2 d-flex align-items-center">Trạng thái</div>
                <div class="col-lg-8 mb-2 d-flex align-items-center">
                    <Select2
                            v-if="stage.displayScreen === 2"
                            v-model="model.FeatureStatusValue"
                            :options="model.FeatureStatusOption"
                            @select="onSelectFeatureStatus($event)"
                            :settings="{allowClear: true, minimumResultsForSearch: -1, placeholder: {id: 0, text: 'Chọn trạng thái'}}">
                    </Select2>
                    <span v-if="stage.displayScreen === 1">{{model.FeatureStatusTitle}}</span>
                </div>
                <div class="col-lg-4 mb-2 d-flex align-items-center">% Hoàn thành</div>
                <div class="col-lg-4 mb-2 offset-right-2">
                    <b-form-input
                        v-if="stage.displayScreen === 2"
                        v-model="model.PercentCompleted"
                        :disabled="stage.disablePercentCompleted"
                        type="number" min="0" max="100"></b-form-input>
                    <span v-if="stage.displayScreen === 1">{{model.PercentCompleted}}</span>
                </div>
<!--                <div class="col-lg-4 mb-2 d-flex align-items-center" v-if="model.nextFeaturesBranchOption.length && stage.isCompletedStatus">CV tiếp theo</div>-->
<!--                <div class="col-lg-20 mb-2" v-if="model.nextFeaturesBranchOption.length && stage.isCompletedStatus">-->
<!--                    <Select2-->
<!--                            v-model="model.nextFeatureBranch"-->
<!--                            :options="model.nextFeaturesBranchOption"-->
<!--                            :settings="{allowClear: true, minimumResultsForSearch: -1, placeholder: {id: 0, text: 'Chọn công việc kế tiếp'}}">-->
<!--                    </Select2>-->
<!--                </div>-->

                <div class="col-lg-4 mb-2 d-flex align-items-center">Người thực hiện</div>
                <div class="col-lg-20 mb-2">
                    <Select2
                        v-if="stage.displayScreen === 2"
                        v-model="model.usersAssign"
                        :options="model.usersAssignOption"
                        :settings="{allowClear: true, minimumResultsForSearch: -1, multiple: true, placeholder: {id: 0, text: 'Chọn người thực hiện'}}">
                    </Select2>
                    <div v-if="stage.displayScreen === 1">
                        <span v-for="(taskUserAssign, key) in model.taskUsersAssign">{{getUserNameByID(taskUserAssign.UserID)}} <i v-if="key !== (model.taskUsersAssign.length - 1)">, </i></span>
                    </div>

                </div>
                <div class="col-lg-2">Mô tả</div>
                <div class="col-lg-20">
                    <b-form-textarea
                        v-if="stage.displayScreen === 2"
                        v-model="model.FeatureStatusDescription"
                        style="min-height: 120px"
                    ></b-form-textarea>
                    <div v-if="stage.displayScreen === 1">{{model.FeatureStatusDescription}}</div>
                </div>
            </div>

<!--            <div v-if="stage.displayScreen === 1">-->
<!--                <task-execution-content v-model="model.TaskExecutionTrans" :TaskStatus="[]" :Task="taskChild"></task-execution-content>-->
<!--            </div>-->
<!--            <div v-if="stage.displayScreen === 2">-->

<!--            </div>-->

<!--            <task-execution-form :title="'Thực hiện'" :Task="task" :TaskStatus="[]" :addnew="true"></task-execution-form>-->
        </b-modal>

<!--        <task-execution-form :title="'Thực hiện'" :Task="task" :TaskStatus="[]" :addnew="true"></task-execution-form>-->
    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import IjcoreDateTimePicker from '@/components/IjcoreDateTimePicker';
    import Select2 from 'v-select2-component';
    import TaskExecutionForm from '@/views/ijtask/task/partials/TaskExecutionForm';
    import TaskExecutionContent from '@/views/ijtask/task/partials/TaskExecutionContent';

    const FeatureStatusApi = 'task/api/dataflow/featureStatus';
    const UpdateFeatureStatusApi = 'task/api/dataflow/updateFeatureStatus';

    export default {
        name: 'dataflow-update-status',
        components: {
            IjcoreDateTimePicker,
            Select2,
            TaskExecutionForm,
            TaskExecutionContent
        },
        props: ['task', 'taskChild', 'value'],
        data() {
            return {
                model: {
                    FeatureStatusDatetime: '',
                    FeatureStatusValue: null,
                    FeatureStatusTitle: '',
                    FeatureStatusDescription: '',
                    FeatureStatusOption: [],
                    taskStatus: null,
                    taskUsersAssign: [],
                    usersAssign: [],
                    usersAssignOption: [],
                    PercentCompleted: null,
                    ExecutionStatus: null,
                    // nextFeatureBranch: null,
                    // nextFeaturesBranchOption: []

                    TaskExecutionTrans: []
                },
                stage: {
                    disablePercentCompleted: false,
                    isCompletedStatus: false,
                    // 1 - view, 2 - edit
                    displayScreen: 1
                }
            }
        },
        mounted(){
            this.init();
        },
        methods: {
            init(){
                // check dataflow is finished
                if (this.task.Finished === 1) {
                    this.$bvToast.toast('Luồng công việc đã hoàn thành', {
                        title: 'Thông báo',
                        variant: 'warning',
                        solid: true
                    });
                    return;
                }

                let self = this;
                let requestData = {
                    method: 'post',
                    url: FeatureStatusApi,
                    data: {
                        StatusID: this.taskChild.FeatureStatusID,
                        TaskID: this.taskChild.TaskID,
                        WFID: this.taskChild.WFID,
                        WFItemID: this.taskChild.WFItemID
                    },
                };


                this.$store.commit('isLoading', true);
                ApiService.customRequest(requestData).then((response) => {
                    self.$store.commit('isLoading', false);
                    let responseData = response.data;
                    if (responseData.status === 1) {

                        if (responseData.data.TaskExecutionTrans) {
                            self.model.TaskExecutionTrans = responseData.data.TaskExecutionTrans;
                            // self.model.FeatureStatusDatetime = __.convertDateTimeToString(self.model.taskStatus.Datetime);
                            // self.model.FeatureStatusValue = self.model.taskStatus.FeatureStatusValue;
                            // self.model.PercentCompleted = self.model.taskStatus.PercentCompleted;
                            // self.model.FeatureStatusDescription = self.model.taskStatus.Content;
                            // self.model.FeatureStatusTitle = self.model.taskStatus.FeatureStatusTitle;
                        }else {
                            self.stage.displayScreen = 2;
                        }

                        // set user assign for last status
                        // if (responseData.data.taskUsersAssign) {
                        //     self.model.taskUsersAssign = responseData.data.taskUsersAssign;
                        //     self.model.usersAssign = [];
                        //     _.forEach(responseData.data.taskUsersAssign, function (taskUserAssign, key) {
                        //         self.model.usersAssign.push(taskUserAssign.UserID);
                        //     });
                        // }

                        // all feature status
                        self.model.FeatureStatusOption = [];
                        _.forEach(responseData.data.status, function (status, key) {
                            let tmpObj = {};
                            tmpObj.id = status.StatusValue;
                            tmpObj.text = status.StatusDescription;
                            tmpObj.ExecutionStatus = status.ExecutionStatus;
                            self.model.FeatureStatusOption.push(tmpObj);
                        });

                        // all user assign
                        // self.model.usersAssignOption = [];
                        // _.forEach(responseData.data.usersAssign, function (userAssign, key) {
                        //     let tmpObj = {};
                        //     tmpObj.id = userAssign.UserID;
                        //     tmpObj.text = userAssign.EmployeeName;
                        //     self.model.usersAssignOption.push(tmpObj);
                        // });

                        // console.log(self.model.usersAssign);
                        // console.log(self.model.usersAssignOption);

                        // self.model.nextFeaturesBranchOption = [];
                        // _.forEach(responseData.data.nextFeaturesBranch, function (nextFeatureBranch, key) {
                        //     let tmpObj = {};
                        //     tmpObj.id = nextFeatureBranch.WFItemID;
                        //     tmpObj.text = nextFeatureBranch.WFItemName;
                        //     tmpObj.ConstraintCondition = nextFeatureBranch.ConstraintCondition;
                        //     self.model.nextFeaturesBranchOption.push(tmpObj);
                        // });

                    } else {
                        self.$store.commit('isLoading', false);
                    }
                }, (error) => {
                  console.log(error);
                  self.$store.commit('isLoading', false);
                  Swal.fire({
                    title: 'Thông báo',
                    text: 'Không kết nối được với máy chủ',
                    confirmButtonText: 'Đóng'
                  });
                });
                this.$refs['modal-update-status'].show();
            },
            // TODO chức năng cập nhật trạng thái
            handleSubmitForm(e){
                e.preventDefault();
                let self = this;
                let featureStatusObj = _.find(this.model.FeatureStatusOption, ['id', Number(this.model.FeatureStatusValue)]);
                let requestData = {
                    method: 'post',
                    url: UpdateFeatureStatusApi,
                    data: {
                        DFID: this.taskChild.DFID,
                        DFKey: this.taskChild.DFKey,
                        WFID: this.taskChild.WFID,
                        WFItemID: this.taskChild.WFItemID,
                        TaskID: this.taskChild.TaskID,
                        TaskIDParent: this.task.TaskID,
                        usersAssign: this.model.usersAssign,
                        PercentCompleted: this.model.PercentCompleted,
                        ExecutionStatus: (featureStatusObj) ? featureStatusObj.ExecutionStatus : 1,
                        FeatureStatusDatetime: this.model.FeatureStatusDatetime,
                        FeatureStatusID: this.taskChild.FeatureStatusID,
                        FeatureStatusValue: this.model.FeatureStatusValue,
                        FeatureStatusTitle: (featureStatusObj) ? featureStatusObj.text : '',
                        FeatureStatusDescription: this.model.FeatureStatusDescription,
                        // nextFeatureBranch: this.model.nextFeatureBranch
                    },
                };

                this.$store.commit('isLoading', true);
                ApiService.customRequest(requestData).then((response) => {
                    self.$store.commit('isLoading', false);
                    let responseData = response.data;
                    if (responseData.status === 1) {
                        // self.$parent.$options.methods.fetchDataflow(false);
                        // self.$dispatch('fetchDataflow', false);
                        self.$emit('onReloadDataflow');
                        this.$emit('input', false);
                    } else {
                        self.$store.commit('isLoading', false);
                    }
                }, (error) => {
                  console.log(error);
                  self.$store.commit('isLoading', false);
                  Swal.fire({
                    title: 'Thông báo',
                    text: 'Không kết nối được với máy chủ',
                    confirmButtonText: 'Đóng'
                  });
                });
            },
            onSelectFeatureStatus(option){
                if (option.ExecutionStatus === 1) {
                    this.model.PercentCompleted = 0;
                    this.stage.disablePercentCompleted = true;
                    this.stage.isCompletedStatus = false;
                }else if (option.ExecutionStatus === 6) {
                    this.model.PercentCompleted = 100;
                    this.stage.disablePercentCompleted = true;
                    this.stage.isCompletedStatus = true;
                } else {
                    this.stage.disablePercentCompleted = false;
                    this.stage.isCompletedStatus = false;
                }
            },
            getUserNameByID(UserID){
                let userAssign = _.find(this.model.usersAssignOption, ['id', UserID]);
                if (userAssign) {
                    return userAssign.text;
                }
                return '';
            },
            onShowUpdateStatus(e){
                this.stage.displayScreen = 2;
                this.model.FeatureStatusDatetime = '';
                this.model.FeatureStatusDescription = '';
            },
            // onSaveAndClose(e){
            //     this.handleSubmitForm(e);
            //     this.$refs['modal-update-status'].hide();
            // },
            onHideModal(e){
                if (e.trigger !== 'ok') this.$emit('input', false);
            }
        },
        watch: {
        }
    }
</script>

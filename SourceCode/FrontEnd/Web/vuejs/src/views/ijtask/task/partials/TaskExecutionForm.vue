<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết" :style="StyleIcon">
    <i class="fa fa-plus-square-o ij-icon ij-icon-plus" aria-hidden="true" v-if="addnew"></i>
    <i class="fa fa-edit ij-icon" aria-hidden="true" v-if="!addnew" style="margin-right: 6px;"></i>
    <b-modal ref="modal" size="lg" id="modal-form-input-execution" no-close-on-backdrop @hide="onHideModalDataflow($event)">
      <template slot="modal-title">
        <i class="fa fa-plus" v-if="addnew"></i><i class="fa fa-edit" v-if="!addnew"></i> {{this.title}}
      </template>
      <div class="form-group row ">
        <label class="col-md-4" style="white-space: nowrap">Công việc </label>
        <label class="col-md-20 ij-data-label">{{Task.TaskName | perView(perGen, 'TaskName')}}</label>
        <label class="col-md-4" style="white-space: nowrap">Ngày bắt đầu</label>
        <label class="col-md-4 ij-data-label" style="white-space: nowrap">{{Task.StartDate | perView(perGen, 'StartDate')}}</label>
        <label class="col-md-4" style="white-space: nowrap">Hạn hoàn thành</label>
        <label class="col-md-4 ij-data-label" style="white-space: nowrap">{{Task.DueDate | perView(perGen, 'DueDate')}}</label>
        <label class="col-md-4" style="white-space: nowrap">Thời lượng(Giờ)</label>
        <label class="col-md-4 ij-data-label" style="white-space: nowrap">{{Task.Duration | perView(perGen, 'Duration')}}</label>
        <label class="col-md-4" style="white-space: nowrap">Phương thức tính</label>
        <label class="col-md-4 ij-data-label" style="white-space: nowrap">{{Task.CalMethodName | perView(perGen, 'CalMethod')}}</label>
        <label class="col-md-4" style="white-space: nowrap">Khối lượng ƯTH</label>
        <label class="col-md-4 ij-data-label" style="white-space: nowrap">{{Task.EstimatedQuantity | perView(perGen, 'EstimatedQuantity') | convertNumberToText}}</label>
        <label class="col-md-4" style="white-space: nowrap">Đơn vị tính</label>
        <label class="col-md-4 ij-data-label" style="white-space: nowrap">{{Task.UomName | perView(perGen, 'UomID')}}</label>
        <div class="ij-line">
        </div>
      </div>
      <div class="form-group row align-items-center">
        <label class="col-md-4" style="white-space: nowrap">Ngày</label>
        <div class="col-md-8" v-if="perEditView(TaskExecution.TransDate, per, 'TransDate')">
          <IjcoreDateTimePicker v-model="TaskExecution.TransDate"  :allowEmptyClear="true" v-if="perEditField(TaskExecution.TransDate, per, 'TransDate')">
          </IjcoreDateTimePicker>
          <input type="text" v-else disabled="true" class="form-control" :value="TaskExecution.TransDate"
                 placeholder=""/>
        </div>
        <div class="col-md-8" v-else>
          <input type="text" disabled class="form-control"
                 placeholder=""/>
        </div>
        <label class="col-md-4" style="white-space: nowrap">Người thực hiện</label>
        <div class="col-md-8" v-if="perEditView(TaskExecution.TransDate, per, 'EmployeeID')">
          <IjcoreModalListing v-model="TaskExecution" :title="'nhân viên'"  :api="'/listing/api/common/list'" :table="'employee'" v-if="perEditField(TaskExecution.EmployeeID, per, 'EmployeeID')">
          </IjcoreModalListing>
          <input type="text" v-else disabled="true" class="form-control" :value="TaskExecution.EmployeeName"
                 placeholder=""/>
        </div>
        <div class="col-md-8" v-else>
          <input type="text" disabled class="form-control"
                 placeholder=""/>
        </div>
      </div>
      <div class="form-group row align-items-center">
        <label class="col-md-4" style="white-space: nowrap">Số giờ</label>
        <div class="col-md-8" v-if="perEditView(TaskExecution.ActualHour, per, 'ActualHour')">
          <IjcoreNumber  v-model="TaskExecution.ActualHour" @changed="updateActualHour" v-if="perEditField(TaskExecution.ActualHour, per, 'ActualHour')"></IjcoreNumber>
          <input type="text" v-else disabled="true" class="form-control" :value="TaskExecution.ActualHour"
                 placeholder=""/>
        </div>
        <div class="col-md-8" v-else>
          <input type="text" disabled class="form-control"
                 placeholder=""/>
        </div>
        <label class="col-md-4" style="white-space: nowrap">Khối lượng</label>
        <div class="col-md-8" v-if="perEditView(TaskExecution.ActualQuantity, per, 'ActualQuantity')">
          <IjcoreNumber  v-model="TaskExecution.ActualQuantity" :readonly="this.Task.isActualQuantity" @changed="updatePercent" v-if="perEditField(TaskExecution.ActualQuantity, per, 'ActualQuantity')"></IjcoreNumber>
          <input type="text" v-else disabled="true" class="form-control" :value="TaskExecution.ActualQuantity"
                 placeholder=""/>
        </div>
        <div class="col-md-8" v-else>
          <input type="text" disabled class="form-control"
                 placeholder=""/>
        </div>
      </div>
      <div class="form-group row align-items-center">
        <label class="col-md-4" style="white-space: nowrap">Trạng thái</label>
        <div class="col-md-8" v-if="perEditView(TaskExecution.StatusValue, per, 'StatusValue')">
          <b-form-select v-model="TaskExecution.StatusValue" :options="TaskStatus" v-on:change="updateStatusDescription(TaskExecution.StatusValue)" v-if="perEditField(TaskExecution.StatusValue, per, 'StatusValue')"></b-form-select>
          <input type="text" v-else disabled="true" class="form-control" :value="TaskExecution.StatusDescription"
                 placeholder=""/>
        </div>
        <div class="col-md-8" v-else>
          <input type="text" disabled class="form-control"
                 placeholder=""/>
        </div>
      </div>
      <div class="form-group row">
        <label class="col-md-4" style="white-space: nowrap">Nội dung</label>
        <div class="col-md-20" v-if="perEditView(value.Description, per, 'TransDate')">
          <textarea v-model="TaskExecution.Description" class="form-control" rows="3" placeholder="Nhập nội dung" v-if="perEditField(TaskExecution.Description, per, 'Description')"></textarea>
          <textarea v-else class="form-control" rows="3" placeholder="" :value="TaskExecution.Description" disabled="true"></textarea>
        </div>
        <div class="col-md-20" v-else>
          <input type="text" disabled class="form-control"
                 placeholder=""/>
        </div>
      </div>
      <template v-slot:modal-footer>
        <div class="left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdate()">
            Lưu
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideModal()">
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
        <div class="right d-flex align-items-center" style="margin-right: 0px;margin-left:auto;">
          Tổng khối lượng thực hiện: &nbsp; <b>{{Task.TotalActualQuantity | convertNumberToText}}</b> &nbsp; | &nbsp; % hoàn thành:
          <IjcoreNumber class="ml-2" style="max-width: 100px" v-model="Task.PercentCompleted"></IjcoreNumber>
        </div>
      </template>

    </b-modal>
  </a>
</template>

<script>
  import ApiService from '@/services/api.service';
  // import mixinLists from '@/mixins/lists';
  import IjcoreDateTimePicker from "../../../../components/IjcoreDateTimePicker";
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  import IjcoreNumber from "../../../../components/IjcoreNumber";
  export default {
    name: 'TaskExecutionForm',
    // mixins: [mixinLists],
    components: {
      IjcoreNumber,
      IjcoreModalListing,
      IjcoreDateTimePicker,
    },
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data () {
      return {
        isActualQuantity: false,
        CalMethodName: '',
        isForm: false,
        listtable: [
        ],
        tableName: '',
        search:'',
        lenghNo: 0,
        TaskExecution:{
          TaskID: this.Task.TaskID,
          ActualHour: '',
          ActualQuantity: '',
          PercentCompleted: '',
          Description: '',
          TransDate: '',
          EmployeeID: '',
          EmployeeName: '',
          StatusID: '',
          StatusName: '',
          StatusValue: '',
          StatusDescription: '',
          addnew: true,
        },
        OldActualQuantity: '0',
        OldTotalActualQuantity: '0',
        EmployeeLogin: JSON.parse(localStorage.getItem('Employee')),
      }
    },
    created() {
      if(this.TaskExecution.ActualQuantity){
        this.OldActualQuantity = this.TaskExecution.ActualQuantity;
      }else{
        this.OldActualQuantity = '0';
      }
    },
    mounted() {
      if (this.isDataflow) {
        if (this.Task.TaskParent && this.Task.TaskParent.StatusCompleted === 1) {
          this.$bvToast.toast('Luồng công việc đã hoàn thành', {
            title: 'Thông báo',
            variant: 'warning',
            solid: true
          });
          return;
        }
        this.onToggleModal();
      }
    },
    methods: {
      perEditView(value, per, field) {
        let AccessField = ','+per['AccessField']+',';
        if(per['AccessField'] === 'all' || AccessField.includes(','+field+',')){
          return true;
        }else{
          return false;
        }
      },
      perEditField(value, per, field){
        let EditField = ','+per['EditField']+',';
        if (per['EditField'] === 'all' || EditField.includes(',' + field + ',')) {
          return true;
        }else{
          return false;
        }
      },
      undoValue(){
      },
      updateActualHour(){
        if(this.Task.CalMethod == 1){
          this.TaskExecution.ActualQuantity = this.TaskExecution.ActualHour;
          this.updatePercent();
        }
      },
      updatePercent(){
        this.Task.EstimatedQuantity = Number(__.convertNumberToText(this.Task.EstimatedQuantity));
        this.Task.OldTotalActualQuantity = Number(__.convertNumberToText(this.Task.OldTotalActualQuantity));
        this.OldActualQuantity = Number(__.convertNumberToText(this.OldActualQuantity));

        if (!this.Task.EstimatedQuantity) {
          return;
        }
        this.Task.TotalActualQuantity = this.Task.OldTotalActualQuantity + this.TaskExecution.ActualQuantity - this.OldActualQuantity;
        this.Task.PercentCompleted = this.Task.TotalActualQuantity * 100 / this.Task.EstimatedQuantity;

        if(this.addnew){
          if(this.Task.PercentCompleted <= 0){
            let statusItem = _.find(this.TaskStatus, ['ExecutionStatus', 1]);
            if (statusItem) {
              this.TaskExecution.StatusValue = statusItem.value;
              this.TaskExecution.StatusDescription = statusItem.text;
            }
          }else if(this.Task.PercentCompleted >= 100){
            let statusItem = _.find(this.TaskStatus, ['ExecutionStatus', 6]);
            if (statusItem) {
              this.TaskExecution.StatusValue = statusItem.value;
              this.TaskExecution.StatusDescription = statusItem.text;
            }
          }else{
            let statusItem = _.find(this.TaskStatus, ['ExecutionStatus', 3]);
            if (statusItem) {
              this.TaskExecution.StatusValue = statusItem.value;
              this.TaskExecution.StatusDescription = statusItem.text;
            }
          }
        }
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
      onHideModal(){
        if(this.addnew){
          this.TaskExecution.ActualHour = '';
          this.TaskExecution.ActualQuantity = '';
          this.Task.TotalActualQuantity = this.Task.OldTotalActualQuantity + this.TaskExecution.ActualQuantity - this.OldActualQuantity;
          this.Task.PercentCompleted = this.Task.TotalActualQuantity * 100 / this.Task.EstimatedQuantity;
        }else{
          this.TaskExecution.ActualHour = this.value[this.keyArray].ActualHour;
          this.TaskExecution.ActualQuantity = this.value[this.keyArray].ActualQuantity;
          this.OldActualQuantity = this.value[this.keyArray].ActualQuantity;
          this.Task.TotalActualQuantity = this.Task.OldTotalActualQuantity;
          this.Task.PercentCompleted = this.Task.TotalActualQuantity * 100 / this.Task.EstimatedQuantity;
        }
        this.$refs['modal'].hide();
        this.isForm = false;
      },
      onHideModalDataflow(){
        if (this.isDataflow) {
          this.$emit('onHideModalTask');
        }
      },
      initDataFlow(){
        let self = this;
        let requestData = {
          method: 'post',
          url: 'task/api/dataflow/featureStatus',
          data: {
            StatusID: this.Task.FeatureStatusID,
            TaskID: this.Task.TaskID,
            WFID: this.Task.WFID,
            WFItemID: this.Task.WFItemID
          },
        };

        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          self.$store.commit('isLoading', false);
          let responseData = response.data;
          if (responseData.status === 1) {

            if (responseData.data.TaskExecutionTrans) {
              self.TaskExecution.ActualHour = responseData.data.TaskExecutionTrans.ActualHour;
              self.TaskExecution.ActualQuantity = responseData.data.TaskExecutionTrans.ActualQuantity;
              self.TaskExecution.PercentCompleted = responseData.data.TaskExecutionTrans.PercentCompleted;
              // self.TaskExecution.Description = responseData.data.TaskExecutionTrans.Description;
              // self.TaskExecution.TransDate = responseData.data.TaskExecutionTrans.TransDate;
              self.TaskExecution.EmployeeID = responseData.data.TaskExecutionTrans.EmployeeID;
              self.TaskExecution.EmployeeName = responseData.data.TaskExecutionTrans.EmployeeName;
              self.TaskExecution.StatusName = responseData.data.TaskExecutionTrans.StatusName;
              self.TaskExecution.StatusValue = responseData.data.TaskExecutionTrans.StatusValue;
              self.TaskExecution.StatusDescription = responseData.data.TaskExecutionTrans.StatusDescription;
            }

            self.TaskExecution.StatusID = self.Task.FeatureStatusID;
            if (self.Task.CalMethod === 1) {
              self.Task.CalMethodName = 'Theo thời gian';
            } else {
              self.Task.CalMethodName = 'Theo khối lượng';
            }
            // all feature status
            self.TaskStatus = [{value: null, text: 'Trạng thái'}];
            _.forEach(responseData.data.status, function (status, key) {
              let tmpObj = {};
              tmpObj.value = status.StatusValue;
              tmpObj.text = status.StatusDescription;
              tmpObj.ExecutionStatus = status.ExecutionStatus;
              self.TaskStatus.push(tmpObj);
            });

            self.$refs['modal'].show();

          } else {
            self.$store.commit('isLoading', false);
            this.onHideModalDataflow();
            Swal.fire({
              title: 'Thông báo',
              text: responseData.msg,
            });

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
      onToggleModal(){
        if (!this.$parent.showTaskExecution) this.$parent.showTaskExecution = true;
        if(!this.addnew){
          this.TaskExecution.TransID = this.value[this.keyArray].TransID;
          this.TaskExecution.TaskID = this.value[this.keyArray].TaskID;
          this.TaskExecution.ActualHour = this.value[this.keyArray].ActualHour;
          this.TaskExecution.ActualQuantity = this.value[this.keyArray].ActualQuantity;
          this.TaskExecution.PercentCompleted = this.value[this.keyArray].PercentCompleted;
          this.TaskExecution.Description = this.value[this.keyArray].Description;
          this.TaskExecution.TransDate = this.value[this.keyArray].TransDate;
          this.TaskExecution.EmployeeID = this.value[this.keyArray].EmployeeID;
          this.TaskExecution.EmployeeName = this.value[this.keyArray].EmployeeName;
          this.TaskExecution.StatusID = this.value[this.keyArray].StatusID;
          this.TaskExecution.StatusName = this.value[this.keyArray].StatusName;
          this.TaskExecution.StatusValue = this.value[this.keyArray].StatusValue;
          this.TaskExecution.StatusDescription = this.value[this.keyArray].StatusDescription;
          this.TaskExecution.addnew = false;

          if(this.TaskExecution.ActualQuantity){
            this.OldActualQuantity = this.TaskExecution.ActualQuantity;
          }else{
            this.OldActualQuantity = '0';
          }
          this.$refs['modal'].show();
        }else{
          this.TaskExecution.EmployeeID = this.EmployeeLogin.EmployeeID;
          this.TaskExecution.EmployeeName = this.EmployeeLogin.EmployeeName;
          this.TaskExecution.TransDate = __.convertDateTimeToString(new Date());

          if(this.Task.PercentCompleted <= 0){
            let statusItem = _.find(this.TaskStatus, ['ExecutionStatus', 1]);
            if (statusItem) {
              this.TaskExecution.StatusValue = statusItem.value;
              this.TaskExecution.StatusDescription = statusItem.text;
            }
          }else if(this.Task.PercentCompleted >= 100){
            let statusItem = _.find(this.TaskStatus, ['ExecutionStatus', 6]);
            if (statusItem) {
              this.TaskExecution.StatusValue = statusItem.value;
              this.TaskExecution.StatusDescription = statusItem.text;
            }
          }else{
            let statusItem = _.find(this.TaskStatus, ['ExecutionStatus', 3]);
            if (statusItem) {
              this.TaskExecution.StatusValue = statusItem.value;
              this.TaskExecution.StatusDescription = statusItem.text;
            }
          }

          this.$refs['modal'].show();

        }

      },
      onResetModal(){
      },
      onEdit(){
        this.isForm = true;
      },
      updateStatusDescription(value){
        let statusItem = _.find(this.TaskStatus, ['value', Number(value)]);
        if (statusItem) {
          this.TaskExecution.StatusDescription = statusItem.text;
          if (statusItem.ExecutionStatus === 1) {
            this.Task.PercentCompleted = 0;
          }
          if (statusItem.ExecutionStatus === 6) {
            this.Task.PercentCompleted = 100;
          }
        }
      },
      onUpdate(){
        let self = this;
        let TaskExecutionData = self.TaskExecution;
        TaskExecutionData.ActualHour = (TaskExecutionData.ActualHour) ? TaskExecutionData.ActualHour : 0;
        TaskExecutionData.ActualQuantity = (TaskExecutionData.ActualQuantity) ? TaskExecutionData.ActualQuantity : 0;
        TaskExecutionData.OldActualQuantity = (this.OldActualQuantity) ? this.OldActualQuantity : 0;
        TaskExecutionData.StatusID = this.Task.StatusID;
        TaskExecutionData.StatusName = this.Task.StatusName;

        let statusItem = _.find(this.TaskStatus, ['value', Number(this.TaskExecution.StatusValue)]);
        if (statusItem && statusItem.ExecutionStatus) {
          TaskExecutionData.ExecutionStatus = statusItem.ExecutionStatus;
        }

        if (this.Task.DFKey) {
          TaskExecutionData.TaskIDParent = this.Task.TaskParent.TaskID;
          TaskExecutionData.DFKey = this.Task.DFKey;
          TaskExecutionData.DFID = this.Task.DFID;
          TaskExecutionData.WFID = this.Task.WFID;
          TaskExecutionData.WFItemID = this.Task.WFItemID;
          TaskExecutionData.isDataflow = true;
        }
        let requestData = {
          method: 'post',
          url: 'task/api/task/task-execution/' + self.Task.TaskID,
          data: {
            TaskExecution: TaskExecutionData,
            PercentCompleted: (this.Task.PercentCompleted) ? this.Task.PercentCompleted : 0
          }
        };
        // edit user
        requestData.data.ItemID = self.Task.TaskID;
        ApiService.setHeader();
        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            this.$bvToast.toast('Cập nhật thành công!', {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });
            if(this.addnew){
              responsesData.data.TaskExcution.TransDate = __.convertDateTimeToString(responsesData.data.TaskExcution.TransDate);
              // responsesData.data.TaskExcution.ActualHour = responsesData.data.TaskExcution.ActualHour;
              // responsesData.data.TaskExcution.ActualQuantity = responsesData.data.TaskExcution.ActualQuantity;
              this.value.push(responsesData.data.TaskExcution);
              this.TaskExecution.ActualHour = '0';
              this.TaskExecution.ActualQuantity = '0';
              if (self.isDataflow) {
                self.$emit('onReloadDataflow');
                self.$emit('onHideModalTask');
              }

            }else{
              this.value[this.keyArray].TransID = this.TaskExecution.TransID;
              this.value[this.keyArray].TaskID = this.TaskExecution.TaskID;
              this.value[this.keyArray].ActualHour = this.TaskExecution.ActualHour;
              this.value[this.keyArray].ActualQuantity = this.TaskExecution.ActualQuantity;
              this.value[this.keyArray].PercentCompleted = this.TaskExecution.PercentCompleted;
              this.value[this.keyArray].Description = this.TaskExecution.Description;
              this.value[this.keyArray].TransDate = this.TaskExecution.TransDate;
              this.value[this.keyArray].EmployeeID = this.TaskExecution.EmployeeID;
              this.value[this.keyArray].EmployeeName = this.TaskExecution.EmployeeName;
              this.value[this.keyArray].StatusID = this.TaskExecution.StatusID;
              this.value[this.keyArray].StatusName = this.TaskExecution.StatusName;
              this.value[this.keyArray].StatusValue = this.TaskExecution.StatusValue;
              this.value[this.keyArray].StatusDescription = this.TaskExecution.StatusDescription;
              this.value[this.keyArray].addnew = false;

              if (self.Task.DFKey) {
                self.$parent.onReloadDataflow();
              }
            }
            this.Task.TotalActualQuantity = responsesData.data.Task.TotalActualQuantity;
            this.Task.OldTotalActualQuantity = this.Task.TotalActualQuantity;
            this.Task.ActualQuantity = responsesData.data.Task.ActualQuantity;
            this.Task.OldActualQuantity = this.Task.ActualQuantity;
            this.Task.PercentCompleted = responsesData.data.Task.PercentCompleted;
            this.Task.EstimatedQuantity = responsesData.data.Task.EstimatedQuantity;
            this.isForm = false;
            this.$refs['modal'].hide();
            self.$store.commit('isLoading', false);
            if ($('.component-dataflow').length) {
              if (responsesData.data.AutoNewTask) {
                self.$_storeTaskDataflowNotice(self.Task.TaskID, 'autoNewTask');
              } else {
                self.$_storeTaskDataflowNotice(self.Task.TaskID, 'execute');
              }
            } else {
              self.$_storeTaskNotice(self.Task.TaskID, 'execute');
            }
          } else {
            let htmlErrors = __.renderErrorApiHtml(responsesData.data);
            Swal.fire(
                    'Thông báo',
                    htmlErrors,
                    'error'
            );
            self.$store.commit('isLoading', false);
          }

        }, (error) => {
          console.log(error);
          Swal.fire(
                  'Thông báo',
                  'Không kết nối được với máy chủ',
                  'error'
          );
          self.$store.commit('isLoading', false);
        });
      }
    },
    watch: {},
    props: {
      value: {},
      title:{},
      name:{},
      api: {},
      Task: {},
      table: {},
      TaskStatus: {},
      addnew: false,
      keyArray: {},
      isDataflow: false,
      per: {},
      perGen: {},
      StyleIcon: {}
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
    margin-bottom: 0;
  }
  .align-items-center label{
    margin-bottom: 0px;
  }
  .ij-line{
    height: 1px;
    width: 100%;
    margin-left: 15px;
    margin-right: 15px;
    border-bottom: 1px solid  #e6e2e2;
    margin-top: 10px;
    margin-bottom: 10px;
  }
  .ij-data-label{
    font-weight: bold;
  }
</style>

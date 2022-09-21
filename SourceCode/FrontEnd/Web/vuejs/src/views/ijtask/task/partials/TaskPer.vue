<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
    <i class="fa fa-plus-square-o ij-icon ij-icon-plus" aria-hidden="true" v-if="addnew"></i>
    <i class="fa fa-edit ij-icon" aria-hidden="true" v-if="!addnew" style="margin-right: 6px;"></i>
    <b-modal ref="modal" id="modal-form-input-task-per" no-close-on-backdrop @hide="onHideModalDataflow($event)">
      <template slot="modal-title">
        <i class="fa fa-plus" v-if="addnew"></i><i class="fa fa-edit" v-if="!addnew"></i> {{this.title}}
      </template>
      <div class="form-group row align-items-center">
        <label class="col-md-2" style="white-space: nowrap">Công việc</label>
        <label class="col-md-20 ij-data-label" style="white-space: nowrap">{{Task.TaskName}}</label>
        <label class="col-md-2" style="white-space: nowrap">Ngày bắt đầu</label>
        <label class="col-md-4 ij-data-label" style="white-space: nowrap">{{Task.StartDate}}</label>
        <label class="col-md-2" style="white-space: nowrap">Hạn hoàn thành</label>
        <label class="col-md-4 ij-data-label" style="white-space: nowrap">{{Task.DueDate}}</label>
        <label class="col-md-2" style="white-space: nowrap">Thời lượng(Giờ)</label>
        <label class="col-md-4 ij-data-label" style="white-space: nowrap">{{Task.Duration}}</label>
        <label class="col-md-2" style="white-space: nowrap">Phương thức tính</label>
        <label class="col-md-4 ij-data-label" style="white-space: nowrap">{{Task.CalMethodName}}</label>
        <label class="col-md-2" style="white-space: nowrap">Khối lượng ƯTH</label>
        <label class="col-md-4 ij-data-label" style="white-space: nowrap">{{Task.EstimatedQuantity}}</label>
        <label class="col-md-2" style="white-space: nowrap">Đơn vị tính</label>
        <label class="col-md-4 ij-data-label" style="white-space: nowrap">{{Task.UomName}}</label>
        <div class="ij-line">
        </div>
      </div>
      <div class="form-group row align-items-center">
        <label class="col-md-2" style="white-space: nowrap">Ngày</label>
        <div class="col-md-8">
          <IjcoreDateTimePicker v-model="TaskExecution.TransDate"  :allowEmptyClear="true">
          </IjcoreDateTimePicker>
        </div>
        <label class="col-md-2" style="white-space: nowrap">Người thực hiện</label>
        <div class="col-md-8">
          <IjcoreModalListing v-model="TaskExecution" :title="'nhân viên'"  :api="'/listing/api/common/list'" :table="'employee'">
          </IjcoreModalListing>
        </div>
      </div>
      <div class="form-group row align-items-center">
        <label class="col-md-2" style="white-space: nowrap">Số giờ</label>
        <div class="col-md-8">
          <IjcoreNumber  v-model="TaskExecution.ActualHour" @changed="updateActualHour"></IjcoreNumber>
        </div>
        <label class="col-md-2" style="white-space: nowrap">Khối lượng</label>
        <div class="col-md-8">
          <IjcoreNumber  v-model="TaskExecution.ActualQuantity" :readonly="this.Task.isActualQuantity" @changed="updatePercent"></IjcoreNumber>
        </div>
      </div>
      <div class="form-group row align-items-center">
        <!--<label class="col-md-2" style="white-space: nowrap">% Hoàn thành</label>-->
        <!--<div class="col-md-8">-->
        <!--<IjcoreNumber  v-model="TaskExecution.PercentCompleted"></IjcoreNumber>-->
        <!--</div>-->

        <label class="col-md-2" style="white-space: nowrap" title="Khối lượng qui đổi">KLQĐ ({{Task.UomName}})</label>
        <div class="col-md-8">
          <IjcoreNumber  v-model="TaskExecution.ActualQuantity" :readonly="this.Task.isActualQuantity" @changed="updatePercent"></IjcoreNumber>
        </div>
      </div>
      <div class="form-group row">
        <label class="col-md-2" style="white-space: nowrap">Nội dung</label>
        <div class="col-md-20">
          <textarea v-model="TaskExecution.Description" class="form-control" rows="3" placeholder="Nhập nội dung"></textarea>
        </div>
      </div>
      <template v-slot:modal-footer>
        <div class="left">
          <!--<b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()" v-if="!isForm"-->
          <!--&gt;-->
          <!--Sửa-->
          <!--</b-button>-->
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
        <div class="right" style="margin-right: 0px;margin-left:auto;">
          Tổng khối lượng thực hiện: <b>{{Task.TotalActualQuantity}}</b> - % hoàn thành: <b>{{Task.PercentCompleted}}</b>
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
    name: 'TaskPer',
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
        if (this.TaskParent.StatusCompleted === 1) {
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
      undoValue(){
      },
      updateActualHour(){
        if(this.Task.CalMethod == 1){
          this.TaskExecution.ActualQuantity = this.TaskExecution.ActualHour;
          this.updatePercent();
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
        this.$refs['modal'].hide();
        this.isForm = false;
      },
      onHideModalDataflow(e){
      },
      onToggleModal(){
        this.$refs['modal'].show();
      },
      onResetModal(){
      },
      onEdit(){
        this.isForm = true;
      },
      onUpdate(){
        let self = this;
        let requestData = {
          method: 'post',
          url: 'task/api/task/task-execution/',
          data: {
            TaskExecution: TaskExecutionData
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
            this.$refs['modal'].hide();
            self.$store.commit('isLoading', false);
            if ($('.component-dataflow').length) {
              self.$_storeTaskDataflowNotice(self.Task.TaskID, 'taskPer');
            }else {
              self.$_storeTaskNotice(self.Task.TaskID, 'taskPer');
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
    watch: {
      'TaskExecution': {
        handler: function (after, before) {
          // Changes detected. Do work...
          // console.log(this.TaskExecution);
        },
        deep: true
      }
    },
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
      TaskParent: {},
      isDataflow: false
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
    margin-bottom: 0;
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

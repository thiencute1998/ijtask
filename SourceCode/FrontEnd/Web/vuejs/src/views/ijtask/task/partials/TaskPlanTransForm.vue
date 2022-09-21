<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
    <i class="fa fa-plus-square-o ij-icon ij-icon-plus" aria-hidden="true" v-if="addnew"></i>
    <i class="fa fa-edit ij-icon" aria-hidden="true" v-if="!addnew" style="margin-right: -3px;"></i>
    <b-modal ref="modal" id="modal-form-input-task-evaluation" scrollable size="xl" no-close-on-backdrop>
      <template slot="modal-title">
        <i class="fa fa-plus" v-if="addnew"></i><i class="fa fa-edit" v-if="!addnew"></i> {{this.title}}
      </template>

      <div class="form-group row align-items-center">
        <label class="col-md-2" style="white-space: nowrap">Công việc</label>
        <label class="col-md-22 ij-data-label" style="white-space: nowrap">{{Task.TaskName}}</label>
      </div>
      <div class="form-group row align-items-center" id="period">
        <label class="col-md-4 m-0">Ngày lập</label>
        <div class="col-md-4 date-picker-div">
          <IjcoreDatePicker v-model="TaskPlanTrans.TransDate" @input="reUpdatePlan()" v-if="!this.TransDate">
          </IjcoreDatePicker>
          <span v-else>{{TaskPlanTrans.TransDate}}</span>
        </div>
        <label class="col-md-4 m-0 align-items-center" title="Ngày bắt đầu điều chỉnh">Ngày BĐ điều chỉnh</label>
        <div class="col-md-4 date-picker-div">
          <IjcoreDatePicker v-model="TaskPlanTrans.StartDate" @input="reUpdatePlan()">
          </IjcoreDatePicker>
        </div>
        <label class="col-md-4 m-0 align-items-center" title="Hạn hoàn thành điều chỉnh">Hạn HT điều chỉnh</label>
        <div class="col-md-4 date-picker-div">
          <IjcoreDatePicker v-model="TaskPlanTrans.DueDate" @input="reUpdatePlan()">
          </IjcoreDatePicker>
        </div>
      </div>
      <div class="form-group row" id="period">
        <label class="col-md-4 m-0">ĐVT</label>
        <label class="col-md-4 m-0">Chiếc</label>
        <label class="col-md-4 m-0">Khối lượng</label>
        <div class="col-md-2">
          <ijcore-number v-model="TaskPlanTrans.EstimatedQuantity">
          </ijcore-number>
        </div>
      </div>
      <div class="form-group row" id="period">
        <label class="col-md-4 m-0">Diễn giải</label>
        <div class="col-md-20">
          <b-form-textarea class="form-control" v-model="TaskPlanTrans.TransComment">

          </b-form-textarea>
        </div>
      </div>

      <div class="div-scroll-table">
        <table class="table b-table table-sm table-bordered el-first-modal">
          <thead>
          <tr class="text-center">
<!--            <div class="td-action-fix-left" id="HeadFixLeftPlan">-->
<!--              <th class="pr-3">Nhân viên</th>-->
<!--              <th class="pr-3">Trọng số</th>-->
<!--              <th class="pr-3">Khối lượng</th>-->
<!--            </div>-->
<!--            <th></th>-->
            <th class="employee" style="max-width: none !important;">Nhân viên</th>
            <th class="pr-3">Trọng số</th>
            <th class="pr-3">Khối lượng</th>
            <th class="pr-3" style="width: 69px;text-align: right" v-for="item in PeriodArray">{{item}}</th>

          </tr>
          </thead>
          <tbody>
              <tr v-for="(item, key) in TaskAssign">
<!--                <div class="td-action-fix-left">-->
<!--                  <td class="pr-3">Nhân viên1111111111111111111</td>-->
<!--                  <td class="pr-3">Trọng số</td>-->
<!--                  <td class="pr-3">Khối lượng</td>-->
<!--                </div>-->
<!--                <td></td>-->
                <td style="max-width: none !important;">{{item.EmployeeName}}</td>
                <td class="td-input-point">
                  <ijcore-number v-model="TaskPlanTransItem[item.EmployeeID].EstimatedQuantityRate" v-if="TaskPlanTransItem[item.EmployeeID]!=undefined"></ijcore-number>
                </td>
                <td class="td-input-point">
                  <ijcore-number v-model="TaskPlanTransItem[item.EmployeeID].EstimatedQuantity" v-if="TaskPlanTransItem[item.EmployeeID]!=undefined"></ijcore-number>
                </td>
                <td class="td-input-point" v-for="itemP in PeriodArray">
                  <ijcore-number v-model="TaskPlanTransSubItem[item.EmployeeID+'_'+itemP].EstimatedQuantity" v-if="TaskPlanTransSubItem[item.EmployeeID+'_'+itemP]!=undefined"></ijcore-number>
                </td>
              </tr>
          </tbody>
        </table>
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
        <div class="right" style="position: absolute; right: 8px;">
          <b-button variant="primary" size="md" class="float-left mr-2">
            <i class="fa fa-cog" aria-hidden="true"></i>
          </b-button>
        </div>
      </template>

    </b-modal>
  </a>
</template>

<script>
  import ApiService from '@/services/api.service';
  import {TokenService} from '@/services/storage.service';
  // import mixinLists from '@/mixins/lists';
  import IjcoreDateTimePicker from "../../../../components/IjcoreDateTimePicker";
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  import IjcoreNumber from "../../../../components/IjcoreNumber";
  import moment from "moment";
  import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
  import IjDatePicker from "../../../ijcore/components/IjDatePicker";
  export default {
    name: 'TaskPlanTransForm',
    // mixins: [mixinLists],
    components: {
      IjDatePicker,
      IjcoreDatePicker,
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
        TaskPlanTrans: {
          TransDate: moment().format('DD/MM/YYYY'),
          StartDate: moment().format('DD/MM/YYYY'),
          DueDate: moment().format('DD/MM/YYYY'),
          TransComment: ''
        },
        TaskPlanTransItem: {

        },
        TaskPlanTransSubItem: {

        },
        TotalPeriod: 0,
        PeriodStart: 0,
        PeriodDue: 0,
        PeriodArray: [],
        WidthHeadFixLeftPlan: 0,
        FromDateTemp: '',
        DueDateTemp: '',
      }
    },
    created() {

    },
    mounted() {

    },
    methods: {
      initPlan(){
        this.TaskPlanTrans.TransDate = moment().format("DD/MM/YYYY");
        this.TaskPlanTrans.StartDate = this.Task.StartDate;
        this.TaskPlanTrans.DueDate = this.Task.DueDate;
        this.StartDateTemp = this.TaskPlanTrans.StartDate
        this.DueDateTemp = this.TaskPlanTrans.DueDate
        this.TaskPlanTrans.EstimatedQuantity = this.Task.EstimatedQuantity;
        console.log(this.Task)
        const PeriodStart = new moment(this.TaskPlanTrans.StartDate, "DD/MM/YYYY");
        const PeriodDue = new moment(this.TaskPlanTrans.DueDate, "DD/MM/YYYY");
        const diffTime = Math.abs(PeriodDue - PeriodStart);
        this.TotalPeriod = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        this.PeriodArray = [];
        this.PeriodArray.push(this.TaskPlanTrans.StartDate);
        for(let i = 0; i < this.TotalPeriod; i++){
          this.PeriodArray.push(PeriodStart.add(1, 'days').format("DD/MM/YYYY"));
        }
        let numberEmployee = this.TaskAssign.length
        let avgEmployeeEstimatedQuantity = 0;
        let EmployeeEstimatedQuantityFirst = 0;
        let avgEmployeeEstimatedQuantityRate = 0;
        let EmployeeEstimatedQuantityRateFirst = 0;
        if(numberEmployee != 0){
          avgEmployeeEstimatedQuantity = Math.floor(this.TaskPlanTrans.EstimatedQuantity/numberEmployee);
          EmployeeEstimatedQuantityFirst = this.TaskPlanTrans.EstimatedQuantity - avgEmployeeEstimatedQuantity*(numberEmployee - 1)
          avgEmployeeEstimatedQuantityRate = Math.floor(100/numberEmployee);
          EmployeeEstimatedQuantityRateFirst = 100 - avgEmployeeEstimatedQuantityRate*(numberEmployee - 1)
        }
        let self = this;
        _.forEach(self.TaskAssign, function (item, key) {
          let EstimatedQuantityTemp = 0;
          if(key == 0){
            EstimatedQuantityTemp = EmployeeEstimatedQuantityFirst;
            self.TaskPlanTransItem[item.EmployeeID] = {
              EmployeeID: item.EmployeeID,
              EmployeeName: item.EmployeeName,
              EstimatedQuantityRate: EmployeeEstimatedQuantityRateFirst,
              EstimatedQuantity: EmployeeEstimatedQuantityFirst
            }
          }else{
            EstimatedQuantityTemp = avgEmployeeEstimatedQuantity;
            self.TaskPlanTransItem[item.EmployeeID] = {
              EmployeeID: item.EmployeeID,
              EmployeeName: item.EmployeeName,
              EstimatedQuantityRate: avgEmployeeEstimatedQuantityRate,
              EstimatedQuantity: avgEmployeeEstimatedQuantity
            }
          }

          let avgPeriodEstimatedQuantity = 0;
          let avgPeriodEstimatedQuantityFirst = 0;
          if(self.TotalPeriod != 0){
            avgPeriodEstimatedQuantity = Math.floor(EstimatedQuantityTemp/(self.TotalPeriod + 1));
            avgPeriodEstimatedQuantityFirst = EstimatedQuantityTemp - avgPeriodEstimatedQuantity*(self.TotalPeriod)
          }else{
            avgPeriodEstimatedQuantityFirst = EstimatedQuantityTemp
          }

          let avgPeriodEstimatedQuantityTemp = 0;
          for(let i = 0; i <= self.TotalPeriod; i++){
            if(i == 0){
              avgPeriodEstimatedQuantityTemp = avgPeriodEstimatedQuantityFirst;
            }else{
              avgPeriodEstimatedQuantityTemp = avgPeriodEstimatedQuantity;
            }
            if(self.TaskPlanTransSubItem[item.EmployeeID+'_'+self.PeriodArray[i]] == undefined){
              self.TaskPlanTransSubItem[item.EmployeeID+'_'+self.PeriodArray[i]] = [];
            }
            self.TaskPlanTransSubItem[item.EmployeeID+'_'+self.PeriodArray[i]] = {
              EmployeeID: item.EmployeeID,
              EstimatedQuantity: avgPeriodEstimatedQuantityTemp,
              PeriodValue: self.PeriodArray[i],
              PeriodType: 1,
              FromDate: self.PeriodArray[i],
              ToDate: self.PeriodArray[i]
            }
          }
        });

        this.$forceUpdate();
      },
      reUpdatePlan(){
        const PeriodStart = new moment(this.TaskPlanTrans.StartDate, "DD/MM/YYYY");
        const PeriodDue = new moment(this.TaskPlanTrans.DueDate, "DD/MM/YYYY");
        const diffTime =PeriodDue - PeriodStart;
        if(diffTime < 0){
          this.$bvToast.toast('Ngày bắt đầu không được nhỏ hơn hạn hoàn thành!', {
            title: 'Thông báo',
            variant: 'success',
            solid: true
          });
          this.TotalPeriod = 0;
        }
        this.TotalPeriod = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        this.PeriodArray = [];
        this.TaskPlanTransItem = {};
        this.TaskPlanTransSubItem = {};
        if(this.TotalPeriod >= 0) {
          this.PeriodArray.push(this.TaskPlanTrans.StartDate);
          for (let i = 1; i <= this.TotalPeriod; i++) {
            this.PeriodArray.push(PeriodStart.add(1, 'days').format("DD/MM/YYYY"));
          }
          let numberEmployee = this.TaskAssign.length
          let avgEmployeeEstimatedQuantity = 0;
          let EmployeeEstimatedQuantityFirst = 0;
          let avgEmployeeEstimatedQuantityRate = 0;
          let EmployeeEstimatedQuantityRateFirst = 0;
          if (numberEmployee != 0) {
            avgEmployeeEstimatedQuantity = Math.floor(this.TaskPlanTrans.EstimatedQuantity / numberEmployee);
            EmployeeEstimatedQuantityFirst = this.TaskPlanTrans.EstimatedQuantity - avgEmployeeEstimatedQuantity * (numberEmployee - 1)
            avgEmployeeEstimatedQuantityRate = Math.floor(100 / numberEmployee);
            EmployeeEstimatedQuantityRateFirst = 100 - avgEmployeeEstimatedQuantityRate * (numberEmployee - 1)
          }
          let self = this;
          _.forEach(self.TaskAssign, function (item, key) {
            let EstimatedQuantityTemp = 0;
            if (key == 0) {
              EstimatedQuantityTemp = EmployeeEstimatedQuantityFirst;
              self.TaskPlanTransItem[item.EmployeeID] = {
                EmployeeID: item.EmployeeID,
                EmployeeName: item.EmployeeName,
                EstimatedQuantityRate: EmployeeEstimatedQuantityRateFirst,
                EstimatedQuantity: EmployeeEstimatedQuantityFirst
              }
            } else {
              EstimatedQuantityTemp = avgEmployeeEstimatedQuantity;
              self.TaskPlanTransItem[item.EmployeeID] = {
                EmployeeID: item.EmployeeID,
                EmployeeName: item.EmployeeName,
                EstimatedQuantityRate: avgEmployeeEstimatedQuantityRate,
                EstimatedQuantity: avgEmployeeEstimatedQuantity
              }
            }

            let avgPeriodEstimatedQuantity = 0;
            let avgPeriodEstimatedQuantityFirst = 0;
            if (self.TotalPeriod != 0) {
              avgPeriodEstimatedQuantity = Math.floor(EstimatedQuantityTemp / (self.TotalPeriod+1));
              avgPeriodEstimatedQuantityFirst = EstimatedQuantityTemp - avgPeriodEstimatedQuantity * (self.TotalPeriod)
            }else {
              avgPeriodEstimatedQuantityFirst = EstimatedQuantityTemp
            }
            let avgPeriodEstimatedQuantityTemp = 0;
            for (let i = 0; i <= self.TotalPeriod; i++) {
              if (i == 0) {
                avgPeriodEstimatedQuantityTemp = avgPeriodEstimatedQuantityFirst;
              } else {
                avgPeriodEstimatedQuantityTemp = avgPeriodEstimatedQuantity;
              }
              if (self.TaskPlanTransSubItem[item.EmployeeID + '_' + self.PeriodArray[i]] == undefined) {
                self.TaskPlanTransSubItem[item.EmployeeID + '_' + self.PeriodArray[i]] = [];
              }
              self.TaskPlanTransSubItem[item.EmployeeID + '_' + self.PeriodArray[i]] = {
                EmployeeID: item.EmployeeID,
                EstimatedQuantity: avgPeriodEstimatedQuantityTemp,
                PeriodValue: self.PeriodArray[i],
                PeriodType: 1,
                FromDate: self.PeriodArray[i],
                ToDate: self.PeriodArray[i]
              }
            }

          });
        }
        this.$forceUpdate();
      },
      loadPlan(){
        let self = this;
        this.$store.commit('isLoading', true);
        const requestData = {
          method: 'post',
          url: 'task/api/task/get-task-plan/' + this.Task.TaskID,
          data: {
            TransDate: self.TaskPlanTrans.TransDate,
          }
        };
        requestData.data.ItemID = this.Task.TaskID;
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;

          if (responsesData.status === 1) {
            if(responsesData.TaskPlanTrans){
              self.TaskPlanTrans.StartDate = __.convertDateToString(responsesData.TaskPlanTrans.StartDate);
              self.TaskPlanTrans.DueDate = __.convertDateToString(responsesData.TaskPlanTrans.DueDate);
              self.StartDateTemp = self.TaskPlanTrans.StartDate
              self.DueDateTemp = self.TaskPlanTrans.DueDate
              self.TaskPlanTrans.TransComment = responsesData.TaskPlanTrans.TransComment;
              self.TaskPlanTrans.EstimatedQuantity = responsesData.TaskPlanTrans.EstimatedQuantity;
            }
            self.TaskPlanTransItem = {};
            self.TaskPlanTransSubItem = {};

            _.forEach(responsesData.TaskPlanTransItem, function (item, key) {
              self.TaskPlanTransItem[item.EmployeeID] = {
                EmployeeID: item.EmployeeID,
                EmployeeName: item.EmployeeName,
                EstimatedQuantityRate: item.EstimatedQuantityRate,
                EstimatedQuantity: item.EstimatedQuantity
              }
            });
            const PeriodStart = new moment(this.TaskPlanTrans.StartDate, "DD/MM/YYYY");
            const PeriodDue = new moment(this.TaskPlanTrans.DueDate, "DD/MM/YYYY");
            const diffTime = Math.abs(PeriodDue - PeriodStart);
            this.TotalPeriod = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            this.PeriodArray = [];
            this.PeriodArray.push(this.TaskPlanTrans.StartDate);
            for(let i = 0; i < this.TotalPeriod; i++){
              this.PeriodArray.push(PeriodStart.add(1, 'days').format("DD/MM/YYYY"));
            }
            _.forEach(responsesData.TaskPlanTransSubItem, function (item, key) {
              if(self.TaskPlanTransSubItem[item.EmployeeID+'_'+item.PeriodValue] == undefined){
                self.TaskPlanTransSubItem[item.EmployeeID+'_'+item.PeriodValue] = [];
              }
              self.TaskPlanTransSubItem[item.EmployeeID+'_'+item.PeriodValue] = {
                EmployeeID: item.EmployeeID,
                EstimatedQuantity: item.EstimatedQuantity,
                PeriodValue: item.PeriodValue,
                PeriodType: 1,
                FromDate: moment(item.FromDate, "DD/MM/YYYY"),
                ToDate: moment(item.ToDate, "DD/MM/YYYY")
              }
            });
            self.$store.commit('isLoading', false);
          } else {
            let htmlErrors = __.renderErrorApiHtml(responsesData.data);
            Swal.fire(
              'Thông báo',
              htmlErrors,
              'error'
            )
            self.$store.commit('isLoading', false);
          }

        }, (error) => {
          console.log(error);
          Swal.fire(
            'Thông báo',
            'Không kết nối được với máy chủ',
            'error'
          )
          self.$store.commit('isLoading', false);
        });
      },
      onUpdate(){
        let self = this;
        let TotalEstimatedQuantity = 0;
        _.forEach(self.TaskPlanTransItem, function (item, key) {
          TotalEstimatedQuantity += item.EstimatedQuantity;
          let TotalEstimatedQuantityEmployee = 0;
          for(let i = 0; i <= self.TotalPeriod; i++){
            TotalEstimatedQuantityEmployee += self.TaskPlanTransSubItem[item.EmployeeID+'_'+self.PeriodArray[i]].EstimatedQuantity;
          }
          if(TotalEstimatedQuantityEmployee != item.EstimatedQuantity){
            self.$bvToast.toast('Khối lượng của tất cả nhân viên phải bằng tổng khối lượng!', {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });
          }

        });
        if(TotalEstimatedQuantity != self.TaskPlanTrans.EstimatedQuantity){
          self.$bvToast.toast('Khối lượng của tất cả nhân viên phải bằng tổng khối lượng!', {
            title: 'Thông báo',
            variant: 'success',
            solid: true
          });
          return false;
        }
        this.$store.commit('isLoading', true);
        const requestData = {
          method: 'post',
          url: 'task/api/task/task-plan/' + this.Task.TaskID,
          data: {
            TaskPlanTrans: self.TaskPlanTrans,
            TaskPlanTransItem: self.TaskPlanTransItem,
            TaskPlanTransSubItem: self.TaskPlanTransSubItem,
            PeriodArray: self.PeriodArray
          }
        };
        // edit user
        requestData.data.ItemID = this.Task.TaskID;
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            this.$bvToast.toast('Cập nhật thành công!', {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });
            let isEdit = 0;
            _.forEach(self.value, function (item, key) {
              if(__.convertDateToString(item.TransDate) == self.TaskPlanTrans.TransDate){
                self.value[key] = responsesData.data;
                self.$emit('changed', self.value)
                isEdit = 1;
              }
            });
            if(isEdit == 0){
              self.value.push(responsesData.data)
            }
            self.$refs['modal'].hide();
            self.$store.commit('isLoading', false);
          } else {
            let htmlErrors = __.renderErrorApiHtml(responsesData.data);
            Swal.fire(
              'Thông báo',
              htmlErrors,
              'error'
            )
            self.$store.commit('isLoading', false);
          }

        }, (error) => {
          console.log(error);
          Swal.fire(
            'Thông báo',
            'Không kết nối được với máy chủ',
            'error'
          )
          self.$store.commit('isLoading', false);
        });
      },
      onHideModal(){
        this.$refs['modal'].hide();
        this.isForm = false;
      },
      onToggleModal(){
        let self = this;
        if(this.TransDate){
          this.TaskPlanTrans.TransDate = __.convertDateToString(this.TransDate)
        }else{
        }
        let FlagType = 1;// FlagType = 1 mới, FlagType = 2 sửa
        _.forEach(this.value, function (item, key) {
          if(__.convertDateToString(item.TransDate) == self.TaskPlanTrans.TransDate){
            FlagType = 2
          }
        });
        if(FlagType == 2){
          this.loadPlan();
        }else{
          this.initPlan();
        }
        this.$refs['modal'].show();
        if (!this.$parent.showTaskPlan) this.$parent.showTaskPlan = true;
      },
    },
    filters: {

    },
    watch: {
    },
    props: {
      Task: {},
      value: {},
      per: {},
      perGen: {},
      addnew: {},
      title: {},
      TaskAssign: {},
      TransDate: {}
    },
  }
</script>
<style>
  .date-picker-div .mx-datepicker, .date-picker-div .mx-input-wrapper{
    width: auto !important;
  }
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
  #period .mx-input-wrapper {
    width: 110px !important;
  }
  .td-input-point{
    padding: 0px !important;
    text-align: right;
  }
  .td-input-point input{
    border-radius: 0px;
    border: none;
    text-align: right;
    padding-right: 5px;
    padding-left: 5px;
    background: rgba(236, 236, 236, 1);
  }
  .td-input-point select{
      border-radius: 0px;
      border: none;
      text-align: right;
      padding-right: 5px;
      padding-left: 5px;
    }

  .td-action-fix-left {
    position: absolute;
    top: auto;
    /*only relevant for first row*/
    background: #fff;
    border-bottom: none !important;
    /*compensate for top border*/
  }
  .td-action-fix-left:last-child {
    border-bottom: 1px solid #c8ced3 !important;
    height: 30px;
  }
  .div-scroll-table {
    width: 100%;
    overflow-x: scroll;
    margin-right: 5em;
    overflow-y: visible;
    padding: 0;
  }
</style>

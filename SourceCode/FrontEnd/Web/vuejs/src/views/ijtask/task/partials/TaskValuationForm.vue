<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
    <i class="fa fa-plus-square-o ij-icon ij-icon-plus" aria-hidden="true" v-if="addnew"></i>
    <i class="fa fa-edit ij-icon" aria-hidden="true" v-if="!addnew" style="margin-right: 6px;"></i>
    <b-modal ref="modal" id="modal-form-input-task-evaluation" scrollable size="xl" no-close-on-backdrop @hide="onHideModalDataflow($event)">
      <template slot="modal-title">
        <i class="fa fa-plus" v-if="addnew"></i><i class="fa fa-edit" v-if="!addnew"></i> {{this.title}}
      </template>

      <div class="form-group row align-items-center">
        <label class="col-md-4" style="white-space: nowrap">Công việc</label>
        <label class="col-md-22 ij-data-label" style="white-space: nowrap">{{Task.TaskName}}</label>
      </div>
      <div class="form-group row align-items-center" id="period">
        <label class="col-md-3 m-0">Nhân viên</label>
        <label class="col-md-4 m-0" v-if="EmployeeID">{{EmployeeName}}</label>
        <div class="col-md-4" v-else>
          <b-form-select v-model="EmployeeIDTemp" class="form-control" :options="EmployeeArr" @change="fetchData">
          </b-form-select>
        </div>

        <label class="col-md-2 m-0">Kỳ</label>
        <div class="col-md-4">
          <b-form-select v-model="FrequencyType"
                         @change="changeFrequencyType" :options="[
                                        {value: 6, text: 'Ngày'},
                                        {value: 5, text: 'Tuần'},
                                        {value: 4, text: 'Tháng'},
                                        {value: 3, text: 'Quý'},
                                        {value: 2, text: '6 tháng'},
                                        {value: 1, text: 'Năm'},
                                        {value: 7, text: 'Vụ việc'},]"
                         :settings="{multiple: true, allowClear: true, placeholder: {id: 0, text: 'Chọn tần suất'}}">
          </b-form-select>
        </div>
        <div class="col-md-4" v-if="FrequencyType==1">
          <b-form-select v-model="FrequencyYear"
                         :options="FrequencyYearOptions" @change="fetchData">
          </b-form-select>
        </div>
        <div class="col-md-4" v-if="FrequencyType==2">
          <b-form-select v-model="Frequency6Month" :options="[
                                        {value: 1, text: 'Đầu năm/'+FrequencyYear},
                                        {value: 2, text: 'Cuối năm/'+FrequencyYear},]" @change="fetchData">
          </b-form-select>
        </div>
        <div class="col-md-4" v-if="FrequencyType==3">
          <b-form-select v-model="FrequencyQuarter" :options="[
                                        {value: 1, text: 'Quý I/'+FrequencyYear},
                                        {value: 2, text: 'Quý II/'+FrequencyYear},
                                        {value: 3, text: 'Quý III/'+FrequencyYear},
                                        {value: 4, text: 'Quý IV/'+FrequencyYear},]" @change="fetchData">
          </b-form-select>
        </div>
        <div class="col-md-4" v-if="FrequencyType==4">
          <b-form-select v-model="FrequencyMonth" :options="[
                                        {value: 1, text: 'Tháng 01/'+FrequencyYear},
                                        {value: 2, text: 'Tháng 02/'+FrequencyYear},
                                        {value: 3, text: 'Tháng 03/'+FrequencyYear},
                                        {value: 4, text: 'Tháng 04/'+FrequencyYear},
                                        {value: 5, text: 'Tháng 05/'+FrequencyYear},
                                        {value: 6, text: 'Tháng 06/'+FrequencyYear},
                                        {value: 7, text: 'Tháng 07/'+FrequencyYear},
                                        {value: 8, text: 'Tháng 08/'+FrequencyYear},
                                        {value: 9, text: 'Tháng 09/'+FrequencyYear},
                                        {value: 10, text: 'Tháng 10/'+FrequencyYear},
                                        {value: 11, text: 'Tháng 11/'+FrequencyYear},
                                        {value: 12, text: 'Tháng 12/'+FrequencyYear},]" @change="fetchData">
          </b-form-select>
        </div>
        <div class="col-md-4" v-if="FrequencyType==5">
          <b-form-select v-model="FrequencyWeek"
                         :options="OptionWeekCurrentYear" @change="fetchData">
          </b-form-select>
        </div>
        <div class="col-md-4" v-if="FrequencyType==6">
          <IjcoreDatePicker  style="width: 100%;" v-model="TransDateTemp" @input="fetchData">
          </IjcoreDatePicker>
        </div>
      </div>
      <table class="table b-table table-sm table-bordered el-first-modal">
        <thead>
        <tr class="text-center">
          <th class="pr-3">Chỉ tiêu đánh giá</th>
          <th class="pr-3" style="width: 200px;">Loại chấm điểm</th>
          <th class="pr-3" style="width: 69px;">Điểm TB</th>
          <th class="pr-3" style="width: 69px;">Xếp loại TB</th>
          <th class="pr-3" style="width: 180px;">{{EmployeeLogin.EmployeeName}}</th>
          <th class="pr-3"  style="width: 180px;" v-for="(item, key) in TaskEvaluator1Job" v-if="item.EvaluatorID != EmployeeLogin.EmployeeID">{{item.EvaluatorName}}</th>
        </tr>
        </thead>
        <tbody>
            <tr v-for="(item, key) in TaskIndicator1Job">
              <td class="pr-3" :title="item.IndicatorName">{{item.IndicatorName}}</td>
              <td class="pr-3">{{item.ScaleRateName}}</td>
              <td class="pr-3 text-right">{{item.LevelInt100}}</td>
              <td class="pr-3">{{item.LevelResult}}</td>
              <td class="td-input-point">
                <b-form-select v-model="TaskEvaluation1JobArr[EmployeeIDTemp +'_'+ TransDateTempServer +'_'+ item.IndicatorID +'_'+ EmployeeLogin.EmployeeID].LevelInt" v-if="TaskEvaluation1JobArr[EmployeeIDTemp +'_'+ TransDateTempServer +'_'+ item.IndicatorID +'_'+ EmployeeLogin.EmployeeID] != undefined && item.IndicatorCalMethod != 2"
                   class="form-control" :options="ScaleRateItem[item.ScaleRateID]"
                   @change="updatePoint(EmployeeIDTemp +'_'+ TransDateTempServer +'_'+ item.IndicatorID +'_'+ EmployeeLogin.EmployeeID, item.ScaleRateID, key)">
                </b-form-select>
                <ijcore-number @changed="updatePointEstimatedQuantity(EmployeeIDTemp +'_'+ TransDateTempServer +'_'+ item.IndicatorID +'_'+ EmployeeLogin.EmployeeID, item.ScaleRateID, key, TaskEvaluation1JobArr[EmployeeIDTemp +'_'+ TransDateTempServer +'_'+ item.IndicatorID +'_'+ EmployeeLogin.EmployeeID].EstimatedQuantity)" v-model="TaskEvaluation1JobArr[EmployeeIDTemp +'_'+ TransDateTempServer +'_'+ item.IndicatorID +'_'+ EmployeeLogin.EmployeeID].EstimatedQuantity" v-if="TaskEvaluation1JobArr[EmployeeIDTemp +'_'+ TransDateTempServer +'_'+ item.IndicatorID +'_'+ EmployeeLogin.EmployeeID] != undefined && item.IndicatorCalMethod == 2"></ijcore-number>
              </td>
              <td v-for="(itemTor, keyTor) in TaskEvaluator1Job" v-if="itemTor.EvaluatorID!=EmployeeLogin.EmployeeID">
                {{TaskEvaluation1JobArr[EmployeeIDTemp +"_"+ TransDateTempServer +"_"+ item.IndicatorID +"_"+ itemTor.EvaluatorID]?TaskEvaluation1JobArr[EmployeeIDTemp +"_"+ TransDateTempServer +"_"+ item.IndicatorID +"_"+ itemTor.EvaluatorID].LevelChar:""}}{{TaskEvaluation1JobArr[EmployeeIDTemp +"_"+ TransDateTempServer +"_"+ item.IndicatorID +"_"+ itemTor.EvaluatorID]?"-"+TaskEvaluation1JobArr[EmployeeIDTemp +"_"+ TransDateTempServer +"_"+ item.IndicatorID +"_"+ itemTor.EvaluatorID].LevelText:""}}
              </td>
            </tr>
        </tbody>
      </table>
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
                  @click="onHideModal()">
            Đóng
          </b-button>
        </div>
        <div class="right" style="position: absolute; right: 8px;">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="fetchDataEvaluation()">
            <i class="fa fa-calculator"></i>
          </b-button>
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
  import IjcoreDateTimePicker from "../../../../components/IjcoreDateTimePicker";
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  import IjcoreNumber from "../../../../components/IjcoreNumber";
  import moment from "moment";
  import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
  export default {
    name: 'TaskValuationForm',
    components: {
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
        AllowWhenNotEvaluation: 1,
        EmployeeLogin: JSON.parse(localStorage.getItem('Employee')),
        isActualQuantity: false,
        CalMethodName: '',
        isForm: false,
        listtable: [
        ],
        tableName: '',
        search:'',
        lenghNo: 0,
        OldActualQuantity: '0',
        OldTotalActualQuantity: '0',
        model: {
          FromDate: '',
          ToDate: '',
          PeriodType: '',
          LevelInt: '',
          LevelResult: '',
          EvaluatorName: '',
          EvaluatorID: '',
        },
        EmployeeArr: [],
        IndicatorArr: [],
        TransDateArr: [],
        TransDateTemp: this.TransDate,
        TransDateTempServer: '',
        EmployeeIDTemp: this.EmployeeID,
        EmployeeNameTemp: this.EmployeeName,
        TaskEvaluation1JobFetch: [],
        Evaluated: false,
        KeyUserLogin: -1,
        TaskIndicator1Job: [],
        ScaleRateItem: [],
        FrequencyType: 1,
        CurrentDate: '',
        FrequencyYear: '',
        FrequencyYearOptions: {},
        Frequency6Month: '',
        FrequencyQuarter: '',
        FrequencyMonth: '',
        FrequencyWeek: {},
        FrequencyFromDate: '',
        FrequencyToDate: '',
        OptionWeekCurrentYear: {},
        TaskEvaluation1JobArr: {},
      }
    },
    created() {

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
      updateEvent(){
        if(WorkdateTemp != localStorage.getItem('workdate')){
          WorkdateTemp = localStorage.getItem('workdate');
          Workdate = WorkdateTemp.split("/");
          CurrentDateTemp = new Date(Workdate[2], Workdate[1] - 1, Workdate[0]);
          this.CurrentDate = CurrentDateTemp
          this.FrequencyYear = CurrentDateTemp.getFullYear()
          this.Frequency6Month = CurrentDateTemp.getMonth() + 1 > 6 ? 2 : 1
          this.FrequencyQuarter = Math.ceil((CurrentDateTemp.getMonth() + 1) / 3)
          this.FrequencyMonth = CurrentDateTemp.getMonth() + 1
          this.FrequencyFromDate = localStorage.getItem('workdate')
          this.FrequencyToDate = localStorage.getItem('workdate')
          this.FrequencyYearOptions = [];
          for(var i = this.FrequencyYear - 5; i<= this.FrequencyYear + 5; i++){
            let obj = {
              value: i,
              text: i
            }
            this.FrequencyYearOptions.push(obj)
          }
          this.getCurrentWeek()
          this.$forceUpdate()
        }
      },
      getCurrentWeek() {
        var start = moment(this.CurrentDate.getFullYear() + "-01-01", "YYYY-MM-DD");
        var end = moment(this.CurrentDate.getFullYear() + "-" + (this.CurrentDate.getMonth() + 1) + "-" + this.CurrentDate.getDate(), "YYYY-MM-DD");
        var end_year = moment(this.CurrentDate.getFullYear() + "-12-31", "YYYY-MM-DD");
        let NumberWeekInCurentYear = Math.ceil((end_year.diff(start, 'days') - end_year.day())/7) + 1;
        this.OptionWeekCurrentYear = [];
        let self = this;
        for (var i = 1; i <= NumberWeekInCurentYear; i++) {
          let txt = 'Tuần ' + i + '//'+self.FrequencyYear;
          if(i < 10){
            txt = 'Tuần 0' + i + '//'+self.FrequencyYear;
          }
          let obj = {
            value: i,
            text: txt
          }
          self.OptionWeekCurrentYear.push(obj)
        }
        self.FrequencyWeek = Math.ceil((end.diff(start, 'days') - end_year.day())/7) + 1;
      },
      changeFrequencyType() {
        this.FrequencyYear = this.CurrentDate.getFullYear()
        this.setTransDate()
        this.$forceUpdate()
        this.fetchData()
      },
      setTransDate(){
        let self = this;
        switch (self.FrequencyType) {
          case 1:
            //Đánh giá 1 năm
            self.TransDateTemp = '31/12/'+self.FrequencyYear;
            self.model.FromDate = moment(self.FrequencyYear + "-01-01", "YYYY-MM-DD");
            break;
          case 2:
            //Đánh giá 6 tháng
            if(self.Frequency6Month == 1){
              var start = moment(self.FrequencyYear + "-06-01", "YYYY-MM-DD");
              var end = moment(self.FrequencyYear + "-07-01", "YYYY-MM-DD");
              self.model.FromDate = start;
              var n = end.diff(start, 'days');
              self.TransDateTemp = n+'/06/'+self.FrequencyYear;
            }else{
              self.TransDateTemp = '31/12/'+self.FrequencyYear;
              self.model.FromDate = moment(self.FrequencyYear + "-07-01", "YYYY-MM-DD");
            }
            break;
          case 3:
            //Đánh giá quý
            switch (self.FrequencyQuarter) {
              case 1:
                var start = moment(self.FrequencyYear + "-01-01", "YYYY-MM-DD");
                var end = moment(self.FrequencyYear + "-04-01", "YYYY-MM-DD");
                var n = end.diff(start, 'days');
                self.TransDateTemp = n+'/03/'+self.FrequencyYear;
                self.model.FromDate = start;
                break;
              case 2:
                var start = moment(self.FrequencyYear + "-04-01", "YYYY-MM-DD");
                var end = moment(self.FrequencyYear + "-07-01", "YYYY-MM-DD");
                var n = end.diff(start, 'days');
                self.TransDateTemp = n+'/06/'+self.FrequencyYear;
                self.model.FromDate = start;
                break;
              case 3:
                var start = moment(self.FrequencyYear + "-07-01", "YYYY-MM-DD");
                var end = moment(self.FrequencyYear + "-10-01", "YYYY-MM-DD");
                var n = end.diff(start, 'days');
                self.TransDateTemp = n+'/09/'+self.FrequencyYear;
                self.model.FromDate = start;
                break;
              case 4:
                self.TransDateTemp = '31/12/'+self.FrequencyYear;
                var start = moment(self.FrequencyYear + "-07-01", "YYYY-MM-DD");
                var end = moment((self.FrequencyYear +1) + "-10-01", "YYYY-MM-DD");
                self.model.FromDate = start;
                break;
              default:
                break;
            }
            break;
          case 4:
            //Đánh giá tháng
            switch (self.FrequencyMonth) {
              case 1:
                var start = moment(self.FrequencyYear + "-01-01", "YYYY-MM-DD");
                var end = moment(self.FrequencyYear + "-02-01", "YYYY-MM-DD");
                var n = end.diff(start, 'days');
                self.TransDateTemp = n+'/01/'+self.FrequencyYear;
                self.model.FromDate = start;
                break;
              case 2:
                var start = moment(self.FrequencyYear + "-02-01", "YYYY-MM-DD");
                var end = moment(self.FrequencyYear + "-03-01", "YYYY-MM-DD");
                var n = end.diff(start, 'days');
                self.TransDateTemp = n+'/02/'+self.FrequencyYear;
                self.model.FromDate = start;
                break;
              case 3:
                var start = moment(self.FrequencyYear + "-03-01", "YYYY-MM-DD");
                var end = moment(self.FrequencyYear + "-04-01", "YYYY-MM-DD");
                var n = end.diff(start, 'days');
                self.TransDateTemp = n+'/03/'+self.FrequencyYear;
                self.model.FromDate = start;
                break;
              case 4:
                var start = moment(self.FrequencyYear + "-04-01", "YYYY-MM-DD");
                var end = moment(self.FrequencyYear + "-05-01", "YYYY-MM-DD");
                var n = end.diff(start, 'days');
                self.TransDateTemp = n+'/04/'+self.FrequencyYear;
                self.model.FromDate = start;
                break;
              case 5:
                var start = moment(self.FrequencyYear + "-05-01", "YYYY-MM-DD");
                var end = moment(self.FrequencyYear + "-06-01", "YYYY-MM-DD");
                var n = end.diff(start, 'days');
                self.TransDateTemp = n+'/05/'+self.FrequencyYear;
                self.model.FromDate = start;
                break;
              case 6:
                var start = moment(self.FrequencyYear + "-06-01", "YYYY-MM-DD");
                var end = moment(self.FrequencyYear + "-07-01", "YYYY-MM-DD");
                var n = end.diff(start, 'days');
                self.TransDateTemp = n+'/06/'+self.FrequencyYear;
                self.model.FromDate = start;
                break;
              case 7:
                var start = moment(self.FrequencyYear + "-07-01", "YYYY-MM-DD");
                var end = moment(self.FrequencyYear + "-08-01", "YYYY-MM-DD");
                var n = end.diff(start, 'days');
                self.TransDateTemp = n+'/07/'+self.FrequencyYear;
                self.model.FromDate = start;
                break;
              case 8:
                var start = moment(self.FrequencyYear + "-08-01", "YYYY-MM-DD");
                var end = moment(self.FrequencyYear + "-09-01", "YYYY-MM-DD");
                var n = end.diff(start, 'days');
                self.TransDateTemp = n+'/08/'+self.FrequencyYear;
                self.model.FromDate = start;
                break;
              case 9:
                var start = moment(self.FrequencyYear + "-09-01", "YYYY-MM-DD");
                var end = moment(self.FrequencyYear + "-10-01", "YYYY-MM-DD");
                var n = end.diff(start, 'days');
                self.TransDateTemp = n+'/09/'+self.FrequencyYear;
                self.model.FromDate = start;
                break;
              case 10:
                var start = moment(self.FrequencyYear + "-10-01", "YYYY-MM-DD");
                var end = moment(self.FrequencyYear + "-11-01", "YYYY-MM-DD");
                var n = end.diff(start, 'days');
                self.TransDateTemp = n+'/10/'+self.FrequencyYear;
                self.model.FromDate = start;
                break;
              case 11:
                var start = moment(self.FrequencyYear + "-11-01", "YYYY-MM-DD");
                var end = moment(self.FrequencyYear + "-12-01", "YYYY-MM-DD");
                var n = end.diff(start, 'days');
                self.TransDateTemp = n+'/11/'+self.FrequencyYear;
                self.model.FromDate = start;
                break;
              case 12:
                self.TransDateTemp = '31/12/'+self.FrequencyYear;
                self.model.FromDate = moment(self.FrequencyYear + "-12-01", "YYYY-MM-DD");
                break;
              default:
                break;
            }
            break;
          case 5:
            //Đánh giá theo tuần
            var start = moment(self.FrequencyYear + "-01-01", "YYYY-MM-DD");
            var numberDay = 8 - start.day() + (self.FrequencyWeek-1)*7;
            var startTemp = start;
            self.TransDateTemp = start.add('days', numberDay).format('DD/MM/YYYY');
            var ArrTransDateTemp = self.TransDateTemp.split("/");
            if(ArrTransDateTemp[2] != undefined && ArrTransDateTemp[2] > self.FrequencyYear){
              self.TransDateTemp = '31/12/'+self.FrequencyYear;
            }
            self.model.FromDate = start;
            break;
          case 6:
            //Đánh giá theo ngày
            self.TransDateTemp = localStorage.getItem('workdate')
            self.model.FromDate = localStorage.getItem('workdate');
            break;
          case 7:
            //Đánh giá vụ việc
            self.TransDateTemp = '31/12/'+self.FrequencyYear;
            break;
          default:
            break;
        }
        if(self.FrequencyType != 6){
          self.model.FromDate = moment(self.model.FromDate).format( 'DD/MM/YYYY')
        }
      },
      fetchDataEvaluation() {
        let self = this;
        let urlApi = 'task/api/task/fetch-evaluation-1job';
        let requestData = {
          method: 'post',
        };
        let data = {
          TaskID: self.Task.TaskID,
          EmployeeID: self.EmployeeIDTemp?self.EmployeeIDTemp:self.EmployeeID,
          TransDate: self.TransDateTemp?self.TransDateTemp:self.TransDate,
          IndicatorID: self.IndicatorID,
          FrequencyType: self.FrequencyType
        };
        if(self.FrequencyType == 1){
          data.FrequencyYear = self.FrequencyYear
        }

        switch (self.FrequencyType) {
          case 1:
            data.FrequencyYear = self.FrequencyYear
            break;
          case 2:
            data.FrequencyYear = self.FrequencyYear
            data.Frequency6Month = self.Frequency6Month
            break;
          case 3:
            data.FrequencyYear = self.FrequencyYear
            data.FrequencyQuarter = self.FrequencyQuarter
            break;
          case 4:
            data.FrequencyYear = self.FrequencyYear
            data.FrequencyMonth = self.FrequencyMonth
            break;
          case 5:
            data.FrequencyYear = self.FrequencyYear
            data.FrequencyWeek = self.FrequencyWeek
            break;
          case 6:
            data.FrequencyYear = self.FrequencyYear
            data.FrequencyFromDate = self.FrequencyFromDate
            data.FrequencyToDate = self.FrequencyToDate
            break;
          case 7:
            data.FrequencyYear = self.FrequencyYear
            data.Frequency6Month = self.Frequency6Month
            break;
          default:
            break;
        }
        requestData.data = data;
        requestData.url = urlApi;
        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          self.defaultModel = responsesData;
          if (responsesData.status === 1) {
            ///////////////////////////////////////////////////////
            self.TaskEvaluation1Job = responsesData.data.TaskEvaluation1Job;
            let TotalEstimatedQuantity = '0';
            let TotalPlanEstimatedQuantity = 0;
            if(responsesData.TotalEstimatedQuantity != undefined){
              TotalEstimatedQuantity = responsesData.TotalEstimatedQuantity
            }
            if(responsesData.TotalPlanEstimatedQuantity != undefined){
              TotalPlanEstimatedQuantity = responsesData.TotalPlanEstimatedQuantity
            }
            _.forEach(self.TaskEvaluation1Job, function (value, key) {
              if(TotalEstimatedQuantity !== '0'){
                self.TaskEvaluation1JobArr[value.EmployeeID +'_'+ self.TransDateTempServer +'_'+ value.IndicatorID +'_'+ self.EmployeeLogin.EmployeeID].EstimatedQuantity = TotalEstimatedQuantity;
              }else{
                self.TaskEvaluation1JobArr[value.EmployeeID +'_'+ self.TransDateTempServer +'_'+ value.IndicatorID +'_'+ self.EmployeeLogin.EmployeeID].EstimatedQuantity = value.EstimatedQuantity;
              }
              _.forEach(self.ScaleRateItem[value.ScaleRateID], function (val, k) {
                if(val.FromPoint100 <= value.LevelInt100 && val.ToPoint100 >= value.LevelInt100){
                  self.TaskEvaluation1JobArr[value.EmployeeID +'_'+ self.TransDateTempServer +'_'+ value.IndicatorID +'_'+ self.EmployeeLogin.EmployeeID].LevelInt = val.LevelInt;
                  self.TaskEvaluation1JobArr[value.EmployeeID +'_'+ self.TransDateTempServer +'_'+ value.IndicatorID +'_'+ self.EmployeeLogin.EmployeeID].LevelInt100 = value.LevelInt100;
                  self.TaskEvaluation1JobArr[value.EmployeeID +'_'+ self.TransDateTempServer +'_'+ value.IndicatorID +'_'+ self.EmployeeLogin.EmployeeID].LevelChar = val.LevelChar;
                  self.TaskEvaluation1JobArr[value.EmployeeID +'_'+ self.TransDateTempServer +'_'+ value.IndicatorID +'_'+ self.EmployeeLogin.EmployeeID].LevelText = val.LevelText;
                }
              });
            });
            if(self.TaskIndicator1Job){
              _.forEach(self.TaskIndicator1Job, function (value, key) {
                if(self.TaskEvaluation1JobArr[self.EmployeeIDTemp +'_'+ self.TransDateTempServer +'_'+ value.IndicatorID +'_'+ self.EmployeeLogin.EmployeeID] === undefined
                  && self.EmployeeIDTemp && self.TransDateTempServer && value.IndicatorID && self.EmployeeLogin.EmployeeID){
                  self.TaskEvaluation1JobArr[self.EmployeeIDTemp +'_'+ self.TransDateTempServer +'_'+ value.IndicatorID +'_'+ self.EmployeeLogin.EmployeeID] = {
                    EmployeeID: self.EmployeeIDTemp,
                    EmployeeName: self.EmployeeNameTemp,
                    EvaluatorID: self.EmployeeLogin.EmployeeID,
                    EvaluatorName: self.EmployeeLogin.EmployeeName,
                    IndicatorID: value.IndicatorID,
                    IndicatorName: value.IndicatorName,
                    IndicatorNo: value.IndicatorNo,
                    LevelInt: '',
                    LevelInt100: '',
                    EstimatedQuantity: 0,
                    LevelText: '',
                    LevelChar: '',
                    LevelResult: '',
                    ScaleRateID: value.ScaleRateID,
                    ScaleRateName: value.ScaleRateName,
                    TaskID: self.Task.TaskID,
                  };
                }
                //Tính trung bình thang điểm 100
                let TotalEstimatedQuantityP = 0;
                let Total100 = 0;
                let Count = 0;
                _.forEach(self.TaskEvaluation1JobArr, function (value1, key1) {
                  if(self.EmployeeIDTemp && self.TransDateTempServer && value.IndicatorID && self.EmployeeLogin.EmployeeID &&
                    ('_'+key1).includes('_'+self.EmployeeIDTemp+'_'+self.TransDateTempServer+'_'+self.TaskEvaluation1JobArr[self.EmployeeIDTemp +'_'+ self.TransDateTempServer +'_'+ value.IndicatorID +'_'+ self.EmployeeLogin.EmployeeID].IndicatorID+'_')){
                    if(value1.LevelInt100){
                      Total100 += value1.LevelInt100;
                      TotalEstimatedQuantityP += value1.EstimatedQuantity
                      Count ++;
                    }
                  }
                });
                if(Count>0){
                  if(value.IndicatorCalMethod == 2){
                    if(TotalPlanEstimatedQuantity > 0){
                      self.TaskIndicator1Job[key].LevelInt100 = Math.round((TotalEstimatedQuantityP/Count)*100/TotalPlanEstimatedQuantity);
                    }
                    self.TaskIndicator1Job[key].LevelInt100 = 100;
                  }else{
                    self.TaskIndicator1Job[key].LevelInt100 = Math.round(Total100/Count);
                  }
                  let maxpoint = 0;
                  let minpoint = 0;
                  let LevelResultMin = '';
                  let LevelIntMin = '';
                  let LevelTextMin = '';
                  let LevelCharMin = '';
                  let LevelInt100Min = '';
                  let LevelResultMax = '';
                  let LevelIntMax = '';
                  let LevelTextMax = '';
                  let LevelCharMax = '';
                  let LevelInt100Max = '';

                  _.forEach(self.ScaleRateItem[value.ScaleRateID], function (value2, key2) {
                    if(key2 == 0) {
                      maxpoint = value2.ToPoint100;
                      minpoint = value2.FromPoint100;
                      LevelResultMin = value2.LevelText;
                      LevelIntMin = value2.LevelInt;
                      LevelCharMin = value2.LevelChar;
                      LevelInt100Min = value2.LevelInt100;
                      LevelResultMax = value2.LevelText;
                      LevelIntMax = value2.LevelInt;
                      LevelCharMax = value2.LevelChar;
                      LevelInt100Max = value2.LevelInt100;
                    }

                    if(value2.ToPoint100 >= maxpoint){
                      maxpoint = value2.ToPoint100;
                      LevelResultMax = value2.LevelText;
                      LevelIntMax = value2.LevelInt;
                      LevelCharMax = value2.LevelChar;
                      LevelInt100Max = value2.LevelInt100;
                    }
                    if(value2.FromPoint100 <= minpoint){
                      minpoint = value2.FromPoint100;
                      LevelResultMin = value2.LevelText;
                      LevelIntMin = value2.LevelInt;
                      LevelCharMin = value2.LevelChar;
                      LevelInt100Min = value2.LevelInt100;
                    }

                    if(self.TaskIndicator1Job[key].LevelInt100 >= value2.FromPoint100 && self.TaskIndicator1Job[key].LevelInt100 <= value2.ToPoint100){
                      self.TaskIndicator1Job[key].LevelResult = value2.LevelText;
                    }
                  });

                  if(self.TaskIndicator1Job[key].LevelInt100 > maxpoint){
                    self.TaskIndicator1Job[key].LevelResult = LevelResultMax;
                    self.TaskIndicator1Job[key].LevelText = LevelTextMax;
                    self.TaskIndicator1Job[key].LevelInt = LevelIntMax;
                    self.TaskIndicator1Job[key].LevelChar = LevelCharMax;
                    self.TaskIndicator1Job[key].LevelInt100 = LevelInt100Max;
                  }

                  if(self.TaskIndicator1Job[key].LevelInt100 < minpoint){
                    self.TaskIndicator1Job[key].LevelResult = LevelResultMin;
                    self.TaskIndicator1Job[key].LevelText = LevelTextMin;
                    self.TaskIndicator1Job[key].LevelInt = LevelIntMin;
                    self.TaskIndicator1Job[key].LevelChar = LevelCharMin;
                    self.TaskIndicator1Job[key].LevelInt100 = LevelInt100Min;
                  }

                }else{
                  self.TaskIndicator1Job[key].LevelInt100 = '';
                  self.TaskIndicator1Job[key].LevelResult = '';
                }
              });
            }
            self.$forceUpdate()
          } else {
          }

          self.$store.commit('isLoading', false);
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
      updatePoint(Index, ScaleRateID, KeyF){
        let self = this;
        let ScaleRate;
        _.forEach(this.ScaleRateItem[ScaleRateID], function (value, key) {
          if(value.LevelInt == self.TaskEvaluation1JobArr[Index].LevelInt){
            self.TaskEvaluation1JobArr[Index].LevelInt100 = value.LevelInt100;
            self.TaskEvaluation1JobArr[Index].LevelChar = value.LevelChar;
            self.TaskEvaluation1JobArr[Index].LevelResult = value.LevelResult;
            self.TaskEvaluation1JobArr[Index].LevelText = value.LevelText;
            ScaleRate = value;
          }
        });
        //Tính trung bình thang điểm 100
        let Total100 = 0;
        let Count = 0;
        _.forEach(this.TaskEvaluation1JobArr, function (value, key) {
            if(key.includes('_'+self.TransDateTempServer+'_'+self.TaskEvaluation1JobArr[Index].IndicatorID+'_')){
              if(value.LevelInt100){
                Total100 += value.LevelInt100;
                Count ++;
              }
            }
        });
        self.TaskIndicator1Job[KeyF].LevelInt100 = Total100/Count;
        _.forEach(this.ScaleRateItem[ScaleRateID], function (value, key) {
          if(self.TaskIndicator1Job[KeyF].LevelInt100 >= value.FromPoint100 && self.TaskIndicator1Job[KeyF].LevelInt100 <= value.ToPoint100){
            self.TaskIndicator1Job[KeyF].LevelResult = value.LevelText;
          }
        });
      },
      updatePointEstimatedQuantity(Index, ScaleRateID, KeyF, VData){
        let self = this;
        let ScaleRate;
        _.forEach(this.ScaleRateItem[ScaleRateID], function (value, key) {
          if(value.LevelInt == self.TaskEvaluation1JobArr[Index].LevelInt){
            ScaleRate = value;
          }
        });
        //Tính trung bình thang điểm 100
        let TotalEstimatedQuantity = 0;
        let Total100 = 0;
        let Count = 0;
        _.forEach(this.TaskEvaluation1JobArr, function (value, key) {
          if(key.includes('_'+self.TransDateTempServer+'_'+self.TaskEvaluation1JobArr[Index].IndicatorID+'_')){
            if(value.EstimatedQuantity){
              TotalEstimatedQuantity += value.EstimatedQuantity;
              Count ++;
            }
          }
        });
        self.TaskIndicator1Job[KeyF].EstimatedQuantity = TotalEstimatedQuantity/Count;
        //Đổi khối lượng thực hiện ra %
        let requestData = {
          method: 'post',
        };
        let urlApi = 'task/api/task/get-plan-estimated-quantity';
        if(self.FrequencyType == 6){
          self.model.FromDate = self.TransDateTemp
        }
        let data = {
          TaskID: self.Task.TaskID,
          EmployeeID: self.EmployeeIDTemp?self.EmployeeIDTemp:self.EmployeeID,
          FromDate: self.model.FromDate,
          ToDate: self.TransDateTemp,
        };
        console.log(data)
        requestData.data = data;
        requestData.url = urlApi;
        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          self.defaultModel = responsesData;
          if (responsesData.status === 1) {
            if(responsesData.data){
              self.TaskIndicator1Job[KeyF].LevelInt100 = Math.floor(self.TaskIndicator1Job[KeyF].EstimatedQuantity*100/responsesData.data[0].Quantity);
              self.TaskEvaluation1JobArr[Index].LevelInt100 = Math.floor(VData/responsesData.data[0].Quantity);
              let maxpoint = 0;
              let minpoint = 0;
              let LevelResultMin = '';
              let LevelIntMin = '';
              let LevelTextMin = '';
              let LevelCharMin = '';
              let LevelInt100Min = '';
              let LevelResultMax = '';
              let LevelIntMax = '';
              let LevelTextMax = '';
              let LevelCharMax = '';
              let LevelInt100Max = '';

              let maxpointS = 0;
              let minpointS = 0;
              let LevelResultMinS = '';
              let LevelIntMinS = '';
              let LevelTextMinS = '';
              let LevelCharMinS = '';
              let LevelInt100MinS = '';
              let LevelResultMaxS = '';
              let LevelIntMaxS = '';
              let LevelTextMaxS = '';
              let LevelCharMaxS = '';
              let LevelInt100MaxS = '';
              _.forEach(this.ScaleRateItem[ScaleRateID], function (value, key) {
                if(key == 0){
                  maxpoint = value.ToPoint100;
                  minpoint = value.FromPoint100;
                  LevelResultMin = value.LevelText;
                  LevelIntMin = value.LevelInt;
                  LevelCharMin = value.LevelChar;
                  LevelInt100Min = value.LevelInt100;
                  LevelResultMax = value.LevelText;
                  LevelIntMax = value.LevelInt;
                  LevelCharMax = value.LevelChar;
                  LevelInt100Max = value.LevelInt100;

                  maxpointS = value.ToPoint100;
                  minpointS = value.FromPoint100;
                  LevelResultMinS = value.LevelText;
                  LevelIntMinS = value.LevelInt;
                  LevelCharMinS = value.LevelChar;
                  LevelInt100MinS = value.LevelInt100;
                  LevelResultMaxS = value.LevelText;
                  LevelIntMaxS = value.LevelInt;
                  LevelCharMaxS = value.LevelChar;
                  LevelInt100MaxS = value.LevelInt100;
                }
                if(value.ToPoint100 >= maxpoint){
                  maxpoint = value.ToPoint100;
                  LevelResultMax = value.LevelText;
                  LevelIntMax = value.LevelInt;
                  LevelCharMax = value.LevelChar;
                  LevelInt100Max = value.LevelInt100;
                }
                if(value.FromPoint100 <= minpoint){
                  minpoint = value.FromPoint100;
                  LevelResultMin = value.LevelText;
                  LevelIntMin = value.LevelInt;
                  LevelCharMin = value.LevelChar;
                  LevelInt100Min = value.LevelInt100;
                }

                if(value.ToPoint100 >= maxpointS){
                  maxpointS = value.ToPoint100;
                  LevelResultMaxS = value.LevelText;
                  LevelIntMaxS = value.LevelInt;
                  LevelCharMaxS = value.LevelChar;
                  LevelInt100MaxS = value.LevelInt100;
                }
                if(value.FromPoint100 <= minpoint){
                  minpoint = value.FromPoint100;
                  LevelResultMin = value.LevelText;
                  LevelIntMin = value.LevelInt;
                  LevelCharMin = value.LevelChar;
                  LevelInt100Min = value.LevelInt100;
                }
                if(value.FromPoint100 <= minpointS){
                  minpointS = value.FromPoint100;
                  LevelResultMinS = value.LevelText;
                  LevelIntMinS = value.LevelInt;
                  LevelCharMinS = value.LevelChar;
                  LevelInt100MinS = value.LevelInt100;
                }
                if(self.TaskIndicator1Job[KeyF].LevelInt100 >= value.FromPoint100 && self.TaskIndicator1Job[KeyF].LevelInt100 <= value.ToPoint100){
                  self.TaskIndicator1Job[KeyF].LevelResult = value.LevelText;
                  self.TaskIndicator1Job[KeyF].LevelText = value.LevelText;
                  self.TaskIndicator1Job[KeyF].LevelInt = value.LevelInt;
                  self.TaskIndicator1Job[KeyF].LevelChar = value.LevelChar;
                  self.TaskIndicator1Job[KeyF].LevelInt100 = value.LevelInt100;
                }
                if(self.TaskEvaluation1JobArr[Index].LevelInt100 >= value.FromPoint100 && self.TaskEvaluation1JobArr[Index].LevelInt100 <= value.ToPoint100){
                  self.TaskEvaluation1JobArr[Index].LevelResult = value.LevelText;
                  self.TaskEvaluation1JobArr[Index].LevelText = value.LevelText;
                  self.TaskEvaluation1JobArr[Index].LevelInt = value.LevelInt;
                  self.TaskEvaluation1JobArr[Index].LevelChar = value.LevelChar;
                  self.TaskEvaluation1JobArr[Index].LevelInt100 = value.LevelInt100;
                }
              });
              if(self.TaskIndicator1Job[KeyF].LevelInt100 > maxpoint){
                self.TaskIndicator1Job[KeyF].LevelResult = LevelResultMax;
                self.TaskIndicator1Job[KeyF].LevelText = LevelTextMax;
                self.TaskIndicator1Job[KeyF].LevelInt = LevelIntMax;
                self.TaskIndicator1Job[KeyF].LevelChar = LevelCharMax;
                self.TaskIndicator1Job[KeyF].LevelInt100 = LevelInt100Max;
              }

              if(self.TaskIndicator1Job[KeyF].LevelInt100 < minpoint){
                self.TaskIndicator1Job[KeyF].LevelResult = LevelResultMin;
                self.TaskIndicator1Job[KeyF].LevelText = LevelTextMin;
                self.TaskIndicator1Job[KeyF].LevelInt = LevelIntMin;
                self.TaskIndicator1Job[KeyF].LevelChar = LevelCharMin;
                self.TaskIndicator1Job[KeyF].LevelInt100 = LevelInt100Min;
              }

              if(self.TaskEvaluation1JobArr[Index].LevelInt100 > maxpointS){
                self.TaskEvaluation1JobArr[Index].LevelResult = LevelResultMaxS;
                self.TaskEvaluation1JobArr[Index].LevelText = LevelTextMaxS;
                self.TaskEvaluation1JobArr[Index].LevelInt = LevelIntMaxS;
                self.TaskEvaluation1JobArr[Index].LevelChar = LevelCharMaxS;
                self.TaskEvaluation1JobArr[Index].LevelInt100 = LevelInt100MaxS;
              }

              if(self.TaskEvaluation1JobArr[Index].LevelInt100 < minpointS){
                self.TaskEvaluation1JobArr[Index].LevelResult = LevelResultMinS;
                self.TaskEvaluation1JobArr[Index].LevelText = LevelTextMinS;
                self.TaskEvaluation1JobArr[Index].LevelInt = LevelIntMinS;
                self.TaskEvaluation1JobArr[Index].LevelChar = LevelCharMinS;
                self.TaskEvaluation1JobArr[Index].LevelInt100 = LevelInt100MinS;
              }
            }
          }



          self.$store.commit('isLoading', false);
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
      fetchData() {
        if(this.FrequencyType != 6){
          this.setTransDate()
        }
        if(this.TransDateTemp){
          let TransDateTempArr = this.TransDateTemp.split('/');
          if(TransDateTempArr[2] !== undefined){
            this.TransDateTempServer = TransDateTempArr[2]+'-'+TransDateTempArr[1]+'-'+TransDateTempArr[0];
          }else{
            this.TransDateTempServer = this.TransDateTemp
          }
        }
        let self = this;
        let urlApi = '';
        let requestData = {
          method: 'post',
        };
        urlApi = 'task/api/task/get-evaluation-1job';
        let data = {
          TaskID: self.Task.TaskID,
          EmployeeID: self.EmployeeIDTemp?self.EmployeeIDTemp:self.EmployeeID,
          TransDate: self.TransDateTemp?self.TransDateTemp:self.TransDate,
          IndicatorID: self.IndicatorID,
          FrequencyType: self.FrequencyType,
          AllowWhenNotEvaluation: self.AllowWhenNotEvaluation
        };
        requestData.data = data;
        requestData.url = urlApi;
        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          self.defaultModel = responsesData;
          if (responsesData.status === 1) {
            self.TaskEvaluation1JobFetch = responsesData.data.TaskEvaluation1Job;
            self.TaskEvaluation1JobArr = {};
            _.forEach(self.TaskEvaluation1JobFetch, function (item, key) {
              if(item.EvaluatorID) {
                self.TaskEvaluation1JobArr[item.EmployeeID + "_" + item.TransDate + "_" + item.IndicatorID + "_" + item.EvaluatorID] = item;
                self.TaskEvaluator1Job[item.EvaluatorID] = {
                  EvaluatorID: item.EvaluatorID,
                  EvaluatorName: item.EvaluatorName
                };
              }
            });
            self.TaskIndicator1Job = responsesData.data.TaskIndicator1Job;
            if(responsesData.data.ScaleRateItem){
              self.ScaleRateItem = []
              _.forEach(responsesData.data.ScaleRateItem, function (value, key) {
                if(self.ScaleRateItem[value.ScaleRateID] === undefined){
                  self.ScaleRateItem[value.ScaleRateID] = [];
                }
                let tmpObj = value;
                tmpObj.value = value.LevelInt;
                tmpObj.text = value.LevelChar +'-'+ value.LevelText;
                self.ScaleRateItem[value.ScaleRateID].push(tmpObj)
              });
            }
            if(self.TaskIndicator1Job){
              _.forEach(self.TaskIndicator1Job, function (value, key) {
                if(self.TaskEvaluation1JobArr[self.EmployeeIDTemp +'_'+ self.TransDateTempServer +'_'+ value.IndicatorID +'_'+ self.EmployeeLogin.EmployeeID] === undefined
                && self.EmployeeIDTemp && self.TransDateTempServer && value.IndicatorID && self.EmployeeLogin.EmployeeID){
                  self.TaskEvaluation1JobArr[self.EmployeeIDTemp +'_'+ self.TransDateTempServer +'_'+ value.IndicatorID +'_'+ self.EmployeeLogin.EmployeeID] = {
                    EmployeeID: self.EmployeeIDTemp,
                    EmployeeName: self.EmployeeNameTemp,
                    EvaluatorID: self.EmployeeLogin.EmployeeID,
                    EvaluatorName: self.EmployeeLogin.EmployeeName,
                    IndicatorID: value.IndicatorID,
                    IndicatorName: value.IndicatorName,
                    IndicatorNo: value.IndicatorNo,
                    LevelInt: '',
                    LevelInt100: '',
                    EstimatedQuantity: '',
                    LevelText: '',
                    LevelChar: '',
                    LevelResult: '',
                    ScaleRateID: value.ScaleRateID,
                    ScaleRateName: value.ScaleRateName,
                    TaskID: self.Task.TaskID,
                  };
                }
                //Tính trung bình thang điểm 100
                let Total100 = 0;
                let Count = 0;
                _.forEach(self.TaskEvaluation1JobArr, function (value1, key1) {
                  if(self.EmployeeIDTemp && self.TransDateTempServer && value.IndicatorID && self.EmployeeLogin.EmployeeID &&
                    ('_'+key1).includes('_'+self.EmployeeIDTemp+'_'+self.TransDateTempServer+'_'+self.TaskEvaluation1JobArr[self.EmployeeIDTemp +'_'+ self.TransDateTempServer +'_'+ value.IndicatorID +'_'+ self.EmployeeLogin.EmployeeID].IndicatorID+'_')){
                    if(value1.LevelInt100){
                      Total100 += value1.LevelInt100;
                      Count ++;
                    }
                  }
                });
                if(Count>0){
                  self.TaskIndicator1Job[key].LevelInt100 = Math.round(Total100/Count);
                  _.forEach(self.ScaleRateItem[value.ScaleRateID], function (value2, key2) {
                    if(self.TaskIndicator1Job[key].LevelInt100 >= value2.FromPoint100 && self.TaskIndicator1Job[key].LevelInt100 <= value2.ToPoint100){
                      self.TaskIndicator1Job[key].LevelResult = value2.LevelText;
                    }
                  });
                }else{
                  self.TaskIndicator1Job[key].LevelInt100 = '';
                  self.TaskIndicator1Job[key].LevelResult = '';
                }
              });
            }
            if(responsesData.data.EmployeeArr != undefined){
              self.EmployeeArr = [];
              _.forEach(responsesData.data.EmployeeArr, function (value, key) {
                let tmpObj = {};
                tmpObj.value = value.EmployeeID;
                tmpObj.text = value.EmployeeName;
                self.EmployeeArr.push(tmpObj);
              });
            }
            if(responsesData.data.TransDateArr != undefined){
              self.TransDateArr = [];
              _.forEach(responsesData.data.TransDateArr, function (value, key) {
                let tmpObj = {};
                tmpObj.value = value.TransDateID;
                tmpObj.text = value.TransDateName;
                self.TransDateArr.push(tmpObj);
              });
            }
          }
          self.$store.commit('isLoading', false);
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
      changePeriodType(){
        var CurrentDate = new Date();
        switch (this.model.PeriodType) {
          case 1:
            this.model.FromDate = '01/01/'+CurrentDate.getFullYear()
            this.model.ToDate = '31/12/'+CurrentDate.getFullYear()
            break;
          case 2:
            var CurrentMonth = CurrentDate.getMonth();
            if(CurrentMonth < 6){
              var d = new Date(CurrentDate.getFullYear()+'-07-01');
              d.setDate(d.getDate()-1);
              var TempToDate = moment(d).format('DD/MM/YYYY');
              this.model.FromDate = '01/01/'+CurrentDate.getFullYear()
              this.model.ToDate = TempToDate
            }else{
              this.model.FromDate = '01/07/'+CurrentDate.getFullYear()
              this.model.ToDate = '31/12/'+CurrentDate.getFullYear()
            }
            break;
          case 3:
            var CurrentMonth = CurrentDate.getMonth();
            var Quarter = Math.floor(CurrentMonth/3);
            var FromMonthQuarter = Quarter*3 + 1;
            var ToMonthQuarter = Quarter*3 + 3;
            if(ToMonthQuarter + 1 < 10){
              ToMonthQuarter = ToMonthQuarter + 1;
              ToMonthQuarter = '0'+ToMonthQuarter
            }else{
              ToMonthQuarter = ''+ToMonthQuarter;
            }
            if(FromMonthQuarter < 10){
              FromMonthQuarter = '0'+FromMonthQuarter
            }else{
              FromMonthQuarter = ''+FromMonthQuarter;
            }
            var d = new Date(CurrentDate.getFullYear()+'-'+ToMonthQuarter+'-01');
            d.setDate(d.getDate()-1);
            var TempToDate = moment(d).format('DD/MM/YYYY');
            this.model.FromDate = '01/'+FromMonthQuarter+'/'+CurrentDate.getFullYear()
            this.model.ToDate = TempToDate
            break;
          case 4:
            var CurrentMonth = CurrentDate.getMonth() + 1;
            var NextMonth = CurrentMonth+1;

            if(NextMonth < 10){
              NextMonth = '0'+NextMonth
            }else{
              NextMonth = ''+NextMonth;
            }
            if(CurrentMonth < 10){
              CurrentMonth = '0'+CurrentMonth
            }else{
              CurrentMonth = ''+CurrentMonth;
            }
            var d = new Date(CurrentDate.getFullYear()+'-'+NextMonth+'-01');
            d.setDate(d.getDate()-1);
            var TempToDate = moment(d).format('DD/MM/YYYY');
            this.model.FromDate = '01/'+CurrentMonth+'/'+CurrentDate.getFullYear()
            this.model.ToDate = TempToDate
            break;
          case 5:
            var d = new Date(CurrentDate.getFullYear()+'-01-01');
            var d1 = new Date(CurrentDate.getFullYear()+'-01-01');
            var dayOfWeekFirst = d.getDay();
            var NumberDayWeekFirst = 8 - dayOfWeekFirst;
            var NumberDays = Math.round((CurrentDate-d)/(1000*60*60*24)) + 1;
            var NumberWeek = Math.floor((NumberDays - NumberDayWeekFirst)/7);
            var NumberFromDate = d.getDate() + NumberWeek*7 + NumberDayWeekFirst;
            var NumberToDate = d.getDate() + NumberWeek*7 + NumberDayWeekFirst + 6;
            this.model.FromDate = moment(d.setDate(NumberFromDate)).format('DD/MM/YYYY')
            this.model.ToDate = moment(d1.setDate(NumberToDate)).format('DD/MM/YYYY')
            break;
          case 6:
            this.model.FromDate = moment(CurrentDate).format('DD/MM/YYYY')
            this.model.ToDate = moment(CurrentDate).format('DD/MM/YYYY')
            break;
          default:
            break;
        }
      },
      showButtonAddValuation(IndicatorID, EmployeeID){
        for (let i = 0; i < this.value.length; i++) {
          if(this.value[i].IndicatorID == IndicatorID && this.value[i].EmployeeID == EmployeeID){
            return false;
          }
        }
        return true;
      },
      addValuation(IndicatorID, Key){
        let EmployeeLogin = JSON.parse(localStorage.getItem('Employee'))
        let k = 0;
        for (let i = 0; i < this.value.length; i++) {
          if(this.value[i].IndicatorID == IndicatorID){
            k++;
          }
        }
        this.value.splice(Key + k, 0, {
          IndicatorName: '',
          IndicatorID: IndicatorID,
          EmployeeName: EmployeeLogin.EmployeeName,
          EmployeeID: EmployeeLogin.EmployeeID,
          EmployeeTitle: EmployeeLogin.Position,
          LevelInt: 0,
          LevelResult: '',
          TaskID: this.Task.TaskID
        });
      },
      removeValuation(Key){
          this.TaskEvaluation1JobFetch.splice(Key, 1);
      },
      perEditView(value, per, field) {
        var AccessField = ','+per['AccessField']+',';
        if(per['AccessField'] == 'all' || AccessField.includes(','+field+',')){
          return true;
        }else{
          return false;
        }
      },
      perEditField(value, per, field){
        var EditField = ','+per['EditField']+',';
        if(per['EditField'] == 'all' || EditField.includes(','+field+',')){
          return true;
        }else{
          return false;
        }
      },
      undoValue(){
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
      onHideModalDataflow(){
        if (this.isDataflow) {
          this.$emit('onHideModalTask');
        }
      },
      onToggleModal(){
        this.FrequencyType = this.FrequencyTypeTemp;
        this.CurrentDate = this.CurrentDateTemp;
        this.FrequencyYear = this.FrequencyYearTemp;
        this.FrequencyYearOptions = this.FrequencyYearOptionsTemp;
        this.Frequency6Month = this.Frequency6MonthTemp;
        this.FrequencyQuarter = this.FrequencyQuarterTemp;
        this.FrequencyMonth = this.FrequencyMonthTemp;
        this.FrequencyWeek = this.FrequencyWeekTemp;
        this.FrequencyFromDate = this.FrequencyFromDateTemp;
        this.FrequencyToDate = this.FrequencyToDateTemp;
        this.OptionWeekCurrentYear = this.OptionWeekCurrentYearTemp;
        this.fetchData();
        this.$refs['modal'].show();
        if (!this.$parent.showTaskValuation) this.$parent.showTaskValuation = true;
      },
      onResetModal(){
      },
      onEdit(){
        this.isForm = true;
      },
      updateStatusDescription(value, key){
        var result = this.TaskStatus.filter(obj => {
          return obj.value === value
        });
      },
      onUpdate() {
        let self = this;
        if (self.TransDateTemp && self.EmployeeIDTemp) {
          this.$store.commit('isLoading', true);
          const requestData = {
            method: 'post',
            url: 'task/api/task/update-evaluation/' + this.Task.TaskID,
            data: {
              TransDate: self.TransDateTemp,
              EmployeeID: self.EmployeeIDTemp,
              EmployeeName: self.EmployeeNameTemp,
              TaskEvaluation1JobArr: self.TaskEvaluation1JobArr,
              TaskIndicator1Job: self.TaskIndicator1Job,
              EvaluatorID: self.EmployeeLogin.EmployeeID,
              EvaluatorName: self.EmployeeLogin.EmployeeName,
              FrequencyType: self.FrequencyType
            }
          };
            requestData.data.TransDate = self.TransDateTemp;
          // edit user
          requestData.data.ItemID = this.Task.TaskID;
          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            let responsesData = responses.data;
            if (responsesData.status === 1) {
              _.forEach(self.TaskEvaluation1JobArr, function (val, key) {
                // if(val.EmployeeID +'_'+ val.TransDate +'_'+ val.EvaluatorID == self.EmployeeIDTemp +'_'+ self.TransDateTempServer +'_'+ self.EmployeeLogin.EmployeeID){
                let check = 0;
                let posEmployeeID = -1;
                let posTransDate = -1;
                let posIndicatorID = -1;
                let numberSameEmployeeID = 0;
                let numberSameTransDate = 0;
                let numberSameIndicatorID = 0;
                _.forEach(self.value, function (val1, key1) {
                  if(key == val1.EmployeeID +'_'+ val1.TransDate +'_'+ val1.IndicatorID +'_'+ val1.EvaluatorID){
                    self.value[key1] = self.TaskEvaluation1JobArr[key];
                    check = 1;
                  }else{
                    //*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*.*
                    if(val1.EmployeeID == val.EmployeeID && val1.TransDate == self.TransDateTempServer && val1.IndicatorID == val.IndicatorID){
                      numberSameIndicatorID++;
                    }else{
                      if(numberSameIndicatorID == 0){
                        posIndicatorID++;
                      }
                      if(val1.EmployeeID == val.EmployeeID && val1.TransDate == self.TransDateTempServer){
                        numberSameTransDate++;
                      }else{
                        if(numberSameTransDate == 0){
                          posTransDate++;
                        }
                        if(val1.EmployeeID == val.EmployeeID){
                          numberSameEmployeeID++;
                        }else{
                          if(numberSameEmployeeID == 0){
                            posEmployeeID++;
                          }
                        }
                      }
                    }
                  }
                });
                if(check == 0){
                  self.TaskEvaluation1JobArr[key].TransDate = self.TransDateTempServer;
                  self.TaskEvaluation1JobArr[key].showChildTransDate = false;
                  self.TaskEvaluation1JobArr[key].showChild = false;
                  if(numberSameIndicatorID > 0){
                    self.value.splice(numberSameIndicatorID + posIndicatorID, 0, self.TaskEvaluation1JobArr[key]);
                  }else{
                    if(numberSameTransDate > 0){
                      self.value.splice(numberSameTransDate + posTransDate + 1, 0, self.TaskEvaluation1JobArr[key]);
                    }else{
                      self.value.splice(numberSameEmployeeID + posEmployeeID +1, 0, self.TaskEvaluation1JobArr[key]);
                    }
                  }
                }
                // }
              });
              self.$emit('changed', self.FrequencyType, self.TransDateTemp, self.FrequencyWeek)
              self.$bvToast.toast('Cập nhật thành công!', {
                title: 'Thông báo',
                variant: 'success',
                solid: true
              });
              this.isForm = false;
              self.$store.commit('isLoading', false);
              self.$refs['modal'].hide();
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
        }else{
          Swal.fire(
            'Chú ý',
            'Bạn phải nhập đủ nhân viên, ngày đánh giá!',
            'error'
          )
        }
      }
    },
    filters: {
      addEvaluation1Job(){
        let EmployeeLogin = JSON.parse(localStorage.getItem('Employee'))
        this.Evaluation1Job.push({
          IndicatorName: '',
          IndicatorID: this.IndicatorID,
          EmployeeName: EmployeeLogin.EmployeeName,
          EmployeeID: EmployeeLogin.EmployeeID,
          EmployeeTitle: EmployeeLogin.Position,
          LevelInt: 0,
          LevelResult: '',
          TaskID: this.Task.TaskID
        });
      },
      convertDateToText(date){
        let dateArr = date.split("-");
        if(dateArr[2]){
          return dateArr[2]+'/'+dateArr[1]+'/'+dateArr[0]
        }else{
          return date;
        }
      }
    },
    watch: {
      EmployeeIDTemp(){
        let self = this;
        _.forEach(self.EmployeeArr, function (field, k) {
          if(field.value == self.EmployeeIDTemp){
            self.EmployeeNameTemp = field.text
          }
        });
      },
      TaskEvaluation1JobArr(){
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
      isDataflow: false,
      per: {},
      perGen: {},
      IndicatorID: {},
      IndicatorName: {},
      EmployeeID: {},
      EmployeeName: {},
      TransDate: {},
      TaskEvaluator1Job: {},
      CurrentDateTemp: {},
      FrequencyTypeTemp: {},
      FrequencyYearTemp: {},
      Frequency6MonthTemp: {},
      FrequencyQuarterTemp: {},
      FrequencyMonthTemp: {},
      FrequencyWeekTemp: {},
      FrequencyFromDateTemp: {},
      FrequencyToDateTemp: {},
      FrequencyYearOptionsTemp:{},
      OptionWeekCurrentYearTemp:{},
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
</style>

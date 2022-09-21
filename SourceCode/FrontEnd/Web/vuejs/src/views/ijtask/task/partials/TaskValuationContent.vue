<template ref="TaskEvaluationContent">
  <div class="table-responsive">
    <table class="not-border" v-if="!isForm">
      <thead>
      <tr class="text-left">
        <th class="pr-3" v-if="ViewPerEmployeeID">Nhân viên</th>
        <th class="pr-3" v-if="ViewPerEmployeeTitle">Loại bảng điểm</th>
        <th class="pr-3" v-if="ViewPerLevelInt">Điểm TB</th>
        <th class="pr-3" v-if="ViewPerLevelInt">Xếp loại TB</th>
        <th class="pr-3">{{EmployeeLogin.EmployeeName}}</th>
        <th class="pr-3" v-for="(item, key) in TaskEvaluator1Job" v-if="item.EvaluatorID != EmployeeLogin.EmployeeID">{{item.EvaluatorName}}</th>
      </tr>
      </thead>
      <tbody>
      <span v-bind:hidden="true">{{loopFor=-1}}</span>
      <span v-bind:hidden="true">{{loopForTransDate=-1}}</span>
      <span v-bind:hidden="true">{{loopForIndicator=-1}}</span>
      <span v-bind:hidden="true">{{loopFor=-1}}</span>
      <span v-bind:hidden="true">{{KeyloopFor=-1}}</span>
      <span v-bind:hidden="true">{{KeyloopForTransDate=-1}}</span>
      <span v-bind:hidden="true">{{ArrDate=[]}}</span>
      <template v-for="(item, key) in TaskEvaluation1Job">
        <template v-if ="loopFor==-1||loopFor!=item.EmployeeID">
          <span v-bind:hidden="true">{{loopFor=item.EmployeeID}}</span>
          <span v-bind:hidden="true">{{KeyloopFor=key}}</span>
          <span v-bind:hidden="true"></span>
          <tr class="bg-tree-tr" style="width: 100%;">
            <td class="bg-tree-td-parent pr-3">
              <div class="div-line-tree-left"></div>
              <i class="pl-2 pr-2 fa fa-plus-square" style="cursor: pointer;" @click="onToggleShowEmployee(key)" v-if="item.showChild == true"></i>
              <i class="pl-2 pr-2 fa fa-minus-square" style="cursor: pointer;" @click="onToggleShowEmployee(key)" v-else></i>
              <span>{{item.EmployeeName}}</span>
            </td>
            <td class="pr-3"></td>
            <td class="pr-3 text-right">{{ArrPointEmployee[item.EmployeeID]?Math.round(ArrPointEmployee[item.EmployeeID].Total/ArrPointEmployee[item.EmployeeID].Count):""}}</td>
            <td style="position: absolute; right: 35px;">
              <TaskValuationForm v-model="TaskEvaluation1Job" :title="'Đánh giá'" :EmployeeName="item.EmployeeName" @changed="updateFromForm"
                                 :EmployeeID="item.EmployeeID" :Task="Task" :FrequencyType="FrequencyType"
                                 :TaskEvaluator1Job="TaskEvaluator1Job"
                                 :CurrentDateTemp="CurrentDateTemp"
                                 :FrequencyTypeTemp="FrequencyTypeTemp"
                                 :FrequencyYearTemp="FrequencyYearTemp"
                                 :Frequency6MonthTemp="Frequency6MonthTemp"
                                 :FrequencyQuarterTemp="FrequencyQuarterTemp"
                                 :FrequencyMonthTemp="FrequencyMonthTemp"
                                 :FrequencyWeekTemp="FrequencyWeekTemp"
                                 :FrequencyFromDateTemp="FrequencyFromDateTemp"
                                 :FrequencyToDateTemp="FrequencyToDateTemp"
                                 :FrequencyYearOptionsTemp="FrequencyYearOptionsTemp"
                                 :OptionWeekCurrentYearTemp="OptionWeekCurrentYearTemp"
                                 :TaskStatus="TaskStatus" :addnew="true">
              </TaskValuationForm>
            </td>
          </tr>
        </template>
        <tr class="bg-tree-tr" style="" v-show="TaskEvaluation1Job[KeyloopFor].showChild == false" v-if="loopForTransDate==-1|| loopForTransDate != item.EmployeeID+'_'+item.TransDate">
          <span v-bind:hidden="true">{{loopForTransDate=item.EmployeeID+'_'+item.TransDate}}</span>
          <span v-bind:hidden="true">{{KeyloopForTransDate=key}}</span>
          <td class="bg-tree-td-parent bg-tree-td-1 pr-3" style="padding-left: 16px;">
            <div class="div-line-tree-left-1"></div>
            <i class="pl-2 pr-2 fa fa-plus-square" style="cursor: pointer;" @click="onToggleShowTransDate(key)" v-if="item.showChildTransDate == true"></i>
            <i class="pl-2 pr-2 fa fa-minus-square" style="cursor: pointer;" @click="onToggleShowTransDate(key)" v-else></i>
            <span>{{item.TransDate|convertDateToText}}</span>
          </td>
          <td class="pr-3"></td>
          <td class="pr-3 text-right">{{ArrPointDate[item.EmployeeID+'_'+item.TransDate]?ArrPointDate[item.EmployeeID+'_'+item.TransDate].Average:""}}</td>
          <td style="position: absolute; right: 29px;">
            <TaskValuationForm v-model="TaskEvaluation1Job" :title="'Đánh giá'" :TransDate="item.TransDate|convertDateToText" :EmployeeName="item.EmployeeName" @changed="updateFromForm"
                               :EmployeeID="item.EmployeeID" :Task="Task" :FrequencyType="FrequencyType"
                               :TaskEvaluator1Job="TaskEvaluator1Job"
                               :CurrentDateTemp="CurrentDateTemp"
                               :FrequencyTypeTemp="FrequencyTypeTemp"
                               :FrequencyYearTemp="FrequencyYearTemp"
                               :Frequency6MonthTemp="Frequency6MonthTemp"
                               :FrequencyQuarterTemp="FrequencyQuarterTemp"
                               :FrequencyMonthTemp="FrequencyMonthTemp"
                               :FrequencyWeekTemp="FrequencyWeekTemp"
                               :FrequencyFromDateTemp="FrequencyFromDateTemp"
                               :FrequencyToDateTemp="FrequencyToDateTemp"
                               :FrequencyYearOptionsTemp="FrequencyYearOptionsTemp"
                               :OptionWeekCurrentYearTemp="OptionWeekCurrentYearTemp"
                               :TaskStatus="TaskStatus" :addnew="false"
            >
            </TaskValuationForm>
          </td>
        </tr>

        <tr class="bg-tree-tr" style="" v-show="TaskEvaluation1Job[KeyloopFor].showChild == false && TaskEvaluation1Job[KeyloopForTransDate].showChildTransDate == false" v-if="loopForIndicator==-1|| loopForIndicator != item.EmployeeID+'_'+item.TransDate+'_'+item.IndicatorID">
          <span v-bind:hidden="true">{{loopForIndicator=item.EmployeeID+'_'+item.TransDate+'_'+item.IndicatorID}}</span>
          <td class="bg-tree-td-parent bg-tree-td-1 pr-3 bg-tree-td-child" style="padding-left: 47px;">
            <div class="div-line-tree-left-2"></div>
            <span>{{item.IndicatorName}}</span>
          </td>
          <td class="pr-3">{{item.ScaleRateName}}</td>
<!--          <span v-bind:hidden="true">{{result=getPointAverage("_"+item.TransDate +"_"+ item.IndicatorID+"_", item.ScaleRateID)}}</span>-->
          <td class="pr-3 text-right">{{item.LevelInt100P}}</td>
<!--          <td class="pr-3 text-right">{{ArrPointIndicator[item.EmployeeID+'_'+item.TransDate+'_'+item.IndicatorID]}}</td>-->
          <td class="pr-3">{{item.LevelResultP}}</td>
          <td class="pr-3" v-if='TaskEvaluation1JobArr[item.EmployeeID +"_"+ item.TransDate +"_"+ item.IndicatorID +"_"+ EmployeeLogin.EmployeeID]&&item.IndicatorCalMethod != 2'>{{TaskEvaluation1JobArr[item.EmployeeID +"_"+ item.TransDate +"_"+ item.IndicatorID +"_"+ EmployeeLogin.EmployeeID].LevelChar+'-'+TaskEvaluation1JobArr[item.EmployeeID +"_"+ item.TransDate +"_"+ item.IndicatorID +"_"+ EmployeeLogin.EmployeeID].LevelText}}</td>
          <td class="pr-3" v-else-if='TaskEvaluation1JobArr[item.EmployeeID +"_"+ item.TransDate +"_"+ item.IndicatorID +"_"+ EmployeeLogin.EmployeeID]&&item.IndicatorCalMethod == 2'>{{TaskEvaluation1JobArr[item.EmployeeID +"_"+ item.TransDate +"_"+ item.IndicatorID +"_"+ EmployeeLogin.EmployeeID].EstimatedQuantity|convertNumberToText}}</td>
          <td class="pr-3" v-else></td>
          <td class="pr-3" v-for="(itemTor, keyTor) in TaskEvaluator1Job" v-if="itemTor.EvaluatorID != EmployeeLogin.EmployeeID">{{TaskEvaluation1JobArr[item.EmployeeID +"_"+ item.TransDate +"_"+ item.IndicatorID +"_"+ itemTor.EvaluatorID]?TaskEvaluation1JobArr[item.EmployeeID +"_"+ item.TransDate +"_"+ item.IndicatorID +"_"+ itemTor.EvaluatorID].LevelChar:''}}{{TaskEvaluation1JobArr[item.EmployeeID +"_"+ item.TransDate +"_"+ item.IndicatorID +"_"+ itemTor.EvaluatorID]?"-"+TaskEvaluation1JobArr[item.EmployeeID +"_"+ item.TransDate +"_"+ item.IndicatorID +"_"+ itemTor.EvaluatorID].LevelText:''}}</td>
        </tr>
      </template>
      </tbody>
    </table>
  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';
  import moment from 'moment';
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  import IjcoreDateTimePicker from "../../../../components/IjcoreDateTimePicker";
  import IjcoreNumber from "../../../../components/IjcoreNumber";
  import TaskValuationForm from "./TaskValuationForm";
  import {TokenService} from "../../../../services/storage.service";

  export default {
    name: 'TaskValuationContent',
    mixins: [mixinLists],
    components: {
      TaskValuationForm,
      IjcoreNumber,
      IjcoreDateTimePicker,
      IjcoreModalListing
    },
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {
        listtable: [],
        tableName: '',
        search: '',
        lenghNo: 0,
        ViewPerEmployeeID: true,
        ViewPerEmployeeTitle: true,
        ViewPerLevelInt: true,
        ViewPerLevelResult: true,
        ArrPointEmployee: {},
        ArrPointDate: {},
        ArrPointIndicator: {},
        EvaluatorMe: {
        },
        EmployeeLogin: JSON.parse(localStorage.getItem('Employee')),
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
        TaskEvaluation1Job: {},
      }
    },
    created() {
    },
    mounted() {
      this.ViewPerEmployeeID = __.perViewColumn(this.per, 'EmployeeID')
      this.ViewPerEmployeeTitle = __.perViewColumn(this.per, 'EmployeeTitle')
      this.ViewPerLevelInt = __.perViewColumn(this.per, 'LevelInt')
      this.ViewPerLevelResult = __.perViewColumn(this.per, 'LevelResult')
      this.updatePoint(this.value)
    },
    methods: {
      updateFromForm($FrequencyType, $TransDateTemp, $FrequencyWeek){
        this.$emit('changed', $FrequencyType, $TransDateTemp, $FrequencyWeek)
        this.$forceUpdate();
      },
      updatePoint(TaskEvaluation1JobVar){
        let self = this;
        self.TaskEvaluation1Job = TaskEvaluation1JobVar;
        let TotalEmployee = 0;
        let TotalDate = 0;
        let TotalIndicator = 0;
        self.ArrPointIndicator = {};
        self.ArrPointDate = {};
        self.ArrPointEmployee = {};
        _.forEach(self.TaskEvaluation1Job, function (val, key) {
          if(self.ArrPointIndicator[val.EmployeeID+'_'+val.TransDate+'_'+val.IndicatorID] === undefined){
            let Obj = {Total: val.LevelInt100, Count : 1, ScaleRateID: val.ScaleRateID, EmployeeID: val.EmployeeID, TransDate: val.TransDate};
            self.ArrPointIndicator[val.EmployeeID+'_'+val.TransDate+'_'+val.IndicatorID] = Obj;
          }else{
            self.ArrPointIndicator[val.EmployeeID+'_'+val.TransDate+'_'+val.IndicatorID]["Total"] += val.LevelInt100;
            self.ArrPointIndicator[val.EmployeeID+'_'+val.TransDate+'_'+val.IndicatorID]["Count"]++;
          }
        });
        _.forEach(self.ArrPointIndicator, function (val, key) {
          if(self.ArrPointIndicator[key].Count>0){
            self.ArrPointIndicator[key].Average = Math.round(self.ArrPointIndicator[key].Total/self.ArrPointIndicator[key].Count);
            self.ArrPointIndicator[key].LevelResult = '';
            _.forEach(self.ScaleRateItem[self.ArrPointIndicator[key].ScaleRateID], function (value2, key2) {
              if(self.ArrPointIndicator[key].Average >= value2.FromPoint100 && self.ArrPointIndicator[key].Average <= value2.ToPoint100){
                self.ArrPointIndicator[key].LevelResult = value2.LevelText;
              }
            });
          }else{
            self.ArrPointIndicator[key].Average = '';
            self.ArrPointIndicator[key].LevelResult = '';
          }

          if(self.ArrPointDate[val.EmployeeID+'_'+val.TransDate] === undefined){
            let Obj = {Total: val.Average, Count : 1, ScaleRateID: val.ScaleRateID, EmployeeID: val.EmployeeID};
            self.ArrPointDate[val.EmployeeID+'_'+val.TransDate] = Obj;
          }else{
            self.ArrPointDate[val.EmployeeID+'_'+val.TransDate]["Total"] += val.Average;
            self.ArrPointDate[val.EmployeeID+'_'+val.TransDate]["Count"]++;
          }
        });
        _.forEach(self.ArrPointDate, function (val, key) {
          if(self.ArrPointDate[key].Count>0){
            self.ArrPointDate[key].Average = Math.round(self.ArrPointDate[key].Total/self.ArrPointDate[key].Count);
          }else{
            self.ArrPointDate[key].Average = '';
          }

          if(self.ArrPointEmployee[val.EmployeeID] === undefined){
            let Obj = {Total: val.Average, Count : 1};
            self.ArrPointEmployee[val.EmployeeID] = Obj;
          }else{
            self.ArrPointEmployee[val.EmployeeID]["Total"] += val.Average;
            self.ArrPointEmployee[val.EmployeeID]["Count"]++;
          }
        });
        this.$forceUpdate()
      },
      getPointAverage(KeyCheck, ScaleRateID){
        let self = this;
        //Tính trung bình thang điểm 100
        let Total100 = 0;
        let Count = 0;
        _.forEach(self.TaskEvaluation1JobArr, function (value1, key1) {
          if(key1.includes(KeyCheck)){
            if(value1.LevelInt100){
              Total100 += value1.LevelInt100;
              Count ++;
            }
          }
        });
        let tmpObj = {};
        if(Count>0){
          tmpObj.LevelInt100 = Math.round(Total100/Count);
          tmpObj.LevelResult = '';
          _.forEach(self.ScaleRateItem[ScaleRateID], function (value2, key2) {
            if(tmpObj.LevelInt100 >= value2.FromPoint100 && tmpObj.LevelInt100 <= value2.ToPoint100){
              tmpObj.LevelResult = value2.LevelText;
            }
          });
        }else{
          tmpObj.LevelInt100 = '';
          tmpObj.LevelResult = '';
        }
        return tmpObj;
      },
      onToggleShowEmployee(key){
        this.TaskEvaluation1Job[key].showChild = !this.TaskEvaluation1Job[key].showChild;
        this.$forceUpdate()
      },
      onToggleShowTransDate(key){
        this.TaskEvaluation1Job[key].showChildTransDate = !this.TaskEvaluation1Job[key].showChildTransDate;
        this.$forceUpdate()
      },
      fetchData() {

      },
      onSaveModal() {

      },
      onCancelModal(e) {
        this.onResetModal();
        e.preventDefault();
      },
      onCancelModal(e) {
        this.onResetModal();
        e.preventDefault();
      },
      onHideModal() {
        this.$refs['modal'].hide();
      },
      onToggleModal() {
        let self = this;
        this.currentPage = 1;
        this.$refs['modal'].show();
        this.fetchData();
      },
      onResetModal() {
      },
    },
    filters: {
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
      currentPage() {
        this.fetchData();
      },
    },
    props: {
      value: {},
      title: {},
      name: {},
      api: {},
      table: {},
      Task: {},
      isForm: false,
      TaskStatus: {},
      per: {},
      perGen: {},
      TaskEvaluation1JobArr: {},
      TaskEvaluator1Job: {},
      ScaleRateItem: {},
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
  .readonly {
    background-color: #fff !important;
  }

  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }

  .modal-footer ol, .modal-footer ul, .modal-footer dl {
    margin-bottom: 0;
  }

  .mx-datepicker {
    display: block !important;
  }

  .NameObject {
    width: 300px;
  }

  .DateTimeText {
    width: 173px;
  }

  .NumberHour {
    width: 80px;
  }

  .mx-input-wrapper {
    width: 173px !important;
  }

  .mx-datepicker {
    width: 173px !important;
  }
  .bg-tree-tr {
    border-left: dot-dot-dash;
    background-repeat: no-repeat;
    background-position: 5px;
    background-position-x: 0px;
  }
  .bg-tree-td {
    background-repeat: no-repeat;
  }
  .bg-tree-td-parent{
    position: relative;
  }
  .bg-tree-td-parent:before {
    display: inline-block;
    content: "";
    position: relative;
    top: -4px;
    left: 8px;
    width: 9px;
    height: 0;
    border-top: 1px dotted #858585;
    z-index: 1;
    padding-left: 5px;
  }
  .bg-tree-td-child:before {
    left: -8px;
    width: 10px;
  }
  .bg-tree-td-1 {
    background-position: 13px;
    background-repeat: no-repeat;
  }
  .bg-tree-td-2 {
    background-position: 18px;
    background-repeat: no-repeat;
  }
  .bg-tree-td-3 {
    background-position: 23px;
    background-repeat: no-repeat;
  }
  .line-tree{
    background-position: 23px;
    background-repeat: no-repeat;
    background-image: url(/img/treeview-default-line.gif);
  }
  .div-line-tree-left, .div-line-tree-left-1, .div-line-tree-left-2, .div-line-tree-left-3{
    position: absolute;
    left: 0px;
    height: 23px;
    top: 0px;
    background-image: url(/img/treeview-default-line.gif);
    background-position-y: -10px;
    background-repeat: repeat-x;
    background-repeat-y: none;
  }
  .div-line-tree-left{
    width: 14px;
  }
  .div-line-tree-left-1{
    width: 25px;
  }
  .div-line-tree-left-2{
    width: 40px;
  }
  .div-line-tree-left-3{
    width: 56px;
  }
</style>

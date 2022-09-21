<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="align-items-center mb-2">
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-name">
                            <span><i class="fa fa-rocket mr-2"></i> Phân bổ điều tiết thu ngân sách</span>
                        </div>
                    </b-col>
                    <b-col class="col-md-12"></b-col>
                </b-row>
                <b-row>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-actions">
                            <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="handleSubmitForm"><i class="fa fa-check-circle"></i> Tạo chứng từ</b-button>

                            <span v-if="model.TransID"><a :href="'/#/statebudgetplanning/'+CreateTransName+'/view/'+this.model.TransID" target="_blank"> Xem chi tiết chứng từ vừa tạo</a> </span>
                        </div>
                    </b-col>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-icons">
                            <div class="main-header-collapse ml-auto">
                                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen=true />
                            </div>
                        </div>
                    </b-col>
                </b-row>
            </div>
        </div>
        <div class="main-body main-body-view-action">
            <vue-perfect-scrollbar class="scroll-area" :settings="$store.state.psSettings">
                <div class="container-fluid">
                    <div role="tablist">
                        <b-card no-body class="mb-2">
                            <b-card-body>
                              <b-card-text>
                                  <div class="form-group row align-items-center">
                                    <label class="col-md-2 m-0">Kỳ</label>
                                    <div class="col-md-3">
                                      <b-form-select
                                        v-model="model.PeriodID" @change="changePeriodType"
                                        :options="PeriodOption">
                                      </b-form-select>
                                    </div>
                                    <div class="col-4" v-if="model.PeriodID !== 99">
                                      <b-form-select
                                        v-if="model.PeriodID !== 5 && model.PeriodID !== 99"
                                        v-model="model.PeriodValue" @change="changePeriodValue"
                                        :options="PeriodValueOption">
                                      </b-form-select>
                                      <ijcore-date-picker v-model="model.FromDate" style="width: 100%;" v-if="model.PeriodID === 5" @input-date-picker="changeFromDate"></ijcore-date-picker>
                                    </div>

                                    <label class="col-md-2 m-0" v-if="model.PeriodID === 99">Từ ngày</label>
                                    <div class="col-md-3" v-if="model.PeriodID === 99">
                                      <ijcore-date-picker v-model="model.FromDate" style="width: 100%;"></ijcore-date-picker>
                                    </div>
                                    <label class="col-md-2 m-0" v-if="model.PeriodID === 99">Đến ngày</label>
                                    <div class="col-md-3" v-if="model.PeriodID === 99">
                                      <ijcore-date-picker v-model="model.ToDate" style="width: 100%;"></ijcore-date-picker>
                                    </div>
                                    <label class="col-md-2 m-0">Loại HTTK</label>
                                    <div class="col-md-3">
                                      <b-form-select v-model="model.CoaTypeID" :options="model.Coatype"  @change="changeCoatype"></b-form-select>
                                    </div>
                                  </div>
                                  <div class="form-group row align-items-center">
                                    <label class="col-md-2 col-xl-2 m-0 text-nowrap" >Khoản thu</label>
                                    <div class="col-md-4 col-xl-4 mt-2 mt-xl-0">
                                      <IjcoreModalAccounting
                                        v-model="model" :title="'khoản thu'" :api="'/listing/api/common/list'"
                                        :table="'revenue'" :FieldID="'RevenueID'" :FieldName="'RevenueName'"
                                        :FieldUpdate="['NormID','NormNo','NormName']"
                                        :FieldNo="'RevenueNo'" :FieldType="1" :FieldWhere="{isRevenueRegulationRate : 1}" @changed="changerevenue">
                                      </IjcoreModalAccounting>
                                    </div>
                                    <label class="col-md-3 col-xl-3 m-0 text-nowrap" >Dự toán thu</label>
                                    <div class="col-md-4 col-xl-4 mt-2 mt-xl-0">
                                      <ijcore-number v-model="model.TotalPlanAmount"></ijcore-number>
                                    </div>
                                    <label class="col-md-2 col-xl-2 m-0 text-nowrap" >Phân bổ</label>
                                    <div class="col-md-4 col-xl-4 mt-2 mt-xl-0">
                                      <ijcore-number v-model="model.TotalAllocAmount"></ijcore-number>
                                    </div>
                                  </div>
                                  <div class="form-group row align-items-center">
                                    <label class="col-md-2 col-xl-2 m-0 text-nowrap" >Diễn giải</label>
                                    <div class="col-md-12 col-xl-12 mt-2 mt-xl-0">
                                      <textarea v-model="model.Comment" class="form-control" id="Comment" rows="3" placeholder="Diễn giải"></textarea>
                                    </div>
                                  </div>
                                  <div class="form-group row align-items-center">
                                    <label class="col-md-2 col-xl-2 m-0 text-nowrap" >Tiền tệ </label>
                                    <div class="col-md-4 col-xl-4 mt-2 mt-xl-0">
                                      <IjcoreModalListing
                                        v-model="model" :title="'tiền tệ'" :api="'/listing/api/common/list'"
                                        :table="'ccy'" :FieldID="'CcyID'" :FieldName="'CcyName'"
                                        :FieldNo="'CcyNo'" :FieldType="1">
                                      </IjcoreModalListing>
                                    </div>
                                    <label class="col-md-2 col-xl-2 m-0 text-nowrap" >Tỷ giá </label>
                                    <div class="col-md-4 col-xl-4 mt-2 mt-xl-0">
                                      <ijcore-number v-model="model.ExchangeRate" ></ijcore-number>
                                    </div>
                                  </div>
                              </b-card-text>
                              <div class="table-responsive b-table-sticky-header b-table-sticky-custom m-0" style="max-height: 350px;">
                                <table class="table b-table table-sm table-bordered table-editable table-tree">
                                  <thead>
                                  <tr>
                                    <th scope="col" style="min-width: 150px; border-left: none;" class="text-center">Khoản thu</th>
                                    <th scope="col" style="min-width: 50px; border-left: none;" class="text-center">Tỷ lệ(%)</th>
                                    <th scope="col" style="min-width: 100px; border-left: none;" class="text-center">Dự toán thu</th>
                                    <th scope="col" style="min-width: 100px; border-left: none;" class="text-center">Qui đổi</th>
                                    <th scope="col" style="min-width: 100px; border-left: none;" class="text-center">Phân bổ</th>
                                    <th scope="col" style="min-width: 100px; border-left: none;" class="text-center">Qui đổi</th>
                                    <th scope="col" style="max-width: 100px; border-left: none;" class="text-center">Tài khoản</th>
                                    <th scope="col" style="min-width: 100px; border-left: none;" class="text-center">ĐMDTCT</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <tr v-for="(field, key) in model.RevenueReguItem">
                                    <td><input type="text" v-model="model.RevenueReguItem[key].RevenueReguName" class="form-control" placeholder=""/></td>
                                    <td><ijcore-number v-model="model.RevenueReguItem[key].ReguRate"></ijcore-number></td>
                                    <td><ijcore-number v-model="model.RevenueReguItem[key].FCPlanAmount"></ijcore-number></td>
                                    <td><ijcore-number v-model="model.RevenueReguItem[key].LCPlanAmount"></ijcore-number></td>
                                    <td><ijcore-number v-model="model.RevenueReguItem[key].FCAllocAmount"></ijcore-number></td>
                                    <td><ijcore-number v-model="model.RevenueReguItem[key].LCAllocAmount"></ijcore-number></td>
                                    <td>
                                      <IjcoreModalAccounting
                                        v-model="model.RevenueReguItem[key]" :title="model.CoaTypeName" :api="'/listing/api/common/list'"
                                        :table="Table_CoaType" :FieldID="'AccountID'" :FieldName="'AccountName'" :FieldNo="'AccountNo'" :FieldType="1" >
                                      </IjcoreModalAccounting>
<!--                                      <b-form-select v-model="model.RevenueReguItem[key].AccountID" :options="model.CoatypeItem"></b-form-select>-->

                                    </td>
                                    <td>
                                      <IjcoreModalAccounting
                                        v-model="model.RevenueReguItem[key]" :title="'chỉ tiêu dự toán'" :api="'/listing/api/common/list'"
                                        :table="'norm_table_item'" :FieldID="'NormTableItemID'" :FieldName="'NormTableItemName'"
                                        :FieldUpdate="['NormID','NormNo','NormName','NormAllotID','NormAllotNo','NormAllotName','NormAllotILevelD','NormAllotLevelNo','NormAllotLevelName']"
                                        :FieldWhere="{Detail : 1,NormType :1,NormID : model.NormID}" :FieldNo="'NormTableItemNo'" :FieldType="1" >
                                      </IjcoreModalAccounting>
                                    </td>
                                  </tr>
                                  </tbody>
                                </table>
                              </div>
                              <div class="form-group row align-items-center" style="margin-top: 10px;">
                                <label class="col-md-2 col-xl-2 m-0 text-nowrap" >Tính</label>
                                <div class="col-md-1 col-xl-1 mt-2 mt-xl-0">
                                  <b-button variant="primary" size="md" class="float-left mr-2" style="padding: 2px 8px;" @click="updateDataCalculating()">
                                    <i class="fa fa-calculator"></i>
                                  </b-button>
                                </div>
                                <label class="col-md-3 col-xl-3 m-0 text-nowrap" >Tạo chứng từ </label>
                                <div class="col-md-4 col-xl-4 mt-2 mt-xl-0">
                                    <b-form-select  v-model="model.CreateTrans"
                                                    :options="[{value: 1, text: 'Lập dự toán'},
                                                              {value: 2, text: 'Xem xét dự toán'},
                                                              {value: 3, text: 'Duyệt dự toán'}
                                                   ]"
                                    ></b-form-select>
                                </div>
                              </div>
                            </b-card-body>
                        </b-card>

                    </div>
                </div>
            </vue-perfect-scrollbar>
        </div>
    </div>
</template>

<script>
    // import Multiselect from 'vue-multiselect';
    import ApiService from '@/services/api.service';
    import {TokenService} from '@/services/storage.service';
    import Swal from 'sweetalert2';
    import 'sweetalert2/src/sweetalert2.scss';
    import IjcoreModalListing from "../../../components/IjcoreModalListing";
    import IjcoreModalAccounting from "../../../components/IjcoreModalAccounting";
    import IjcoreNumber from "../../../components/IjcoreNumber";
    import moment from "moment";
    import IjcoreDatePicker from "../../../components/IjcoreDatePicker";
    import ColumnResizer from "column-resizer";

    const UpdateApi = '';
    const EditApi = '';
    const ListApi = '';
    const ListRouter = '';

    export default {
        name: 'FormUser',
        data () {
            return {
                model: {
                  TransDate: moment().format('L'),
                  PostDate: moment().format('L'),
                  eTransDate: moment().format('L'),
                  TransNo: '',
                  eTransNo: '',
                  TransTypeID: '2',
                  TransTypeName: '',
                  RevenueID: '',
                  RevenueNo: '',
                  RevenueName: '',
                  TotalAmount: '',
                  CreateTrans: 1,
                  PeriodID: 1,
                  PeriodType: 1,
                  StatusID: 1,
                  StatusValue: 3,
                  PeriodName: new Date().getFullYear(),
                  PeriodFromDate:  '01/01/'+new Date().getFullYear(),
                  PeriodToDate:  '31/12/'+new Date().getFullYear(),
                  PeriodValue: null,
                  PeriodValueName: '',
                  FromDate: null,
                  ToDate: null,
                  CoaTypeID: 1,
                  CoaTypeNo: '01',
                  CoaTypeName: 'TKHN',
                  Coatype: [],
                  CoatypeItem: [],
                  CcyID: 33,
                  CcyName: 'VND',
                  ExchangeRate: 1,
                  RevenueReguItem: [],
                  Comment: '',
                  TransID: '',
                  NormID: '',
                  NormNo: '',
                  NormName: '',
                },
                PeriodOption: [
                  {value: null, text: 'Chọn chu kỳ'},
                  {value: 1, text: 'Năm'},
                  {value: 2, text: 'Quý'},
                  {value: 3, text: 'Tháng'},
                  {value: 4, text: 'Tuần'},
                  {value: 5, text: 'Ngày'},
                  {value: 6, text: '6 tháng'},
                  {value: 7, text: '9 tháng'},
                  {value: 8, text: '3 năm'},
                  {value: 9, text: '5 năm'},
                  {value: 10, text: '10 năm'},
                  {value: 99, text: 'Tùy chọn'},
                ],
                PeriodValueOption: [],
                Table_CoaType: 'coa_con',
                CreateTransName: 'sbpmakeplan',
                SysAutoactTypeID: 116,
                stage: {
                    updatedData: false
                },
            }

        },
        computed: {},
        components: {
          IjcoreModalListing,
          IjcoreModalAccounting,
          IjcoreNumber,
          IjcoreDatePicker,
        },
        beforeCreate() {},
        created() {},
        mounted() {
            this.fetchData();
            this.changePeriodType();
        },
        updated() {
            this.stage.updatedData = true;
        },
        methods: {
            fetchData() {
              if (document.querySelector('.table-column-resizable')) {
                new ColumnResizer(
                  document.querySelector('.table-column-resizable'),{resizeMode: 'overflow'}
                );
              }
              let self = this;
              let urlApi = 'state-budget-planning/api/sbpreguplan/create';
              let requestData = {
                method: 'get',
                data: {}
              };

              requestData.url = urlApi;
              this.$store.commit('isLoading', true);

              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                if (responsesData.status === 1) {
                  self.model.ArrCoatype = responsesData.data.ArrCoatype;
                  self.model.TransNo = responsesData.data.auto;
                  self.model.eTransNo = Number(self.model.TransNo) + 1;

                  if (_.isArray(responsesData.data.ArrCoatype)) {
                    self.Coatype = [];
                    _.forEach(responsesData.data.ArrCoatype, function (value, key) {
                      let tmpObj = {};
                      tmpObj.value = value.CoaTypeID;
                      tmpObj.text = value.CoaTypeName;
                      self.model.Coatype.push(tmpObj);
                    });
                  }

                }
                self.$store.commit('isLoading', false);
              }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
              });
            },
            handleSubmitForm(){
                let self = this;
                    const requestData = {
                        method: 'post',
                        url: 'state-budget-planning/api/sbpreguplan/create-trans',
                        data: {
                          master: {
                            TransDate: this.model.PeriodToDate,
                            PostDate: this.model.PeriodToDate,
                            eTransDate: this.model.PeriodToDate,
                            TransNo: this.model.TransNo,
                            eTransNo: this.model.eTransNo,
                            TransTypeID: this.model.TransTypeID,
                            TransTypeName: this.model.TransTypeName,
                            CoaTypeID: this.model.CoaTypeID,
                            CoaTypeNo: this.model.CoaTypeNo,
                            CoaTypeName: this.model.CoaTypeName,
                            PeriodID: this.model.PeriodID,
                            PeriodType: this.model.PeriodType,
                            PeriodName: this.model.PeriodName,
                            PeriodFromDate: this.model.FromDate,
                            PeriodToDate: this.model.ToDate,
                            Comment: this.model.Comment,
                            CreateTrans: this.model.CreateTrans,
                            TotalAllocAmount: this.model.TotalAllocAmount,
                            RevenueID: this.model.RevenueID,
                            RevenueNo: this.model.RevenueNo,
                            RevenueName: this.model.RevenueName,
                            StatusID: this.model.StatusID,
                            StatusValue: this.model.StatusValue,
                          },
                          detail: this.model.RevenueReguItem
                        }
                    };

                    this.$store.commit('isLoading', true);
                    ApiService.setHeader();
                    ApiService.customRequest(requestData).then((responses) => {
                      let responsesData = responses.data; //console.log(responses.data);
                      this.model.TransID = responsesData.TransID;
                      this.model.CreateTrans = responsesData.CreateTrans;
                      if(this.model.CreateTrans==2){
                        this.CreateTransName = 'sbpreviewplan';
                      }else if(this.model.CreateTrans==3){this.CreateTransName = 'sbpapprovalplan';}else{this.CreateTransName = 'sbpmakeplan';}
                      if (responsesData.status === 1) {
                        if(responsesData.checkRevenue === 1){
                          self.$bvToast.toast('Bạn chưa chọn khoản thu!', {
                            title: 'Thông báo',
                            variant: 'success',
                            solid: true
                          });
                        }else{
                          self.$bvToast.toast('Chứng từ được tạo thành công!', {
                            title: 'Thông báo',
                            variant: 'success',
                            solid: true
                          });
                        }
                        this.setnullitem();
                        this.fetchData();
                        // self.$router.push({
                        //   name: ListApi,
                        //   params: {id: self.idParams, message: 'Chứng từ được tạo thành công!'}
                        // });
                      } else {
                        let htmlErrors = responsesData.data.msg;
                        self.$bvToast.toast(htmlErrors, {
                          title: 'Thông báo',
                          variant: 'warning',
                          solid: true
                        });
                      }
                      self.$store.commit('isLoading', false);
                    }, (error) => {
                        self.$store.commit('isLoading', false);
                        console.log(error);
                    });
            },
            setnullitem() {
              this.model.RevenueID = '';
              this.model.RevenueNo = '';
              this.model.RevenueName = '';
              this.model.TotalPlanAmount = '';
              this.model.TotalAllocAmount = '';
              this.model.Comment = '';
              this.model.RevenueReguItem = [];
            },
            onBackToList() {
                this.$router.push({path: ListRouter});
            },
            changerevenue(){
              let RevenueID = this.model.RevenueID;
              let RevenueNo = this.model.RevenueNo;
              let FromDate = this.model.FromDate;
              let self = this;
              let requestData = {
                method: 'post',
                url: '/state-budget-planning/api/sbpreguplan/get-list-regu',
                data: {
                  RevenueID: RevenueID,
                  RevenueNo: RevenueNo,
                  PeriodFromDate: FromDate,
                },

              };
              ApiService.customRequest(requestData).then((response) => {
                let responsesData = response.data;
                if (responsesData.status === 1) {
                  if (_.isArray(responsesData.data.RevenueRegu)) {
                    self.model.RevenueReguItem = [];
                      let tmpObj0 = {};
                      tmpObj0.RevenueID = self.model.RevenueID;
                      tmpObj0.RevenueNo = self.model.RevenueNo;
                      tmpObj0.RevenueName = self.model.RevenueName;
                      tmpObj0.RevenueReguName = self.model.RevenueName;
                      tmpObj0.ReguRate = 100;
                      tmpObj0.BudgetLevel = 0;
                      tmpObj0.PlanAmount = self.model.TotalPlanAmount;
                      tmpObj0.AllocAmount = self.model.TotalAllocAmount;
                      tmpObj0.Detail = 0;
                      self.model.RevenueReguItem.push(tmpObj0);
                    _.forEach(responsesData.data.RevenueRegu, function (item, key) {
                      let tmpObj = {};
                      tmpObj.RevenueReguName = item.RevenueReguName;
                      tmpObj.RevenueNo = item.RevenueNo;
                      tmpObj.BudgetLevel = item.BudgetLevel;
                      tmpObj.ReguRate = item.ReguRate;
                      tmpObj.Detail = 1;
                      self.model.RevenueReguItem.push(tmpObj);
                    });
                  }
                } else {
                  let htmlErrors = responsesData.data.msg;
                    self.$bvToast.toast(htmlErrors, {
                      title: 'Thông báo',
                      variant: 'warning',
                      solid: true
                  });
                }
                //console.log(self.model.RevenueReguItem);
                self.$store.commit('isLoading', false);
              }, (error) => {
                self.$store.commit('isLoading', false);
              });
            },
            updateDataCalculating(){
              let TotalPlanAmount = this.model.TotalPlanAmount;
              let TotalAllocAmount = this.model.TotalAllocAmount;
              let ExchangeRate = this.model.ExchangeRate;
              _.forEach(this.model.RevenueReguItem, function (item, key) {
                item.FCPlanAmount = (TotalPlanAmount * Number(item.ReguRate))/100;
                item.LCPlanAmount = ((TotalPlanAmount * Number(item.ReguRate))/100) * Number(ExchangeRate);
                item.FCAllocAmount = (TotalAllocAmount * Number(item.ReguRate))/100;
                item.LCAllocAmount = ((TotalAllocAmount * Number(item.ReguRate))/100) * Number(ExchangeRate);
              });
              this.$forceUpdate();
            },
            changeCreateTrans(){
              // let self = this;
              // let check_ct = this.model.CreateTrans;
              // if(check_ct==2){ this.SysAutoactTypeID = 117;}else if(check_ct==3){this.SysAutoactTypeID = 118;}else{this.SysAutoactTypeID = 116;}
              // self.model.CoatypeItem = [];
              // let ArrAutoact_1 = _.filter(this.model.ArrAutoact, ['SysAutoactTypeID', this.SysAutoactTypeID]);
              // _.forEach(ArrAutoact_1, function (item, key) {
              //   let tmpObj = {};
              //   tmpObj.value = item.AutoactID;
              //   tmpObj.text = item.AutoactName;
              //   self.model.CoatypeItem.push(tmpObj);
              // });
            },
            changeCoatype(){
              let check_ct = this.model.CoaTypeID;
              if(check_ct){
                switch(check_ct){
                  case 1:
                    this.model.CoaTypeID = 1;
                    this.model.CoaTypeNo = '01';
                    this.model.CoaTypeName = 'TKHN';
                    this.Table_CoaType = 'coa_con';
                    break;
                  case 2:
                    this.model.CoaTypeID = 2;
                    this.model.CoaTypeNo = '02';
                    this.model.CoaTypeName = 'TK Tabmis';
                    this.Table_CoaType = 'coa_tab';
                    break;
                  case 3:
                    this.model.CoaTypeID = 3;
                    this.model.CoaTypeNo = '03';
                    this.model.CoaTypeName = 'TKQG';
                    this.Table_CoaType = 'coa_sna';
                    break;
                  case 4:
                    this.model.CoaTypeID = 4;
                    this.model.CoaTypeNo = '04';
                    this.model.CoaTypeName = 'TK HCSN';
                    this.Table_CoaType = 'coa_anu';
                    break;
                  case 5:
                    this.model.CoaTypeID = 5;
                    this.model.CoaTypeNo = '05';
                    this.model.CoaTypeName = 'TK BQLDA';
                    this.Table_CoaType = 'coa_pmu';
                    break;
                  case 6:
                    this.model.CoaTypeID = 6;
                    this.model.CoaTypeNo = '06';
                    this.model.CoaTypeName = 'TK Xã phường';
                    this.Table_CoaType = 'coa_scb';
                    break;
                  default:
                    this.model.CoaTypeID = 1;
                    this.model.CoaTypeNo = '01';
                    this.model.CoaTypeName = 'TKHN';
                    this.Table_CoaType = 'coa_con';
                    break;
                }
              }
              this.$forceUpdate();
            },
            changePeriodType(){
              let self = this;
              let workDate = TokenService.getWorkdate();
              if (!workDate) {
                workDate = moment().format('L');
              }
              let momentWorkDate = moment(workDate, 'L');
              let yearWorkDate = momentWorkDate.get("year");
              let monthWorkDate = momentWorkDate.get('month');
              this.PeriodValueOption = [];
              switch (this.model.PeriodID) {
                case 1:
                  for (let i = 8; i >= 1; i--) {
                    let year = Number(yearWorkDate) - i;
                    let tmpObj = {};
                    tmpObj.value = year;
                    tmpObj.text = year;
                    tmpObj.fromDate = moment([year]).startOf("year").format('L');
                    tmpObj.toDate = moment([year]).endOf("year").format('L');
                    self.PeriodValueOption.push(tmpObj);
                  }
                  this.PeriodValueOption.push({
                    value: yearWorkDate,
                    text: yearWorkDate,
                    fromDate: moment([yearWorkDate]).startOf("year").format('L'),
                    toDate: moment([yearWorkDate]).endOf("year").format('L')
                  });
                  for (let i = 1; i <= 8; i++) {
                    let year = Number(yearWorkDate) + i;
                    let tmpObj = {};
                    tmpObj.value = Number(yearWorkDate);
                    tmpObj.text = year;
                    tmpObj.fromDate = moment([year]).startOf("year").format('L');
                    tmpObj.toDate = moment([year]).endOf("year").format('L');
                    self.PeriodValueOption.push(tmpObj);
                  }
                  this.model.PeriodValue = Number(yearWorkDate);
                  break;
                case 2:
                  this.PeriodValueOption.push({
                    value: 1,
                    text: 'Quý 1/' + yearWorkDate,
                    fromDate: moment([yearWorkDate, 0]).startOf("months").format('L'),
                    toDate: moment([yearWorkDate, 2]).endOf("months").format('L')
                  });
                  this.PeriodValueOption.push({
                    value: 2,
                    text: 'Quý 2/' + yearWorkDate,
                    fromDate: moment([yearWorkDate, 3]).startOf("months").format('L'),
                    toDate: moment([yearWorkDate, 5]).endOf("months").format('L')
                  });
                  this.PeriodValueOption.push({
                    value: 3,
                    text: 'Quý 3/' + yearWorkDate,
                    fromDate: moment([yearWorkDate, 6]).startOf("months").format('L'),
                    toDate: moment([yearWorkDate, 8]).endOf("months").format('L')
                  });
                  this.PeriodValueOption.push({
                    value: 4,
                    text: 'Quý 4/' + yearWorkDate,
                    fromDate: moment([yearWorkDate, 9]).startOf("months").format('L'),
                    toDate: moment([yearWorkDate, 11]).endOf("months").format('L')
                  });
                  this.model.PeriodValue = 1;
                  break;
                case 3:
                  for (let i = 1; i <= 12; i++) {
                    self.PeriodValueOption.push({
                      value: i,
                      text: 'Tháng ' + i + '/' + yearWorkDate,
                      fromDate: moment([yearWorkDate, i - 1]).startOf("months").format('L'),
                      toDate: moment([yearWorkDate, i - 1]).endOf("months").format('L')
                    });
                  }
                  this.model.PeriodValue = 1;
                  break;
                case 4:
                  for (let i = 1; i <= 52; i++) {
                    self.PeriodValueOption.push({
                      value: i,
                      text: 'Tuần ' + i + '/' + yearWorkDate,
                      fromDate: moment(workDate).week(i - 1).startOf('week').format('L'),
                      toDate: moment(workDate).week(i-1).endOf('week').format('L')
                    });
                  }
                  this.model.PeriodValue = 1;
                  break;
                case 5:
                  this.model.FromDate = workDate;
                  this.model.ToDate = this.model.FromDate;
                  break;
                case 6:
                  self.PeriodValueOption.push({
                    value: 1,
                    text: yearWorkDate + '/6th đầu',
                    fromDate: moment([yearWorkDate, 0]).startOf("months").format('L'),
                    toDate: moment([yearWorkDate, 5]).endOf("months").format('L')
                  });
                  self.PeriodValueOption.push({
                    value: 2,
                    text: yearWorkDate + '/6th cuối',
                    fromDate: moment([yearWorkDate, 6]).startOf("months").format('L'),
                    toDate: moment([yearWorkDate, 11]).endOf("months").format('L')
                  });
                  this.model.PeriodValue = 1;
                  // this.model.FromDate = workDate;
                  // this.model.ToDate = workDate;
                  break;
                case 7:
                  this.PeriodValueOption.push({
                    value: 1,
                    text: (Number(yearWorkDate) - 1) + '/9 tháng',
                    fromDate: moment([(Number(yearWorkDate) - 1), 0]).startOf("months").format('L'),
                    toDate: moment([(Number(yearWorkDate) - 1), 8]).endOf("months").format('L')
                  });
                  this.PeriodValueOption.push({
                    value: 2,
                    text: (Number(yearWorkDate)) + '/9 tháng',
                    fromDate: moment([yearWorkDate, 0]).startOf("months").format('L'),
                    toDate: moment([yearWorkDate, 8]).endOf("months").format('L')
                  });
                  this.PeriodValueOption.push({
                    value: 3,
                    text: (Number(yearWorkDate) + 1) + '/9 tháng',
                    fromDate: moment([(Number(yearWorkDate) + 1), 0]).startOf("months").format('L'),
                    toDate: moment([(Number(yearWorkDate) + 1), 8]).endOf("months").format('L')
                  });
                  this.model.PeriodValue = 2;
                  break;
                case 8:
                  this.PeriodValueOption.push({
                    value: 1,
                    text: (Number(yearWorkDate) - 3) + ' - ' + (Number(yearWorkDate) - 1),
                    fromDate: moment([Number(yearWorkDate) - 3]).startOf("year").format('L'),
                    toDate: moment([Number(yearWorkDate) - 1]).endOf("year").format('L')
                  });
                  this.PeriodValueOption.push({
                    value: 2,
                    text: (Number(yearWorkDate)) + ' - ' + (Number(yearWorkDate) + 2),
                    fromDate: moment([Number(yearWorkDate)]).startOf("year").format('L'),
                    toDate: moment([Number(yearWorkDate) + 1]).endOf("year").format('L')
                  });
                  this.PeriodValueOption.push({
                    value: 3,
                    text: (Number(yearWorkDate) + 3) + ' - ' + (Number(yearWorkDate) + 5),
                    fromDate: moment([Number(yearWorkDate) + 3]).startOf("year").format('L'),
                    toDate: moment([Number(yearWorkDate) + 5]).endOf("year").format('L')
                  });
                  this.model.PeriodValue = 2;
                  break;
                case 9:
                  this.PeriodValueOption.push({
                    value: 1,
                    text: (Number(yearWorkDate) - 5) + ' - ' + (Number(yearWorkDate) - 1),
                    fromDate: moment([Number(yearWorkDate) - 5]).startOf("year").format('L'),
                    toDate: moment([Number(yearWorkDate) - 1]).endOf("year").format('L')
                  });
                  this.PeriodValueOption.push({
                    value: 2,
                    text: (Number(yearWorkDate)) + ' - ' + (Number(yearWorkDate) + 4),
                    fromDate: moment([Number(yearWorkDate)]).startOf("year").format('L'),
                    toDate: moment([Number(yearWorkDate) + 4]).endOf("year").format('L')
                  });
                  this.PeriodValueOption.push({
                    value: 3,
                    text: (Number(yearWorkDate) + 5) + ' - ' + (Number(yearWorkDate) + 9),
                    fromDate: moment([Number(yearWorkDate) + 5]).startOf("year").format('L'),
                    toDate: moment([Number(yearWorkDate) + 9]).endOf("year").format('L')
                  });
                  this.model.PeriodValue = 2;
                  break;
                case 10:
                  this.PeriodValueOption.push({
                    value: 1,
                    text: (Number(yearWorkDate) - 10) + ' - ' + (Number(yearWorkDate) - 1),
                    fromDate: moment([Number(yearWorkDate) - 10]).startOf("year").format('L'),
                    toDate: moment([Number(yearWorkDate) - 1]).endOf("year").format('L')
                  });
                  this.PeriodValueOption.push({
                    value: 2,
                    text: (Number(yearWorkDate)) + ' - ' + (Number(yearWorkDate) + 9),
                    fromDate: moment([Number(yearWorkDate)]).startOf("year").format('L'),
                    toDate: moment([Number(yearWorkDate) + 9]).endOf("year").format('L')
                  });
                  this.PeriodValueOption.push({
                    value: 3,
                    text: (Number(yearWorkDate) + 10) + ' - ' + (Number(yearWorkDate) + 19),
                    fromDate: moment([Number(yearWorkDate) + 10]).startOf("year").format('L'),
                    toDate: moment([Number(yearWorkDate) + 19]).endOf("year").format('L')
                  });
                  this.model.PeriodValue = 2;
                  break;
                case 99:
                  this.model.FromDate = '';
                  this.model.ToDate = '';
                  break;
                default:
                  break;
              }
              this.changePeriodValue();
            },
            changePeriodValue(){
              let dateRange = _.find(this.PeriodValueOption, ['value', Number(this.model.PeriodValue)]);
              if (dateRange) {
                this.model.FromDate = dateRange.fromDate;
                this.model.ToDate = dateRange.toDate;
              }
            },
            changeFromDate() {
              this.model.FromDate = this.model.ToDate;
            },
        },
        watch: {

        }
    }
</script>
<style lang="css">
    .sysadmin-box {
        margin-bottom: 8px;
        margin-top: 8px;
    }
    .sysadmin-pane {
        margin-left: 30px;
        border-left: 1px solid #bbbbbb;
        padding-left: 10px;
    }
</style>

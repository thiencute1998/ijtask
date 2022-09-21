<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Phiếu ĐGCV: {{model.TransName}}</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onCreateClicked"><i
                class="fa fa-plus"></i> Thêm
              </b-button>
              <EvaluationTransPer :TransID="model.TransID"></EvaluationTransPer>
              <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="onEditClicked"><i
                class="fa fa-edit"></i> Sửa
              </b-button>
              <b-dropdown variant="primary" id="dropdown-actions" class="main-header-action mr-2" text="Thao tác">
                <b-dropdown-item @click="handleDeleteItem">Xóa</b-dropdown-item>
                <b-dropdown-item @click="handleCopyItem">Nhân bản</b-dropdown-item>
                <b-dropdown-item>Chia sẻ</b-dropdown-item>
                <b-dropdown-item>Chat</b-dropdown-item>
                <b-dropdown-item>Zalo</b-dropdown-item>
                <b-dropdown-item>Phân quyền</b-dropdown-item>
              </b-dropdown>
            </div>
          </b-col>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-icons">
              <div class="main-header-item-counter ml-auto mr-3" v-if="reqParams">
                <span>{{itemNo}} - {{reqParams.idsArray.length + this.reqParams.perPage * (this.reqParams.currentPage - 1)}}</span>
                /
                <span>{{reqParams.total}}</span>
              </div>
              <b-button-group id="main-header-views" class="main-header-views">
                <b-button id="tooltip-view-prev" @click="onNavigationItem('prev')" v-if="reqParams"
                          class="main-header-view"><i class="fa fa-angle-left"></i></b-button>
                <b-button id="tooltip-view-next" @click="onNavigationItem('next')" v-if="reqParams"
                          class="main-header-view"><i class="fa fa-angle-right"></i></b-button>
                <b-button id="tooltip-view-back-to-list" @click="onBackToList" class="main-header-view"><i
                  class="fa fa-list"></i></b-button>
                <b-tooltip container="main-header-views" target="tooltip-view-back-to-list">Danh sách</b-tooltip>
                <b-tooltip container="main-header-views" target="tooltip-view-prev">Trước</b-tooltip>
                <b-tooltip container="main-header-views" target="tooltip-view-next">Sau</b-tooltip>
              </b-button-group>
              <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen="true"/>
              </div>

            </div>
          </b-col>
        </b-row>
      </div>

    </div>
    <div class="main-body main-body-view-action">
      <vue-perfect-scrollbar class="scroll-area" :settings="$store.state.psSettings">
        <div class="container-fluid">
          <b-card>
            <div class="form-body">
              <div class="form-group row align-items-center">
                <label class="col-md-4 m-0">Loại chỉ tiêu</label>
                <div class="col-md-6">
                  {{model.TransType == 2? 'Cá nhân' : 'Đơn vị'}}
                </div>
                <label class="col-md-2 m-0" v-show="model.TransType == 1">Đơn vị</label>
                <div class="col-md-6" v-show="model.TransType == 1">
                  {{model.CompanyName}}
                </div>
                <label class="col-md-2 m-0" v-show="model.TransType == 2">Nhân viên</label>
                <div class="col-md-6" v-show="model.TransType == 2">
                  {{model.EmployeeName}}
                </div>
                <div class="col-md-4 d-flex align-items-center app-object-code">
                  <span>Mã số</span>
                  <span>{{model.TransNo}}</span>
                </div>
              </div>
              <div class="form-group row align-items-center">
                <label class="col-md-4 m-0">Chu kỳ</label>
                <div class="col-md-2">
                  {{PeriodType[model.PeriodType]}}
                </div>
                <label class="col-md-2 m-0">Từ ngày</label>
                <div class="col-md-4 fromdate-todate">
                  {{model.FromDate}}
                </div>
                <label class="col-md-2 m-0">Đến ngày</label>
                <div class="col-md-4 fromdate-todate">
                  {{model.ToDate}}
                </div>
              </div>

              <div class="form-group row align-items-center">
                <label class="col-md-4 m-0" for="TransName">Tên</label>
                <div class="col-md-20">
                  {{model.TransName}}
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-4 m-0">Giá trị loại chỉ tiêu:</label>
                <label class="col-md-20 m-0"></label>
              </div>

              <div class="div-scroll-table">
                <table class="table b-table table-sm table-bordered" style="width: 2000px;">
                <thead>
                <tr>
                  <th scope="col" style="width: 3%; border-bottom: none;" class="text-center"><i title="Công việc" class="fa fa-building-o"></i></th>
                  <th scope="col" style="border-bottom: none;" class="text-center">Phương pháp đánh giá</th>
                  <th scope="col" style="border-bottom: none; width:250px;" class="text-center">Chỉ tiêu</th>
                  <th scope="col" style="border-bottom: none;" class="text-center" >ĐVT</th>
                  <th scope="col" style="border-bottom: none; width:120px;" class="text-center" >Loại chấm điểm</th>
                  <th scope="col" style="border-bottom: none;" class="text-center" >Trọng số</th>
                  <th scope="col" style="border-bottom: none;" class="text-center" >Chỉ số mục tiêu</th>
                  <th scope="col" style="border-bottom: none;" class="text-center" >Chỉ số thực tế</th>
                  <th scope="col" style="border-bottom: none;" class="text-center" >% HT</th>
                  <th scope="col" style="border-bottom: none;" class="text-center" >{{EmployeeLogin.EmployeeName}}</th>
                  <th scope="col" style="border-bottom: none;" class="text-center" v-for="(item, key) in TaskEvaluatorEmployee" v-if="item.EvaluatorID != EmployeeLogin.EmployeeID">{{item.EvaluatorName}}</th>
                  <th scope="col" style="width: 80px;border-bottom: none;" v-if="showAction==1"></th>
                  <th scope="col" class="text-center td-action-fix-right" style="height: 29px;" v-if="showAction==1"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(field, fkey) in model.EvaluationTransItem">
                  <td class="text-center">
                    <i class="fa fa-building-o color-w" @click="onViewTask(fkey)" style="cursor: pointer;" title="Xem công việc"></i>
                  </td>
                  <td class="has-padding">{{model.EvaluationMethodName[field.EvaluationMethod]}}</td>
                  <td class="has-padding">{{field.IndicatorName}}</td>
                  <td class="has-padding">{{field.UomName}}</td>
                  <td class="has-padding">{{KeyResult[field.GradingType]}}</td>
                  <td class="has-padding text-right">{{field.ObjectiveRate|convertNumberToText}}</td>
                  <td class="has-padding text-right">{{field.ObjectiveIndex|convertNumberToText}}</td>
                  <td class="has-padding text-right">{{field.ActualIndex|convertNumberToText}}</td>
                  <td class="has-padding text-right">{{field.ActualRate|convertNumberToText}}</td>

                  <td scope="col" style="border-bottom: none;" class="text-right" >
                    {{TaskEvaluationEmployeeArr[field.IndicatorID +'_'+ EmployeeLogin.EmployeeID]?TaskEvaluationEmployeeArr[field.IndicatorID +'_'+ EmployeeLogin.EmployeeID].ActualIndex:""|convertNumberToText}}
                  </td>
                  <td scope="col" style="border-bottom: none;" class="text-right" v-for="(item, key1) in TaskEvaluatorEmployee" v-if="item.EvaluatorID != EmployeeLogin.EmployeeID">
                    {{TaskEvaluationEmployeeArr[field.IndicatorID +'_'+ item.EvaluatorID]?TaskEvaluationEmployeeArr[field.IndicatorID +'_'+ item.EvaluatorID].ActualIndex:""|convertNumberToText}}
                  </td>
                  <th scope="col" style="width: 80px;" v-if="showAction==1"></th>
                  <th scope="col" class="text-center td-action-fix-right" v-if="showAction==1">
                    <i @click="onViewOnTransKeyResult(field.TransItemID)" v-if="field.EvaluationMethod == 2"
                       class="fa fa-external-link ij-icon ij-icon-plus"
                       title="Xem kết quả then chốt"
                       style="font-size: 18px; cursor: pointer"></i>
                    <i @click="onViewOnTransFeedback(field.TransItemID)" v-if="field.EvaluationMethod == 6"
                       class="fa fa-external-link ij-icon ij-icon-plus"
                       title="Xem kết quả phản hồi"
                       style="font-size: 18px; cursor: pointer"></i>
                  </th>

                </tr>
                </tbody>
              </table>
              </div>
            </div>
          </b-card>
        </div>
      </vue-perfect-scrollbar>
    </div>

    <!-- Popup Add KeyResult -->
    <b-modal ref="ModalViewKeyResult" id="ij-modal-lg" size="xl"
             title="Phiếu chỉ tiêu ĐGCV – Kết quả then chốt ">
      <div class="main-body pb-5 pt-10" style="margin-top: 10px;margin-bottom: 0px;">
        <div class="table-responsive pb-10">
          <table class="table b-table table-hover table-sm b-table-selectable table-bordered">
            <thead>
            <tr>
              <th scope="col" style="border-bottom: none;" class="text-center">Chỉ tiêu</th>
              <th scope="col" style="width: 15%; border-bottom: none;" class="text-center">Loại chấm điểm</th>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">ĐVT</th>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Trọng số</th>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Chỉ số mục tiêu</th>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Kết quả thực tế</th>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">% hoàn thành</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(field, key) in model.EvaluationTransItemKeyresult[TransItemIDCurrent]">
              <td>{{model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].IndicatorName}}</td>
              <td>{{KeyResultType[model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].KeyresultType]}}</td>
              <td>{{model.Uom[model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].UomID]}}</td>
              <td class="text-left">{{model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].Rate}}</td>
              <td>{{model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].ObjectiveIndex}}</td>
              <td>{{model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].ActualIndex}}</td>
              <td>{{model.EvaluationTransItemKeyresult[TransItemIDCurrent][key].ActualRate}}</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideModalViewKeyResult()">
            Đóng
          </b-button>
        </div>
      </template>
    </b-modal>

    <!-- Popup Add Feedback -->
    <b-modal ref="ModalViewFeedback" id="ij-modal-lg" size="xl"
             title="Phiếu chỉ tiêu ĐGCV – Kết quả phản hồi">
      <div class="main-body pb-5 pt-10" style="margin-top: 10px;margin-bottom: 0px;">
        <div class="table-responsive pb-10">
          <table class="table b-table table-hover table-sm b-table-selectable table-bordered">
            <thead>
            <tr>
              <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Tên chỉ tiêu</th>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Là kết quả</th>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Kiểu kết quả</th>
              <th scope="col" style="border-bottom: none;" class="text-center">Ghi chú</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(field, key) in model.EvaluationTransItemFeedback[TransItemIDCurrent]">
              <td>{{model.EvaluationTransItemFeedback[TransItemIDCurrent][key].FeedbackName}}</td>
              <td class="text-center">
                <b-form-checkbox :disabled="true" v-model="model.EvaluationTransItemFeedback[TransItemIDCurrent][key].IsBinaryData==1">
                </b-form-checkbox>
              </td>
              <td>{{model.BinaryData[model.EvaluationTransItemFeedback[TransItemIDCurrent][key].BinaryDataID]}}</td>
              <td>{{model.EvaluationTransItemFeedback[TransItemIDCurrent][key].Description}}</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideModalViewFeedback()">
            Đóng
          </b-button>
        </div>
      </template>
    </b-modal>
    <!-- Popup View task.IndicatorID -->
    <b-modal ref="Task" id="modal-task-indicator" size="xl"
             title="Danh sách công việc">
      <div class="main-body main-body-view-action">
        <table class="table b-table table-sm table-bordered">
          <thead>
          <tr>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Mã số</th>
            <th scope="col" style="width: 60%; border-bottom: none;" class="text-center">Tên</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">ĐVT</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Khối lượng ƯTH</th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Khối lượng ĐTH </th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(task, key) in model.TaskIndicator">
            <td class="text-center">
              {{task.TaskNo}}
            </td>
            <td>
              {{task.TaskName}}
            </td>
            <td>
              {{model.Uom[task.UomID]}}
            </td>
            <td class="text-right">
              {{task.EstimatedQuantity|convertNumberToText}}
            </td>
            <td class="text-right">
            </td>
          </tr>
          </tbody>
        </table>

      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideTask()">
            Đóng
          </b-button>
        </div>
      </template>
    </b-modal>

  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import Swal from 'sweetalert2';
  import 'sweetalert2/src/sweetalert2.scss';
  import EvaluationTransPer from "./partials/EvaluationTransPer";

  const ListRouter = 'task-evaluation-trans';
  const EditRouter = 'task-evaluation-trans-edit';
  const CreateRouter = 'task-evaluation-trans-create';
  const DetailApi = 'task/api/evaluation-trans/view';
  const ListApi = 'task/api/evaluation-trans';
  const DeleteApi = 'task/api/evaluation-trans/delete';
  const TransType = {
    1: 'Chỉ tiêu đơn vị',
    2: 'Chỉ tiêu cá nhân',
  };
  // const EvaluationMethod = {
  //   1: 'Chỉ số đo lường hiệu suất',
  //   2: 'Mục tiêu và kết quả then chốt',
  //   3: 'Thẻ điểm cân bằng',
  //   4: 'Bảng điểm',
  //   5: 'Danh sách kiểm tra',
  //   6: 'Phản hồi 360 độ',
  //   7: 'Trắc nghiệm',
  // };
  export default {
    name: 'task-evaluation-trans',
    data() {
      return {
        idParams: this.$route.params.id,
        reqParams: this.$route.params.req,
        model: {
          EvaluationTransItemKeyresult: [],
          EvaluationTransItemFeedback: [],
          EvaluationTransItem: [],
          TransID: null,
          TransNo: '',
          TransName: '',
          UomID: '',
          UomName: '',
          TransType: null,
          EvaluationMethod: null,
          Uom: [],
          BinaryData: [],
          Inactive: false,
          TaskIndicator: {},
          GradingTypeName: {},
          FrequencyTypeName: {},
          EvaluationMethodName: {
            1 : 'Chỉ số đo lường hiệu suất',
            2 : 'Mục tiêu và kết quả then chốt',
            3 : 'Thẻ điểm cân bằng',
            4 : 'Bảng điểm',
            5 : 'Danh sách kiểm tra',
            6 : 'Phản hồi 360 độ',
            7 : 'Trắc nghiệm'
          },
        },
        TaskEvaluationEmployeeArr: {},
        TaskEvaluatorEmployee: {},
        EmployeeLogin: JSON.parse(localStorage.getItem('Employee')),
        showAction: 0,
        KeyResult : {
          1: 'Điểm thường',
          2: 'Điểm thưởng',
          3: 'Điểm phạt',
        },
        KeyResultType : {
          1: 'Khối lượng',
          2: 'Bảng điểm',
          3: 'Nhị phân',
        },
        PeriodType : {
          1: 'Năm',
          2: '6 tháng',
          3: 'Quý',
          4: 'Tháng',
          5: 'Tuần',
          6: 'Ngày',
        },
        TransItemIDCurrent: '',
        defaultModel: {},
        stage: {
          updatedData: false,
          message: (this.$route.params.message) ? this.$route.params.message : ''
        }
      }

    },

    components: {EvaluationTransPer},
    beforeCreate() {
      if (!this.$route.params.id) {
        this.$router.push({name: ListRouter});
      }
    },
    mounted() {
      this.fetchData();

      // hiển thị thông báo
      if (this.stage.message && this.stage.message !== '') {
        this.$bvToast.toast(this.stage.message, {
          title: 'Thông báo',
          variant: 'success',
          solid: true
        });
      }
    },
    updated() {
      this.stage.updatedData = true;
    },
    computed: {
      itemNo() {
        let index = 0;
        index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
        return index;
      }
    },
    methods: {
      fetchData() {
        if (this.idParams == 0 || _.isUndefined(this.idParams)) {
          return false;
        }
        let self = this;
        let urlApi = '';
        let requestData = {
          method: 'get',
        };
        // Api edit user
        if (this.idParams) {
          urlApi = DetailApi + '/' + this.idParams;
          let data = {
            id: this.idParams
          };
          requestData.data = data;
        }
        requestData.url = urlApi;
        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          self.defaultModel = responsesData;
          if (responsesData.status === 1) {
            if (responsesData.data.data) {
              self.model.TransName = responsesData.data.data.TransName;
              self.model.TransNo = responsesData.data.data.TransNo;
              self.model.TransType = responsesData.data.data.TransType;
              //self.model.EvaluationMethod = responsesData.data.data.EvaluationMethod;
              self.model.PeriodType = responsesData.data.data.PeriodType;
              self.model.FromDate = __.convertDateToString(responsesData.data.data.FromDate);
              self.model.ToDate = __.convertDateToString(responsesData.data.data.ToDate);
              self.model.CompanyID = responsesData.data.data.CompanyID;
              self.model.CompanyNo = responsesData.data.data.CompanyNo;
              self.model.CompanyName = responsesData.data.data.CompanyName;
              self.model.EmployeeID = responsesData.data.data.EmployeeID;
              self.model.EmployeeNo = responsesData.data.data.EmployeeNo;
              self.model.EmployeeName = responsesData.data.data.EmployeeName;
            }


            _.forEach(responsesData.data.Uom, function (value, key) {
              self.model.Uom[value.UomID] = value.UomName;
            });
            //////////////////////////TaskEvaluationEmployee//////////////////////////
            _.forEach(responsesData.data.TaskEvaluationEmployee, function (value, key) {
              self.TaskEvaluationEmployeeArr[value.IndicatorID+'_'+value.EvaluatorID] = value;
              self.TaskEvaluatorEmployee[value.EvaluatorID] = {
                EvaluatorID: value.EvaluatorID,
                EvaluatorName: value.EvaluatorName
              };
            });
            // self.model.Evaluation1Job = responsesData.data.Evaluation1Job;
            // self.setTaskIndicatorList(responsesData.data.Evaluation1Job);
            _.forEach(responsesData.data.EvaluationTransItem, function (field, key) {
              let fieldObj = {};
              self.RowItem = field.TransItemID;
              fieldObj = field;
              fieldObj.TransItemID = self.RowItem;
              fieldObj.LineIDTemp = self.RowItem;
              if(fieldObj.EvaluationMethod == 2 || fieldObj.EvaluationMethod == 6){
                self.showAction = 1;
              }

              fieldObj.LineIDTemp = self.RowItem;
              self.RowItem = self.RowItem + 1;
              self.model.EvaluationTransItem.push(fieldObj);

              if (self.TaskEvaluationEmployeeArr[field.IndicatorID+'_'+self.EmployeeLogin.EmployeeID] === undefined) {
                self.TaskEvaluationEmployeeArr[field.IndicatorID+'_'+self.EmployeeLogin.EmployeeID] = {
                  TransItemID: '',
                  TransID: '',
                  ActualIndex: 0,
                  ActualRate: 0,
                  EvaluatorID: self.EmployeeLogin.EmployeeID,
                  EvaluatorName: self.EmployeeLogin.EmployeeName,
                };
              }
            });
            _.forEach(responsesData.data.EvaluationTransItemKeyresult, function (field, key) {
              let fieldObj = field;
              fieldObj = field;
              fieldObj.LineIDTemp = self.TransItemIDCurrent;
              if (self.model.EvaluationTransItemKeyresult[fieldObj.TransItemID] == undefined) {
                self.model.EvaluationTransItemKeyresult[fieldObj.TransItemID] = [];
              }
              self.model.EvaluationTransItemKeyresult[fieldObj.TransItemID].push(fieldObj);
            });
            _.forEach(responsesData.data.EvaluationTransItemFeedback, function (field, key) {
              let fieldObj = field;
              fieldObj.LineIDTemp = self.TransItemIDCurrent;
              if (self.model.EvaluationTransItemFeedback[fieldObj.TransItemID] == undefined) {
                self.model.EvaluationTransItemFeedback[fieldObj.TransItemID] = [];
              }
              self.model.EvaluationTransItemFeedback[fieldObj.TransItemID].push(fieldObj)
            });
          }

          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      getUomNameByID(UomID) {
        let UomObj = _.find(this.model.Uom, ['UomID', UomID]);
        if (_.isObject(UomObj)) return UomObj.UomName;
        return '';
      },
      onNavigationItem(type) {
        let currentIndex = this.reqParams.idsArray.indexOf(this.idParams);
        let newIndex = (type === 'prev') ? currentIndex - 1 : currentIndex + 1;

        if (newIndex === (this.reqParams.idsArray.length) && (this.reqParams.currentPage != this.reqParams.lastPage)) {
          this.reqParams.currentPage = this.reqParams.currentPage + 1;
          this.getItemIds(type);
        } else if (newIndex < 0 && this.reqParams.currentPage > 1) {
          this.reqParams.currentPage = this.reqParams.currentPage - 1;
          this.getItemIds(type);
        } else {
          this.idParams = (this.reqParams.idsArray[newIndex]) ? this.reqParams.idsArray[newIndex] : this.idParams;
        }
      },
      getItemIds(type) {
        let self = this;
        let requestData = {
          method: 'post',
          url: ListApi,
          data: {
            per_page: this.reqParams.perPage,
            page: this.reqParams.currentPage,
            type: 'only-id'
          }
        };

        if (this.reqParams.search.TransName !== '') {
          requestData.data.TransName = this.reqParams.search.TransName;
        }

        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let dataResponse = response.data;
          if (dataResponse.status === 1) {
            self.reqParams.total = dataResponse.data.total;
            self.reqParams.perPage = String(dataResponse.data.per_page);
            self.reqParams.currentPage = dataResponse.data.current_page;

            this.reqParams.idsArray = [];
            _.forEach(dataResponse.data.data, function (value, key) {
              self.reqParams.idsArray.push(value.CustomerCodeID);
            });

            (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          self.$store.commit('isLoading', false);
          console.log(error);
        });

      },
      handleCopyItem() {
        this.$router.push({name: CreateRouter, params: {itemCopy: this.defaultModel}});
      },
      onEditClicked() {
        this.$router.push({
          name: EditRouter,
          params: {id: this.idParams, req: this.reqParams}
        });
      },
      onCreateClicked() {
        this.$router.push({name: CreateRouter});
      },
      onBackToList(message = '') {
        if (_.isString(message)) {
          this.$router.push({name: ListRouter, params: {message: message}});
        } else {
          this.$router.push({name: ListRouter});
        }

      },
      handleDeleteItem() {
        let self = this;
        let title = 'Bạn có muốn xóa bản ghi?';
        Swal.fire({
          title: title,
          text: 'Bạn sẽ không thể khôi phục thông tin bản ghi!',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Đồng ý',
          cancelButtonText: 'Hủy bỏ'
        }).then((result) => {
          if (result.value) {
            let requestData = {
              method: 'post',
              url: DeleteApi + '/' + self.idParams,
              data: {
                array_id: [self.idParams],
              },
            };

            ApiService.setHeader();
            ApiService.customRequest(requestData).then((response) => {
              let responseData = response.data;
              if (responseData.status === 1) {
                self.onBackToList('Bản ghi đã được xóa');
              } else {
                Swal.fire(
                  'Có lỗi',
                  '',
                  'error'
                );
              }
            }, (error) => {
              console.log(error);

            });

          }
        });
      },
      onViewOnTransKeyResult(TransItemID) {
        this.TransItemIDCurrent = TransItemID;
        this.$refs['ModalViewKeyResult'].show();
      },
      onViewOnTransFeedback(TransItemID) {
        this.TransItemIDCurrent = TransItemID;
        this.$refs['ModalViewFeedback'].show();
      },
      onHideModalViewKeyResult(){
        this.$refs['ModalViewKeyResult'].hide();
      },
      onHideModalViewFeedback(){
        this.$refs['ModalViewFeedback'].hide();
      },
      updateModel() {
        if (this.stage.updatedData) {
          this.$forceUpdate();
        }
      },

      onViewTask(key) {
        let self = this;
        let requestData = {
          method: 'post',
          data: {
            FromDate: self.model.FromDate,
            ToDate: self.model.ToDate,
            FrequencyType: self.model.PeriodType,
            EmployeeID: self.model.EmployeeID,
            IndicatorID: self.model.EvaluationTransItem[key].IndicatorID,
          },
          url: 'task/api/evaluation-trans/get-task'
        };

        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.model.TaskIndicator = responsesData.data;
            _.forEach(responsesData.Uom, function (value, key) {
              self.model.Uom[value.UomID] = value.UomName;
            });
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
        this.$forceUpdate();
        this.$refs['Task'].show();
      },
      onHideTask() {
        this.isForm = false;
        this.$refs['Task'].hide();
      },
    },
    watch: {
      idParams() {
        this.fetchData();
      }
    }
  }
</script>

<style>
  .td-action-fix-right {
    position: absolute;
    width: 83px;
    right: 20px;
    top: auto;
    /*only relevant for first row*/
    background: #fff;
    border-bottom: none !important;
    /*compensate for top border*/
  }
  .td-action-fix-right:last-child {
    border-bottom: 1px solid #c8ced3 !important;
    height: 31px;
  }
  .div-scroll-table {
    width: 100%;
    overflow-x: scroll;
    margin-right: 5em;
    overflow-y: visible;
    padding: 0;
  }
</style>


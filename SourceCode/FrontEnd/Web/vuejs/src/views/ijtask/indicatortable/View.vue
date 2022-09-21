<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Bảng chỉ tiêu ĐGCV: {{model.TableName}}</span>
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
              <IndicatorTablePer :TableID="this.idParams"></IndicatorTablePer>
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
                <label class="col-md-4 m-0" for="TableName">Tên</label>
                <div class="col-md-16">
                  {{model.TableName|perView(TablePer, 'TableName')}}
                </div>
                <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                  <span>Mã số</span>
                  {{model.TableNo|perView(TablePer, 'TableNo')}}
                </div>
              </div>
              <div class="form-group row align-items-center">
                <label class="col-md-4 m-0">Loại chỉ tiêu</label>
                <div class="col-md-6">
                  {{model.IndicatorType|perView(TablePer, 'IndicatorType')}}
                </div>
                <label class="col-md-2 m-0" v-show="model.IndicatorType_val == 1">Đơn vị</label>
                <div class="col-md-6" v-show="model.IndicatorType_val == 1">
                  {{model.CompanyName|perView(TablePer, 'CompanyID')}}
                </div>
                <label class="col-md-2 m-0" v-show="model.IndicatorType_val == 2">Nhân viên</label>
                <div class="col-md-6" v-show="model.IndicatorType_val == 2">
                  {{model.EmployeeName|perView(TablePer, 'EmployeeID')}}
                </div>
              </div>
              <div class="form-group row align-items-center">

                <label class="col-md-4 m-0">Chu kỳ</label>
                <div class="col-md-2">
                  {{model.PeriodType|perView(TablePer, 'PeriodType')}}
                </div>
                <label class="col-md-2 m-0">Từ ngày</label>
                <div class="col-md-4 fromdate-todate">
                  {{model.FromDate|perView(TablePer, 'FromDate')}}
                </div>
                <label class="col-md-2 m-0">Đến ngày</label>
                <div class="col-md-2">
                  {{model.ToDate|perView(TablePer, 'ToDate')}}
                </div>
              </div>

              <table class="table b-table table-sm table-bordered">
                <thead>
                <tr>
                  <th scope="col" style="border-bottom: none;" class="text-center">Phương pháp</th>
                  <th scope="col" style="border-bottom: none;" class="text-center">Chỉ tiêu</th>
                  <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Đơn vị tính</th>
                  <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Loại chấm điểm</th>
                  <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Trọng số</th>
                  <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Tần suất</th>
                  <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Chỉ số</th>
<!--                  <th scope="col" style="border-bottom: none;" class="text-center">Mô tả</th>-->
                  <th scope="col" style="width: 50px; border-bottom: none;" class="text-center"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(field, fkey) in model.IndicatorTableItem">
                  <td class="has-padding" :title="EvaluationMethodArr[field.EvaluationMethod]">{{EvaluationMethodArr[field.EvaluationMethod]}}</td>
                  <td class="has-padding" :title="field.IndicatorName">{{field.IndicatorName}}</td>
                  <td class="has-padding" :title="model.Uom[field.UomID]">{{model.Uom[field.UomID]}}</td>
                  <td class="has-padding" :title="model.GradingTypeName[field.GradingType]">{{model.GradingTypeName[field.GradingType]}}</td>
                  <td class="has-padding text-right" :title="field.ObjectiveRate">{{field.ObjectiveRate}}</td>
                  <td class="has-padding" :title="model.FrequencyTypeName[field.FrequencyType]">{{model.FrequencyTypeName[field.FrequencyType]}}</td>
                  <td class="has-padding text-right" :title="field.ObjectiveIndex">{{field.ObjectiveIndex}}</td>
<!--                  <td class="has-padding" :title="field.Description">{{field.Description}}</td>-->
                  <td class="has-padding text-center">
                    <i @click="onViewOnTableKeyResult(field.TableItemID)"
                       class="fa fa-external-link ij-icon ij-icon-plus"
                       title="Xem kết quả then chốt" v-if="model.IndicatorTableItem[fkey].EvaluationMethod==2"
                       style="font-size: 18px; cursor: pointer"></i>
                    <i @click="onViewOnTableFeedback(field.TableItemID)"
                       class="fa fa-external-link ij-icon ij-icon-plus"
                       title="Xem kết quả phản hồi" v-if="model.IndicatorTableItem[fkey].EvaluationMethod ==6"
                       style="font-size: 18px; cursor: pointer"></i>
                  </td>

                </tr>
                </tbody>
              </table>
            </div>
          </b-card>
        </div>
      </vue-perfect-scrollbar>
    </div>

    <!-- Popup Add KeyResult -->
    <b-modal ref="ModalViewKeyResult" id="ij-modal-lg" size="xl"
             title="Bảng mẫu chỉ tiêu ĐGCV – Kết quả then chốt ">
      <div class="main-body pb-5 pt-10" style="margin-top: 10px;margin-bottom: 0px;">
        <div class="table-responsive pb-10">
          <table class="table b-table table-hover table-sm b-table-selectable table-bordered">
            <thead>
            <tr>
              <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Tên chỉ tiêu</th>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Đơn vị tính</th>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Loại chấm điểm</th>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Trọng số</th>
              <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Ghi chú</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(field, key) in model.IndicatorTableItemKeyresult[TableItemIDCurrent]">
              <td :title="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].IndicatorName">{{model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].IndicatorName}}</td>
              <td :title="model.Uom[model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].UomID]">{{model.Uom[model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].UomID]}}</td>
              <td :title="KeyResult[model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].KeyresultType]">{{KeyResult[model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].KeyresultType]}}</td>
              <td class="text-right" :title="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].Rate">{{model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].Rate}}</td>
              <td :title="model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].Description">{{model.IndicatorTableItemKeyresult[TableItemIDCurrent][key].Description}}</td>
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
             title="Bảng mẫu chỉ tiêu ĐGCV – Kết quả phản hồi">
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
            <tr v-for="(field, key) in model.IndicatorTableItemFeedback[TableItemIDCurrent]">
              <td :title="model.IndicatorTableItemFeedback[TableItemIDCurrent][key].IndicatorName">{{model.IndicatorTableItemFeedback[TableItemIDCurrent][key].IndicatorName}}</td>
              <td class="text-center">
                <b-form-checkbox :disabled="true" v-model="model.IndicatorTableItemFeedback[TableItemIDCurrent][key].IsBinaryData==1">
                </b-form-checkbox>
              </td>
              <td :title="model.BinaryData[model.IndicatorTableItemFeedback[TableItemIDCurrent][key].BinaryDataID]">{{model.BinaryData[model.IndicatorTableItemFeedback[TableItemIDCurrent][key].BinaryDataID]}}</td>
              <td :title="model.IndicatorTableItemFeedback[TableItemIDCurrent][key].Description">{{model.IndicatorTableItemFeedback[TableItemIDCurrent][key].Description}}</td>
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
  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import Swal from 'sweetalert2';
  import 'sweetalert2/src/sweetalert2.scss';
  import IndicatorTablePer from "./partials/IndicatorTablePer";

  const ListRouter = 'task-indicator-table';
  const EditRouter = 'task-indicator-table-edit';
  const CreateRouter = 'task-indicator-table-create';
  const DetailApi = 'task/api/indicator-table/view';
  const ListApi = 'task/api/indicator-table';
  const DeleteApi = 'task/api/indicator-table/delete';
  const IndicatorType = {
    1: 'Chỉ tiêu đơn vị',
    2: 'Chỉ tiêu cá nhân',
  };
  const PeriodType = {
    1: 'Năm',
    2: '6 tháng',
    3: 'Quý',
    4: 'Tháng',
    5: 'Tuần',
    6: 'Ngày',
  };

  export default {
    name: 'task-indicator-table',
    data() {
      return {
        idParams: this.$route.params.id,
        reqParams: this.$route.params.req,
        model: {
          IndicatorTableItemKeyresult: [],
          IndicatorTableItemFeedback: [],
          TableID: null,
          TableNo: '',
          TableName: '',
          UomID: '',
          UomName: '',
          IndicatorType: null,
          EvaluationMethod: null,
          Uom: [],
          BinaryData: [],
          Inactive: false,
          GradingTypeName: {},
          FrequencyTypeName: {},
        },
        KeyResult : {
          1: 'Điểm thường',
          2: 'Điểm thưởng',
          3: 'Điểm phạt',
        },
        EvaluationMethodArr : {
          1: 'Chỉ số đo lường hiệu suất',
          2: 'Mục tiêu và kết quả then chốt',
          3: 'Thẻ điểm cân bằng',
          4: 'Bảng điểm',
          5: 'Danh sách kiểm tra',
          6: 'Phản hồi 360 độ',
        },
        TablePer: {},
        TableItemIDCurrent: '',
        defaultModel: {},
        stage: {
          updatedData: false,
          message: (this.$route.params.message) ? this.$route.params.message : ''
        }
      }

    },

    components: {IndicatorTablePer},
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
            self.model.TableID = responsesData.data.data.TableID;
            self.model.TableNo = responsesData.data.data.TableNo;
            self.model.TableName = responsesData.data.data.TableName;
            self.model.inactive = (responsesData.data.data.Inactive) ? true : false;
            self.model.FromDate = __.convertDateToString(responsesData.data.data.FromDate);
            self.model.ToDate = __.convertDateToString(responsesData.data.data.ToDate);
            self.model.TableNo = responsesData.data.data.TableNo;
            self.model.TableName = responsesData.data.data.TableName;

            self.model.EmployeeID = responsesData.data.data.EmployeeID;
            self.model.EmployeeNo = responsesData.data.data.EmployeeNo;
            self.model.EmployeeName = responsesData.data.data.EmployeeName;

            self.model.CompanyID = responsesData.data.data.CompanyID;
            self.model.CompanyNo = responsesData.data.data.CompanyNo;
            self.model.CompanyName = responsesData.data.data.CompanyName;

            self.model.EvaluationMethod_val = responsesData.data.data.EvaluationMethod;
            self.model.IndicatorType_val = responsesData.data.data.IndicatorType;
            self.model.PeriodType = (responsesData.data.data.PeriodType && PeriodType[responsesData.data.data.PeriodType]) ? PeriodType[responsesData.data.data.PeriodType] : '';
            self.model.IndicatorType = (responsesData.data.data.IndicatorType && IndicatorType[responsesData.data.data.IndicatorType]) ? IndicatorType[responsesData.data.data.IndicatorType] : 'Chỉ tiêu đơn vị';
            self.model.EvaluationMethod = (responsesData.data.data.EvaluationMethod && EvaluationMethod[responsesData.data.data.EvaluationMethod]) ? EvaluationMethod[responsesData.data.data.EvaluationMethod] : 'Chỉ số đo lường hiệu suất';

            self.model.IndicatorTable = responsesData.data.IndicatorTable;
            self.model.IndicatorTableItem = responsesData.data.IndicatorTableItem;
            self.model.GradingTypeName = responsesData.data.GradingTypeName;
            self.model.FrequencyTypeName = responsesData.data.FrequencyTypeName;
            self.TablePer = responsesData.data.TablePer;
            _.forEach(responsesData.data.Uom, function (field, key) {
              self.model.Uom[field.UomID] = field.UomName
            });
            _.forEach(responsesData.data.BinaryData, function (field, key) {
              self.model.BinaryData[field.BinaryDataID] = field.BinaryDataName
            });
            _.forEach(responsesData.data.IndicatorTableItemKeyResult, function (field, key) {
              if(self.model.IndicatorTableItemKeyresult[field.TableItemID] == undefined){
                field.LineIDTemp = field.TableItemID;
                self.model.IndicatorTableItemKeyresult[field.TableItemID] = [];
              }
              self.model.IndicatorTableItemKeyresult[field.TableItemID].push(field)
            });
            _.forEach(responsesData.data.IndicatorTableItemFeedback, function (field, key) {
              if(self.model.IndicatorTableItemFeedback[field.TableItemID] == undefined){
                field.LineIDTemp = field.TableItemID;
                self.model.IndicatorTableItemFeedback[field.TableItemID] = [];
              }
              self.model.IndicatorTableItemFeedback[field.TableItemID].push(field)
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

        if (this.reqParams.search.TableName !== '') {
          requestData.data.TableName = this.reqParams.search.TableName;
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
      onViewOnTableKeyResult(TableItemID) {
        this.TableItemIDCurrent = TableItemID;
        this.$refs['ModalViewKeyResult'].show();
      },
      onViewOnTableFeedback(TableItemID) {
        this.TableItemIDCurrent = TableItemID;
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

    },
    watch: {
      idParams() {
        this.fetchData();
      }
    }
  }
</script>

<style>
</style>


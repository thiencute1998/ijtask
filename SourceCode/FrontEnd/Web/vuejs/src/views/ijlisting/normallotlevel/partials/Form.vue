<template>
  <div class="main-entry component-norm-allot-level-form">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Định mức phân bổ dự toán<span v-if="model.CateName">:</span> {{model.CateName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Định mức phân bổ dự toán<span v-if="model.CateName">:</span> {{model.CateName}}</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="handleSubmitForm"><i
                class="fa fa-check-square-o"></i> Lưu
              </b-button>
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onBackToList"><i
                class="fa fa-ban"></i> Hủy
              </b-button>
            </div>
          </b-col>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-icons">
              <div class="main-header-collapse">
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
            <div class="form-group row align-items-center">
              <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" title="Mã chỉ tiêu dự toán" style="white-space: nowrap">Mã CTDT</div>
              <div class="col-md-3 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3">
                <ijcore-modal-listing
                  v-model="model" title="Chỉ tiêu dự toán"  api="/listing/api/common/list"
                  @changed="changeNorm"
                  field-name="NormName" field-no="NormNo" field-i-d="NormID" table="norm" :field-type="2">
                </ijcore-modal-listing>
              </div>
              <div class="col-md-3 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span title="Tên chỉ tiêu dự toán">Tên CTDT</span>
              </div>
              <div class="col-md-15">{{model.NormName}}</div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" title="Tiêu chí phân bổ dự toán">TCPBDT </label>
              <div class="col-md-21">
                <norm-allot-map :norm="model" v-model="model.NormAllot"></norm-allot-map>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" title="Mã số định mức phân bổ dự toán">Mã số ĐMPBDT </label>
              <div class="col-md-3">
                <b-form-input v-model="model.NormAllotLevelNo"></b-form-input>
              </div>
              <label class="col-md-3 m-0" title="Tên định mức phân bổ dự toán">Tên ĐMPBDT </label>
              <div class="col-md-15">
                <b-form-input v-model="model.NormAllotLevelName"></b-form-input>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Là mục con của </label>
              <div class="col-md-21">
                <ijcore-modal-parent v-model="model"
                                   :title="'Định mức phân bổ dự toán'"
                                   :api="'/listing/api/common/get-parent'"
                                   :table="'norm_allot_level'" field-i-d="NormAllotLevelID"
                                   field-no="NormAllotLevelNo"
                                   field-name="NormAllotLevelName"
                                   placeholderInput="Chọn định mức phân bổ dự toán cha"
                                   placeholderSearch="Nhập tên định mức phân bổ dự toán">
                </ijcore-modal-parent>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Ngày hiệu lực </label>
              <div class="col-md-6">
                <ijcore-date-picker v-model="model.EffectiveDate"></ijcore-date-picker>
              </div>
              <label class="col-md-3 m-0">Ngày hết hiệu lực </label>
              <div class="col-md-6">
                <ijcore-date-picker v-model="model.ExpirationDate"></ijcore-date-picker>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Đơn vị tính</label>
              <div class="col-md-4 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3">
                <ijcore-modal-listing
                  v-model="model" title="Đơn vị tính"  api="/listing/api/common/list"
                  field-name="UomName" field-no="UomNo" field-i-d="UomID" table="uom">
                </ijcore-modal-listing>
              </div>
              <label class="col-md-2 m-0">Định mức</label>
              <div class="col-md-4">
                <ijcore-number v-model="model.LCUnitPrice"></ijcore-number>
              </div>
            </div>

            <label>Đơn giá :</label>
            <table class="table b-table table-sm table-bordered">
              <thead>
              <tr style="z-index: 4">
                <th scope="col" style="width: 20%" class="text-center">Ngày hiệu lực</th>
                <th scope="col" style="width: 20%" class="text-center">Ngày hết hiệu lực</th>
                <th scope="col" style="width: 14%" class="text-center">Đơn vị tính</th>
                <th scope="col" style="width: 20%" class="text-center">Định mức tối thiểu</th>
                <th scope="col" style="width: 20%" class="text-center">Định mức tối đa</th>
                <th scope="col" style="width: 6%" class="text-center"></th>
              </tr>
              </thead>
              <tbody>
                <tr v-for="(field, key) in NormAllotLevelItem">
                  <td class="text-center">{{NormAllotLevelItem[key].EffectiveDate | convertServerDateToClientDate}}</td>
                  <td class="text-center">{{NormAllotLevelItem[key].ExpirationDate | convertServerDateToClientDate}}</td>
                  <td>{{NormAllotLevelItem[key].UomName}}</td>
                  <td class="text-right">{{NormAllotLevelItem[key].FCMinUnitPrice | formatCurrency}}</td>
                  <td class="text-right">{{NormAllotLevelItem[key].FCMaxUnitPrice | formatPercent}}</td>
                  <td class="text-center">
                    <div class="d-flex align-center justify-content-center">
                      <master-detail-form v-model="NormAllotLevelItem[key]" title="Đơn giá định mức phân bổ dự toán" :is-create="false" @saved="sortFieldOnTable"></master-detail-form>
                      <i @click="onDeleteFieldOnTable(key)" class="fa fa-trash-o ml-2" title="Xóa"
                         style="font-size: 18px; cursor: pointer"></i>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
            <master-detail-form title="Đơn giá định mức phân bổ dự toán" :is-create="true" @saved="onAddFieldOnTable"></master-detail-form>
          </b-card>
        </div>
      </vue-perfect-scrollbar>
    </div>
  </div>
</template>

<script>
import ApiService from '@/services/api.service';
import Swal from 'sweetalert2';
import 'sweetalert2/src/sweetalert2.scss';
import vSelect from 'vue-select';
import MaskedInput from 'vue-text-mask';
import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
import Select2 from 'v-select2-component';
import moment from 'moment';
import draggable from 'vuedraggable';
import ColumnResizer from "column-resizer";
import IjcoreModalListing from "../../../../components/IjcoreModalListing";
import ModalNormAllotMultiListing from "./ModalNormAllotMultiListing";
import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
import IjcoreNumber from "../../../../components/IjcoreNumber";
import MasterDetailForm from "./MasterDetailForm";
import NormAllotMap from "./NormAllotMap";
import IjcoreModalParent from "../../../../components/IjcoreModalParent";

moment.locale('vi');
const ListRouter = 'listing-normallotlevel';
const EditRouter = 'listing-normallotlevel-edit';
const CreateRouter = 'listing-normallotlevel-edit';
const ViewRouter = 'listing-normallotlevel-view';
const ViewApi = 'listing/api/norm-allot-level/view';
const EditApi = 'listing/api/norm-allot-level/edit';
const CreateApi = 'listing/api/norm-allot-level/create';
const StoreApi = 'listing/api/norm-allot-level/store';
const UpdateApi = 'listing/api/norm-allot-level/update';
const ListApi = 'listing/api/norm-allot-level';

const dataTypeOption = {
  1: 'Số',
  2: 'Kí tự',
  3: 'Ngày',
  4: 'Ngày giờ',
  5: 'Có/Không',
  6: 'Đúng/Sai'
};

const DataTypeOption = [
  {value: 1, text: 'Số'},
  {value: 2, text: 'Kí tự'},
  {value: 3, text: 'Ngày'},
  {value: 4, text: 'Ngày giờ'},
  {value: 5, text: 'Có/Không'},
  {value: 6, text: 'Đúng/Sai'}
];

export default {
  name: 'listing-normallotlevel-view',
  data() {
    return {
      idParams: this.idParamsProps,
      reqParams: this.reqParamsProps,
      model: {
        NormAllotLevelID: null,
        NormAllotLevelNo: '',
        NormAllotLevelName: '',
        NormAllotLevelType: 1,
        NormAllot: [],
        ParentID: null,
        ParentNo: '',
        ParentName: '',
        Level: 1,
        Detail: 1,
        NormID: null,
        NormNo: '',
        NormName: '',
        NormAllotID: null,
        NormAllotNo: '',
        NormAllotName: '',
        EffectiveDate: '',
        ExpirationDate: '',
        UomID: '',
        UomNo: '',
        UomName: '',
        FCUnitPrice: null,
        LCUnitPrice: null,
      },
      NormAllotLevelItem: [],
      stage: {
        updatedData: false
      },

      // for field operator time
      formats: {
        title: 'MMMM YYYY',
        weekdays: 'W',
        navMonths: 'MMM',
        input: ['L', 'YYYY-MM-DD', 'YYYY/MM/DD'], // Only for `v-date-picker`
        dayPopover: 'L', // Only for `v-date-picker`
      }
    }

  },

  props: {
    idParamsProps: {
      type: Number,
      default: 0
    },
    reqParamsProps: {
      type: Object,
      default: function () {
        return {}
      }
    },
    itemCopy: {
      type: Object,
      default: function () {
        return {}
      }
    }
  },

  components: {
    Select2,
    MaskedInput,
    draggable,
    IjcoreModalListing,
    IjcoreDatePicker,
    IjcoreNumber,
    MasterDetailForm,
    ModalNormAllotMultiListing,
    NormAllotMap,
    IjcoreModalParent
  },
  beforeCreate() {
  },
  mounted() {
    this.fetchData();
    if (document.querySelector('.table-column-resizable')) {
      new ColumnResizer(
        document.querySelector('.table-column-resizable'),{resizeMode: 'overflow'}
      );
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
    },
    dataTypeOption() {
      return DataTypeOption;
    }
  },
  methods: {
    fetchData() {
      let self = this;
      let urlApi = CreateApi;
      let requestData = {
        method: 'get',
        data: {}
      };
      // Api edit user
      if (this.idParams) {
        urlApi = EditApi + '/' + this.idParams;
        requestData.data.id = this.idParams;
      }
      requestData.url = urlApi;
      this.$store.commit('isLoading', true);

      ApiService.setHeader();
      ApiService.customRequest(requestData).then((responses) => {
        let responsesData = responses.data;
        // copy item
        if (!self.idParams && !_.isEmpty(self.itemCopy)) {
          responsesData = self.itemCopy;
        }
        if (responsesData.status === 1) {
          if (self.idParams || !_.isEmpty(self.itemCopy)) {
            if (_.isObject(responsesData.data.data)) {
              self.model.NormAllotLevelID = responsesData.data.data.NormAllotLevelID;
              self.model.NormAllotLevelNo = responsesData.data.data.NormAllotLevelNo;
              self.model.NormAllotLevelName = responsesData.data.data.NormAllotLevelName;
              self.model.NormAllotLevelType = responsesData.data.data.NormAllotLevelType;
              self.model.ParentID = responsesData.data.data.ParentID;
              self.model.ParentNo = responsesData.data.data.ParentNo;
              self.model.ParentName = responsesData.data.data.ParentName;
              self.model.NormID = responsesData.data.data.NormID;
              self.model.NormNo = responsesData.data.data.NormNo;
              self.model.NormName = responsesData.data.data.NormName;
              self.model.EffectiveDate = __.convertServerDateToClientDate(responsesData.data.data.EffectiveDate);
              self.model.ExpirationDate = __.convertServerDateToClientDate(responsesData.data.data.ExpirationDate);
              self.model.UomID = responsesData.data.data.UomID;
              self.model.UomNo = responsesData.data.data.UomNo;
              self.model.UomName = responsesData.data.data.UomName;
              self.model.FCUnitPrice = responsesData.data.data.FCUnitPrice;
              self.model.LCUnitPrice = responsesData.data.data.LCUnitPrice;

              self.model.NormAllot = responsesData.data.NormAllotLevelMap;
              self.NormAllotLevelItem = responsesData.data.NormAllotLevelItem;

              _.forEach(self.model.NormAllot, function (normAllot, key) {
                self.$set(self.model.NormAllot[key], 'Checked', true);
              });
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
    changeNorm(data){
      this.model.NormAllotLevelName = data.NormName;
      this.model.NormAllotLevelType = data.NormType;
      if (!this.model.NormID) {
        this.model.NormAllot = [];
        return false;
      }
      let self = this;
      let requestData = {
        method: 'get',
        url: 'listing/api/norm/get-norm-allot/' + this.model.NormID,
        data: {},
      };

      this.$store.commit('isLoading', true);
      ApiService.setHeader();
      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;
        if (responseData.status === 1) {
          self.model.NormAllot = responseData.data;
          _.forEach(self.model.NormAllot, function (normAllot, key) {
            self.$set(self.model.NormAllot[key], 'Checked', true);
          });
          this.$bvToast.toast('Cập nhật thành công', {
            variant: 'success',
            title: 'Thông báo',
            solid: true
          });
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });
    },
    selectedNormAllot(link){
      let linkReset = link.filter(function (val) {
        return val
      });
      let self = this;
      _.forEach(linkReset, function (item, key) {
        self.model.NormAllot.push({
          NormAllotID: item.NormAllotID,
          NormAllotNo: item.NormAllotNo,
          NormAllotName: item.NormAllotName,
          NormID: item.NormID,
          NormNo: item.NormNo,
          NormName: item.NormName
        });
      });
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

      if (this.reqParams.search.CateName !== '') {
        requestData.data.CateName = this.reqParams.search.CateName;
      }
      if (this.reqParams.search.CateNo !== '') {
        requestData.data.CateNo = this.reqParams.search.CateNo;
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
            self.reqParams.idsArray.push(value.CateID);
          });

          (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
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
    onAddFieldOnTable(data) {
      let fieldObj = {};
      _.forEach(data, function (value, key) {
        fieldObj[key] = value;
      });
      this.NormAllotLevelItem.push(fieldObj);
      this.sortFieldOnTable();
    },
    onDeleteFieldOnTable(key) {
      this.NormAllotLevelItem.splice(key, 1);
    },
    sortFieldOnTable(){
      this.NormAllotLevelItem = _.orderBy(this.NormAllotLevelItem, ['EffectiveDate'], ['desc']);
      this.updateLCUnitPrice();
      this.$forceUpdate();
    },
    updateLCUnitPrice() {
      if (this.NormAllotLevelItem[0]) {
        this.model.FCUnitPrice = this.NormAllotLevelItem[0].FCUnitPrice
        this.model.LCUnitPrice = this.NormAllotLevelItem[0].LCUnitPrice
      }
    },
    handleSubmitForm() {
      let self = this;
      let normAllot = _.filter(this.model.NormAllot, ['Checked', true]);
      const requestData = {
        method: 'post',
        url: StoreApi,
        data: {
          master: {
            NormAllotLevelNo: this.model.NormAllotLevelNo,
            NormAllotLevelName: this.model.NormAllotLevelName,
            NormAllotLevelType: this.model.NormAllotLevelType,
            NormAllot: normAllot,
            ParentID: this.model.ParentID,
            ParentNo: this.model.ParentNo,
            ParentName: this.model.ParentName,
            NormID: this.model.NormID,
            NormNo: this.model.NormNo,
            NormName: this.model.NormName,
            EffectiveDate: this.model.EffectiveDate,
            ExpirationDate: this.model.ExpirationDate,
            UomID: this.model.UomID,
            UomNo: this.model.UomNo,
            UomName: this.model.UomName,
            FCUnitPrice: this.model.FCUnitPrice,
            LCUnitPrice: this.model.LCUnitPrice,
          },
          detail: this.NormAllotLevelItem
        }
      };

      // edit user
      if (this.idParams) {
        requestData.url = UpdateApi + '/' + this.idParams;
      }
      this.$store.commit('isLoading', true);
      ApiService.setHeader();
      ApiService.customRequest(requestData).then((responses) => {
        let responsesData = responses.data;
        if (responsesData.status === 1) {
          this.$router.push({
            name: ViewRouter,
            params: {id: responsesData.data}
          });
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        Swal.fire(
          'Thông báo',
          'Không kết nối được với máy chủ',
          'error'
        );
        self.$store.commit('isLoading', false);
      });
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

      let self = this;
      let params = (this.$route.params.req) ? this.$route.params.req:{};
      let query = this.$route.query;
      query.isBackToList = true;
      if (_.isString(message)) {
        params.message = message;
        this.$router.push({
          name: ViewRouter,
          query: query,
          params: {id: self.idParams, req: params, message: 'Bản ghi đã được cập nhật!'}
        });
      } else {
        this.$router.push({name: ListRouter, query: query, params: params});
      }
    },
    updateModel() {
      if (this.stage.updatedData) {
        this.$forceUpdate();
      }
    },
    autoCorrectedDatePipe: () => {
      return createAutoCorrectedDatePipe('dd/mm/yyyy')
    },
    autoCorrectedDateTimePipe: () => {
      return createAutoCorrectedDatePipe('dd/mm/yyyy hh:mm')
    },
    changeVendorTableItem(moved) {},
  },
  watch: {
    idParams() {
      this.fetchData();
    },
  }
}
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="css">
.v-select .dropdown-menu {
  max-height: 170px !important;
}
.component-norm-allot-level-form .mx-datepicker {
  width: 125px;
}
.custom-align {
  flex: 0 0 12.3%;
}
</style>

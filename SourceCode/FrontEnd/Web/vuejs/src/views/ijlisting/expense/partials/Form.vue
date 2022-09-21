<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i>Khoản chi<span v-if="model.ExpenseName">:</span> {{model.ExpenseName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i>Khoản chi<span v-if="model.ExpenseName">:</span> {{model.ExpenseName}}</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="handleSubmitForm"><i class="fa fa-check-square-o"></i> Lưu</b-button>
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onBackToList"><i class="fa fa-ban"></i> Hủy</b-button>
            </div>
          </b-col>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-icons">
              <div class="main-header-collapse">
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
          <b-card>
            <div class="form-group row align-items-center">
              <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
              <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                <input v-model="model.ExpenseName" type="text" id="ExpenseName" class="form-control" placeholder="Tên khoản chi" name="ExpenseName"/>
              </div>
              <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.ExpenseNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Là mục con: </label>
              <div class="col-md-17">
                <IjcoreModalSysTemParent v-model="model" :title="'Khoản chi'" :api="'/listing/api/common/list'" :table="'expense'" :fieldName="'ExpenseName'" :fieldNo="'ExpenseNo'" :fieldID="'ExpenseID'" :placeholderInput="'Chọn khoản chi cha'" :placeholderSearch="'Nhập tên khoản chi'" :columnID="'ExpenseID'" :columnNo="'ExpenseNo'" :columnName="'ExpenseName'">
                </IjcoreModalSysTemParent>
              </div>
              <div v-if="model.ParentID" class="col-lg-4 col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.ParentNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Loại khoản chi</label>
              <div class="col-md-21">
                <expense-modal-search-input-catelist
                  v-model="model.ExpenseCate"
                  title-modal="Loại khoản chi"
                  placeholder="Loại khoản chi"
                ></expense-modal-search-input-catelist>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Đơn vị tính</label>
              <div class="col-md-9 mb-3 mb-sm-0">
                <ijcore-modal-search-listing
                  v-model="model" :title="'Đơn vị tính'" :table="'uom'" :api="'/listing/api/common/list'"
                  :fieldID="'UomID'" :fieldNo="'UomNo'" :fieldName="'UomName'"
                  :fieldAssignID="'UomID'" :fieldAssignNo="'UomNo'" :fieldAssignName="'UomName'"
                >
                </ijcore-modal-search-listing>
              </div>
              <label class="col-md-3 m-0">Mục - Tiểu mục</label>
              <div class="col-md-9 mb-3 mb-sm-0">
                <ijcore-modal-search-listing
                  v-model="model" :title="'Mục - Tiểu mục'" :table="'sbi_item'" :api="'/listing/api/common/list'"
                  :fieldID="'SbiItemID'" :fieldNo="'SbiItemNo'" :fieldName="'SbiItemName'"
                  :fieldAssignID="'SbiItemID'" :fieldAssignNo="'SbiItemNo'" :fieldAssignName="'SbiItemName'"
                >
                </ijcore-modal-search-listing>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Cân đối ngân sách</label>
              <div class="col-md-9">
                <b-form-select v-model="model.BudgetBalanceType" :options="BudgetBalanceTypeOption"></b-form-select>
              </div>
              <label class="col-md-3 m-0">Loại NSNN</label>
              <div class="col-md-9">
                <b-form-select v-model="model.BudgetStateType " :options="BudgetStateTypeOption"></b-form-select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 m-0">ĐMDTCS</label>
              <div class="col-md-9 mb-3 mb-sm-0">
                <ijcore-modal-search-listing
                  v-model="model" :title="'Định mức dự toán cơ sở'" :table="'norm'" :api="'/listing/api/common/list'"
                  :fieldID="'NormID'" :fieldNo="'NormNo'" :fieldName="'NormName'"
                  :fieldAssignID="'NormID'" :fieldAssignNo="'NormNo'" :fieldAssignName="'NormName'"
                >
                </ijcore-modal-search-listing>
              </div>
              <label class="col-md-3 m-0">Lĩnh vực chi</label>
              <div class="col-md-9 mb-3 mb-sm-0">
                <ijcore-modal-search-listing
                  v-model="model" :title="'Định mức dự toán cơ sở'" :table="'sector'" :api="'/listing/api/common/list'"
                  :fieldID="'SectorID'" :fieldNo="'SectorNo'" :fieldName="'SectorName'"
                  :fieldAssignID="'SectorID'" :fieldAssignNo="'SectorNo'" :fieldAssignName="'SectorName'"
                >
                </ijcore-modal-search-listing>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-md-3" for="Note">Ghi chú</label>
              <div class="col-md-21">
                <textarea v-model="model.Note" class="form-control" id="Note" rows="3" placeholder="Ghi chú" name="Note"></textarea>
              </div>
            </div>
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
import Select2 from 'v-select2-component'
import IjcoreModalListing from "../../../../components/IjcoreModalListing";
import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
import ExpenseModalSearchInputCatelist from "@/views/ijlisting/expense/partials/ExpenseModalSearchInputCatelist";
import IjcoreModalSysTemParent from "../../../../components/IjcoreModalSystemParent";
import ExpenseModalSearchUom from "@/views/ijlisting/expense/partials/ExpenseModalSearchUom";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";

const ListRouter = 'listing-expense';
const EditRouter = 'listing-expense-edit';
const ViewRouter = 'listing-expense-view';
const CreateRouter = 'listing-expense-create';
const ViewApi = 'listing/api/expense/view';
const EditApi = 'listing/api/expense/edit';
const CreateApi = 'listing/api/expense/create';
const StoreApi = 'listing/api/expense/store';
const UpdateApi = 'listing/api/expense/update';
const ListApi = 'listing/api/expense';

export default {
  name: 'listing-view-item',
  data () {
    return {
      BudgetBalanceTypeOption: {
        '1': 'Trong CĐNS',
        '2': 'Ngoài CĐNS',
      },
      BudgetStateTypeOption: {
        '1': 'Trong ngân sách',
        '2': 'Ngoài ngân sách',
      },
      idParams: this.idParamsProps,
      reqParams: this.reqParamsProps,
      model: {
        SbiItemID: null,
        SbiItemNo: '',
        SbiItemName: '',
        ExpenseID: null,
        ExpenseNo: '',
        ExpenseName: '',
        ParentID: null,
        ParentNo: '',
        ParentName: null,
        Note: '',
        NOrder: '',
        Inactive: false,
        UomID: null,
        UomName: '',
        UomOption: [],
        ExpenseCate: [],
        BudgetBalanceType: 1,
        BudgetStateType: 1,
        NormID: null,
        NormNo: '',
        NormName: '',
        SectorID: null,
        SectorNo: '',
        SectorName:'',
      },
      stage: {
        isNotification: false,
        updatedData: false
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
    IjcoreModalListing,
    ExpenseModalSearchUom,
    Select2,
    ExpenseModalSearchInputCatelist,
    IjcoreModalSysTemParent,
    IjcoreModalSearchInput,
    IjcoreModalSearchListing
  },
  beforeCreate() {},
  mounted() {
    this.fetchData();
  },
  updated() {
    this.stage.updatedData = true;
  },
  computed: {
    itemNo(){
      let index = 0;
      index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
      return index;
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
      if(this.idParams){
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
          responsesData.data.data = self.itemCopy.data.data;
        }

        if (responsesData.status === 1) {

          if (self.idParams || !_.isEmpty(self.itemCopy)) {
            if (_.isObject(responsesData.data.data)) {
              self.model.ExpenseID = responsesData.data.data.ExpenseID;
              self.model.ParentID = responsesData.data.data.ParentID;
              self.model.ExpenseName = responsesData.data.data.ExpenseName;
              self.model.ExpenseNo = responsesData.data.data.ExpenseNo;
              self.model.Note = responsesData.data.data.Note;
              self.model.UomID = responsesData.data.data.UomID;
              self.model.UomName = responsesData.data.data.UomName;
              self.model.SectorID = responsesData.SectorID;
              self.model.SectorNo = responsesData.SectorNo;
              self.model.SectorName = responsesData.SectorName;
              self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
            }

            if (!_.isEmpty(self.itemCopy)) {
              self.model.ExpenseNo = '';
            }
          }else {
            self.model.ExpenseNo = '';
          }


          if (_.isArray(responsesData.data.expense)) {

            self.model.ExpenseOption = [];
            _.forEach(responsesData.data.expense, function (value, key) {
              let tmpObj = {};
              tmpObj.id = value.ExpenseID;
              tmpObj.text = value.ExpenseName;
              self.model.ExpenseOption.push(tmpObj);
            });
          }

          if (_.isArray(responsesData.data.uom)) {

            self.model.UomOption = [];
            _.forEach(responsesData.data.uom, function (value, key) {
              let tmpObj = {};
              tmpObj.id = value.UomID;
              tmpObj.text = value.UomName;
              self.model.UomOption.push(tmpObj);
            });
          }
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });
    },
    changeParent(){
      let self = this;
      let urlApi = this.api;
      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;

        this.model.ExpenseNo = responseData.data;
        self.$store.commit('isLoading', false);
      }, (error) => {
        self.$store.commit('isLoading', false);
      });
    },
    onNavigationItem(type) {
      let currentIndex = this.reqParams.idsArray.indexOf(this.idParams);
      let newIndex = (type === 'prev') ? currentIndex - 1 : currentIndex + 1;

      if (newIndex === (this.reqParams.idsArray.length) && (this.reqParams.currentPage != this.reqParams.lastPage)) {
        this.reqParams.currentPage = this.reqParams.currentPage + 1;
        this.getItemIds(type);
      } else if (newIndex < 0 && this.reqParams.currentPage > 1){
        this.reqParams.currentPage = this.reqParams.currentPage - 1;
        this.getItemIds(type);
      }
      else {
        this.idParams = (this.reqParams.idsArray[newIndex]) ? this.reqParams.idsArray[newIndex] : this.idParams;
      }
    },
    getItemIds(type){
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

      if (this.reqParams.search.expenseNo !== '') {
        requestData.data.ExpenseNo = this.reqParams.search.expenseNo;
      }
      if (this.reqParams.search.expenseName !== '') {
        requestData.data.ExpenseName = this.reqParams.search.expenseName;
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
            self.reqParams.idsArray.push(value.ExpenseID);
          });

          (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
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
        url: StoreApi + '',
        data: {
          ExpenseNo: this.model.ExpenseNo,
          ExpenseName: this.model.ExpenseName,
          SbiItemID: this.model.SbiItemID,
          SbiItemNo: this.model.SbiItemNo,
          SbiItemName: this.model.SbiItemName,
          Inactive: (this.model.Inactive) ? 1 : 0,
          UomID: this.model.UomID,
          UomName: this.model.UomName,
          Note: this.model.Note,
          ParentID: this.model.ParentID,
          ParentNo: this.model.ParentNo,
          ParentName: this.model.ParentName,
          ExpenseCate: this.model.ExpenseCate,
          BudgetBalanceType: this.model.BudgetBalanceType,
          BudgetStateType: this.model.BudgetStateType,
          NormID: this.model.NormID,
          NormNo: this.model.NormNo,
          NormName: this.model.NormName,
          SectorID: this.model.SectorID,
          SectorNo: this.model.SectorNo,
          SectorName: this.model.SectorName,
        }
      };

      // edit user
      if (this.idParams) {
        requestData.data.ItemID = this.idParams;
        requestData.url = UpdateApi + '/' + this.idParams;
      }

      this.$store.commit('isLoading', true);
      ApiService.setHeader();
      ApiService.customRequest(requestData).then((responses) => {
        let responsesData = responses.data;
        if (responsesData.status === 1) {
          debugger
          if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
          let item = {
            ExpenseID: responsesData.data,
            ExpenseNo: self.model.ExpenseNo,
            ExpenseName: self.model.ExpenseName,
            SbiItemID: self.model.SbiItemID,
            SbiItemNo: self.model.SbiItemNo,
            SbiItemName: self.model.SbiItemName,
            Inactive: (self.model.Inactive) ? 1 : 0,
            UomName: self.model.UomName,
            UomID: self.model.UomID,
            Note: self.model.Note,
            ParentID: self.model.ParentID,
            ExpenseCate: self.model.ExpenseCate,
            SectorID: self.model.SectorID,
            SectorNo: self.model.SectorNo,
            SectorName: self.model.SectorName,
          }
          let indexold =  _.findIndex(self.$route.params.req.itemsArray, {'ExpenseID': item.ExpenseID});
          let indexParent = null;
          if(indexold  < 0){
            if(self.model.ParentID){
              indexParent = _.findIndex(self.$route.params.req.itemsArray, {'ExpenseID': self.model.ParentID});
              if(indexParent >= 0){
                let ClassParentBeforUpdate = self.$route.params.req.itemsArray[indexParent].Class;
                if(ClassParentBeforUpdate == 'fa fa-plus-square-o'){
                  self.$route.params.req.itemsArray[indexParent].Class = 'fa fa-minus-square-o';
                  self.getListChild(self.$route.params.req.itemsArray[indexParent].ExpenseID);
                } else {
                  self.$set(self.$route.params.req.itemsArray[indexParent], 'Detail', 0);
                  self.$set(self.$route.params.req.itemsArray[indexParent], 'Class', 'fa fa-minus-square-o');
                  item.Level = self.$route.params.req.itemsArray[indexParent].Level + 1;
                  self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, indexParent + 1, item);
                };
              }
            } else {
              item.Level = 1;
              item.Detail = 1;
              self.$route.params.req.itemsArray.push(item);
              _.orderBy(self.$route.params.req.itemsArray, ['CompanyID'], 'asc');
            }
          }
          self.onBackToList('Bản ghi đã được cập nhật');
        }else if (responsesData.status === 4){
          Swal.fire(
            'Lỗi',
            'Không được sửa bản ghi Tổng hợp',
            'error'
          )
        } else {
          let htmlErrors = __.renderErrorApiHtml(responsesData.data);
          Swal.fire(
            'Thông báo',
            htmlErrors,
            'error'
          )
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

    onEditClicked(){
      this.$router.push({
        name: EditRouter,
        params: {id: this.idParams, req: this.reqParams}
      });
    },
    onCreateClicked(){
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
          params: {id: self.idParams, req: params, message: message}
        });
      } else {
        this.$router.push({
          name: ListRouter,
          query: query,
          params: params
        });
      }
    },

    updateModel() {
      if (this.stage.updatedData) {
        this.$forceUpdate();
      }
    },

    autoCorrectedTaxRatePipe() {

    },
    getListChild(ExpenseID){
      let self = this;
      let requestData = {
        method: 'post',
        url: 'listing/api/expense/get-list-child',
        data: {
          per_page: this.perPage,
          page: this.currentPage,
          ParentID: ExpenseID,
        },
      };

      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;
        if (responseData.status === 1) {
          debugger
          let listChild = _.toArray(responseData.data);
          _.map(listChild, function(v, k){
            if(v.Detail == 0){
              v.Class = "fa fa-plus-square-o";
            } else {
              v.Class = "";
            }
            return v;
          });
          let keyParent = _.findIndex(self.$route.params.req.itemsArray, ['ExpenseID', ExpenseID]);
          _.forEach(listChild, function (val, key){
            self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, keyParent + 1, val );
          });
          _.orderBy(self.$route.params.req.itemsArray,'ExpenseNo','asc');
        };
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });
      this.isBackToList = false;
    },


  },
  watch: {
    idParams() {
      this.fetchData();
    },

  }
}
</script>
<!--<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>-->
<style lang="css">
.select2-container{
  width: 100% !important;
}
</style>

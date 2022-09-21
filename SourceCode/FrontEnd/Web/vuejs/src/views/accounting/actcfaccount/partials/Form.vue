<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i>Sửa tài khoản kết chuyển </span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i>Thêm tài khoản kết chuyển </span>
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
              <label for="CoaType" class="col-md-3 m-0">Tên HTTK</label>
              <div class="col-md-9">
                <ijcore-modal-search-listing id="CoaType"
                  v-model="model" :title="'Hệ thống tài khoản'" :table="'coa_type'" :api="'/listing/api/common/list'"
                  :fieldID="'CoaTypeID'" :fieldNo="'CoaTypeNo'" :fieldName="'CoaTypeName'"
                  :fieldAssignID="'CoaTypeID'" :fieldAssignNo="'CoaTypeNo'" :fieldAssignName="'CoaTypeName'"
                >
                </ijcore-modal-search-listing>
              </div>
              <label class="col-md-3" for="CFAccountType">Loại số dư</label>
              <div class="col-md-9">
                <b-form-select id="CFAccountType" v-model="model.CFAccountType" :options="model.CFAccountTypeOption"></b-form-select>
              </div>
            </div>
            <div class="form-group row" v-if="model.CoaTypeID !== null">
              <label class="col-md-3" for="FromAccount">Số TK từ</label>
              <div class="col-md-9">
                <IjcoreModalAccounting id="FromAccount"
                                       v-model="model" :title="model.CoaTypeName" :api="'/listing/api/common/list'"
                                       :table="model.SearchTable" :FieldID="'FromAccountID'" :FieldName="'FromAccountName'" :FieldNo="'FromAccountNo'" :FieldType="2" :columnID="model.SearchField + 'ID'" :columnNo="model.SearchField + 'No'" :columnName="model.SearchField + 'Name'">
                </IjcoreModalAccounting>
              </div>
              <label class="col-md-3" for="ToAccount">Số TK đến</label>
              <div class="col-md-9">
                <IjcoreModalAccounting id="ToAccount"
                                       v-model="model" :title="model.CoaTypeName" :api="'/listing/api/common/list'"
                                       :table="model.SearchTable" :FieldID="'ToAccountID'" :FieldName="'ToAccountName'" :FieldNo="'ToAccountNo'" :FieldType="2" :columnID="model.SearchField + 'ID'" :columnNo="model.SearchField + 'No'" :columnName="model.SearchField + 'Name'">
                </IjcoreModalAccounting>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-md-3" for="Description">Diễn giải</label>
              <div class="col-md-21">
                <textarea v-model="model.Description" class="form-control" id="Description" rows="3" placeholder="Diễn giải" name="Description"></textarea>
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
import IjcoreModalParent from "../../../../components/IjcoreModalParent";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";
import IjcoreModalAccounting from "@/components/IjcoreModalAccounting";

const ListRouter = 'accounting-actcfaccount';
const EditRouter = 'accounting-actcfaccount-edit';
const ViewRouter = 'accounting-actcfaccount-view';
const CreateRouter = 'accounting-actcfaccount-create';
const ViewApi = 'accounting/api/actcfaccount/view';
const EditApi = 'accounting/api/actcfaccount/edit';
const CreateApi = 'accounting/api/actcfaccount/create';
const StoreApi = 'accounting/api/actcfaccount/store';
const UpdateApi = 'accounting/api/actcfaccount/update';
const ListApi = 'accounting/api/actcfaccount';

export default {
  name: 'accounting-view-item',
  data () {
    return {
      idParams: this.idParamsProps,
      reqParams: this.reqParamsProps,
      model: {
        CFAccountID: null,
        CoaTypeID: null,
        CoaTypeNo: '',
        CoaTypeName: '',
        FromAccountID: null,
        FromAccountNo: '',
        ToAccountID: null,
        ToAccountNo: '',
        CFAccountType: 1,
        Description: '',
        Norder: null,
        Inactive: false,
        SearchTable: '',
        SearchField: '',

        CFAccountTypeOption: [],
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
    Select2,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    IjcoreModalSearchListing,
    IjcoreModalAccounting,
  },
  beforeCreate() {},
  // created(){
  // },
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
          responsesData = self.itemCopy;
        }

        if (responsesData.status === 1) {

          if (self.idParams || !_.isEmpty(self.itemCopy)) {
            if (_.isObject(responsesData.data.data)) {
              self.model.CFAccountID = responsesData.data.data.CFAccountID;
              self.model.CoaTypeID = responsesData.data.data.CoaTypeID;
              self.model.CoaTypeNo = responsesData.data.data.CoaTypeNo;
              self.model.CoaTypeName = responsesData.data.data.CoaTypeName;
              self.model.FromAccountID = responsesData.data.data.FromAccountID;
              self.model.FromAccountNo = responsesData.data.data.FromAccountNo;
              self.model.ToAccountID = responsesData.data.data.ToAccountID;
              self.model.ToAccountNo = responsesData.data.data.ToAccountNo;
              self.model.Norder = responsesData.data.data.Norder;
              self.model.CFAccountType = responsesData.data.data.CFAccountType;
              self.model.Description = responsesData.data.data.Description;
              self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
            }
            if (!_.isEmpty(self.itemCopy)) {

            }
          }
          self.model.CFAccountTypeOption = responsesData.CFAccountTypeOption;
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
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

      if (this.reqParams.search.CoaTypeName !== '') {
        requestData.data.CoaTypeName = this.reqParams.search.CoaTypeName;
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
            self.reqParams.idsArray.push(value.CFAccountID);
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
        url: StoreApi,
        data: {
          CoaTypeID: this.model.CoaTypeID,
          CoaTypeNo: this.model.CoaTypeNo,
          CoaTypeName: this.model.CoaTypeName,
          FromAccountID: this.model.FromAccountID,
          FromAccountNo: this.model.FromAccountNo,
          ToAccountID: this.model.ToAccountID,
          ToAccountNo: this.model.ToAccountNo,
          CFAccountType: this.model.CFAccountType,
          Norder: this.model.Norder,
          Inactive: (this.model.Inactive) ? 1 : 0,
          Description: this.model.Description,
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
          if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
          self.$router.push({
            name: ViewRouter,
            params: {id: self.idParams, message: 'Bản ghi đã được cập nhật!'}
          });
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
    onBackToList() {
      this.$router.push({name: ListRouter});
    },

    updateModel() {
      if (this.stage.updatedData) {
        this.$forceUpdate();
      }
    },

    autoCorrectedTaxRatePipe() {

    },
    changeUserContact() {
      let employee = _.find(this.model.EmployeeOption, ['id', Number(this.model.EmployeeID)]);
      if (employee) {
        this.model.ContactName = employee.text;
      }
    },
    selectCoaType(val){
      switch (Number(val)){
        case 1:
          this.model.SearchTable = 'coa_con';
          this.model.SearchField = 'ConAccount';
          break;
        case 2:
          this.model.SearchTable = 'coa_tab';
          this.model.SearchField = 'TabAccount'
          break;
        case 3:
          this.model.SearchTable = 'coa_sna';
          this.model.SearchField = 'SnaAccount'
          break;
        case 4:
          this.model.SearchTable = 'coa_anu';
          this.model.SearchField = 'AnuAccount'
          break;
        case 5:
          this.model.SearchTable = 'coa_pmu';
          this.model.SearchField = 'PmuAccount'
          break;
        case 6:
          this.model.SearchTable = 'coa_scb';
          this.model.SearchField = 'ScbAccount'
          break;
        case 7:
          this.model.SearchTable = 'coa_eas';
          this.model.SearchField = 'EasAccount'
          break;
        case 8:
          this.model.SearchTable = 'coa_snr';
          this.model.SearchField = 'SnrAccount'
          break;
        case 9:
          this.model.SearchTable = 'coa_sia';
          this.model.SearchField = 'SiaAccount'
          break;
        case 10:
          this.model.SearchTable = 'coa_pcf';
          this.model.SearchField = 'PcfAccount';
          break;
        case 11:
          this.model.SearchTable = 'coa_ldi';
          this.model.SearchField = 'LdiAccount'
          break;
        default:
          this.model.SearchTable = '';
          this.model.SearchField = '';
          break;
      }
    }

  },
  watch: {
    idParams() {
      this.fetchData();
    },
    'model.CoaTypeNo'(newVal){
      this.selectCoaType(newVal);
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

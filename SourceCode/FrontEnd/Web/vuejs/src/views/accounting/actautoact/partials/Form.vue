<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Nghiệp vụ hạch toán tự động<span v-if="model.AutoactName">:</span> {{model.AutoactName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Nghiệp vụ hạch toán tự động<span v-if="model.AutoactName">:</span> {{model.AutoactName}}</span>
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
              <div class="col-md-21 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                <input v-model="model.AutoactName" type="text" id="AutoactName" class="form-control" placeholder="Tên nghiệp vụ hạch toán tự động" name="AutoactName"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 mb-0" for="CoaType">Loại HTTK</label>
              <div class="col-md-5">
                <IjcoreModalAccounting id="CoaType"
                                       v-model="model" :title="'Loại hệ thống tài khoản'" :api="'/listing/api/common/list'"
                                       :table="'coa_type'" :FieldID="'CoaTypeID'" :FieldName="'CoaTypeName'" :FieldNo="'CoaTypeNo'" :FieldType="2" :columnID="'CoaTypeID'" :columnNo="'CoaTypeNo'" :columnName="'CoaTypeName'">
                </IjcoreModalAccounting>
              </div>
              <label class="col-md-3 mb-0" for="DebitAccount">Tài khoản nợ</label>
              <div class="col-md-5">
                <IjcoreModalAccounting id="DebitAccount"
                                       v-model="model" :title="'Tài khoản nợ'" :api="'/listing/api/common/list'"
                                       :table="table" :FieldID="'DebitAccountID'" :FieldName="'DebitAccountName'" :FieldNo="'DebitAccountNo'" :FieldType="2" :columnID="'AccountID'" :columnNo="'AccountNo'" :columnName="'AccountName'">
                </IjcoreModalAccounting>
              </div>
              <label class="col-md-3 mb-0" for="CreditAccount">Tài khoản có</label>
              <div class="col-md-5">
                <IjcoreModalAccounting id="CreditAccount"
                                       v-model="model" :title="'Tài khoản nợ'" :api="'/listing/api/common/list'"
                                       :table="table" :FieldID="'CreditAccountID'" :FieldName="'CreditAccountName'" :FieldNo="'CreditAccountNo'" :FieldType="2" :columnID="'AccountID'" :columnNo="'AccountNo'" :columnName="'AccountName'">
                </IjcoreModalAccounting>
              </div>
            </div>
            <div class="form-group row align-center">
              <label class="col-md-3 mb-0">Loại nghiệp vụ</label>
              <div class="col-md-5">
                <ijcore-modal-search-listing
                  v-model="model" :title="'Loại nghiệp vụ hạch toán tự động'" :table="'sys_left_menu'" :api="'accounting/api/actautoact/get-autoact-type'"
                  :fieldID="'MenuID'"  :fieldName="'MenuName'"
                  :fieldAssignID="'SysAutoactTypeID'"  :fieldAssignName="'SysAutoactTypeName'"
                  :hideFiedNoOrID="true"
                >
                </ijcore-modal-search-listing>
              </div>
              <label class="col-md-3 mb-0">Loại Thu/Chi</label>
              <div class="col-md-5">
                <b-form-select v-model="model.AutoactType" :options="AutoactTypeOptions"></b-form-select>
              </div>
            </div>
            <div class="form-group row align-items-center">
                <label class="col-md-3 mb-0" for="Description">Diễn giải</label>
                <div class="col-md-21" id="Description">
                  <input v-model="model.Description" placeholder="Diễn giải" type="text" class="form-control">
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
import IjcoreModalAccounting from "@/components/IjcoreModalAccounting";
import IjcoreModalSearchListing from "../../../../components/IjcoreModalSearchListing";

const ListRouter = 'accounting-actautoact';
const EditRouter = 'accounting-actautoact-edit';
const ViewRouter = 'accounting-actautoact-view';
const CreateRouter = 'accounting-actautoact-create';
const ViewApi = 'accounting/api/actautoact/view';
const EditApi = 'accounting/api/actautoact/edit';
const CreateApi = 'accounting/api/actautoact/create';
const StoreApi = 'accounting/api/actautoact/store';
const UpdateApi = 'accounting/api/actautoact/update';
const ListApi = 'accounting/api/actautoact';

export default {
  name: 'accounting-view-item',
  data () {
    return {
      idParams: this.idParamsProps,
      reqParams: this.reqParamsProps,
      model: {
        AutoactID: null,
        AutoactName: '',
        DebitAccountID: null,
        DebitAccountNo: '',
        DebitAccountName: '',
        CreditAccountID: null,
        CreditAccountNo: '',
        CreditAccountName: '',
        Description: '',
        CoaTypeID: 1,
        CoaTypeNo: '01',
        CoaTypeName: 'Hợp nhất',
        Norder: null,
        Inactive: false,
        SysAutoactTypeID: null,
        SysAutoactTypeName: '',
        AutoactType : null,
      },
      AutoactTypeOptions : [
        {value : null, text : '--- Chọn loại thu chi ---'},
        {value : 1, text : 'Thu'},
        {value : 2, text : 'Chi'},
        {value : 3, text : 'Thu và chi'},
      ],
      stage: {
        isNotification: false,
        updatedData: false
      },
      table: 'coa_con'
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
    IjcoreModalAccounting,
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
              self.model.AutoactID = responsesData.data.data.AutoactID;
              self.model.AutoactName = responsesData.data.data.AutoactName;
              self.model.DebitAccountID = responsesData.data.data.DebitAccountID;
              self.model.DebitAccountNo = responsesData.data.data.DebitAccountNo;
              self.model.DebitAccountName = responsesData.data.data.DebitAccountName;
              self.model.CreditAccountID = responsesData.data.data.CreditAccountID;
              self.model.CreditAccountNo = responsesData.data.data.CreditAccountNo;
              self.model.CreditAccountName = responsesData.data.data.CreditAccountName;
              self.model.Description = responsesData.data.data.Description;
              self.model.CoaTypeID = responsesData.data.data.CoaTypeID;
              self.model.CoaTypeNo = responsesData.data.data.CoaTypeNo;
              self.model.CoaTypeName = responsesData.data.data.CoaTypeName;
              self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
              self.model.SysAutoactTypeID = responsesData.data.data.SysAutoactTypeID;
              self.model.SysAutoactTypeName = responsesData.data.data.SysAutoactTypeName;
              self.model.AutoactType = responsesData.data.data.AutoactType;
            }
            if (!_.isEmpty(self.itemCopy)) {

            }
          }
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

      if (this.reqParams.search.AutoactName !== '') {
        requestData.data.AutoactName = this.reqParams.search.AutoactName;
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
            self.reqParams.idsArray.push(value.AutoactID);
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
          AutoactName: this.model.AutoactName,
          DebitAccountID: this.model.DebitAccountID,
          DebitAccountNo: this.model.DebitAccountNo,
          CreditAccountID: this.model.CreditAccountID,
          CreditAccountNo: this.model.CreditAccountNo,
          Description: this.model.Description,
          CoaTypeID: this.model.CoaTypeID,
          CoaTypeNo: this.model.CoaTypeNo,
          CoaTypeName: this.model.CoaTypeName,
          Norder: this.model.Norder,
          SysAutoactTypeID: this.model.SysAutoactTypeID,
          SysAutoactTypeName: this.model.SysAutoactTypeName,
          Inactive: (this.model.Inactive) ? 1 : 0,
          AutoactType: this.model.AutoactType
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
          console.log(self.idParams)
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
    }

  },
  watch: {
    idParams() {
      this.fetchData();
    },
    'model.CoaTypeNo'(){
      this.model.DebitAccountID = null;
      this.model.DebitAccountNo = '';
      this.model.CreditAccountID = null;
      this.model.CreditAccountNo = '';
      switch(this.model.CoaTypeNo){
        case '01':
          this.table = 'coa_con';
          break;
        case '02':
          this.table = 'coa_tab';
          break;
        case '03':
          this.table = 'coa_sna';
          break;
        case '04':
          this.table = 'coa_anu';
          break;
        case '05':
          this.table = 'coa_pmu';
          break;
        case '06':
          this.table = 'coa_scb';
          break;
        case '07':
          this.table = 'coa_eas';
          break;
        case '08':
          this.table = 'coa_eas';
          break;
        case '09':
          this.table = 'coa_eas';
          break;
        case '10':
          this.table = 'coa_snr';
          break;
        case '11':

          break;
        case '12':
          this.table = 'coa_sia';
          break;
        case '13':
          this.table = 'coa_pcf';
          break;
        default:
          this.table = 'coa_ldi';
          break;

      }
    }
  }
}
</script>
<!--<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>-->
<style lang="css">
.select2-container{
  width: 100% !important;
}
</style>

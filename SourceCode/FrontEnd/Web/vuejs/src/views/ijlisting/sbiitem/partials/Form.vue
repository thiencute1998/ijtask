<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Mục - Tiểu mục<span v-if="model.SbiItemName">:</span> {{model.SbiItemName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Mục - Tiểu mục<span v-if="model.SbiItemName">:</span> {{model.SbiItemName}}</span>
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
              <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                <input v-model="model.SbiItemName" type="text" id="SbiItemName" class="form-control" placeholder="Tên mục - tiểu mục" name="SbiItemName"/>
              </div>
              <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.SbiItemNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Là mục con của</label>
              <div class="col-md-9">
                <IjcoreModalParent v-model="model" :title="'Mục - Tiểu mục'" :api="'/listing/api/common/get-parent'" :table="'sbi_item'" :fieldID="'SbiItemID'" :fieldNo="'SbiItemNo'" :fieldName="'SbiItemName'" :placeholderInput="'Chọn mục - tiểu mục cha'" :placeholderSearch="'Nhập tên mục - tiểu mục cha'" :specialTable="'SbiItem'">
                </IjcoreModalParent>
              </div>
              <label class="col-md-3 m-0">Quyền truy cập</label>
              <div class="col-md-9">
                <b-form-select v-model="model.AccessType" :options="AccessTypeOptions" id="item-uom">
                </b-form-select>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Loại</label>
              <div class="col-md-9">
                <b-form-select v-model="model.SbiItemType" :options="ItemTypeOption">
                </b-form-select>
              </div>
              <label class="col-md-3 m-0">Nhóm</label>
              <div class="col-md-9">
                <b-form-select v-model="model.SbiItemGroup" :options="ItemGroupOption">
                </b-form-select>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Khoản thu</label>
              <div class="col-md-9">
                <ijcore-modal-search-listing
                  v-model="model" :title="'Khoản thu'" :table="'revenue'" :api="'/listing/api/common/list'"
                  :fieldID="'RevenueID'" :fieldNo="'RevenueID'" :fieldName="'RevenueName'"
                  :fieldAssignID="'RevenueID'" :fieldAssignNo="'RevenueID'" :fieldAssignName="'RevenueName'"
                >
                </ijcore-modal-search-listing>
              </div>
              <label class="col-md-3 m-0">Khoản chi</label>
              <div class="col-md-9">
                <ijcore-modal-search-listing
                  v-model="model" :title="'Khoản chi'" :table="'expense'" :api="'/listing/api/common/list'"
                  :fieldID="'ExpenseID'" :fieldNo="'ExpenseNo'" :fieldName="'ExpenseName'"
                  :fieldAssignID="'ExpenseID'" :fieldAssignNo="'ExpenseNo'" :fieldAssignName="'ExpenseName'"
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
import IjcoreModalParent from "../../../../components/IjcoreModalParent";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";

const ListRouter = 'listing-sbi-item';
const EditRouter = 'listing-sbi-item-edit';
const ViewRouter = 'listing-sbi-item-view';
const CreateRouter = 'listing-sbi-item-create';
const ViewApi = 'listing/api/sbi-item/view';
const EditApi = 'listing/api/sbi-item/edit';
const CreateApi = 'listing/api/sbi-item/create';
const StoreApi = 'listing/api/sbi-item/store';
const UpdateApi = 'listing/api/sbi-item/update';
const ListApi = 'listing/api/sbi-item';

export default {
  name: 'listing-view-item',
  data () {
    return {
      idParams: this.idParamsProps,
      reqParams: this.reqParamsProps,
      model: {
        SbiItemID: null,
        SbiItemNo: '',
        SbiItemName: '',
        ParentID: null,
        SbiItemType: 1,
        SbiItemGroup: 1,
        RevenueID: null,
        RevenueName: '',
        ExpenseID: null,
        ExpenseName: '',
        Note: '',
        Prefix: '',
        Suffix: '',
        Inactive: false,
        AccessType: 1,
      },
      AccessTypeOptions:{
        1: 'Chia sẻ',
        2: 'Công khai',
        3: 'Riêng tư'
      },
      ItemTypeOption: [],
      ItemGroupOption: [],
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
              self.model.SbiItemID = responsesData.data.data.SbiItemID;
              self.model.SbiItemName = responsesData.data.data.SbiItemName;
              self.model.SbiItemNo = responsesData.data.data.SbiItemNo;
              self.model.Note = responsesData.data.data.Note;
              self.model.Prefix = responsesData.data.data.Prefix;
              self.model.Suffix = responsesData.data.data.Suffix;
              self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
            }
          }
          self.ItemTypeOption = responsesData.ItemTypeOption;
          self.ItemGroupOption = responsesData.ItemGroupOption;
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
      let requestData = {
        method: 'post',
        url: '/listing/api/common/auto-child',
        data: {
          per_page: 10,
          page: this.currentPage,
          table: 'sbi_item',
          ParentID: this.model.ParentID,
        },

      };

      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;

        this.model.SbiItemNo = responseData.data;
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

      if (this.reqParams.search.sbi_itemNo !== '') {
        requestData.data.SbiItemNo = this.reqParams.search.sbi_itemNo;
      }
      if (this.reqParams.search.sbi_itemName !== '') {
        requestData.data.SbiItemName = this.reqParams.search.sbi_itemName;
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
            self.reqParams.idsArray.push(value.SbiItemID);
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
          SbiItemNo: this.model.SbiItemNo,
          SbiItemName: this.model.SbiItemName,
          ParentID: this.model.ParentID,
          SbiItemType: this.model.SbiItemType,
          SbiItemGroup: this.model.SbiItemGroup,
          RevenueID: this.model.SbiItemGroup,
          RevenueName: this.model.RevenueName,
          ExpenseID: this.model.ExpenseID,
          ExpenseName: this.model.ExpenseName,
          Inactive: (this.model.Inactive) ? 1 : 0,
          Note: this.model.Note,
          AccessType: this.model.AccessType,
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
  }
}
</script>
<!--<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>-->
<style lang="css">
.select2-container{
  width: 100% !important;
}
</style>

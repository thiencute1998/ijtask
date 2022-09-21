<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Lĩnh vực chi<span v-if="model.SectorName">:</span> {{model.SectorName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Lĩnh vực chi<span v-if="model.SectorName">:</span> {{model.SectorName}}</span>
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
                <input v-model="model.SectorName" type="text" id="SectorName" class="form-control" placeholder="Tên lĩnh vực chi" name="SectorName"/>
              </div>
              <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.SectorNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Là mục con: </label>
              <div class="col-md-17">
                <IjcoreModalSysTemParent v-model="model" :title="'Lĩnh vực chi'" :api="'/listing/api/common/list'" :table="'sector'" :fieldName="'SectorName'" :fieldNo="'SectorNo'" :fieldID="'SectorID'" :placeholderInput="'Chọn lĩnh vực chi cha'" :placeholderSearch="'Nhập tên lĩnh vực chi'" :columnID="'SectorID'" :columnNo="'SectorNo'" :columnName="'SectorName'">
                </IjcoreModalSysTemParent>
              </div>
              <div v-if="model.ParentID" class="col-lg-4 col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.ParentNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Loại lĩnh vực chi</label>
              <div class="col-md-21">
                <sector-modal-search-input-catelist
                  v-model="model.SectorCate"
                  :listApi="'listing/api/sector/get-sector-cate-list'"
                  title-modal="Loại lĩnh vực chi"
                  placeholder="Loại lĩnh vực chi"
                ></sector-modal-search-input-catelist>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" >Loại khoản</label>
              <div class="col-md-9 mb-3 mb-sm-0">
                <ijcore-modal-search-listing
                  v-model="model" :title="'Loại khoản'" :table="'sbi_category'" :api="'/listing/api/common/list'"
                  :fieldID="'SbiCategoryID'" :fieldNo="'SbiCategoryNo'" :fieldName="'SbiCategoryName'"
                  :fieldAssignID="'SbiCategoryID'" :fieldAssignNo="'SbiCategoryNo'" :fieldAssignName="'SbiCategoryName'"
                >
                </ijcore-modal-search-listing>
              </div>
              <label class="col-md-3 m-0">Quyền truy cập</label>
              <div class="col-md-5">
                <b-form-select v-model="model.AccessType" :options="AccessTypeOptions" id="item-uom">
                </b-form-select>
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
import SectorModalSearchInputCatelist from "@/views/ijlisting/sector/partials/SectorModalSearchInputCatelist";
import IjcoreModalParent from "../../../../components/IjcoreModalParent";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";
import IjcoreModalSysTemParent from "../../../../components/IjcoreModalSystemParent";


const ListRouter = 'listing-sector';
const EditRouter = 'listing-sector-edit';
const ViewRouter = 'listing-sector-view';
const CreateRouter = 'listing-sector-create';
const ViewApi = 'listing/api/sector/view';
const EditApi = 'listing/api/sector/edit';
const CreateApi = 'listing/api/sector/create';
const StoreApi = 'listing/api/sector/store';
const UpdateApi = 'listing/api/sector/update';
const ListApi = 'listing/api/sector';


export default {
  name: 'listing-view-item',
  data () {
    return {

      idParams: this.idParamsProps,
      reqParams: this.reqParamsProps,
      model: {
        SectorID: null,
        SectorNo: '',
        SectorName: '',
        ParentID:null,
        ParentNo:'',
        ParentName: '',
        SbiCategoryID: null,
        SbiCategoryNo: '',
        SbiCategoryName: '',
        Note: '',
        Inactive: false,
        AccessType: 1,
        SectorCate: [],

      },
      AccessTypeOptions:{
        1: 'Chia sẻ',
        2: 'Công khai',
        3: 'Riêng tư'
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
    SectorModalSearchInputCatelist,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    IjcoreModalSearchListing,
    IjcoreModalSysTemParent,
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
              self.model.SectorID = responsesData.data.data.SectorID;
              self.model.SectorName = responsesData.data.data.SectorName;
              self.model.ParentID = responsesData.data.data.ParentID;
              self.model.ParentNo = responsesData.data.data.ParentNo;
              self.model.ParentName = responsesData.data.data.ParentName;
              self.model.SectorNo = responsesData.data.data.SectorNo;
              self.model.SbiCategoryID = responsesData.data.data.SbiCategoryID;
              self.model.SbiCategoryNo = responsesData.data.data.SbiCategoryNo;
              self.model.SbiCategoryName = responsesData.data.data.SbiCategoryName;
              self.model.Note = responsesData.data.data.Note;
              self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
            }

            if (!_.isEmpty(self.itemCopy)) {
              self.model.SectorNo = responsesData.data.auto;
            }
          }else {
            self.model.SectorNo = responsesData.data.auto;
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
      let requestData = {
        method: 'post',
        url: '/listing/api/common/auto-child',
        data: {
          per_page: 10,
          page: this.currentPage,
          table: 'sector',
          ParentID: this.model.ParentID,
        },

      };

      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;

        this.model.SectorNo = responseData.data;
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

      if (this.reqParams.search.sectorNo !== '') {
        requestData.data.SectorNo = this.reqParams.search.sectorNo;
      }
      if (this.reqParams.search.sectorName !== '') {
        requestData.data.SectorName = this.reqParams.search.sectorName;
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
            self.reqParams.idsArray.push(value.SectorID);
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
      debugger
      const requestData = {
        method: 'post',
        url: StoreApi +'?XDEBUG_SESSION_START=PHPSTORM',
        data: {
          SectorNo: this.model.SectorNo,
          SectorName: this.model.SectorName,
          ParentID: this.model.ParentID,
          ParentNo: this.model.ParentNo,
          ParentName: this.model.ParentName,
          Inactive: (this.model.Inactive) ? 1 : 0,
          SbiCategoryID: this.model.SbiCategoryID,
          SbiCategoryNo: this.model.SbiCategoryNo,
          SbiCategoryName: this.model.SbiCategoryName,
          Note: this.model.Note,
          AccessType: this.model.AccessType,
          SectorCate: this.model.SectorCate
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

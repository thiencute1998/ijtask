<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Chỉ thị<span v-if="model.DirectionName">:</span> {{model.DirectionName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Chỉ thị<span v-if="model.DirectionName">:</span> {{model.DirectionName}}</span>
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
                <input v-model="model.DirectionName" type="text" id="DirectionName" class="form-control" placeholder="Tên chỉ thị" name="DirectionName"/>
              </div>
              <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.DirectionNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Là con của</label>
              <div class="col-md-15">
                <IjcoreModalParent v-model="model" :title="'Chỉ thị'" :api="'/listing/api/common/list'" :table="'direction'" :fieldID="'DirectionID'" :fieldNo="'DirectionNo'" :fieldName="'DirectionName'" :placeholderInput="'Chọn chỉ thị cha'" :placeholderSearch="'Nhập tên chỉ thị'">
                </IjcoreModalParent>
              </div>
              <div v-if="model.ParentID" class="col-lg-6 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.ParentNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Loại chỉ thị</label>
              <div class="col-md-21">
                <direction-modal-search-input-catelist
                  v-model="model.DirectionCate"
                  title-modal="Loại chỉ thị"
                  placeholder="Loại chỉ thị"
                ></direction-modal-search-input-catelist>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Cơ quan ban hành</label>
              <div class="col-md-5 mb-3 mb-sm-0">
                <IjcoreModalSearchCompany v-model="model.CompanyIssuedName" :fieldCateList="'39'" :fieldCateValue="[]" :title="'Cơ quan ban hành'" :table="'company'" :api="'/listing/api/company/get-cate-value'"  :placeholderInput="'Cơ quan ban hành'" @changeCompanyIssued="updateCompanyIssued"></IjcoreModalSearchCompany>
              </div>
              <label class="col-md-3 m-0" >Người ký</label>
              <div class="col-md-5">
                <input v-model="model.SignerIssuedName" type="text" class="form-control" placeholder="Người ký">
              </div>
              <label class="col-md-3 m-0">Quyền truy cập</label>
              <div class="col-md-5">
                <b-form-select v-model="model.AccessType" :options="AccessTypeOptions" id="item-uom">
                </b-form-select>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Ngày ban hành</label>
              <div class="col-md-5 mb-3 mb-sm-0">
                <IjcoreDatePicker v-model="model.DirectionDate"></IjcoreDatePicker>
              </div>
              <label class="col-md-3 m-0">Đã đóng</label>
              <div class="col-md-5">
                <b-form-checkbox v-model="model.Closed"></b-form-checkbox>
              </div>
              <label class="col-md-3 m-0">Ngày đóng</label>
              <div class="col-md-5">
                <IjcoreDatePicker v-model="model.ClosedDate"></IjcoreDatePicker>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-md-3" for="Description">Mô tả</label>
              <div class="col-md-21">
                <textarea v-model="model.Description" class="form-control" id="Description" rows="3" placeholder="Mô tả" name="Description"></textarea>
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
import DirectionModalSearchInputCatelist from "./DirectionModalSearchInputCatelist";
import IjcoreModalParent from "../../../../components/IjcoreModalParent";
import IjcoreDatePicker from '@/components/IjcoreDatePicker';
import IjcoreModalSearchCompany from "@/components/IjcoreModalSearchCompany";
import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";

const ListRouter = 'listing-direction';
const EditRouter = 'listing-direction-edit';
const ViewRouter = 'listing-direction-view';
const CreateRouter = 'listing-direction-create';
const ViewApi = 'listing/api/direction/view';
const EditApi = 'listing/api/direction/edit';
const CreateApi = 'listing/api/direction/create';
const StoreApi = 'listing/api/direction/store';
const UpdateApi = 'listing/api/direction/update';
const ListApi = 'listing/api/direction';

export default {
  name: 'listing-view-item',
  data () {
    return {
      idParams: this.idParamsProps,
      reqParams: this.reqParamsProps,
      model: {
        DirectionID: null,
        DirectionNo: '',
        DirectionName: '',
        ParentID: null,
        ParentNo: '',
        ParentName: null,
        DirectionDate: null,
        CompanyIssuedID: null,
        CompanyIssuedName: '',
        SignerIssuedID: null,
        SignerIssuedName: '',
        Closed: false,
        ClosedDate: null,
        Description: '',
        NumberValue: 1,
        Inactive: false,
        AccessType: 1,
        DirectionCate: [],
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
    DirectionModalSearchInputCatelist,
    IjcoreModalParent,
    IjcoreModalSearchInput,
    IjcoreDatePicker,
    IjcoreModalSearchCompany,
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
              self.model.DirectionID = responsesData.data.data.DirectionID;
              self.model.ParentID = responsesData.data.data.ParentID;
              self.model.DirectionName = responsesData.data.data.DirectionName;
              self.model.DirectionNo = responsesData.data.data.DirectionNo;
              self.model.DirectionDate = responsesData.data.data.DirectionDate;
              self.model.Description = responsesData.data.data.Description;
              self.model.NumberValue = responsesData.data.data.NumberValue;
              self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
            }
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
          table: 'direction',
          ParentID: this.model.ParentID,
        },

      };

      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;

        this.model.DirectionNo = responseData.data;
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

      if (this.reqParams.search.directionNo !== '') {
        requestData.data.DirectionNo = this.reqParams.search.directionNo;
      }
      if (this.reqParams.search.directionName !== '') {
        requestData.data.DirectionName = this.reqParams.search.directionName;
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
            self.reqParams.idsArray.push(value.DirectionID);
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
          DirectionNo: this.model.DirectionNo,
          DirectionName: this.model.DirectionName,
          ParentID: this.model.ParentID,
          Inactive: (this.model.Inactive) ? 1 : 0,
          DirectionDate: this.model.DirectionDate,
          CompanyIssuedID: this.model.CompanyIssuedID,
          CompanyIssuedName: this.model.CompanyIssuedName,
          // SignerIssuedID: this.model.SignerIssuedID,
          SignerIssuedName: this.model.SignerIssuedName,
          Closed: this.model.Closed,
          ClosedDate: this.model.ClosedDate,
          Description: this.model.Description,
          AccessType: this.model.AccessType,
          NumberValue: this.model.NumberValue,
          DirectionCate: this.model.DirectionCate
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
    updateCompanyIssued(data){
      this.model.CompanyIssuedID = data.CompanyID;
      this.model.CompanyIssuedName = data.CompanyName;
    }

  },
  watch: {
    idParams() {
      this.fetchData();
    },
    // 'model.ParentID'(){
    //   let self = this;
    //   let urlApi = '/listing/api/common/auto-child';
    //   let requestData = {
    //     method: 'post',
    //     url: urlApi,
    //     data: {
    //       per_page: 10,
    //       page: this.currentPage,
    //       table: 'direction',
    //       ParentID: this.model.ParentID,
    //     }
    //   }
    //   self.$store.commit('isLoading',true)
    //   ApiService.setHeader();
    //   ApiService.customRequest(requestData)
    //     .then(response=>{
    //       let responseData = response.data;
    //       if(responseData.status === 1){
    //         self.model.DirectionNo = responseData.data;
    //       }
    //       self.$store.commit('isLoading',false)
    //     }).catch(error=> {
    //     self.$store.commit('isLoading',false)
    //   })
    // }
  }
}
</script>
<!--<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>-->
<style lang="css">
.select2-container{
  width: 100% !important;
}
.mx-datepicker{
  width: 100%;
}
</style>

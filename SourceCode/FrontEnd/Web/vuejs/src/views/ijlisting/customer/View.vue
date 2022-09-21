<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Khách hàng: {{Customer.CustomerName}}</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onCreateClicked"><i class="fa fa-plus"></i> Thêm</b-button>
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
                <!--                                <span>{{itemNo}} - {{itemTotalPerPage}}</span>-->
                <!--                                /-->
                <!--                                <span>{{itemTotal}}</span>-->
                <span>{{itemNo}} - {{itemTotal}}</span>
              </div>
              <b-button-group id="main-header-views" class="main-header-views">
                <b-button id="tooltip-view-prev" @click="onNavigationItem('prev')" v-if="reqParams" class="main-header-view"><i class="fa fa-angle-left"></i></b-button>
                <b-button id="tooltip-view-next" @click="onNavigationItem('next')" v-if="reqParams" class="main-header-view"><i class="fa fa-angle-right"></i></b-button>
                <b-button id="tooltip-view-back-to-list" @click="onBackToList" class="main-header-view"><i class="fa fa-list"></i></b-button>
                <b-tooltip container="main-header-views" target="tooltip-view-back-to-list">Danh sách</b-tooltip>
                <b-tooltip container="main-header-views" target="tooltip-view-prev">Trước</b-tooltip>
                <b-tooltip container="main-header-views" target="tooltip-view-next">Sau</b-tooltip>
              </b-button-group>
              <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
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
            <div class="form-group row ij-line-head">
              <label class="col-md-4">Thông tin chung</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <customer-general-modal v-model="Customer" :title="'Khách hàng : ' + Customer.CustomerName" @changeDefaultModel="changeItemCopy"></customer-general-modal>
                  <a class="ij-a-icon" @click="showGeneralInfo = !showGeneralInfo">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showGeneralInfo"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showGeneralInfo">
              <customer-general-view v-model="Customer"></customer-general-view>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4">Phân quyền</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <div class="float-right">
                    <customer-per-modal v-model="Customer" :title="'Phân quyền: ' + Customer.CustomerName" @changed="fetchData" :per="CustomerPer" :EmployeeOption="EmployeeOption" :CompanyOption="CompanyOption" :GroupOption="GroupOption"></customer-per-modal>
                    <a @click="showCustomerPer = !showCustomerPer" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCustomerPer"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <b-collapse v-model="showCustomerPer">
              <div class="form-group row">
                <div class="col-md-24 m-0">
                  <customer-per-view v-model="Customer" :per="CustomerPer" :EmployeeOption="EmployeeOptionArr"></customer-per-view>
                </div>
              </div>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4" @click="onToggleCustomerLink">Danh mục liên kết</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <CustomerLinkModal @on:get-data="onToggleCustomerLink(false)" v-model="CustomerLink" :title="'Danh mục liên kết'" :Customer="Customer"></CustomerLinkModal>
                  <a class="ij-a-icon" @click="onToggleCustomerLink">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showCustomerLink"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showCustomerLink">
              <CustomerLinkContent v-model="CustomerLink"></CustomerLinkContent>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4" @click="onToggleCustomerFile">Tệp</label>
              <div class="col-md-20">
                <div class="float-right">
                  <span @click="downloadAllFile" style="font-size: 18px; cursor: pointer" title="Tải tất cả tệp"><i class="fa fa-download"></i></span>
                  <ijcore-upload-multiple-file v-on:changed="changeFile" :isIcon="true"></ijcore-upload-multiple-file>
                  <a @click="onToggleCustomerFile" class="ij-a-icon">
                    <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCustomerFile"
                       title="Thu gọn"></i>
                    <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                       title="Mở rộng"></i>
                  </a>
                </div>

              </div>
            </div>
            <b-collapse v-model="showCustomerFile">
              <div class="form-group row">
                <div class="col-md-24 m-0">
                  <customer-file-view v-model="CustomerFile" :Customer="Customer"></customer-file-view>
                </div>
              </div>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4" @click="onToggleCustomerVideo">Phim</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <div class="float-right">
                    <IjcoreUploadMultipleVideo v-on:changed="changeVideo" :isIcon="true"></IjcoreUploadMultipleVideo>
                    <a @click="onToggleCustomerVideo" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCustomerVideo"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <b-collapse v-model="showCustomerVideo">
              <div class="form-group row">
                <div class="col-md-24 m-0">
                  <CustomerVideoView v-model="CustomerVideo" :Customer="Customer"></CustomerVideoView>
                </div>
              </div>
            </b-collapse>
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
import CustomerGeneralView from "./partials/CustomerGeneralView";
import CustomerGeneralModal from "./partials/CustomerGeneralModal";
import CustomerLinkModal from "./partials/CustomerLinkModal";
import CustomerLinkContent from "./partials/CustomerLinkContent";
import IjcoreUploadMultipleFile from "../../../components/IjcoreUploadMultipleFile";
import CustomerFileView from "./partials/CustomerFileView";
import IjcoreUploadMultipleVideo from "@/components/IjcoreUploadMultipleVideo";
import CustomerVideoView from "./partials/CustomerVideoView";
import CustomerPerView from "./partials/CustomerPerView";
import CustomerPerModal from "./partials/CustomerPerModal";

const ListRouter = 'listing-customer';
const EditRouter = 'listing-customer-edit';
const CreateRouter = 'listing-customer-create';
const ViewApi = 'listing/api/customer/view';
const ListApi = 'listing/api/customer';
const DeleteApi = 'listing/api/customer/delete';
const UpdateApi = 'listing/api/customer/update';

export default {
  name: 'listing-view-item',
  data () {
    return {
      showGeneralInfo: true,
      showCustomerLink: false,
      showCustomerFile: false,
      showCustomerVideo: false,
      showCustomerPer: false,
      Customer: {
        CustomerID: null,
        CustomerNo: '',
        CustomerName: '',
        Address: '',
        BillTo: '',
        ShipTo: '',
        TaxCode: '',
        BankAccount: '',
        BankName: '',
        OfficePhone: '',
        Website: '',
        Province: {},
        District: {},
        Commune: {},
        Fax: '',
        Email: '',
        Note: '',
        isVendor: false,
        Prefix: '',
        Suffix: '',
        Inactive: false,
        AccessType: null,
        UserIDCreated: null,
        AuthorizedPerson: null,
        CustomerCate: [],
        CustomerCateList: [],
        CustomerCateValue: [],
      },
      CustomerLink: [],
      CustomerFile: [],
      CustomerVideo: [],
      CustomerPer: {},
      EmployeeOption: [],
      EmployeeOptionArr: [],
      CompanyOption: [],
      GroupOption: [],
      idParams: this.$route.params.id,
      reqParams: this.$route.params.req,
      defaultModel: {},
      stage: {
        updatedData: false,
        message: (this.$route.params.message) ? this.$route.params.message : ''
      }
    }

  },

  components: {
    CustomerGeneralView,
    CustomerGeneralModal,
    CustomerLinkModal,
    CustomerLinkContent,
    IjcoreUploadMultipleFile,
    CustomerFileView,
    IjcoreUploadMultipleVideo,
    CustomerVideoView,
    CustomerPerView,
    CustomerPerModal,
  },
  beforeCreate() {

    this.$router.onReady(() => {
      if (!this.$route.params.id) {
        this.$router.push({name: ListRouter});
      }
    });
  },
  created(){
  },
  beforeMount(){},
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
    itemNo(){
      if (!this.idParams) return;
      let index = 0;
      index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
      return index;
    },
    itemTotalPerPage(){
      if (!this.idParams) return;
      return this.reqParams.idsArray.length + this.reqParams.perPage * (this.reqParams.currentPage - 1);
    },
    itemTotal(){
      if (!this.idParams) return;
      return this.reqParams.total;

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
      if(this.idParams){
        urlApi = ViewApi + '/' + this.idParams;
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
          self.Customer.CustomerID = responsesData.data.data.CustomerID;
          self.Customer.CustomerName = responsesData.data.data.CustomerName;
          self.Customer.CustomerNo = responsesData.data.data.CustomerNo;
          self.Customer.Address = responsesData.data.data.Address;
          self.Customer.Fax = responsesData.data.data.Fax;
          self.Customer.Email = responsesData.data.data.Email;
          self.Customer.Note = responsesData.data.data.Note;
          self.Customer.BillTo = responsesData.data.data.BillTo;
          self.Customer.ShipTo = responsesData.data.data.ShipTo;
          self.Customer.TaxCode = responsesData.data.data.TaxCode;
          self.Customer.BankAccount = responsesData.data.data.BankAccount;
          self.Customer.BankName = responsesData.data.data.BankName;
          self.Customer.OfficePhone = responsesData.data.data.OfficePhone;
          self.Customer.Website = responsesData.data.data.Website;
          self.Customer.Province.ProvinceID = responsesData.data.data.ProvinceID;
          self.Customer.Province.ProvinceName = responsesData.data.data.ProvinceName;
          self.Customer.District.DistrictID = responsesData.data.data.DistrictID;
          self.Customer.District.DistrictName = responsesData.data.data.DistrictName;
          self.Customer.Commune.CommuneID = responsesData.data.data.CommuneID;
          self.Customer.Commune.CommuneName = responsesData.data.data.CommuneName;
          self.Customer.isVendor = (responsesData.data.data.isVendor) ? true : false;
          self.Customer.Prefix = responsesData.data.data.Prefix;
          self.Customer.Suffix = responsesData.data.data.Suffix;
          self.Customer.Inactive = (responsesData.data.data.Inactive) ? true : false;
          self.Customer.AccessType = responsesData.data.data.AccessType;
          self.Customer.UserIDCreated = responsesData.data.data.UserIDCreated;
          self.Customer.AuthorizedPerson = responsesData.data.data.AuthorizedPerson;

          self.Customer.CustomerCate = [];
          self.$set(self.Customer,'CustomerCate',[]);
          if(responsesData.data.CustomerCate){
            _.forEach(responsesData.data.CustomerCate, (customerCate, key)=>{
              let tmpObj = {};
              if(customerCate.CateID){
                let cateList = _.find(responsesData.data.CustomerCateList, ['CateID', customerCate.CateID]);
                if(cateList){
                  tmpObj.CateID = cateList.CateID;
                  tmpObj.CateName = cateList.CateName;
                }
              }
              if(customerCate.CateValue){
                // let cateValue = _.find(responsesData.data.CustomerCateValue, (cate)=> {
                //   return cate.CateID === customerCate.CateID && cate.CateValue === customerCate.CateValue;
                // });
                let cateValue = _.find(responsesData.data.CustomerCateValue,{
                  CateID: customerCate.CateID,
                  CateValue: customerCate.CateValue
                });
                if(cateValue){
                  tmpObj.CateValue = cateValue.CateValue;
                  tmpObj.Description = cateValue.Description;
                }
              }
              // self.Customer.CustomerCate.push(tmpObj);
              self.$set(self.Customer.CustomerCate, self.Customer.CustomerCate.length, tmpObj);
            })
          }

          // Employee
          self.EmployeeOption = [];
          self.EmployeeOptionArr = [];
          _.forEach(responsesData.Employee, function (val, key) {
            if(val.EmployeeID === self.Customer.AuthorizedPerson){
              self.Customer.EmployeeName = val.EmployeeName;
            }
            self.EmployeeOptionArr[val.EmployeeID] = val.EmployeeName;
            let tmpObj = {};
            tmpObj.id = val.EmployeeID;
            tmpObj.text = val.EmployeeName;
            self.EmployeeOption.push(tmpObj);
          });
          //Customer
          self.CompanyOption = [];
          _.forEach(responsesData.Company, function (val, key) {
            let tmpObj = {};
            tmpObj.id = val.CompanyID;
            tmpObj.text = val.CompanyName;
            self.CompanyOption.push(tmpObj);
          });
          //Group
          self.GroupOption = [];
          _.forEach(responsesData.Group, function (val, key) {
            let tmpObj = {};
            tmpObj.id = val.UserGroupID;
            tmpObj.text = val.UserGroupName;
            self.GroupOption.push(tmpObj);
          });
          _.forEach(responsesData.CustomerPer, function (val, key) {
            responsesData.CustomerPer[key].Access = (responsesData.CustomerPer[key].Access) ? true : false;
            responsesData.CustomerPer[key].Edit = (responsesData.CustomerPer[key].Edit) ? true : false;
            responsesData.CustomerPer[key].Delete = (responsesData.CustomerPer[key].Delete) ? true : false;
            responsesData.CustomerPer[key].Create = (responsesData.CustomerPer[key].Create) ? true : false;
          });
          self.CustomerPer = responsesData.CustomerPer;
          self.CustomerPerEmployee = responsesData.CustomerPerEmployee;

        }
        else if(responsesData.status === 3){
          self.$router.push({name: ListRouter, params: {message: responsesData.msg}});
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

      if (this.reqParams.search.CustomerNo !== '') {
        requestData.data.CustomerNo = this.reqParams.search.CustomerNo;
      }
      if (this.reqParams.search.CustomerName !== '') {
        requestData.data.CustomerName = this.reqParams.search.CustomerName;
      }
      if (this.reqParams.search.Address !== '') {
        requestData.data.Address = this.reqParams.search.Address;
      }
      if (this.reqParams.search.Fax !== '') {
        requestData.data.Fax = this.reqParams.search.Fax;
      }
      if (this.reqParams.search.Email !== '') {
        requestData.data.Email = this.reqParams.search.Email;
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
            self.reqParams.idsArray.push(value.CustomerID);
          });

          (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
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
      let params = (this.$route.params.req) ? this.$route.params.req:{};
      let query = this.$route.query;
      query.isBackToList = true;
      if (_.isString(message)) {
        params.message = message;
        this.$router.push({name: ListRouter, query: query, params: params});
      } else {
        this.$router.push({name: ListRouter, query: query, params: params});
      }

    },
    handleCopyItem(){
      this.$router.push({name: CreateRouter, params: {itemCopy: this.defaultModel}});
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
              console.log(response);
            }
          }, (error) => {
            console.log(error);

          });

        }
      });
    },

    updateModel() {
      if (this.stage.updatedData) {
        this.$forceUpdate();
      }
    },
    onToggleCustomerLink(toggle = true){
      let self = this;
      if(!this.CustomerLink.length){
        let requestData = {
          method: 'get',
          url: '/listing/api/customer/get-customer-link/' + this.$route.params.id,
          data: {}
        }

        self.$store.commit('isLoading',false);
        ApiService.setHeader();
        ApiService.customRequest(requestData)
          .then(response =>{
            let responseData = response.data;
            self.CustomerLink = responseData.data;
          })
          .catch(error=>{
            console.log(error);

          })
      }
      if(toggle){
        this.showCustomerLink = !this.showCustomerLink;
      }
    },
    onToggleCustomerFile(toggle = true){
      let self = this;
      if(!this.CustomerFile.length){
        let requestData = {
          method: 'get',
          url: 'listing/api/customer/get-customer-file/' + this.Customer.CustomerID,
          data:{}
        }

        ApiService.setHeader();
        ApiService.customRequest(requestData)
          .then(response=> {
            let reponseData = response.data;
            console.log(reponseData)
            if(reponseData.status === 1){
              self.CustomerFile = reponseData.data;
            }
          })
          .catch(error=>{
            console.log(error)
          })
      }
      if (toggle) {
        this.showCustomerFile = !this.showCustomerFile;
      }
    },
    downloadAllFile(){
      let self = this;
      let requestData = {
        url: 'listing/api/customer/download-all-file/' + this.Customer.CustomerID,
        method: 'get',
        data: {}
      };

      this.$store.commit('isLoading', true);
      ApiService.setHeader();
      ApiService.customRequest(requestData).then((responses) => {
        self.$store.commit('isLoading', false);
        let responsesData = responses.data;
        if (responsesData.status === 1) {
          let link = document.createElement('a');
          link.href = self.$store.state.appRootApi + responsesData.data;
          link.download = 'Archive.zip';
          link.click();
        } else {
          self.$bvToast.toast(responsesData.msg, {
            title: 'Thông báo',
            variant: 'warning',
            solid: true
          });
        }
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });
    },
    changeFile(files) {
      let self = this;
      let dateC = __.convertDateTimeToString(new Date());
      for (let i = 0; i < files.length; i++) {
        self.$store.commit('isLoading', true);
        let file = files[i];
        let formData = new FormData();
        formData.append('LineID', '');
        formData.append('FileUpload', file);
        formData.append('CustomerID', self.Customer.CustomerID);
        formData.append('FileName', file.name);
        formData.append('Description', file.name);
        formData.append('FileType', file.name.split('.').pop());
        formData.append('FileSize', file.size);
        formData.append('DateModified', dateC);
        formData.append('UserModified', '');
        formData.append('changeFile', 1);
        formData.append('changeData', 1);

        let currentObj = this;
        const config = {
          headers: {
            'content-type': 'multipart/form-data',
          }
        };

        // send upload request
        axios.post('listing/api/customer/customer-upload-file/' + self.Customer.CustomerID, formData, config)
          .then(function (response) {
            let responseData = response.data;
            if (responseData.status === 1) {
              currentObj.success = response.data.success;
              currentObj.filename = "";
              let dataR = response.data.data;
              self.CustomerFile.push({
                LineID: dataR.LineID,
                FileUpload: file,
                CustomerID: dataR.CustomerID,
                FileID: dataR.FileID,
                FileName: dataR.FileName,
                Description: dataR.FileName,
                FileType: dataR.FileType,
                FileSize: dataR.FileSize,
                DateModified: dateC,
                UserModified: dataR.UserModified,
                Link: dataR.Link,
                DateModifiedRoot: '',
                FileNameRoot: '',
                changeFile: 0,//Đã thay đổi file
                changeData: 0
              });

              self.$bvToast.toast('Tải lên thành công', {
                title: 'Thông báo',
                variant: 'success',
                solid: true
              });
              self.showCustomerFile = true;
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Thông báo',
                text: responseData.msg,
                confirmButtonText: 'Đóng'
              });
            }
            self.$store.commit('isLoading', false);
          })
          .catch(function (error) {
            // currentObj.output = error;
          });
      }

    },
    onToggleCustomerVideo(toggle = true) {
      let self = this;
      if (!this.CustomerVideo.length) {
        let requestData = {
          method: 'get',
          url: 'listing/api/customer/get-customer-video/' + this.Customer.CustomerID,
          data: {}
        };
        // Api edit user
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.CustomerVideo = responsesData.data;
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      }
      if (toggle) {
        this.showCustomerVideo = !this.showCustomerVideo;
      }
    },
    changeVideo(videos) {
      let self = this;
      let dateC = __.convertDateTimeToString(new Date());
      for (var i = 0; i < videos.length; i++) {
        self.$store.commit('isLoading', true);
        var video = videos[i];
        let formData = new FormData();
        formData.append('LineID', '');
        formData.append('VideoUpload', video);
        formData.append('CustomerID', self.Customer.CustomerID);
        formData.append('VideoName', video.name);
        formData.append('Description', video.name);
        formData.append('VideoType', video.name.split('.').pop());
        formData.append('VideoSize', video.size);
        formData.append('DateModified', dateC);
        formData.append('UserModified', '');
        formData.append('changeVideo', 1);
        formData.append('changeData', 1);

        let currentObj = this;
        const config = {
          headers: {
            'content-type': 'multipart/form-data',
          }
        };

        // send upload request
        axios.post('listing/api/customer/customer-upload-video/' + self.Customer.CustomerID, formData, config)
          .then(function (response) {
            let responseData = response.data;
            if (responseData.status === 1) {
              currentObj.success = response.data.success;
              currentObj.videoname = "";
              let dataR = response.data.data;
              self.CustomerVideo.push({
                LineID: dataR.LineID,
                VideoUpload: video,
                CustomerID: dataR.CustomerID,
                FileID: dataR.FileID,
                VideoName: dataR.VideoName,
                Description: dataR.VideoName,
                VideoType: dataR.VideoType,
                VideoSize: dataR.VideoSize,
                DateModified: dateC,
                UserModified: dataR.UserModified,
                Link: dataR.Link,
                DateModifiedRoot: '',
                FileNameRoot: '',
                changeVideo: 0,//Đã thay đổi file
                changeData: 0
              });
              self.$bvToast.toast('Tải lên thành công', {
                title: 'Thông báo',
                variant: 'success',
                solid: true
              });
              self.showCustomerVideo = true;
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Thông báo',
                text: responseData.msg,
                confirmButtonText: 'Đóng'
              });
            }
            self.$store.commit('isLoading', false);
          })
          .catch(function (error) {
            // currentObj.output = error;
          });
      }

    },
    changeItemCopy(result){
      this.defaultModel.data.data = result.data;
    },
  },
  watch: {
    // idParams() {
    //     this.fetchData();
    // },
    '$route.params.id': function (id) {
      this.idParams = id;
      this.fetchData();
    },
    'Customer.ParentID'(){
      let self = this;
      let urlApi = '/listing/api/common/auto-child';
      let requestData = {
        method: 'post',
        url: urlApi,
        data: {
          per_page: 10,
          page: this.currentPage,
          table: 'customer',
          ParentID: this.Customer.ParentID,
        }
      }
      self.$store.commit('isLoading',true)
      ApiService.setHeader();
      ApiService.customRequest(requestData)
        .then(response=>{
          let responseData = response.data;
          if(responseData.status === 1){
            this.Customer.CustomerNo = responseData.data;
          }
          self.$store.commit('isLoading',false)
        }).catch(error=> {
        self.$store.commit('isLoading',false)
      })
    }
  },
  // beforeDestroy(){
  //     window.removeEventListener('unload', this.onReloadPage)
  // }
}
</script>

<style lang="css"></style>

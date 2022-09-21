<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Mục - Tiểu mục: {{SbiItem.SbiItemName}}</span>
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
                  <sbi-item-general-modal v-model="SbiItem" :title="'Mục - Tiểu mục : ' + SbiItem.SbiItemName" @changeDefaultModel="changeItemCopy"></sbi-item-general-modal>
                  <a class="ij-a-icon" @click="showGeneralInfo = !showGeneralInfo">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showGeneralInfo"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showGeneralInfo">
              <sbi-item-general-view v-model="SbiItem"></sbi-item-general-view>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4">Phân quyền</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <div class="float-right">
                    <sbi-item-per-modal v-model="SbiItem" :title="'Phân quyền: ' + SbiItem.SbiItemName" @changed="fetchData" :per="SbiItemPer" :EmployeeOption="EmployeeOption" :CompanyOption="CompanyOption" :GroupOption="GroupOption"></sbi-item-per-modal>
                    <a @click="showSbiItemPer = !showSbiItemPer" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showSbiItemPer"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <b-collapse v-model="showSbiItemPer">
              <div class="form-group row">
                <div class="col-md-24 m-0">
                  <sbi-item-per-view v-model="SbiItem" :per="SbiItemPer" :EmployeeOption="EmployeeOptionArr"></sbi-item-per-view>
                </div>
              </div>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4" @click="onToggleSbiItemLink">Danh mục liên kết</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <SbiItemLinkModal @on:get-data="onToggleSbiItemLink(false)" v-model="SbiItemLink" :title="'Danh mục liên kết'" :SbiItem="SbiItem"></SbiItemLinkModal>
                  <a class="ij-a-icon" @click="onToggleSbiItemLink">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showSbiItemLink"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showSbiItemLink">
              <SbiItemLinkContent v-model="SbiItemLink"></SbiItemLinkContent>
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
import SbiItemGeneralView from "./partials/SbiItemGeneralView";
import SbiItemGeneralModal from "./partials/SbiItemGeneralModal";
import SbiItemLinkModal from "./partials/SbiItemLinkModal";
import SbiItemLinkContent from "./partials/SbiItemLinkContent";
import IjcoreUploadMultipleFile from "../../../components/IjcoreUploadMultipleFile";
import IjcoreUploadMultipleVideo from "@/components/IjcoreUploadMultipleVideo";
import SbiItemPerView from "./partials/SbiItemPerView";
import SbiItemPerModal from "./partials/SbiItemPerModal";

const ListRouter = 'listing-sbi-item';
const EditRouter = 'listing-sbi-item-edit';
const CreateRouter = 'listing-sbi-item-create';
const ViewApi = 'listing/api/sbi-item/view';
const ListApi = 'listing/api/sbi-item';
const DeleteApi = 'listing/api/sbi-item/delete';
const UpdateApi = 'listing/api/sbi-item/update';

export default {
  name: 'listing-view-item',
  data () {
    return {
      showGeneralInfo: true,
      showSbiItemLink: false,
      showSbiItemPer: false,
      SbiItem: {
        SbiItemID: null,
        SbiItemNo: '',
        SbiItemName: '',
        ParentID: null,
        ParentNo: '',
        ParentName: '',
        SbiItemType: null,
        SbiItemGroup: null,
        RevenueID: null,
        RevenueName: '',
        ExpenseID: null,
        ExpenseName: '',
        Note: '',
        Prefix: '',
        Suffix: '',
        Inactive: false,
        AccessType: null,
        UserIDCreated: null,
        AuthorizedPerson: null,
        ItemTypeOption: [],
        ItemGroupOption: [],
      },
      SbiItemLink: [],
      SbiItemFile: [],
      SbiItemVideo: [],
      SbiItemPer: {},
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
    SbiItemGeneralView,
    SbiItemGeneralModal,
    SbiItemLinkModal,
    SbiItemLinkContent,
    IjcoreUploadMultipleFile,
    IjcoreUploadMultipleVideo,
    SbiItemPerView,
    SbiItemPerModal,
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
          self.SbiItem.SbiItemID = responsesData.data.data.SbiItemID;
          self.SbiItem.SbiItemName = responsesData.data.data.SbiItemName;
          self.SbiItem.SbiItemNo = responsesData.data.data.SbiItemNo;
          self.SbiItem.ParentID = responsesData.data.data.ParentID;
          self.SbiItem.SbiItemType = responsesData.data.data.SbiItemType;
          self.SbiItem.SbiItemGroup = responsesData.data.data.SbiItemGroup;
          self.SbiItem.RevenueID = responsesData.data.data.RevenueID;
          self.SbiItem.RevenueName = responsesData.data.data.RevenueName;
          self.SbiItem.ExpenseID = responsesData.data.data.ExpenseID;
          self.SbiItem.ExpenseName = responsesData.data.data.ExpenseName;
          self.SbiItem.Note = responsesData.data.data.Note;
          self.SbiItem.Prefix = responsesData.data.data.Prefix;
          self.SbiItem.Suffix = responsesData.data.data.Suffix;
          self.SbiItem.Inactive = (responsesData.data.data.Inactive) ? true : false;
          self.SbiItem.AccessType = responsesData.data.data.AccessType;
          self.SbiItem.UserIDCreated = responsesData.data.data.UserIDCreated;
          self.SbiItem.AuthorizedPerson = responsesData.data.data.AuthorizedPerson;
          self.SbiItem.ParentNo = responsesData.data.Parent.ParentNo;
          self.SbiItem.ParentName = responsesData.data.Parent.ParentName;
          self.SbiItem.ItemTypeOption = responsesData.ItemTypeOption;
          self.SbiItem.ItemGroupOption = responsesData.ItemGroupOption;
          // Employee
          self.EmployeeOption = [];
          self.EmployeeOptionArr = [];
          _.forEach(responsesData.Employee, function (val, key) {
            if(val.EmployeeID === self.SbiItem.AuthorizedPerson){
              self.SbiItem.EmployeeName = val.EmployeeName;
            }
            self.EmployeeOptionArr[val.EmployeeID] = val.EmployeeName;
            let tmpObj = {};
            tmpObj.id = val.EmployeeID;
            tmpObj.text = val.EmployeeName;
            self.EmployeeOption.push(tmpObj);
          });
          //SbiItem
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
          _.forEach(responsesData.SbiItemPer, function (val, key) {
            responsesData.SbiItemPer[key].Access = (responsesData.SbiItemPer[key].Access) ? true : false;
            responsesData.SbiItemPer[key].Edit = (responsesData.SbiItemPer[key].Edit) ? true : false;
            responsesData.SbiItemPer[key].Delete = (responsesData.SbiItemPer[key].Delete) ? true : false;
            responsesData.SbiItemPer[key].Create = (responsesData.SbiItemPer[key].Create) ? true : false;
          });
          self.SbiItemPer = responsesData.SbiItemPer;
          self.SbiItemPerEmployee = responsesData.SbiItemPerEmployee;

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

      if (this.reqParams.search.SbiItemNo !== '') {
        requestData.data.SbiItemNo = this.reqParams.search.SbiItemNo;
      }
      if (this.reqParams.search.SbiItemName !== '') {
        requestData.data.SbiItemName = this.reqParams.search.SbiItemName;
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
      let params = this.$route.params.req;
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
    onToggleSbiItemLink(toggle = true){
      let self = this;
      if(!this.SbiItemLink.length){
        let requestData = {
          method: 'get',
          url: '/listing/api/sbi-item/get-sbi-item-link/' + this.$route.params.id,
          data: {}
        }

        self.$store.commit('isLoading',false);
        ApiService.setHeader();
        ApiService.customRequest(requestData)
          .then(response =>{
            let responseData = response.data;
            self.SbiItemLink = responseData.data;
          })
          .catch(error=>{
            console.log(error);

          })
      }
      if(toggle){
        this.showSbiItemLink = !this.showSbiItemLink;
      }
    },
    changeItemCopy(result){
      this.defaultModel.data.data = result.data;
    }
  },
  watch: {
    // idParams() {
    //     this.fetchData();
    // },
    '$route.params.id': function (id) {
      this.idParams = id;
      this.fetchData();
    },

  },
  // beforeDestroy(){
  //     window.removeEventListener('unload', this.onReloadPage)
  // }
}
</script>

<style lang="css"></style>

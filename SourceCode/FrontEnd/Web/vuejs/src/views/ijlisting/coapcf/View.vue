<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Hệ thống tài khoản quỹ tín dụng nhân dân: {{CoaPcf.AccountName}}</span>
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
                  <coa-pcf-general-modal v-model="CoaPcf" :title="'Hệ thống tài khoản quỹ tín dụng nhân dân : ' + CoaPcf.AccountName" ></coa-pcf-general-modal>
                  <a class="ij-a-icon" @click="showGeneralInfo = !showGeneralInfo">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showGeneralInfo"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showGeneralInfo">
              <coa-pcf-general-view v-model="CoaPcf"></coa-pcf-general-view>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4">Phân quyền</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <div class="float-right">
                    <coa-pcf-per-modal v-model="CoaPcf" :title="'Phân quyền: ' + CoaPcf.AccountName" @changed="fetchData" :per="CoaPcfPer" :EmployeeOption="EmployeeOption" :CoaPcfOption="CoaPcfOption" :GroupOption="GroupOption"></coa-pcf-per-modal>
                    <a @click="showCoaPcfPer = !showCoaPcfPer" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCoaPcfPer"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <b-collapse v-model="showCoaPcfPer">
              <div class="form-group row">
                <div class="col-md-24 m-0">
                  <coa-pcf-per-view v-model="CoaPcf" :per="CoaPcfPer" :EmployeeOption="EmployeeOptionArr"></coa-pcf-per-view>
                </div>
              </div>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4" @click="onToggleCoaPcfLink">Danh mục liên kết</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <CoaPcfLinkModal @on:get-data="onToggleCoaPcfLink(false)" v-model="CoaPcfLink" :title="'Danh mục liên kết'" :CoaPcf="CoaPcf"></CoaPcfLinkModal>
                  <a class="ij-a-icon" @click="onToggleCoaPcfLink">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showCoaPcfLink"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showCoaPcfLink">
              <CoaPcfLinkContent v-model="CoaPcfLink"></CoaPcfLinkContent>
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
import CoaPcfGeneralView from "./partials/CoaPcfGeneralView";
import CoaPcfGeneralModal from "./partials/CoaPcfGeneralModal";
import CoaPcfLinkModal from "./partials/CoaPcfLinkModal";
import CoaPcfLinkContent from "./partials/CoaPcfLinkContent";
import CoaPcfPerView from "./partials/CoaPcfPerView";
import CoaPcfPerModal from "./partials/CoaPcfPerModal";

const ListRouter = 'listing-coa-pcf';
const EditRouter = 'listing-coa-pcf-edit';
const CreateRouter = 'listing-coa-pcf-create';
const ViewApi = 'listing/api/coa-pcf/view';
const ListApi = 'listing/api/coa-pcf';
const DeleteApi = 'listing/api/coa-pcf/delete';
const UpdateApi = 'listing/api/coa-pcf/update';

export default {
  name: 'listing-view-item',
  data () {
    return {
      showGeneralInfo: true,
      showCoaPcfLink: false,

      showCoaPcfPer: false,
      CoaPcf: {
        AccountID: null,
        AccountNo: '',
        AccountName: '',
        EmployeeID: null,
        Note: '',
        ParentID: null,
        ParentNo: '',
        ParentName: '',
        Inactive: false,
        AccessType: null,
        BalanceType:null,
        AccessTypeOptions: {},
        BalanceTypeOptions: {},
        UserIDCreated: null,
        AuthorizedPerson: null,
        CoaPcfCate: [],
        CoaPcfCateList: [],
        CoaPcfCateValue: [],
      },

      CoaPcfLink: [],
      CoaPcfPer: {},
      EmployeeOption: [],
      EmployeeOptionArr: [],
      CoaPcfOption: [],
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
    CoaPcfGeneralView,
    CoaPcfGeneralModal,
    CoaPcfLinkModal,
    CoaPcfLinkContent,
    CoaPcfPerView,
    CoaPcfPerModal,
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
          self.CoaPcf.AccountID = responsesData.data.data.AccountID;
          self.CoaPcf.AccountName = responsesData.data.data.AccountName;
          self.CoaPcf.AccountNo = responsesData.data.data.AccountNo;
          self.CoaPcf.EmployeeID = responsesData.data.data.EmployeeID;
          self.CoaPcf.ContactName = responsesData.data.data.ContactName;
          self.CoaPcf.ContactTel = responsesData.data.data.ContactTel;
          self.CoaPcf.Note = responsesData.data.data.Note;
          self.CoaPcf.BalanceType = responsesData.data.data.BalanceType;
          self.CoaPcf.Inactive = (responsesData.data.data.Inactive) ? true : false;
          self.CoaPcf.ParentID = responsesData.data.data.ParentID;
          self.CoaPcf.ParentNo = responsesData.data.Parent.AccountNo;
          self.CoaPcf.ParentName = responsesData.data.Parent.AccountName;
          self.CoaPcf.AccessType = responsesData.data.data.AccessType;
          self.CoaPcf.UserIDCreated = responsesData.data.data.UserIDCreated;
          self.CoaPcf.AuthorizedPerson = responsesData.data.data.AuthorizedPerson;
          self.CoaPcf.AccessTypeOptions = responsesData.data.AccessTypeOptions;
          self.CoaPcf.BalanceTypeOptions = responsesData.data.BalanceTypeOptions;

          self.CoaPcf.CoaPcfCate = [];
          self.$set(self.CoaPcf,'CoaPcfCate',[]);
          if(responsesData.data.CoaPcfCate){
            _.forEach(responsesData.data.CoaPcfCate, (coaPcfCate, key)=>{
              let tmpObj = {};
              if(coaPcfCate.CateID){
                let cateList = _.find(responsesData.data.CoaPcfCateList, ['CateID', coaPcfCate.CateID]);
                if(cateList){
                  tmpObj.CateID = cateList.CateID;
                  tmpObj.CateName = cateList.CateName;
                }
              }
              if(coaPcfCate.CateValue){
                // let cateValue = _.find(responsesData.data.CoaPcfCateValue, (cate)=> {
                //   return cate.CateID === coaPcfCate.CateID && cate.CateValue === coaPcfCate.CateValue;
                // });
                let cateValue = _.find(responsesData.data.CoaPcfCateValue,{
                  CateID: coaPcfCate.CateID,
                  CateValue: coaPcfCate.CateValue
                });
                if(cateValue){
                  tmpObj.CateValue = cateValue.CateValue;
                  tmpObj.Description = cateValue.Description;
                }
              }
              // self.CoaPcf.CoaPcfCate.push(tmpObj);
              self.$set(self.CoaPcf.CoaPcfCate, self.CoaPcf.CoaPcfCate.length, tmpObj);
            })
          }

          // Employee
          self.EmployeeOption = [];
          self.EmployeeOptionArr = [];
          _.forEach(responsesData.Employee, function (val, key) {
            if(val.EmployeeID === self.CoaPcf.AuthorizedPerson){
              self.CoaPcf.EmployeeName = val.EmployeeName;
            }
            self.EmployeeOptionArr[val.EmployeeID] = val.EmployeeName;
            let tmpObj = {};
            tmpObj.id = val.EmployeeID;
            tmpObj.text = val.EmployeeName;
            self.EmployeeOption.push(tmpObj);
          });
          //CoaPcf
          self.CoaPcfOption = [];
          _.forEach(responsesData.CoaPcf, function (val, key) {
            let tmpObj = {};
            tmpObj.id = val.AccountID;
            tmpObj.text = val.AccountName;
            self.CoaPcfOption.push(tmpObj);
          });
          //Group
          self.GroupOption = [];
          _.forEach(responsesData.Group, function (val, key) {
            let tmpObj = {};
            tmpObj.id = val.UserGroupID;
            tmpObj.text = val.UserGroupName;
            self.GroupOption.push(tmpObj);
          });
          _.forEach(responsesData.CoaPcfPer, function (val, key) {
            responsesData.CoaPcfPer[key].Access = (responsesData.CoaPcfPer[key].Access) ? true : false;
            responsesData.CoaPcfPer[key].Edit = (responsesData.CoaPcfPer[key].Edit) ? true : false;
            responsesData.CoaPcfPer[key].Delete = (responsesData.CoaPcfPer[key].Delete) ? true : false;
            responsesData.CoaPcfPer[key].Create = (responsesData.CoaPcfPer[key].Create) ? true : false;
          });
          self.CoaPcfPer = responsesData.CoaPcfPer;
          self.CoaPcfPerEmployee = responsesData.CoaPcfPerEmployee;

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

      if (this.reqParams.search.AccountNo !== '') {
        requestData.data.AccountNo = this.reqParams.search.AccountNo;
      }
      if (this.reqParams.search.AccountName !== '') {
        requestData.data.AccountName = this.reqParams.search.AccountName;
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
            self.reqParams.idsArray.push(value.AccountID);
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
    onToggleCoaPcfLink(toggle = true){
      let self = this;
      if(!this.CoaPcfLink.length){
        let requestData = {
          method: 'get',
          url: '/listing/api/coa-pcf/get-coa-pcf-link/' + this.$route.params.id,
          data: {}
        }

        self.$store.commit('isLoading',false);
        ApiService.setHeader();
        ApiService.customRequest(requestData)
          .then(response =>{
            let responseData = response.data;
            self.CoaPcfLink = responseData.data;
          })
          .catch(error=>{
            console.log(error);

          })
      }
      if(toggle){
        this.showCoaPcfLink = !this.showCoaPcfLink;
      }
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

  },
  // beforeDestroy(){
  //     window.removeEventListener('unload', this.onReloadPage)
  // }
}
</script>

<style lang="css"></style>

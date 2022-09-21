<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Hệ thống tài khoản Quốc gia: {{CoaSna.AccountName}}</span>
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
                  <coa-sna-general-modal v-model="CoaSna" :title="'Hệ thống tài khoản Quốc gia : ' + CoaSna.AccountName" ></coa-sna-general-modal>
                  <a class="ij-a-icon" @click="showGeneralInfo = !showGeneralInfo">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showGeneralInfo"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showGeneralInfo">
              <coa-sna-general-view v-model="CoaSna"></coa-sna-general-view>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4">Phân quyền</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <div class="float-right">
                    <coa-sna-per-modal v-model="CoaSna" :title="'Phân quyền: ' + CoaSna.AccountName" @changed="fetchData" :per="CoaSnaPer" :EmployeeOption="EmployeeOption" :CoaSnaOption="CoaSnaOption" :GroupOption="GroupOption"></coa-sna-per-modal>
                    <a @click="showCoaSnaPer = !showCoaSnaPer" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCoaSnaPer"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <b-collapse v-model="showCoaSnaPer">
              <div class="form-group row">
                <div class="col-md-24 m-0">
                  <coa-sna-per-view v-model="CoaSna" :per="CoaSnaPer" :EmployeeOption="EmployeeOptionArr"></coa-sna-per-view>
                </div>
              </div>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4" @click="onToggleCoaSnaLink">Danh mục liên kết</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <CoaSnaLinkModal @on:get-data="onToggleCoaSnaLink(false)" v-model="CoaSnaLink" :title="'Danh mục liên kết'" :CoaSna="CoaSna"></CoaSnaLinkModal>
                  <a class="ij-a-icon" @click="onToggleCoaSnaLink">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showCoaSnaLink"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showCoaSnaLink">
              <CoaSnaLinkContent v-model="CoaSnaLink"></CoaSnaLinkContent>
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
import CoaSnaGeneralView from "./partials/CoaSnaGeneralView";
import CoaSnaGeneralModal from "./partials/CoaSnaGeneralModal";
import CoaSnaLinkModal from "./partials/CoaSnaLinkModal";
import CoaSnaLinkContent from "./partials/CoaSnaLinkContent";
import CoaSnaPerView from "./partials/CoaSnaPerView";
import CoaSnaPerModal from "./partials/CoaSnaPerModal";

const ListRouter = 'listing-coa-sna';
const EditRouter = 'listing-coa-sna-edit';
const CreateRouter = 'listing-coa-sna-create';
const ViewApi = 'listing/api/coa-sna/view';
const ListApi = 'listing/api/coa-sna';
const DeleteApi = 'listing/api/coa-sna/delete';
const UpdateApi = 'listing/api/coa-sna/update';

export default {
  name: 'listing-view-item',
  data () {
    return {
      showGeneralInfo: true,
      showCoaSnaLink: false,

      showCoaSnaPer: false,
      CoaSna: {
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
        CoaSnaCate: [],
        CoaSnaCateList: [],
        CoaSnaCateValue: [],
      },

      CoaSnaLink: [],
      CoaSnaPer: {},
      EmployeeOption: [],
      EmployeeOptionArr: [],
      CoaSnaOption: [],
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
    CoaSnaGeneralView,
    CoaSnaGeneralModal,
    CoaSnaLinkModal,
    CoaSnaLinkContent,
    CoaSnaPerView,
    CoaSnaPerModal,
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
          self.CoaSna.AccountID = responsesData.data.data.AccountID;
          self.CoaSna.AccountName = responsesData.data.data.AccountName;
          self.CoaSna.AccountNo = responsesData.data.data.AccountNo;
          self.CoaSna.EmployeeID = responsesData.data.data.EmployeeID;
          self.CoaSna.ContactName = responsesData.data.data.ContactName;
          self.CoaSna.ContactTel = responsesData.data.data.ContactTel;
          self.CoaSna.Note = responsesData.data.data.Note;
          self.CoaSna.BalanceType = responsesData.data.data.BalanceType;
          self.CoaSna.Inactive = (responsesData.data.data.Inactive) ? true : false;
          self.CoaSna.ParentID = responsesData.data.data.ParentID;
          self.CoaSna.ParentNo = responsesData.data.Parent.AccountNo;
          self.CoaSna.ParentName = responsesData.data.Parent.AccountName;
          self.CoaSna.AccessType = responsesData.data.data.AccessType;
          self.CoaSna.UserIDCreated = responsesData.data.data.UserIDCreated;
          self.CoaSna.AuthorizedPerson = responsesData.data.data.AuthorizedPerson;
          self.CoaSna.AccessTypeOptions = responsesData.data.AccessTypeOptions;
          self.CoaSna.BalanceTypeOptions = responsesData.data.BalanceTypeOptions;

          self.CoaSna.CoaSnaCate = [];
          self.$set(self.CoaSna,'CoaSnaCate',[]);
          if(responsesData.data.CoaSnaCate){
            _.forEach(responsesData.data.CoaSnaCate, (coaSnaCate, key)=>{
              let tmpObj = {};
              if(coaSnaCate.CateID){
                let cateList = _.find(responsesData.data.CoaSnaCateList, ['CateID', coaSnaCate.CateID]);
                if(cateList){
                  tmpObj.CateID = cateList.CateID;
                  tmpObj.CateName = cateList.CateName;
                }
              }
              if(coaSnaCate.CateValue){
                // let cateValue = _.find(responsesData.data.CoaSnaCateValue, (cate)=> {
                //   return cate.CateID === coaSnaCate.CateID && cate.CateValue === coaSnaCate.CateValue;
                // });
                let cateValue = _.find(responsesData.data.CoaSnaCateValue,{
                  CateID: coaSnaCate.CateID,
                  CateValue: coaSnaCate.CateValue
                });
                if(cateValue){
                  tmpObj.CateValue = cateValue.CateValue;
                  tmpObj.Description = cateValue.Description;
                }
              }
              // self.CoaSna.CoaSnaCate.push(tmpObj);
              self.$set(self.CoaSna.CoaSnaCate, self.CoaSna.CoaSnaCate.length, tmpObj);
            })
          }

          // Employee
          self.EmployeeOption = [];
          self.EmployeeOptionArr = [];
          _.forEach(responsesData.Employee, function (val, key) {
            if(val.EmployeeID === self.CoaSna.AuthorizedPerson){
              self.CoaSna.EmployeeName = val.EmployeeName;
            }
            self.EmployeeOptionArr[val.EmployeeID] = val.EmployeeName;
            let tmpObj = {};
            tmpObj.id = val.EmployeeID;
            tmpObj.text = val.EmployeeName;
            self.EmployeeOption.push(tmpObj);
          });
          //CoaSna
          self.CoaSnaOption = [];
          _.forEach(responsesData.CoaSna, function (val, key) {
            let tmpObj = {};
            tmpObj.id = val.AccountID;
            tmpObj.text = val.AccountName;
            self.CoaSnaOption.push(tmpObj);
          });
          //Group
          self.GroupOption = [];
          _.forEach(responsesData.Group, function (val, key) {
            let tmpObj = {};
            tmpObj.id = val.UserGroupID;
            tmpObj.text = val.UserGroupName;
            self.GroupOption.push(tmpObj);
          });
          _.forEach(responsesData.CoaSnaPer, function (val, key) {
            responsesData.CoaSnaPer[key].Access = (responsesData.CoaSnaPer[key].Access) ? true : false;
            responsesData.CoaSnaPer[key].Edit = (responsesData.CoaSnaPer[key].Edit) ? true : false;
            responsesData.CoaSnaPer[key].Delete = (responsesData.CoaSnaPer[key].Delete) ? true : false;
            responsesData.CoaSnaPer[key].Create = (responsesData.CoaSnaPer[key].Create) ? true : false;
          });
          self.CoaSnaPer = responsesData.CoaSnaPer;
          self.CoaSnaPerEmployee = responsesData.CoaSnaPerEmployee;

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
    onToggleCoaSnaLink(toggle = true){
      let self = this;
      if(!this.CoaSnaLink.length){
        let requestData = {
          method: 'get',
          url: '/listing/api/coa-sna/get-coa-sna-link/' + this.$route.params.id,
          data: {}
        }

        self.$store.commit('isLoading',false);
        ApiService.setHeader();
        ApiService.customRequest(requestData)
          .then(response =>{
            let responseData = response.data;
            self.CoaSnaLink = responseData.data;
          })
          .catch(error=>{
            console.log(error);

          })
      }
      if(toggle){
        this.showCoaSnaLink = !this.showCoaSnaLink;
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

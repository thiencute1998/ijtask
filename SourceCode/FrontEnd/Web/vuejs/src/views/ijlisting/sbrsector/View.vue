<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Lĩnh vực thu: {{SbrSector.SbrSectorName}}</span>
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
                  <sbr-sector-general-modal v-model="SbrSector" :title="'Lĩnh vực thu : ' + SbrSector.SbrSectorName" @changeDefaultModel="changeItemCopy"></sbr-sector-general-modal>
                  <a class="ij-a-icon" @click="showGeneralInfo = !showGeneralInfo">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showGeneralInfo"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showGeneralInfo">
              <sbr-sector-general-view v-model="SbrSector"></sbr-sector-general-view>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4">Phân quyền</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <div class="float-right">
                    <sbr-sector-per-modal v-model="SbrSector" :title="'Phân quyền: ' + SbrSector.SbrSectorName" @changed="fetchData" :per="SbrSectorPer" :EmployeeOption="EmployeeOption" :CompanyOption="CompanyOption" :GroupOption="GroupOption"></sbr-sector-per-modal>
                    <a @click="showSbrSectorPer = !showSbrSectorPer" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showSbrSectorPer"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <b-collapse v-model="showSbrSectorPer">
              <div class="form-group row">
                <div class="col-md-24 m-0">
                  <sbr-sector-per-view v-model="SbrSector" :per="SbrSectorPer" :EmployeeOption="EmployeeOptionArr"></sbr-sector-per-view>
                </div>
              </div>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4" @click="onToggleSbrSectorLink">Danh mục liên kết</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <SbrSectorLinkModal @on:get-data="onToggleSbrSectorLink(false)" v-model="SbrSectorLink" :title="'Danh mục liên kết'" :SbrSector="SbrSector"></SbrSectorLinkModal>
                  <a class="ij-a-icon" @click="onToggleSbrSectorLink">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showSbrSectorLink"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showSbrSectorLink">
              <SbrSectorLinkContent v-model="SbrSectorLink"></SbrSectorLinkContent>
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
import SbrSectorGeneralView from "./partials/SbrSectorGeneralView";
import SbrSectorGeneralModal from "./partials/SbrSectorGeneralModal";
import SbrSectorLinkModal from "./partials/SbrSectorLinkModal";
import SbrSectorLinkContent from "./partials/SbrSectorLinkContent";
import SbrSectorPerView from "./partials/SbrSectorPerView";
import SbrSectorPerModal from "./partials/SbrSectorPerModal";

const ListRouter = 'listing-sbr-sector';
const EditRouter = 'listing-sbr-sector-edit';
const CreateRouter = 'listing-sbr-sector-create';
const ViewApi = 'listing/api/sbr-sector/view';
const ListApi = 'listing/api/sbr-sector';
const DeleteApi = 'listing/api/sbr-sector/delete';
const UpdateApi = 'listing/api/sbr-sector/update';

export default {
  name: 'listing-view-item',
  data () {
    return {
      showGeneralInfo: true,
      showSbrSectorLink: false,
      showSbrSectorPer: false,
      SbrSector: {
        SbrSectorID: null,
        SbrSectorNo: '',
        SbrSectorName: '',
        ParentID: null,
        ParentNo: '',
        ParentName: '',
        SbiChapterID: null,
        SbiChapterName: '',
        SbiChapterNo: '',
        Note: '',
        Detail:'',
        Inactive: false,
        AccessType: null,
        UserIDCreated: null,
        AuthorizedPerson: null,
        SbrSectorCate: [],
        SbrSectorCateList: [],
        SbrSectorCateValue: [],
      },
      SbrSectorLink: [],
      SbrSectorPer: {},
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
    SbrSectorGeneralView,
    SbrSectorGeneralModal,
    SbrSectorLinkModal,
    SbrSectorLinkContent,
    SbrSectorPerView,
    SbrSectorPerModal,
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
          self.SbrSector.SbrSectorID = responsesData.data.data.SbrSectorID;
          self.SbrSector.SbrSectorName = responsesData.data.data.SbrSectorName;
          self.SbrSector.SbrSectorNo = responsesData.data.data.SbrSectorNo;
          self.SbrSector.ParentID = responsesData.data.data.ParentID;
          self.SbrSector.ParentNo = responsesData.data.data.ParentNo;
          self.SbrSector.ParentName = responsesData.data.data.ParentName;
          self.SbrSector.SbiChapterID = responsesData.data.data.SbiChapterID;
          self.SbrSector.SbiChapterNo = responsesData.data.data.SbiChapterNo;
          self.SbrSector.SbiChapterName = responsesData.data.data.SbiChapterName;
          self.SbrSector.Note = responsesData.data.data.Note;
          self.SbrSector.Detail = responsesData.data.data.Detail;
          self.SbrSector.Inactive = (responsesData.data.data.Inactive) ? true : false;
          self.SbrSector.AccessType = responsesData.data.data.AccessType;
          self.SbrSector.UserIDCreated = responsesData.data.data.UserIDCreated;
          self.SbrSector.AuthorizedPerson = responsesData.data.data.AuthorizedPerson;
          self.SbrSector.SbrSectorCate = [];
          self.$set(self.SbrSector,'SbrSectorCate',[]);
          if(responsesData.data.SbrSectorCate){
            _.forEach(responsesData.data.SbrSectorCate, (sbrSectorCate, key)=>{
              let tmpObj = {};
              if(sbrSectorCate.CateID){
                let cateList = _.find(responsesData.data.SbrSectorCateList, ['CateID', sbrSectorCate.CateID]);
                if(cateList){
                  tmpObj.CateID = cateList.CateID;
                  tmpObj.CateName = cateList.CateName;
                }
              }
              if(sbrSectorCate.CateValue){
                // let cateValue = _.find(responsesData.data.SbrSectorCateValue, (cate)=> {
                //   return cate.CateID === sbrSectorCate.CateID && cate.CateValue === sbrSectorCate.CateValue;
                // });
                let cateValue = _.find(responsesData.data.SbrSectorCateValue,{
                  CateID: sbrSectorCate.CateID,
                  CateValue: sbrSectorCate.CateValue
                });
                if(cateValue){
                  tmpObj.CateValue = cateValue.CateValue;
                  tmpObj.Description = cateValue.Description;
                }
              }
              // self.SbrSector.SbrSectorCate.push(tmpObj);
              self.$set(self.SbrSector.SbrSectorCate, self.SbrSector.SbrSectorCate.length, tmpObj);
            })
          }

          // Employee
          self.EmployeeOption = [];
          self.EmployeeOptionArr = [];
          _.forEach(responsesData.Employee, function (val, key) {
            if(val.EmployeeID === self.SbrSector.AuthorizedPerson){
              self.SbrSector.EmployeeName = val.EmployeeName;
            }
            self.EmployeeOptionArr[val.EmployeeID] = val.EmployeeName;
            let tmpObj = {};
            tmpObj.id = val.EmployeeID;
            tmpObj.text = val.EmployeeName;
            self.EmployeeOption.push(tmpObj);
          });
          //Company
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
          _.forEach(responsesData.SbrSectorPer, function (val, key) {
            responsesData.SbrSectorPer[key].Access = (responsesData.SbrSectorPer[key].Access) ? true : false;
            responsesData.SbrSectorPer[key].Edit = (responsesData.SbrSectorPer[key].Edit) ? true : false;
            responsesData.SbrSectorPer[key].Delete = (responsesData.SbrSectorPer[key].Delete) ? true : false;
            responsesData.SbrSectorPer[key].Create = (responsesData.SbrSectorPer[key].Create) ? true : false;
          });
          self.SbrSectorPer = responsesData.SbrSectorPer;
          self.SbrSectorPerEmployee = responsesData.SbrSectorPerEmployee;

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

      if (this.reqParams.search.SbrSectorNo !== '') {
        requestData.data.SbrSectorNo = this.reqParams.search.SbrSectorNo;
      }
      if (this.reqParams.search.SbrSectorName !== '') {
        requestData.data.SbrSectorName = this.reqParams.search.SbrSectorName;
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
            self.reqParams.idsArray.push(value.SbrSectorID);
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
      if (_.isString(message)) {
        this.$router.push({name: ListRouter, params: {message: message}});
      } else {
        this.$router.push({name: ListRouter});
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
    onToggleSbrSectorLink(toggle = true){
      let self = this;
      if(!this.SbrSectorLink.length){
        let requestData = {
          method: 'get',
          url: '/listing/api/sbr-sector/get-sbr-sector-link/' + this.$route.params.id,
          data: {}
        }

        self.$store.commit('isLoading',false);
        ApiService.setHeader();
        ApiService.customRequest(requestData)
          .then(response =>{
            let responseData = response.data;
            self.SbrSectorLink = responseData.data;
          })
          .catch(error=>{
            console.log(error);

          })
      }
      if(toggle){
        this.showSbrSectorLink = !this.showSbrSectorLink;
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
    'SbrSector.ParentID'(){
      let self = this;
      let urlApi = '/listing/api/common/auto-thuld';
      let requestData = {
        method: 'post',
        url: urlApi,
        data: {
          per_page: 10,
          page: this.currentPage,
          table: 'sbr_sector',
          ParentID: this.SbrSector.ParentID,
        }
      }
      self.$store.commit('isLoading',true)
      ApiService.setHeader();
      ApiService.customRequest(requestData)
        .then(response=>{
          let responseData = response.data;
          if(responseData.status === 1){
            this.SbrSector.SbrSectorNo = responseData.data;
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

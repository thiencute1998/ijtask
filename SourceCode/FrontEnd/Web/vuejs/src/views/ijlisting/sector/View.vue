<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Lĩnh vực chi: {{Sector.SectorName}}</span>
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
                  <sector-general-modal v-model="Sector" :title="'Lĩnh vực chi : ' + Sector.SectorName" @changeDefaultModel="changeItemCopy"></sector-general-modal>
                  <a class="ij-a-icon" @click="showGeneralInfo = !showGeneralInfo">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showGeneralInfo"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showGeneralInfo">
              <sector-general-view v-model="Sector"></sector-general-view>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4">Phân quyền</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <div class="float-right">
                    <sector-per-modal v-model="Sector" :title="'Phân quyền: ' + Sector.SectorName" @changed="fetchData" :per="SectorPer" :EmployeeOption="EmployeeOption" :CompanyOption="CompanyOption" :GroupOption="GroupOption"></sector-per-modal>
                    <a @click="showSectorPer = !showSectorPer" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showSectorPer"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <b-collapse v-model="showSectorPer">
              <div class="form-group row">
                <div class="col-md-24 m-0">
                  <sector-per-view v-model="Sector" :per="SectorPer" :EmployeeOption="EmployeeOptionArr"></sector-per-view>
                </div>
              </div>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4" @click="onToggleSectorLink">Danh mục liên kết</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <SectorLinkModal @on:get-data="onToggleSectorLink(false)" v-model="SectorLink" :title="'Danh mục liên kết'" :Sector="Sector"></SectorLinkModal>
                  <a class="ij-a-icon" @click="onToggleSectorLink">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showSectorLink"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showSectorLink">
              <SectorLinkContent v-model="SectorLink"></SectorLinkContent>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4" @click="onToggleSectorFile">Tệp</label>
              <div class="col-md-20">
                <div class="float-right">
                  <span @click="downloadAllFile" style="font-size: 18px; cursor: pointer" title="Tải tất cả tệp"><i class="fa fa-download"></i></span>
                  <ijcore-upload-multiple-file v-on:changed="changeFile" :isIcon="true"></ijcore-upload-multiple-file>
                  <a @click="onToggleSectorFile" class="ij-a-icon">
                    <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showSectorFile"
                       title="Thu gọn"></i>
                    <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                       title="Mở rộng"></i>
                  </a>
                </div>

              </div>
            </div>
            <b-collapse v-model="showSectorFile">
              <div class="form-group row">
                <div class="col-md-24 m-0">
                  <sector-file-view v-model="SectorFile" :Sector="Sector"></sector-file-view>
                </div>
              </div>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4" @click="onToggleSectorVideo">Phim</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <div class="float-right">
                    <IjcoreUploadMultipleVideo v-on:changed="changeVideo" :isIcon="true"></IjcoreUploadMultipleVideo>
                    <a @click="onToggleSectorVideo" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showSectorVideo"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <b-collapse v-model="showSectorVideo">
              <div class="form-group row">
                <div class="col-md-24 m-0">
                  <SectorVideoView v-model="SectorVideo" :Sector="Sector"></SectorVideoView>
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
import SectorGeneralView from "./partials/SectorGeneralView";
import SectorGeneralModal from "./partials/SectorGeneralModal";
import SectorLinkModal from "./partials/SectorLinkModal";
import SectorLinkContent from "./partials/SectorLinkContent";
import IjcoreUploadMultipleFile from "../../../components/IjcoreUploadMultipleFile";
import SectorFileView from "./partials/SectorFileView";
import IjcoreUploadMultipleVideo from "@/components/IjcoreUploadMultipleVideo";
import SectorVideoView from "./partials/SectorVideoView";
import SectorPerView from "./partials/SectorPerView";
import SectorPerModal from "./partials/SectorPerModal";

const ListRouter = 'listing-sector';
const EditRouter = 'listing-sector-edit';
const CreateRouter = 'listing-sector-create';
const ViewApi = 'listing/api/sector/view';
const ListApi = 'listing/api/sector';
const DeleteApi = 'listing/api/sector/delete';
const UpdateApi = 'listing/api/sector/update';

export default {
  name: 'listing-view-item',
  data () {
    return {
      showGeneralInfo: true,
      showSectorLink: false,
      showSectorFile: false,
      showSectorVideo: false,
      showSectorPer: false,
      Sector: {
        SectorID: null,
        SectorNo: '',
        SectorName: '',
        ParentID: null,
        ParentNo: '',
        ParentName: '',
        SbiCategoryID: '',
        SbiCategoryNo: '',
        SbiCategoryName: '',
        Note: '',
        Detail: null,
        Inactive: false,
        AccessType: null,
        UserIDCreated: null,
        AuthorizedPerson: null,
        SectorCate: [],
        SectorCateList: [],
        SectorCateValue: [],
      },
      SectorLink: [],
      SectorFile: [],
      SectorVideo: [],
      SectorPer: {},
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
    SectorGeneralView,
    SectorGeneralModal,
    SectorLinkModal,
    SectorLinkContent,
    IjcoreUploadMultipleFile,
    SectorFileView,
    IjcoreUploadMultipleVideo,
    SectorVideoView,
    SectorPerView,
    SectorPerModal,
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
          self.Sector.SectorID = responsesData.data.data.SectorID;
          self.Sector.SectorName = responsesData.data.data.SectorName;
          self.Sector.SectorNo = responsesData.data.data.SectorNo;
          self.Sector.ParentID = responsesData.data.data.ParentID;
          self.Sector.ParentNo = responsesData.data.data.ParentNo;
          self.Sector.ParentName = responsesData.data.data.ParentName;
          self.Sector.SbiCategoryID = responsesData.data.data.SbiCategoryID;
          self.Sector.SbiCategoryNo = responsesData.data.data.SbiCategoryNo;
          self.Sector.SbiCategoryName = responsesData.data.data.SbiCategoryName;
          self.Sector.Note = responsesData.data.data.Note;
          self.Sector.Detail = responsesData.data.data.Detail;
          self.Sector.Prefix = responsesData.data.data.Prefix;
          self.Sector.Suffix = responsesData.data.data.Suffix;
          self.Sector.Inactive = (responsesData.data.data.Inactive) ? true : false;
          self.Sector.AccessType = responsesData.data.data.AccessType;
          self.Sector.UserIDCreated = responsesData.data.data.UserIDCreated;
          self.Sector.AuthorizedPerson = responsesData.data.data.AuthorizedPerson;

          self.Sector.SectorCate = [];
          self.$set(self.Sector,'SectorCate',[]);
          if(responsesData.data.SectorCate){
            _.forEach(responsesData.data.SectorCate, (sectorCate, key)=>{
              let tmpObj = {};
              if(sectorCate.CateID){
                let cateList = _.find(responsesData.data.SectorCateList, ['CateID', sectorCate.CateID]);
                if(cateList){
                  tmpObj.CateID = cateList.CateID;
                  tmpObj.CateName = cateList.CateName;
                }
              }
              if(sectorCate.CateValue){
                // let cateValue = _.find(responsesData.data.SectorCateValue, (cate)=> {
                //   return cate.CateID === sectorCate.CateID && cate.CateValue === sectorCate.CateValue;
                // });
                let cateValue = _.find(responsesData.data.SectorCateValue,{
                  CateID: sectorCate.CateID,
                  CateValue: sectorCate.CateValue
                });
                if(cateValue){
                  tmpObj.CateValue = cateValue.CateValue;
                  tmpObj.Description = cateValue.Description;
                }
              }
              // self.Sector.SectorCate.push(tmpObj);
              self.$set(self.Sector.SectorCate, self.Sector.SectorCate.length, tmpObj);
            })
          }

          // Employee
          self.EmployeeOption = [];
          self.EmployeeOptionArr = [];
          _.forEach(responsesData.Employee, function (val, key) {
            if(val.EmployeeID === self.Sector.AuthorizedPerson){
              self.Sector.EmployeeName = val.EmployeeName;
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
          _.forEach(responsesData.SectorPer, function (val, key) {
            responsesData.SectorPer[key].Access = (responsesData.SectorPer[key].Access) ? true : false;
            responsesData.SectorPer[key].Edit = (responsesData.SectorPer[key].Edit) ? true : false;
            responsesData.SectorPer[key].Delete = (responsesData.SectorPer[key].Delete) ? true : false;
            responsesData.SectorPer[key].Create = (responsesData.SectorPer[key].Create) ? true : false;
          });
          self.SectorPer = responsesData.SectorPer;
          self.SectorPerEmployee = responsesData.SectorPerEmployee;

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

      if (this.reqParams.search.SectorNo !== '') {
        requestData.data.SectorNo = this.reqParams.search.SectorNo;
      }
      if (this.reqParams.search.SectorName !== '') {
        requestData.data.SectorName = this.reqParams.search.SectorName;
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
    onToggleSectorLink(toggle = true){
      let self = this;
      if(!this.SectorLink.length){
        let requestData = {
          method: 'get',
          url: '/listing/api/sector/get-sector-link/' + this.$route.params.id,
          data: {}
        }

        self.$store.commit('isLoading',false);
        ApiService.setHeader();
        ApiService.customRequest(requestData)
          .then(response =>{
            let responseData = response.data;
            self.SectorLink = responseData.data;
          })
          .catch(error=>{
            console.log(error);

          })
      }
      if(toggle){
        this.showSectorLink = !this.showSectorLink;
      }
    },
    onToggleSectorFile(toggle = true){
      let self = this;
      if(!this.SectorFile.length){
        let requestData = {
          method: 'get',
          url: 'listing/api/sector/get-sector-file/' + this.Sector.SectorID,
          data:{}
        }

        ApiService.setHeader();
        ApiService.customRequest(requestData)
          .then(response=> {
            let reponseData = response.data;
            console.log(reponseData)
            if(reponseData.status === 1){
              self.SectorFile = reponseData.data;
            }
          })
          .catch(error=>{
            console.log(error)
          })
      }
      if (toggle) {
        this.showSectorFile = !this.showSectorFile;
      }
    },
    downloadAllFile(){
      let self = this;
      let requestData = {
        url: 'listing/api/sector/download-all-file/' + this.Sector.SectorID,
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
        formData.append('SectorID', self.Sector.SectorID);
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
        axios.post('listing/api/sector/sector-upload-file/' + self.Sector.SectorID, formData, config)
          .then(function (response) {
            let responseData = response.data;
            if (responseData.status === 1) {
              currentObj.success = response.data.success;
              currentObj.filename = "";
              let dataR = response.data.data;
              self.SectorFile.push({
                LineID: dataR.LineID,
                FileUpload: file,
                SectorID: dataR.SectorID,
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
              self.showSectorFile = true;
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
    onToggleSectorVideo(toggle = true) {
      let self = this;
      if (!this.SectorVideo.length) {
        let requestData = {
          method: 'get',
          url: 'listing/api/sector/get-sector-video/' + this.Sector.SectorID,
          data: {}
        };
        // Api edit user
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.SectorVideo = responsesData.data;
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      }
      if (toggle) {
        this.showSectorVideo = !this.showSectorVideo;
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
        formData.append('SectorID', self.Sector.SectorID);
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
        axios.post('listing/api/sector/sector-upload-video/' + self.Sector.SectorID, formData, config)
          .then(function (response) {
            let responseData = response.data;
            if (responseData.status === 1) {
              currentObj.success = response.data.success;
              currentObj.videoname = "";
              let dataR = response.data.data;
              self.SectorVideo.push({
                LineID: dataR.LineID,
                VideoUpload: video,
                SectorID: dataR.SectorID,
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
              self.showSectorVideo = true;
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
    'Sector.ParentID'(){
      let self = this;
      let urlApi = '/listing/api/common/auto-child';
      let requestData = {
        method: 'post',
        url: urlApi,
        data: {
          per_page: 10,
          page: this.currentPage,
          table: 'sector',
          ParentID: this.Sector.ParentID,
        }
      }
      self.$store.commit('isLoading',true)
      ApiService.setHeader();
      ApiService.customRequest(requestData)
        .then(response=>{
          let responseData = response.data;
          if(responseData.status === 1){
            this.Sector.SectorNo = responseData.data;
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

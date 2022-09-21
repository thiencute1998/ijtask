<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Nhân viên: {{Employee.EmployeeName}}</span>
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
                  <employee-general-modal v-model="Employee" :title="'Nhân viên : ' + Employee.EmployeeName" :PositionOption="PositionOption" @changeDefaultModel="changeItemCopy"></employee-general-modal>
                  <a class="ij-a-icon" @click="showGeneralInfo = !showGeneralInfo">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showGeneralInfo"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showGeneralInfo">
              <employee-general-view v-model="Employee"></employee-general-view>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4" @click="onToggleEmployeeLink">Danh mục liên kết</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <EmployeeLinkModal @on:get-data="onToggleEmployeeLink(false)" v-model="EmployeeLink" :title="'Danh mục liên kết'" :Employee="Employee"></EmployeeLinkModal>
                  <a class="ij-a-icon" @click="onToggleEmployeeLink">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showEmployeeLink"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showEmployeeLink">
              <EmployeeLinkContent v-model="EmployeeLink"></EmployeeLinkContent>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4" @click="onToggleEmployeeFile">Tệp</label>
              <div class="col-md-20">
                <div class="float-right">
                  <span @click="downloadAllFile" style="font-size: 18px; cursor: pointer" title="Tải tất cả tệp"><i class="fa fa-download"></i></span>
                  <ijcore-upload-multiple-file v-on:changed="changeFile" :isIcon="true"></ijcore-upload-multiple-file>
                  <a @click="onToggleEmployeeFile" class="ij-a-icon">
                    <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showEmployeeFile"
                       title="Thu gọn"></i>
                    <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                       title="Mở rộng"></i>
                  </a>
                </div>

              </div>
            </div>
            <b-collapse v-model="showEmployeeFile">
              <div class="form-group row">
                <div class="col-md-24 m-0">
                  <employee-file-view v-model="EmployeeFile" :Employee="Employee"></employee-file-view>
                </div>
              </div>
            </b-collapse>
            <div class="form-group row ij-line-head">
              <label class="col-md-4" @click="onToggleEmployeeVideo">Phim</label>
              <div class="col-md-20">
                <div class="ij-icon-popup float-right">
                  <div class="float-right">
                    <IjcoreUploadMultipleVideo v-on:changed="changeVideo" :isIcon="true"></IjcoreUploadMultipleVideo>
                    <a @click="onToggleEmployeeVideo" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showEmployeeVideo"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <b-collapse v-model="showEmployeeVideo">
              <div class="form-group row">
                <div class="col-md-24 m-0">
                  <EmployeeVideoView v-model="EmployeeVideo" :Employee="Employee"></EmployeeVideoView>
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
import EmployeeGeneralView from "./partials/EmployeeGeneralView";
import EmployeeGeneralModal from "./partials/EmployeeGeneralModal";
import EmployeeLinkModal from "./partials/EmployeeLinkModal";
import EmployeeLinkContent from "./partials/EmployeeLinkContent";
import IjcoreUploadMultipleFile from "../../../components/IjcoreUploadMultipleFile";
import EmployeeFileView from "./partials/EmployeeFileView";
import IjcoreUploadMultipleVideo from "@/components/IjcoreUploadMultipleVideo";
import EmployeeVideoView from "./partials/EmployeeVideoView";

const ListRouter = 'listing-employee';
const EditRouter = 'listing-employee-edit';
const CreateRouter = 'listing-employee-create';
const ViewApi = 'listing/api/employee/view';
const ListApi = 'listing/api/employee';
const DeleteApi = 'listing/api/employee/delete';
const UpdateApi = 'listing/api/employee/update';

export default {
  name: 'listing-view-item',
  data () {
    return {
      showGeneralInfo: true,
      showEmployeeLink: false,
      showEmployeeFile: false,
      showEmployeeVideo: false,
      Employee: {
        EmployeeID: null,
        EmployeeNo: '',
        FirstName: '',
        MiddleName: '',
        LastName: '',
        EmployeeName: '',
        ShortName: '',
        DepartmentID: '',
        DepartmentNo: '',
        DepartmentName: '',
        PositionName: '',
        BirthDay: null,
        CitizenIdNo: '',
        CitizenIdDate: '',
        CitizenIdAt: '',
        OfficePhone: '',
        HandPhone: '',
        FacebookID: null,
        TwitterID: null,
        SkypeID: null,
        ZaloID: null,
        UserID: null,
        UserName: '',
        Email: '',
        CompanyID: null,
        CompanyName: '',
        Note: '',
        Inactive: false,
        AccessType: null,
        UserIDCreated: null,
        AuthorizedPerson: null,
        EmployeeNameType: 3,
        EmployeeCate: [],
        EmployeeCateList: [],
        EmployeeCateValue: [],
      },
      EmployeeLink: [],
      EmployeeFile: [],
      EmployeeVideo: [],
      EmployeeOption: [],
      CompanyOptionArr: [],
      CompanyOption: [],
      GroupOption: [],
      PositionOption: [],
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
    EmployeeGeneralView,
    EmployeeGeneralModal,
    EmployeeLinkModal,
    EmployeeLinkContent,
    IjcoreUploadMultipleFile,
    EmployeeFileView,
    IjcoreUploadMultipleVideo,
    EmployeeVideoView,
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
          self.Employee.EmployeeID = responsesData.data.data.EmployeeID;
          self.Employee.EmployeeName = responsesData.data.data.EmployeeName;
          self.Employee.EmployeeNo = responsesData.data.data.EmployeeNo;
          self.Employee.FirstName = responsesData.data.data.FirstName;
          self.Employee.MiddleName = responsesData.data.data.MiddleName;
          self.Employee.LastName = responsesData.data.data.LastName;
          self.Employee.ShortName = responsesData.data.data.ShortName;
          self.Employee.CompanyID = responsesData.data.data.CompanyID;
          self.Employee.CompanyName = responsesData.CompanyName;
          self.Employee.DepartmentID = responsesData.data.data.DepartmentID;
          self.Employee.DepartmentNo = responsesData.data.data.DepartmentNo;
          self.Employee.DepartmentName = responsesData.data.data.DepartmentName;
          self.Employee.PositionName = responsesData.data.data.PositionName;
          self.Employee.BirthDay = responsesData.data.data.BirthDay;
          self.Employee.CitizenIdNo = responsesData.data.data.CitizenIdNo;
          self.Employee.CitizenIdDate = responsesData.data.data.CitizenIdDate;
          self.Employee.CitizenIdAt = responsesData.data.data.CitizenIdAt;
          self.Employee.OfficePhone = responsesData.data.data.OfficePhone;
          self.Employee.HandPhone = responsesData.data.data.HandPhone;
          self.Employee.FacebookID = responsesData.data.data.FacebookID;
          self.Employee.TwitterID = responsesData.data.data.TwitterID;
          self.Employee.SkypeID = responsesData.data.data.SkypeID;
          self.Employee.ZaloID = responsesData.data.data.ZaloID;
          self.Employee.UserID = responsesData.data.data.UserID;
          self.Employee.Email = responsesData.data.data.Email;
          self.Employee.Note = responsesData.data.data.Note;
          self.Employee.Inactive = (responsesData.data.data.Inactive) ? true : false;
          self.Employee.AccessType = responsesData.data.data.AccessType;
          self.Employee.UserIDCreated = responsesData.data.data.UserIDCreated;
          self.Employee.AuthorizedPerson = responsesData.data.data.AuthorizedPerson;
          self.Employee.EmployeeNameType = responsesData.EmployeeNameType;
          self.Employee.EmployeeCate = [];
          self.$set(self.Employee,'EmployeeCate',[]);
          if(responsesData.data.EmployeeCate){
            _.forEach(responsesData.data.EmployeeCate, (employeeCate, key)=>{
              let tmpObj = {};
              if(employeeCate.CateID){
                let cateList = _.find(responsesData.data.EmployeeCateList, ['CateID', employeeCate.CateID]);
                if(cateList){
                  tmpObj.CateID = cateList.CateID;
                  tmpObj.CateName = cateList.CateName;
                }
              }
              if(employeeCate.CateValue){
                // let cateValue = _.find(responsesData.data.EmployeeCateValue, (cate)=> {
                //   return cate.CateID === employeeCate.CateID && cate.CateValue === employeeCate.CateValue;
                // });
                let cateValue = _.find(responsesData.data.EmployeeCateValue,{
                  CateID: employeeCate.CateID,
                  CateValue: employeeCate.CateValue
                });
                if(cateValue){
                  tmpObj.CateValue = cateValue.CateValue;
                  tmpObj.Description = cateValue.Description;
                }
              }
              // self.Employee.EmployeeCate.push(tmpObj);
              self.$set(self.Employee.EmployeeCate, self.Employee.EmployeeCate.length, tmpObj);
            })
          }

          // Company
          self.CompanyOption = [];
          self.CompanyOptionArr = [];
          _.forEach(responsesData.Company, function (val, key) {
            // if(val.EmployeeID === self.Employee.AuthorizedPerson){
            //   self.Employee.CompanyName = val.CompanyName;
            // }
            self.CompanyOptionArr[val.CompanyID] = val.CompanyName;
            let tmpObj = {};
            tmpObj.id = val.CompanyID;
            tmpObj.text = val.CompanyName;
            self.CompanyOption.push(tmpObj);
          });
          //Employee
          self.EmployeeOption = [];
          _.forEach(responsesData.Employee, function (val, key) {
            let tmpObj = {};
            tmpObj.id = val.EmployeeID;
            tmpObj.text = val.EmployeeName;
            self.EmployeeOption.push(tmpObj);
          });
          //Group
          self.GroupOption = [];
          _.forEach(responsesData.Group, function (val, key) {
            let tmpObj = {};
            tmpObj.id = val.UserGroupID;
            tmpObj.text = val.UserGroupName;
            self.GroupOption.push(tmpObj);
          });

          //Position
          self.PositionOption = [];
          if(_.isArray(responsesData.Position)){
            _.forEach(responsesData.Position, function(value,key){
              let tmpObj = {};
              tmpObj.id = value.PositionName;
              tmpObj.text = value.PositionName;
              self.PositionOption.push(tmpObj);
            })
          }

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

      if (this.reqParams.search.EmployeeNo !== '') {
        requestData.data.EmployeeNo = this.reqParams.search.EmployeeNo;
      }
      if (this.reqParams.search.EmployeeName !== '') {
        requestData.data.EmployeeName = this.reqParams.search.CompanyName;
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
            self.reqParams.idsArray.push(value.EmployeeID);
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
    onToggleEmployeeLink(toggle = true){
      let self = this;
      if(!this.EmployeeLink.length){
        let requestData = {
          method: 'get',
          url: '/listing/api/company/get-company-link/' + this.$route.params.id,
          data: {}
        }

        self.$store.commit('isLoading',false);
        ApiService.setHeader();
        ApiService.customRequest(requestData)
          .then(response =>{
            let responseData = response.data;
            self.EmployeeLink = responseData.data;
          })
          .catch(error=>{
            console.log(error);

          })
      }
      if(toggle){
        this.showEmployeeLink = !this.showEmployeeLink;
      }
    },
    onToggleEmployeeFile(toggle = true){
      let self = this;
      if(!this.EmployeeFile.length){
        let requestData = {
          method: 'get',
          url: 'listing/api/employee/get-employee-file/' + this.Employee.EmployeeID,
          data:{}
        }

        ApiService.setHeader();
        ApiService.customRequest(requestData)
          .then(response=> {
            let reponseData = response.data;
            console.log(reponseData)
            if(reponseData.status === 1){
              self.EmployeeFile = reponseData.data;
            }
          })
          .catch(error=>{
            console.log(error)
          })
      }
      if (toggle) {
        this.showEmployeeFile = !this.showEmployeeFile;
      }
    },
    downloadAllFile(){
      let self = this;
      let requestData = {
        url: 'listing/api/employee/download-all-file/' + this.Employee.EmployeeID,
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
        formData.append('EmployeeID', self.Employee.EmployeeID);
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
        axios.post('listing/api/employee/employee-upload-file/' + self.Employee.EmployeeID, formData, config)
          .then(function (response) {
            let responseData = response.data;
            if (responseData.status === 1) {
              currentObj.success = response.data.success;
              currentObj.filename = "";
              let dataR = response.data.data;
              self.EmployeeFile.push({
                LineID: dataR.LineID,
                FileUpload: file,
                EmployeeID: dataR.EmployeeID,
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
              self.showEmployeeFile = true;
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
    onToggleEmployeeVideo(toggle = true) {
      let self = this;
      if (!this.EmployeeVideo.length) {
        let requestData = {
          method: 'get',
          url: 'listing/api/employee/get-employee-video/' + this.Employee.EmployeeID,
          data: {}
        };
        // Api edit user
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.EmployeeVideo = responsesData.data;
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      }
      if (toggle) {
        this.showEmployeeVideo = !this.showEmployeeVideo;
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
        formData.append('EmployeeID', self.Employee.EmployeeID);
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
        axios.post('listing/api/employee/employee-upload-video/' + self.Employee.EmployeeID, formData, config)
          .then(function (response) {
            let responseData = response.data;
            if (responseData.status === 1) {
              currentObj.success = response.data.success;
              currentObj.videoname = "";
              let dataR = response.data.data;
              self.EmployeeVideo.push({
                LineID: dataR.LineID,
                VideoUpload: video,
                EmployeeID: dataR.EmployeeID,
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
              self.showEmployeeVideo = true;
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
  },
  // beforeDestroy(){
  //     window.removeEventListener('unload', this.onReloadPage)
  // }
}
</script>

<style lang="css"></style>

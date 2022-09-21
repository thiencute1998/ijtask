<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Tài liệu:
                <span v-for="item in ArrBreadcrumb" v-if="ArrBreadcrumb.length" style="cursor: pointer;">/<a href="#" @click="handleViewItem($event, item.DocID)">{{item.DocName}}</a></span>
                <span v-if="ArrBreadcrumb.length">/</span>{{Doc.DocName}}
                <span v-if="ArrBreadcrumbChild.length">: </span>
                <span v-for="(item, key) in ArrBreadcrumbChild" v-if="ArrBreadcrumbChild.length" style="cursor: pointer;"><span v-if="key !== 0"> & </span><a href="#" @click="handleViewItem($event, item.DocID)">{{item.DocName}}</a></span>
              </span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onCreateClicked"><i
                class="fa fa-plus"></i> Thêm
              </b-button>
<!--              <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="onEditClicked"><i-->
<!--                class="fa fa-edit"></i> Sửa-->
<!--              </b-button>-->
              <b-dropdown variant="primary" id="dropdown-actions" class="main-header-action mr-2" text="Thao tác">
                <b-dropdown-item @click="handleDeleteItem">Xóa</b-dropdown-item>
                <b-dropdown-item @click="handleAddChild">Thêm tài liệu con</b-dropdown-item>
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
                <span>{{itemNo}} - {{reqParams.idsArray.length + this.reqParams.perPage * (this.reqParams.currentPage - 1)}}</span>
                /
                <span>{{reqParams.total}}</span>
              </div>
              <b-button-group id="main-header-views" class="main-header-views">
                <b-button id="tooltip-view-prev" @click="onNavigationItem('prev')" v-if="reqParams"
                          class="main-header-view"><i class="fa fa-angle-left"></i></b-button>
                <b-button id="tooltip-view-next" @click="onNavigationItem('next')" v-if="reqParams"
                          class="main-header-view"><i class="fa fa-angle-right"></i></b-button>
                <b-button id="tooltip-view-back-to-list" @click="onBackToList" class="main-header-view"><i
                  class="fa fa-list"></i></b-button>
                <b-tooltip container="main-header-views" target="tooltip-view-back-to-list">Danh sách</b-tooltip>
                <b-tooltip container="main-header-views" target="tooltip-view-prev">Trước</b-tooltip>
                <b-tooltip container="main-header-views" target="tooltip-view-next">Sau</b-tooltip>
              </b-button-group>
              <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen="true"/>
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
            <div class="form-group row ij-line-head" id="task-detail-general-info" style="margin-top: 0px !important;">
              <label class="col-md-4 m-0">Thông tin chung</label>
              <div class="col-md-20 float-right">
                <div class="float-right">
                  <DocGeneralModal v-model="this.Doc" :title="'Tài liệu: '+Doc.DocName" :per="DocPer" :EmployeeOption="EmployeeOption"  :CompanyOption="CompanyOption" :DocPerEmployee="DocPerEmployee">
                  </DocGeneralModal>
                  <a @click="showGeneralInfo = !showGeneralInfo" class="ij-a-icon">
                    <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showGeneralInfo"
                       title="Thu gọn"></i>
                    <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showGeneralInfo"
                       title="Mở rộng"></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse class="mt-2 ij-content-view" v-model="showGeneralInfo">
              <DocGeneralView v-model="Doc" :per="DocPer" :EmployeeOption="EmployeeOption" :CompanyOption="CompanyOption" :DocPerEmployee="DocPerEmployee">
              </DocGeneralView>
            </b-collapse>

            <div v-if="DocPerEmployee.TypePer==2">
              <div class="form-group row ij-line-head">
                <label class="col-md-4 m-0">Loại tài liệu</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">

                    <DocCateModal v-model="this.DocCate" :Doc="Doc" :title="'Loại tài liệu: '+Doc.DocName" @changed="fetchData">
                    </DocCateModal>

                    <a @click="showDocCate = !showDocCate" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showDocCate"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showDocCate"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showDocCate">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <DocCateView v-model="DocCate">
                    </DocCateView>
                  </div>
                </div>
              </b-collapse>

              <div class="form-group row ij-line-head">
                <label class="col-md-4 m-0">Phân quyền</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <DocPerModal v-model="this.Doc" :title="'Phân quyền: ' + Doc.DocName" @changed="fetchData" :per="DocPer" :EmployeeOption="EmployeeOption" :CompanyOption="CompanyOption" :GroupOption="GroupOption"></DocPerModal>
                    <a @click="showDocPer = !showDocPer" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showDocPer"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showDocPer"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showDocPer">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <DocPerView v-model="Doc" :per="DocPer" :EmployeeOption="EmployeeOptionArr">
                    </DocPerView>
                  </div>
                </div>
              </b-collapse>



              <div class="form-group row ij-line-head">
                <label class="col-md-4 m-0">Danh mục liên kết</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <DocLinkModal v-model="DocLink" :title="'Danh mục liên kết: '+Doc.DocName" :Doc="Doc"></DocLinkModal>
                    <a @click="showDocLink = !showDocLink" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showDocLink"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showDocLink"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showDocLink">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <DocLinkView v-model="DocLink">
                    </DocLinkView>
                  </div>
                </div>
              </b-collapse>
              <div class="form-group row ij-line-head">
                <label class="col-md-4 m-0">Tệp</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <span @click="downloadAllFile" style="font-size: 18px; cursor: pointer" title="Tải tất cả tệp"><i class="fa fa-download"></i></span>
                    <IjcoreUploadMultipleFile v-on:changed="changeFile" :isIcon="true"></IjcoreUploadMultipleFile>
                    <a @click="showDocFile = !showDocFile" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showDocFile"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showDocFile"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showDocFile">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <DocFileView v-model="DocFile" :Doc="Doc">
                    </DocFileView>
                  </div>
                </div>
              </b-collapse>

              <div class="form-group row ij-line-head">
                <label class="col-md-4 m-0">Phim</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">

                    <IjcoreUploadMultipleVideo v-on:changed="changeVideo" :isIcon="true"></IjcoreUploadMultipleVideo>
                    <a @click="showDocVideo = !showDocVideo" class="ij-a-icon">
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showDocVideo"
                         title="Thu gọn"></i>
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showDocVideo"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showDocVideo">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <DocVideoView v-model="DocVideo" :Doc="Doc">
                    </DocVideoView>
                  </div>
                </div>
              </b-collapse>
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
  import DocGeneralView from "./partials/DocGeneralView";
  import DocGeneralModal from "./partials/DocGeneralModal";
  import moment from "moment";
  import DocPerModal from "./partials/DocPerModal";
  import DocLinkView from "./partials/DocLinkView";
  import DocLinkModal from "./partials/DocLinkModal";
  import DocFileView from "./partials/DocFileView";
  import IjcoreUploadMultipleFile from "../../../components/IjcoreUploadMultipleFile";
  import IjcoreUploadMultipleVideo from "../../../components/IjcoreUploadMultipleVideo";
  import DocVideoView from "./partials/DocVideoView";
  import DocPerView from "./partials/DocPerView";
  import DocCateView from "./partials/DocCateView";
  import DocCateModal from "./partials/DocCateModal";
  import DocChildView from "./partials/DocChildView";

  const ListRouter = 'doc-doc';
  const EditRouter = 'doc-doc-edit';
  const CreateRouter = 'doc-doc-create';
  const ViewRouter = 'doc-doc-view';
  const DetailApi = 'doc/api/doc/view';
  const ListApi = 'doc/api/doc';
  const DeleteApi = 'doc/api/doc/delete';

  export default {
    name: 'doc-view-doc',
    data() {
      return {
        showGeneralInfo: true,
        showDocLink: true,
        showDocFile: true,
        showDocVideo: true,
        showDocPer: true,
        showDocCate: true,
        showDocChild: false,
        LoadedChild: 0,
        idParams: this.$route.params.id,
        reqParams: this.$route.params.req,
        Doc:{},
        DocChild: {},
        DocPer:{},
        DocLink:{},
        DocFile:{},
        DocVideo:{},
        DocCate:{},
        DocPerEmployee: {},
        defaultModel: {},
        EmployeeOption: [],
        CompanyOption: [],
        GroupOption: [],
        EmployeeOptionArr: [],
        stage: {
          updatedData: false,
          message: (this.$route.params.message) ? this.$route.params.message : ''
        },

        ArrBreadcrumb: [],
        ArrBreadcrumbChild: []
      }

    },

    components: {
      DocChildView,
      DocCateModal,
      DocCateView,
      DocPerView,
      DocVideoView,
      IjcoreUploadMultipleVideo,
      IjcoreUploadMultipleFile,
      DocFileView, DocLinkModal, DocLinkView, DocPerModal, DocGeneralModal, DocGeneralView},
    beforeCreate() {
      if (!this.$route.params.id) {
        this.$router.push({name: ListRouter});
      }
    },
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
      itemNo() {
        let index = 0;
        index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
        return index;
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
        if (this.idParams) {
          urlApi = DetailApi + '/' + this.idParams;
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

          if(responsesData.status === 3){
            this.$router.push({name: ListRouter, params: {message: 'Bạn không có quyền truy cập!'}});
          }else{
            //Doc general
            self.ArrBreadcrumb = [];
            self.ArrBreadcrumbChild = [];
            if (responsesData.DocChild) {
              self.ArrBreadcrumbChild = responsesData.DocChild;
            }
            self.Doc = responsesData.Doc;
            if(self.Doc.Title){
              let ArrTitleDoc = self.Doc.Title.split("/")
              let ArrIDDoc = self.Doc.Path.split("-")
              // Display array values on page
              for(let i = 0; i < ArrTitleDoc.length; i++){
                if(ArrIDDoc[i]){
                  self.ArrBreadcrumb.push({
                    DocID: ArrIDDoc[i],
                    DocName: ArrTitleDoc[i]
                  })
                }
              }
            }
            self.Doc.DocDate = moment(self.Doc.DocDate).format('DD/MM/YYYY')
            self.Doc.EffectiveDate = moment(self.Doc.EffectiveDate).format('DD/MM/YYYY')
            if (self.Doc.PublicCompanyID) {
              self.Doc.PublicCompanyID = self.Doc.PublicCompanyID.split(',');
            }
            //Employee
            self.EmployeeOption = [];
            self.EmployeeOptionArr = [];
            _.forEach(responsesData.Employee, function (val, key) {
              if(val.EmployeeID == self.Doc.AuthorizedPerson){
                self.Doc.EmployeeName = val.EmployeeName;
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
              if(val.CompanyID == self.Doc.PublicCompanyID){
                self.Doc.PublicCompanyName = val.CompanyName;
              }
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
            _.forEach(responsesData.DocPer, function (val, key) {
              if(responsesData.DocPer[key].Access == 1){
                responsesData.DocPer[key].Access = true;
              }else{
                responsesData.DocPer[key].Access = false;
              }
              if(responsesData.DocPer[key].Edit == 1){
                responsesData.DocPer[key].Edit = true;
              }else{
                responsesData.DocPer[key].Edit = false;
              }
              if(responsesData.DocPer[key].Delete == 1){
                responsesData.DocPer[key].Delete = true;
              }else{
                responsesData.DocPer[key].Delete = false;
              }
              if(responsesData.DocPer[key].Create == 1){
                responsesData.DocPer[key].Create = true;
              }else{
                responsesData.DocPer[key].Create = false;
              }
            });
            self.DocPerEmployee = responsesData.DocPerEmployee;
            self.DocCate = responsesData.DocCate;
            self.DocPer = responsesData.DocPer;
            self.DocLink = responsesData.DocLink;
            self.DocFile = responsesData.DocFile;
            _.forEach(self.DocFile, function (item, key) {
              self.DocFile[key].DateModified = __.convertDateTimeToString(item.DateModified);
              self.DocFile[key].changeFile = 0;
              self.DocFile[key].changeData = 0;
            });
            self.DocVideo = responsesData.DocVideo;
            _.forEach(self.DocVideo, function (item, key) {
              self.DocVideo[key].DateModified = __.convertDateTimeToString(item.DateModified);
            });
            self.defaultModel = responsesData;
          }

          self.$store.commit('isLoading', false);
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
          formData.append('DocID', self.Doc.DocID);
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
          axios.post('doc/api/doc/doc-upload-file/' + self.Doc.DocID, formData, config)
            .then(function (response) {
              let responseData = response.data;
              if (responseData.status === 1) {
                currentObj.success = response.data.success;
                currentObj.filename = "";
                let dataR = response.data.data;
                self.DocFile.push({
                  LineID: dataR.LineID,
                  FileUpload: file,
                  DocID: dataR.DocID,
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
      changeVideo(videos) {
        let self = this;
        let dateC = __.convertDateTimeToString(new Date());
        for (var i = 0; i < videos.length; i++) {
          self.$store.commit('isLoading', true);
          var video = videos[i];
          let formData = new FormData();
          formData.append('LineID', '');
          formData.append('VideoUpload', video);
          formData.append('DocID', self.Doc.DocID);
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
          axios.post('doc/api/doc/doc-upload-video/' + self.Doc.DocID, formData, config)
            .then(function (response) {
              let responseData = response.data;
              if (responseData.status === 1) {
                currentObj.success = response.data.success;
                currentObj.videoname = "";
                let dataR = response.data.data;
                self.DocVideo.push({
                  LineID: dataR.LineID,
                  VideoUpload: video,
                  DocID: dataR.DocID,
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
      onNavigationItem(type) {
        let currentIndex = this.reqParams.idsArray.indexOf(this.idParams);
        let newIndex = (type === 'prev') ? currentIndex - 1 : currentIndex + 1;

        if (newIndex === (this.reqParams.idsArray.length) && (this.reqParams.currentPage != this.reqParams.lastPage)) {
          this.reqParams.currentPage = this.reqParams.currentPage + 1;
          this.getItemIds(type);
        } else if (newIndex < 0 && this.reqParams.currentPage > 1) {
          this.reqParams.currentPage = this.reqParams.currentPage - 1;
          this.getItemIds(type);
        } else {
          this.idParams = (this.reqParams.idsArray[newIndex]) ? this.reqParams.idsArray[newIndex] : this.idParams;
        }
      },

      getItemIds(type) {
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

        if (this.reqParams.search.docNo !== '') {
          requestData.data.VendorNo = this.reqParams.search.docNo;
        }
        if (this.reqParams.search.docName !== '') {
          requestData.data.VendorName = this.reqParams.search.docName;
        }
        if (this.reqParams.search.officePhone !== '') {
          requestData.data.OfficePhone = this.reqParams.search.officePhone;
        }
        if (this.reqParams.search.fax !== '') {
          requestData.data.Fax = this.reqParams.search.fax;
        }
        if (this.reqParams.search.email !== '') {
          requestData.data.Email = this.reqParams.search.email;
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
              self.reqParams.idsArray.push(value.VendorID);
            });

            (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });

      },

      onEditClicked() {
        this.$router.push({
          name: EditRouter,
          params: {id: this.idParams, req: this.reqParams}
        });
      },
      handleViewItem(e, DocID) {
        e.preventDefault();
        e.stopPropagation();
        this.idParams = DocID;
        this.addHashToLocation();
      },
      addHashToLocation() {
        let url = '#/doc/doc/view/' + this.idParams;
        history.pushState(
          {},
          null,
          url
        );
      },
      handleCopyItem() {
        let self = this;
        let urlApi = '';
        let requestData = {
          method: 'get',
        };
        // Api edit user
        if(this.idParams){
          urlApi = 'doc/api/doc/get-per-parent';
          let data = {
            ParentID: this.Doc.ParentID
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
            this.$router.push({name: CreateRouter, params: {itemCopy: this.Doc}});
          }else if(responsesData.status == 3){
            this.$bvToast.toast('Bạn không có quyền tạo tài liệu con của tài liệu '+self.Doc.ParentName, {
              variant: 'success',
              solid: true
            });
          }

          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      handleAddChild() {
        let self = this;
        if (self.DocPerEmployee.Create == 1) {
          let DocChild = self.Doc;
          DocChild.ParentName = self.Doc.DocName;
          DocChild.ParentID = self.Doc.DocID;
          DocChild.DocName = '';
          this.$router.push({name: CreateRouter, params: {itemCopy: DocChild}});
        }else{
          this.$bvToast.toast('Bạn không có quyền tạo tài liệu con của tài liệu '+self.Doc.DocName, {
            variant: 'success',
            solid: true
          });
        }
      },
      onCreateClicked() {
        this.$router.push({name: CreateRouter});
      },
      onBackToList(message = '') {
        if (_.isString(message)) {
          this.$router.push({name: ListRouter, params: {message: message, viewType: 'tree'}});
        } else {
          this.$router.push({name: ListRouter});
        }

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
      downloadAllFile(){
        let self = this;
        let requestData = {
          url: 'doc/api/doc/download-all-file/' + this.Doc.DocID,
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
      }
    },
    watch: {
      idParams() {
        this.fetchData();
      }
    }
  }
</script>

<style lang="css">
  .ij-icon {
    font-size: 18px;
    padding-left: 10px;
  }

  .ij-a-icon:hover {
    cursor: pointer;
  }

  .ij-content-view {
    margin-bottom: 10px;
  }

  .ij-content-view .form-group {
    margin-bottom: 3px;
  }

  .ij-line-head {
    border-bottom: 1px solid #e6e2e2;
    margin-left: 0;
    margin-right: 0;
  }

  .ij-line-head label {
    padding-left: 0 !important;
    padding-right: 0 !important;
    font-size: 16px;
  }

  .not-border th, .not-border td {
    border: none;
  }

  .float-right {
    padding-right: 0;
  }
</style>

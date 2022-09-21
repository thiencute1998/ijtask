<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span><i class="icon-eye icon mr-2"></i> Chương: {{SbiChapter.SbiChapterName}}</span>
                        </div>
                    </b-col>
                    <b-col class="col-md-6"></b-col>
                </b-row>
                <b-row>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-actions">
                          <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onCreateClicked"><i class="fa fa-plus"></i> Thêm</b-button>
<!--                          <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="onEditClicked"><i class="fa fa-edit"></i> Sửa</b-button>-->
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
                            <div class="main-header-item-counter ml-auto mr-3" v-if="reqParams && itemNo">
                                <span>{{itemNo}} - {{reqParams.total}}</span>
                            </div>
                            <b-button-group id="main-header-views" class="main-header-views">
                                <b-button id="tooltip-view-prev" @click="onNavigationItem('prev')" v-if="reqParams && itemNo" class="main-header-view"><i class="fa fa-angle-left"></i></b-button>
                                <b-button id="tooltip-view-next" @click="onNavigationItem('next')" v-if="reqParams && itemNo" class="main-header-view"><i class="fa fa-angle-right"></i></b-button>
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
                    <!-- general-->
                    <div class="form-group row ij-line-head mt-0" id="sbi-chapter-detail-general-info">
                      <label class="col-md-4 m-0" @click="showGeneralInfo = !showGeneralInfo">Thông tin chung</label>
                      <div class="col-md-20 float-right">
                        <div class="float-right ij-icon-popup">
                          <sbi-chapter-general-modal v-model="SbiChapter" :title="'Chương: ' + SbiChapter.SbiChapterName"></sbi-chapter-general-modal>
                          <a @click="showGeneralInfo = !showGeneralInfo" class="ij-a-icon">
                            <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showGeneralInfo" title="Thu gọn"></i>
                            <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showGeneralInfo" title="Mở rộng"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <b-collapse class="mt-2 ij-content-view" v-model="showGeneralInfo">
                      <sbi-chapter-general-view v-model="SbiChapter"></sbi-chapter-general-view>
                    </b-collapse>
                    <!-- general-->


                    <!-- per-->
                    <div class="form-group row ij-line-head">
                      <label class="col-md-4 m-0">Phân quyền</label>
                      <div class="col-md-20 float-right">
                        <div class="float-right">
                          <sbi-chapter-per-modal v-model="SbiChapter" :title="'Phân quyền: ' + SbiChapter.SbiChapterName" @changed="fetchData" :per="SbiChapterPer" :EmployeeOption="EmployeeOption" :CompanyOption="CompanyOption" :GroupOption="GroupOption"></sbi-chapter-per-modal>
                          <a @click="showSbiChapterPer = !showSbiChapterPer" class="ij-a-icon">
                            <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showSbiChapterPer"
                               title="Thu gọn"></i>
                            <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showSbiChapterPer"
                               title="Mở rộng"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <b-collapse class="mt-2" v-model="showSbiChapterPer">
                      <div class="form-group row">
                        <div class="col-md-24 m-0">
                          <sbi-chapter-per-view v-model="SbiChapter" :per="SbiChapterPer" :EmployeeOption="EmployeeOptionArr"></sbi-chapter-per-view>
                        </div>
                      </div>
                    </b-collapse>
                    <!-- per-->

                    <!-- sbi-chapter link-->
                    <div class="form-group row ij-line-head" id="sbi-chapter-detail-link">
                      <label class="col-md-4 m-0" @click="onToggleSbiChapterLink">Danh mục liên kết</label>
                      <div class="col-md-20 float-right">
                        <div class="float-right">
                          <sbi-chapter-link-modal @on:get-data="onToggleSbiChapterLink(false)" v-model="SbiChapterLink" :title="'Danh mục liên kết'" :SbiChapter="SbiChapter"></sbi-chapter-link-modal>
                          <a @click="onToggleSbiChapterLink" class="ij-a-icon">
                            <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="showSbiChapterLink" title="Thu gọn"></i>
                            <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="!showSbiChapterLink" title="Mở rộng"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <b-collapse class="mt-2" v-model="showSbiChapterLink">
                      <div class="form-group row">
                        <div class="col-md-24 m-0">
                          <sbi-chapter-link-content v-model="SbiChapterLink"></sbi-chapter-link-content>
                        </div>
                      </div>
                    </b-collapse>


                    <!-- file -->
                    <div class="form-group row ij-line-head">
                      <label class="col-md-4 m-0" @click="onToggleSbiChapterFile">Tệp</label>
                      <div class="col-md-20 float-right">
                        <div class="float-right">
                          <span @click="downloadAllFile" style="font-size: 18px; cursor: pointer" title="Tải tất cả tệp"><i class="fa fa-download"></i></span>
                          <ijcore-upload-multiple-file v-on:changed="changeFile" :isIcon="true"></ijcore-upload-multiple-file>
                          <a @click="onToggleSbiChapterFile" class="ij-a-icon">
                            <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showSbiChapterFile"
                               title="Thu gọn"></i>
                            <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showSbiChapterFile"
                               title="Mở rộng"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <b-collapse class="mt-2" v-model="showSbiChapterFile">
                      <div class="form-group row">
                        <div class="col-md-24 m-0">
                          <sbi-chapter-file-view v-model="SbiChapterFile" :SbiChapter="SbiChapter"></sbi-chapter-file-view>
                        </div>
                      </div>
                    </b-collapse>
                    <!-- file -->

                    <!-- video-->
                    <div class="form-group row ij-line-head">
                      <label class="col-md-4 m-0" @click="onToggleSbiChapterVideo">Phim</label>
                      <div class="col-md-20 float-right">
                        <div class="float-right">

                          <ijcore-upload-multiple-video v-on:changed="changeVideo" :isIcon="true"></ijcore-upload-multiple-video>
                          <a @click="onToggleSbiChapterVideo" class="ij-a-icon">
                            <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showSbiChapterVideo"
                               title="Thu gọn"></i>
                            <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showSbiChapterVideo"
                               title="Mở rộng"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <b-collapse class="mt-2" v-model="showSbiChapterVideo">
                      <div class="form-group row">
                        <div class="col-md-24 m-0">
                          <sbi-chapter-video-view v-model="SbiChapterVideo" :SbiChapter="SbiChapter">
                          </sbi-chapter-video-view>
                        </div>
                      </div>
                    </b-collapse>
                    <!-- video-->

                  </b-card>
                </div>
            </vue-perfect-scrollbar>
        </div>
    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import SbiChapterGeneralModal from "./partials/SbiChapterGeneralModal";
    import SbiChapterGeneralView from "./partials/SbiChapterGeneralView";
    import SbiChapterLinkContent from './partials/SbiChapterLinkContent';
    import SbiChapterLinkModal from "./partials/SbiChapterLinkModal";
    import IjcoreUploadMultipleFile from "../../../components/IjcoreUploadMultipleFile";
    import SbiChapterFileView from "./partials/SbiChapterFileView";
    import IjcoreUploadMultipleVideo from "../../../components/IjcoreUploadMultipleVideo";
    import SbiChapterVideoView from "./partials/SbiChapterVideoView";
    import SbiChapterPerView from "./partials/SbiChapterPerView";
    import SbiChapterPerModal from "./partials/SbiChapterPerModal";

    const ListRouter = 'listing-sbi-chapter';
    const EditRouter = 'listing-sbi-chapter-edit';
    const CreateRouter = 'listing-sbi-chapter-create';
    const ViewApi = 'listing/api/sbi-chapter/view';
    const ListApi = 'listing/api/sbi-chapter';
    const DeleteApi = 'listing/api/sbi-chapter/delete';

    export default {
        name: 'listing-view-sbi-chapter',
        data () {
            return {
              idParams: this.$route.params.id,
              reqParams: this.$route.params.req,
              showGeneralInfo: true,
              showSbiChapterLink: false,
              showSbiChapterFile: false,
              showSbiChapterVideo: false,
              showSbiChapterPer: true,
              showSbiChapterCate: true,
              showSbiChapterChild: false,
              SbiChapter: {},
              SbiChapterPer: {},
              SbiChapterLink: [],
              SbiChapterFile: [],
              SbiChapterVideo: [],
              SbiChapterCate: [],
              model: {},
              defaultModel: {},
              EmployeeOption: [],
              CompanyOption: [],
              GroupOption: [],
              EmployeeOptionArr: [],
              stage: {
                  updatedData: false,
                message: (this.$route.params.message) ? this.$route.params.message : ''
              }
            }

        },

        components: {
          SbiChapterLinkContent,
          SbiChapterGeneralView,
          SbiChapterGeneralModal,
          SbiChapterLinkModal,
          IjcoreUploadMultipleFile,
          SbiChapterFileView,
          IjcoreUploadMultipleVideo,
          SbiChapterVideoView,
          SbiChapterPerView,
          SbiChapterPerModal
        },
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
            itemNo(){
              let index = 0;
              if (this.reqParams.idsArray) {
                index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
              }
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
            if(this.idParams){
                urlApi = ViewApi + '/' + this.idParams + '?XDEBUG_SESSION_START=PHPSTORM';
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
                self.SbiChapter = responsesData.data.data;

                self.$set(self.SbiChapter, 'Uom', {});
                self.SbiChapter.Uom.UomID = responsesData.data.data.UomID;
                self.SbiChapter.Uom.UomName = responsesData.data.data.UomName;

                // self.Task.TaskCate = [];
                self.$set(self.SbiChapter, 'SbiChapterCate', []);
                if (responsesData.data.SbiChapterCate) {
                  _.forEach(responsesData.data.SbiChapterCate, function (fixedAssetCate, key) {
                    let tmpCate = {};
                    if (fixedAssetCate.CateID) {
                      let cateList = _.find(responsesData.data.SbiChapterCateList, ['CateID', fixedAssetCate.CateID]);
                      if (cateList) {
                        tmpCate.CateID = cateList.CateID;
                        tmpCate.CateName = cateList.CateName;
                      }
                    }

                    if (fixedAssetCate.CateValue) {
                      let cateValue = _.find(responsesData.data.SbiChapterCateValue, {
                        CateID: fixedAssetCate.CateID,
                        CateValue: fixedAssetCate.CateValue
                      });
                      if (cateValue) {
                        tmpCate.CateValue = fixedAssetCate.CateValue;
                        tmpCate.Description = cateValue.Description;
                      }
                    }
                    self.$set(self.SbiChapter.SbiChapterCate, self.SbiChapter.SbiChapterCate.length, tmpCate);
                  });
                }

                //Employee
                self.EmployeeOption = [];
                self.EmployeeOptionArr = [];
                _.forEach(responsesData.Employee, function (val, key) {
                  if(val.EmployeeID === self.SbiChapter.AuthorizedPerson){
                    self.SbiChapter.EmployeeName = val.EmployeeName;
                  }
                  self.EmployeeOptionArr[val.EmployeeID] = val.EmployeeName;
                  let tmpObj = {};
                  tmpObj.id = val.EmployeeID;
                  tmpObj.userID = val.UserID;
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
                _.forEach(responsesData.SbiChapterPer, function (val, key) {
                  responsesData.SbiChapterPer[key].Access = (responsesData.SbiChapterPer[key].Access) ? true : false;
                  responsesData.SbiChapterPer[key].Edit = (responsesData.SbiChapterPer[key].Edit) ? true : false;
                  responsesData.SbiChapterPer[key].Delete = (responsesData.SbiChapterPer[key].Delete) ? true : false;
                  responsesData.SbiChapterPer[key].Create = (responsesData.SbiChapterPer[key].Create) ? true : false;
                });
                self.SbiChapterPer = responsesData.SbiChapterPer;
                self.SbiChapterPerEmployee = responsesData.SbiChapterPerEmployee;

              }else if (responsesData.status === 3) {
                self.$router.push({name: ListRouter, params: {message: responsesData.msg}});
              }

              self.$store.commit('isLoading', false);
            }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
            });
          },
          onToggleSbiChapterLink(toggle = true) {
            let self = this;
            if (!this.SbiChapterLink.length) {
              let requestData = {
                method: 'get',
                url: 'listing/api/sbi-chapter/get-sbi-chapter-link/' + this.SbiChapter.SbiChapterID,
                data: {}
              };
              // Api edit user
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                if (responsesData.status === 1) {
                  self.SbiChapterLink = responsesData.data;
                }
                self.$store.commit('isLoading', false);
              }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
              });
            }
            if (toggle) {
              this.showSbiChapterLink = !this.showSbiChapterLink;
            }
          },
          onToggleSbiChapterFile(toggle = true) {
            let self = this;
            if (!this.SbiChapterFile.length) {
              let requestData = {
                method: 'get',
                url: 'listing/api/sbi-chapter/get-sbi-chapter-file/' + this.SbiChapter.SbiChapterID,
                data: {}
              };
              // Api edit user
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                if (responsesData.status === 1) {
                  self.SbiChapterFile = responsesData.data;
                }
                self.$store.commit('isLoading', false);
              }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
              });
            }
            if (toggle) {
              this.showSbiChapterFile = !this.showSbiChapterFile;
            }
          },
          onToggleSbiChapterVideo(toggle = true) {
            let self = this;
            if (!this.SbiChapterVideo.length) {
              let requestData = {
                method: 'get',
                url: 'listing/api/sbi-chapter/get-sbi-chapter-video/' + this.SbiChapter.SbiChapterID,
                data: {}
              };
              // Api edit user
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                if (responsesData.status === 1) {
                  self.SbiChapterVideo = responsesData.data;
                }
                self.$store.commit('isLoading', false);
              }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
              });
            }
            if (toggle) {
              this.showSbiChapterVideo = !this.showSbiChapterVideo;
            }
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

            if (this.reqParams.search.SbiChapterNo) {
                requestData.data.SbiChapterNo = this.reqParams.search.SbiChapterNo;
            }
            if (this.reqParams.search.SbiChapterName) {
                requestData.data.SbiChapterName = this.reqParams.search.SbiChapterName;
            }
            if (this.reqParams.search.officePhone) {
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
                        self.reqParams.idsArray.push(value.SbiChapterID);
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
          handleCopyItem(){
            this.$router.push({name: CreateRouter, params: {itemCopy: this.defaultModel}});
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
                              self.$bvToast.toast(responseData.msg, {
                                title: 'Thông báo',
                                variant: 'warning',
                                solid: true
                              });
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
              url: 'listing/api/sbi-chapter/download-all-file/' + this.SbiChapter.SbiChapterID,
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
              formData.append('SbiChapterID', self.SbiChapter.SbiChapterID);
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
              axios.post('listing/api/sbi-chapter/sbi-chapter-upload-file/' + self.SbiChapter.SbiChapterID + '?XDEBUG_SESSION_START=PHPSTORM', formData, config)
                .then(function (response) {
                  let responseData = response.data;
                  if (responseData.status === 1) {
                    currentObj.success = response.data.success;
                    currentObj.filename = "";
                    let dataR = response.data.data;
                    self.SbiChapterFile.push({
                      LineID: dataR.LineID,
                      FileUpload: file,
                      SbiChapterID: dataR.SbiChapterID,
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
                    self.showSbiChapterFile = true;
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
              formData.append('SbiChapterID', self.SbiChapter.SbiChapterID);
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
              axios.post('listing/api/sbi-chapter/sbi-chapter-upload-video/' + self.SbiChapter.SbiChapterID, formData, config)
                .then(function (response) {
                  let responseData = response.data;
                  if (responseData.status === 1) {
                    currentObj.success = response.data.success;
                    currentObj.videoname = "";
                    let dataR = response.data.data;
                    self.SbiChapterVideo.push({
                      LineID: dataR.LineID,
                      VideoUpload: video,
                      SbiChapterID: dataR.SbiChapterID,
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
                    self.showSbiChapterVideo = true;
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
        },
        watch: {
            idParams() {
                this.fetchData();
            }
        }
    }
</script>

<style lang="css"></style>

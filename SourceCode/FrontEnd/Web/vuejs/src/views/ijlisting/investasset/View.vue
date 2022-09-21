<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span><i class="icon-eye icon mr-2"></i> Tài sản đầu tư: {{InvestAsset.InvestAssetName}}</span>
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
                    <div class="form-group row ij-line-head mt-0" id="invest-asset-detail-general-info">
                      <label class="col-md-4 m-0" @click="showGeneralInfo = !showGeneralInfo">Thông tin chung</label>
                      <div class="col-md-20 float-right">
                        <div class="float-right ij-icon-popup">
                          <invest-asset-general-modal v-model="InvestAsset" :title="'Tài sản đầu tư: ' + InvestAsset.InvestAssetName"></invest-asset-general-modal>
                          <a @click="showGeneralInfo = !showGeneralInfo" class="ij-a-icon">
                            <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showGeneralInfo" title="Thu gọn"></i>
                            <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showGeneralInfo" title="Mở rộng"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <b-collapse class="mt-2 ij-content-view" v-model="showGeneralInfo">
                      <invest-asset-general-view v-model="InvestAsset"></invest-asset-general-view>
                    </b-collapse>
                    <!-- general-->


                    <!-- per-->
                    <div class="form-group row ij-line-head">
                      <label class="col-md-4 m-0">Phân quyền</label>
                      <div class="col-md-20 float-right">
                        <div class="float-right">
                          <invest-asset-per-modal v-model="InvestAsset" :title="'Phân quyền: ' + InvestAsset.InvestAssetName" @changed="fetchData" :per="InvestAssetPer" :EmployeeOption="EmployeeOption" :CompanyOption="CompanyOption" :GroupOption="GroupOption"></invest-asset-per-modal>
                          <a @click="showInvestAssetPer = !showInvestAssetPer" class="ij-a-icon">
                            <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showInvestAssetPer"
                               title="Thu gọn"></i>
                            <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showInvestAssetPer"
                               title="Mở rộng"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <b-collapse class="mt-2" v-model="showInvestAssetPer">
                      <div class="form-group row">
                        <div class="col-md-24 m-0">
                          <invest-asset-per-view v-model="InvestAsset" :per="InvestAssetPer" :EmployeeOption="EmployeeOptionArr"></invest-asset-per-view>
                        </div>
                      </div>
                    </b-collapse>
                    <!-- per-->

                    <!-- invest-asset link-->
                    <div class="form-group row ij-line-head" id="invest-asset-detail-link">
                      <label class="col-md-4 m-0" @click="onToggleInvestAssetLink">Danh mục liên kết</label>
                      <div class="col-md-20 float-right">
                        <div class="float-right">
                          <invest-asset-link-modal @on:get-data="onToggleInvestAssetLink(false)" v-model="InvestAssetLink" :title="'Danh mục liên kết'" :InvestAsset="InvestAsset"></invest-asset-link-modal>
                          <a @click="onToggleInvestAssetLink" class="ij-a-icon">
                            <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="showInvestAssetLink" title="Thu gọn"></i>
                            <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="!showInvestAssetLink" title="Mở rộng"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <b-collapse class="mt-2" v-model="showInvestAssetLink">
                      <div class="form-group row">
                        <div class="col-md-24 m-0">
                          <invest-asset-link-content v-model="InvestAssetLink"></invest-asset-link-content>
                        </div>
                      </div>
                    </b-collapse>


                    <!-- file -->
                    <div class="form-group row ij-line-head">
                      <label class="col-md-4 m-0" @click="onToggleInvestAssetFile">Tệp</label>
                      <div class="col-md-20 float-right">
                        <div class="float-right">
                          <span @click="downloadAllFile" style="font-size: 18px; cursor: pointer" title="Tải tất cả tệp"><i class="fa fa-download"></i></span>
                          <ijcore-upload-multiple-file v-on:changed="changeFile" :isIcon="true"></ijcore-upload-multiple-file>
                          <a @click="onToggleInvestAssetFile" class="ij-a-icon">
                            <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showInvestAssetFile"
                               title="Thu gọn"></i>
                            <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showInvestAssetFile"
                               title="Mở rộng"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <b-collapse class="mt-2" v-model="showInvestAssetFile">
                      <div class="form-group row">
                        <div class="col-md-24 m-0">
                          <invest-asset-file-view v-model="InvestAssetFile" :InvestAsset="InvestAsset"></invest-asset-file-view>
                        </div>
                      </div>
                    </b-collapse>
                    <!-- file -->

                    <!-- video-->
                    <div class="form-group row ij-line-head">
                      <label class="col-md-4 m-0" @click="onToggleInvestAssetVideo">Phim</label>
                      <div class="col-md-20 float-right">
                        <div class="float-right">

                          <ijcore-upload-multiple-video v-on:changed="changeVideo" :isIcon="true"></ijcore-upload-multiple-video>
                          <a @click="onToggleInvestAssetVideo" class="ij-a-icon">
                            <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showInvestAssetVideo"
                               title="Thu gọn"></i>
                            <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showInvestAssetVideo"
                               title="Mở rộng"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <b-collapse class="mt-2" v-model="showInvestAssetVideo">
                      <div class="form-group row">
                        <div class="col-md-24 m-0">
                          <invest-asset-video-view v-model="InvestAssetVideo" :InvestAsset="InvestAsset">
                          </invest-asset-video-view>
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
    import InvestAssetGeneralModal from "./partials/InvestAssetGeneralModal";
    import InvestAssetGeneralView from "./partials/InvestAssetGeneralView";
    import InvestAssetLinkContent from './partials/InvestAssetLinkContent';
    import InvestAssetLinkModal from "./partials/InvestAssetLinkModal";
    import IjcoreUploadMultipleFile from "../../../components/IjcoreUploadMultipleFile";
    import InvestAssetFileView from "./partials/InvestAssetFileView";
    import IjcoreUploadMultipleVideo from "../../../components/IjcoreUploadMultipleVideo";
    import InvestAssetVideoView from "./partials/InvestAssetVideoView";
    import InvestAssetPerView from "./partials/InvestAssetPerView";
    import InvestAssetPerModal from "./partials/InvestAssetPerModal";

    const ListRouter = 'listing-invest-asset';
    const EditRouter = 'listing-invest-asset-edit';
    const CreateRouter = 'listing-invest-asset-create';
    const ViewApi = 'listing/api/invest-asset/view';
    const ListApi = 'listing/api/invest-asset';
    const DeleteApi = 'listing/api/invest-asset/delete';

    export default {
        name: 'listing-view-invest-asset',
        data () {
            return {
              idParams: this.$route.params.id,
              reqParams: this.$route.params.req,
              showGeneralInfo: true,
              showInvestAssetLink: false,
              showInvestAssetFile: false,
              showInvestAssetVideo: false,
              showInvestAssetPer: true,
              showInvestAssetCate: true,
              showInvestAssetChild: false,
              InvestAsset: {},
              InvestAssetPer: {},
              InvestAssetLink: [],
              InvestAssetFile: [],
              InvestAssetVideo: [],
              InvestAssetCate: [],
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
          InvestAssetLinkContent,
          InvestAssetGeneralView,
          InvestAssetGeneralModal,
          InvestAssetLinkModal,
          IjcoreUploadMultipleFile,
          InvestAssetFileView,
          IjcoreUploadMultipleVideo,
          InvestAssetVideoView,
          InvestAssetPerView,
          InvestAssetPerModal
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
                urlApi = ViewApi + '/' + this.idParams + '';
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
                self.InvestAsset = responsesData.data.data;

                self.$set(self.InvestAsset, 'Uom', {});
                self.InvestAsset.Uom.UomID = responsesData.data.data.UomID;
                self.InvestAsset.Uom.UomName = responsesData.data.data.UomName;

                // self.Task.TaskCate = [];
                self.$set(self.InvestAsset, 'InvestAssetCate', []);
                if (responsesData.data.InvestAssetCate) {
                  _.forEach(responsesData.data.InvestAssetCate, function (investAssetCate, key) {
                    let tmpCate = {};
                    if (investAssetCate.CateID) {
                      let cateList = _.find(responsesData.data.InvestAssetCateList, ['CateID', investAssetCate.CateID]);
                      if (cateList) {
                        tmpCate.CateID = cateList.CateID;
                        tmpCate.CateName = cateList.CateName;
                      }
                    }

                    if (investAssetCate.CateValue) {
                      let cateValue = _.find(responsesData.data.InvestAssetCateValue, {
                        CateID: investAssetCate.CateID,
                        CateValue: investAssetCate.CateValue
                      });
                      if (cateValue) {
                        tmpCate.CateValue = investAssetCate.CateValue;
                        tmpCate.Description = cateValue.Description;
                      }
                    }
                    self.$set(self.InvestAsset.InvestAssetCate, self.InvestAsset.InvestAssetCate.length, tmpCate);
                  });
                }

                //Employee
                self.EmployeeOption = [];
                self.EmployeeOptionArr = [];
                _.forEach(responsesData.Employee, function (val, key) {
                  if(val.EmployeeID === self.InvestAsset.AuthorizedPerson){
                    self.InvestAsset.EmployeeName = val.EmployeeName;
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
                _.forEach(responsesData.InvestAssetPer, function (val, key) {
                  responsesData.InvestAssetPer[key].Access = (responsesData.InvestAssetPer[key].Access) ? true : false;
                  responsesData.InvestAssetPer[key].Edit = (responsesData.InvestAssetPer[key].Edit) ? true : false;
                  responsesData.InvestAssetPer[key].Delete = (responsesData.InvestAssetPer[key].Delete) ? true : false;
                  responsesData.InvestAssetPer[key].Create = (responsesData.InvestAssetPer[key].Create) ? true : false;
                });
                self.InvestAssetPer = responsesData.InvestAssetPer;
                self.InvestAssetPerEmployee = responsesData.InvestAssetPerEmployee;

              }else if (responsesData.status === 3) {
                self.$router.push({name: ListRouter, params: {message: responsesData.msg}});
              }

              self.$store.commit('isLoading', false);
            }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
            });
          },
          onToggleInvestAssetLink(toggle = true) {
            let self = this;
            if (!this.InvestAssetLink.length) {
              let requestData = {
                method: 'get',
                url: 'listing/api/invest-asset/get-invest-asset-link/' + this.InvestAsset.InvestAssetID,
                data: {}
              };
              // Api edit user
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                if (responsesData.status === 1) {
                  self.InvestAssetLink = responsesData.data;
                }
                self.$store.commit('isLoading', false);
              }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
              });
            }
            if (toggle) {
              this.showInvestAssetLink = !this.showInvestAssetLink;
            }
          },
          onToggleInvestAssetFile(toggle = true) {
            let self = this;
            if (!this.InvestAssetFile.length) {
              let requestData = {
                method: 'get',
                url: 'listing/api/invest-asset/get-invest-asset-file/' + this.InvestAsset.InvestAssetID,
                data: {}
              };
              // Api edit user
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                if (responsesData.status === 1) {
                  self.InvestAssetFile = responsesData.data;
                }
                self.$store.commit('isLoading', false);
              }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
              });
            }
            if (toggle) {
              this.showInvestAssetFile = !this.showInvestAssetFile;
            }
          },
          onToggleInvestAssetVideo(toggle = true) {
            let self = this;
            if (!this.InvestAssetVideo.length) {
              let requestData = {
                method: 'get',
                url: 'listing/api/invest-asset/get-invest-asset-video/' + this.InvestAsset.InvestAssetID,
                data: {}
              };
              // Api edit user
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                if (responsesData.status === 1) {
                  self.InvestAssetVideo = responsesData.data;
                }
                self.$store.commit('isLoading', false);
              }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
              });
            }
            if (toggle) {
              this.showInvestAssetVideo = !this.showInvestAssetVideo;
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

            if (this.reqParams.search.InvestAssetNo) {
                requestData.data.InvestAssetNo = this.reqParams.search.InvestAssetNo;
            }
            if (this.reqParams.search.InvestAssetName) {
                requestData.data.InvestAssetName = this.reqParams.search.InvestAssetName;
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
                        self.reqParams.idsArray.push(value.InvestAssetID);
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
              url: 'listing/api/invest-asset/download-all-file/' + this.InvestAsset.InvestAssetID,
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
              formData.append('InvestAssetID', self.InvestAsset.InvestAssetID);
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
              axios.post('listing/api/invest-asset/invest-asset-upload-file/' + self.InvestAsset.InvestAssetID + '', formData, config)
                .then(function (response) {
                  let responseData = response.data;
                  if (responseData.status === 1) {
                    currentObj.success = response.data.success;
                    currentObj.filename = "";
                    let dataR = response.data.data;
                    self.InvestAssetFile.push({
                      LineID: dataR.LineID,
                      FileUpload: file,
                      InvestAssetID: dataR.InvestAssetID,
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
                    self.showInvestAssetFile = true;
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
              formData.append('InvestAssetID', self.InvestAsset.InvestAssetID);
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
              axios.post('listing/api/invest-asset/invest-asset-upload-video/' + self.InvestAsset.InvestAssetID, formData, config)
                .then(function (response) {
                  let responseData = response.data;
                  if (responseData.status === 1) {
                    currentObj.success = response.data.success;
                    currentObj.videoname = "";
                    let dataR = response.data.data;
                    self.InvestAssetVideo.push({
                      LineID: dataR.LineID,
                      VideoUpload: video,
                      InvestAssetID: dataR.InvestAssetID,
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
                    self.showInvestAssetVideo = true;
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

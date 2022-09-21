<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span><i class="icon-eye icon mr-2"></i> Nhà cung cấp: {{Vendor.VendorName}}</span>
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
                    <div class="form-group row ij-line-head mt-0" id="vendor-detail-general-info">
                      <label class="col-md-4 m-0" @click="showGeneralInfo = !showGeneralInfo">Thông tin chung</label>
                      <div class="col-md-20 float-right">
                        <div class="float-right ij-icon-popup">
                          <vendor-general-modal v-model="Vendor" :title="'Nhà cung cấp: ' + Vendor.VendorName"></vendor-general-modal>
                          <a @click="showGeneralInfo = !showGeneralInfo" class="ij-a-icon">
                            <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showGeneralInfo" title="Thu gọn"></i>
                            <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showGeneralInfo" title="Mở rộng"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <b-collapse class="mt-2 ij-content-view" v-model="showGeneralInfo">
                      <vendor-general-view v-model="Vendor"></vendor-general-view>
                    </b-collapse>
                    <!-- general-->


                    <!-- per-->
                    <div class="form-group row ij-line-head">
                      <label class="col-md-4 m-0">Phân quyền</label>
                      <div class="col-md-20 float-right">
                        <div class="float-right">
                          <vendor-per-modal v-model="Vendor" :title="'Phân quyền: ' + Vendor.VendorName" @changed="fetchData" :per="VendorPer" :EmployeeOption="EmployeeOption" :VendorOption="VendorOption" :GroupOption="GroupOption"></vendor-per-modal>
                          <a @click="showVendorPer = !showVendorPer" class="ij-a-icon">
                            <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showVendorPer"
                               title="Thu gọn"></i>
                            <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showVendorPer"
                               title="Mở rộng"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <b-collapse class="mt-2" v-model="showVendorPer">
                      <div class="form-group row">
                        <div class="col-md-24 m-0">
                          <vendor-per-view v-model="Vendor" :per="VendorPer" :EmployeeOption="EmployeeOptionArr"></vendor-per-view>
                        </div>
                      </div>
                    </b-collapse>
                    <!-- per-->

                    <!-- vendor link-->
                    <div class="form-group row ij-line-head" id="vendor-detail-link">
                      <label class="col-md-4 m-0" @click="onToggleVendorLink">Danh mục liên kết</label>
                      <div class="col-md-20 float-right">
                        <div class="float-right">
                          <vendor-link-modal @on:get-data="onToggleVendorLink(false)" v-model="VendorLink" :title="'Danh mục liên kết'" :Vendor="Vendor"></vendor-link-modal>
                          <a @click="onToggleVendorLink" class="ij-a-icon">
                            <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="showVendorLink" title="Thu gọn"></i>
                            <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="!showVendorLink" title="Mở rộng"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <b-collapse class="mt-2" v-model="showVendorLink">
                      <div class="form-group row">
                        <div class="col-md-24 m-0">
                          <vendor-link-content v-model="VendorLink"></vendor-link-content>
                        </div>
                      </div>
                    </b-collapse>
                    <!-- vendor link-->

                    <!-- file -->
                    <div class="form-group row ij-line-head">
                      <label class="col-md-4 m-0" @click="onToggleVendorFile">Tệp</label>
                      <div class="col-md-20 float-right">
                        <div class="float-right">
                          <span @click="downloadAllFile" style="font-size: 18px; cursor: pointer" title="Tải tất cả tệp"><i class="fa fa-download"></i></span>
                          <ijcore-upload-multiple-file v-on:changed="changeFile" :isIcon="true"></ijcore-upload-multiple-file>
                          <a @click="onToggleVendorFile" class="ij-a-icon">
                            <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showVendorFile"
                               title="Thu gọn"></i>
                            <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showVendorFile"
                               title="Mở rộng"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <b-collapse class="mt-2" v-model="showVendorFile">
                      <div class="form-group row">
                        <div class="col-md-24 m-0">
                          <vendor-file-view v-model="VendorFile" :Vendor="Vendor"></vendor-file-view>
                        </div>
                      </div>
                    </b-collapse>
                    <!-- file -->

                    <!-- video-->
                    <div class="form-group row ij-line-head">
                      <label class="col-md-4 m-0" @click="onToggleVendorVideo">Phim</label>
                      <div class="col-md-20 float-right">
                        <div class="float-right">

                          <ijcore-upload-multiple-video v-on:changed="changeVideo" :isIcon="true"></ijcore-upload-multiple-video>
                          <a @click="onToggleVendorVideo" class="ij-a-icon">
                            <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showVendorVideo"
                               title="Thu gọn"></i>
                            <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="!showVendorVideo"
                               title="Mở rộng"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    <b-collapse class="mt-2" v-model="showVendorVideo">
                      <div class="form-group row">
                        <div class="col-md-24 m-0">
                          <vendor-video-view v-model="VendorVideo" :Vendor="Vendor">
                          </vendor-video-view>
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
    import VendorGeneralModal from "./partials/VendorGeneralModal";
    import VendorGeneralView from "./partials/VendorGeneralView";
    import VendorLinkContent from './partials/VendorLinkContent';
    import VendorLinkModal from "./partials/VendorLinkModal";
    import IjcoreUploadMultipleFile from "../../../components/IjcoreUploadMultipleFile";
    import VendorFileView from "./partials/VendorFileView";
    import IjcoreUploadMultipleVideo from "../../../components/IjcoreUploadMultipleVideo";
    import VendorVideoView from "./partials/VendorVideoView";
    import VendorPerView from "./partials/VendorPerView";
    import VendorPerModal from "./partials/VendorPerModal";
    import Swal from "sweetalert2";

    const ListRouter = 'listing-vendor';
    const EditRouter = 'listing-vendor-edit';
    const CreateRouter = 'listing-vendor-create';
    const ViewApi = 'listing/api/vendor/view';
    const ListApi = 'listing/api/vendor';
    const DeleteApi = 'listing/api/vendor/delete';

    export default {
        name: 'listing-view-vendor',
        data () {
            return {
              idParams: this.$route.params.id,
              reqParams: this.$route.params.req,
              showGeneralInfo: true,
              showVendorLink: false,
              showVendorFile: false,
              showVendorVideo: false,
              showVendorPer: true,
              showVendorCate: true,
              showVendorChild: false,
              Vendor: {},
              VendorPer: {},
              VendorLink: [],
              VendorFile: [],
              VendorVideo: [],
              VendorCate: [],
              model: {},
              defaultModel: {},
              EmployeeOption: [],
              VendorOption: [],
              GroupOption: [],
              EmployeeOptionArr: [],
              stage: {
                  updatedData: false,
                message: (this.$route.params.message) ? this.$route.params.message : ''
              }
            }

        },

        components: {
          VendorLinkContent,
          VendorGeneralView,
          VendorGeneralModal,
          VendorLinkModal,
          IjcoreUploadMultipleFile,
          VendorFileView,
          IjcoreUploadMultipleVideo,
          VendorVideoView,
          VendorPerView,
          VendorPerModal
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
                self.Vendor = responsesData.data.data;
                self.Vendor.isCustomer = (self.Vendor.isCustomer) ? true : false;
                self.$set(self.Vendor, 'Province', {});
                self.Vendor.Province.ProvinceID = responsesData.data.data.ProvinceID;
                self.Vendor.Province.ProvinceName = responsesData.data.data.ProvinceName;
                self.$set(self.Vendor, 'District', {});
                self.Vendor.District.DistrictID = responsesData.data.data.DistrictID;
                self.Vendor.District.DistrictName = responsesData.data.data.DistrictName;
                self.$set(self.Vendor, 'Commune', {});
                self.Vendor.Commune.CommuneID = responsesData.data.data.CommuneID;
                self.Vendor.Commune.CommuneName = responsesData.data.data.CommuneName;

                // self.Task.TaskCate = [];
                self.$set(self.Vendor, 'VendorCate', []);
                if (responsesData.data.VendorCate) {
                  _.forEach(responsesData.data.VendorCate, function (vendorCate, key) {
                    let tmpCate = {};
                    if (vendorCate.CateID) {
                      let cateList = _.find(responsesData.data.VendorCateList, ['CateID', vendorCate.CateID]);
                      if (cateList) {
                        tmpCate.CateID = cateList.CateID;
                        tmpCate.CateName = cateList.CateName;
                      }
                    }

                    if (vendorCate.CateValue) {
                      let cateValue = _.find(responsesData.data.VendorCateValue, {
                        CateID: vendorCate.CateID,
                        CateValue: vendorCate.CateValue
                      });
                      if (cateValue) {
                        tmpCate.CateValue = vendorCate.CateValue;
                        tmpCate.Description = cateValue.Description;
                      }
                    }
                    self.$set(self.Vendor.VendorCate, self.Vendor.VendorCate.length, tmpCate);
                  });
                }

                //Employee
                self.EmployeeOption = [];
                self.EmployeeOptionArr = [];
                _.forEach(responsesData.Employee, function (val, key) {
                  if(val.EmployeeID === self.Vendor.AuthorizedPerson){
                    self.Vendor.EmployeeName = val.EmployeeName;
                  }
                  self.EmployeeOptionArr[val.EmployeeID] = val.EmployeeName;
                  let tmpObj = {};
                  tmpObj.id = val.EmployeeID;
                  tmpObj.userID = val.UserID;
                  tmpObj.text = val.EmployeeName;
                  self.EmployeeOption.push(tmpObj);
                });
                //Vendor
                self.VendorOption = [];
                _.forEach(responsesData.Vendor, function (val, key) {
                  let tmpObj = {};
                  tmpObj.id = val.VendorID;
                  tmpObj.text = val.VendorName;
                  self.VendorOption.push(tmpObj);
                });
                //Group
                self.GroupOption = [];
                _.forEach(responsesData.Group, function (val, key) {
                  let tmpObj = {};
                  tmpObj.id = val.UserGroupID;
                  tmpObj.text = val.UserGroupName;
                  self.GroupOption.push(tmpObj);
                });
                _.forEach(responsesData.VendorPer, function (val, key) {
                  responsesData.VendorPer[key].Access = (responsesData.VendorPer[key].Access) ? true : false;
                  responsesData.VendorPer[key].Edit = (responsesData.VendorPer[key].Edit) ? true : false;
                  responsesData.VendorPer[key].Delete = (responsesData.VendorPer[key].Delete) ? true : false;
                  responsesData.VendorPer[key].Create = (responsesData.VendorPer[key].Create) ? true : false;
                });
                self.VendorPer = responsesData.VendorPer;
                self.VendorPerEmployee = responsesData.VendorPerEmployee;

              }else if (responsesData.status === 3) {
                self.$router.push({name: ListRouter, params: {message: responsesData.msg}});
              }

              self.$store.commit('isLoading', false);
            }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
            });
          },
          onToggleVendorLink(toggle = true) {
            let self = this;
            if (!this.VendorLink.length) {
              let requestData = {
                method: 'get',
                url: 'listing/api/vendor/get-vendor-link/' + this.Vendor.VendorID,
                data: {}
              };
              // Api edit user
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                if (responsesData.status === 1) {
                  self.VendorLink = responsesData.data;
                }
                self.$store.commit('isLoading', false);
              }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
              });
            }
            if (toggle) {
              this.showVendorLink = !this.showVendorLink;
            }
          },
          onToggleVendorFile(toggle = true) {
            let self = this;
            if (!this.VendorFile.length) {
              let requestData = {
                method: 'get',
                url: 'listing/api/vendor/get-vendor-file/' + this.Vendor.VendorID,
                data: {}
              };
              // Api edit user
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                if (responsesData.status === 1) {
                  self.VendorFile = responsesData.data;
                }
                self.$store.commit('isLoading', false);
              }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
              });
            }
            if (toggle) {
              this.showVendorFile = !this.showVendorFile;
            }
          },
          onToggleVendorVideo(toggle = true) {
            let self = this;
            if (!this.VendorVideo.length) {
              let requestData = {
                method: 'get',
                url: 'listing/api/vendor/get-vendor-video/' + this.Vendor.VendorID,
                data: {}
              };
              // Api edit user
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                if (responsesData.status === 1) {
                  self.VendorVideo = responsesData.data;
                }
                self.$store.commit('isLoading', false);
              }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
              });
            }
            if (toggle) {
              this.showVendorVideo = !this.showVendorVideo;
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

            if (this.reqParams.search.VendorNo) {
                requestData.data.VendorNo = this.reqParams.search.VendorNo;
            }
            if (this.reqParams.search.VendorName) {
                requestData.data.VendorName = this.reqParams.search.VendorName;
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
            let self = this;
            let params = (this.$route.params.req) ? this.$route.params.req:{};
            let query = this.$route.query;
            query.isBackToList = true;
            this.$router.push({
              name: CreateRouter,
              query: query,
              params: {id: self.idParams, req: params}
            });
          },
          onBackToList(message = '') {
            let params = (this.$route.params.req) ? this.$route.params.req:{};
            let query = this.$route.query;
            query.isBackToList = true;
            if (_.isString(message)) {
              params.message = message;
              this.$router.push({name: ListRouter, query: query, params: params, message: message});
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
                    let index = _.findIndex(self.$route.params.req.itemsArray, {'VendorID' : self.idParams});
                    self.$route.params.req.itemsArray.splice(index, 1);
                    self.onBackToList('Bản ghi đã được xóa');
                  } else  if (responseData.status === 4) {
                    Swal.fire(
                      'Lỗi',
                      'Không được xóa bản ghi Tổng hợp',
                      'error'
                    );
                  } else {
                    Swal.fire(
                      'Có lỗi',
                      '',
                      'error'
                    );

                  }
                }, (error) => {
                  console.log(error);
                  Swal.fire({
                    title: 'Thông báo',
                    text: 'Không kết nối được với máy chủ',
                    confirmButtonText: 'Đóng'
                  });

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
              url: 'listing/api/vendor/download-all-file/' + this.Vendor.VendorID,
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
              formData.append('VendorID', self.Vendor.VendorID);
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
              axios.post('listing/api/vendor/vendor-upload-file/' + self.Vendor.VendorID, formData, config)
                .then(function (response) {
                  let responseData = response.data;
                  if (responseData.status === 1) {
                    currentObj.success = response.data.success;
                    currentObj.filename = "";
                    let dataR = response.data.data;
                    self.VendorFile.push({
                      LineID: dataR.LineID,
                      FileUpload: file,
                      VendorID: dataR.VendorID,
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
                    self.showVendorFile = true;
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
              formData.append('VendorID', self.Vendor.VendorID);
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
              axios.post('listing/api/vendor/vendor-upload-video/' + self.Vendor.VendorID, formData, config)
                .then(function (response) {
                  let responseData = response.data;
                  if (responseData.status === 1) {
                    currentObj.success = response.data.success;
                    currentObj.videoname = "";
                    let dataR = response.data.data;
                    self.VendorVideo.push({
                      LineID: dataR.LineID,
                      VideoUpload: video,
                      VendorID: dataR.VendorID,
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
                    self.showVendorVideo = true;
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

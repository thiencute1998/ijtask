<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span><i class="icon-eye icon mr-2"></i> Chương trình mục tiêu: {{Program.ProgramName}}</span>
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
                            <program-general-modal v-model="Program" :title="'Chương trìnnh mục tiêu : ' + Program.ProgramName" @changeDefaultModel="changeItemCopy"></program-general-modal>
                            <a class="ij-a-icon" @click="showGeneralInfo = !showGeneralInfo">
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showGeneralInfo"></i>
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                            </a>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showGeneralInfo">
                        <program-general-view v-model="Program"></program-general-view>
                      </b-collapse>
                      <div class="form-group row ij-line-head">
                        <label class="col-md-4">Phân quyền</label>
                        <div class="col-md-20">
                          <div class="ij-icon-popup float-right">
                            <div class="float-right">
                              <program-per-modal v-model="Program" :title="'Phân quyền: ' + Program.ProgramName" @changed="fetchData" :per="ProgramPer" :EmployeeOption="EmployeeOption" :CompanyOption="CompanyOption" :GroupOption="GroupOption"></program-per-modal>
                              <a @click="showProgramPer = !showProgramPer" class="ij-a-icon">
                                <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showProgramPer"
                                   title="Thu gọn"></i>
                                <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                                   title="Mở rộng"></i>
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showProgramPer">
                        <div class="form-group row">
                          <div class="col-md-24 m-0">
                            <program-per-view v-model="Program" :per="ProgramPer" :EmployeeOption="EmployeeOptionArr"></program-per-view>
                          </div>
                        </div>
                      </b-collapse>
                      <div class="form-group row ij-line-head">
                        <label class="col-md-4" @click="onToggleProgramLink">Danh mục liên kết</label>
                        <div class="col-md-20">
                          <div class="ij-icon-popup float-right">
                            <ProgramLinkModal @on:get-data="onToggleProgramLink(false)" v-model="ProgramLink" :title="'Danh mục liên kết'" :Program="Program"></ProgramLinkModal>
                            <a class="ij-a-icon" @click="onToggleProgramLink">
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showProgramLink"></i>
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                            </a>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showProgramLink">
                        <ProgramLinkContent v-model="ProgramLink"></ProgramLinkContent>
                      </b-collapse>
                      <div class="form-group row ij-line-head">
                        <label class="col-md-4" @click="onToggleProgramFile">Tệp</label>
                        <div class="col-md-20">
                          <div class="float-right">
                            <span @click="downloadAllFile" style="font-size: 18px; cursor: pointer" title="Tải tất cả tệp"><i class="fa fa-download"></i></span>
                            <ijcore-upload-multiple-file v-on:changed="changeFile" :isIcon="true"></ijcore-upload-multiple-file>
                              <a @click="onToggleProgramFile" class="ij-a-icon">
                                <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showProgramFile"
                                   title="Thu gọn"></i>
                                <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                                   title="Mở rộng"></i>
                              </a>
                          </div>

                        </div>
                      </div>
                      <b-collapse v-model="showProgramFile">
                        <div class="form-group row">
                          <div class="col-md-24 m-0">
                            <program-file-view v-model="ProgramFile" :Program="Program"></program-file-view>
                          </div>
                        </div>
                      </b-collapse>
                      <div class="form-group row ij-line-head">
                        <label class="col-md-4" @click="onToggleProgramVideo">Phim</label>
                        <div class="col-md-20">
                          <div class="ij-icon-popup float-right">
                            <div class="float-right">
                              <IjcoreUploadMultipleVideo v-on:changed="changeVideo" :isIcon="true"></IjcoreUploadMultipleVideo>
                              <a @click="onToggleProgramVideo" class="ij-a-icon">
                                <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showProgramVideo"
                                   title="Thu gọn"></i>
                                <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                                   title="Mở rộng"></i>
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showProgramVideo">
                        <div class="form-group row">
                          <div class="col-md-24 m-0">
                            <ProgramVideoView v-model="ProgramVideo" :Program="Program"></ProgramVideoView>
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
    import ProgramGeneralView from "./partials/ProgramGeneralView";
    import ProgramGeneralModal from "./partials/ProgramGeneralModal";
    import ProgramLinkModal from "./partials/ProgramLinkModal";
    import ProgramLinkContent from "./partials/ProgramLinkContent";
    import IjcoreUploadMultipleFile from "../../../components/IjcoreUploadMultipleFile";
    import ProgramFileView from "./partials/ProgramFileView";
    import IjcoreUploadMultipleVideo from "@/components/IjcoreUploadMultipleVideo";
    import ProgramVideoView from "./partials/ProgramVideoView";
    import ProgramPerView from "./partials/ProgramPerView";
    import ProgramPerModal from "./partials/ProgramPerModal";

    const ListRouter = 'listing-program';
    const EditRouter = 'listing-program-edit';
    const CreateRouter = 'listing-program-create';
    const ViewApi = 'listing/api/program/view';
    const ListApi = 'listing/api/program';
    const DeleteApi = 'listing/api/program/delete';
    const UpdateApi = 'listing/api/program/update';

    export default {
        name: 'listing-view-item',
        data () {
            return {
                showGeneralInfo: true,
                showProgramLink: false,
                showProgramFile: false,
                showProgramVideo: false,
                showProgramPer: false,
                Program: {
                  ProgramID: null,
                  ProgramNo: '',
                  ProgramName: '',
                  ProgramType: 1,
                  ManagementLevel: 1,
                  Note: '',
                  Prefix: '',
                  Suffix: '',
                  Inactive: false,
                  AccessType: null,
                  UserIDCreated: null,
                  AuthorizedPerson: null,
                  ProgramCate: [],
                  ProgramCateList: [],
                  ProgramCateValue: [],
                },
                ProgramLink: [],
                ProgramFile: [],
                ProgramVideo: [],
                ProgramPer: {},
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
          ProgramGeneralView,
          ProgramGeneralModal,
          ProgramLinkModal,
          ProgramLinkContent,
          IjcoreUploadMultipleFile,
          ProgramFileView,
          IjcoreUploadMultipleVideo,
          ProgramVideoView,
          ProgramPerView,
          ProgramPerModal,
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
                        self.Program.ProgramID = responsesData.data.data.ProgramID;
                        self.Program.ProgramName = responsesData.data.data.ProgramName;
                        self.Program.ProgramNo = responsesData.data.data.ProgramNo;
                        self.Program.ProgramType = responsesData.data.data.ProgramType;
                        self.Program.ManagementLevel = responsesData.data.data.ManagementLevel;
                        self.Program.Note = responsesData.data.data.Note;
                        self.Program.Prefix = responsesData.data.data.Prefix;
                        self.Program.Suffix = responsesData.data.data.Suffix;
                        self.Program.Inactive = (responsesData.data.data.Inactive) ? true : false;
                        self.Program.AccessType = responsesData.data.data.AccessType;
                        self.Program.UserIDCreated = responsesData.data.data.UserIDCreated;
                        self.Program.AuthorizedPerson = responsesData.data.data.AuthorizedPerson;

                        self.Program.ProgramCate = [];
                        self.$set(self.Program,'ProgramCate',[]);
                        if(responsesData.data.ProgramCate){
                          _.forEach(responsesData.data.ProgramCate, (programCate, key)=>{
                            let tmpObj = {};
                            if(programCate.CateID){
                              let cateList = _.find(responsesData.data.ProgramCateList, ['CateID', programCate.CateID]);
                              if(cateList){
                                tmpObj.CateID = cateList.CateID;
                                tmpObj.CateName = cateList.CateName;
                              }
                            }
                            if(programCate.CateValue){
                              // let cateValue = _.find(responsesData.data.ProgramCateValue, (cate)=> {
                              //   return cate.CateID === programCate.CateID && cate.CateValue === programCate.CateValue;
                              // });
                              let cateValue = _.find(responsesData.data.ProgramCateValue,{
                                CateID: programCate.CateID,
                                CateValue: programCate.CateValue
                              });
                              if(cateValue){
                                tmpObj.CateValue = cateValue.CateValue;
                                tmpObj.Description = cateValue.Description;
                              }
                            }
                            // self.Program.ProgramCate.push(tmpObj);
                            self.$set(self.Program.ProgramCate, self.Program.ProgramCate.length, tmpObj);
                          })
                        }

                        // Employee
                        self.EmployeeOption = [];
                        self.EmployeeOptionArr = [];
                        _.forEach(responsesData.Employee, function (val, key) {
                          if(val.EmployeeID === self.Program.AuthorizedPerson){
                            self.Program.EmployeeName = val.EmployeeName;
                          }
                          self.EmployeeOptionArr[val.EmployeeID] = val.EmployeeName;
                          let tmpObj = {};
                          tmpObj.id = val.EmployeeID;
                          tmpObj.text = val.EmployeeName;
                          self.EmployeeOption.push(tmpObj);
                        });
                        //Program
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
                        _.forEach(responsesData.ProgramPer, function (val, key) {
                          responsesData.ProgramPer[key].Access = (responsesData.ProgramPer[key].Access) ? true : false;
                          responsesData.ProgramPer[key].Edit = (responsesData.ProgramPer[key].Edit) ? true : false;
                          responsesData.ProgramPer[key].Delete = (responsesData.ProgramPer[key].Delete) ? true : false;
                          responsesData.ProgramPer[key].Create = (responsesData.ProgramPer[key].Create) ? true : false;
                        });
                        self.ProgramPer = responsesData.ProgramPer;
                        self.ProgramPerEmployee = responsesData.ProgramPerEmployee;

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

                if (this.reqParams.search.ProgramNo !== '') {
                    requestData.data.ProgramNo = this.reqParams.search.ProgramNo;
                }
                if (this.reqParams.search.ProgramName !== '') {
                    requestData.data.ProgramName = this.reqParams.search.ProgramName;
                }
                if (this.reqParams.search.ProgramType !== '') {
                    requestData.data.ProgramType = this.reqParams.search.ProgramType;
                }
                if (this.reqParams.search.ManagementLevel !== '') {
                    requestData.data.ManagementLevel = this.reqParams.search.ManagementLevel;
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
                            self.reqParams.idsArray.push(value.ProgramID);
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
            onToggleProgramLink(toggle = true){
              let self = this;
              if(!this.ProgramLink.length){
                let requestData = {
                  method: 'get',
                  url: '/listing/api/program/get-program-link/' + this.$route.params.id,
                  data: {}
                }

                self.$store.commit('isLoading',false);
                ApiService.setHeader();
                ApiService.customRequest(requestData)
                .then(response =>{
                  let responseData = response.data;
                  self.ProgramLink = responseData.data;
                })
                .catch(error=>{
                  console.log(error);

                })
              }
              if(toggle){
                this.showProgramLink = !this.showProgramLink;
              }
            },
            onToggleProgramFile(toggle = true){
              let self = this;
              if(!this.ProgramFile.length){
                let requestData = {
                  method: 'get',
                  url: 'listing/api/program/get-program-file/' + this.Program.ProgramID,
                  data:{}
                }

                ApiService.setHeader();
                ApiService.customRequest(requestData)
                .then(response=> {
                  let reponseData = response.data;
                  console.log(reponseData)
                  if(reponseData.status === 1){
                    self.ProgramFile = reponseData.data;
                  }
                })
                .catch(error=>{
                  console.log(error)
                })
              }
              if (toggle) {
                this.showProgramFile = !this.showProgramFile;
              }
            },
            downloadAllFile(){
              let self = this;
              let requestData = {
                url: 'listing/api/program/download-all-file/' + this.Program.ProgramID,
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
              formData.append('ProgramID', self.Program.ProgramID);
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
              axios.post('listing/api/program/program-upload-file/' + self.Program.ProgramID, formData, config)
                .then(function (response) {
                  let responseData = response.data;
                  if (responseData.status === 1) {
                    currentObj.success = response.data.success;
                    currentObj.filename = "";
                    let dataR = response.data.data;
                    self.ProgramFile.push({
                      LineID: dataR.LineID,
                      FileUpload: file,
                      ProgramID: dataR.ProgramID,
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
                    self.showProgramFile = true;
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
            onToggleProgramVideo(toggle = true) {
            let self = this;
            if (!this.ProgramVideo.length) {
              let requestData = {
                method: 'get',
                url: 'listing/api/program/get-program-video/' + this.Program.ProgramID,
                data: {}
              };
              // Api edit user
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                if (responsesData.status === 1) {
                  self.ProgramVideo = responsesData.data;
                }
                self.$store.commit('isLoading', false);
              }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
              });
            }
            if (toggle) {
              this.showProgramVideo = !this.showProgramVideo;
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
              formData.append('ProgramID', self.Program.ProgramID);
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
              axios.post('listing/api/program/program-upload-video/' + self.Program.ProgramID, formData, config)
                .then(function (response) {
                  let responseData = response.data;
                  if (responseData.status === 1) {
                    currentObj.success = response.data.success;
                    currentObj.videoname = "";
                    let dataR = response.data.data;
                    self.ProgramVideo.push({
                      LineID: dataR.LineID,
                      VideoUpload: video,
                      ProgramID: dataR.ProgramID,
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
                    self.showProgramVideo = true;
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
            'Program.ParentID'(){
              let self = this;
              let urlApi = '/listing/api/common/auto-child';
              let requestData = {
                method: 'post',
                url: urlApi,
                data: {
                  per_page: 10,
                  page: this.currentPage,
                  table: 'program',
                  ParentID: this.Program.ParentID,
                }
              }
              self.$store.commit('isLoading',true)
              ApiService.setHeader();
              ApiService.customRequest(requestData)
                .then(response=>{
                  let responseData = response.data;
                  if(responseData.status === 1){
                    this.Program.ProgramNo = responseData.data;
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

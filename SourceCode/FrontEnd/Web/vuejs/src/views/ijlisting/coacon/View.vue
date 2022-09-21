<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span><i class="icon-eye icon mr-2"></i> Hệ thống tài khoản hợp nhất: {{CoaCon.AccountName}}</span>
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
                            <coa-con-general-modal v-model="CoaCon" :title="'Hệ thống tài khoản hợp nhất : ' + CoaCon.AccountName" ></coa-con-general-modal>
                            <a class="ij-a-icon" @click="showGeneralInfo = !showGeneralInfo">
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showGeneralInfo"></i>
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                            </a>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showGeneralInfo">
                        <coa-con-general-view v-model="CoaCon"></coa-con-general-view>
                      </b-collapse>
                      <div class="form-group row ij-line-head">
                        <label class="col-md-4">Phân quyền</label>
                        <div class="col-md-20">
                          <div class="ij-icon-popup float-right">
                            <div class="float-right">
                              <coa-con-per-modal v-model="CoaCon" :title="'Phân quyền: ' + CoaCon.AccountName" @changed="fetchData" :per="CoaConPer" :EmployeeOption="EmployeeOption" :CoaConOption="CoaConOption" :GroupOption="GroupOption"></coa-con-per-modal>
                              <a @click="showCoaConPer = !showCoaConPer" class="ij-a-icon">
                                <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="showCoaConPer"
                                   title="Thu gọn"></i>
                                <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-else
                                   title="Mở rộng"></i>
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showCoaConPer">
                        <div class="form-group row">
                          <div class="col-md-24 m-0">
                            <coa-con-per-view v-model="CoaCon" :per="CoaConPer" :EmployeeOption="EmployeeOptionArr"></coa-con-per-view>
                          </div>
                        </div>
                      </b-collapse>
                      <div class="form-group row ij-line-head">
                        <label class="col-md-4" @click="onToggleCoaConLink">Danh mục liên kết</label>
                        <div class="col-md-20">
                          <div class="ij-icon-popup float-right">
                            <CoaConLinkModal @on:get-data="onToggleCoaConLink(false)" v-model="CoaConLink" :title="'Danh mục liên kết'" :CoaCon="CoaCon"></CoaConLinkModal>
                            <a class="ij-a-icon" @click="onToggleCoaConLink">
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showCoaConLink"></i>
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                            </a>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showCoaConLink">
                        <CoaConLinkContent v-model="CoaConLink"></CoaConLinkContent>
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
    import CoaConGeneralView from "./partials/CoaConGeneralView";
    import CoaConGeneralModal from "./partials/CoaConGeneralModal";
    import CoaConLinkModal from "./partials/CoaConLinkModal";
    import CoaConLinkContent from "./partials/CoaConLinkContent";
    import CoaConPerView from "./partials/CoaConPerView";
    import CoaConPerModal from "./partials/CoaConPerModal";

    const ListRouter = 'listing-coa-con';
    const EditRouter = 'listing-coa-con-edit';
    const CreateRouter = 'listing-coa-con-create';
    const ViewApi = 'listing/api/coa-con/view';
    const ListApi = 'listing/api/coa-con';
    const DeleteApi = 'listing/api/coa-con/delete';
    const UpdateApi = 'listing/api/coa-con/update';

    export default {
        name: 'listing-view-item',
        data () {
            return {
                showGeneralInfo: true,
                showCoaConLink: false,

                showCoaConPer: false,
                CoaCon: {
                  AccountID: null,
                  AccountNo: '',
                  AccountName: '',
                  EmployeeID: null,
                  Note: '',
                  Detail: null,
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
                  CoaConCate: [],
                  CoaConCateList: [],
                  CoaConCateValue: [],
                },

                CoaConLink: [],
                CoaConPer: {},
                EmployeeOption: [],
                EmployeeOptionArr: [],
                CoaConOption: [],
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
          CoaConGeneralView,
          CoaConGeneralModal,
          CoaConLinkModal,
          CoaConLinkContent,
          CoaConPerView,
          CoaConPerModal,
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
                        self.CoaCon.AccountID = responsesData.data.data.AccountID;
                        self.CoaCon.AccountName = responsesData.data.data.AccountName;
                        self.CoaCon.AccountNo = responsesData.data.data.AccountNo;
                        self.CoaCon.Note = responsesData.data.data.Note;
                        self.CoaCon.Detail = responsesData.data.data.Detail;
                        self.CoaCon.BalanceType = responsesData.data.data.BalanceType;
                        self.CoaCon.Inactive = (responsesData.data.data.Inactive) ? true : false;
                        self.CoaCon.ParentID = responsesData.data.data.ParentID;
                        self.CoaCon.ParentNo = responsesData.data.Parent.AccountNo;
                        self.CoaCon.ParentName = responsesData.data.Parent.AccountName;
                        self.CoaCon.AccessType = responsesData.data.data.AccessType;
                        self.CoaCon.UserIDCreated = responsesData.data.data.UserIDCreated;
                        self.CoaCon.AuthorizedPerson = responsesData.data.data.AuthorizedPerson;
                        self.CoaCon.AccessTypeOptions = responsesData.data.AccessTypeOptions;
                        self.CoaCon.BalanceTypeOptions = responsesData.data.BalanceTypeOptions;

                        self.CoaCon.CoaConCate = [];
                        self.$set(self.CoaCon,'CoaConCate',[]);
                        if(responsesData.data.CoaConCate){
                          _.forEach(responsesData.data.CoaConCate, (coaConCate, key)=>{
                            let tmpObj = {};
                            if(coaConCate.CateID){
                              let cateList = _.find(responsesData.data.CoaConCateList, ['CateID', coaConCate.CateID]);
                              if(cateList){
                                tmpObj.CateID = cateList.CateID;
                                tmpObj.CateName = cateList.CateName;
                              }
                            }
                            if(coaConCate.CateValue){
                              // let cateValue = _.find(responsesData.data.CoaConCateValue, (cate)=> {
                              //   return cate.CateID === coaConCate.CateID && cate.CateValue === coaConCate.CateValue;
                              // });
                              let cateValue = _.find(responsesData.data.CoaConCateValue,{
                                CateID: coaConCate.CateID,
                                CateValue: coaConCate.CateValue
                              });
                              if(cateValue){
                                tmpObj.CateValue = cateValue.CateValue;
                                tmpObj.Description = cateValue.Description;
                              }
                            }
                            // self.CoaCon.CoaConCate.push(tmpObj);
                            self.$set(self.CoaCon.CoaConCate, self.CoaCon.CoaConCate.length, tmpObj);
                          })
                        }

                        // Employee
                        self.EmployeeOption = [];
                        self.EmployeeOptionArr = [];
                        _.forEach(responsesData.Employee, function (val, key) {
                          if(val.EmployeeID === self.CoaCon.AuthorizedPerson){
                            self.CoaCon.EmployeeName = val.EmployeeName;
                          }
                          self.EmployeeOptionArr[val.EmployeeID] = val.EmployeeName;
                          let tmpObj = {};
                          tmpObj.id = val.EmployeeID;
                          tmpObj.text = val.EmployeeName;
                          self.EmployeeOption.push(tmpObj);
                        });
                        //CoaCon
                        self.CoaConOption = [];
                        _.forEach(responsesData.CoaCon, function (val, key) {
                          let tmpObj = {};
                          tmpObj.id = val.AccountID;
                          tmpObj.text = val.AccountName;
                          self.CoaConOption.push(tmpObj);
                        });
                        //Group
                        self.GroupOption = [];
                        _.forEach(responsesData.Group, function (val, key) {
                          let tmpObj = {};
                          tmpObj.id = val.UserGroupID;
                          tmpObj.text = val.UserGroupName;
                          self.GroupOption.push(tmpObj);
                        });
                        _.forEach(responsesData.CoaConPer, function (val, key) {
                          responsesData.CoaConPer[key].Access = (responsesData.CoaConPer[key].Access) ? true : false;
                          responsesData.CoaConPer[key].Edit = (responsesData.CoaConPer[key].Edit) ? true : false;
                          responsesData.CoaConPer[key].Delete = (responsesData.CoaConPer[key].Delete) ? true : false;
                          responsesData.CoaConPer[key].Create = (responsesData.CoaConPer[key].Create) ? true : false;
                        });
                        self.CoaConPer = responsesData.CoaConPer;
                        self.CoaConPerEmployee = responsesData.CoaConPerEmployee;

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
                              let index = _.findIndex(self.$route.params.req.itemsArray, {'AccountID' : self.idParams});
                              self.$route.params.req.itemsArray.splice(index, 1);
                              let indexParent = _.findIndex(self.$route.params.req.itemsArray, {'AccountID':self.CoaCon.ParentID});
                              if(indexParent >= 0){
                                let child = _.filter(self.$route.params.req.itemsArray, {'ParentID': self.CoaCon.ParentID});
                                if(child.length == 0){
                                  self.$set(self.$route.params.req.itemsArray[indexParent], 'Class', '');
                                  self.$set(self.$route.params.req.itemsArray[indexParent], 'Detail', 1);
                                }
                              }
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
            onToggleCoaConLink(toggle = true){
              let self = this;
              if(!this.CoaConLink.length){
                let requestData = {
                  method: 'get',
                  url: '/listing/api/coa-con/get-coa-con-link/' + this.$route.params.id,
                  data: {}
                }

                self.$store.commit('isLoading',false);
                ApiService.setHeader();
                ApiService.customRequest(requestData)
                .then(response =>{
                  let responseData = response.data;
                  self.CoaConLink = responseData.data;
                })
                .catch(error=>{
                  console.log(error);

                })
              }
              if(toggle){
                this.showCoaConLink = !this.showCoaConLink;
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

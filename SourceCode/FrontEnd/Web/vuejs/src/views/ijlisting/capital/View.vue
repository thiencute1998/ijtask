<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span><i class="icon-eye icon mr-2"></i> Nguồn vốn: {{Capital.CapitalName}}</span>
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
                            <capital-general-modal v-model="Capital" :title="'Nguồn vốn : ' + Capital.CapitalName"></capital-general-modal>
                            <a class="ij-a-icon" @click="showGeneralInfo = !showGeneralInfo">
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showGeneralInfo"></i>
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                            </a>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showGeneralInfo">
                        <capital-general-view v-model="Capital"></capital-general-view>
                      </b-collapse>

                      <div class="form-group row ij-line-head">
                        <label class="col-md-3" @click="onToggleCapitalLink">Danh mục liên kết</label>
                        <div class="col-md-21">
                          <div class="ij-icon-popup float-right">
                            <CapitalLinkModal @on:get-data="onToggleCapitalLink(false)" v-model="CapitalLink" :title="'Danh mục liên kết'" :Capital="Capital"></CapitalLinkModal>
                            <a class="ij-a-icon" @click="onToggleCapitalLink">
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showCapitalLink"></i>
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                            </a>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showCapitalLink">
                        <CapitalLinkContent v-model="CapitalLink"></CapitalLinkContent>
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
    import CapitalGeneralView from "./partials/CapitalGeneralView";
    import CapitalGeneralModal from "./partials/CapitalGeneralModal";
    import CapitalLinkModal from "./partials/CapitalLinkModal";
    import CapitalLinkContent from "./partials/CapitalLinkContent";
    const ListRouter = 'listing-capital';
    const EditRouter = 'listing-capital-edit';
    const CreateRouter = 'listing-capital-create';
    const ViewApi = 'listing/api/capital/view';
    const ListApi = 'listing/api/capital';
    const DeleteApi = 'listing/api/capital/delete';
    const UpdateApi = 'listing/api/capital/update';

    export default {
        name: 'listing-view-item',
        data () {
            return {
                showGeneralInfo: true,
                showCapitalLink: false,
                Capital: {
                  CapitalID: null,
                  CapitalNo: '',
                  CapitalName: '',
                  Note: '',
                  Detail: null,
                  ParentID: null,
                  ParentNo: '',
                  ParentName: '',
                  Inactive: false,
                  CapitalCate: [],
                  CapitalCateList: [],
                  CapitalCateValue: [],
                  CapitalInOut: 1,
                  BudgetStateType: 1
                },
                CapitalLink: [],
                CapitalOption: [],
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
          CapitalGeneralView,
          CapitalGeneralModal,
          CapitalLinkModal,
          CapitalLinkContent,
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
                        self.Capital.CapitalID = responsesData.data.data.CapitalID;
                        self.Capital.CapitalName = responsesData.data.data.CapitalName;
                        self.Capital.CapitalNo = responsesData.data.data.CapitalNo;
                        self.Capital.Note = responsesData.data.data.Note;
                        self.Capital.Detail = responsesData.data.data.Detail;
                        self.Capital.Inactive = (responsesData.data.data.Inactive) ? true : false;
                        self.Capital.ParentID = responsesData.data.data.ParentID;
                        self.Capital.ParentNo = responsesData.data.Parent.ParentNo;
                        self.Capital.ParentName = responsesData.data.Parent.ParentName;
                        self.Capital.CapitalInOut = responsesData.data.data.CapitalInOut;
                        self.Capital.BudgetStateType = responsesData.data.data.BudgetStateType;

                        self.Capital.CapitalCate = [];
                        self.$set(self.Capital,'CapitalCate',[]);
                        if(responsesData.data.CapitalCate){
                          _.forEach(responsesData.data.CapitalCate, (capitalCate, key)=>{
                            let tmpObj = {};
                            if(capitalCate.CateID){
                              let cateList = _.find(responsesData.data.CapitalCateList, ['CateID', capitalCate.CateID]);
                              if(cateList){
                                tmpObj.CateID = cateList.CateID;
                                tmpObj.CateNo = cateList.CateNo;
                                tmpObj.CateName = cateList.CateName;
                              }
                            }
                            if(capitalCate.CateValue){
                              // let cateValue = _.find(responsesData.data.CapitalCateValue, (cate)=> {
                              //   return cate.CateID === capitalCate.CateID && cate.CateValue === capitalCate.CateValue;
                              // });
                              let cateValue = _.find(responsesData.data.CapitalCateValue,{
                                CateID: capitalCate.CateID,
                                CateValue: capitalCate.CateValue
                              });
                              if(cateValue){
                                tmpObj.CateValue = cateValue.CateValue;
                                tmpObj.Description = cateValue.Description;
                              } else {
                                tmpObj.CateValue = null;
                                tmpObj.Description = '';
                              }
                            }

                            self.Capital.CapitalCate.push(tmpObj);

                          })
                        }

                        //Capital
                        self.CapitalOption = [];
                        _.forEach(responsesData.Capital, function (val, key) {
                          let tmpObj = {};
                          tmpObj.id = val.CapitalID;
                          tmpObj.text = val.CapitalName;
                          self.CapitalOption.push(tmpObj);
                        });

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

                if (this.reqParams.search.CapitalNo !== '') {
                    requestData.data.CapitalNo = this.reqParams.search.CapitalNo;
                }
                if (this.reqParams.search.CapitalName !== '') {
                    requestData.data.CapitalName = this.reqParams.search.CapitalName;
                }
                if (this.reqParams.search.Tel !== '') {
                    requestData.data.Tel = this.reqParams.search.Tel;
                }
                if (this.reqParams.search.Fax !== '') {
                    requestData.data.Fax = this.reqParams.search.Fax;
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
                            self.reqParams.idsArray.push(value.CapitalID);
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
                  url: DeleteApi + '/' + self.idParams+ '?XDEBUG_SESSION_START=PHPSTORM',
                  data: {
                    array_id: [self.idParams],
                  },
                };
                ApiService.setHeader();
                ApiService.customRequest(requestData).then((response) => {
                  let responseData = response.data;
                  if (responseData.status === 1) {
                    let index = _.findIndex(self.$route.params.req.itemsArray, {'CapitalID' : self.idParams});
                    self.$route.params.req.itemsArray.splice(index, 1);
                    let indexParent = _.findIndex(self.$route.params.req.itemsArray, {'CapitalID':self.Capital.ParentID});
                    if(indexParent >= 0){
                      let child = _.filter(self.$route.params.req.itemsArray, {'ParentID': self.Capital.ParentID});
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
            onToggleCapitalLink(toggle = true){
              let self = this;
              if(!this.CapitalLink.length){
                let requestData = {
                  method: 'get',
                  url: '/listing/api/capital/get-capital-link/' + this.$route.params.id,
                  data: {}
                }

                self.$store.commit('isLoading',false);
                ApiService.setHeader();
                ApiService.customRequest(requestData)
                .then(response =>{
                  let responseData = response.data;
                  self.CapitalLink = responseData.data;
                })
                .catch(error=>{
                  console.log(error);

                })
              }
              if(toggle){
                this.showCapitalLink = !this.showCapitalLink;
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

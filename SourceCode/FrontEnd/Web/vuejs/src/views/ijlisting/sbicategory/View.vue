<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span><i class="icon-eye icon mr-2"></i> Loại khoản: {{SbiCategory.SbiCategoryName}}</span>
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
                            <sbi-category-general-modal v-model="SbiCategory" :title="'Loại khoản : ' + SbiCategory.SbiCategoryName"></sbi-category-general-modal>
                            <a class="ij-a-icon" @click="showGeneralInfo = !showGeneralInfo">
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showGeneralInfo"></i>
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                            </a>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showGeneralInfo">
                        <sbi-category-general-view v-model="SbiCategory"></sbi-category-general-view>
                      </b-collapse>

                      <div class="form-group row ij-line-head">
                        <label class="col-md-4" @click="onToggleSbiCategoryLink">Danh mục liên kết</label>
                        <div class="col-md-20">
                          <div class="ij-icon-popup float-right">
                            <SbiCategoryLinkModal @on:get-data="onToggleSbiCategoryLink(false)" v-model="SbiCategoryLink" :title="'Danh mục liên kết'" :SbiCategory="SbiCategory"></SbiCategoryLinkModal>
                            <a class="ij-a-icon" @click="onToggleSbiCategoryLink">
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showSbiCategoryLink"></i>
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                            </a>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showSbiCategoryLink">
                        <SbiCategoryLinkContent v-model="SbiCategoryLink"></SbiCategoryLinkContent>
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
    import SbiCategoryGeneralView from "./partials/SbiCategoryGeneralView";
    import SbiCategoryGeneralModal from "./partials/SbiCategoryGeneralModal";
    import SbiCategoryLinkModal from "./partials/SbiCategoryLinkModal";
    import SbiCategoryLinkContent from "./partials/SbiCategoryLinkContent";
    const ListRouter = 'listing-sbi-category';
    const EditRouter = 'listing-sbi-category-edit';
    const CreateRouter = 'listing-sbi-category-create';
    const ViewApi = 'listing/api/sbi-category/view';
    const ListApi = 'listing/api/sbi-category';
    const DeleteApi = 'listing/api/sbi-category/delete';
    const UpdateApi = 'listing/api/sbi-category/update';

    export default {
        name: 'listing-view-item',
        data () {
            return {
                showGeneralInfo: true,
                showSbiCategoryLink: false,
                SbiCategory: {
                  SbiCategoryID: null,
                  SbiCategoryNo: '',
                  SbiCategoryName: '',
                  Note: '',
                  ParentID: null,
                  ParentNo: '',
                  ParentName: '',
                  UomID: null,
                  UomName: '',
                  Inactive: false,
                  SbiCategoryCate: [],
                  SbiCategoryCateList: [],
                  SbiCategoryCateValue: [],
                },
                SbiCategoryLink: [],
                SbiCategoryOption: [],
                UomOption: [],
                UomOptionArrL: [],
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
          SbiCategoryGeneralView,
          SbiCategoryGeneralModal,
          SbiCategoryLinkModal,
          SbiCategoryLinkContent,
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
                        self.SbiCategory.SbiCategoryID = responsesData.data.data.SbiCategoryID;
                        self.SbiCategory.SbiCategoryName = responsesData.data.data.SbiCategoryName;
                        self.SbiCategory.SbiCategoryNo = responsesData.data.data.SbiCategoryNo;
                        self.SbiCategory.Note = responsesData.data.data.Note;
                        self.SbiCategory.Inactive = (responsesData.data.data.Inactive) ? true : false;
                        self.SbiCategory.UomID = responsesData.data.data.UomID;
                        self.SbiCategory.UomName = responsesData.data.data.UomName;
                        self.SbiCategory.ParentID = responsesData.data.data.ParentID;
                        self.SbiCategory.ParentNo = responsesData.data.Parent.ParentNo;
                        self.SbiCategory.ParentName = responsesData.data.Parent.ParentName;

                        self.SbiCategory.SbiCategoryCate = [];
                        self.$set(self.SbiCategory,'SbiCategoryCate',[]);
                        if(responsesData.data.SbiCategoryCate){
                          _.forEach(responsesData.data.SbiCategoryCate, (sbiCategoryCate, key)=>{
                            let tmpObj = {};
                            if(sbiCategoryCate.CateID){
                              let cateList = _.find(responsesData.data.SbiCategoryCateList, ['CateID', sbiCategoryCate.CateID]);
                              if(cateList){
                                tmpObj.CateID = cateList.CateID;
                                tmpObj.CateName = cateList.CateName;
                              }
                            }
                            if(sbiCategoryCate.CateValue){
                              // let cateValue = _.find(responsesData.data.SbiCategoryCateValue, (cate)=> {
                              //   return cate.CateID === sbiCategoryCate.CateID && cate.CateValue === sbiCategoryCate.CateValue;
                              // });
                              let cateValue = _.find(responsesData.data.SbiCategoryCateValue,{
                                CateID: sbiCategoryCate.CateID,
                                CateValue: sbiCategoryCate.CateValue
                              });
                              if(cateValue){
                                tmpObj.CateValue = cateValue.CateValue;
                                tmpObj.Description = cateValue.Description;
                              }
                            }

                            self.UomOption = [];
                            self.UomOptionArr = [];
                            _.forEach(responsesData.Uom, function (val, key) {
                              if(val.UomID === self.SbiCategory.AuthorizedPerson){
                                self.SbiCategory.UomName = val.UomName;
                              }
                              self.UomOptionArr[val.UomID] = val.UomName;
                              let tmpObj = {};
                              tmpObj.id = val.UomID;
                              tmpObj.text = val.UomName;
                              self.UomOption.push(tmpObj);
                            });
                            // self.SbiCategory.SbiCategoryCate.push(tmpObj);
                            self.$set(self.SbiCategory.SbiCategoryCate, self.SbiCategory.SbiCategoryCate.length, tmpObj);
                          })
                        }

                        //SbiCategory
                        self.SbiCategoryOption = [];
                        _.forEach(responsesData.SbiCategory, function (val, key) {
                          let tmpObj = {};
                          tmpObj.id = val.SbiCategoryID;
                          tmpObj.text = val.SbiCategoryName;
                          self.SbiCategoryOption.push(tmpObj);
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

                if (this.reqParams.search.SbiCategoryNo !== '') {
                    requestData.data.SbiCategoryNo = this.reqParams.search.SbiCategoryNo;
                }
                if (this.reqParams.search.SbiCategoryName !== '') {
                    requestData.data.SbiCategoryName = this.reqParams.search.SbiCategoryName;
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
                            self.reqParams.idsArray.push(value.SbiCategoryID);
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
            onToggleSbiCategoryLink(toggle = true){
              let self = this;
              if(!this.SbiCategoryLink.length){
                let requestData = {
                  method: 'get',
                  url: '/listing/api/sbi-category/get-sbi-category-link/' + this.$route.params.id,
                  data: {}
                }

                self.$store.commit('isLoading',false);
                ApiService.setHeader();
                ApiService.customRequest(requestData)
                .then(response =>{
                  let responseData = response.data;
                  self.SbiCategoryLink = responseData.data;
                })
                .catch(error=>{
                  console.log(error);

                })
              }
              if(toggle){
                this.showSbiCategoryLink = !this.showSbiCategoryLink;
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

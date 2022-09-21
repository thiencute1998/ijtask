<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span><i class="icon-eye icon mr-2"></i> Chỉ tiêu dự toán: {{Norm.NormName}}</span>
                        </div>
                    </b-col>
                    <b-col class="col-md-6"></b-col>
                </b-row>
                <b-row>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-actions">
                            <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onCreateClicked"><i class="fa fa-plus"></i> Thêm</b-button>
                          <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="onEditClicked"><i class="fa fa-edit"></i> Sửa</b-button>
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
<!--                            <norm-general-modal v-model="Norm" :title="'Chỉ tiêu định mức dự toán : ' + Norm.NormName" @changeDefaultModel="changeItemCopy"></norm-general-modal>-->
                            <a class="ij-a-icon" @click="showGeneralInfo = !showGeneralInfo">
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showGeneralInfo"></i>
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                            </a>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showGeneralInfo">
                        <norm-general-view v-model="Norm"></norm-general-view>
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
    import NormGeneralView from "./partials/NormGeneralView";
    import NormGeneralModal from "./partials/NormGeneralModal";
    const ListRouter = 'listing-norm';
    const EditRouter = 'listing-norm-edit';
    const CreateRouter = 'listing-norm-create';
    const ViewApi = 'listing/api/norm/view';
    const ListApi = 'listing/api/norm';
    const DeleteApi = 'listing/api/norm/delete';
    const UpdateApi = 'listing/api/norm/update';

    export default {
        name: 'listing-view-item',
        data () {
            return {
                showGeneralInfo: true,
                Norm: {
                  NormID: null,
                  NormNo: '',
                  ParentNo: '',
                  ParentID:null,
                  ParentName: '',
                  NormName: '',
                  NormType: 1,
                  Comment: '',
                  UomID: null,
                  UomNo: '',
                  UomName: '',
                  RevenueID: null,
                  RevenueNo: '',
                  RevenueName: '',
                  ExpenseID: null,
                  ExpenseNo: '',
                  ExpenseName: '',
                  Inactive: false,
                  NormCate: [],
                  NormCateList: [],
                  NormCateValue: [],
                  NormMap: [],
                  NormTableOption: [],
                },
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
          NormGeneralView,
          NormGeneralModal,
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
                        self.Norm.NormID = responsesData.data.data.NormID;
                        self.Norm.NormNo = responsesData.data.data.NormNo;
                        self.Norm.NormName = responsesData.data.data.NormName;
                        self.Norm.NormType = responsesData.data.data.NormType;
                        self.Norm.Comment = responsesData.data.data.Comment;
                        self.Norm.ParentID = responsesData.data.data.ParentID;
                        self.Norm.ParentNo = responsesData.data.Parent.ParentNo;
                        self.Norm.ParentName = responsesData.data.Parent.ParentName;
                        self.Norm.Inactive = (responsesData.data.data.Inactive) ? true : false;
                        self.Norm.UomID = responsesData.data.data.UomID;
                        self.Norm.UomNo = responsesData.data.data.UomNo;
                        self.Norm.UomName = responsesData.data.data.UomName;
                        self.Norm.RevenueID = responsesData.data.data.RevenueID;
                        self.Norm.RevenueNo = responsesData.data.data.RevenueNo;
                        self.Norm.RevenueName = responsesData.data.data.RevenueName;
                        self.Norm.ExpenseID = responsesData.data.data.ExpenseID;
                        self.Norm.ExpenseNo = responsesData.data.data.ExpenseNo;
                        self.Norm.ExpenseName = responsesData.data.data.ExpenseName;

                        self.Norm.NormCate = [];
                        self.$set(self.Norm,'NormCate',[]);
                        if(responsesData.data.NormCate){
                          _.forEach(responsesData.data.NormCate, (normCate, key)=>{
                            let tmpObj = {};
                            if(normCate.CateID){
                              let cateList = _.find(responsesData.data.NormCateList, ['CateID', normCate.CateID]);
                              if(cateList){
                                tmpObj.CateID = cateList.CateID;
                                tmpObj.CateName = cateList.CateName;
                              }
                            }
                            if(normCate.CateValue){
                              // let cateValue = _.find(responsesData.data.NormCateValue, (cate)=> {
                              //   return cate.CateID === normCate.CateID && cate.CateValue === normCate.CateValue;
                              // });
                              let cateValue = _.find(responsesData.data.NormCateValue,{
                                CateID: normCate.CateID,
                                CateValue: normCate.CateValue
                              });
                              if(cateValue){
                                tmpObj.CateValue = cateValue.CateValue;
                                tmpObj.Description = cateValue.Description;
                              }
                            }

                            // self.Norm.NormCate.push(tmpObj);
                            self.$set(self.Norm.NormCate, self.Norm.NormCate.length, tmpObj);
                          })
                        }

                        if (_.isArray(responsesData.data.NormTable)) {
                          self.Norm.NormTableOption = [{value: null, text: '--Chọn bảng chỉ tiêu--'}];
                          _.forEach(responsesData.data.NormTable, function (value, key) {
                            let tmpObj = {};
                            tmpObj.value = value.NormTableID;
                            tmpObj.text = value.NormTableName;
                            self.Norm.NormTableOption.push(tmpObj);
                          });
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

                if (this.reqParams.search.NormNo !== '') {
                    requestData.data.NormNo = this.reqParams.search.NormNo;
                }
                if (this.reqParams.search.NormName !== '') {
                    requestData.data.NormName = this.reqParams.search.NormName;
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
                            self.reqParams.idsArray.push(value.NormID);
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
                    let index = _.findIndex(self.$route.params.req.itemsArray, {'NormID' : self.idParams});
                    self.$route.params.req.itemsArray.splice(index, 1);
                    let indexParent = _.findIndex(self.$route.params.req.itemsArray, {'NormID':self.Norm.ParentID});
                    if(indexParent >= 0){
                      let child = _.filter(self.$route.params.req.itemsArray, {'ParentID': self.Norm.ParentID});
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

<style lang="css">
</style>

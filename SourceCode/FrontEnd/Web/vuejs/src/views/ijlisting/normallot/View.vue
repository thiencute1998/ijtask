<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span><i class="icon-eye icon mr-2"></i> TCPBDT: {{NormAllot.NormAllotName}}</span>
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
                            <norm-allot-general-modal v-model="NormAllot" :title="'Tiêu chí phân bổ dự toán: ' + NormAllot.NormAllotName"></norm-allot-general-modal>
                            <a class="ij-a-icon" @click="showGeneralInfo = !showGeneralInfo">
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showGeneralInfo"></i>
                              <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                            </a>
                          </div>
                        </div>
                      </div>
                      <b-collapse v-model="showGeneralInfo">
                        <norm-allot-general-view v-model="NormAllot"></norm-allot-general-view>
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
    import NormAllotGeneralView from "./partials/NormAllotGeneralView";
    import NormAllotGeneralModal from "./partials/NormAllotGeneralModal";
    const ListRouter = 'listing-normallot';
    const EditRouter = 'listing-normallot-edit';
    const CreateRouter = 'listing-normallot-create';
    const ViewApi = 'listing/api/norm-allot/view';
    const ListApi = 'listing/api/norm-allot';
    const DeleteApi = 'listing/api/norm-allot/delete';
    const UpdateApi = 'listing/api/norm-allot/update';

    export default {
        name: 'listing-view-item',
        data () {
            return {
                showGeneralInfo: true,
                NormAllot: {
                  NormAllotID: null,
                  NormAllotNo: '',
                  NormAllotName: '',
                  Note: '',
                  ParentID: null,
                  ParentNo: '',
                  ParentName: '',
                  Inactive: false,
                  NormAllotCate: [],
                  NormAllotCateList: [],
                  NormAllotCateValue: [],
                  UomID: null,
                  UomNo : '',
                  UomName : '',
                  EffectiveDate : null,
                  ExpirationDate : null,
                },
                NormAllotOption: [],
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
          NormAllotGeneralView,
          NormAllotGeneralModal,

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
                        self.NormAllot.NormAllotID = responsesData.data.data.NormAllotID;
                        self.NormAllot.NormAllotName = responsesData.data.data.NormAllotName;
                        self.NormAllot.NormAllotNo = responsesData.data.data.NormAllotNo;
                        self.NormAllot.Note = responsesData.data.data.Note;
                        self.NormAllot.Inactive = (responsesData.data.data.Inactive) ? true : false;
                        self.NormAllot.ParentID = responsesData.data.data.ParentID;
                        self.NormAllot.ParentNo = responsesData.data.Parent.ParentNo;
                        self.NormAllot.ParentName = responsesData.data.Parent.ParentName;
                        self.NormAllot.UomID = responsesData.data.data.UomID;
                        self.NormAllot.UomNo = responsesData.data.data.UomNo;
                        self.NormAllot.UomName = responsesData.data.data.UomName;
                        self.NormAllot.EffectiveDate = responsesData.data.data.EffectiveDate;
                        self.NormAllot.ExpirationDate = responsesData.data.data.ExpirationDate;
                        self.NormAllot.NormAllotCate = [];
                        self.$set(self.NormAllot,'NormAllotCate',[]);
                        if(responsesData.data.NormAllotCate){
                          _.forEach(responsesData.data.NormAllotCate, (normAllotCate, key)=>{
                            let tmpObj = {};
                            if(normAllotCate.CateID){
                              let cateList = _.find(responsesData.data.NormAllotCateList, ['CateID', normAllotCate.CateID]);
                              if(cateList){
                                tmpObj.CateID = cateList.CateID;
                                tmpObj.CateNo = cateList.CateNo;
                                tmpObj.CateName = cateList.CateName;
                              }
                            }
                            if(normAllotCate.CateValue){
                              // let cateValue = _.find(responsesData.data.NormAllotCateValue, (cate)=> {
                              //   return cate.CateID === normAllotCate.CateID && cate.CateValue === normAllotCate.CateValue;
                              // });
                              let cateValue = _.find(responsesData.data.NormAllotCateValue,{
                                CateID: normAllotCate.CateID,
                                CateValue: normAllotCate.CateValue
                              });
                              if(cateValue){
                                tmpObj.CateValue = cateValue.CateValue;
                                tmpObj.Description = cateValue.Description;
                              } else {
                                tmpObj.CateValue = null;
                                tmpObj.Description = '';
                              }
                            }

                            self.NormAllot.NormAllotCate.push(tmpObj);

                          })
                        }

                        //NormAllot
                        self.NormAllotOption = [];
                        _.forEach(responsesData.NormAllot, function (val, key) {
                          let tmpObj = {};
                          tmpObj.id = val.NormAllotID;
                          tmpObj.text = val.NormAllotName;
                          self.NormAllotOption.push(tmpObj);
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

                if (this.reqParams.search.NormAllotNo !== '') {
                    requestData.data.NormAllotNo = this.reqParams.search.NormAllotNo;
                }
                if (this.reqParams.search.NormAllotName !== '') {
                    requestData.data.NormAllotName = this.reqParams.search.NormAllotName;
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
                            self.reqParams.idsArray.push(value.NormAllotID);
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
                    let index = _.findIndex(self.$route.params.req.itemsArray, {'NormAllotID' : self.idParams});
                    self.$route.params.req.itemsArray.splice(index, 1);
                    let indexParent = _.findIndex(self.$route.params.req.itemsArray, {'NormAllotID':self.NormAllot.ParentID});
                    if(indexParent >= 0){
                      let child = _.filter(self.$route.params.req.itemsArray, {'ParentID': self.NormAllot.ParentID});
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
    }
</script>

<style lang="css"></style>

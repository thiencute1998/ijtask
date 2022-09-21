<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Tài khoản đồng thời</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onCreateClicked"><i class="fa fa-plus"></i> Thêm</b-button>
              <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="onEditClicked"><i class="fa fa-edit"></i> Sửa</b-button>
              <b-dropdown variant="primary" id="dropdown-actions" disabled class="main-header-action mr-2" text="Thao tác">
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
          <div class="card">
            <div class="form-group row" style="margin-top: 10px; margin-bottom: 0px; margin-right: 10px;">
              <div class="col-md-8">
                <div class="form-group row align-items-center ml-2">
                  <label class="col-md-6">HTTK</label>
                  <div class="col-md-18">
                    {{AccountingCCAccount.CoaTypeName}}
                  </div>
                </div>
              </div>
              <div class="col-md-16">
                <b-tabs card>
                  <b-tab title="Bút toán hạch toán" active>
                    <b-card-text>
                      <div class="form-group row align-items-center ">
                        <label class="col-md-3">TKHT Nợ</label>
                        <div class="col-md-9">
                          {{AccountingCCAccount.EntryDebitAccountNo}}
                        </div>
                        <label class="col-md-3">TKHT Có</label>
                        <div class="col-md-9">
                          {{AccountingCCAccount.EntryCreditAccountNo}}
                        </div>
                      </div>
                      <div class="form-group row align-items-center ">
                        <label class="col-md-3">Nghiệp vụ</label>
                        <div class="col-md-9">
                          {{AccountingCCAccount.EntryInTransTypeName}}
                        </div>
                        <label class="col-md-3">Điều kiện</label>
                        <div class="col-md-9">
                          {{AccountingCCAccount.ConditionalExpression}}
                        </div>
                      </div>
                    </b-card-text>
                  </b-tab>
                  <b-tab title="Bút toán đồng thời 1">
                    <b-card-text>
                      <div class="form-group row align-items-center ">
                        <label class="col-md-3">TKĐT Nợ</label>
                        <div class="col-md-9">
                          {{AccountingCCAccount.CCDebitAccountNo}}
                        </div>
                        <label class="col-md-3">TKĐT Nợ</label>
                        <div class="col-md-9">
                          {{AccountingCCAccount.CCCreditAccountNo}}
                        </div>
                      </div>
                      <div class="form-group row align-items-center ">
                        <label class="col-md-3">Nghiệp vụ</label>
                        <div class="col-md-9">
                          {{AccountingCCAccount.CCInTransTypeName}}
                        </div>
                        <label class="col-md-3">Giá tri âm</label>
                        <div class="col-md-9">
                          {{AccountingCCAccount.EnterNegativeValue}}
                        </div>
                      </div>
                      <div class="form-group row align-items-center">
                        <label class="col-md-3">Diễn giải</label>
                        <div class="col-md-21">
                          {{AccountingCCAccount.Description}}
                        </div>
                      </div>
                    </b-card-text>
                  </b-tab>
                  <b-tab title="Bút toán đồng thời 2">
                    <b-card-text>
                      <div class="form-group row align-items-center ">
                        <label class="col-md-3">TKĐT Nợ</label>
                        <div class="col-md-9">
                          {{AccountingCCAccount.CCDebitAccountNo2}}
                        </div>
                        <label class="col-md-3">TKĐT Nợ</label>
                        <div class="col-md-9">
                          {{AccountingCCAccount.CCCreditAccountNo2}}
                        </div>
                      </div>
                      <div class="form-group row align-items-center ">
                        <label class="col-md-3">Nghiệp vụ</label>
                        <div class="col-md-9">
                          {{AccountingCCAccount.CCInTransTypeName2}}
                        </div>
                        <label class="col-md-3">Giá tri âm</label>
                        <div class="col-md-9">
                          {{AccountingCCAccount.EnterNegativeValue2}}
                        </div>
                      </div>
                      <div class="form-group row align-items-center ">
                        <label class="col-md-3">Diễn giải</label>
                        <div class="col-md-21">
                          {{AccountingCCAccount.Description2}}
                        </div>
                      </div>
                    </b-card-text>
                  </b-tab>
                  <b-tab title="Bút toán đồng thời 3">
                    <b-card-text>
                      <div class="form-group row align-items-center ">
                        <label class="col-md-3">TKĐT Nợ</label>
                        <div class="col-md-9">
                          {{AccountingCCAccount.CCDebitAccountNo3}}
                        </div>
                        <label class="col-md-3">TKĐT Nợ</label>
                        <div class="col-md-9">
                          {{AccountingCCAccount.CCCreditAccountNo3}}
                        </div>
                      </div>
                      <div class="form-group row align-items-center ">
                        <label class="col-md-3">Nghiệp vụ</label>
                        <div class="col-md-9">
                          {{AccountingCCAccount.CCInTransTypeName3}}
                        </div>
                        <label class="col-md-3">Giá tri âm</label>
                        <div class="col-md-9">
                          {{AccountingCCAccount.EnterNegativeValue3}}
                        </div>
                      </div>
                      <div class="form-group row align-items-center ">
                        <label class="col-md-3">Diễn giải</label>
                        <div class="col-md-21">
                          {{AccountingCCAccount.Description3}}
                        </div>
                      </div>
                    </b-card-text>
                  </b-tab>
                </b-tabs>
              </div>
            </div>
          </div>

        </div>
      </vue-perfect-scrollbar>
    </div>
  </div>
</template>

<script>
import ApiService from '@/services/api.service';
import Swal from 'sweetalert2';
import 'sweetalert2/src/sweetalert2.scss';
const ListRouter = 'accounting-actccaccount';
const EditRouter = 'accounting-actccaccount-edit';
const CreateRouter = 'accounting-actccaccount-create';
const ViewApi = 'accounting/api/actccaccount/view';
const ListApi = 'accounting/api/actccaccount';
const DeleteApi = 'accounting/api/actccaccount/delete';
const UpdateApi = 'accounting/api/actccaccount/update';

export default {
  name: 'accounting-view-item',
  data () {
    return {
      AccountingCCAccount: {
        CCAccountID: null,
        CoaTypeID: null,
        CoaTypeNo: '',
        CoaTypeName: '',
        EntryDebitAccountID: null,
        EntryDebitAccountNo: '',
        EntryCreditAccountID: null,
        EntryCreditAccountNo: '',
        EntryInTransTypeID: null,
        EntryInTransTypeName: '',
        ConditionalExpression: '',
        CCDebitAccountID: null,
        CCDebitAccountNo: '',
        CCCreditAccountID: null,
        CCCreditAccountNo: '',
        CCInTransTypeID: null,
        CCInTransTypeName: '',
        EnterNegativeValue: '',
        Description: '',
        CCDebitAccountID2: null,
        CCDebitAccountNo2: '',
        CCCreditAccountID2: null,
        CCCreditAccountNo2: '',
        CCInTransTypeID2: null,
        CCInTransTypeName2: '',
        EnterNegativeValue2: '',
        Description2: '',
        CCDebitAccountID3: null,
        CCDebitAccountNo3: '',
        CCCreditAccountID3: null,
        CCCreditAccountNo3: '',
        CCInTransTypeID3: null,
        CCInTransTypeName3: '',
        EnterNegativeValue3: '',
        Description3: '',
        Norder: '',
        Inactive: false,
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
          self.AccountingCCAccount.CCAccountID = responsesData.data.data.CCAccountID;
          self.AccountingCCAccount.CoaTypeID = responsesData.data.data.CoaTypeID;
          self.AccountingCCAccount.CoaTypeNo = responsesData.data.data.CoaTypeNo;
          self.AccountingCCAccount.CoaTypeName = responsesData.data.data.CoaTypeName;
          self.AccountingCCAccount.EntryDebitAccountID = responsesData.data.data.EntryDebitAccountID;
          self.AccountingCCAccount.EntryDebitAccountNo = responsesData.data.data.EntryDebitAccountNo;
          self.AccountingCCAccount.EntryCreditAccountID = responsesData.data.data.EntryCreditAccountID;
          self.AccountingCCAccount.EntryCreditAccountNo = responsesData.data.data.EntryCreditAccountNo;
          self.AccountingCCAccount.EntryInTransTypeID = responsesData.data.data.EntryInTransTypeID;
          self.AccountingCCAccount.EntryInTransTypeName = responsesData.data.data.EntryInTransTypeName;
          self.AccountingCCAccount.ConditionalExpression = responsesData.data.data.ConditionalExpression;
          self.AccountingCCAccount.CCDebitAccountID = responsesData.data.data.CCDebitAccountID;
          self.AccountingCCAccount.CCDebitAccountNo = responsesData.data.data.CCDebitAccountNo;
          self.AccountingCCAccount.CCCreditAccountID = responsesData.data.data.CCCreditAccountID;
          self.AccountingCCAccount.CCCreditAccountNo = responsesData.data.data.CCCreditAccountNo;
          self.AccountingCCAccount.CCInTransTypeID = responsesData.data.data.CCInTransTypeID;
          self.AccountingCCAccount.CCInTransTypeName = responsesData.data.data.CCInTransTypeName;
          self.AccountingCCAccount.EnterNegativeValue = responsesData.data.data.EnterNegativeValue;
          self.AccountingCCAccount.Description = responsesData.data.data.Description;
          self.AccountingCCAccount.CCDebitAccountID2 = responsesData.data.data.CCDebitAccountID2;
          self.AccountingCCAccount.CCDebitAccountNo2 = responsesData.data.data.CCDebitAccountNo2;
          self.AccountingCCAccount.CCCreditAccountID2 = responsesData.data.data.CCCreditAccountID2;
          self.AccountingCCAccount.CCCreditAccountNo2 = responsesData.data.data.CCCreditAccountNo2;
          self.AccountingCCAccount.CCInTransTypeID2 = responsesData.data.data.CCInTransTypeID2;
          self.AccountingCCAccount.CCInTransTypeName2 = responsesData.data.data.CCInTransTypeName2;
          self.AccountingCCAccount.EnterNegativeValue2 = responsesData.data.data.EnterNegativeValue2;
          self.AccountingCCAccount.Description2 = responsesData.data.data.Description2;
          self.AccountingCCAccount.CCDebitAccountID3 = responsesData.data.data.CCDebitAccountID3;
          self.AccountingCCAccount.CCDebitAccountNo3 = responsesData.data.data.CCDebitAccountNo3;
          self.AccountingCCAccount.CCCreditAccountID3 = responsesData.data.data.CCCreditAccountID3;
          self.AccountingCCAccount.CCCreditAccountNo3 = responsesData.data.data.CCCreditAccountNo3;
          self.AccountingCCAccount.CCInTransTypeID3 = responsesData.data.data.CCInTransTypeID3;
          self.AccountingCCAccount.CCInTransTypeName3 = responsesData.data.data.CCInTransTypeName3;
          self.AccountingCCAccount.EnterNegativeValue3 = responsesData.data.data.EnterNegativeValue3;
          self.AccountingCCAccount.Description3 = responsesData.data.data.Description3;
          self.AccountingCCAccount.Norder = responsesData.data.data.Norder;
          self.AccountingCCAccount.Inactive = (responsesData.data.data.Inactive) ? true : false;
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

      if (this.reqParams.search.CoaTypeName !== '') {
        requestData.data.CoaTypeName = this.reqParams.search.CoaTypeName;
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
            self.reqParams.idsArray.push(value.CCAccountID);
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
.tab-content{ border-bottom: none !important; border-right: none !important; border-left: none !important;}
.nav-tabs .nav-link{ border-top: none !important; }
.div-scroll-table {
  width: 100%;
  overflow-x: scroll;
  margin-right: 5em;
  overflow-y: visible;
  padding: 0;
}
.td-action-fix-right-form:last-child{
  border-bottom: 1px solid #c8ced3 !important;
  height: 34px;
}
.top-detail i { color: #00a2e8}
.top-detail span:hover{ cursor: pointer}
#bar_detail .form-control{ padding-right: 2px !important; padding-left: 9px !important; border:  none !important;}
.card-header{ padding-top: 5px !important; padding-bottom: 5px !important; background: none !important;}
.card-header .nav-tabs .nav-link{ color: #0b0e0f !important; padding: 0.55rem 0.625rem;}
.card-header .nav-tabs .nav-link:hover{ text-decoration: underline;}
.card-header .nav-tabs .nav-link.active{ font-weight: bold; text-decoration:underline; }
.tab-content{ border: none !important;}
.card-header .nav-tabs{ border: none !important;}
.nav-tabs .nav-link{ border: none !important;}
.table-bordered thead th, .table-bordered thead td {
  border-bottom-width: 1px !important;
}
.comments{ }
.mx-3{ margin-right: 0px !important;}
#bar_detail .new-row{ margin-top: 6px; font-weight: normal; font-size: 14px;}
.td-select2 {
  width: 99% !important;
  max-width: 650px;
  height: 30px;
  overflow-y: hidden;
}
.td-select2 .select2-container .select2-selection--multiple{
  width: 100% !important;
}
.select2-container{ width: 100% !important;}
.table-view tr th { z-index: 10 !important;}
.table-view tr td span{ display: block; padding-right: 3px; padding-left: 3px;}
#member-radio .form-check-inline{ display: block; margin-bottom: 5px;}
.tooltip.b-tooltip { opacity: 1;}
</style>

<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Nghiệp vụ hạch toán tự động: {{AccountingAutoact.AutoactName}}</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onCreateClicked"><i class="fa fa-plus"></i> Thêm</b-button>
              <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="onEditClicked"><i class="fa fa-edit"></i> Sửa</b-button>
              <b-dropdown variant="primary" id="dropdown-actions"  class="main-header-action mr-2" text="Thao tác">
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
            <div class="form-group row">
              <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên</div>
              <div class="col-md-21 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
                {{AccountingAutoact.AutoactName}}
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 mb-0">Loại HTTK: </label>
              <div class="col-md-5">
                {{AccountingAutoact.CoaTypeName}}
              </div>
              <label class="col-md-3 mb-0">Tài khoản Nợ: </label>
              <div class="col-md-5">
                {{AccountingAutoact.DebitAccountNo}}
              </div>
              <label class="col-md-3 mb-0">Tài khoản Có: </label>
              <div class="col-md-5">
                {{AccountingAutoact.CreditAccountNo}}
              </div>
            </div>
            <div class="form-group row align-center">
              <div class="col-md-3">Loại nghiệp vụ</div>
              <div class="col-md-5">{{AccountingAutoact.SysAutoactTypeName}}</div>
              <div class="col-md-3">Loại Thu/ Chi: </div>
              <div class="col-md-5">{{ (AccountingAutoact.AutoactType) ? AutoactTypeOptions[AccountingAutoact.AutoactType].text : ''}}</div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 mb-0">Diễn giải: </label>
              <div class="col-md-21">
                {{AccountingAutoact.Description}}
              </div>
            </div>
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
const ListRouter = 'accounting-actautoact';
const EditRouter = 'accounting-actautoact-edit';
const CreateRouter = 'accounting-actautoact-create';
const ViewApi = 'accounting/api/actautoact/view';
const ListApi = 'accounting/api/actautoact';
const DeleteApi = 'accounting/api/actautoact/delete';
const UpdateApi = 'accounting/api/actautoact/update';

export default {
  name: 'accounting-view-item',
  data () {
    return {
      AccountingAutoact: {
        AutoactID: null,
        AutoactName: '',
        DebitAccountID: null,
        DebitAccountNo: '',
        CreditAccountID: null,
        CreditAccountNo: '',
        Description: '',
        CoaTypeID: null,
        CoaTypeNo: '',
        CoaTypeName: null,
        SnaCreditAccountNo: '',
        SysAutoactTypeID: null,
        SysAutoactTypeName: '',
        Inactive: false,
        AutoactType: null,
      },
      AutoactTypeOptions : [
        {value : 0, text: ''},
        {value : 1, text : 'Thu'},
        {value : 2, text : 'Chi'},
        {value : 3, text : 'Thu và chi'},
      ],
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
          self.AccountingAutoact.AutoactID = responsesData.data.data.AutoactID;
          self.AccountingAutoact.AutoactName = responsesData.data.data.AutoactName;
          self.AccountingAutoact.DebitAccountID = responsesData.data.data.DebitAccountID;
          self.AccountingAutoact.DebitAccountNo = responsesData.data.data.DebitAccountNo;
          self.AccountingAutoact.CreditAccountID = responsesData.data.data.CreditAccountID;
          self.AccountingAutoact.CreditAccountNo = responsesData.data.data.CreditAccountNo;
          self.AccountingAutoact.Description = responsesData.data.data.Description;
          self.AccountingAutoact.CoaTypeID = responsesData.data.data.CoaTypeID;
          self.AccountingAutoact.CoaTypeNo = responsesData.data.data.CoaTypeNo;
          self.AccountingAutoact.CoaTypeName = responsesData.data.data.CoaTypeName;
          self.AccountingAutoact.Inactive = (responsesData.data.data.Inactive) ? true : false;
          self.AccountingAutoact.SysAutoactTypeID = responsesData.data.data.SysAutoactTypeID;
          self.AccountingAutoact.SysAutoactTypeName = responsesData.data.data.SysAutoactTypeName;
          self.AccountingAutoact.AutoactType = responsesData.data.data.AutoactType;

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

      if (this.reqParams.search.AutoactName !== '') {
        requestData.data.AutoactName = this.reqParams.search.AutoactName;
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
            self.reqParams.idsArray.push(value.AutoactID);
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
    idParams() {
        this.fetchData();
    },
    // '$route.params.id': function (id) {
    //   this.idParams = id;
    //   this.fetchData();
    // },

  },
  // beforeDestroy(){
  //     window.removeEventListener('unload', this.onReloadPage)
  // }
}
</script>

<style lang="css">
</style>

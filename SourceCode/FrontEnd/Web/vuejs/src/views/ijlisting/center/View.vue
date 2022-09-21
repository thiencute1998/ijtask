<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Cơ quan trung ương: {{Center.CenterName}}</span>
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
                <span>{{itemNo}} - {{reqParams.total}}</span>
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

            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Mã</label>
              <div class="col-md-5 mb-3 mb-sm-0">
                {{ Center.CenterNo }}
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Tên</label>
              <div class="col-md-21">
                {{ Center.CenterName }}
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Địa chỉ</label>
              <div class="col-md-21">
                {{ Center.CenterAddress }}
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">ĐT di động</label>
              <div class="col-md-5 mb-3 mb-sm-0">
                {{ Center.CenterHandPhone }}
              </div>

              <label class="col-md-2 m-0">ĐT cố định</label>
              <div class="col-md-5 mb-3 mb-sm-0">
                {{ Center.CenterTel }}
              </div>

              <label class="col-md-2 m-0">Fax</label>
              <div class="col-md-7">
                {{ Center.CenterFax }}
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">E-Mail</label>
              <div class="col-md-9 mb-3 mb-sm-0">
                {{ Center.CenterEmail }}
              </div>
              <label class="col-md-3 m-0">Website</label>
              <div class="col-md-9">
                {{ Center.CenterWebsite }}
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Loại</label>
              <div class="col-md-9">
                <span>{{Center.CenterType ? CenterTypeOptions[Center.CenterType].text : ''}}</span>
              </div>
            </div>
            <b>Đại diện</b>
            <hr>

            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Tên</label>
              <div class="col-md-9 mb-3 mb-sm-0">
                {{ Center.ContactName }}
              </div>
              <label class="col-md-3 m-0">Chức vụ</label>
              <div class="col-md-9">
                {{ Center.ContactTitle }}
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">ĐT di động</label>
              <div class="col-md-9 mb-3 mb-sm-0">
                {{ Center.ContactHandPhone }}
              </div>
              <label class="col-md-3 m-0">ĐT cố định</label>
              <div class="col-md-9">
                {{ Center.ContactTel }}
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Email</label>
              <div class="col-md-9 mb-3 mb-sm-0">
                {{ Center.ContactEmail }}
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0"><b>Ghi chú</b></label>
              <div class="col-md-21">
                {{ Center.Note }}
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


const ListRouter = 'listing-center';
const EditRouter = 'listing-center-edit';
const CreateRouter = 'listing-center-create';
const ViewApi = 'listing/api/center/view';
const ListApi = 'listing/api/center';
const DeleteApi = 'listing/api/center/delete';
const UpdateApi = 'listing/api/center/update';

export default {
  name: 'listing-view-item',
  data () {
    return {
      showGeneralInfo: true,
      showCoaconLink: false,
      Center: {
        CenterID: null,
        CenterNo: '',
        CenterName: '',
        CenterAddress: '',
        CenterTel: '',
        CenterHandPhone: '',
        CenterFax: '',
        CenterEmail: '',
        CenterWebsite: '',
        ContactName: '',
        ContactTitle: '',
        ContactTel: '',
        ContactHandPhone: '',
        ContactEmail: '',
        Note: '',
        CenterType: null,
      },
      CenterTypeOptions: [
        {value: 1, text: 'Bộ'},
        {value: 2, text: 'Cơ quan ngang bộ'},
        {value: 3, text: 'Cơ quan trực thuộc trung chính phủ'},
      ],
      CoaconLink: [],
      CoaconOption: [],

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

          self.Center.CenterID = responsesData.data.data.CenterID;
          self.Center.CenterName = responsesData.data.data.CenterName;
          self.Center.CenterNo = responsesData.data.data.CenterNo;
          self.Center.CenterAddress = responsesData.data.data.CenterAddress;
          self.Center.CenterTel = responsesData.data.data.CenterTel;
          self.Center.CenterHandPhone = responsesData.data.data.CenterHandPhone;
          self.Center.CenterFax = responsesData.data.data.CenterFax;
          self.Center.CenterEmail = responsesData.data.data.CenterEmail;
          self.Center.CenterWebsite = responsesData.data.data.CenterWebsite;
          self.Center.ContactName = responsesData.data.data.ContactName;
          self.Center.ContactTitle = responsesData.data.data.ContactTitle;
          self.Center.ContactTel = responsesData.data.data.ContactTel;
          self.Center.ContactHandPhone = responsesData.data.data.ContactHandPhone;
          self.Center.ContactEmail = responsesData.data.data.ContactEmail;
          self.Center.Note = responsesData.data.data.Note;
          self.Center.CenterType = responsesData.data.data.CenterType;

          //Center
          self.CenterOption = [];
          _.forEach(responsesData.Center, function (val, key) {
            let tmpObj = {};
            tmpObj.id = val.CenterID;
            tmpObj.text = val.CenterName;
            self.CenterOption.push(tmpObj);
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

      if (this.reqParams.search.AccountNo !== '') {
        requestData.data.AccountNo = this.reqParams.search.AccountNo;
      }
      if (this.reqParams.search.AccountName !== '') {
        requestData.data.AccountName = this.reqParams.search.AccountName;
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
    onToggleCoaconLink(toggle = true){
      let self = this;
      if(!this.CoaconLink.length){
        let requestData = {
          method: 'get',
          url: '/listing/api/coa_con/get-coa_con-link/' + this.$route.params.id,
          data: {}
        }

        self.$store.commit('isLoading',false);
        ApiService.setHeader();
        ApiService.customRequest(requestData)
          .then(response =>{
            let responseData = response.data;
            self.CoaconLink = responseData.data;
          })
          .catch(error=>{
            console.log(error);

          })
      }
      if(toggle){
        this.showCoaconLink = !this.showCoaconLink;
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

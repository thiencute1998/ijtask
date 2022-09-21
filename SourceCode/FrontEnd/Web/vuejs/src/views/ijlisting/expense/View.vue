<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Khoản chi: {{Expense.ExpenseName}}</span>
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
                  <expense-general-modal v-model="Expense" :title="'Khoản chi : ' + Expense.ExpenseName"></expense-general-modal>
                  <a class="ij-a-icon" @click="showGeneralInfo = !showGeneralInfo">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showGeneralInfo"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showGeneralInfo">
              <expense-general-view v-model="Expense"></expense-general-view>
            </b-collapse>

            <div class="form-group row ij-line-head">
              <label class="col-md-3" @click="onToggleExpenseLink">Danh mục liên kết</label>
              <div class="col-md-21">
                <div class="ij-icon-popup float-right">
                  <ExpenseLinkModal @on:get-data="onToggleExpenseLink(false)" v-model="ExpenseLink" :title="'Danh mục liên kết'" :Expense="Expense"></ExpenseLinkModal>
                  <a class="ij-a-icon" @click="onToggleExpenseLink">
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Thu gọn" v-if="showExpenseLink"></i>
                    <i aria-hidden="true" class="fa fa-expand ij-icon expand-compress" title="Mở rộng" v-else></i>
                  </a>
                </div>
              </div>
            </div>
            <b-collapse v-model="showExpenseLink">
              <ExpenseLinkContent v-model="ExpenseLink"></ExpenseLinkContent>
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
import ExpenseGeneralView from "./partials/ExpenseGeneralView";
import ExpenseGeneralModal from "./partials/ExpenseGeneralModal";
import ExpenseLinkModal from "./partials/ExpenseLinkModal";
import ExpenseLinkContent from "./partials/ExpenseLinkContent";
const ListRouter = 'listing-expense';
const EditRouter = 'listing-expense-edit';
const CreateRouter = 'listing-expense-create';
const ViewApi = 'listing/api/expense/view';
const ListApi = 'listing/api/expense';
const DeleteApi = 'listing/api/expense/delete';
const UpdateApi = 'listing/api/expense/update';

export default {
  name: 'listing-view-item',
  data () {
    return {
      showGeneralInfo: true,
      showExpenseLink: false,
      Expense: {
        SbiItemID: null,
        SbiItemNo: '',
        SbiItemName: '',
        ExpenseID: null,
        ExpenseNo: '',
        ExpenseName: '',
        Note: '',
        Detail: '',
        ParentID: null,
        ParentNo: '',
        ParentName: '',
        UomID: null,
        UomName: '',
        Inactive: false,
        ExpenseCate: [],
        ExpenseCateList: [],
        ExpenseCateValue: [],
        NormID: null,
        NormNo: '',
        NormName: '',
        BudgetBalanceType: 1,
        BudgetStateType: 1,
        SectorID: null,
        SectorNo: '',
        SectorName: '',
      },
      ExpenseLink: [],
      ExpenseOption: [],
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
    ExpenseGeneralView,
    ExpenseGeneralModal,
    ExpenseLinkModal,
    ExpenseLinkContent,
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
          self.Expense.ExpenseID = responsesData.data.data.ExpenseID;
          self.Expense.ExpenseName = responsesData.data.data.ExpenseName;
          self.Expense.ExpenseNo = responsesData.data.data.ExpenseNo;
          self.Expense.SbiItemID = responsesData.data.data.SbiItemID;
          self.Expense.SbiItemNo = responsesData.data.data.SbiItemNo;
          self.Expense.SbiItemName = responsesData.data.data.SbiItemName;
          self.Expense.Note = responsesData.data.data.Note;
          self.Expense.Detail = responsesData.data.data.Detail;
          self.Expense.Inactive = (responsesData.data.data.Inactive) ? true : false;
          self.Expense.UomID = responsesData.data.data.UomID;
          self.Expense.UomName = responsesData.data.data.UomName;
          self.Expense.ParentID = responsesData.data.data.ParentID;
          self.Expense.ParentNo = responsesData.data.Parent.ParentNo;
          self.Expense.ParentName = responsesData.data.Parent.ParentName;
          self.Expense.NormID = responsesData.data.data.NormID;
          self.Expense.NormNo = responsesData.data.data.NormNo;
          self.Expense.NormName = responsesData.data.data.NormName;
          self.Expense.BudgetBalanceType = responsesData.data.data.BudgetBalanceType;
          self.Expense.BudgetStateType = responsesData.data.data.BudgetStateType;
          self.Expense.SectorID = responsesData.data.data.SectorID;
          self.Expense.SectorNo = responsesData.data.data.SectorNo;
          self.Expense.SectorName = responsesData.data.data.SectorName;
          self.Expense.ExpenseCate = [];
          self.$set(self.Expense,'ExpenseCate',[]);
          if(responsesData.data.ExpenseCate){
            _.forEach(responsesData.data.ExpenseCate, (expenseCate, key)=>{
              let tmpObj = {};
              if(expenseCate.CateID){
                let cateList = _.find(responsesData.data.ExpenseCateList, ['CateID', expenseCate.CateID]);
                if(cateList){
                  tmpObj.CateID = cateList.CateID;
                  tmpObj.CateName = cateList.CateName;
                  tmpObj.CateNo = cateList.CateNo;
                }
              }
              if(expenseCate.CateValue){
                // let cateValue = _.find(responsesData.data.ExpenseCateValue, (cate)=> {
                //   return cate.CateID === expenseCate.CateID && cate.CateValue === expenseCate.CateValue;
                // });
                let cateValue = _.find(responsesData.data.ExpenseCateValue,{
                  CateID: expenseCate.CateID,
                  CateValue: expenseCate.CateValue
                });
                if(cateValue){
                  tmpObj.CateValue = cateValue.CateValue;
                  tmpObj.Description = cateValue.Description;
                }
              }

              self.UomOption = [];
              self.UomOptionArr = [];
              _.forEach(responsesData.Uom, function (val, key) {
                if(val.UomID === self.Expense.AuthorizedPerson){
                  self.Expense.UomName = val.UomName;
                }
                self.UomOptionArr[val.UomID] = val.UomName;
                let tmpObj = {};
                tmpObj.id = val.UomID;
                tmpObj.text = val.UomName;
                self.UomOption.push(tmpObj);
              });
              // self.Expense.ExpenseCate.push(tmpObj);
              self.$set(self.Expense.ExpenseCate, self.Expense.ExpenseCate.length, tmpObj);
            })
          }

          //Expense
          self.ExpenseOption = [];
          _.forEach(responsesData.Expense, function (val, key) {
            let tmpObj = {};
            tmpObj.id = val.ExpenseID;
            tmpObj.text = val.ExpenseName;
            self.ExpenseOption.push(tmpObj);
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

      if (this.reqParams.search.ExpenseNo !== '') {
        requestData.data.ExpenseNo = this.reqParams.search.ExpenseNo;
      }
      if (this.reqParams.search.ExpenseName !== '') {
        requestData.data.ExpenseName = this.reqParams.search.ExpenseName;
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
            self.reqParams.idsArray.push(value.ExpenseID);
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
              self.onBackToList('Bản ghi đã được xóa');
            } else if(responseData.status === 4){
              Swal.fire(
                'Có lỗi',
                'Không thể xóa bản ghi tổng hợp',
                'error'
              );
            }else {
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
    onToggleExpenseLink(toggle = true){
      let self = this;
      if(!this.ExpenseLink.length){
        let requestData = {
          method: 'get',
          url: '/listing/api/expense/get-expense-link/' + this.$route.params.id,
          data: {}
        }

        self.$store.commit('isLoading',false);
        ApiService.setHeader();
        ApiService.customRequest(requestData)
          .then(response =>{
            let responseData = response.data;
            self.ExpenseLink = responseData.data;
          })
          .catch(error=>{
            console.log(error);

          })
      }
      if(toggle){
        this.showExpenseLink = !this.showExpenseLink;
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

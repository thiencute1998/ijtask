<template>
  <div>
    <a class="ij-a-icon" @click="showModal" title="Chi tiết">
      <i class="fa fa-external-link ij-icon"></i>
    </a>
    <b-modal scrollable ref="modal" id="modal-form-input-expense-general" size="xl">
      <template v-slot:modal-title>
        <i class="fa fa-edit" v-if="isForm"></i><i class="icon-eye icon" v-else></i> {{title}}
      </template>
      <expense-general-form v-model="value" :expenseOptions="expenseOptions" :uomOptions="uomOptions" v-if="isForm"></expense-general-form>
      <expense-general-view :isDetail="true" v-model="value" v-else></expense-general-view>
      <template v-slot:modal-footer>
        <div class="w-100">
          <b-button variant="primary" size="md" class="mr-2" v-if="!isForm" @click="onEdit">Sửa</b-button>
          <b-button variant="primary" size="md" class="mr-2" v-if="isForm" @click="onUpdate">Lưu</b-button>
          <b-button variant="primary" size="md" class="mr-2" v-if="isForm" @click="onHideModal">Hủy</b-button>
          <b-button variant="primary" size="md" class="mr-2" @click="onHideModal">Đóng</b-button>
        </div>
      </template>
    </b-modal>
  </div>
</template>
<script>
import ApiService from "@/services/api.service";
import Swal from "sweetalert2";
import ExpenseGeneralView from "./ExpenseGeneralView";
import ExpenseGeneralForm from "./ExpenseGeneralForm";

export default {
  name: 'ExpenseGeneralModal',
  props: ['value','title'],
  components: {
    ExpenseGeneralView,
    ExpenseGeneralForm
  },
  data() {
    return{
      isForm: false,
      uomOptions: [],
      expenseOptions: [],
    }
  },
  methods: {
    showModal(){
      this.$refs['modal'].show();
    },
    onHideModal(){
      this.isForm = false;
      this.$refs['modal'].hide();
    },
    onEdit(){
      this.isForm = true;
    },
    onUpdate(){
    let updateAPI = 'listing/api/expense/update/';
      let requestData = {
        method: 'post',
        url: updateAPI + this.value.ExpenseID ,
        data: {
          ExpenseID: this.value.ExpenseID,
          ExpenseNo: this.value.ExpenseNo,
          ExpenseName: this.value.ExpenseName,
          SbiItemID: this.value.SbiItemID,
          SbiItemNo: this.value.SbiItemNo,
          SbiItemName: this.value.SbiItemName,
          ParentID: this.value.ParentID,
          ParentNo: this.value.ParentNo,
          ParentName: this.value.ParentName,
          Note: this.value.Note,
          UomID: this.value.UomID,
          Inactive: (this.value.Inactive) ? 1 : 0,
          ExpenseCate: this.value.ExpenseCate,
          NormID: this.value.NormID,
          NormNo: this.value.NormNo,
          NormName: this.value.NormName,
          BudgetBalanceType: this.value.BudgetBalanceType,
          BudgetStateType: this.value.BudgetStateType,
          SectorID: this.value.SectorID,
          SectorNo: this.value.SectorNo,
          SectorName: this.value.SectorName,
        }
      }
      this.$store.commit('isLoading',true);
      let self = this;
      ApiService.setHeader();
      ApiService.customRequest(requestData)
        .then(response=>{
          let responsesData = response.data;
          if(responsesData.status === 1){
            debugger
            if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
            let item = {
              ExpenseID: responsesData.data,
              ExpenseNo: self.value.ExpenseNo,
              ExpenseName: self.value.ExpenseName,
              SbiItemID: self.value.SbiItemID,
              SbiItemNo: self.value.SbiItemNo,
              SbiItemName: self.value.SbiItemName,
              ParentID: self.value.ParentID,
              ParentNo: self.value.ParentNo,
              ParentName: self.value.ParentName,
              Note: self.value.Note,
              UomID: self.value.UomID,
              Inactive: (self.value.Inactive) ? 1 : 0,
              ExpenseCate: self.value.ExpenseCate,
              NormID: self.value.NormID,
              NormNo: self.value.NormNo,
              NormName: self.value.NormName,
              BudgetBalanceType: self.value.BudgetBalanceType,
              BudgetStateType: self.value.BudgetStateType,
              SectorID: self.value.SectorID,
              SectorNo: self.value.SectorNo,
              SectorName: self.value.SectorName,
            }
            let indexold =  _.findIndex(self.$route.params.req.itemsArray, {'ExpenseID': item.ExpenseID});
            let indexParent = null;
            if(indexold >= 0 && self.$route.params.req.itemsArray){
              item.Detail = 1;
              // set for new Parent
              let ParentOldID =  self.$route.params.req.itemsArray[indexold].ParentID;
              self.$route.params.req.itemsArray.splice(indexold, 1);
              if(item.ParentID) {
                indexParent = _.findIndex(self.$route.params.req.itemsArray, {'ExpenseID': self.value.ParentID});
                if(indexParent >= 0 ){
                  let ClassParentBeforUpdate = self.$route.params.req.itemsArray[indexParent].Class;
                  if(ClassParentBeforUpdate == 'fa fa-plus-square-o'){
                    self.$route.params.req.itemsArray[indexParent].Class = 'fa fa-minus-square-o';
                    self.getListChild(self.$route.params.req.itemsArray[indexParent].ExpenseID);
                  } else {
                    self.$set(self.$route.params.req.itemsArray[indexParent], 'Detail', 0);
                    self.$set(self.$route.params.req.itemsArray[indexParent], 'Class', 'fa fa-minus-square-o');
                    item.Level = self.$route.params.req.itemsArray[indexParent].Level + 1;
                    self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, indexParent + 1, item);
                  };
                }
              }else {
                item.Level = 1;
                self.$route.params.req.itemsArray.push(item);
              }

              // set for ParentOld
              let indexParentOld = _.findIndex(self.$route.params.req.itemsArray, {'ExpenseID':  ParentOldID});
              if(indexParentOld >= 0){
                let ParentOld = self.$route.params.req.itemsArray[indexParentOld];
                let child = _.filter(self.$route.params.req.itemsArray, ['ParentID', ParentOld.ExpenseID]);
                if(child.length > 0){
                  self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Detail', 0);
                  self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Class', 'fa fa-minus-square-o');
                } else {
                  self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Detail', 1);
                  self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Class', '');
                }
              }
            }
            self.isForm = false;
            self.$bvToast.toast('Bản ghi đã được cập nhật', {
              variant: 'success',
              solid: true
            })
          }  else if(responsesData.status === 4){
            self.value.ExpenseNo = responseData.data.ExpenseNo;
            self.value.ParentNo = responseData.data.ParentNo;
            self.value.ParentID = responseData.data.ParentID;
            self.value.ParentName = responseData.data.ParentName ;
            Swal.fire(
              'Thông báo',
              'Không thể sửu mã số và khoản thu cha của khoản thu  tổng hợp',
              'error'
            );
          }
          else {
            let htmlErrors = __.renderErrorApiHtml(responsesData.data);
            Swal.fire(
              'Thông báo',
              htmlErrors,
              'error'
            );
          }
          this.$store.commit('isLoading',false);
        }).catch(error=>{
        Swal.fire('Thông báo','Không kết nối được với máy chủ','error');
        this.$store.commit('isLoading',false);
        console.log(error);
      })
      // ApiService

    },
    onBackToList(message = '') {

      let self = this;
      let params = (this.$route.params.req) ? this.$route.params.req:{};
      let query = this.$route.query;
      query.isBackToList = true;
      if (_.isString(message)) {
        params.message = message;
        this.$router.push({
          name: ViewRouter,
          query: query,
          params: {id: self.idParams, req: params, message: 'Bản ghi đã được cập nhật!'}
        });
      } else {
        this.$router.push({
          name: ListRouter,
          query: query,
          params: params
        });
      }
    },
    getListChild(ExpenseID){
      let self = this;
      let requestData = {
        method: 'post',
        url: 'listing/api/expense/get-list-child',
        data: {
          per_page: this.perPage,
          page: this.currentPage,
          ParentID: ExpenseID,
        },
      };

      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;
        if (responseData.status === 1) {
          debugger
          let listChild = _.toArray(responseData.data);
          _.map(listChild, function(v, k){
            if(v.Detail == 0){
              v.Class = "fa fa-plus-square-o";
            } else {
              v.Class = "";
            }
            return v;
          });
          let keyParent = _.findIndex(self.$route.params.req.itemsArray, ['ExpenseID', ExpenseID]);
          _.forEach(listChild, function (val, key){
            self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, keyParent + 1, val );
          });
          _.orderBy(self.$route.params.req.itemsArray,'ExpenseNo','asc');
        };
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });
      this.isBackToList = false;
    },

  }
}
</script>

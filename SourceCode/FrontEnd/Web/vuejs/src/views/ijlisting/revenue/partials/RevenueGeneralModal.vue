<template>
  <div>
    <a class="ij-a-icon" @click="showModal" title="Chi tiết">
      <i class="fa fa-external-link ij-icon"></i>
    </a>
    <b-modal scrollable ref="modal" id="modal-form-input-revenue-general" size="xl">
      <template v-slot:modal-title>
        <i class="fa fa-edit" v-if="isForm"></i><i class="icon-eye icon" v-else></i> {{title}}
      </template>
      <revenue-general-form v-model="value" :revenueOptions="revenueOptions" :uomOptions="uomOptions" v-if="isForm"></revenue-general-form>
      <revenue-general-view :isDetail="true" v-model="value" v-else></revenue-general-view>
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
import RevenueGeneralView from "./RevenueGeneralView";
import RevenueGeneralForm from "./RevenueGeneralForm";
import IjcoreNumber from "@/components/IjcoreNumber";


export default {
  name: 'RevenueGeneralModal',
  props: ['value','title'],
  components: {
    RevenueGeneralView,
    RevenueGeneralForm,
    IjcoreNumber
  },
  data() {
    return{
      isForm: false,
      uomOptions: [],
      revenueOptions: [],
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
      let self = this;
      let updateAPI = 'listing/api/revenue/update/';
      let requestData = {
        method: 'post',
        url: updateAPI + this.value.RevenueID+'?XDEBUG_SESSION_START=PHPSTORM',
        data: {
          RevenueID: self.value.RevenueID,
          RevenueNo: self.value.RevenueNo,
          RevenueName: self.value.RevenueName,
          SbiItemID: self.value.SbiItemID,
          SbiItemNo: self.value.SbiItemNo,
          SbiItemName: self.value.SbiItemName,
          ParentID: self.value.ParentID,
          ParentNo: self.value.ParentNo,
          ParentName: self.value.ParentName,
          Note: self.value.Note,
          UomID: self.value.UomID,
          UomName: self.value.UomName,
          Inactive: (self.value.Inactive) ? 1 : 0,
          RevenueCate: self.value.RevenueCate,
          NormID: self.value.NormID,
          NormNo: self.value.NormNo,
          NormName: self.value.NormName,
          BudgetBalanceType: self.value.BudgetBalanceType,
          BudgetStateType: self.value.BudgetStateType,
          isRevenueRegulationRate : self.value.isRevenueRegulationRate,
          RevenueReguItem : self.value.RevenueReguItem,
        }
      }
      self.$store.commit('isLoading',true);

      ApiService.setHeader();
      ApiService.customRequest(requestData)
        .then(response=>{
          let responsesData = response.data;
          if(responsesData.status === 1){
            debugger
            if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
            let item = {
              RevenueID: responsesData.data,
              RevenueNo: self.value.RevenueNo,
              RevenueName: self.value.RevenueName,
              SbiItemID: self.value.SbiItemID,
              SbiItemNo: self.value.SbiItemNo,
              SbiItemName: self.value.SbiItemName,
              ParentID: self.value.ParentID,
              ParentNo: self.value.ParentNo,
              ParentName: self.value.ParentName,
              Note: self.value.Note,
              UomID: self.value.UomID,
              UomName: self.value.UomName,
              Inactive: (self.value.Inactive) ? 1 : 0,
              RevenueCate: self.value.RevenueCate,
              NormID: self.value.NormID,
              NormNo: self.value.NormNo,
              NormName: self.value.NormName,
              BudgetBalanceType: self.value.BudgetBalanceType,
              BudgetStateType: self.value.BudgetStateType,
              isRevenueRegulationRate : self.value.isRevenueRegulationRate,
              RevenueReguItem : self.value.RevenueReguItem,
            }
            if(self.$route.params.req){
              let indexold =  _.findIndex(self.$route.params.req.itemsArray, {'RevenueID': item.RevenueID});
              let indexParent = null;
              if(indexold >= 0 && self.$route.params.req.itemsArray){
                item.Detail = 1;
                // set for new Parent
                let ParentOldID =  self.$route.params.req.itemsArray[indexold].ParentID;
                self.$route.params.req.itemsArray.splice(indexold, 1);
                if(item.ParentID) {
                  indexParent = _.findIndex(self.$route.params.req.itemsArray, {'RevenueID': self.value.ParentID});
                  let ClassParentBeforUpdate = self.$route.params.req.itemsArray[indexParent].Class;
                  if(indexParent >= 0 ){
                    if(ClassParentBeforUpdate == 'fa fa-plus-square-o'){
                      self.$route.params.req.itemsArray[indexParent].Class = 'fa fa-minus-square-o';
                      self.getListChild(self.$route.params.req.itemsArray[indexParent].RevenueID);
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
                let indexParentOld = _.findIndex(self.$route.params.req.itemsArray, {'RevenueID':  ParentOldID});
                if(indexParentOld >= 0){
                  let ParentOld = self.$route.params.req.itemsArray[indexParentOld];
                  let child = _.filter(self.$route.params.req.itemsArray, ['ParentID', ParentOld.RevenueID]);
                  if(child.length > 0){
                    self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Detail', 0);
                    self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Class', 'fa fa-minus-square-o');
                  } else {
                    self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Detail', 1);
                    self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Class', '');
                  }
                }
              }
            }
            self.isForm = false;
            self.$bvToast.toast('Bản ghi đã được cập nhật', {
              variant: 'success',
              solid: true
            })
          } else if(responseData.status === 4){
            self.value.RevenueNo = responseData.data.RevenueNo;
            self.value.ParentNo = responseData.data.ParentNo;
            self.value.ParentID = responseData.data.ParentID;
            self.value.ParentName = responseData.data.ParentName ;
            Swal.fire(
              'Thông báo',
              'Không thể sửu mã số và khoản thu cha của khoản thu  tổng hợp',
              'error'
            );
          } else {
            let htmlErrors = __.renderErrorApiHtml(responseData.data);
            Swal.fire(
              'Thông báo',
              htmlErrors,
              'error'
            );
          }
          self.$store.commit('isLoading',false);
        }).catch(error=>{
        Swal.fire('Thông báo','Không kết nối được với máy chủ','error');
        self.$store.commit('isLoading',false);
        console.log(error);
      })
      // ApiService
    },
    getListChild(RevenueID){
      let self = this;
      let requestData = {
        method: 'post',
        url: 'listing/api/revenue/get-list-child',
        data: {
          per_page: this.perPage,
          page: this.currentPage,
          ParentID: RevenueID,
        },
      };

      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;
        if (responseData.status === 1) {
          let listChild = _.toArray(responseData.data);
          _.map(listChild, function(v, k){
            if(v.Detail == 0){
              v.Class = "fa fa-plus-square-o";
            } else {
              v.Class = "";
            }
            return v;
          });
          let keyParent = _.findIndex(self.$route.params.req.itemsArray, ['RevenueID', RevenueID]);
          _.forEach(listChild, function (val, key){
            self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, keyParent + 1, val );
          });
          _.orderBy(self.$route.params.req.itemsArray,'RevenueNo','asc');
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

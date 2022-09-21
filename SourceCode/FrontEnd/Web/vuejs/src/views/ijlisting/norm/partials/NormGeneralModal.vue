<template>
  <div>
    <a class="ij-a-icon" @click="showModal" title="Chi tiết">
      <i class="fa fa-external-link ij-icon"></i>
    </a>
    <b-modal scrollable ref="modal" id="modal-form-input-norm-general" size="xl">
      <template v-slot:modal-title>
        <i class="fa fa-edit" v-if="isForm"></i><i class="icon-eye icon" v-else></i> {{title}}
      </template>
      <norm-general-form v-model="value" v-if="isForm"></norm-general-form>
      <norm-general-view :isDetail="true" v-model="value" v-else></norm-general-view>
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
import NormGeneralView from "./NormGeneralView";
import NormGeneralForm from "./NormGeneralForm";

export default {
  name: 'NormGeneralModal',
  props: ['value','title'],
  components: {
    NormGeneralView,
    NormGeneralForm
  },
  data() {
    return{
      isForm: false,
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
      let updateAPI = 'listing/api/norm/update/';
      let requestData = {

        method: 'post',
        url: updateAPI + this.value.NormID,
        data: {
          NormID: this.value.NormID,
          NormNo: this.value.NormNo,
          ParentID: this.value.ParentID,
          ParentNo: this.value.ParentNo,
          ParentName: this.value.ParentName,
          NormName: this.value.NormName,
          NormType: this.value.NormType,
          Comment: this.value.Comment,
          UomID: this.value.UomID,
          UomNo: this.value.UomNo,
          UomName: this.value.UomName,
          Inactive: (this.value.Inactive) ? 1 : 0,
          NormCate: this.value.NormCate,
          // NormMap: this.value.NormMap,
        }
      }
      if(this.value.NormType == 1 || this.value.NormType == 3){
        requestData.data.RevenueID = this.value.RevenueID;
        requestData.data.RevenueNo = this.value.RevenueNo;
        requestData.data.RevenueName = this.value.RevenueName;
      }
      else if(this.value.NormType == 2 || this.value.NormType == 3){
        requestData.data.ExpenseID = this.value.ExpenseID;
        requestData.data.ExpenseNo = this.value.ExpenseNo;
        requestData.data.ExpenseName = this.value.ExpenseName;
      }
      this.$store.commit('isLoading',true);
      let self = this;
      ApiService.setHeader();
      ApiService.customRequest(requestData)
        .then(response=>{
          let responsesData = response.data;
          if(responsesData.status === 1){
            if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
            let item = {
              NormID: responsesData.data,
              NormNo: self.value.NormNo,
              NormName: self.value.NormName,
              NormType: self.value.NormType,
              ParentID: self.value.ParentID,
              ParentNo: self.value.ParentNo,
              Comment: self.value.Comment,
              UomID: self.value.UomID,
              UomNo: self.value.UomNo,
              UomName: self.value.UomName,
              Inactive: (self.value.Inactive) ? 1 : 0,
              NormCate: self.value.NormCate,
            }
            let indexold =  _.findIndex(self.$route.params.req.itemsArray, {'NormID': item.NormID});
            let indexParent = null;
            if(indexold >= 0 && self.$route.params.req.itemsArray){
              item.Detail = 1;
              // set for new Parent
              let ParentOldID =  self.$route.params.req.itemsArray[indexold].ParentID;
              self.$route.params.req.itemsArray.splice(indexold, 1);
              if(item.ParentID) {
                indexParent = _.findIndex(self.$route.params.req.itemsArray, {'NormID': self.value.ParentID});
                if(indexParent >= 0 ){
                  self.$set(self.$route.params.req.itemsArray[indexParent], 'Detail', 0);
                  self.$set(self.$route.params.req.itemsArray[indexParent], 'Class', 'fa fa-minus-square-o');
                  item.Level = self.$route.params.req.itemsArray[indexParent].Level + 1;
                  self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, indexParent + 1, item);
                }
              }else {
                item.Level = 1;
                self.$route.params.req.itemsArray.push(item);
              }

              // set for ParentOld
              let indexParentOld = _.findIndex(self.$route.params.req.itemsArray, {'NormID':  ParentOldID});
              if(indexParentOld >= 0){
                let ParentOld = self.$route.params.req.itemsArray[indexParentOld];
                let child = _.filter(self.$route.params.req.itemsArray, ['ParentID', ParentOld.NormID]);
                if(child.length >= 0){
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
          } else if(responsesData.status === 4){
            Swal.fire(
              'Lỗi',
              'Không được sửa bản ghi Tổng hợp',
              'error'
            )
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

    }
  }
}
</script>

<template>
  <div>
    <a class="ij-a-icon" @click="showModal" title="Chi tiết">
      <i class="fa fa-external-link ij-icon"></i>
    </a>
    <b-modal scrollable ref="modal" id="modal-form-input-sbi-item-general" size="xl">
      <template v-slot:modal-title>
        <i class="fa fa-edit" v-if="isForm"></i><i class="icon-eye icon" v-else></i> {{title}}
      </template>
      <sbi-item-general-form v-model="value" v-if="isForm"></sbi-item-general-form>
      <sbi-item-general-view :isDetail="true" v-model="value" v-else></sbi-item-general-view>
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
import SbiItemGeneralView from "./SbiItemGeneralView";
import SbiItemGeneralForm from "./SbiItemGeneralForm";

export default {
  name: 'SbiItemGeneralModal',
  props: ['value','title'],
  components: {
    SbiItemGeneralView,
    SbiItemGeneralForm
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
      let updateAPI = 'listing/api/sbi-item/update/';
      let requestData = {
        method: 'post',
        url: updateAPI + this.value.SbiItemID,
        data: {
          SbiItemID: this.value.SbiItemID,
          SbiItemNo: this.value.SbiItemNo,
          SbiItemName: this.value.SbiItemName,
          ParentID: this.value.ParentID,
          ParentName: this.value.ParentName,
          SbiItemType: this.value.SbiItemType,
          SbiItemGroup: this.value.SbiItemGroup,
          RevenueID: this.value.RevenueID,
          RevenueName: this.value.RevenueName,
          ExpenseID: this.value.ExpenseID,
          ExpenseName: this.value.ExpenseName,
          Note: this.value.Note,
          AccessType: this.value.AccessType,
          Inactive: (this.value.Inactive) ? 1 : 0,
        }
      }
      this.$store.commit('isLoading',true);
      let self = this;
      ApiService.setHeader();
      ApiService.customRequest(requestData)
        .then(response=>{
          let responseData = response.data;
          if(responseData.status === 1){
            self.$emit('changeDefaultModel', requestData);
            this.isForm = false;
            this.$refs['modal'].hide();
          }
          else {
            let htmlErrors = __.renderErrorApiHtml(responseData.data);
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

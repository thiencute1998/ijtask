<template>
  <div>
    <a class="ij-a-icon" @click="showModal" title="Chi tiết">
      <i class="fa fa-external-link ij-icon"></i>
    </a>
    <b-modal scrollable ref="modal" id="modal-form-input-coa-ldi-general" size="xl">
      <template v-slot:modal-title>
        <i class="fa fa-edit" v-if="isForm"></i><i class="icon-eye icon" v-else></i> {{title}}
      </template>
      <coa-ldi-general-form v-model="value" :coaLdiOptions="coaLdiOptions" :employeeOptions="employeeOptions" v-if="isForm"></coa-ldi-general-form>
      <coa-ldi-general-view :isDetail="true" v-model="value" v-else></coa-ldi-general-view>
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
import CoaLdiGeneralView from "./CoaLdiGeneralView";
import CoaLdiGeneralForm from "./CoaLdiGeneralForm";

export default {
  name: 'CoaLdiGeneralModal',
  props: ['value','title'],
  components: {
    CoaLdiGeneralView,
    CoaLdiGeneralForm
  },
  data() {
    return{
      isForm: false,
      employeeOptions: [],
      coaLdiOptions: [],
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
      let updateAPI = 'listing/api/coa-ldi/update/';
      let requestData = {
        method: 'post',
        url: updateAPI + this.value.AccountID,
        data: {
          AccountID: this.value.AccountID,
          AccountNo: this.value.AccountNo,
          AccountName: this.value.AccountName,
          ParentID: this.value.ParentID,
          Detail: this.value.Detail,
          BalanceType: this.value.BalanceType,
          Note: this.value.Note,
          CoaLdiCate: this.value.CoaLdiCate,
        }
      }
      this.$store.commit('isLoading',true);
      let self = this;
      ApiService.setHeader();
      ApiService.customRequest(requestData)
      .then(response=>{
        let responseData = response.data;
        if(responseData.status === 1){
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

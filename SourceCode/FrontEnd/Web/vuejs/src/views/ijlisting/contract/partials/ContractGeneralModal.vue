<template>
  <div>
    <a class="ij-a-icon" @click="showModal" title="Chi tiết">
      <i class="fa fa-external-link ij-icon"></i>
    </a>
    <b-modal scrollable ref="modal" id="modal-form-input-contract-general" size="xl">
      <template v-slot:modal-title>
        <i class="fa fa-edit" v-if="isForm"></i><i class="icon-eye icon" v-else></i> {{title}}
      </template>
      <contract-general-form v-model="value" :contractOptions="contractOptions" :employeeOptions="employeeOptions" v-if="isForm"></contract-general-form>
      <contract-general-view :isDetail="true" v-model="value" v-else></contract-general-view>
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
import ContractGeneralView from "./ContractGeneralView";
import ContractGeneralForm from "./ContractGeneralForm";

export default {
  name: 'ContractGeneralModal',
  props: ['value','title'],
  components: {
    ContractGeneralView,
    ContractGeneralForm
  },
  data() {
    return{
      isForm: false,
      employeeOptions: [],
      contractOptions: [],
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
      let updateAPI = 'listing/api/contract/update/';
      let requestData = {
        method: 'post',
        url: updateAPI + this.value.ContractID,
        data: {
          ContractID: this.value.ContractID,
          ContractNo: this.value.ContractNo,
          ContractName: this.value.ContractName,
          Note: this.value.Note,
          ContractType: this.value.ContractType,
          ManagementLevel: this.value.ManagementLevel,
          Inactive: (this.value.Inactive) ? 1 : 0,
          ContractCate: this.value.ContractCate,
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

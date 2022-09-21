<template>
  <div>
    <a class="ij-a-icon" @click="showModal" title="Chi tiết">
      <i class="fa fa-external-link ij-icon"></i>
    </a>
    <b-modal scrollable ref="modal" id="modal-form-input-sbi-category-general" size="xl">
      <template v-slot:modal-title>
        <i class="fa fa-edit" v-if="isForm"></i><i class="icon-eye icon" v-else></i> {{title}}
      </template>
      <sbi-category-general-form v-model="value" :sbiCategoryOptions="sbiCategoryOptions" :uomOptions="uomOptions" v-if="isForm"></sbi-category-general-form>
      <sbi-category-general-view :isDetail="true" v-model="value" v-else></sbi-category-general-view>
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
import SbiCategoryGeneralView from "./SbiCategoryGeneralView";
import SbiCategoryGeneralForm from "./SbiCategoryGeneralForm";

export default {
  name: 'SbiCategoryGeneralModal',
  props: ['value','title'],
  components: {
    SbiCategoryGeneralView,
    SbiCategoryGeneralForm
  },
  data() {
    return{
      isForm: false,
      uomOptions: [],
      sbiCategoryOptions: [],
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
      let updateAPI = 'listing/api/sbi-category/update/';
      let requestData = {
        method: 'post',
        url: updateAPI + this.value.SbiCategoryID,
        data: {
          SbiCategoryID: this.value.SbiCategoryID,
          SbiCategoryNo: this.value.SbiCategoryNo,
          SbiCategoryName: this.value.SbiCategoryName,
          ParentID: this.value.ParentID,
          Note: this.value.Note,
          UomID: this.value.UomID,
          Inactive: (this.value.Inactive) ? 1 : 0,
          SbiCategoryCate: this.value.SbiCategoryCate,
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

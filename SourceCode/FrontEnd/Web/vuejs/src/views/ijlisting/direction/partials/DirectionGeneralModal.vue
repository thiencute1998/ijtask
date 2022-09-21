<template>
  <div>
    <a class="ij-a-icon" @click="showModal" title="Chi tiết">
      <i class="fa fa-external-link ij-icon"></i>
    </a>
    <b-modal scrollable ref="modal" id="modal-form-input-direction-general" size="xl">
      <template v-slot:modal-title>
        <i class="fa fa-edit" v-if="isForm"></i><i class="icon-eye icon" v-else></i> {{title}}
      </template>
      <direction-general-form v-model="value" :directionOptions="directionOptions" :employeeOptions="employeeOptions"  v-if="isForm"></direction-general-form>
      <direction-general-view :isDetail="true" v-model="value" v-else></direction-general-view>
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
import DirectionGeneralView from "./DirectionGeneralView";
import DirectionGeneralForm from "./DirectionGeneralForm";

export default {
  name: 'DirectionGeneralModal',
  props: ['value','title'],
  components: {
    DirectionGeneralView,
    DirectionGeneralForm
  },
  data() {
    return{
      isForm: false,
      employeeOptions: [],
      directionOptions: [],
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
      let updateAPI = 'listing/api/direction/update/';
      let requestData = {
        method: 'post',
        url: updateAPI + this.value.DirectionID,
        data: {
          DirectionNo: this.value.DirectionNo,
          DirectionName: this.value.DirectionName,
          ParentID: this.value.ParentID,
          Inactive: (this.value.Inactive) ? 1 : 0,
          DirectionDate: this.value.DirectionDate,
          CompanyIssuedID: this.value.CompanyIssuedID,
          CompanyIssuedName: this.value.CompanyIssuedName,
          // SignerIssuedID: this.value.SignerIssuedID,
          SignerIssuedName: this.value.SignerIssuedName,
          Closed: this.value.Closed,
          ClosedDate: this.value.ClosedDate,
          Description: this.value.Description,
          AccessType: this.value.AccessType,
          NumberValue: this.value.NumberValue,
          DirectionCate: this.value.DirectionCate
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

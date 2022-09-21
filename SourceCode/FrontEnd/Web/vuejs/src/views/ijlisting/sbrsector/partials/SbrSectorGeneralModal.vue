<template>
  <div>
    <a class="ij-a-icon" @click="showModal" title="Chi tiết">
      <i class="fa fa-external-link ij-icon"></i>
    </a>
    <b-modal scrollable ref="modal" id="modal-form-input-sbr-sector-general" size="xl">
      <template v-slot:modal-title>
        <i class="fa fa-edit" v-if="isForm"></i><i class="icon-eye icon" v-else></i> {{title}}
      </template>
      <sbr-sector-general-form v-model="value" v-if="isForm"></sbr-sector-general-form>
      <sbr-sector-general-view :isDetail="true" v-model="value" v-else></sbr-sector-general-view>
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
import SbrSectorGeneralView from "./SbrSectorGeneralView";
import SbrSectorGeneralForm from "./SbrSectorGeneralForm";

export default {
  name: 'SbrSectorGeneralModal',
  props: ['value','title'],
  components: {
    SbrSectorGeneralView,
    SbrSectorGeneralForm
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
      let updateAPI = 'listing/api/sbr-sector/update/';
      let requestData = {
        method: 'post',
        url: updateAPI + this.value.SbrSectorID+'?XDEBUG_SESSION_START=PHPSTORM',
        data: {
          SbrSectorID: this.value.SbrSectorID,
          SbrSectorNo: this.value.SbrSectorNo,
          SbrSectorName: this.value.SbrSectorName,
          ParentID: this.value.ParentID,
          ParentNo: this.value.ParentNo,
          ParentName: this.value.ParentName,
          SbrSectorType: this.value.SbrSectorType,
          Note: this.value.Note,
          AccessType: this.value.AccessType,
          Inactive: (this.value.Inactive) ? 1 : 0,
          SbrSectorCate: this.value.SbrSectorCate,
        }
      }
      requestData.data.SbiChapterID = this.value.SbiChapterID;
      requestData.data.SbiChapterNo = this.value.SbiChapterNo;
      requestData.data.SbiChapterName = this.value.SbiChapterName;

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

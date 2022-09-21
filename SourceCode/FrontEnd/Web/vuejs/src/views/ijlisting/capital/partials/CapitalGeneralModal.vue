<template>
  <div>
    <a class="ij-a-icon" @click="showModal" title="Chi tiết">
      <i class="fa fa-external-link ij-icon"></i>
    </a>
    <b-modal scrollable ref="modal" id="modal-form-input-capital-general" size="xl" @shown="onShownModal">
      <template v-slot:modal-title>
        <i class="fa fa-edit" v-if="isForm"></i><i class="icon-eye icon" v-else></i> {{title}}
      </template>
      <capital-general-form v-model="value" :capitalOptions="capitalOptions"  v-if="isForm"></capital-general-form>
      <capital-general-view :isDetail="true" v-model="value" v-else></capital-general-view>
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
import CapitalGeneralView from "./CapitalGeneralView";
import CapitalGeneralForm from "./CapitalGeneralForm";

export default {
  name: 'CapitalGeneralModal',
  props: ['value','title'],
  components: {
    CapitalGeneralView,
    CapitalGeneralForm
  },
  data() {
    return{
      isForm: false,
      capitalOptions: [],
    }
  },
  mounted() {},
  methods: {
    showModal(){
      this.$refs['modal'].show();
    },
    onHideModal(){
      this.isForm = false;
      this.$refs['modal'].hide();
    },
    onShownModal(){
      $('.modal-content').removeAttr('tabindex');
    },
    onEdit(){
      this.isForm = true;
    },
    onUpdate(){
      let updateAPI = 'listing/api/capital/update/';
      let requestData = {
        method: 'post',
        url: updateAPI + this.value.CapitalID+'?XDEBUG_SESSION_START=PHPSTORM',
        data: {
          CapitalID: this.value.CapitalID,
          CapitalNo: this.value.CapitalNo,
          CapitalName: this.value.CapitalName,
          ParentID: this.value.ParentID,
          ParentNo: this.value.ParentNo,
          ParentName: this.value.ParentName,
          Note: this.value.Note,
          Inactive: (this.value.Inactive) ? 1 : 0,
          CapitalCate: this.value.CapitalCate,
          CapitalInOut: this.value.CapitalInOut,
          BudgetStateType: this.value.BudgetStateType
        }
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
              CapitalID: responsesData.data,
              CapitalNo: self.value.CapitalNo,
              CapitalName: self.value.CapitalName,
              Inactive: (self.value.Inactive) ? 1 : 0,
              ParentID: self.value.ParentID,
              ParentNo: self.value.ParentNo,
              ParentName: self.value.ParentName,
              CapitalCate: self.value.CapitalCate
            }
            debugger
            let indexold =  _.findIndex(self.$route.params.req.itemsArray, {'CapitalID': item.CapitalID});
            let indexParent = null;
            if(indexold >= 0 && self.$route.params.req.itemsArray){
              item.Detail = 1;
              // set for new Parent
              let ParentOldID =  self.$route.params.req.itemsArray[indexold].ParentID;
              self.$route.params.req.itemsArray.splice(indexold, 1);
              if(item.ParentID) {
                indexParent = _.findIndex(self.$route.params.req.itemsArray, {'CapitalID': self.value.ParentID});
                if(indexParent >= 0 ){
                  let ClassParentBeforUpdate = self.$route.params.req.itemsArray[indexParent].Class;
                  if(ClassParentBeforUpdate == 'fa fa-plus-square-o'){
                    self.$route.params.req.itemsArray[indexParent].Class = 'fa fa-minus-square-o';
                    self.getListChild(self.$route.params.req.itemsArray[indexParent].CapitalID);
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
              let indexParentOld = _.findIndex(self.$route.params.req.itemsArray, {'CapitalID':  ParentOldID});
              if(indexParentOld >= 0){
                let ParentOld = self.$route.params.req.itemsArray[indexParentOld];
                let child = _.filter(self.$route.params.req.itemsArray, ['ParentID', ParentOld.CapitalID]);
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
    },
    getListChild(CapitalID){
      let self = this;
      let requestData = {
        method: 'post',
        url: 'listing/api/capital/get-list-child',
        data: {
          per_page: this.perPage,
          page: this.currentPage,
          ParentID: CapitalID,
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
          let keyParent = _.findIndex(self.$route.params.req.itemsArray, ['CapitalID', CapitalID]);
          _.forEach(listChild, function (val, key){
            self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, keyParent + 1, val );
          });
          _.orderBy(self.$route.params.req.itemsArray,'CapitalNo','asc');
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

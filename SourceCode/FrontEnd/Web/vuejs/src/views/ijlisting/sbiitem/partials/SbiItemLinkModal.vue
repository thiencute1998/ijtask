<template>
  <a @click="onToggleModal" class="ij-a-icon" title="Chi tiết">
    <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
    <b-modal ref="modal" id="modal-form-input-sbi-item-link" scrollable size="xl">
      <template slot="modal-title">
        <i class="fa fa-edit" v-if="isForm"></i><i class="icon-eye icon" v-else></i>{{title}}
      </template>
      <SbiItemLinkContent v-model="value" :isForm="isForm" :SysTable="SysTable"></SbiItemLinkContent>
      <template slot="modal-footer">
        <div class="w-100">
          <b-button variant="primary" class="mr-2" v-if="isForm" @click="onUpdate">Lưu</b-button>
          <b-button variant="primary" class="mr-2" v-else @click="onEdit">Sửa</b-button>
          <b-button variant="primary" class="mr-2" v-if="isForm" @click="onHideModal">Hủy</b-button>
          <b-button variant="primary" @click="onHideModal">Đóng</b-button>
        </div>
      </template>
    </b-modal>
  </a>
</template>
<script>
import ApiService from "@/services/api.service";
import SbiItemLinkContent from "./SbiItemLinkContent";

export default {
  name: 'SbiItemLinkModal',
  props: {
    value: [Array, Object],
    title: {},
    SbiItem: {}

  },
  components: {
    SbiItemLinkContent
  },
  data(){
    return{
      isForm: false,
      tableName: '',
      search: '',
      lenghNo: 0,
      SysTable: [],
    }
  },
  methods: {
    fetchData(){
      let self = this;
      let requestData = {
        method: 'get',
        url: '/listing/api/common/get-table',
        data: {}
      }

      self.$store.commit('isLoading',true);
      ApiService.setHeader();
      ApiService.customRequest(requestData)
        .then(response=> {
          let responseData = response.data
          self.SysTable = [];
          _.forEach(responseData.data, (value, key)=> {
            if (value.TableName !== 'sbi-item') {
              let tmpObj = {};
              tmpObj.value = value.TableName;
              tmpObj.text = value.TableDescription;
              self.SysTable.push(tmpObj);
            }
          })
          self.$store.commit('isLoading',false);
        })
        .catch(error=>{
          self.$store.commit('isLoading',false);
        })
    },
    onToggleModal(){
      this.$emit('on:get-data');
      this.currentPage = 1;
      this.$refs['modal'].show();
      if(this.$parent.showSbiItemLink){
        console.log(this.$parent);
        this.$parent.showSbiItemLink = true;
      }
    },
    onEdit(){
      this.fetchData();
      this.isForm = true;
    },
    onHideModal(){
      this.$refs['modal'].hide();
      this.isForm = false;
    },
    onUpdate(){
      let self = this;
      let requestData = {
        method: 'post',
        url: '/listing/api/sbi-item/sbi-item-data-list',
        data: {
          SbiItemLink : this.value,
          SbiItemID: this.SbiItem.SbiItemID
        }

      }
      self.$store.commit('isLoading',true);
      ApiService.setHeader();
      ApiService.customRequest(requestData)
        .then(response=> {
          let responseData = response.data;
          if(responseData.status === 1){
            if (!self.idParams && responseData.data) self.idParams = responseData.data;
            self.$bvToast.toast('Cập nhật thành công',{
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });
            self.isForm = false;
          }
          else {
            let htmlErrors = __.renderErrorApiHtml(responseData.data);
            Swal.fire(
              'Thông báo',
              htmlErrors,
              'error'
            );
          }
          self.onHideModal();
          self.$store.commit('isLoading',false);
        })
        .catch(error=>{
          Swal.fire(
            'Thông báo',
            'Không kết nối được với máy chủ',
            'error'
          );
          self.$store.commit('isLoading',false);
        })
    }
  }
}
</script>

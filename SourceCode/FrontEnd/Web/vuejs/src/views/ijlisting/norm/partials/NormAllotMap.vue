<template>
  <div>
    <span @click="init($event)">
<!--      <i class="fa fa-sitemap" style="transform: rotate(-90deg);" aria-hidden="true"></i>-->
      <i title="Thiết lập tiêu chí phân bổ dự toán" class="fa fa-external-link" style="font-size: 18px; cursor: pointer;"></i>
    </span>
    <b-modal :ref="'modal-norm-allot-map-' + norm.NormID" :id="'modal-norm-allot-map-' + norm.NormID" size="lg">
      <template slot="modal-title">Thiết lập tiêu chí phân bổ dự toán</template>
      <norm-allot-map-content v-model="NormAllotMap" :norm="norm" :is-form="isForm"></norm-allot-map-content>
      <template v-slot:modal-footer="{ ok, cancel, hide}">
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit" v-if="!isForm">Sửa</b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdate" v-if="isForm">Lưu</b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onView" v-if="isForm">Hủy</b-button>
          <b-button variant="primary" size="md" class="float-left" @click="hide">Đóng</b-button>
        </div>
      </template>

    </b-modal>
  </div>
</template>

<script>
import ApiService from '@/services/api.service';
import Select2 from 'v-select2-component';
import NormAllotMapContent from "./NormAllotMapContent";


export default {
  name: 'norm-allot-map',
  components: {Select2, NormAllotMapContent},
  props: {
    value: [Array, Object],
    norm: [Object, Array]
  },
  computed: {
    rows() {
      return this.totalRows
    },
  },
  data() {
    return {
      NormAllotMap: [],
      isForm: false,
    }
  },
  created() {
  },
  mounted() {
  },
  methods: {
    init(e) {
      e.preventDefault();
      e.stopPropagation();
      if (!this.NormAllotMap.length) {
        this.getNormAllotMap();
      }
      this.$bvModal.show('modal-norm-allot-map-' + this.norm.NormID);
    },
    getNormAllotMap() {
      let self = this;
      let requestData = {
        method: 'get',
        url: 'listing/api/norm/get-norm-allot/' + this.norm.NormID,
        data: {},
      };

      this.$store.commit('isLoading', true);
      ApiService.setHeader();
      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;
        if (responseData.status === 1) {
          self.NormAllotMap = responseData.data;
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });
    },
    onView(){
      this.isForm = false;
    },
    onEdit(){
      this.isForm = true;
    },
    onUpdate() {
      let self = this;
      let requestData = {
        method: 'post',
        url: 'listing/api/norm/update-norm-allot',
        data: {
          NormID: this.norm.NormID,
          NormAllotMap: this.NormAllotMap
        },
      };

      this.$store.commit('isLoading', true);
      ApiService.setHeader();
      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;
        if (responseData.status === 1) {
          this.$bvToast.toast('Cập nhật thành công', {
            title: 'Thông báo',
            variant: 'success',
            solid: true
          });
          self.isForm = false;
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });
    }
  },
  watch: {
  }
}
</script>
<style></style>

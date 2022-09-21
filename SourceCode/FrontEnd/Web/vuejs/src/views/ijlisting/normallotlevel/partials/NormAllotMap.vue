<template>
  <div class="component-norm-allot-map">
<!--    <span @click="init($event)"><i class="fa fa-external-link" style="font-size: 18px; cursor: pointer;" aria-hidden="true"></i></span>-->
    <div v-if="isView">{{selectedName}}</div>
    <div style="position: relative" v-if="!isView">
      <b-form-input :readonly="true" type="text" @click="init($event)" :title="selectedName" v-model="selectedName"></b-form-input>
      <i class="fa fa-external-link icon-popup" @click="init"></i>
    </div>
    <b-modal ref="modal-norm-allot-map" id="modal-norm-allot-map" size="lg">
      <template slot="modal-title">Thiết lập tiêu chí phân bổ dự toán</template>
      <div class="table-responsive table-responsive-assign" ref="DivContainerTable">
        <table class="table b-table table-sm table-bordered">
          <thead>
          <tr class="text-left">
            <th class="pr-3" v-if="!isView" style="width: 5%;">
              <b-form-checkbox v-model="CheckAll" @change="selectAll"></b-form-checkbox>
            </th>
            <th class="pr-3" style="width: 45%">Chỉ tiêu dự toán</th>
            <th class="pr-3">Tiêu chí phân bổ dự toán</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(item, key) in value">
            <td v-if="!isView">
              <b-form-checkbox v-model="value[key].Checked"></b-form-checkbox>
            </td>
            <td>{{item.NormName}}</td>
            <td>{{item.NormAllotName}}</td>
          </tr>
          </tbody>
        </table>
      </div>
      <template v-slot:modal-footer="{ ok, cancel, hide}">
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" v-if="!isView" @click="onUpdate">Đồng ý</b-button>
          <b-button variant="primary" size="md" class="float-left" @click="hide">Đóng</b-button>
        </div>
      </template>

    </b-modal>
  </div>
</template>

<script>
import ApiService from '@/services/api.service';
import Select2 from 'v-select2-component';

export default {
  name: 'norm-allot-map',
  components: {Select2},
  props: {
    value: [Array, Object],
    norm: [Object, Array],
    isView: false
  },
  computed: {
    rows() {
      return this.totalRows
    },
  },
  data() {
    return {
      selectedName: '',
      disableForm: false,
      CheckAll: false
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
      if (!this.norm.NormID) {
        if (this.value && this.value.length) {
          this.value = [];
        }
        this.disableForm = true;
        this.$bvToast.toast('Bạn chưa chọn CTDT', {
          variant: 'warning',
          title: 'Thông báo',
          solid: true
        });
        return false;
      }else {
        this.disableForm = false;
        this.$bvModal.show('modal-norm-allot-map');
      }
    },
    onView(){},
    onEdit(){},
    onUpdate() {
      this.$bvModal.hide('modal-norm-allot-map');
    },
    selectAll(){
      let self = this;
      _.forEach(this.value, function (item, key) {
        self.value[key].Checked = self.CheckAll;
      });
    },
    renderInputName(){
      let self = this;
      this.selectedName = '';
      let itemsSelected = _.filter(this.value, ['Checked', true]);
      _.forEach(itemsSelected, function (item, key) {
        if (item.NormAllotName) {
          self.selectedName += item.NormAllotName;
        }
        if (key !== (self.value.length - 1)) {
          self.selectedName += ', ';
        }
      });
      if (_.isString(this.selectedName) && this.selectedName.length) {
        this.selectedName.trim();
        this.selectedName.substring(0, this.selectedName.length - 1);
      }
    },
  },
  watch: {
    'value': {
      handler(val){
        // do stuff
        this.renderInputName();
      },
      deep: true
    }
  }
}
</script>
<style>
.component-norm-allot-map .icon-popup {
  position: absolute;
  background: #ebebeb;
  top: 50%;
  transform: translateY(-50%);
  right: 10px;
}
</style>

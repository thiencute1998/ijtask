<template>
  <div>
    <div class="table-responsive">
      <table class="not-border">
        <thead>
        <tr class="text-left">
          <th class="pr-3">Nhân viên</th>
          <th class="pr-3">Xem</th>
          <th class="pr-3">Trường xem</th>
          <th class="pr-3">Sửa</th>
          <th class="pr-3">Trường sửa</th>
          <th class="pr-3">Xóa</th>
          <th class="pr-3">Thêm</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(field, key) in per">
          <td class="pr-3">{{EmployeeOption[field.EmployeeID]}}</td>
          <td class="pr-3 text-center">
            <b-form-checkbox :disabled="true" v-model="per[key].Access">
            </b-form-checkbox>
          </td>
          <td class="pr-3">
            <coa-sia-per-field-view class="app-disable" :CoaSia="value" :disable="!per[key].Access" :title="'Phân quyền'" :table="'coa_sia'" v-model="field.AccessField"></coa-sia-per-field-view>
          </td>
          <td class="pr-3 text-center">
            <b-form-checkbox :disabled="true" v-model="per[key].Edit">
            </b-form-checkbox>
          </td>
          <td class="pr-3">
            <coa-sia-per-field-view class="app-disable" :CoaSia="value" :disable="!per[key].Edit" :title="'Phân quyền'" :table="'coa_sia'" v-model="field.EditField"></coa-sia-per-field-view>
          </td>
          <td class="pr-3 text-center">
            <b-form-checkbox :disabled="true" v-model="per[key].Delete">
            </b-form-checkbox>
          </td>
          <td class="pr-3 text-center">
            <b-form-checkbox :disabled="true" v-model="per[key].Create">
            </b-form-checkbox>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>

</template>

<script>
import ApiService from '@/services/api.service';
import CoaSiaPerFieldView from "./CoaSiaPerFieldView";

export default {
  name: 'CoaSiaPerView',
  components: {CoaSiaPerFieldView},
  computed: {},
  data() {
    return {
      OptionAccessType: {
        1 : 'Chia sẻ',
        2 : 'Công khai',
        3 : 'Riêng tư'
      },
    }
  },
  created() {
  },
  mounted() {
  },
  methods: {
    fetchData() {

    },
    onCancelModal(e) {
      this.onResetModal();
      e.preventDefault();
    },
    onHideModal() {
      this.$refs['modal'].hide();
    },
    onToggleModal() {
      let self = this;
      this.currentPage = 1;
      this.$refs['modal'].show();
      this.fetchData();
    },
    onResetModal() {
    },
  },
  watch: {
    currentPage() {
      this.fetchData();
    }
  },
  filters: {},
  props: ['title', 'value', 'name', 'api', 'table', 'Task', 'isDetail', 'per', 'EmployeeOption', 'CoaSiaOption'],
}
</script>
<style>
.mr-bottom-3 {
  margin-bottom: 3px !important;
}

.readonly {
  background-color: #fff !important;
}

.table th, .table td {
  border-bottom: 1px solid #c8ced3;
}

.modal-footer ol, .modal-footer ul, .modal-footer dl {
  margin-bottom: 0;
}
</style>

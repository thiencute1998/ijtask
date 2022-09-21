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
<!--          <th class="pr-3">Thêm</th>-->
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
            <CustomerPerFieldView :Customer="value" :disable="!per[key].Access" :title="'Phân quyền'" :table="'doc'" v-model="field.AccessField"></CustomerPerFieldView>
          </td>
          <td class="pr-3 text-center">
            <b-form-checkbox :disabled="true" v-model="per[key].Edit">
            </b-form-checkbox>
          </td>
          <td class="pr-3">
            <CustomerPerFieldView :Customer="value" :disable="!per[key].Edit" :title="'Phân quyền'" :table="'doc'" v-model="field.EditField"></CustomerPerFieldView>
          </td>
          <td class="pr-3 text-center">
            <b-form-checkbox :disabled="true" v-model="per[key].Delete">
            </b-form-checkbox>
          </td>
<!--          <td class="pr-3 text-center">-->
<!--            <b-form-checkbox :disabled="true" v-model="per[key].Create">-->
<!--            </b-form-checkbox>-->
<!--          </td>-->
        </tr>
        </tbody>
      </table>
    </div>
  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import CustomerPerFieldView from "./CustomerPerFieldView";

  export default {
    name: 'CustomerPerView',
    components: {CustomerPerFieldView},
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
    props: ['title', 'value', 'name', 'api', 'table', 'Task', 'isDetail', 'per', 'EmployeeOption', 'CompanyOption'],
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
    margin-bottom: 0px;
  }

  #modal-form-input-task-general-content .modal-lg .modal-content {
    width: 1024px;
    margin: auto;
  }

  @media (max-width: 1024px) {
    #modal-form-input-task-general-content .modal-lg {
      max-width: 100%;
    }

    #modal-form-input-task-general-content .modal-lg .modal-content {
      width: 90%;
      margin: auto;
    }
  }

  @media (min-width: 992px) {
    #modal-form-input-task-general-content .modal-lg {
      max-width: 100%;
    }
  }
  .width-datepicker-auto .mx-datepicker{
    width: auto;
  }
  .width-select2-auto .select2-container{
    width: 100% !important;
  }
</style>

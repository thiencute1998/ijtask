<template>
  <div class="table-responsive">
    <table class="not-border" v-if="!isForm">
      <thead>
      <tr class="text-left">
        <th class="pr-3" style="min-width: 120px;">Tên</th>
        <th class="pr-3">Ngày tạo</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in value">
        <td class="pr-3">{{item.DocName}}</td>
        <td class="pr-3">{{item.DocDate}}</td>
      </tr>
      </tbody>
    </table>
  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';
  import moment from 'moment';

  export default {
    name: 'DocChildView',
    mixins: [mixinLists],
    components: {

    },
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {
        optionStatus: [
          {value: 0, text: "Chưa hoàn thành"},
          {value: 1, text: "Đã hoàn thành"},
        ],
        listtable: [],
        tableName: '',
        search: '',
        lenghNo: 0,
        object: {
          master: {},
          detail: [],
        },
        TitleModal: '',
      }
    },
    created() {
    },
    mounted() {
    },
    methods: {
      fetchData() {
      },
      onSaveModal() {

      },
      onCancelModal(e) {
        this.onResetModal();
        e.preventDefault();
      },
      onCancelModal(e) {
        this.onResetModal();
        e.preventDefault();
      },
      onHideModal() {
        this.$refs['modal'].hide();
      },
      deleteLine(key) {
        this.value.splice(key, 1);
      }
    },
    watch: {
      currentPage() {
        this.fetchData();
      }
    },
    filters: {
    },
    props: {
      value: [Array, Object],
      title: {},
      name: {},
      api: {},
      table: {},
      Doc: {},
      isForm: false,
      SysTable: {},
      per: {}
    },
  }
</script>
<style>
  .readonly {
    background-color: #fff !important;
  }

  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }

  .modal-footer ol, .modal-footer ul, .modal-footer dl {
    margin-bottom: 0px;
  }

  #modal-form-input-link .modal-lg .modal-content {
    width: 1024px;
    margin: auto;
  }

  @media (max-width: 1024px) {
    #modal-form-input-link .modal-lg {
      max-width: 100%;
    }

    #modal-form-input-link .modal-lg .modal-content {
      width: 90%;
      margin: auto;
    }
  }

  @media (min-width: 992px) {
    #modal-form-input-link .modal-lg {
      max-width: 100%;
    }
  }

  .mx-datepicker {
    display: block !important;
  }

  .NameObject {
    width: 300px;
  }

  .NumberHour {
    width: 80px;
  }


  .td-status {
    width: 150px;
  }
</style>

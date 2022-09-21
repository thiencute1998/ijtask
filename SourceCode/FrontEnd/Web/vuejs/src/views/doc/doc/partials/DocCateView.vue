<template>
  <div>
    <div class="table-responsive">
      <table class="not-border">
        <thead>
        <tr class="text-left">
          <th class="pr-3">Loại tài liệu</th>
          <th class="pr-3">Giá trị</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(field, key) in value">
          <td class="pr-3">{{field.CateName}}</td>
          <td class="pr-3">{{field.Description}}</td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';

  export default {
    name: 'DocCateView',
    mixins: [mixinLists],
    components: {},
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
    props: ['title', 'value', 'name', 'api', 'table', 'Doc'],
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

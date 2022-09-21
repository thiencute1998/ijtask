<template>
  <div class="table-responsive">
    <table class="table b-table table-sm table-bordered table-editable el-first-modal">
      <thead>
      <tr class="text-center">
        <th class="pr-3">Loại</th>
        <th class="pr-3">Mã số</th>
        <th class="pr-3">Tên</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in this.value">
        <td class="td-status">
          <b-form-select v-model="value[key].LinkTable" :options="SysTable"
                         v-on:change="changeSysTable(key)"></b-form-select>
        </td>

        <td style="width: 180px;">
          <IjcoreModalDataListing v-model="value[key]" :title="value[key].LinkTableName"
                                  :api="'/listing/api/common/list'" :table="value[key].LinkTable">
          </IjcoreModalDataListing>
        </td>
        <td>
          <input v-model="value[key].LinkName" class="form-control"/>
        </td>
        <td style="text-align: center;width: 50px;"><i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o"
                                                       style="font-size: 18px; cursor: pointer;"></i></td>
      </tr>
      </tbody>
    </table>

    <a @click="addLine()" class="new-row"><i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới</a>
  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';
  import moment from 'moment';
  import IjcoreModalDataListing from "../../../../components/IjcoreModalDataListing";

  export default {
    name: 'DocLinkForm',
    mixins: [mixinLists],
    components: {
      IjcoreModalDataListing
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
        ViewPerLinkTable: true,
        ViewPerLinkID: true,
        EditPerLinkTable: true,
        EditPerLinkID: true,
      }
    },
    created() {
    },
    mounted() {
    },
    methods: {
      changeSysTable(key) {
        var result = this.SysTable.filter(obj => {
          if (obj.value === this.value[key].LinkTable) {
            return obj;
          }
        });

        this.value[key].LinkTableName = result[0].text;
        this.value[key].LinkID = '';
        this.value[key].LinkName = '';
        this.value[key].LinkNo = '';
        this.TitleModal = this.value[key].LinkTableName;
      },
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
      onToggleModal() {
        let self = this;
        this.currentPage = 1;
        this.$refs['modal'].show();
        this.fetchData();
      },
      onResetModal() {
      },
      clickText: function (event, key) {
        if (this.isForm) {
          event.target.hidden = true;
          event.target.nextSibling.hidden = false;
          this.value[key].addnew = true;
        }
      },
      hideInput: function (event, loop, key) {
        let element = event.target;
        if (event.target.value) {
          for (let i = 1; i <= loop; i++) {
            element = element.parentElement;
          }
          element.hidden = true;
          element.previousSibling.hidden = false;
          this.value[key].addnew = false;
        }
      },
      addLine() {
        this.value.push({
          LinkID: '',
          LinkNo: '',
          LinkName: '',
          LinkTable: '',
          LinkTableName: '',
          addnew: true,
        });
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
      showStatus: function (value) {
        if (value == 0) {
          return "Chưa hoàn thành";
        } else {
          return "Đã hoàn thành";
        }
      }
    },
    props: {
      value: [Array, Object],
      title: {},
      name: {},
      api: {},
      table: {},
      Task: {},
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

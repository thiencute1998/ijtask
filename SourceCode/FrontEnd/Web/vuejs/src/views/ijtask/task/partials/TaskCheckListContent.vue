<template>
  <div class="table-responsive">
    <table class="not-border" v-if="!isForm">
      <thead>
      <tr class="text-left">
        <th class="pr-3" v-if="ViewPerCompletedDate">Ngày hoàn thành</th>
        <th class="pr-3" v-if="ViewPerCheckListName">Tên</th>
        <th class="pr-3" v-if="ViewPerStatus">Tình trạng</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in value">
        <td class="pr-3" v-if="ViewPerCompletedDate">{{item.CompletedDate}}</td>
        <td class="pr-3" v-if="ViewPerCheckListName">{{item.CheckListName}}</td>
        <td class="pr-3" v-if="ViewPerStatus">{{item.Status | showStatus}}</td>
      </tr>
      </tbody>
    </table>

    <table v-if="isForm" class="table b-table table-sm table-bordered table-editable el-first-modal">
      <thead>
      <tr class="text-center">
        <th class="pr-3">Ngày hoàn thành</th>
        <th class="pr-3">Tên</th>
        <th class="pr-3">Tình trạng</th>
        <th v-if="per['Delete']"></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in this.value">
        <td class="DateTimeText" v-if="ViewPerCompletedDate">
          <IjcoreDateTimePicker v-model="value[key].CompletedDate" v-if="EditPerCompletedDate">
          </IjcoreDateTimePicker>
          <input type="text" disabled v-else class="form-control" placeholder="" :value="value[key].CompletedDate"/>
        </td>
        <td class="DateTimeText" v-else>
          <input type="text" disabled class="form-control" placeholder=""/>
        </td>
        <td v-if="ViewPerCheckListName">
          <input v-model="value[key].CheckListName" class="form-control" v-if="EditPerCheckListName"/>
          <input type="text" disabled v-else class="form-control" placeholder="" :value="value[key].CheckListName"/>
        </td>
        <td v-else>
          <input type="text" disabled class="form-control" placeholder=""/>
        </td>
        <td class="td-status" v-if="ViewPerStatus">
          <b-form-select v-model="value[key].Status" :options="optionStatus" v-if="EditPerStatus"></b-form-select>
          <input type="text" disabled v-else class="form-control" placeholder="" :value="value[key].StatusName"/>
        </td>
        <td class="td-status" v-else>
          <input type="text" disabled class="form-control" placeholder=""/>
        </td>
        <td style="text-align: center;width: 50px;" v-if="per['Delete']"><i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o"
                                                       style="font-size: 18px; cursor: pointer;"></i></td>
      </tr>
      </tbody>
    </table>

    <a @click="addLine()" v-if="isForm && per['Edit']" class="new-row"><i aria-hidden="true"
                                                           class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm
      mới</a>
  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';
  import moment from 'moment';
  import IjcoreDateTimePicker from "../../../../components/IjcoreDateTimePicker";

  export default {
    name: 'TaskCheckListContent',
    mixins: [mixinLists],
    components: {
      IjcoreDateTimePicker,
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
        ViewPerCompletedDate: true,
        ViewPerCheckListName: true,
        ViewPerStatus: true,
        EditPerCompletedDate: true,
        EditPerCheckListName: true,
        EditPerStatus: true,
      }
    },
    created() {
    },
    mounted() {

      if(this.value.length == 0){
        this.value.push({
          CompletedDate: '',
          CheckListName: '',
          Status: '',
          addnew: true,
        });
      }

      this.ViewPerCompletedDate = __.perViewColumn(this.per, 'CompletedDate')
      this.ViewPerCheckListName = __.perViewColumn(this.per, 'CheckListName')
      this.ViewPerStatus = __.perViewColumn(this.per, 'Status')
      this.EditPerCompletedDate = __.perEditColumn(this.per, 'CompletedDate')
      this.EditPerCheckListName = __.perEditColumn(this.per, 'CheckListName')
      this.EditPerStatus = __.perEditColumn(this.per, 'Status')
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
          CompletedDate: __.convertDateTime(new Date()),
          CheckListName: '',
          Status: '',
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
        if(value === ''){
          return "";
        }else{
          if (value == 0) {
            return "Chưa hoàn thành";
          } else {
            return "Đã hoàn thành";
          }
        }
      }
    },
    props: {
      value: {},
      title: {},
      name: {},
      api: {},
      table: {},
      Task: {},
      isForm: false,
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
  .mx-datepicker {
    display: block !important;
  }

  .mx-input-wrapper {
    width: 173px !important;
  }

  .mx-datepicker {
    width: 173px !important;
  }

  .td-status {
    width: 150px;
  }
</style>

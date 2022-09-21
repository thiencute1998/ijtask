<template>
  <div class="table-responsive">
    <table class="not-border" v-if="!isForm">
      <thead>
      <tr class="text-left">
        <th class="pr-3" v-if="ViewPerRequestDate">Ngày yêu cầu</th>
        <th class="pr-3" v-if="ViewPerRequestDueDate">Hạn hoàn thành</th>
        <th class="pr-3" v-if="ViewPerDescription">Mô tả</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in value">
        <td class="pr-3" v-if="ViewPerRequestDate">{{item.RequestDate}}</td>
        <td class="pr-3" v-if="ViewPerRequestDueDate">{{item.RequestDueDate | convertServerDateToClientDate}}</td>
        <td class="pr-3" v-if="ViewPerDescription">{{item.Description}}</td>
      </tr>
      </tbody>
    </table>

    <table v-if="isForm" class="table b-table table-sm table-bordered table-editable el-first-modal">
      <thead>
      <tr class="text-center">
        <th>Ngày yêu cầu</th>
        <th>Hạn hoàn thành</th>
        <th>Mô tả</th>
        <th v-if="per['Delete'] == 1"></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in this.value">
        <td class="DateTimeText" v-if="ViewPerRequestDate">
          <IjcoreDateTimePicker v-model="value[key].RequestDate" :allowEmptyClear="true" v-if="EditPerRequestDate">
          </IjcoreDateTimePicker>
          <input type="text" disabled v-else class="form-control" placeholder="" :value="value[key].RequestDate"/>
        </td>
        <td class="DateTimeText" v-else>
          <input type="text" disabled class="form-control" placeholder=""/>
        </td>
        <td class="DateTimeText" v-if="ViewPerRequestDueDate">
          <ijcore-date-picker v-model="value[key].RequestDueDate" v-if="EditPerRequestDueDate" :disable-before-day="Task.StartDate" :disable-after-day="Task.DueDate" disable-format-date="L"></ijcore-date-picker>
          <input type="text" disabled v-else class="form-control" placeholder="" :value="value[key].RequestDueDate"/>
        </td>
        <td class="DateTimeText" v-else>
          <input type="text" disabled class="form-control" placeholder=""/>
        </td>
        <td v-if="ViewPerDescription">
          <input v-model="value[key].Description" class="form-control" v-if="EditPerDescription"/>
          <input type="text" disabled v-else class="form-control" placeholder="" :value="value[key].Description"/>
        </td>
        <td v-else>
          <input type="text" disabled class="form-control" placeholder=""/>
        </td>
        <td style="text-align: center;width: 50px;" v-if="per['Delete'] == 1"><i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o" style="font-size: 18px; cursor: pointer;"></i>
        </td>
      </tr>
      </tbody>
    </table>

    <a @click="addLine()" v-if="isForm && per['Edit'] == 1" class="new-row"><i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i>Thêm mới</a>
  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  import IjcoreDatePickerAssign from "../../../../components/IjcoreDatePickerAssign";
  import IjcoreDateTimePicker from "../../../../components/IjcoreDateTimePicker";
  import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";

  export default {
    name: 'TaskRequestContent',
    components: {
      IjcoreDateTimePicker,
      IjcoreDatePickerAssign,
      IjcoreModalListing,
      IjcoreDatePicker
    },
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {
        listtable: [],
        tableName: '',
        search: '',
        lenghNo: 0,
        ViewPerRequestDate: true,
        ViewPerRequestDueDate: true,
        ViewPerDescription: true,
        EditPerRequestDate: true,
        EditPerRequestDueDate: true,
        EditPerDescription: true,
      }
    },
    created() {
      this.ViewPerRequestDate = __.perViewColumn(this.per, 'RequestDate');
      this.ViewPerRequestDueDate = __.perViewColumn(this.per, 'RequestDueDate');
      this.ViewPerDescription = __.perViewColumn(this.per, 'Description');
      this.EditPerRequestDate = __.perEditColumn(this.per, 'RequestDate');
      this.EditPerRequestDueDate = __.perEditColumn(this.per, 'RequestDueDate');
      this.EditPerDescription = __.perEditColumn(this.per, 'Description');
    },
    mounted() {
      if(this.value.length == 0){
        this.value.push({
          RequestDate: '',
          RequestDueDate: '',
          Description: '',
          addnew: true,
        });
      }
    },
    methods: {
      formatDate(data) {
        data = data.split(' ');
        data = data[0];
        data = data.split('-');
        let dd = data[2];
        let mm = data[1];
        let yyyy = data[0];
        data = dd + '/' + mm + '/' + yyyy;
        return data;
      },
      fetchData() {

      },
      onSaveModal() {

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
          RequestDate: __.convertDateTime(new Date()),
          RequestDueDate: '',
          Description: '',
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
    margin-bottom: 0;
  }

  .mx-datepicker {
    display: block !important;
  }


  .DateTimeText {
    width: 173px;
  }


  .mx-input-wrapper {
    width: 173px !important;
  }

  .mx-datepicker {
    width: 173px !important;
  }
</style>

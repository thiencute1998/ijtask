<template>
  <div class="table-responsive">
    <table class="not-border" v-if="!isForm">
      <thead>
      <tr class="text-left">
        <th class="pr-3">Loại</th>
        <th class="pr-3">Mã số</th>
        <th class="pr-3">Tên</th>
      </tr>
      </thead>
      <tbody>
        <tr v-for="(item, key) in value">
          <td>{{item.LinkTableName}}</td>
          <td>{{item.LinkNo}}</td>
          <td>{{item.LinkName}}</td>
        </tr>
      </tbody>
    </table>
    <table v-else class="table b-table table-sm table-bordered table-editable el-first-modal">
      <thead>
        <tr class="text-center">
          <th class="pr-3">Loại</th>
          <th class="pr-3">Mã số</th>
          <th class="pr-3">Tên</th>
          <th ></th>
        </tr>
      </thead>
      <tbody>
        <tr class="text-left" v-for="(item, key) in value">
          <td class="td-status">
              <b-form-select v-model="value[key].LinkTable" :options="SysTable" @change="changeSysTable(key)"></b-form-select>
          </td>
          <td style="width: 180px;">
            <IjcoreModalDataListing v-model="value[key]" :title="value[key].LinkTableName" :api="'/listing/api/common/list'" :table="value[key].LinkTable"></IjcoreModalDataListing>
          </td>
          <td>
            <b-form-input v-model="value[key].LinkName"></b-form-input>
          </td>
          <td style="text-align: center;width: 50px;"><i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o" style="font-size: 18px; cursor: pointer;"></i></td>
        </tr>
      </tbody>
    </table>
    <a @click="addLine()" v-if="isForm" class="new-row"><i aria-hidden="true"
                                                           class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm
      mới</a>
  </div>
</template>
<script>
import IjcoreModalDataListing from "@/components/IjcoreModalDataListing";

export default {
  name: 'ProgramLinkContent',
  props: {
    value: [Array, Object],
    isForm: false,
    SysTable: {}
  },
  components: {
    IjcoreModalDataListing
  },
  methods: {
    changeSysTable(key){
      let result = this.SysTable.filter(obj => {
        if (obj.value === this.value[key].LinkTable) {
          return obj;
        }
      });

      this.value[key].LinkTableName = result[0].text;
      this.value[key].LinkID = '';
      this.value[key].LinkName = '';
      this.value[key].LinkNo = '';
    },
    addLine(){
      this.value.push({
        LinkID: '',
        LinkName: '',
        LinkNo: '',
        LinkTable: '',
        LinkTableName: '',
      });
    },
    deleteLine(key){
      this.value.splice(key,1);
    }
  }

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

  .td-status {
    width: 150px;
  }
</style>

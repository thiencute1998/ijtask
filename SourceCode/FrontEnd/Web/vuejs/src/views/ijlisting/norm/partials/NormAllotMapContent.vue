<template>
  <div class="table-responsive table-responsive-assign" ref="DivContainerTable">
    <table class="table b-table table-sm table-bordered" v-if="!isForm">
      <thead>
      <tr class="text-left">
        <th class="pr-3" style="width: 5%;">STT</th>
        <th class="pr-3" style="width: 45%">Chỉ tiêu dự toán</th>
        <th class="pr-3">Tiêu chí phân bổ dự toán</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in value">
        <td>{{key + 1}}</td>
        <td>{{item.NormName}}</td>
        <td>{{item.NormAllotName}}</td>
      </tr>
      </tbody>
    </table>
    <table v-if="isForm" class="table b-table table-sm table-bordered table-editable el-first-modal table-fit-content" ref="TableEditAssign">
      <thead>
      <tr class="text-center">
        <th class="pr-3" style="width: 5%;">STT</th>
        <th class="pr-3" style="width: 45%">Chỉ tiêu ĐMDTCS</th>
        <th class="pr-3">Chỉ tiêu ĐMPBDT</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
        <tr v-for="(item, key) in value">
          <td class="pl-2">{{key + 1}}</td>
          <td class="pl-2">{{item.NormName}}</td>
          <td>
            <ijcore-modal-listing
              v-model="value[key]" title="ĐMPBDT" api="/listing/api/common/list"
              field-name="NormAllotName" field-no="NormAllotNo" field-id="NormAllotID"
              :FieldNoConfig="{show: true, tdStyle: {width: '15%'}}"
              table="norm_allot">
            </ijcore-modal-listing>
          </td>
          <td class="text-center">
            <i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o" style="font-size: 18px; cursor: pointer;"></i>
          </td>
        </tr>
      </tbody>
    </table>
    <ijcore-modal-multi-listing
      v-model="value" @changed="addLine" v-if="isForm"
      title="Tiêu chí phân bổ dự toán" :api="'/listing/api/common/list'"
      field-i-d="NormAllotID"
      field-no="NormAllotNo"
      field-name="NormAllotName"
      table="norm_allot"></ijcore-modal-multi-listing>
  </div>
</template>
<script>
  import ApiService from '@/services/api.service';
  import IjcoreModalListing from "@/components/IjcoreModalListing";
  import IjcoreModalMultiListing from "@/components/IjcoreModalMultiListing";
  import Select2 from "v-select2-component";

  export default {
    name: 'TaskAssignContent',
    components: {
      IjcoreModalMultiListing,
      IjcoreModalListing,
      Select2,
    },
    props: {
      value: [Array, Object],
      api: [String],
      isForm: false,
      norm: [Array, Object]
    },
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {}
    },
    created() {},
    mounted() {},
    methods: {
      addLine(link) {
        let linkReset = link.filter(function (val) {
          return val
        });
        let self = this;
        _.forEach(linkReset, function (item, key) {
          let indexExist = _.findIndex(self.value, ['NormAllotID', item.NormAllotID]);
          if (indexExist < 0) {
            self.value.push({
              NormAllotID: item.NormAllotID,
              NormAllotNo: item.NormAllotNo,
              NormAllotName: item.NormAllotName,
              NormID: self.norm.NormID,
              NormNo: self.norm.NormNo,
              NormName: self.norm.NormName
            });
          }
        });
      },
      deleteLine(key) {
        this.value.splice(key, 1);
      }
    },
    watch: {},
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

  .td-select2 {
    width: 650px !important;
    max-width: 650px;
    height: 30px;
    overflow-y: hidden;
  }

  .td-select2 .select2-selection{
    border: none !important;
  }
  .td-select2 .selection {
    width: 100%;
  }
  .td-select2 .select2-container{
    width: auto !important;
    min-width: 100%;
    display: inline-flex;
  }
  .td-select2 .select2-container .select2-selection--multiple{
    width: 100%;
  }
  .td-select2 .select2-selection__choice {
    max-width: none;
  }
  .td-select2 .select2-container--default .select2-selection--multiple .select2-selection__choice{
    margin-top: 1px;
    margin-bottom: 1px;
    height: 30px;
    display: flex;
    padding-top: 2px;
  }
  .td-select2 .select2-container--default.select2-container--focus .select2-selection--multiple{
    display: flex;
    height: 32px;
  }
  .td-select2 .select2-container .select2-selection--multiple{
    min-height: auto;
  }
  .td-select2 .select2-container--default .select2-search--inline .select2-search__field{
    height: 32px !important;
    margin-top: -1px !important;
  }
  .td-select2 .select2-container--default .select2-selection--multiple .select2-selection__rendered li{
    height: 30px;
  }
  .td-select2 .select2-container .select2-selection--multiple .select2-selection__rendered{
    display: flex !important;
  }
  .td-select2 > div{
    height: 100%;
  }

  .table-editable .td-select2 .select2-container--default .select2-selection--multiple .select2-selection__rendered li{
    height: 26px;
  }
  .table-responsive-assign .table-editable .select2-container--default .select2-selection--multiple .select2-selection__choice{
    margin-top: 3px !important;
  }
  .table-fit-content.table.b-table td {
    max-width: none;
  }
</style>

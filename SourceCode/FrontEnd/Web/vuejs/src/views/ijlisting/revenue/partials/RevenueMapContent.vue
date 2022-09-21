<template>
  <div class="table-responsive table-responsive-assign" ref="DivContainerTable">
    <table class="table b-table table-sm table-bordered" >
      <thead>
      <tr class="text-left">
        <th class="pr-3" style="width: 5%;"></th>
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
  </div>
</template>
<script>
  import ApiService from '@/services/api.service';
  import IjcoreModalListing from "@/components/IjcoreModalListing";

  export default {
    name: 'TaskAssignContent',
    components: {
      IjcoreModalListing,

    },
    props: {
      value: [Array, Object],

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

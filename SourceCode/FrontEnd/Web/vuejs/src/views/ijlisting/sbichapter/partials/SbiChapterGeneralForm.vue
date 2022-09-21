<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-md-4 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Tên</div>
      <div class="col-lg-17 col-md-20 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3 app-object-name">
        <input v-model="value.SbiChapterName" type="text" class="form-control" placeholder="Tên chương"/>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-8 app-object-code d-md-none d-sm-none d-none d-lg-flex align-items-center">
        <span>Mã số</span>
        <input type="text" v-model="value.SbiChapterNo" class="form-control" placeholder="Mã số"/>
      </div>

    </div>

    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-md-4 col-sm-4 mb-lg-0 mb-md-3 mb-sm-3" title="Loại chương">Loại chương</div>
      <div class="col-lg-21 col-md-20 col-sm-20 mb-lg-0 mb-md-3 mb-sm-3 mb-3">
        <sbi-chapter-modal-search-input-vcatelist
          v-model="value.SbiChapterCate"
          tableApi="fixed_asset_cate_list"
          refModal="myModalSearchVcatelist"
          id-modal="myModalSearchVcatelist"
          placeholder="Loại chương"
          title-modal="Loại chương" size-modal="lg"></sbi-chapter-modal-search-input-vcatelist>
      </div>
    </div>

    <div class="form-group row align-items-center">
      <div class="col-lg-3 col-lg-3 col-md-4 col-sm-4" title="Quyền truy cập">Quyền truy cập</div>
      <div class="col-lg-9 col-lg-9 col-md-20 col-sm-20">
        <b-form-select v-model="value.AccessType" :options="AccessTypeOptions" id="item-uom"></b-form-select>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-md-3" for="Note">Ghi chú</label>
      <div class="col-md-21">
        <textarea v-model="value.Note" class="form-control" id="Note" rows="3" placeholder="Ghi chú" name="Note"></textarea>
      </div>
    </div>

  </div>

</template>

<script>
  import ApiService from '@/services/api.service';
  import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
  import Select2 from 'v-select2-component';
  import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
  import SbiChapterModalSearchInputVcatelist from "./SbiChapterModalSearchInputVcatelist";

  export default {
    name: 'sbi-chapter-general-form',
    components: {IjcoreDatePicker, Select2, IjcoreModalSearchInput, SbiChapterModalSearchInputVcatelist},
    computed: {},
    data() {
      return {
        AccessTypeOptions:{
          1: 'Chia sẻ',
          2: 'Công khai',
          3: 'Riêng tư'
        }
      }
    },
    created() {},
    mounted() {},
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
    props: ['title', 'value', 'name', 'api', 'table', 'Task', 'isDetail', 'per', 'EmployeeOption', 'CompanyOption', 'SbiChapterPerEmployee'],
  }
</script>
<style>
  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }

</style>

<template>
  <a @click="onToggleModal()" class="new-row mb-2">
    <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm chỉ tiêu
    <b-modal ref="modal" id="modal" size="lg">
      <template slot="modal-title">
        Chỉ tiêu dự toán
      </template>
      <div class="form-group row">
        <div class="col-md-24">
          <b-form-select v-model="NormTableID" :options="value.NormTableOption" @change="getListNormTable($event)"></b-form-select>
        </div>
      </div>
      <div class="table-responsive" v-if="LoadData">
        <table class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
          <thead>
            <tr>
              <th class="pr-3 td-ijcheckbox">
                <b-form-checkbox v-model="CheckAll" @change="checkAllItem($event)">Chọn tất cả định mức dự toán chi tiết</b-form-checkbox>
              </th>
            </tr>
          </thead>
          <tbody>
          <tr v-for="(item, key) in listtable" :style="listtable[key].Style" style="border-bottom: 1px solid #c8ced3;position: relative;"
          >
            <td class="pr-3 td-ijcheckbox" style="border: none;width: 95%;">
              <b-form-checkbox v-model="listtable[key].IsCheck">
                {{listtable[key].NormTableItemName}}
              </b-form-checkbox>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button
            variant="primary"
            size="md"
            class="float-left mr-2"
            @click="updateForParent()"
          >
            Chọn
          </b-button>
          <b-button
            variant="primary"
            size="md"
            class="float-left"
            @click="onHideModal()"
          >
            Đóng
          </b-button>
        </div>
      </template>

    </b-modal>
  </a>
</template>

<script>
import ApiService from '@/services/api.service';
import mixinLists from '@/mixins/lists';
import Select2 from 'v-select2-component';

export default {
  name: 'NormMapForm',
  mixins: [mixinLists],
  components: {Select2},
  computed: {
    rows() {
      return this.totalRows
    },
    getItemSelected(){
      return _.filter(this.listtable, function(value, key){
        return value.IsCheck === true;
      })
    }
  },
  data() {
    return {
      listtable: [],
      search: '',
      NormTableID: null,
      CheckAll: false,
      LoadData: false,
    }
  },
  created() {
  },
  mounted() {
  },
  methods: {
    clickCheckbox(){
      return false;
    },
    fetchData() {

      // scroll to top perfect scroll
      const container = document.querySelector('.b-table-sticky-header');
      if (container) container.scrollTop = 0;

    },
    updateForParent() {
      this.$emit('changed', this.getItemSelected);
      this.$refs['modal'].hide();
      this.onResetModal();
    },
    onSaveModal() {
      this.$bvToast.toast('Đã lưu ràng buộc', {
        variant: 'success',
        solid: true
      });
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
      this.$refs['modal'].show();
    },
    onResetModal() {
      this.listtable = [];
      this.search = '';
      this.NormTableID = null;
      this.CheckAll = false;
      this.LoadData = false;
    },
    getListNormTable($event){
      this.listtable = [];
      this.CheckAll = false;
      let self = this;
      let urlApi = '/listing/api/norm/get-table-item';
      let requestData = {
        method: 'post',
        url: urlApi,
        data: {
          NormTableID: self.NormTableID
        },
      };
      if($event !== null){
        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then(response =>{
          let dataResponse = response.data;
          if(dataResponse.status === 1){
            this.LoadData = true;
            _.forEach(dataResponse.data, function (val, key) {
              let isExist = 0;
              _.forEach(self.value.NormMap, function (v, k) {
                if(v.NormTableItemID == val.NormTableItemID){
                  isExist = 1;
                }
              });
              if(isExist == 0){
                self.listtable.push({
                  NormTableID: val.NormTableID,
                  NormTableName: val.NormTableName,
                  NormTableNo: val.NormTableNo,
                  NormTableItemID : val.NormTableItemID,
                  NormTableItemNo: val.NormTableItemNo,
                  NormTableItemName: val.Description,
                  IsCheck: false
                });
              }
            });
          }
          self.$store.commit('isLoading', false);
        }).catch(error=>{
          console.log(error);
          self.$store.commit('isLoading', false);
        })
      }
      else{
        this.LoadData = false;
      }
    },
    checkAllItem($event){
      _.map(this.listtable, (value)=>{
        return value.IsCheck = $event;
      })
    }
  },
  watch: {
  },
  props: {
    value: {
      type: Object,
      default() {
        return {
        };
      }
    },
  },
}
</script>
<style>
.modal-dialog{
  overflow-y: initial !important
}
.modal-body{
  max-height: 70vh;
  overflow-y: auto;
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
.td-ijcheckbox{
  padding-top: 0px !important;
  padding-bottom: 0px !important;
}
.td-ijcheckbox div{
  height: 30px;
  padding-top: 5px;
}
.td-ijcheckbox div label{
  width: 100%;
  cursor: pointer;
}
a.new-row{
  width: 200px;
}
</style>

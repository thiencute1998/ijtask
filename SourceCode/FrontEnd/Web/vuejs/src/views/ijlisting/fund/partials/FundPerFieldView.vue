<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
    <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
    <b-modal ref="modal" id="modal-form-input-per-module-detail" scrollable size="md">
      <template slot="modal-title">
        <i class="icon-eye icon"></i> {{this.title}}
      </template>
      <div class="table-responsive">
        <table>
          <thead>
          <tr class="text-left">
            <th>
              <b-form-checkbox :disabled="true" v-model="checkAll">
              </b-form-checkbox>
            </th>
            <th>Tên trường</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(item, key) in this.listtable">
            <td class="text-center">
              <b-form-checkbox v-model="item.checked" :disabled="true" @change="checkItem(key, item.checked)">
              </b-form-checkbox>
            </td>
            <td>
              {{item.FieldDescription}}
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

export default {
  name: 'fund-per-field-view',
  components: {},
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
      checkAll: true,
      arrListField: [],
      notFetch: false,
    }
  },
  created() {
  },
  mounted() {
  },
  methods: {
    checkAllItem(checkAll){
      let self = this;
      if(!checkAll){
        _.forEach(this.listtable, function (value, key) {
          value.checked = true;
          self.arrListField.push(value.FieldName);
        });
      }else{
        _.forEach(this.listtable, function (value, key) {
          value.checked = false;
        });
        self.arrListField = [];
      }
      this.$forceUpdate();
    },
    checkItem(key, checked){
      if(checked){
        this.checkAll = false;
        var index = this.arrListField.indexOf(this.listtable[key].FieldName);
        if (index !== -1) this.arrListField.splice(index, 1);
      }else{
        this.arrListField.push(this.listtable[key].FieldName);
      }
    },
    fetchData() {
      let self = this;
      let urlApi = '/task/api/task/table-field-per';
      let requestData = {
        method: 'post',
        url: urlApi,
        data: {
          TableName: 'doc',
        },

      };

      // if (this.modelSearch.CompanyNo.trim() !== '') {
      //     requestData.data.CompanyNo = this.modelSearch.CompanyNo;
      // }

      this.$store.commit('isLoading', true);
      ApiService.customRequest(requestData).then((response) => {
        let dataResponse = response.data;
        if (dataResponse.status === 1) {
          let dataPer = ',' + self.value + ',';
          _.forEach(dataResponse.data, function (value, key) {
            if(dataPer.indexOf(',all,') !== -1){
              self.arrListField.push(value.FieldName);
              value.checked = true;
            }else{
              if(dataPer.indexOf(','+value.FieldName+',') !== -1){
                self.arrListField.push(value.FieldName);
                value.checked = true;
              }else{
                value.checked = false;
                self.checkAll = false;
              }
            }
            self.listtable.push(value);
          });
        }
        self.notFetch = true;
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });

      // scroll to top perfect scroll
      const container = document.querySelector('.b-table-sticky-header');
      if (container) container.scrollTop = 0;

    },
    onCancelModal(e) {
      this.onResetModal();
      e.preventDefault();
    },
    onHideModal() {
      this.$refs['modal'].hide();
    },
    onToggleModal() {
      if(!this.disable){
        this.arrListField = [];
        let self = this;
        this.currentPage = 1;
        this.$refs['modal'].show();
        if(!this.notFetch){
          this.fetchData();
        }
      }
    },
    onResetModal() {
    },
    onEdit() {
    },
  },
  watch: {
  },
  props: {
    value: {},
    title: {},
    name: {},
    api: {},
    table: {},
    tablePer: {},
    isForm: {},
    fieldModel: {},
    disable:{}
  },
}
</script>
<style>
.readonly {
  background-color: #fff !important;
}
.text-center{
  text-align: center;
}
</style>

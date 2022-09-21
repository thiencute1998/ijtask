<template>
  <div class="ijcore ijcore-modal ijcore-modal-search-input" @mouseenter="onMouseenter">
    <b-input-group :class="classInput" :id="idInput" :rel="idInput">
      <b-form-input :readonly="true" :placeholder="placeholder" type="text" @click="init" :title="selectedItem.name" v-model="selectedItem.name" :name="nameInput"></b-form-input>
      <b-input-group-append>
        <b-button v-if="showIconClose" variant="light" @click="onClearValue" class="ijcore-element-clear"><i class="cui-circle-x icons"></i></b-button>
        <b-button variant="light" @click="init" class="ijcore-element-search"><i class="fa fa-search"></i></b-button>
      </b-input-group-append>
    </b-input-group>

    <b-modal :id="idModal" :title="titleModal"
             :content-class="'sb-modal-content' + classModal"
             :ref="refModal"
             :no-fade="noFadeModal"
             :size="sizeModal">

      <template v-slot:modal-footer="{ ok, cancel, hide }">
        <b-button class="ml-0 mr-2" variant="primary" @click="onSubmitSearch">
          Tìm
        </b-button>
        <b-button variant="primary" @click="hide()">
          Đóng
        </b-button>
      </template>

      <div class="ijcore-search-task-link ijcore-modal-search">
        <table class="table b-table table-sm table-bordered table-editable">
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
              <b-form-select v-model="value[key].LinkTable" :options="SysTable" v-on:change="changeSysTable(key)"></b-form-select>
            </td>
            <td style="width: 180px;">
              <IjcoreModalDataListing v-model="value[key]" :title="value[key].LinkTableName"  :api="'/listing/api/common/list'" :table="value[key].LinkTable">
              </IjcoreModalDataListing>
            </td>
            <td><input v-model="value[key].LinkName" class="form-control"/></td>
            <td style="text-align: center;width: 50px;"><i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o" style="font-size: 18px; cursor: pointer;"></i></td>
          </tr>
          </tbody>
        </table>
        <a @click="addLine()" class="new-row"><i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới</a>
      </div>
    </b-modal>
  </div>
</template>

<script>
import ApiService from '@/services/api.service';
import IjcoreModalDataListing from "@/components/IjcoreModalDataListing";
export default {
  name: 'advanced-forms',
  components: {
    IjcoreModalDataListing
  },
  data () {
    return {
      perPage: this.itemPerPage,
      currentPage: 1,
      itemsArray: [],
      totalRows: 10,
      selectedItem: {
        id: '',
        name: ''
      },
      inputFieldId: '',
      inputFieldName: '',
      showIconClose: false,

      SysTable: [],
      // VendorLink: this.value
    }
  },
  props:{
    value: [Array, Object],
    urlApi: {
      type: String,
      default: ''
    },
    titleModal: {
      type: String,
      default: ''
    },
    classModal: {
      type: String,
      default: ''
    },
    refModal: {
      type: String,
      default: 'appModal'
    },
    idModal: {
      type: String,
      default: 'appModal'
    },
    noFadeModal: {
      type: Boolean,
      default: false
    },
    sizeModal: {
      type: String,
      default: 'md' // sm|md|lg|xl
    },
    selectFieldsApi: {
      type: Array,
      default: function () { return [] }
    },
    searchFieldsApi: {
      type: Array,
      default() {
        return [];
      }
    },
    propsTable: {
      type: Object,
      default() {
        return {
          id: '',
          primaryKey: '',
          striped: true,
          bordered: true,
          borderless: false,
          outlined: false,
          dark: false,
          hover: true,
          small: true,
          fixed: true,
          responsive: true,
          stickyHeader: false,
          captionTop: false,
          tableVariant: '',
          tableClass: '',
          stacked: '',
          headVariant: '',
          threadClass: '',
          threadTrClass: {},
          footClone: false,
          footVariant: '',
          tfootClass: {},
          tfootTrClass: {},
          tbodyTrClass: {},
          tbodyClass: {},
          tbodyTransitionProps: {},
          tbodyTransitionHandlers: {}
          // Todo:: add more props for table
        };
      }
    },
    itemPerPage: {
      type: Number,
      default: 6
    },
    idInput: {
      type: String,
      default: 'inputModel'
    },
    classInput: {
      type: String,
      default: ''
    },
    nameInput: {
      type: String,
      default: 'name_input'
    },
    nameInputHidden: {
      type: String,
      default: 'name_input_hidden'
    },
    requestData:{
      type: Object,
      default(){
        return {}
      }
    },
    placeholder: {
      type: String,
      default: ''
    }
  },
  computed: {
    rows() {
      return this.totalRows
    },
  },
  methods: {
    init(){
      let objFieldId = _.find(this.selectFieldsApi, ['fieldForSelected', 'id']);
      let objFieldName = _.find(this.selectFieldsApi, ['fieldForSelected', 'name']);

      this.inputFieldId = (objFieldId) ? objFieldId.field : 'id';
      this.inputFieldName = (objFieldName) ? objFieldName.field : 'name';
      if (!this.SysTable.length) {
        this.fetchData();
      }
      this.$refs[this.refModal].show();
      if (!this.value.length) {
        this.addLine();
      }
    },
    fetchData(){
      let self = this;
      let requestData = {
        method: 'get',
        data: {}
      };
      requestData.url = '/listing/api/common/get-table';
      this.$store.commit('isLoading', true);

      ApiService.setHeader();
      ApiService.customRequest(requestData).then((responses) => {
        let responsesData = responses.data;
        self.SysTable = [];
        _.forEach(responsesData.data, function (value, key) {
          if (value.TableName !== 'renvenue') {
            let tmpObj = {};
            tmpObj.value = value.TableName;
            tmpObj.text = value.TableDescription;
            self.SysTable.push(tmpObj);
          }
        });

        let extraLink = [
          // {
          //   value: 'object',
          //   text: 'Đối tượng khác'
          // },
          // {
          //   value: 'task',
          //   text: 'Công việc'
          // },
          // {
          //   value: 'employee',
          //   text: 'Nhân viên'
          // }
        ];

        self.SysTable = _.unionBy(self.SysTable, extraLink, 'value');

        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });
    },
    getCell(key){
      return `cell(${key})`; // simple string interpolation
    },
    onSubmitSearch(){
      this.$emit('onSubmitSearch');
      this.$bvModal.hide(this.idModal);
    },
    onSelectedItem(item, index, event) {
      let objTmp = {};
      objTmp.id = item[this.inputFieldId];
      objTmp.name = item[this.inputFieldName];
      this.selectedItem = objTmp;
      this.$emit('input', item);
      this.$bvModal.hide(this.idModal);
    },
    onClearValue(){
      this.selectedItem = {
        name: '',
        id: ''
      };
      this.$emit('input', []);
      this.$emit('onClear');
    },
    onMouseenter(){
      if (this.selectedItem.name) {
        this.showIconClose = true;
      }else {
        this.showIconClose = false;
      }
    },
    renderInputName(){
      let self = this;
      this.selectedItem[this.inputFieldName] = '';
      _.forEach(this.value, function (link, key) {
        if (link.LinkTableName) {
          self.selectedItem[self.inputFieldName] += link.LinkTableName;
        }
        if (link.LinkName) {
          self.selectedItem[self.inputFieldName] += ': ' + link.LinkName + ',';
        }
      });
      if (_.isString(this.selectedItem[this.inputFieldName]) && this.selectedItem[this.inputFieldName].length) {
        this.selectedItem[this.inputFieldName].trim();
        this.selectedItem[this.inputFieldName].substring(0, this.selectedItem[this.inputFieldName].length - 1);
      }
    },
    changeSysTable(key){
      let result = this.SysTable.filter(obj => {
        if(obj.value === this.value[key].LinkTable){
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
        LinkNo: '',
        LinkName: '',
        LinkTable: '',
        LinkTableName: '',
        addnew: true,
      });
    },
    deleteLine(key){
      this.value.splice(key, 1);
    }
  },
  watch: {
    currentPage() {
      this.fetchData();
    },
    'value': {
      handler(val){
        // do stuff
        let self = this;
        if (_.isEmpty(this.value)) {
          this.selectedItem = {
            id: '',
            name: ''
          };
        } else {
          this.renderInputName();
        }
      },
      deep: true
    }
  }
}
</script>
<style lang="css">
.ijcore-modal-search-input .input-group-append {
  position: absolute;
  right: 0;
  z-index: 9;
}
.ijcore-modal-search-input .ijcore-element-clear {
  display: none !important;
}
.ijcore-modal-search-input:hover .ijcore-element-clear{
  display: inline-block !important;
}
.ijcore-modal-search-input input {
  padding-right: 56px;
  background: #fff !important;
  border-bottom-right-radius: 0.25rem !important;
  border-top-right-radius: 0.25rem !important;
}
.ijcore-modal-search-input button{
  background: transparent;
  border: none;
  padding: 0.275rem 0.5rem;
}
.ijcore-modal-search-input button:hover{
  background: transparent !important;
}
.ijcore-modal-search-input .input-group {
  align-items: center;
}
.ijcore-modal-data tr {
  cursor: pointer;
}
</style>

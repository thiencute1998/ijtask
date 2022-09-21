<template>
    <div class="ijcore ijcore-modal ijcore-modal-search-input component-task-modal-search-indicator" @mouseenter="onMouseenter">
<!--        <b-input-group :class="classInput" :id="idInput" :rel="idInput">-->
<!--            <b-form-input :readonly="true" :placeholder="placeholder" type="text" @click="init" :title="selectedItem.name" v-model="selectedItem.name" :name="nameInput"></b-form-input>-->
<!--            <b-input-group-append>-->
<!--                <b-button v-if="showIconClose" variant="light" @click="onClearValue" class="ijcore-element-clear"><i class="cui-circle-x icons"></i></b-button>-->
<!--                <b-button variant="light" @click="init" class="ijcore-element-search"><i class="fa fa-search"></i></b-button>-->
<!--            </b-input-group-append>-->
<!--        </b-input-group>-->
      <a @click="init()" class="ij-a-icon" title="Chi tiết">
        <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
      </a>

      <b-modal :id="idModal" :title="titleModal"
               :content-class="'sb-modal-content' + classModal"
               :ref="refModal"
               :no-fade="noFadeModal"
               :size="sizeModal">

        <template v-slot:modal-footer="{ ok, cancel, hide }">
          <b-button variant="primary" class="mr-2" @click="handleSubmitForm">
            Lưu
          </b-button>
          <b-button variant="primary" @click="hide()">
            Đóng
          </b-button>
        </template>

        <div class="ijcore-search-task-indicator ijcore-modal-search">
          <div class="table-responsive">
            <table class="table b-table table-sm table-bordered table-editable table-reset-default">
              <thead>
              <tr class="text-center">
<!--                <th class="pr-3" style="min-width: 200px; width: 25%">Bảng chỉ tiêu</th>-->
                <th class="pr-3" style="min-width: 200px; width: 25%">Chỉ tiêu</th>
                <th class="pr-3" style="min-width: 200px; width: 45%">Nhân viên</th>
                <th class="b-table-sticky-column-right" style="min-width: 40px; width: 5%"></th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(item, key) in this.value">
                <td style="width: 180px;">
                  <ijcore-modal-search-input
                    @on:selected="onSelectedIndicator($event, key)"
                    @on:clear="onClearIndicator(key)"
                    v-model="value[key].Indicator"
                    :select-fields-api="[
                            {field: 'IndicatorID',fieldForSelected: 'id', showInTable: false, key: 'IndicatorID'},
                            {field: 'IndicatorName', fieldForSelected: 'name', showInTable: true, label: 'Tên chỉ tiêu', key: 'IndicatorName', sortable: true, thClass: 'd-none'}
                          ]"
                    :search-fields-api="[{field: 'IndicatorName', placeholder: 'Nhập chỉ tiêu', name: 'IndicatorName', class: '', style: ''}]"
                    tableApi="task_indicator_table_item"
                    ref="myModalSearchInputIndicatorTableItem"
                    :request-data="{TaskID: task.TaskID}"
                    id-modal="myModalSearchInputIndicatorTableItem"
                    :item-per-page="8"
                    placeholder="Chỉ tiêu"
                    :url-api="$store.state.appRootApi + '/task/api/common/get-task-indicator-table-item'"
                    name-input="input-indicator-table-item"
                    title-modal="Chỉ tiêu" size-modal="lg">
                  </ijcore-modal-search-input>
                </td>
                <td class="custom-select2-multiple">
                  <Select2 v-model="value[key].EmployeeIDs" :settings="{multiple: true}" :options="IndicatorEmployee | filterIndicatorEmployee(value[key].Indicator)" @select="onSelectEmployee($event, key)"></Select2>
                </td>
                <td class="b-table-sticky-column-right" style="text-align: center;width: 50px;"><i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o" style="font-size: 18px; cursor: pointer;"></i></td>
              </tr>
              </tbody>
            </table>
          </div>
<!--          <a @click="addLine()" class="new-row"><i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới</a>-->
          <ijcore-modal-multi-listing
            @changed="addLine" :title="'Chỉ tiêu đánh giá'"
            :api="$store.state.appRootApi + '/task/api/common/get-task-indicator-table-item'"
            :table="'task_indicator'" :FieldName="'IndicatorName'"
            :FieldID="'IndicatorID'" :FieldNo="'IndicatorNo'" :FieldUpdate="['IndicatorName', 'IndicatorID', 'IndicatorNo', 'ScaleRateID', 'ScaleRateName', 'IndicatorCalMethod']"
            :FieldWhere="{TaskID: task.TaskID}">
          </ijcore-modal-multi-listing>
        </div>
      </b-modal>
    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import IjcoreModalDataListing from "@/components/IjcoreModalDataListing";
    import IjcoreModalSearchInput from '@/components/IjcoreModalSearchInput';
    import IjcoreSelect2Server from '@/components/IjcoreSelect2Server';
    import Select2 from 'v-select2-component';
    import IjcoreModalMultiListing from "@/components/IjcoreModalMultiListing";

    export default {
      name: 'advanced-forms',
      components: {
        IjcoreModalDataListing,
        IjcoreModalSearchInput,
        IjcoreSelect2Server,
        Select2,
        IjcoreModalMultiListing
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

          //
          indicatorTableOption: [],
          keySelected: null,

          EmployeeOption: [],
          IndicatorEmployee: [],
          // TaskLink: this.value
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
        },
        task: [Array, Object, String, Number]
      },
      computed: {
          rows() {
              return this.totalRows
          },
      },
      mounted(){},
      filters: {
        filterIndicatorEmployee(value, indicator) {
          let result = [];
          if (indicator) {
            let indicatorEmployee = _.filter(value, ['IndicatorID', indicator.IndicatorID]);

            _.forEach(indicatorEmployee, function (item, key) {
              let tmpObj = {};
              tmpObj.id = item.EmployeeID;
              tmpObj.text = item.EmployeeName;
              tmpObj.IndicatorID = item.IndicatorID;
              result.push(tmpObj);
            });
          }

          return result;

        }
      },
      methods: {
        onSelectEmployee(params, key){
          let self = this;
          this.value[key].Employee = [];
          _.forEach(this.value[key].EmployeeIDs, function (EmployeeID, index) {
            let indicatorEmployee = _.find(self.IndicatorEmployee, ['EmployeeID', Number(EmployeeID)]);
            let tmpObj = {};
            tmpObj.EmployeeID = indicatorEmployee.EmployeeID;
            tmpObj.EmployeeName = indicatorEmployee.EmployeeName;
            self.value[key].Employee.push(tmpObj);
          });
        },
        onSelectedIndicator(indicator, key){
          let self = this;
          let indicatorExist = _.filter(this.value, function (o) {
            return  o.Indicator.IndicatorID == self.value[key].Indicator.IndicatorID
          });
          if (indicatorExist.length > 1) {
            this.value[key].Indicator = {};
            this.value[key].EmployeeIDs = [];
            this.value[key].Employee = [];
            this.$bvToast.toast('Chỉ tiêu đã được chọn', {
              title: 'Thông báo',
              variant: 'warning',
              solid: true
            });
            return;
          }

          let indicatorEmployee = _.filter(this.IndicatorEmployee, ['IndicatorID', indicator.IndicatorID]);
          this.value[key].EmployeeIDs = [];
          this.value[key].Employee = [];
          _.forEach(indicatorEmployee, function (item, _key) {
            self.value[key].EmployeeIDs.push(item.EmployeeID);
            self.value[key].Employee.push({
              EmployeeID: item.EmployeeID,
              EmployeeName: item.EmployeeName
            });
          });

        },
        onClearIndicator(key){
          this.value[key].EmployeeIDs = [];
          this.value[key].Employee = [];
        },
        init(){
          let objFieldId = _.find(this.selectFieldsApi, ['fieldForSelected', 'id']);
          let objFieldName = _.find(this.selectFieldsApi, ['fieldForSelected', 'name']);

          this.inputFieldId = (objFieldId) ? objFieldId.field : 'id';
          this.inputFieldName = (objFieldName) ? objFieldName.field : 'name';
          if (!this.indicatorTableOption.length) {
            this.fetchData();
          }
          this.$refs[this.refModal].show();
          if (!this.$parent.showTaskIndicator) this.$parent.showTaskIndicator = true;
        },
        fetchData(){
          let self = this;
          let requestData = {
            method: 'post',
            data: {
              TaskID: this.task.TaskID,
              mergeEmployee: true
            }
          };
          requestData.url = '/task/api/common/get-task-indicator-table-item';
          this.$store.commit('isLoading', true);

          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            let responsesData = responses.data;
            self.IndicatorEmployee = responsesData.data;
            self.$store.commit('isLoading', false);
          }, (error) => {
            console.log(error);
            self.$store.commit('isLoading', false);
          });
        },
        handleSubmitForm(){
          let self = this;
          let requestData = {
            method: 'post',
            data: {
              TaskID: this.task.TaskID,
              TaskNo: this.task.TaskNo,
              Indicator: this.value
            }
          };
          requestData.url = '/task/api/task/task-indicator';
          this.$store.commit('isLoading', true);

          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            let responsesData = responses.data;
            this.$bvModal.hide(this.idModal);
            if (responsesData.status === 1) {
              this.$bvToast.toast('Cập nhật thành công', {
                title: 'Thông báo',
                variant: 'success',
                solid: true
              });
            }
            self.$store.commit('isLoading', false);
            self.$_storeTaskNotice(self.task.TaskID, 'indicator');
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
        addLine(listTable){
          let self = this;
          listTable.forEach(function (item, key) {
            if (item) {
              let existIndicator = _.find(self.value, function (o) {
                return o.Indicator.IndicatorID === item.IndicatorID;
              });
              if (!existIndicator) {
                let tmpObj = {
                  Indicator: null,
                  EmployeeIDs: [],
                  Employee: []
                };
                tmpObj.Indicator = item;
                let indicatorEmployee = _.filter(self.IndicatorEmployee, ['IndicatorID', item.IndicatorID]);
                _.forEach(indicatorEmployee, function (ie, _key) {
                  tmpObj.EmployeeIDs.push(ie.EmployeeID);
                  tmpObj.Employee.push({
                    EmployeeID: ie.EmployeeID,
                    EmployeeName: ie.EmployeeName
                  });
                });
                self.$set(self.value, self.value.length, tmpObj);
              }
            }
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

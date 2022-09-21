<template>
    <div class="ijcore ijcore-modal ijcore-modal-search-input component-modal-search-input" @mouseenter="onMouseenter">
        <b-input-group :class="classInput" :id="idInput" :rel="idInput">
            <b-form-input :readonly="true" :placeholder="placeholder" type="text" @click="init" :title="selectedItem.name" v-model="selectedItem.name" :name="nameInput"></b-form-input>
            <b-input-group-append>
                <b-button v-if="showIconClose" variant="light" @click="onClearValue" class="ijcore-element-clear"><i class="cui-circle-x icons"></i></b-button>
                <b-button variant="light" @click="init" class="ijcore-element-search"><i class="fa fa-search"></i></b-button>
            </b-input-group-append>
            <input type="hidden" v-model="selectedItem.id" :name="nameInputHidden" />
        </b-input-group>

        <b-modal :id="idModal" :title="titleModal"
                 :content-class="'modal-search-input ' + classModal"
                 :ref="refModal"
                 :no-fade="noFadeModal"
                 :size="sizeModal" ok-title="Đóng" ok-only>
          <div v-if="!multiColumn" class="ijcore-search ijcore-modal-search pt-10">
            <b-input-group v-if="searchFieldsApi.length > 1">
              <b-form-input
                v-for="(searchField, index) in searchFieldsApi" :key="searchField.id"
                v-model="searchFieldModel[searchField.field]"
                :class="searchField.class"
                type="text"
                class="mr-3"
                :style="searchField.style"
                @keyup.enter="onSubmitSearch"
                @blur="onSubmitSearch"
                :placeholder="searchField.placeholder">
              </b-form-input>

                <!-- Attach Right button -->
              <b-button variant="primary" @click="onSubmitSearch">
                  <i class="fa fa-search"></i>
              </b-button>
            </b-input-group>

            <b-form-group v-else>
              <b-input-group>
                <b-form-input
                  :class="searchFieldsApi[0].class"
                  :style="searchFieldsApi[0].style"
                  :placeholder="searchFieldsApi[0].placeholder"
                  @keyup.enter="onSubmitSearch"
                  v-model="searchFieldModel[searchFieldsApi[0].field]"
                  type="text">
                </b-form-input>
                <!-- Attach Right button -->
                <b-input-group-append>
                    <b-button variant="primary" @click="onSubmitSearch">
                        <i class="fa fa-search"></i>
                    </b-button>
                </b-input-group-append>
              </b-input-group>
            </b-form-group>

          </div>
          <div v-else class="pb-10"></div>
          <div class="ijcore-modal-data">
            <b-table :hover="propsTable.hover" :striped="propsTable.striped"
                     :bordered="propsTable.bordered"
                     :small="propsTable.small"
                     :fields="captions"
                     class="mb-0 pb-10"
                     :foot-clone="propsTable.footClone"
                     @row-clicked="onSelectedItem"
                     fixed="fixed" responsive="sm" :items="itemsArray">


              <template slot="top-row" slot-scope="data" v-if="multiColumn">
                <td role="cell" v-for="(field, key) in searchFieldsApi">
                  <b-form-input
                    :class="field.class"
                    :style="field.style"
                    :placeholder="field.placeholder"
                    @keyup.enter="onSubmitSearch"
                    v-model="searchFieldModel[field.field]"
                    type="text">
                  </b-form-input>
                </td>
              </template>

              <template v-slot:[getCell(inputFieldName)]="data">
                <span :title="data.item[inputFieldName]">{{data.item[inputFieldName]}}</span>
              </template>
            </b-table>
          </div>

          <template v-slot:modal-footer="{ ok, cancel, hide }">
            <b-button class="ml-0" variant="primary" @click="hide()">
              Đóng
            </b-button>
            <div class="ijcore-modal-pagination mr-0">
              <div class="overflow-auto">
                <b-pagination
                  v-model="currentPage"
                  :total-rows="rows"
                  :per-page="perPage"
                  aria-controls="my-table"
                ></b-pagination>
              </div>
            </div>
          </template>

        </b-modal>
    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import _ from 'lodash';
    export default {
      name: 'advanced-forms',
      components: {},
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
          searchFieldModel:{},
          inputFieldId: '',
          inputFieldName: '',
          showIconClose: false
        }
      },
      updated() {
        if (!_.isEmpty(this.value)) {
          this.selectedItem.id = this.value[this.inputFieldId];
          this.selectedItem.name = this.value[this.inputFieldName];
        }
      },
      mounted(){
        let objFieldId = _.find(this.selectFieldsApi, ['fieldForSelected', 'id']);
        let objFieldName = _.find(this.selectFieldsApi, ['fieldForSelected', 'name']);

        this.inputFieldId = (objFieldId) ? objFieldId.field : 'id';
        this.inputFieldName = (objFieldName) ? objFieldName.field : 'name';

        if (!_.isEmpty(this.value)) {
          this.selectedItem.id = this.value[this.inputFieldId];
          this.selectedItem.name = this.value[this.inputFieldName];
        }
      },
      props:{
        value: {
            type: Object,
            default () {
                return {}
            }
        },
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
                    striped: false,
                    bordered: false,
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
        captions: function() {
            let fieldsTable = [];
            _.forEach(this.selectFieldsApi, function (value, index) {
                let objTmp = {};
                objTmp.label = value.label;
                objTmp.key = value.key;
                objTmp.headerTitle = value.headerTitle;
                objTmp.headerAbbr = value.headerAbbr;
                objTmp.class = value.class;
                objTmp.formatter = value.formatter;
                objTmp.sortable = value.sortable;
                objTmp.sortDirection = value.sortDirection;
                objTmp.sortByFormatted = value.sortByFormatted;
                objTmp.filterByFormatted = value.filterByFormatted;
                objTmp.tdClass = value.tdClass;
                objTmp.thClass = value.thClass;
                objTmp.thStyle = value.thStyle;
                objTmp.variant = value.variant;
                objTmp.tdAttr = value.tdAttr;
                objTmp.isRowHeader = value.isRowHeader;
                objTmp.stickyColumn = value.stickyColumn;
                // fieldsTable[value.field] = objTmp;
                if (!value.showInTable) {
                    objTmp.thClass = 'd-none';
                    objTmp.tdClass = 'd-none';
                }
                fieldsTable.push(objTmp);
            });
            return fieldsTable;
        },
        multiColumn(){
          let shownColumn = _.filter(this.selectFieldsApi, ['showInTable', true]);
          if (shownColumn.length > 1) return true;
          return false;
        }
      },
      methods: {
        init(){
          if (!this.itemsArray.length) {
            this.fetchData();
          }
          this.$refs[this.refModal].show();
          this.$emit('on:init')
        },
        fetchData(){
          let self = this;
          let requestData = {
            method: 'post',
            url: this.urlApi,
            data: {
                per_page: this.perPage,
                page: this.currentPage
            },
          };

          _.forEach(this.searchFieldsApi, function (value, key) {
            requestData.data[value.field] = self.searchFieldModel[value.field];
          });

          // send request data
          _.forEach(this.requestData, function (data, key) {
            requestData.data[key] = data;
          });


          self.$store.commit('isLoading', true);
          ApiService.customRequest(requestData).then((response) => {
            self.$store.commit('isLoading', false);
            let responseData = response.data;
            if (responseData.status === 1) {
              self.itemsArray = _.toArray(responseData.data.data);
              self.perPage = responseData.data.per_page;
              self.currentPage = responseData.data.current_page;
              self.totalRows = responseData.data.total;
            }
          }, (error) => {
            console.log(error);
            self.$store.commit('isLoading', false);
          });
        },
        getCell(key){
          return `cell(${key})`; // simple string interpolation
        },
        onSubmitSearch(){
            this.fetchData();
        },
        onSelectedItem(item, index, event) {
          let objTmp = {};
          objTmp.id = item[this.inputFieldId];
          objTmp.name = item[this.inputFieldName];
          this.selectedItem = objTmp;
          this.$forceUpdate();
          this.$emit('input', item);
          this.$emit('on:selected', item);
          this.$bvModal.hide(this.idModal);
        },
        onClearValue(){
          this.selectedItem = {};
          this.$emit('input', {});
          this.$emit('on:clear');
        },
        onMouseenter(){
          if (this.selectedItem.name) {
            this.showIconClose = true;
          }else {
            this.showIconClose = false;
          }
        },
      },
      watch: {
        currentPage() {
          this.fetchData();
        },
        'value': {
          handler(val){
            // do stuff
            if (_.isEmpty(this.value)) {
              this.selectedItem = {
                name: '',
                id: ''
              };
            }
          },
          deep: true
        },
        requestData: {
          handler(val){
            // do stuff
            this.itemsArray = [];
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
    .modal-search-input .modal-footer {
      justify-content: space-between;
    }
    .modal-search-input table th {
      font-weight: normal;
    }
    .modal-search-input .form-group {
      margin-bottom: 0;
    }
    .modal-search-input .input-group .form-control{
      border-bottom-left-radius: 0;
    }
    .modal-search-input .input-group-append .btn{
      border-bottom-right-radius: 0;
    }
</style>

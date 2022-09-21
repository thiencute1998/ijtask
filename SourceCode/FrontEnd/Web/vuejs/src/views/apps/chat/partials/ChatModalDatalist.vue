<template>
    <div class="component-modal-datalist">
      <b-modal :id="idModal" :title="titleModal"
               :content-class="'sb-modal-content' + classModal"
               :ref="refModal"
               :no-fade="noFadeModal"
               :size="sizeModal">

        <template v-slot:modal-footer="{ ok, cancel, hide }">
          <b-button class="ml-0 mr-2" variant="primary" @click="onSaveDatalist" v-if="!isSearch">
            Lưu
          </b-button>
          <b-button class="ml-0 mr-2" variant="primary" @click="onSaveDatalist" v-if="isSearch">
            Tìm
          </b-button>
          <b-button variant="primary" @click="hide()">
            Đóng
          </b-button>
        </template>

        <div class="modal-datalist">
          <table class="table b-table table-sm table-bordered table-editable">
            <thead>
            <tr class="text-center">
              <th class="pr-3" style="width: 20%">Đối tượng</th>
              <th class="pr-3">Mã số</th>
              <th class="pr-3">Tên</th>
              <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, key) in datalist">
              <td class="td-status">
                <b-form-select v-model="datalist[key].DatalistTable" :options="SysTable" v-on:change="changeSysTable(key)"></b-form-select>
              </td>
              <td style="width: 180px;">
                <IjcoreModalDataListing v-if="item.DatalistTable !== 'tag'" v-model="datalist[key]" :title="datalist[key].LinkTableName"  :api="'/listing/api/common/list'" :table="datalist[key].DatalistTable">
                </IjcoreModalDataListing>
              </td>
              <td>
                <input v-if="item.DatalistTable !== 'tag'" v-model="datalist[key].LinkName" class="form-control"/>
                <input v-else v-model="datalist[key].LinkName" placeholder="#SmartBooks #Pabmis" class="form-control"/>
              </td>
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
        name: 'chat-modal-datalist',
        components: {
          IjcoreModalDataListing
        },
        data () {
          return {
            perPage: this.itemPerPage,
            currentPage: 1,
            itemsArray: [],
            totalRows: 10,
            SysTable: [],
            datalist: []
          }
        },
        props:{
          value: [Array, Object],
          isSearch: {
            type: Boolean,
            default: false
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
            if (!this.SysTable.length) {
              this.fetchData();
            }
            if (!this.datalist.length) {
              this.addLine();
            }
            this.$refs[this.refModal].show();
          },
          fetchData(){
            let self = this;
            let requestData = {
              method: 'get',
              data: {}
            };
            requestData.url = '/extensions/api/chat/get-table';
            this.$store.commit('isLoading', true);

            ApiService.setHeader();
            ApiService.customRequest(requestData).then((responses) => {
              let responsesData = responses.data;
              self.SysTable = [];
              _.forEach(responsesData.data, function (value, key) {
                let tmpObj = {};
                tmpObj.value = value.TableName;
                tmpObj.text = value.TableDescription;
                self.SysTable.push(tmpObj);
              });

              self.SysTable.push({
                value: 'tag',
                text: 'Từ khóa'
              });

              self.$store.commit('isLoading', false);
            }, (error) => {
              console.log(error);
              self.$store.commit('isLoading', false);
            });
          },
          getCell(key){
            return `cell(${key})`; // simple string interpolation
          },
          onSaveDatalist(){
            this.$emit('on:save-category-key', this.datalist);
            this.$bvModal.hide(this.idModal);
          },
          changeSysTable(key){
            let result = this.SysTable.filter(obj => {
              if(obj.value === this.datalist[key].DatalistTable){
                return obj;
              }
            });

            this.datalist[key].LinkTableName = result[0].text;
            this.datalist[key].LinkID = '';
            this.datalist[key].LinkName = '';
            this.datalist[key].LinkNo = '';
          },
          addLine(){
            this.datalist.push({
              LinkID: '',
              LinkNo: '',
              LinkName: '',
              DatalistTable: '',
              LinkTableName: ''
            });
          },
          deleteLine(key){
            this.datalist.splice(key, 1);
          }
        },
        watch: {
          currentPage() {
            this.fetchData();
          },
          value: {
            handler(val){
              // do stuff
              this.datalist = (this.value && this.value.Datalist) ? this.value.Datalist : [];
            },
            deep: true
          }
        }
    }
</script>
<style lang="css"></style>

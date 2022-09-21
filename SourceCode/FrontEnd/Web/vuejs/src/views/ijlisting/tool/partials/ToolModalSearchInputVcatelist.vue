<template>
    <div class="ijcore ijcore-modal component-modal-search-vcatelist" @mouseenter="onMouseenter" @mouseleave="onmouseleave">
      <div style="position: relative">
        <b-form-input :class="classInput" :id="idInput" :rel="idInput" :readonly="true" :placeholder="placeholder" type="text" @click="init" :title="selectedItem.name" v-model="selectedItem.name" :name="nameInput"></b-form-input>
        <i v-if="showIconClose" @click="onClearValue" class="cui-circle-x icons icon-clear"></i>
        <i class="fa fa-external-link icon-popup" @click="init"></i>
      </div>
      <b-modal :id="idModal" :title="titleModal"
               :content-class="'sb-modal-content' + classModal"
               :ref="refModal"
               :no-fade="noFadeModal"
               :size="sizeModal" @hide="onHideModal($event)" @show="onShowModal">
        <template v-slot:modal-footer="{ ok, cancel, hide }">
<!--          <b-button class="mr-2 ml-0" variant="primary" @click="onSubmitSearch">-->
<!--            Tìm-->
<!--          </b-button>-->
          <b-button variant="primary" @click="hide()">
            Đóng
          </b-button>
        </template>
        <div class="ijcore-search-tool-vcatelist ijcore-modal-search">
          <table class="table b-table table-sm table-bordered table-editable">
            <thead>
            <tr class="text-center">
              <th class="pr-3">Loại CCDC</th>
              <th class="pr-3">Giá trị</th>
              <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, key) in value">
              <td>
                <Select2 v-model="value[key].CateID" :settings="{allowClear: true, placeholder: {id: null, text: 'Chọn loại công cụ dụng cụ'}}" :options="CateListOptions" @select="onSelectCateList($event, key)"></Select2>
              </td>
              <td>
                <Select2 v-model="value[key].CateValue" :settings="{allowClear: true, placeholder: {id: null, text: 'Chọn giá trị'}}" :options="CateValueOptions | filterCateValue(value[key].CateID)" @select="onSelectCateValue($event, key)"></Select2>
              </td>
              <td style="width: 50px; vertical-align: center">
                <div class="d-flex align-content-center justify-content-center">
                  <i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o" style="font-size: 18px; cursor: pointer;"></i>
                </div>
              </td>
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
    import Select2 from 'v-select2-component';

    export default {
      name: 'modal-search-vcatelist',
      components: {
        Select2,
      },
      data () {
        return {
          CateList: [],
          CateListOptions: [],
          CateValue: [],
          CateValueOptions: [],

          showIconClose: false,
          selectedItem: {
            id: '',
            name: ''
          },
        }
      },
      props:{
        value: [Array, Object],
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
        classInput: {
          type: String,
          default: ''
        },
        idInput: {
          type: String,
          default: ''
        },
        nameInput: {
          type: String,
          default: ''
        },
        placeholder:{
          type: String,
          default: ''
        }
      },
      computed: {
          rows() {
              return this.totalRows
          },
      },
      mounted() {
        // this.fetchData();
        this.renderInputName();
      },
      filters: {
        filterCateValue(value, CateID){
          if (CateID) {
            let CateValue = _.filter(value, ['CateID', Number(CateID)]);
            return CateValue;
          }
          return [];
        },
      },
      methods: {
        init(){
          if (!this.CateList.length) {
            this.fetchData();
          }
          if (!this.value.length) {
            this.addLine();
          }
          this.$bvModal.show(this.idModal);
        },
        fetchData(){
          let self = this;
          let requestData = {
            method: 'get',
            data: {}
          };
          requestData.url = '/listing/api/tool/get-cate-list' + '';
          this.$store.commit('isLoading', true);

          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            let responsesData = responses.data;
            if (responsesData.status === 1) {
              self.CateList = responsesData.data.ToolCateList;
              self.CateListOptions = [];
              _.forEach(self.CateList, function (CateList, key) {
                let tmpObj = {};
                tmpObj.id = CateList.CateID;
                tmpObj.text = CateList.CateName;
                self.CateListOptions.push(tmpObj);
              });

              self.CateValue = responsesData.data.ToolCateValue;
              self.CateValueOptions = [];
              _.forEach(self.CateValue, function (CateValue, key) {
                let tmpObj = {};
                tmpObj.id = CateValue.CateValue;
                tmpObj.text = CateValue.Description;
                tmpObj.CateID = CateValue.CateID;
                self.CateValueOptions.push(tmpObj);
              });
            }
            self.$store.commit('isLoading', false);
          }, (error) => {
            console.log(error);
            self.$store.commit('isLoading', false);
            Swal.fire({
              title: 'Thông báo',
              text: 'Không kết nối được với máy chủ',
              confirmButtonText: 'Đóng'
            });
          });
        },
        onClearValue(){
          this.selectedItem = {
            name: '',
            id: ''
          };
          this.$emit('input', []);
          this.$emit('onClear');
        },
        onSelectCateList(selected, key){
          this.value[key].CateName = selected.text;
        },
        onSelectCateValue(selected, key){
          this.value[key].Description = selected.text;
        },
        addLine(){
          let tmpObj = {};
          tmpObj.CateID = null;
          tmpObj.CateName = '';
          tmpObj.CateValue = null;
          tmpObj.Description = '';
          this.value.push(tmpObj);
        },
        deleteLine(key){
          this.value.splice(key, 1);
        },
        onMouseenter(){
          if (this.selectedItem.name) {
            this.showIconClose = true;
          }else {
            this.showIconClose = false;
          }
        },
        onmouseleave(){
          this.showIconClose = false;
        },
        renderInputName(){
          let self = this;
          this.selectedItem.name = '';
          _.forEach(this.value, function (item, key) {
            if (item.CateName) {
              self.selectedItem.name += item.CateName;
            }
            if (item.Description) {
              self.selectedItem.name += ': ' + item.Description + ', ';
            }
          });
          if (_.isString(this.selectedItem.name) && this.selectedItem.name.length) {
            this.selectedItem.name.trim();
            this.selectedItem.name.substring(0, this.selectedItem.name.length - 1);
          }
        },
        onSubmitSearch(){
          this.$emit('onSubmitSearch');
          this.$bvModal.hide(this.idModal);
        },
        onShowModal(){
          if (!this.CateList.length) {
            this.fetchData();
          }
        },
        onHideModal(event){}
      },
      watch: {
        'value': {
          handler(val){
            // do stuff
            let self = this;
            if (_.isEmpty(this.value)) {
              this.selectedItem = {};
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

  .component-modal-search-vcatelist .icon-clear {
    position: absolute;
    background-color: #ebebeb;
    top: 50%;
    right: 30px;
    transform: translateY(-50%);
  }
  .component-modal-search-vcatelist .icon-popup {
    position: absolute;
    background: #ebebeb;
    top: 50%;
    transform: translateY(-50%);
    right: 10px;
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
  .ijcore-search-tool-vcatelist .select2.select2-container {
    width: 100% !important;
  }
  .ijcore-modal-search .select2-container--default .select2-selection--single {
    border: none;
  }
  .component-modal-search-vcatelist input.form-control {
    background-color: #fff;
  }
</style>

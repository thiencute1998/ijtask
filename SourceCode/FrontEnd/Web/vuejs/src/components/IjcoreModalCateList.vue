<template>
    <div class="ijcore ijcore-modal-cate-list" @mouseenter="onMouseenter" @mouseleave="onmouseleave">
      <div style="position: relative" v-if="!isView">
        <b-form-input style="background: #fff" :readonly="true" :placeholder="placeholder" type="text" @click="init" :title="inputName" v-model="inputName"></b-form-input>
        <i v-if="showIconClose && !isView" @click="onClear" class="cui-circle-x icons icon-clear"></i>
      </div>
      <div v-else>
        <span :title="inputName">{{inputName}}</span>
      </div>
      <b-modal ref="modal-cate-list" id="modal-cate-list" size="lg"
               :title="title">
        <div class="main-body main-body-view-action">
          <table class="table b-table table-sm table-bordered table-editable">
            <thead>
            <tr>
              <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Loại chỉ tiêu</th>
              <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Giá trị</th>
              <th scope="col" style="width: 3%; border-bottom: none;" class="text-center"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(field, key) in value">
              <td>
                <ijcore-modal-listing
                  v-model="value[key]" :title="title" api="/listing/api/common/list"
                  :table="tableCateList" FieldID="CateID" FieldName="CateName"
                  :FieldNo="'CateNo'">
                </ijcore-modal-listing>
              </td>
              <td>
                <ijcore-modal-listing
                  @changed="changeCateValue($event)" v-model="value[key]" title="giá trị" api="/listing/api/common/list"
                  :table="tableCateValue" FieldID="LineID" FieldName="Description" FieldNo="CateValue"
                  :field-update="fieldUpdateCateValue"
                  :FieldWhere="{CateID : value[key].CateID}">
                </ijcore-modal-listing>
              </td>

              <td class="text-center">
                <i @click="onDeleteFieldCate(key)" class="fa fa-trash-o" title="Xóa"
                   style="font-size: 18px; cursor: pointer"></i>
              </td>
            </tr>
            </tbody>
          </table>
          <a @click="onAddFieldCate" class="new-row">
            <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
          </a>
        </div>
        <template v-slot:modal-footer>
          <div class="w-100 left">
            <b-button variant="primary" size="md" class="float-left mr-2" @click="saveModal">
              <span v-if="!isFilter">Lưu</span>
              <span v-else>Tìm</span>
            </b-button>
            <b-button variant="primary" size="md" class="float-left mr-2" @click="hideModal">
              Đóng
            </b-button>
          </div>
        </template>
      </b-modal>
    </div>
</template>
<style>
.ijcore-modal-cate-list .input-group-append {
  position: absolute;
  right: 0;
  z-index: 9;
}
.ijcore-modal-cate-list .icon-clear {
  position: absolute;
  background-color: #ebebeb;
  top: 50%;
  right: 10px;
  transform: translateY(-50%);
}
</style>
<script>
  import IjcoreModalListing from "./IjcoreModalListing";
  export default {
    name: 'ijcore-modal-cate-list',
    components: {
      IjcoreModalListing
    },
    data () {
      return {
        inputName: '',
        showIconClose: false
      }
    },
    mounted() {
      this.renderInputName();
    },
    methods: {
      init(){
        if (!this.isView) {
          this.showModal();
        }
      },
      changeCateValue(data) {},
      saveModal(){
        if (this.isFilter) {
          this.$emit('saved');
        }
        this.hideModal();
      },
      showModal() {
        this.$bvModal.show('modal-cate-list');
      },
      hideModal(){
        this.$bvModal.hide('modal-cate-list');
      },
      onDeleteFieldCate(key) {
        this.value.splice(key, 1);
      },

      onAddFieldCate() {
        let fieldObj = {};
        fieldObj.CateID = null;
        fieldObj.CateName = '';
        fieldObj.CateValue = null;
        fieldObj.Description = '';
        this.value.push(fieldObj);
      },
      onClear(){
        this.inputName = '';
        this.renderInputName();
        this.$emit('input', []);
        this.$emit('onClear');
      },
      onMouseenter(){
        if (this.inputName) {
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
        this.inputName = '';
        _.forEach(this.value, function (item, key) {
          if (item.CateName) {
            self.inputName += item.CateName;
          }
          if (item.Description) {
            self.inputName += ': ' + item.Description + ', ';
          }
        });
        if (_.isString(this.inputName) && this.inputName.length) {
          this.inputName.trim();
          this.inputName.substring(0, this.inputName.length - 1);
        }
      },
    },
    props:{
      value: [Array, Object],
      isView: false,
      isFilter: false,
      title: [String],
      tableCateList: [String],
      tableCateValue: [String],
      objectID: [String],
      placeholder: [String],
      fieldUpdateCateList: [Array, Object],
      fieldUpdateCateValue: [Array, Object]
    },
    watch: {
      'value': {
        handler(val){
          // do stuff
          if (_.isEmpty(this.value)) {
            this.inputName = '';
          } else {
            this.renderInputName();
          }
        },
        deep: true
      }
    }
  }
</script>

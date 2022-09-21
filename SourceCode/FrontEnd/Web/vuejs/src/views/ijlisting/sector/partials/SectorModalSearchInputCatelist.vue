<template>
  <div class="ijcore ijcore-modal component-modal-search-ccatelist" @mouseenter="onMouseenter" @mouseleave="onMouseleave">
    <div style="position: relative;">
      <b-form-input v-model="selectedItem.name" @click="init" :readonly="true" :placeholder="placeholder">
      </b-form-input>
      <i v-if="showIconClose" @click="onClearValue" class="cui-circle-x icons icon-clear"></i>
      <i class="fa fa-external-link icon-popup" @click="init"></i>
    </div>
    <b-modal :id="idModal" :title="titleModal"
             :content-class="'sb-modal-content'"
             :size="sizeModal"
             @show="onShowModal"  @hide="onHideModal($event)" @shown="onShownModal">
      <template v-slot:modal-footer="{ ok, cancel, hide}">
        <b-button variant="primary" @click="hide()">
          Đóng
        </b-button>
      </template>
      <div class="ijcore-modal-sector-ccatelist ijcore-modal-search">
        <table class="table b-table table-sm table-bordered table-editable">
          <thead>
          <tr>
            <th class="pr-3">Loại khoản thu</th>
            <th class="pr-3">Giá trị</th>
            <th></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(item, key) in value" :key="key">
            <td>
              <Select2 v-model="value[key].CateID" :settings="{allowClear: true, placeholder: {id: null, text: 'Chọn loại khoản thu'}}" :options="CateListOptions" @select="onSelectCateList($event, key)"></Select2>
            </td>
            <td>
              <Select2 v-model="value[key].CateValue" :settings="{allowClear: true, placeholder: {id: null, text:'Chọn giá trị'}}" :options="CateValueOptions | filterCateValue(value[key].CateID)" @select="onSelectCateValue($event, key)"></Select2>
            </td>
            <td >
              <div class="d-flex align-content-center justify-content-center">
                <i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o" style="font-size: 18px; cursor: pointer;"></i>
              </div>
            </td>
          </tr>
          </tbody>
        </table>
        <a @click="addLine" class="new-row"><i class="fa fa-plus-square-o ij-icon"></i> Thêm mới</a>
      </div>
    </b-modal>
  </div>

</template>
<script>
import ApiService from "@/services/api.service";
import Select2 from "v-select2-component";

export default {
  name: 'sector-modal-search-input-catelist',
  components: {
    Select2
  },
  props: {
    value: [Array, Object],
    idModal: {
      type: String,
      default: 'appModal'
    },
    titleModal: {
      type: String,
      default: ''
    },
    sizeModal: {
      type: String,
      default: 'lg'
    },
    placeholder: {
      type: String,
      default: ''
    },
    listApi:{
      type: String,
      default: ''
    }
  },
  data() {
    return {
      CateList: [],
      CateListOptions: [],
      CateValue: [],
      CateValueOptions: [],
      showIconClose: false,
      selectedItem: {
        id: '',
        name: '',
      },
    }
  },
  computed: {

  },
  filters: {
    filterCateValue(value, CateID){
      if(CateID){
        // let listCateValues = listItems.map(cateValue=> cateValue.CateValue);
        // console.log(listCateValues);
        let CateValue = _.filter(value, item => item['CateID'] === Number(CateID));
        return CateValue;
      }
      return [];
    }
  },
  mounted() {
    let self = this;
    this.renderInputName();
    this.$root.$on('bv::show::modal', (bvEvent, modalId) => {
    });
  },
  methods: {
    init(){
      if(!this.CateList.length){
        this.fetchData();
      }
      if(!this.value.length){
        this.addLine();
      }
      this.$bvModal.show(this.idModal);
    },
    onClearValue(){
      this.selectedItem = {
        id: '',
        name: ''
      };
      this.$emit('input', []);

    },
    addLine(){
      let tmpObject= {};
      tmpObject.CateID = null;
      tmpObject.CateName = '';
      tmpObject.CateValue = null;
      tmpObject.Description = '';
      this.value.push(tmpObject);
    },
    onShowModal(){
      // if(!this.CateList.length){
      //  this.fetchData();
      // }
    },
    onHideModal(event){},
    onShownModal(){
      $('.modal-content').removeAttr('tabindex');
    },
    onMouseenter(){
      if(this.selectedItem.name){
        this.showIconClose = true;
      }
    },
    onMouseleave(){
      this.showIconClose = false;
    },
    fetchData(){
      let self = this;
      let requestData = {
        method: 'get',
        data: {}
      };
      requestData.url= self.listApi;
      this.$store.commit('isLoading',true);
      ApiService.setHeader();
      ApiService.customRequest(requestData)
        .then(response =>{
          this.$store.commit('isLoading',false);
          let responsesData = response.data;
          self.CateList = responsesData.data.SectorCateList;
          _.forEach(self.CateList,(value,key)=> {
            let tmpObject = {};
            tmpObject.id = value.CateID;
            tmpObject.no = value.CateNo;
            tmpObject.text = value.CateNo + "-" + value.CateName;
            tmpObject.CateName = value.CateName;
            self.CateListOptions.push(tmpObject);
          })

          self.CateValue = responsesData.data.SectorCateValue;
          _.forEach(self.CateValue, (value,key)=> {
            let tmpObject = {};
            tmpObject.id = value.CateValue;
            tmpObject.text = value.CateValue + "-"+ value.Description;
            tmpObject.CateID = value.CateID;
            tmpObject.Description = value.Description;
            self.CateValueOptions.push(tmpObject);
          })
        }).catch(error =>{
        self.$store.commit('isLoading',false);
        Swal.fire({
          title: 'Thông báo',
          text: 'Không kết nối được đến máy chủ',
          confirmButtonText: 'Đóng'
        });
      })
    },
    onSelectCateList(selected, key){
      this.value[key].CateName = selected.CateName;
      this.value[key].CateNo = selected.no;
    },
    onSelectCateValue(selected, key){
      this.value[key].Description = selected.Description;
    },
    deleteLine(key){
      this.value.splice(key,1);
    },
    renderInputName(){
      let self = this;
      self.selectedItem.name = '';
      _.forEach(self.value,function(item,key){
        if(item.CateName){
          self.selectedItem.name += item.CateName;
        }
        if(item.Description){
          self.selectedItem.name += ': '+ item.Description + ', ';
        }
      });
      if(_.isString(this.selectedItem.name) && this.selectedItem.name.length){
        this.selectedItem.name.trim();
        this.selectedItem.name.substring(0,this.selectedItem.name.length - 1);
      }
    },
  },
  watch: {
    value: {
      deep: true,
      handler(val){
        if(_.isEmpty(this.value)){
          this.selectedItem = {};
        }
        else{
          this.renderInputName();
        }
      }
    }
  }
}
</script>
<style lang="css">
.component-modal-search-ccatelist .icon-clear {
  position: absolute;
  background-color: #ebebeb;
  top: 50%;
  right: 30px;
  transform: translateY(-50%);
}

.icon-popup{
  position: absolute;
  top: 50%;
  right: 0;
  transform: translate(-50%,-50%);
  -webkit-transform: translate(-50%,-50%);
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

.ijcore-modal-sector-ccatelist .select2.select2-container{
  width: 100% !important;
}
.ijcore-modal-search .select2-container--default .select2-selection--single {
  border: none;
}

.component-modal-search-ccatelist input.form-control {
  background-color: #fff;
}
</style>

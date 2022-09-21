<template>
  <div class="ijcore-modal-parent">
    <b-input-group @mouseenter="onMouseenter">
      <b-form-input :placeholder="placeholderInput" :readonly="true" v-model="value.ParentName" class="input-parent-parent readonly form-control"></b-form-input>
      <input type="hidden" :readonly="true" v-model="value.ParentID" class="readonly form-control"/>
      <div class="input-group-append">
        <b-button v-if="showIconClose" variant="light" @click="onClearValue" class="ijcore-element-clear"><i class="cui-circle-x icons"></i></b-button>
        <b-button variant="light" @click="onToggleModal" class="ijcore-element-search"><i class="fa fa-search"></i></b-button>
      </div>
    </b-input-group>
    <b-modal ref="modal" id="modal">
      <template slot="modal-title">
        {{this.title}}
      </template>
      <b-input-group>
        <b-form-input v-model="search" :placeholder="placeholderSearch" class="readonly form-control" @change="fetchData"></b-form-input>
        <div class="input-group-prepend"  @click="fetchData">
                                <span class="input-group-text">
                                  <i class='fa fa-search'></i>
                                </span>
        </div>
      </b-input-group>
      <div class="table-responsive">
        <table class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
          <tbody>
          <tr v-for="item in listtable" @click="updateForParent(item[FieldIDData], item[FieldNameData], item[FieldNoData])">
            <td class="pr-3 tr-no" style="min-width: 80px;">{{item[FieldNoData]}}</td>
            <td class="pr-3" style="min-width: 280px;"><span :title="item[FieldNameData]">{{item[FieldNameData]}}</span></td>
          </tr>
          </tbody>
        </table>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button
            variant="primary"
            size="md"
            class="float-left"
            @click="onHideModal()"
          >
            Đóng
          </b-button>
          <div class="d-flex flex-wrap justify-content-between align-items-center m-0 float-right">
            <div class="main-footer-pagination">
              <div class="overflow-auto">
                <b-pagination
                  v-model="currentPage"
                  :total-rows="rows"
                  :per-page="perPage"
                  aria-controls="my-table"
                  size="md"
                ></b-pagination>
              </div>
            </div>
          </div>
        </div>
      </template>

    </b-modal>
  </div>
</template>

<script>
import ApiService from '@/services/api.service';
import mixinLists from '@/mixins/lists';

export default {
  name: 'IjcoreModalParent',
  mixins: [mixinLists],
  components: {
  },
  computed: {
    rows() {
      return this.totalRows
    },
  },
  data () {
    return {
      showIconClose: false,
      listtable: [
      ],
      tableName: '',
      FieldNameData: '',
      FieldNoData: '',
      FieldIDData: '',
      search:'',
      lenghNo: 0,
    }
  },
  created() {
    if(this.specialTable){
      this.tableName = this.specialTable;
    }
    else{
      this.tableName = this.table.charAt(0).toUpperCase() + this.table.slice(1);
    }
    this.currentPage = 1;
  },
  mounted() {
    let self = this;
    if (this.fieldID) {
      this.FieldIDData = this.fieldID;
    } else {
      this.FieldIDData = this.tableName + 'ID';
    }
    if (this.fieldNo) {
      this.FieldNoData = this.fieldNo;
    } else {
      this.FieldNoData = this.tableName + 'No';
    }
    if (this.fieldName) {
      this.FieldNameData = this.fieldName;
    } else {
      this.FieldNameData = this.tableName + 'Name';
    }
    this.$el.querySelector('.input-parent-parent').addEventListener('click', function () {
      self.onToggleModal();
    });
  },
  methods: {
    fetchData(){
      let self = this;
      let urlApi = this.api;
      let requestData = {
        method: 'post',
        url: urlApi,
        data: {
          per_page: 10,
          page: this.currentPage,
          table: (this.specialTable) ? this.table : this.tableName,
          search: this.search,
          FieldName: this.fieldName,
          FieldNo: this.fieldNo,
          FieldID: this.fieldID,
        },
      };

      if(this.currentID && this.currentID !== null){
        requestData.data.CurrentID = this.currentID;
      }

      this.$store.commit('isLoading', true);
      ApiService.customRequest(requestData).then((response) => {
        let dataResponse = response.data;

        if (dataResponse.status === 1) {
          self.totalRows = dataResponse.data.total;
          self.perPage = String(dataResponse.data.per_page);
          self.currentPage = dataResponse.data.current_page;
          // converse object to array
          self.listtable = _.toArray(dataResponse.data.data);
          // set params request
          self.paramsReqRouter.lastPage = dataResponse.data.last_page;
          self.paramsReqRouter.from = dataResponse.data.from;
          self.paramsReqRouter.to = dataResponse.data.to;
          self.$_lists_setParamsReqRouter();
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });

      // scroll to top perfect scroll
      const container = document.querySelector('.b-table-sticky-header');
      if (container) container.scrollTop = 0;

    },
    updateForParent: function (ParentID, ParentName, ParentNo) {
      this.value.ParentID = ParentID;
      this.value.ParentName = ParentName;
      this.value.ParentNo = ParentNo;
      this.$refs['modal'].hide();
    },
    onMouseenter(){
      if(this.value.ParentID){
        this.showIconClose = true;
      }
      else{
        this.showIconClose = false;
      }
    },
    onClearValue(){
      this.value.ParentID = null;
      this.value.ParentName = '';
    },
    onSaveModal(){
      this.$bvToast.toast('Đã lưu ràng buộc', {
        variant: 'success',
        solid: true
      });
    },
    onCancelModal(e){
      this.onResetModal();
      e.preventDefault();
    },
    onHideModal(){
      this.$refs['modal'].hide();
    },
    onToggleModal(){
      let self = this;
      this.currentPage = 1;
      this.$refs['modal'].show();
      this.fetchData();
    },
    onResetModal(){
    },
  },
  watch: {
    currentPage() {
      this.fetchData();
    },
  },
  props: {
    value: {
      type: Object,
      default() {
        return {
          ParentID: '',
        };
      }
    },
    title:{},
    name:{},
    api: {},
    table: {},
    disabled: {},
    fieldName: {},
    placeholderInput: {},
    placeholderSearch: {},
    specialTable: {},
    currentID: {},
    fieldNo: {},
    fieldID : {}
  },
}
</script>
<style>
.ijcore-modal-parent .input-group-append {
  position: absolute;
  right: 0;
  z-index: 9;
}
.ijcore-modal-parent .ijcore-element-clear {
  display: none !important;
}
.ijcore-modal-parent:hover .ijcore-element-clear{
  display: inline-block !important;
}
.ijcore-modal-parent input {
  padding-right: 56px;
  background: #fff !important;
  border-bottom-right-radius: 0.25rem !important;
  border-top-right-radius: 0.25rem !important;
}
.ijcore-modal-parent button{
  background: transparent;
  border: none;
  padding: 0.275rem 0.5rem;
}
.ijcore-modal-parent button:hover{
  background: transparent !important;
}
.ijcore-modal-parent .input-group {
  align-items: center;
}
.readonly{
  background-color: #fff !important;
}
.table th, .table td{
  border-bottom: 1px solid #c8ced3;
}
.modal-footer ol,.modal-footer ul,.modal-footer dl{
  margin-bottom: 0px;
}
</style>

<template>
  <div class="ijcore-modal-search-listing" @mouseenter="onMouseenter">
    <b-input-group>
      <b-form-input :placeholder="'Chọn ' + title" :title="value[this.fieldAssignName]" :readonly="true" v-model="value[this.fieldAssignName]" v-if="!showNo" class="input-ijcore-listing readonly form-control"></b-form-input>
      <b-form-input :placeholder="'Chọn ' + title" :title="value[this.fieldAssignName]" :readonly="true" v-model="value[this.fieldAssignNo]" v-else :disabled="disabled" class="input-ijcore-listing readonly form-control"></b-form-input>
      <input type="hidden" :readonly="true" v-model="value[this.fieldAssignID]" class="readonly form-control"/>
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
        <b-form-input v-model="search" :placeholder="'Nhập ' + title" class="readonly form-control" @change="fetchData"></b-form-input>
        <div class="input-group-prepend"  @click="fetchData">
                                <span class="input-group-text">
                                  <i class='fa fa-search'></i>
                                </span>
        </div>
      </b-input-group>
      <div class="table-responsive">
        <table class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
          <tbody>
          <tr v-for="item in listtable" @click="updateForParent(item[fieldID], item[fieldNo], item[fieldName])">

            <td class="pr-3 tr-no" style="min-width: 80px;" v-if="!hideFiedNoOrID">
                <span v-if="(item[fieldNo] && item[fieldNo] !== '')">{{item[fieldNo]}}</span>
                  <span v-else>{{item[fieldID]}}</span>
            </td>
            <!--            <td class="pr-3"><span :title="item[table + 'Name']">{{item[table + 'Name']}}</span></td>-->
            <td class="pr-3" style="min-width: 280px;"><span :title="item[fieldName]">{{item[fieldName]}}</span></td>
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
  name: 'IjcoreModalSearchListing',
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
      search:'',
      lenghNo: 0,
    }
  },
  created() {
    this.currentPage = 1;
  },
  mounted() {
    let self = this;
    this.$el.querySelector('.input-ijcore-listing').addEventListener('click', function () {
      self.onToggleModal();
    });
  },
  methods: {
    fetchData(){
      let tableName = this.table.charAt(0).toUpperCase() + this.table.slice(1);
      let self = this;
      let urlApi = this.api+'?XDEBUG_SESSION_START=PHPSTORM';
      let requestData = {
        method: 'post',
        url: urlApi,
        data: {
          per_page: 10,
          page: this.currentPage,
          table: tableName,
          search: this.search,
          FieldName: this.fieldName,
          FieldID: this.fieldID,
          FieldWhere: this.FieldWhere,
        },
      };
      if(this.fieldNo && this.fieldNo !== ''){
        requestData.data.FieldNo = this.fieldNo;
      }

      if(this.currentID && this.currentID !== null){
        requestData.data.currentID = this.currentID;
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
          // if (self.listtable[0]) {
          //   self.lenghNo = "width: " + (self.listtable[0][self.table + 'No']).length * 10 + "px !important;";
          // }
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
    updateForParent: function (itemID, itemNo, itemName) {
      this.value[this.fieldAssignID] = itemID;
      this.value[this.fieldAssignNo] = itemNo;
      this.value[this.fieldAssignName] = itemName;
      // select AutOrg for company
      this.$emit('selectOrg', itemID);
      this.$refs['modal'].hide();
    },
    onMouseenter(){
      if(this.value[this.fieldAssignID]){
        this.showIconClose = true;
      }
      else{
        this.showIconClose = false;
      }
    },
    onClearValue(){
      this.$emit('clearOrg');
      this.value[this.fieldAssignID] = null;
      this.value[this.fieldAssignNo] = '';
      this.value[this.fieldAssignName] = '';
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
    value: [Object, Array],
    title:{},
    name:{},
    api: {},
    table: {},
    disabled: {},
    fieldName: {},
    fieldID: {},
    fieldNo: {},
    fieldAssignID: {},
    fieldAssignNo: {},
    fieldAssignName: {},
    currentID: {},
    hideFiedNoOrID:false,
    showNo: false,
    FieldWhere: {},
  },
}
</script>
<style>
.ijcore-modal-search-listing .input-group-append {
  position: absolute;
  right: 0;
  z-index: 9;
}
.ijcore-modal-search-listing .ijcore-element-clear {
  display: none !important;
}
.ijcore-modal-search-listing:hover .ijcore-element-clear{
  display: inline-block !important;
}
.ijcore-modal-search-listing input {
  padding-right: 56px;
  background: #fff !important;
  border-bottom-right-radius: 0.25rem !important;
  border-top-right-radius: 0.25rem !important;
}
.ijcore-modal-search-listing button{
  background: transparent;
  border: none;
  padding: 0.275rem 0.5rem;
}
.ijcore-modal-search-listing button:hover{
  background: transparent !important;
}
.ijcore-modal-search-listing .input-group {
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

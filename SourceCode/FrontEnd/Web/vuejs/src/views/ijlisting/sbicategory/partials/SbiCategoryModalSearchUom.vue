<template>
  <div class="ijcore-modal-search-input" @mouseenter="onMouseenter">
    <b-input-group>
      <b-form-input placeholder="Chọn đơn vị tính" :readonly="true" v-model="value.UomName" class="input-sbi-category-uom readonly form-control"></b-form-input>
      <input type="hidden" :readonly="true" v-model="value.UomID" class="readonly form-control"/>
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
        <b-form-input v-model="search" placeholder="Nhập tên đơn vị tính" class="readonly form-control" @change="fetchData"></b-form-input>
        <div class="input-group-prepend"  @click="fetchData">
                                <span class="input-group-text">
                                  <i class='fa fa-search'></i>
                                </span>
        </div>
      </b-input-group>
      <div class="table-responsive">
        <table class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
          <tbody>
          <tr v-for="item in listtable" @click="updateForParent(item[tableName+'ID'], item[tableName+'Name'])">
            <td class="pr-3 tr-no" style="width: 80px;">{{item[tableName + 'ID']}}</td>
            <!--            <td class="pr-3"><span :title="item[tableName + 'Name']">{{item[tableName + 'Name']}}</span></td>-->
            <td class="pr-3"><span :title="item.UomName">{{item.UomName}}</span></td>
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
  name: 'SbiCategoryModalSearchUom',
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
      search:'',
      lenghNo: 0,
    }
  },
  created() {
    this.tableName = this.table.charAt(0).toUpperCase() + this.table.slice(1);
    this.currentPage = 1;
  },
  mounted() {
    let self = this;
    this.$el.querySelector('.input-sbi-category-uom').addEventListener('click', function () {
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
          table: this.tableName,
          search: this.search,
          FieldName: this.fieldName,
          FieldNo: this.fieldNo,
          FieldID: this.fieldID,
        },

      };

      this.$store.commit('isLoading', true);
      ApiService.customRequest(requestData).then((response) => {
        let dataResponse = response.data;

        if (dataResponse.status === 1) {
          self.totalRows = dataResponse.data.total;
          self.perPage = String(dataResponse.data.per_page);
          self.currentPage = dataResponse.data.current_page;
          // converse object to array
          self.listtable = _.toArray(dataResponse.data.data);
          if (self.listtable[0]) {
            self.lenghNo = "width: " + (self.listtable[0][self.tableName + 'No']).length * 10 + "px !important;";
          }
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
    updateForParent: function (UomID, UomName) {
      this.value.UomID = UomID;
      this.value.UomName = UomName;
      this.$refs['modal'].hide();
    },
    onMouseenter(){
      if(this.value.UomID){
        this.showIconClose = true;
      }
      else{
        this.showIconClose = false;
      }
    },
    onClearValue(){
      this.value.UomID = null;
      this.value.UomName = '';
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
          UomID: '',
        };
      }
    },
    title:{},
    name:{},
    api: {},
    table: {},
    disabled: {},
    fieldName: {},
    fieldNo: {},
    fieldID : {}
  },
}
</script>
<style>
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

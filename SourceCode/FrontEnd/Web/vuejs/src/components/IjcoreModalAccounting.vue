<template>
  <div class="ijcore-modal-parent">
    <b-input-group>
      <b-form-input :placeholder="'Nhập ' + this.title" :title="value[FieldNameData]" :readonly="false" v-model="value[FieldNoData]"
                    class="readonly form-control" @click="onToggleModal()"></b-form-input>
      <input type="hidden" :readonly="true" v-model="value[FieldIDData]" class="readonly form-control"/>
      <div class="input-group-prepend" @click="onToggleModal()">
<!--        <span class="input-group-text"><i class='fa fa-search'></i></span>-->
        <b-button variant="light" @click="onToggleModal" class="ijcore-element-search"><i class="fa fa-search"></i></b-button>
      </div>
    </b-input-group>
    <b-modal ref="modal" id="modal">
      <template slot="modal-title">
        {{this.TitleModal}}
      </template>
      <b-input-group class="pt-10">
        <b-form-input v-model="search" :placeholder="'Nhập tên hoặc mã số'" class="readonly form-control" @change="fetchData"></b-form-input>
        <div class="input-group-prepend" @click="fetchData">
          <span class="input-group-text"><i class='fa fa-search'></i></span>
        </div>
      </b-input-group>
      <div class="table-responsive pb-10">
        <table class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
          <tbody>
          <tr v-for="(item, key) in listtable" @click="updateForParent(item[FieldIDData], item[FieldNameData], item[FieldNoData], item[FieldDetailData], key)">
            <td class="pr-3 tr-no" style="min-width: 80px;">{{item[FieldNoData]}}</td>
            <td class="pr-3" style="min-width: 340px;">
              {{item[FieldNameData]}}
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left" @click="onHideModal()">Đóng</b-button>
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
    name: 'IjcoreModalListing',
    mixins: [mixinLists],
    components: {},
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {
        listtable: [],
        tableName: '',
        search: '',
        TitleModal: '',
        FieldNameData: '',
        FieldNoData: '',
        FieldIDData: '',
        FieldDetailData: '',
      }
    },
    created() {

    },
    mounted() {
      this.tableName = this.table.charAt(0).toUpperCase() + this.table.slice(1);
      this.TitleModal = this.title.charAt(0).toUpperCase() + this.title.slice(1);
      this.currentPage = 1;
      if (this.FieldID) {
        this.FieldIDData = this.FieldID;
      } else {
        this.FieldIDData = this.tableName + 'ID';
      }
      if (this.FieldNo) {
        this.FieldNoData = this.FieldNo;
      } else {
        this.FieldNoData = this.tableName + 'No';
      }
      if (this.FieldName) {
        this.FieldNameData = this.FieldName;
      } else {
        this.FieldNameData = this.tableName + 'Name';
      }
      if (this.FieldDetail) {
        this.FieldDetailData = this.FieldDetail;
      }
    },
    methods: {
      fetchData() {
        let self = this;
        let urlApi = this.api;
        if(this.FieldType==2){
          this.FieldIDData = this.columnID;
            this.FieldNameData = this.columnName;
              this.FieldNoData = this.columnNo;
        }else{
          this.FieldIDData = this.FieldID;
          this.FieldNoData = this.FieldNo;
          this.FieldNameData = this.FieldName;
          this.FieldDetailData = this.FieldDetail;
        }
        let requestData = {
          method: 'post',
          url: urlApi,
          data: {
            per_page: 10,
            page: this.currentPage,
            table: this.table.charAt(0).toUpperCase() + this.table.slice(1),
            search: this.search,
            FieldID: this.FieldIDData,
            FieldName: this.FieldNameData,
            FieldNo: this.FieldNoData,
            FieldDetail: this.FieldDetailData,
            FieldWhere: this.FieldWhere,
            FieldSelect: this.FieldSelect
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
        //const container = document.querySelector('.b-table-sticky-header');
        //if (container) container.scrollTop = 0;

      },
      updateForParent: function (ParentID, ParentName, ParentNo, Detail, Key) {
        let self = this;
        if(Detail==0){
          this.$bvToast.toast('Chọn lại mã chi tiết', {
            variant: 'success',
            solid: true
          });
        }else{
        if(this.FieldType==2){
          this.FieldIDData = this.FieldID;
          this.FieldNameData = this.FieldName;
          this.FieldNoData = this.FieldNo;
        }else{
          this.FieldIDData = this.FieldID;
          this.FieldNoData = this.FieldNo;
          this.FieldNameData = this.FieldName;
        }
        self.value[this.FieldIDData] = ParentID;
        self.value[this.FieldNameData] = ParentName;
        self.value[this.FieldNoData] = ParentNo;

        if(self.FieldUpdate){
          _.forEach(this.FieldUpdate, function (field, k) {
            self.value[field] = self.listtable[Key][field];
          });
        }
        this.$emit('changed', true);
        this.$refs['modal'].hide();
        this.$forceUpdate();
        }
      },
      onSaveModal() {
        this.$bvToast.toast('Đã lưu ràng buộc', {
          variant: 'success',
          solid: true
        });
      },
      onCancelModal(e) {
        this.onResetModal();
        e.preventDefault();
      },
      onHideModal() {
        this.$refs['modal'].hide();
      },
      onToggleModal() {
        this.tableName = this.table.charAt(0).toUpperCase() + this.table.slice(1);
        this.TitleModal = this.title.charAt(0).toUpperCase() + this.title.slice(1);
        this.currentPage = 1;
        let self = this;
        this.currentPage = 1;
        this.$refs['modal'].show();
        this.fetchData();
      },
      onResetModal() {
      },
    },
    watch: {
      currentPage() {
        this.fetchData();
      }
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
      title: {},
      name: {},
      api: {},
      table: {},
      FieldNo: {},
      FieldName: {},
      FieldID: {},
      FieldUpdate:{},
      FieldWhere:{},
      FieldSelect: {},
      FieldType: {},
      columnID: {},
      columnNo: {},
      columnName: {},
      FieldDetail: {},
    },
  }
</script>
<style>
  .readonly {
    background-color: #fff !important;
  }
  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }
  .modal-footer ol, .modal-footer ul, .modal-footer dl {
    margin-bottom: 0;
  }
  .ijcore-modal-parent button{
    background: transparent;
    border: none;
    padding: 0.275rem 0.5rem;
  }
  .ijcore-modal-parent button:hover{
    background: transparent !important;
  }
</style>

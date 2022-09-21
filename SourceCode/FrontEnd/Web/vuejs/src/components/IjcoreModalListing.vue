<template>
  <div class="ijcore ijcore-modal ijcore-modal-search-input component-modal-search-input" @mouseleave="onMouseLeave" @mouseenter="onMouseenter">
    <b-input-group>
      <b-form-input v-if="FieldType === 1" :placeholder="'Chọn ' + title" :title="value[FieldNameData]" :readonly="true" v-model="value[FieldNameData]"
                    class="readonly form-control" @click="onToggleModal()"></b-form-input>
      <b-form-input v-else :placeholder="placeholderInput" :title="(value[FieldNoData]) ? value[FieldNameData] : title" :readonly="true" v-model="value[FieldNoData]"
                    class="readonly form-control" @click="onToggleModal()"></b-form-input>
      <input type="hidden" :readonly="true" v-model="value[FieldIDData]" class="readonly form-control"/>
      <b-input-group-append>
        <b-button v-if="showIconClose" variant="light" @click="onClearValue" class="ijcore-element-clear"><i class="cui-circle-x icons"></i></b-button>
        <b-button variant="light" @click="onToggleModal" class="ijcore-element-search"><i class="fa fa-search"></i></b-button>
      </b-input-group-append>

<!--      <div class="input-group-prepend" @click="onToggleModal()">-->
<!--                <span class="input-group-text">-->
<!--                  <i class='fa fa-search'></i>-->
<!--                </span>-->
<!--      </div>-->
    </b-input-group>
    <b-modal ref="modal" id="modal">
      <template slot="modal-title">
        {{this.TitleModal}}
      </template>
      <b-input-group class="pt-10">
        <b-form-input v-model="search" :placeholder="placeholder" class="readonly form-control"
                      @change="fetchData"></b-form-input>
        <div class="input-group-prepend" @click="fetchData">
                                <span class="input-group-text">
                                  <i class='fa fa-search'></i>
                                </span>
        </div>
      </b-input-group>
      <div class="table-responsive pb-10">
        <table class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
          <tbody>
          <tr v-for="(item, key) in listtable" @click="updateForParent(item[FieldIDData], item[FieldNameData], item[FieldNoData], key)">
            <td v-if="FieldNoConfig && FieldNoConfig.show" :style="FieldNoConfig.tdStyle">{{item[FieldNoData]}}</td>
            <td class="pr-3" :title="item[FieldNameData]">
              {{item[FieldNameData]}}
            </td>
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

  export default {
    name: 'IjcoreModalListing',
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
        tabletransName: '',
        search: '',
        TitleModal: '',
        FieldNameData: '',
        FieldNoData: '',
        FieldIDData: '',
        showIconClose: false,
        placeholder: '',
        perPage: (this.$store.state.optionBehavior.perPage) ? this.$store.state.optionBehavior.perPage : null,
        currentPage: 1,
        itemsArray: [],
        totalRows: null,
      }
    },
    created() {
    },
    mounted() {
      this.tableName = this.table.charAt(0).toUpperCase() + this.table.slice(1);
      this.tabletransName = this.tabletrans;
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

      if (this.FieldNoConfig) {
        this.placeholder = 'Chọn tên hoặc mã số ' + this.title;
      } else {
        this.placeholder = 'Chọn tên ' + this.title;
      }
    },
    methods: {
      fetchData() {
        let self = this;
        let urlApi = this.api;
        let requestData = {
          method: 'post',
          url: urlApi,
          data: {
            per_page: 10,
            page: this.currentPage,
            table: this.tableName,
            tabletrans: this.tabletransName,
            search: this.search,
            FieldID: this.FieldIDData,
            FieldName: this.FieldNameData,
            FieldNo: this.FieldNoData,
            FieldWhere: this.FieldWhere,
            FieldSelect: this.FieldSelect
          },

        };

        // if (this.modelSearch.CompanyNo.trim() !== '') {
        //     requestData.data.CompanyNo = this.modelSearch.CompanyNo;
        // }

        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let dataResponse = response.data;
          if (dataResponse.status == 1) {
            self.totalRows = dataResponse.data.total;
            self.perPage = String(dataResponse.data.per_page);
            self.currentPage = dataResponse.data.current_page;
            // converse object to array
            self.listtable = _.toArray(dataResponse.data.data);
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });


      },
      updateForParent: function (ParentID, ParentName, ParentNo, Key) {
        let self = this;
        self.value[this.FieldIDData] = ParentID;
        self.value[this.FieldNameData] = ParentName;
        self.value[this.FieldNoData] = ParentNo;
        if(self.FieldUpdate){
          _.forEach(this.FieldUpdate, function (field, k) {
            self.value[field] = self.listtable[Key][field];
          });
        }
        if (self.FieldUpdateSpecial) {
          _.forEach(self.FieldUpdateSpecial, function (field, keyUpdate) {
            self.value[keyUpdate] = self.listtable[Key][field];
          });
        }
        this.$emit('changed', self.listtable[Key]);
        this.$refs['modal'].hide();
        this.$forceUpdate();
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
        this.tabletransName = this.tabletrans;
        this.TitleModal = this.title.charAt(0).toUpperCase() + this.title.slice(1);
        this.currentPage = 1;
        let self = this;
        this.currentPage = 1;
        this.$refs['modal'].show();
        this.fetchData();
      },
      onResetModal() {
      },
      onClearValue(){
        let self = this;
        this.value[this.FieldIDData] = null;
        this.value[this.FieldNameData] = '';
        this.value[this.FieldNoData] = '';
        if(this.FieldUpdate){
          _.forEach(this.FieldUpdate, function (field, k) {
            self.value[field] = '';
          });
        }
        if (this.FieldUpdateSpecial) {
          _.forEach(self.FieldUpdateSpecial, function (field, keyUpdate) {
            self.value[keyUpdate] = '';
          });
        }
        this.showIconClose = false;
        this.$emit('on:clear');
        this.$emit('changed', this.value);
      },
      onMouseenter(){
        if (this.value[this.FieldNameData]) {
          this.showIconClose = true;
        }else {
          this.showIconClose = false;
        }
      },
      onMouseLeave() {
        this.showIconClose = false;
      }
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
      placeholderInput: [String],
      name: {},
      api: {},
      table: {},
      tabletrans: {},
      FieldNo: {},
      FieldName: {},
      FieldID: {},
      FieldUpdate:{},
      FieldUpdateSpecial: [Object, String, Array],
      FieldWhere:{},
      FieldSelect: {},
      FieldType: {
        type: Number,
        default: 1
      },
      FieldNoConfig: [Object]
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
    padding-right: 35px;
    background: #fff !important;
    border-bottom-right-radius: 0.25rem !important;
    border-top-right-radius: 0.25rem !important;
  }
  .ijcore-modal-search-input button{
    background: transparent;
    border: none;
    padding: 0.275rem 0.25rem 0.275rem 0;
  }
  .ijcore-modal-search-input button:hover{
    background: transparent !important;
  }
  .ijcore-modal-search-input .input-group {
    align-items: center;
  }

</style>

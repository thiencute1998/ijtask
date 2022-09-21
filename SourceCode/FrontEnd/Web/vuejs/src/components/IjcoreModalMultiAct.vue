<template>
  <a @click="onToggleModal()" class="new-row">
    <i aria-hidden="true" class="fa fa-th-large ij-icon ij-icon-plus" style="font-size: 16px;"></i> {{this.title}}
    <b-modal ref="modal" id="modal">
      <template slot="modal-title">
        Chọn {{this.TitleModal}}
      </template>
      <b-input-group class="el-first-modal">
        <b-form-input v-model="search" :placeholder="'Nhập tên '+this.title" class="readonly form-control"
                      @change="fetchData"></b-form-input>
        <div class="input-group-prepend" @click="fetchData">
                                <span class="input-group-text">
                                  <i class='fa fa-search'></i>
                                </span>
        </div>
      </b-input-group>
      <div class="table-responsive">
        <table class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
          <tbody>
          <tr v-for="(item, key) in link" style="border-bottom: 1px solid #c8ced3;position: relative;" v-if="item && (item[FieldIDData] in link)" @click="selectItemChecked(item[FieldIDData], item[FieldNameData], item[FieldNoData], key)">
            <td class="pr-3 td-ijcheckbox" style="border: none;width: 95%;">
              <b-form-checkbox v-model="item.checked">
                {{item[FieldNameData]}}
              </b-form-checkbox>
            </td>
          </tr>
          <tr v-for="(item, key) in listtable" :style="listtable[key].Style" style="border-bottom: 1px solid #c8ced3;position: relative;"  v-if="item && !(item[FieldIDData] in link)" @click="selectItem(item[FieldIDData], item[FieldNameData], item[FieldNoData], key)"
          >
            <td class="pr-3 td-ijcheckbox" style="border: none;width: 95%;">
              <b-form-checkbox v-model="listtable[key].checked=false">
                {{item[FieldNameData]}}
              </b-form-checkbox>
            </td>
            <!--            <td class="pr-3" style="border: none;width: auto;max-width: none;">-->
            <!--              <i class="fa fa-check-circle-o" v-if="listtable[key].checked == 1 || link[item[tableName+'ID']]" style="position: relative;right: -15px;"></i>-->
            <!--            </td>-->
          </tr>
          </tbody>
        </table>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button
            variant="primary"
            size="md"
            class="float-left mr-2"
            @click="updateForParent()"
          >
            Chọn
          </b-button>
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
  </a>
</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';

  export default {
    name: 'IjcoreModalMultiAct',
    mixins: [mixinLists],
    components: {},
    computed: {
      rows() {
        return this.totalRows
      }
    },
    data() {
      return {
        listtable: [],
        tableName: '',
        search: '',
        TitleModal: '',
        link: [],
        FieldNameData: '',
        FieldNoData: '',
        FieldIDData: '',
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
    },
    methods: {
      clickCheckbox(){
        return false;
      },
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
          if (dataResponse.status == '1') {
            self.totalRows = dataResponse.data.total;
            self.perPage = String(dataResponse.data.per_page);
            self.currentPage = dataResponse.data.current_page;
            // converse object to array
            self.listtable = _.toArray(dataResponse.data.data);
            // this.link = [];
            $.each(self.listtable, function(key, value) {
              value.checked = true;
              self.listtable[value[this.FieldIDData]] = value;
              if(self.value.ArrCoaChecked.includes(value.CoaTypeID)){
                value['key'] = key;
                self.link[value.CoaTypeID] = value;
              }
            });
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
      selectItem(ParentID, ParentName, ParentNo, key, type) {
        let obj = {};
        let self = this;
        if(this.FieldUpdate){
          _.forEach(this.FieldUpdate, function (field, k) {
            obj[field] = self.listtable[key][field];
          });
        }else{
          obj = self.listtable[key];
        }
        obj[this.FieldIDData] = ParentID;
        obj[this.FieldNameData] = ParentName;
        obj[this.FieldNoData] = ParentNo;
        obj['key'] = key;
        obj['checked'] = true;
        // delete this.listtable[ParentID];
        this.link[ParentID] = obj;
        this.$forceUpdate();
      },
      selectItemChecked(ParentID, ParentName, ParentNo, key, type) {
        let obj = {};
        let self = this;
        if(this.FieldUpdate){
          _.forEach(this.FieldUpdate, function (field, k) {
            obj[field] = self.link[key][field];
          });
        }else{
          obj = self.link[key];
        }
        obj[this.FieldIDData] = ParentID;
        obj[this.FieldNameData] = ParentName;
        obj[this.FieldNoData] = ParentNo;
        obj['key'] = key;
        obj['checked'] = true;
        delete this.link[ParentID];
        this.$forceUpdate();
      },
      updateForParent() {
        this.$emit('changed', this.link);
        // this.link = [];
        this.$refs['modal'].hide();
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
        this.fetchData();
        let self = this;
        this.currentPage = 1;
        this.$refs['modal'].show();
      },
      onResetModal() {
      },
    },
    watch: {
      currentPage() {
        this.fetchData();
      },
    },
    props: {
      value: {},
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
    margin-bottom: 0px;
  }
  .td-ijcheckbox{
    padding-top: 0px !important;
    padding-bottom: 0px !important;
  }
  .td-ijcheckbox div{
    height: 30px;
    padding-top: 5px;
  }
  .td-ijcheckbox div label{
    width: 100%;
    cursor: pointer;
  }
</style>

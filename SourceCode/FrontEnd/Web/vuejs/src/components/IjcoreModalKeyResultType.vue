<template>
  <i class="fa fa-external-link" @click="onToggleModal()">
    <input type="hidden" :readonly="true" v-model="value[FieldIDData]" class="readonly form-control">
    <b-modal ref="modal" id="modal">
      <template slot="modal-title">
        {{this.TitleModal}}
      </template>
      <b-input-group class="pt-10">
        <b-form-input v-model="search" :placeholder="'Nhập tên '+this.title" class="readonly form-control"
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
<!--          <tr v-for="item in listtable" @click="updateForParent(item[FieldID], item[FieldNameData], item[FieldNoData])">-->
          <tr v-for="item in listtable" @click="updateForParent(item)">
            <td class="pr-3">{{item[FieldNameData]}}</td>
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
  </i>
</template>

<script>
  import ApiService from '@/services/api.service';

  export default {
    name: 'IjcoreKeyResultType',
    components: {},
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {
        perPage: 10,
        currentPage: 1,
        totalRows: null,
        listtable: [],
        tableName: '',
        search: '',
        TitleModal: '',
        FieldNameData: '',
        FieldNoData: '',
        FieldIDData: '',
        listDetailTable: []
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
            FieldNo: this.FieldNoData
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
      updateForParent: function (item) {
        let self = this;
        this.value[this.FieldIDData] = item[this.FieldID];
        this.value[this.FieldNameData] = item[this.FieldName];
        if(self.FieldUpdate){
          _.forEach(this.FieldUpdate, function (field, k) {
            self.value[field] = item[field];
          });
        }
        this.value[this.FieldNoData] = item[this.FieldNo];

        if (this.tableDetail) {
          let requestData = {
            method: 'post',
            url: '/listing/api/common/list',
            data: {
              per_page: 1000,
              page: 1,
              table: this.tableDetail,
              FieldWhere: {[this.FieldID]: item[this.FieldID]}
            },
          };
          this.$store.commit('isLoading', true);
          ApiService.customRequest(requestData).then((response) => {
            let responseData = response.data;
            if (responseData.status === 1) {
              self.listDetailTable = responseData.data.data;
              self.value.DetailItems = responseData.data.data;
            }
            this.$emit('changed', {
              master: item,
              detail: this.listDetailTable
            });
            self.$store.commit('isLoading', false);
          }, (error) => {
            console.log(error);
            self.$store.commit('isLoading', false);
          });
        } else {
          this.$emit('changed', item);
        }
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
        this.showFirst = false;
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
      },
      FirstShow: function(newVal, oldVal) {
        if(newVal != 0){
          this.onToggleModal();
        }
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
      FieldUpdate: [Array, Object],
      FirstShow: {
        type: Number,
        default() {
          return 0;
        }
      },
      tableDetail: [String]
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
</style>

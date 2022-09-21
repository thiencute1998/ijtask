<template>
  <b-button type="submit" variant="primary" class="main-header-action mr-2" title="Chọn mẫu chỉ tiêu ĐGCV"
            @click="onToggleModal" style="background-color: #00a2e8 !important;">
    <i class='fa fa-external-link'></i> Mẫu
    <input type="hidden" :readonly="true" v-model="value['TemplateID']" class="readonly form-control"></input>
    <b-modal ref="modal" id="modal">
      <template slot="modal-title">
        Mẫu chỉ tiêu đánh giá công việc
      </template>
      <b-input-group class="pt-10">
        <b-form-input v-model="search" :placeholder="'Nhập tên mẫu chỉ tiêu ĐGCV'" class="readonly form-control"
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
          <tr v-for="item in listtable" @click="updateForParent(item['TemplateID'], item['TemplateName'])">
            <td class="pr-3">{{item['TemplateName']}}</td>
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
  </b-button>
</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';

  export default {
    name: 'IjcoreModalIndicatorTemp',
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
      }
    },
    created() {
    },
    mounted() {
    },
    methods: {
      fetchData() {
        let self = this;
        let urlApi = '/task/api/indicator-temp/get-list';
        let requestData = {
          method: 'post',
          url: urlApi,
          data: {
            per_page: 10,
            page: this.currentPage,
            table: this.tableName,
            search: this.search,
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
      updateForParent: function (ParentID, ParentName) {
        this.value['TemplateID'] = ParentID;
        this.value['TemplateName'] = ParentName;
        this.$emit('changed', true);
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
      onCancelModal(e) {
        this.onResetModal();
        e.preventDefault();
      },
      onHideModal() {
        this.$refs['modal'].hide();
      },
      onToggleModal() {
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
      table: {},
      FieldNo: {},
      FieldName: {},
      FieldID: {},
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

x<template>
  <div>
    <b-input-group>
      <b-form-input :placeholder="'Nhập '+this.title" :title="value['ParentName']" :readonly="true" v-model="value['ParentName']"
                    class="readonly form-control" @click="onToggleModal()"></b-form-input>
      <input type="hidden" :readonly="true" v-model="value['ParentID']" class="readonly form-control"></input>
      <div class="input-group-prepend" @click="onToggleModal()">
                <span class="input-group-text">
                  <i class='fa fa-search'></i>
                </span>
      </div>
    </b-input-group>
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
          <tr v-for="(item, key) in listtable" @click="updateForParent(item[FieldIDData], item[FieldNameData], key)">
            <span v-show="false">{{styletd="padding-left: "+(15*(item.Level-1))+"px;"}}</span>
            <td class="pr-3" v-if="ViewType=='tree'&&item.IsHide==0" :style="styletd">
              <i class="fa fa-folder-o" v-if="item.detail!=1&&item.IsOpen==0" @click="showChild(item.DocID, key)" style="float: left;padding-top: 4px; padding-right: 5px;"></i>
              <i class="fa fa-folder-open-o" v-else="item.detail!==1&&item.IsOpen==1" @click="showChild(item.DocID, key)" style="float: left;padding-top: 4px; padding-right: 5px;"></i>
              <div @click="clickTitle" style="height: 100%; float: inherit;"> {{item[FieldNameData]}}</div>
            </td>
            <td class="pr-3" v-else-if="ViewType=='list'" :title="item.Title">
              {{item.Title}}
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
  import mixinLists from '@/mixins/lists';

  export default {
    name: 'IjcoreModalDocSearch',
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
        FieldIDData: '',
        ClickShowChild: 0,
        ViewType: 'Tree'
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
      if (this.FieldName) {
        this.FieldNameData = this.FieldName;
      } else {
        this.FieldNameData = this.tableName + 'Name';
      }
    },
    methods: {
      clickTitle(){
        this.ClickShowChild = 0;
      },
      showChild(ParentID, key){
        this.ClickShowChild = 1;
        let self = this;
        if(this.listtable[key].IsOpen == 1){
          this.listtable[key].IsOpen = 0;
          _.forEach(this.listtable, function (field, k) {
            let str = self.listtable[key].Path? self.listtable[key].Path+self.listtable[key].DocID+'-': '-'+self.listtable[key].DocID+'-';
            if(field.Path&&field.Path.indexOf(str) >= 0){
              self.listtable[k].IsHide = 1;
            }
          });

        }else{
          this.listtable[key].IsOpen = 1;
          _.forEach(this.listtable, function (field, k) {
            let str = self.listtable[key].Path? self.listtable[key].Path+self.listtable[key].DocID+'-': '-'+self.listtable[key].DocID+'-';
            if(field.Path&&field.Path.indexOf(str) >= 0){
              self.listtable[k].IsHide = 0;
            }
          });
          if(this.listtable[key].IsLoad != 1){
            let urlApi = this.api;
            let requestData = {
              method: 'post',
              url: '/doc/api/doc/get-list-child',
              data: {
                ParentID: ParentID
              },
            };
            this.$store.commit('isLoading', true);
            ApiService.customRequest(requestData).then((response) => {
              let dataResponse = response.data;
              if (dataResponse.status == '1') {
                self.listtable[key].IsOpen = 1;
                self.listtable[key].IsLoad = 1;
                _.forEach(dataResponse.data, function (field, k) {
                  field.IsOpen = 0;
                  field.IsHide = 0;
                  self.listtable.splice(key + 1, 0, field);
                });
              }
              self.$forceUpdate()
              self.$store.commit('isLoading', false);
            }, (error) => {
              console.log(error);
              self.$store.commit('isLoading', false);
            });
          }

          return false;
        }

      },
      fetchData() {
        if(this.search){
          this.ViewType = 'list';
        }else{
          this.ViewType = 'tree';
        }
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
            console.log(dataResponse)
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
      updateForParent: function (ParentID, ParentName, ParentNo, Key) {
        if(this.ClickShowChild == 1){
          return false;
        }
        let self = this;
        self.value['ParentID'] = ParentID;
        self.value['ParentName'] = ParentName;
        this.$emit('changed', true);
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
</style>

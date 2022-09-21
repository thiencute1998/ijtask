<template>
  <div>
    <b-input-group>
      <b-form-input placeholder="Chọn công việc cha" :readonly="true" v-model="value.ParentName" class="input-task-parent readonly form-control"></b-form-input>
      <input type="hidden" :readonly="true" v-model="value.ParentID" class="readonly form-control"/>
      <div class="input-group-prepend"  @click="onToggleModal()" disabled="disabled">
                <span class="input-group-text">
                  <i class='fa fa-search'></i>
                </span>
      </div>
    </b-input-group>
    <b-modal ref="modal" id="modal">
      <template slot="modal-title">
        {{this.title}}
      </template>
      <b-input-group>
        <b-form-input v-model="search" placeholder="Nhập tên công việc cha" class="readonly form-control" @change="fetchData"></b-form-input>
        <div class="input-group-prepend"  @click="fetchData">
                                <span class="input-group-text">
                                  <i class='fa fa-search'></i>
                                </span>
        </div>
      </b-input-group>
      <div class="table-responsive">
        <table class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
          <tbody>
          <tr v-for="item in listtable" @click="updateForParent(item[tableName+'ID'], item[tableName+'Name'], item[tableName+'No'], formatDate(item['StartDate']), formatDate(item['DueDate']))">
<!--            <td class="pr-3 tr-no" :style="lenghNo">{{item[tableName + 'No']}}</td>-->
<!--            <td class="pr-3"><span :title="item[tableName + 'Name']">{{item[tableName + 'Name']}}</span></td>-->
            <td class="pr-3"><span :title="getName(item)">{{getName(item)}}</span></td>
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
    name: 'IjcoreModalTask',
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
      this.$el.querySelector('.input-task-parent').addEventListener('click', function () {
        self.onToggleModal();
      });
    },
    methods: {
      formatDate(data){
        if (data) {
          data = data.split(' ');
          data = data[0];
          data = data.split('-');
          let dd = data[2];
          let mm = data[1];
          let yyyy = data[0];
          data = dd + '/' + mm + '/' + yyyy;
        }
        return data;
      },
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
      getName(item){
        if (this.tableName === 'task' || this.tableName === 'Task') {
          let name = '';
          if (item.Type === 2 && item.ParentName) {
            name = item.TaskName + ' (' + item.ParentName + ')';
          } else {
            name = item.TaskName;
          }
          name = __.stripHtml(name);
          return name;
        } else {
          return item[this.tableName + 'Name'];
        }
      },
      updateForParent: function (ParentID, ParentName, ParentNo, StartDate, DueDate) {
        this.value.ParentID = ParentID;
        this.value.ParentName = ParentName;
        this.value.ParentNo = ParentNo;
        let oldStartDate = this.value['StartDate'];
        let oldDueDate = this.value['DueDate'];
        this.value.statusHour = 0;
        if(oldStartDate != StartDate){
          if(__.convertDate(this.value.StartDate) < __.convertDate(StartDate)){
            this.value.StartDate = StartDate;
          }else{
            this.value.StartDate = StartDate;
          }
          this.value.statusHour = 1;
        }

        if(__.convertDate(this.value.DueDate) < __.convertDate(DueDate)){
          this.value.DueDate = DueDate;
        }else{
          this.value.DueDate = DueDate;
        }
        this.$refs['modal'].hide();
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
      title:{},
      name:{},
      api: {},
      table: {},
      disabled: {}
    },
  }
</script>
<style>
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

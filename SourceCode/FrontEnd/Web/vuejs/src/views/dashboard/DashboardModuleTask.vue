<template>
  <div class="animated fadeIn component-dashboard-module-task">
    <div class="row">
      <div class="col-24 mb-2">
        <b-card-group deck class="task-statistic">
          <b-card :bg-variant="(taskType === 1) ? 'primary' : 'secondary'" class="text-center mb-md-2 mb-lg-2 mb-xl-0" @click="changeTaskType(1)">
            <b-card-text>
              <div><h5 class="d-inline">{{totalTask.Total1}}</h5> công việc chưa thực hiện</div>
            </b-card-text>
          </b-card>
          <b-card :bg-variant="(taskType === 2) ? 'primary' : 'secondary'" class="text-center mb-md-2 mb-lg-2 mb-xl-0" @click="changeTaskType(2)">
            <b-card-text>
              <div><h5 class="d-inline">{{totalTask.Total2}}</h5> công việc chờ duyệt</div>
            </b-card-text>
          </b-card>
          <b-card :bg-variant="(taskType === 3) ? 'primary' : 'secondary'" class="text-center mb-md-2 mb-lg-2 mb-xl-0" @click="changeTaskType(3)">
            <b-card-text>
              <div><h5 class="d-inline">{{totalTask.Total3}}</h5> công việc đã từ chối</div>
            </b-card-text>
          </b-card>
          <b-card :bg-variant="(taskType === 4) ? 'primary' : 'secondary'" class="text-center mb-md-2 mb-lg-2 mb-xl-0" @click="changeTaskType(4)">
            <b-card-text>
              <div><h5 class="d-inline">{{totalTask.Total4}}</h5> công việc đang thực hiện</div>
            </b-card-text>
          </b-card>
          <b-card :bg-variant="(taskType === 5) ? 'primary' : 'secondary'" class="text-center mb-md-2 mb-lg-2 mb-xl-0" @click="changeTaskType(5)">
            <b-card-text>
              <div><h5 class="d-inline" title="Công việc đã hoàn thành">{{totalTask.Total5}}</h5> công việc đã HT</div>
            </b-card-text>
          </b-card>
          <b-card :bg-variant="(taskType === 6) ? 'primary' : 'secondary'" class="text-center mb-md-2 mb-lg-2 mb-xl-0" @click="changeTaskType(6)">
            <b-card-text>
              <div><h5 class="d-inline">{{totalTask.Total6}}</h5> công việc khẩn cấp</div>
            </b-card-text>
          </b-card>
          <b-card :bg-variant="(taskType === 7) ? 'primary' : 'secondary'" class="text-center mb-md-2 mb-lg-2 mb-xl-0" @click="changeTaskType(7)">
            <b-card-text>
              <div><h5 class="d-inline">{{totalTask.Total7}}</h5> công việc trễ hạn</div>
            </b-card-text>
          </b-card>
          <b-card :bg-variant="(taskType === 8) ? 'primary' : 'secondary'" class="text-center mb-md-2 mb-lg-2 mb-xl-0" @click="changeTaskType(8)">
            <b-card-text>
              <div><h5 class="d-inline">{{totalTask.Total8}}</h5> công việc yếu kém</div>
            </b-card-text>
          </b-card>
          <b-card :bg-variant="(taskType === 9) ? 'primary' : 'secondary'" class="text-center mb-md-2 mb-lg-2 mb-xl-0" @click="changeTaskType(9)">
            <b-card-text>
              <div><h5 class="d-inline">{{totalTask.Total9}}</h5> công việc tôi giao</div>
            </b-card-text>
          </b-card>
          <b-card :bg-variant="(taskType === 10) ? 'primary' : 'secondary'" class="text-center mb-md-2 mb-lg-2 mb-xl-0" @click="changeTaskType(10)">
            <b-card-text title="Công việc tôi chịu trách nhiệm chính">
              <div><h5 class="d-inline">{{totalTask.Total10}}</h5> công việc tôi chịu TNC</div>
            </b-card-text>
          </b-card>
          <b-card :bg-variant="(taskType === 11) ? 'primary' : 'secondary'" class="text-center mb-md-2 mb-lg-2 mb-xl-0" @click="changeTaskType(11)">
            <b-card-text>
              <div><h5 class="d-inline">{{totalTask.Total11}}</h5> công việc tôi thực hiện</div>
            </b-card-text>
          </b-card>
        </b-card-group>
      </div>
      <div class="col-24">
        <div class="main-entry">
          <div class="main-body main-body-view-list">
            <b-card class="m-0 border-0" body-class="py-0 px-0">
              <div class="content-body">
                <div class="content-table content-body-list">
                  <div class="b-table-sticky-header table-responsive">
                    <table role="table" aria-busy="false" aria-colcount="2" aria-multiselectable="true"
                           class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
                      <thead role="rowgroup" class="thead-light">
                      <tr role="row" class="">
                        <th role="columnheader" scope="col" style="width: 15%; min-width: 250px">Tên công việc</th>
                        <th role="columnheader" class="d-md-none d-lg-none d-xl-table-cell" scope="col" style="width: 5%; min-width: 50px">Ưu tiên</th>
                        <th role="columnheader" class="d-md-none d-lg-none d-xl-table-cell" scope="col" style="width: 3%; min-width: 60px">Ngày tạo</th>
                        <th role="columnheader" class="d-md-none d-lg-none d-xl-table-cell" scope="col" title="Ngày bắt đầu" style="width: 3%; min-width: 60px">Ngày BĐ</th>
                        <th role="columnheader" scope="col" title="Hạn hoàn thành" style="width: 3%; min-width: 60px">Hạn HT</th>
                        <th role="columnheader" class="d-md-none d-lg-none d-xl-table-cell" scope="col" title="Người thực hiện" style="width: 3%; min-width: 60px">Người TH</th>
                        <th role="columnheader" class="d-md-none d-lg-none d-xl-table-cell" scope="col" style="width: 2%; min-width: 60px">Trạng thái</th>
                        <th role="columnheader" scope="col" title="Phần trăm hoàn thành" style="width: 1%; min-width: 40px">% HT</th>
                      </tr>
                      </thead>
                      <tbody role="rowgroup">
                        <tr role="row" tabindex="0"
                            aria-selected="false"
                            :class="[(!item.ParentID) ? 'task-parent' : 'task-children', (itemsArray[key].rowSelected) ? 'b-table-row-selected table-active' : '']"
                            :data-parent-id="item.ParentID"
                            v-if="!itemsArray[key].hidden"
                            :key="key"
                            v-for="(item, key) in itemsArray">
                          <td aria-colindex="2" role="cell" class="field-task-name">
                            <div @click="onRowClicked(item)">
                              <span :title="getTaskName(item)">{{getTaskName(item)}}</span>
                            </div>
                          </td>
                          <td @click="onRowClicked(item)" class="d-md-none d-lg-none d-xl-table-cell" aria-colindex="2" role="cell">
                            <span>{{Priority[item.Priority]}}</span>
                          </td>
                          <td @click="onRowClicked(item)" class="d-md-none d-lg-none d-xl-table-cell text-center" aria-colindex="2" role="cell">
                            <span>{{item.CreateDate | convertServerDateToClientDate}}</span>
                          </td>
                          <td @click="onRowClicked(item)" class="d-md-none d-lg-none d-xl-table-cell text-center" aria-colindex="2" role="cell">
                            <span>{{item.StartDate | convertServerDateToClientDate}}</span>
                          </td>
                          <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="text-center">
                            <span>{{item.DueDate | convertServerDateToClientDate}}</span>
                          </td>
                          <td @click="onRowClicked(item)" class="d-md-none d-lg-none d-xl-table-cell" aria-colindex="2" role="cell">
                            <ijcore-users-icon :all-users="model.taskAssign" filter-name="TaskID" :filter-value="item.TaskID" :number="4"></ijcore-users-icon>
                          </td>
                          <td @click="onRowClicked(item)" class="d-md-none d-lg-none d-xl-table-cell" aria-colindex="2" role="cell">
                            <span>{{item.StatusDescription}}</span>
                          </td>
                          <td @click="onRowClicked(item)" aria-colindex="2" role="cell" class="text-right pr-3">
                            <div class="d-inline-flex align-items-center justify-content-end">
                              <b-badge :variant="(item.PercentCompleted <= 0) ? 'warning' : (item.PercentCompleted > 0 && item.PercentCompleted < 100) ? 'primary' : 'success'">{{item.PercentCompleted}}</b-badge>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                </div>
              </div>
            </b-card>
          </div>
          <div class="main-footer">
            <div class="d-flex flex-wrap justify-content-between align-items-center m-0">
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
              <div class="total-item">
                <span style="font-weight: 500">Tổng số: {{totalRows}}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';
  import IjcoreUsersIcon from "../../components/IjcoreUsersIcon";
  let currentUser = JSON.parse(localStorage.getItem('Employee'));

  export default {
    name: 'dashboard-module-task',
    mixins: [mixinLists],
    props: {
      DateRange: [Array, Object],
      query: [Array, Object]
    },
    data() {
      return {
        taskType: 1,
        model: {
          taskAssign: []
        },
        filter: {
          TaskName: (this.$route.query && this.$route.query.TaskName) ? this.$route.query.TaskName : '',
          Type: (this.$route.query && this.$route.query.Type) ? this.$route.query.Type : null,
          CreateDate: (this.$route.query && this.$route.query.CreateDate) ? this.$route.query.CreateDate : null,
          StartDate: (this.$route.query && this.$route.query.StartDate) ? this.$route.query.StartDate : null,
          DueDate: (this.$route.query && this.$route.query.DueDate) ? this.$route.query.DueDate : null,
          DateRange: (this.$route.query && this.$route.query.DateRange) ? this.$route.query.DateRange : null,
          PercentCompleted: (this.$route.query && this.$route.query.PercentCompleted) ? this.$route.query.PercentCompleted : null,
          Priority: (this.$route.query && this.$route.query.Priority) ? this.$route.query.Priority : null,
          AccessType: (this.$route.query && this.$route.query.AccessType) ? this.$route.query.AccessType : null,
          Status: (this.$route.query && this.$route.query.Status) ? this.$route.query.Status : [],
          Company: (this.$route.query && this.$route.query.Company) ? this.$route.query.Company : null,
          Project: (this.$route.query && this.$route.query.Project) ? this.$route.query.Project : null,
          CreateEmployeeID: (this.$route.query && this.$route.query.CreateEmployeeID) ? this.$route.query.CreateEmployeeID : null,
          AssignEmployeeID: (this.$route.query && this.$route.query.AssignEmployeeID) ? this.$route.query.AssignEmployeeID : null,
          ReceiveEmployeeID: (this.$route.query && this.$route.query.ReceiveEmployeeID) ? this.$route.query.ReceiveEmployeeID : null,
          ResponEmployeeID: (this.$route.query && this.$route.query.ResponEmployeeID) ? this.$route.query.ResponEmployeeID : null,
          ImplementEmployeeID: (this.$route.query && this.$route.query.ImplementEmployeeID) ? this.$route.query.ImplementEmployeeID : null,
          Contract: (this.$route.query && this.$route.query.Contract) ? this.$route.query.Contract : null,
          Customer: (this.$route.query && this.$route.query.Customer) ? this.$route.query.Customer : null,
          Vendor: (this.$route.query && this.$route.query.Vendor) ? this.$route.query.Vendor : null,
          Object: (this.$route.query && this.$route.query.Object) ? this.$route.query.Object : null,
          Item: (this.$route.query && this.$route.query.Item) ? this.$route.query.Item : null,
          Doc: (this.$route.query && this.$route.query.Doc) ? this.$route.query.Doc : null,
          TaskLink: (this.$route.query && this.$route.query.TaskLink) ? this.$route.query.TaskLink : [],
          TaskCateList: (this.$route.query && this.$route.query.TaskCateList) ? this.$route.query.TaskCateList : [],
          ExecutionStatus: 1,
          Expired: false
        },
        Priority: {
          1: 'Khẩn cấp',
          2: 'Cao',
          3: 'Bình thường',
          4: 'Thấp',
        },
        PriorityOptions: [],
        AccessType: [],
        AccessTypeOptions: [],
        TypeOptions: [],
        totalTask: []
      }
    },
    computed: {
      rows() {
        return this.totalRows
      },
    },
    components: {
      IjcoreUsersIcon
    },
    created() {
      // init setting
      this.settings.FieldID = 'TaskID';
      this.settings.Table = 'task';
      this.settings.FieldInactive = 'Inactive';

      this.settings.ListApi = 'task/api/task';
      this.settings.DeleteApi = 'task/api/task/delete';
      this.settings.CreateRouter = 'task-task-create';
      this.settings.EditRouter = 'task-task-edit';
      this.settings.ViewRouter = 'task-task-view';

      this.modelSearch = {
        TaskName: ''
      };
      this.init();
    },
    mounted () {
    },
    methods: {
      init(){
        this.fetchData();
        this.getStatistic();
      },
      fetchData(){
        let self = this;
        let urlApi = this.settings.ListApi;
        let requestData = {
          method: 'post',
          url: urlApi,
          data: {
            per_page: this.perPage,
            page: this.currentPage,
            viewType: 'list',
            filter: {},
            dashboard: true
          },

        };

        if (this.filter.TaskName !== '') {
          requestData.data.filter.TaskName = this.filter.TaskName;
        }
        if (this.filter.Type) {
          requestData.data.filter.Type = this.filter.Type;
        }

        if (this.DateRange && this.DateRange.fromDate && this.DateRange.fromDate !== '' && this.DateRange.fromDate !== '__/__/____') {
          requestData.data.filter.fromDate = this.DateRange.fromDate;
        }
        if (this.DateRange && this.DateRange.toDate && this.DateRange.toDate !== '' && this.DateRange.toDate !== '__/__/____') {
          requestData.data.filter.toDate = this.DateRange.toDate;
        }

        if (this.filter.Priority) {
          requestData.data.filter.Priority = this.filter.Priority;
        }
        if (this.filter.CreateDate && !_.isEmpty(this.filter.CreateDate.dateTime) && this.filter.CreateDate.dateTime !== '__/__/____') {
          requestData.data.filter.CreateDate = this.filter.CreateDate;
        }
        if (this.filter.StartDate && !_.isEmpty(this.filter.StartDate.dateTime) && this.filter.StartDate.dateTime !== '__/__/____') {
          requestData.data.filter.StartDate = this.filter.StartDate;
        }
        if (this.filter.DueDate && !_.isEmpty(this.filter.DueDate.dateTime) && this.filter.DueDate.dateTime !== '__/__/____') {
          requestData.data.filter.DueDate = this.filter.DueDate;
        }
        if (this.filter.Status && this.filter.Status.length) {
          requestData.data.filter.Status = this.filter.Status;
        }
        if (this.filter.PercentCompleted) {
          requestData.data.filter.PercentCompleted = this.filter.PercentCompleted;
        }
        if (this.filter.DateRange && this.filter.DateRange.fromDate && this.filter.DateRange.fromDate !== '' && this.filter.DateRange.fromDate !== '__/__/____') {
          requestData.data.filter.fromDate = this.filter.DateRange.fromDate;
        }
        if (this.filter.DateRange && this.filter.DateRange.toDate && this.filter.DateRange.toDate !== '' && this.filter.DateRange.toDate !== '__/__/____') {
          requestData.data.filter.toDate = this.filter.DateRange.toDate;
        }
        if (this.filter.AccessType) {
          requestData.data.filter.AccessType = this.filter.AccessType;
        }
        if (this.filter.CreateEmployeeID) {
          requestData.data.filter.CreateEmployeeID = this.filter.CreateEmployeeID;
        }
        if (this.filter.AssignEmployeeID) {
          requestData.data.filter.AssignEmployeeID = this.filter.AssignEmployeeID;
        }
        if (this.filter.ReceiveEmployeeID) {
          requestData.data.filter.ReceiveEmployeeID = this.filter.ReceiveEmployeeID;
        }
        if (this.filter.ResponEmployeeID) {
          requestData.data.filter.ResponEmployeeID = this.filter.ResponEmployeeID;
        }
        if (this.filter.ImplementEmployeeID) {
          requestData.data.filter.ImplementEmployeeID = this.filter.ImplementEmployeeID;
        }
        if (this.filter.ExecutionStatus) {
          requestData.data.filter.ExecutionStatus = this.filter.ExecutionStatus;
        }

        if (this.filter.Expired) {
          requestData.data.filter.Expired = true;
        }

        if (this.fullTextSearch !== '') {
          requestData.data.fullTextSearch = this.fullTextSearch;
        }

        _.forEach(requestData.data.filter, function (value, key) {
          self.query[key] = value;
        });

        if (!_.isEmpty(self.query)){
          if (!_.isEqual(this.$route.query, self.query)) {
            this.$router.replace({query: self.query});
          }
        }
        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let responseData = response.data;
          if (responseData.status === 1) {
            self.totalRows = responseData.data.data.total;
            self.perPage = String(responseData.data.data.per_page);
            self.currentPage = responseData.data.data.current_page;
            // converse object to array
            self.itemsArray = _.toArray(responseData.data.data.data);
            self.itemsArray = _.uniqBy(self.itemsArray, 'TaskID');

            self.model.taskAssign = responseData.data.taskAssign;

            // set params request
            self.paramsReqRouter.lastPage = responseData.data.data.last_page;
            self.paramsReqRouter.from = responseData.data.data.from;
            self.paramsReqRouter.to = responseData.data.data.to;
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
      getStatistic(){
        let self = this;
        let requestData = {
          method: 'post',
          url: 'task/api/dashboard-statistic',
          data: {
            dashboard: true,
            filter: {}
          },
        };

        if (this.DateRange && this.DateRange.fromDate && this.DateRange.fromDate !== '' && this.DateRange.fromDate !== '__/__/____') {
          requestData.data.filter.fromDate = this.DateRange.fromDate;
        }
        if (this.DateRange && this.DateRange.toDate && this.DateRange.toDate !== '' && this.DateRange.toDate !== '__/__/____') {
          requestData.data.filter.toDate = this.DateRange.toDate;
        }

        ApiService.customRequest(requestData).then((response) => {
          let responseData = response.data;
          if (responseData.status === 1) {
            self.totalTask = responseData.data;
          }
        }, (error) => {
          console.log(error);
        });
      },
      getTaskName(task){
        let name = '';
        if (task.Type === 2 && task.ParentName) {
          name = task.TaskName + ' (' + task.ParentName + ')';
        } else {
          name = task.TaskName;
        }
        name = __.stripHtml(name);
        return name;
      },
      onRowClicked(item){
        this.$router.push({
          name: this.settings.ViewRouter,
          params: {id: item[this.settings.FieldID], req: this.paramsReqRouter}
        });
      },
      changeTaskType(type) {
        // Chưa thực hiện | Đang chờ | Đang thực hiện | Tạm dừng | Hủy bỏ | Đã hoàn thành
        this.resetFilter();
        this.taskType = type;
        switch (type) {
          case 1:
            this.filter.ExecutionStatus = 1;
            this.fetchData();
            break;
          case 2:
            this.filter.ExecutionStatus = 2;
            this.fetchData();
            break;
          case 3:
            this.filter.ExecutionStatus = 5;
            this.fetchData();
            break;
          case 4:
            this.filter.ExecutionStatus = 3;
            this.fetchData();
            break;
          case 5:
            this.filter.ExecutionStatus = 6;
            this.fetchData();
            break;
          case 6:
            // Công việc khẩn cấp
            this.filter.Priority = 1;
            this.fetchData();
            break;
          case 7:
            // Trễ hạn Expired: false
            this.filter.Expired = true;
            this.fetchData();
            break;
          case 8:
            // TODO: yếu kém
            this.totalRows = 0;
            this.currentPage = 1;
            // converse object to array
            this.itemsArray = [];

            // set params request
            this.paramsReqRouter.lastPage = 0;
            this.paramsReqRouter.from = 0;
            this.paramsReqRouter.to = 0;
            break;
          case 9:
            this.filter.AssignEmployeeID = currentUser.EmployeeID;
            this.fetchData();
            break;
          case 10:
            this.filter.ResponEmployeeID = currentUser.EmployeeID;
            this.fetchData();
            break;
          case 11:
            this.filter.ImplementEmployeeID = currentUser.EmployeeID;
            this.fetchData();
            break;
          default:
            break;
        }
      },
      resetFilter(){
        this.filter.ExecutionStatus = null;
        this.filter.Priority = null;
        this.filter.Expired = false;
        this.filter.AssignEmployeeID = null;
        this.filter.ResponEmployeeID = null;
        this.filter.ImplementEmployeeID = null;
      }
    },
    watch: {
      'DateRange': {
        handler(val){
          // do stuff
          this.init();
        },
        deep: true
      }
    }
  }
</script>
<style>
  .component-dashboard-module-task .bg-primary{
    background: rgba(0, 162, 232, 0.6) !important;
  }
</style>

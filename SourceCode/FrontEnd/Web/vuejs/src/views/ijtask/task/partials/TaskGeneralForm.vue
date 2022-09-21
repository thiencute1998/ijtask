<template>
  <div class="main-entry">
    <vue-perfect-scrollbar class="scroll-area" :settings="$store.state.psSettings">
      <div class="form-group row align-items-center el-first-modal">
        <div class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên công việc
          <i style="cursor: pointer;"
             :class="[(!perEditField(value.DoneNowType, per, 'DoneNowType') || value.Type === 2 ? 'app-disable' : '')]"
             :style="{color: (value.DoneNowType !== 0) ? '#00a2e8' : '#f3d2d2'}"
             @click="changeIsNotification"
             class="fa fa-bullhorn" title="Là thông báo"></i>
        </div>
        <div class="col-lg-14" v-if="perEditView(value.TaskName, per, 'TaskName')">
          <input type="text" v-model="value.TaskName" v-if="perEditField(value.TaskName, per, 'TaskName')" id="task-name" class="form-control"
                 name="ItemName" placeholder="Nhập tên công việc"/>
          <input type="text" v-else disabled="true" class="form-control" :value="value.TaskName"
                 placeholder=""/>
        </div>
        <div class="col-lg-14" v-else>
          <input type="text" disabled class="form-control"
                 placeholder=""/>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code"  v-if="perEditView(value.TaskNo, per, 'TaskNo')">
          <span>Mã số</span>
          <input type="text" v-model="value.TaskNo"  v-if="perEditField(value.TaskNo, per, 'TaskNo')" class="form-control" placeholder="Nhập mã số"/>
          <input type="text" v-else class="form-control" placeholder="Nhập mã số" :value="value.TaskNo"/>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code"  v-else>
          <span>Mã số</span>
          <input type="text" disabled class="form-control" placeholder=""/>
        </div>
      </div>

      <div class="form-group row align-items-center">
        <div class="col-md-4" style="white-space: nowrap">Là con của</div>
        <div class="col-md-14" v-if="perEditView(value.ParentID, per, 'ParentID')">
          <IjcoreModalTask v-model="value" :today="value.ParentID" :title="'Công việc'" v-if="perEditField(value.ParentID, per, 'TaskNo')"
                           :api="'/task/api/task/list-modal'" :table="'task'">
          </IjcoreModalTask>
          <input type="text" disabled v-else class="form-control" :value="value.ParentName"/>
        </div>
        <div class="col-md-14" v-else>
          <input type="text" disabled class="form-control"/>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code"  v-if="perEditView(value.ParentID, per, 'ParentID')">
          <span>Mã số</span>
          <input type="text" v-model="value.ParentNo"  v-if="perEditField(value.ParentNo, per, 'ParentNo')" class="form-control" placeholder="Nhập mã số"/>
          <input type="text" disabled v-else class="form-control" :value="value.ParentNo"/>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code"  v-else>
          <span>Mã số</span>
          <input type="text" disabled class="form-control" placeholder=""/>
        </div>
      </div>

      <div class="form-group row align-items-center" v-if="value.DoneNowType !== 0">
        <label class="col-md-4 m-0" title="Loại công việc hoàn thành ngay">Loại công việc HT ngay</label>
        <div class="col-lg-6">
          <b-form-select v-model="value.DoneNowType" :options="[{value: 1, text: 'Thông báo'}, {value: 2, text: 'Nhắc nhở'}]"></b-form-select>
        </div>
      </div>

      <div class="form-group row align-items-center">
        <label class="col-md-4" style="white-space: nowrap">Mức độ ưu tiên</label>
        <div class="col-md-4" v-if="perEditView(value.Priority, per, 'Priority')">
          <b-form-select v-model="value.Priority" :options="value.PriorityOptions" id="item-uom"  v-if="perEditField(value.Priority, per, 'Priority')"></b-form-select>
          <input type="text" disabled v-else class="form-control" placeholder="" :value="value.PriorityName"/>
        </div>
        <div class="col-md-2" v-else>
          <input type="text" disabled class="form-control" placeholder=""/>
        </div>

        <div class="col-md-4" style="white-space: nowrap">Quyền truy cập</div>
        <div class="col-md-4" v-if="perEditView(value.AccessType, per, 'AccessType')">
          <b-form-select v-model="value.AccessType" :options="value.AccessTypeOptions" id="item-uom" v-if="perEditField(value.AccessType, per, 'AccessType')"></b-form-select>
          <input type="text" disabled v-else class="form-control" placeholder="" :value="value.AccessTypeName"/>
        </div>
        <div class="col-md-4" v-else>
          <input type="text" disabled class="form-control" placeholder=""/>
        </div>

        <div class="col-md-8" v-if="value.AccessType==3&&perEditView(value.PublicCompanyID, per, 'PublicCompanyID')">
          <b-form-select v-model="value.PublicCompanyID" :options="value.CompanyOptions"
                         id="PublicCompanyID" v-if="perEditField(value.PublicCompanyID, per, 'PublicCompanyID')"></b-form-select>
          <input type="text" disabled v-else class="form-control" placeholder="" :value="value.PublicCompanyName"/>
        </div>
        <div class="col-md-8" v-if="value.AccessType==3&&!perEditView(value.PublicCompanyID, per, 'PublicCompanyID')">
          <input type="text" disabled class="form-control" placeholder=""/>
        </div>
      </div>
      <div class="form-group row">
        <label class="col-md-4" style="white-space: nowrap">Mô tả</label>
        <div class="col-md-20" v-if="perEditView(value.TaskDescription, per, 'TaskDescription')">
          <textarea v-model="value.TaskDescription" class="form-control" id="TaskDescription" rows="3"
                    placeholder="Nhập mô tả" name="TaskDescription"  v-if="perEditField(value.TaskDescription, per, 'TaskDescription')"></textarea>
          <textarea disabled class="form-control" id="TaskDescription" rows="3" v-else :value="value.TaskDescription"></textarea>
        </div>
        <div class="col-md-20" v-else>
          <textarea disabled class="form-control" id="TaskDescription" rows="3"></textarea>
        </div>
      </div>

      <div class="form-group row align-items-center">
        <label class="col-md-4 m-0">Đơn vị tính</label>
        <div class="col-md-4"  v-if="perEditView(value.UomID, per, 'UomID')">
          <b-form-select v-model="value.UomID" :options="value.UomOptions" id="UomID" v-if="perEditField(value.UomID, per, 'UomID')"></b-form-select>
          <input type="text" disabled v-else class="form-control" placeholder="" :value="value.UomName"/>
        </div>
        <div class="col-md-2"  v-else>
          <input type="text" disabled class="form-control" placeholder=""/>
        </div>
        <div class="col-md-4"></div>
        <label class="col-md-4 m-0">Kiểu lịch</label>
        <div class="col-md-8" v-if="perEditView(value.CalendarTypeID, per, 'CalendarTypeID')">
          <b-form-select v-model="value.CalendarTypeID" :options="value.CalendarOptions"  v-if="perEditField(value.CalendarTypeID, per, 'CalendarTypeID')"
                         id="CalendarTypeID"></b-form-select>
          <input type="text" disabled v-else class="form-control" placeholder="" :value="value.CalendarName"/>
        </div>
        <div class="col-md-8"  v-else>
          <input type="text" disabled class="form-control" placeholder=""/>
        </div>
      </div>

      <div class="form-group row align-items-center">
        <label class="col-md-4 m-0">Ngày bắt đầu</label>
        <div class="col-md-8" v-on:mouseover="updateStatusHour()" v-if="perEditView(value.StartDate, per, 'StartDate')">
          <IjcoreDatePicker v-model="value.StartDate" v-if="perEditField(value.StartDate, per, 'StartDate')">
          </IjcoreDatePicker>
          <input type="text" disabled v-else class="form-control" placeholder="" :value="value.StartDate"/>
        </div>
        <div class="col-md-8"  v-else>
          <input type="text" disabled class="form-control" placeholder=""/>
        </div>

        <label class="col-md-4 m-0">Hạn hoàn thành</label>
        <div class="col-md-8" v-on:mouseover="updateStatusHour()" v-if="perEditView(value.DueDate, per, 'DueDate')">
          <IjcoreDatePicker v-model="value.DueDate" v-if="perEditField(value.StartDate, per, 'StartDate')">
          </IjcoreDatePicker>
          <input type="text" disabled v-else class="form-control" placeholder="" :value="value.DueDate"/>
        </div>
        <div class="col-md-8"  v-else>
          <input type="text" disabled class="form-control" placeholder=""/>
        </div>

      </div>
      <div class="form-group row align-items-center">
        <label class="col-md-4 m-0">Số giờ ước thực hiện</label>
        <div class="col-md-8" v-if="perEditView(value.Duration, per, 'Duration')">
          <ijcore-number v-model="value.Duration" v-if="perEditField(value.Duration, per, 'Duration')"
                 placeholder="Nhập số giờ ước thực hiện"/>
          <input type="text" disabled v-else class="form-control" placeholder="" :value="value.Duration"/>
        </div>
        <div class="col-md-8"  v-else>
          <input type="text" disabled class="form-control" placeholder=""/>
        </div>

        <label class="col-md-4 m-0">KL ước thực hiện</label>
        <div class="col-md-8" v-if="perEditView(value.EstimatedQuantity, per, 'EstimatedQuantity')">
          <ijcore-number v-model="value.EstimatedQuantity" v-if="perEditField(value.EstimatedQuantity, per, 'EstimatedQuantity')"
                 name="EstimatedQuantity" placeholder="Nhập khối lượng ước thực hiện"/>
          <input type="text" disabled v-else class="form-control" placeholder="" :value="value.EstimatedQuantity"/>
        </div>
        <div class="col-md-8"  v-else>
          <input type="text" disabled class="form-control" placeholder=""/>
        </div>
      </div>

      <div class="form-group row align-content-center">
        <label class="col-md-4 m-0">Loại công việc</label>
        <div class="col-md-20">
          <task-modal-search-input-tcatelist
            v-model="value.TaskCate"
            tableApi="task_cate_list"
            refModal="myModalSearchTcatelist"
            id-modal="myModalSearchTcatelist"
            placeholder="Loại công việc"
            title-modal="Loại công việc" size-modal="lg"></task-modal-search-input-tcatelist>
        </div>
      </div>

      <div class="form-group row align-content-center">
        <label class="col-md-4 m-0">Loại trạng thái</label>
        <div class="col-md-8" v-if="perEditView(value.StatusID, per, 'StatusID')">
          {{value.StatusName}}
        </div>
        <label class="col-md-4 m-0">Trạng thái</label>
        <div class="col-md-8" v-if="perEditView(value.StatusID, per, 'StatusID')">
          <b-form-select v-model="value.StatusValue" :options="value.StatusValueOption"></b-form-select>
        </div>
      </div>
    </vue-perfect-scrollbar>
  </div>
</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';
  import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
  import IjcoreModalTask from "../../../../components/IjcoreModalTask";
  import {TokenService} from '@/services/storage.service';
  import IjcoreNumber from "../../../../components/IjcoreNumber";
  import TaskModalSearchInputTcatelist from "./TaskModalSearchInputTcatelist";

  export default {
    name: 'TaskGeneralForm',
    mixins: [mixinLists],
    components: {
      IjcoreNumber,
      IjcoreModalTask,
      IjcoreDatePicker,
      TaskModalSearchInputTcatelist
    },
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {
        CalMethodName: '',
        listtable: [],
        tableName: '',
        search: '',
        lenghNo: 0,
      }
    },
    created() {
    },
    mounted() {
    },
    methods: {
      perEditView(value, per, field) {
        var AccessField = ','+per['AccessField']+',';
        if(per['AccessField'] == 'all' || AccessField.includes(','+field+',')){
          return true;
        }else{
          return false;
        }
      },
      perEditField(value, per, field){
        var EditField = ','+per['EditField']+',';
        if(per['EditField'] == 'all' || EditField.includes(','+field+',')){
          return true;
        }else{
          return false;
        }
      },
      updateStatusHour() {
        this.value.statusHour = 0;
      },
      fetchData() {
      },
      changeIsNotification(){
        if (!this.perEditField(this.value.DoneNowType, this.per, 'DoneNowType') || this.value.Type === 2) {
          this.$bvToast.toast('Bạn không có quyền sửa', {
            title: 'Thông báo',
            variant: 'warning',
            solid: true
          });
          return false;
        } else {
          let workDate = TokenService.getWorkdate();
          if(this.value.DoneNowType === 0){
            this.value.Type = 1;
            this.value.DueDate = this.value.StartDate = workDate;
            this.value.DoneNowType = 1;
            this.value.ParentID = null;
            this.value.ParentName = '';
            this.value.ParentNo = '';
          }else{
            this.value.DoneNowType = 0;
          }
          this.$forceUpdate();
        }
      },
      handleSubmitForm() {
        let self = this;
        const requestData = {
          method: 'post',
          url: StoreApi,
          data: {
            TaskNo: this.value.TaskNo,
            TaskName: this.value.TaskName,
            ParentID: this.value.ParentID,
            ParentName: this.value.ParentName,
            Priority: this.value.Priority,
            AccessType: this.value.AccessType,
            PublicCompanyID: this.value.PublicCompanyID,
            TaskDescription: this.value.TaskDescription,
            UomID: this.value.UomID,
            CalendarTypeID: this.value.CalendarTypeID,
            StartDate: this.value.StartDate,
            DueDate: this.value.DueDate,
            Duration: this.value.Duration,
            EstimatedQuantity: this.value.EstimatedQuantity,
          }
        };

        // edit user
        if (this.idParams) {
          requestData.data.ItemID = this.idParams;
          requestData.url = UpdateApi + '/' + this.idParams;
        }

        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
            self.$router.push({
              name: ViewRouter,
              params: {id: self.idParams, message: 'Bản ghi đã được cập nhật!'}
            });
          } else {
            let htmlErrors = __.renderErrorApiHtml(responsesData.data);
            Swal.fire(
              'Thông báo',
              htmlErrors,
              'error'
            )
          }

        }, (error) => {
          console.log(error);
          Swal.fire(
            'Thông báo',
            'Không kết nối được với máy chủ',
            'error'
          )
        });
      },

      onCreateClicked() {
        this.$router.push({name: CreateRouter});
      },
      onBackToList() {
        this.$router.push({name: ListRouter});
      },

      updateModel() {
        if (this.stage.updatedData) {
          this.$forceUpdate();
        }
      },
      getHour() {
        if (this.value.CalendarTypeID && this.value.StartDate && this.value.DueDate) {
          let self = this;
          let urlApi = '/task/api/task/get-hour';
          let requestData = {
            method: 'post',
            url: urlApi,
            data: {
              StartDate: self.value.StartDate,
              DueDate: self.value.DueDate,
              CalendarTypeID: self.value.CalendarTypeID,
            },

          };
          this.$store.commit('isLoading', true);
          ApiService.customRequest(requestData).then((response) => {
            let dataResponse = response.data;

            if (dataResponse.status === 1) {
              this.value.Duration = dataResponse.data;
            }
            self.$store.commit('isLoading', false);
          }, (error) => {
            console.log(error);
            self.$store.commit('isLoading', false);
          });

          // scroll to top perfect scroll
          const container = document.querySelector('.b-table-sticky-header');
          if (container) container.scrollTop = 0;
        }
      },
    },
    watch: {
      idParams() {
        this.fetchData();
      },
      'value.CalendarTypeID'() {
        this.getHour();
      },
      'value.StartDate'() {
        if (__.convertDate(this.value.StartDate) > __.convertDate(this.value.DueDate)) {
          this.value.StartDate = this.value.DueDate;
        }
        this.getHour();
        this.value.statusHour = 1;
      },
      'value.DueDate'() {
        if (__.convertDate(this.value.StartDate) > __.convertDate(this.value.DueDate)) {
          this.value.DueDate = this.value.StartDate;
        }
        if (this.value.statusHour == 0) {
          this.getHour();
        }
      },
      'value.ParentID'() {
        let self = this;
        let urlApi = this.api;
        let requestData = {
          method: 'post',
          url: '/listing/api/common/auto-child',
          data: {
            per_page: 10,
            page: this.currentPage,
            table: 'task',
            ParentID: this.value.ParentID,
          },

        };

        // if (this.valueSearch.CompanyNo.trim() !== '') {
        //     requestData.data.CompanyNo = this.valueSearch.CompanyNo;
        // }

        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let dataResponse = response.data;
          this.value.TaskNo = dataResponse.data;
          self.$store.commit('isLoading', false);
        }, (error) => {
          self.$store.commit('isLoading', false);
        });

      },
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
      per: {}
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
</style>

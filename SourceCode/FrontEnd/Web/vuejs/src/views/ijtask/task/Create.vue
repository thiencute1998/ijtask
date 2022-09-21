<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Công việc<span v-if="model.TaskName">:</span> {{model.TaskName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Công việc<span v-if="model.TaskName">:</span> {{model.TaskName}}</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="handleSubmitForm"><i
                class="fa fa-check-square-o"></i> Lưu
              </b-button>
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onBackToList"><i
                class="fa fa-ban"></i> Hủy
              </b-button>
            </div>
          </b-col>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-icons">
              <div class="main-header-collapse">
                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen=true></sidebar-toggle>
              </div>
            </div>
          </b-col>
        </b-row>
      </div>

    </div>
    <div class="main-body main-body-view-action">
      <vue-perfect-scrollbar class="scroll-area" :settings="$store.state.psSettings">
        <div class="container-fluid">
          <b-card>
            <div class="form-group row align-items-center">
              <label for="task-name" class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2 col-form-label">
                Tên công việc
                <i style="cursor: pointer;" class="fa fa-bullhorn" title="Là thông báo" @click="changeIsNotification" :style="colorIsNotification"></i>
              </label>
              <div class="col-lg-16 col-md-16">
                <input type="text" v-model="model.TaskName" id="task-name" class="form-control" autocomplete="task-name" placeholder="Nhập tên công việc"/>
              </div>
              <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.TaskNo" class="form-control" placeholder="Nhập mã số"/>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label for="task-name" class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2 col-form-label">
                Tên công việc cha
              </label>
              <div class="col-lg-16 col-md-16">
                <IjcoreModalTask v-if="!stage.isNotification" v-model="model" :today="model.ParentID" :title="'Công việc'" :api="'/task/api/task/list-modal'" :table="'task'">
                </IjcoreModalTask>
                <div role="group" class="input-group app-disable" v-if="stage.isNotification">
                  <input type="text" placeholder="Chọn công việc cha" readonly="readonly" class="readonly form-control form-control" id="__BVID__400"/>
                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div>
                </div>
              </div>
              <div class="col-lg-4 col-md-6 col-sm-8 col-24 pl-0  d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.ParentNo" readonly="true"
                       class="readonly form-control"
                       :class="[(stage.isNotification) ? 'app-disable' : '']"
                       placeholder="Nhập mã số"/>
              </div>
            </div>

            <div class="form-group row align-items-center" v-if="stage.isNotification">
              <div class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" title="Loại công việc hoàn thành ngay" style="white-space: nowrap">Loại công việc HT ngay</div>
              <div class="col-lg-6">
                <b-form-select v-model="model.DoneNowType" :options="[{value: 1, text: 'Thông báo'}, {value: 2, text: 'Nhắc nhở'}]"></b-form-select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-md-4" style="white-space: nowrap" for="task-description">Mô tả</label>
              <div class="col-md-20">
                <textarea v-model="model.TaskDescription" class="form-control" id="task-description" rows="3"
                          placeholder="Nhập mô tả" name="task-description"></textarea>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-4" style="white-space: nowrap">Mức độ ưu tiên</label>
              <div class="col-md-4">
                <b-form-select v-model="model.Priority" :options="model.PriorityOptions" id="item-uom"></b-form-select>
              </div>
              <div class="col-md-4" style="white-space: nowrap">Quyền truy cập</div>
              <div class="col-md-4">
                <b-form-select v-model="model.AccessType" :options="model.AccessTypeOptions" id="item-uom"></b-form-select>
              </div>
              <div class="col-md-8" v-if="model.AccessType == 2">
                <b-form-select v-model="model.PublicCompanyID" :options="model.CompanyOptions" id="PublicCompanyID"></b-form-select>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0">Đơn vị tính</label>
              <div class="col-md-4">
                <b-form-select v-model="model.UomID" :options="model.UomOptions" id="UomID"></b-form-select>
              </div>

              <label class="col-md-4 m-0">Kiểu lịch</label>
              <div class="col-md-8">
                <b-form-select v-model="model.CalendarTypeID" :options="model.CalendarOptions" id="CalendarTypeID"></b-form-select>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0">Ngày bắt đầu</label>
              <div class="col-md-8" v-on:mouseover="updateStatusHour()">
                <IjcoreDatePicker v-model="model.StartDate" style="width: 100%;">
                </IjcoreDatePicker>
              </div>

              <label class="col-md-4 m-0">Hạn hoàn thành</label>
              <div class="col-md-8" v-on:mouseover="updateStatusHour()">
                <IjcoreDatePicker v-model="model.DueDate">
                </IjcoreDatePicker>
              </div>

            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-4 m-0">Số giờ ước thực hiện</label>
              <div class="col-md-8">
                <input type="number" v-model="model.Duration" id="Duration" class="form-control" name="Duration" placeholder="Nhập số giờ ước thực hiện"/>
              </div>

              <label class="col-md-4 m-0">KL ước thực hiện</label>
              <div class="col-md-8">
                <input type="number" v-model="model.EstimatedQuantity" id="EstimatedQuantity" class="form-control" name="EstimatedQuantity" placeholder="Nhập khối lượng ước thực hiện"/>
              </div>

            </div>
            <div class="form-group row align-items-center">
              <div class="col-lg-4">Loại trạng thái</div>
              <div class="col-lg-8">
                <b-form-select v-model="model.StatusID" :options="model.StatusOption" @change="onChangeStatus"></b-form-select>
              </div>
              <div class="col-lg-4">Trạng thái</div>
              <div class="col-lg-8">
                <b-form-select v-model="model.StatusValue" :options="model.StatusValueOption | filterStatusValueOption(Number(model.StatusID))"></b-form-select>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <div class="col-lg-4">Loại công việc</div>
              <div class="col-lg-20">
                <task-modal-search-input-tcatelist
                  v-model="model.TaskCate"
                  tableApi="task_cate_list"
                  refModal="myModalSearchTcatelist"
                  id-modal="myModalSearchTcatelist"
                  placeholder="Loại công việc"
                  title-modal="Loại công việc" size-modal="lg"></task-modal-search-input-tcatelist>
              </div>
            </div>

            <div class="form-group row align-items-center">
              <div class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Nhóm công việc</div>
              <div class="col-lg-4">
                <b-form-select v-model="model.Type" :options="[{value: 1, text: 'Độc lập'}, {value: 2, text: 'Quy trình'}]" :disabled="stage.disableWorkflow || stage.isNotification">
                  <template v-slot:first>
                    <b-form-select-option :value="null" disabled>-- Chọn loại công việc --</b-form-select-option>
                  </template>
                </b-form-select>
              </div>
              <div class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" v-if="model.Type === 2" style="white-space: nowrap">Quy trình áp dụng</div>
              <div class="col-lg-12" v-if="model.Type === 2">
                <Select2 v-model="model.WFID" :settings="{allowClear: true, placeholder: {id: null, text: 'Quy trình áp dụng'}, disabled: stage.disableWorkflow}" :options="model.TaskWorkflowOption" @select="onSelectTaskWorkflow($event)"></Select2>
              </div>
            </div>
          </b-card>
        </div>
      </vue-perfect-scrollbar>
    </div>
  </div>
</template>

<script>
  import ApiService from '@/services/api.service';
  import Swal from 'sweetalert2';
  import 'sweetalert2/src/sweetalert2.scss';
  import IjcoreDatePicker from '@/components/IjcoreDatePicker';
  import IjcoreModalTask from '@/components/IjcoreModalTask';
  import IjcoreModalListing from "../../../components/IjcoreModalListing";
  import Select2 from 'v-select2-component';
  import IjcoreSelect2Server from '@/components/IjcoreSelect2Server';
  import {TokenService} from '@/services/storage.service';
  import TaskModalSearchInputTcatelist from "./partials/TaskModalSearchInputTcatelist";

  const ListRouter = 'task-task-list';
  const EditRouter = 'task-task-edit';
  const CreateRouter = 'task-task-create';
  const ViewRouter = 'task-task-view';
  const DetailApi = 'task/api/task/view';
  const EditApi = 'task/api/task/edit';
  const CreateApi = 'task/api/task/create';
  const StoreApi = 'task/api/task/store';
  const UpdateApi = 'task/api/task/update';
  const ListApi = 'task/api/task';
  let today = new Date();
  let dd = String(today.getDate()).padStart(2, '0');
  let mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
  let yyyy = today.getFullYear();

  today = dd + '/' + mm + '/' + yyyy;
  String.prototype.capitalize = function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
  };
  export default {
    name: 'task-detail-task',
    components: {
      IjcoreModalListing,
      IjcoreDatePicker,
      IjcoreModalTask,
      Select2,
      IjcoreSelect2Server,
      TaskModalSearchInputTcatelist
    },
    data() {
      return {
        idParams: this.idParamsProps,
        reqParams: this.reqParamsProps,
        model: {
          TaskID: null,
          TaskNo: '',
          TaskName: '',
          ParentID: '',
          ParentName: '',
          ParentNo: '',
          Priority: 3,
          AccessType: 1,
          PublicCompanyID: '',
          TaskDescription: '',
          UomID: '',
          CalendarTypeID: '',
          StartDate: today,
          DueDate: '',
          Duration: '',
          EstimatedQuantity: '',
          PriorityOptions: [],
          AccessTypeOptions: [],
          CompanyOptions: [],
          CalendarOptions: [],
          UomOptions: [],
          TaskOptions: [],
          statusHour: 0,
          DoneNowType: 0,
          Type: (this.$route.params.WFID) ? 2 : 1,

          TaskCate: [],

          StatusID: null,
          StatusOption: [],

          StatusValue: null,
          StatusValueOption: [],

          WFID: (this.$route.params.WFID) ? this.$route.params.WFID : null,
          TaskWorkflow: [],
          TaskWorkflowOption: [],
          jsonFlowchart: null
        },
        colorIsNotification: 'color: #f3d2d2',
        itemCopy: (this.$route.params.itemCopy) ? this.$route.params.itemCopy : {},
        itemParent: (this.$route.params.itemParent) ? this.$route.params.itemParent : {},
        stage: {
          updatedData: false,
          disableWorkflow: (this.$route.params.WFID) ? true : false,
          isNotification: false
        },
      }

    },
    props: {
      idParamsProps: {
        type: Number,
        default: 0
      },
      reqParamsProps: {
        type: Object,
        default: function () {
          return {}
        }
      },
    },

    beforeCreate() {
    },
    mounted() {
      this.fetchData();
    },
    updated() {
      this.stage.updatedData = true;
    },
    computed: {
      taskNo() {
        let index = 0;
        index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
        return index;
      },
    },
    filters:{
      filterStatusValueOption(value, StatusID){
        if (StatusID) return _.filter(value, ['StatusID', StatusID]);
        return value;
      }
    },
    methods: {
      changeIsNotification() {
        this.stage.isNotification = !this.stage.isNotification;
        let workDate = TokenService.getWorkdate();
        if(this.stage.isNotification){
          this.model.Type = 1;
          this.colorIsNotification = 'color: #00a2e8';
          this.model.DueDate = this.model.StartDate = workDate;
          this.model.DoneNowType = 1;
          this.model.ParentID = null;
          this.model.ParentName = '';
          this.model.ParentNo = '';
        }else{
          this.colorIsNotification = 'color: #f3d2d2';
          this.model.DoneNowType = 0;
        }
      },
      updateStatusHour() {
        this.model.statusHour = 0;
      },
      fetchData() {
        let self = this;
        let urlApi = CreateApi;
        let requestData = {
          method: 'post',
          data: {}
        };
        if (!_.isEmpty(self.itemCopy) && (self.itemCopy.Type === 2)) {
          requestData.data.CopyID = self.itemCopy.TaskID;
        }
        // Api edit user
        if (this.idParams) {
          urlApi = EditApi + '/' + this.idParams;
          requestData.data.id = this.idParams;
        }
        requestData.url = urlApi;
        this.$store.commit('isLoading', true);

        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.model.TaskNo = responsesData.data.auto;
            self.model.PriorityOptions = [];
            _.forEach(responsesData.data.Priority, function (value, key) {
              let tmpObj = {};
              tmpObj.value = key;
              tmpObj.text = value;
              self.model.PriorityOptions.push(tmpObj);
            });

            self.model.AccessTypeOptions = [];
            _.forEach(responsesData.data.AccessType, function (value, key) {
              let tmpObj = {};
              tmpObj.value = key;
              tmpObj.text = value;
              self.model.AccessTypeOptions.push(tmpObj);
            });
            if (_.isArray(responsesData.data.Calendar)) {
              self.model.CalendarOptions = [];
              _.forEach(responsesData.data.Calendar, function (value, key) {
                if (value.isDefault == 1) {
                  self.model.CalendarTypeID = value.CalendarTypeID;
                }
                let tmpObj = {};
                tmpObj.value = value.CalendarTypeID;
                tmpObj.text = value.CalendarName;
                self.model.CalendarOptions.push(tmpObj);
              });
            }
            if (_.isArray(responsesData.data.Uom)) {
              self.model.UomOptions = [
                {value: "", text: '[Chọn đơn vị tính]'}
              ];
              _.forEach(responsesData.data.Uom, function (value, key) {
                let tmpObj = {};
                tmpObj.value = value.UomID;
                tmpObj.text = value.UomName;
                self.model.UomOptions.push(tmpObj);
              });

              // get uom option setting
              let optionSetting = JSON.parse(localStorage.getItem('OptionSetting'));
              if (optionSetting) {
                let defaultUom = _.find(optionSetting, ['SettingKey', 'GLOBAL_UOM']);
                if (defaultUom) {
                  self.model.UomID = Number(defaultUom.SettingValue);
                }
              }

            }

            if (_.isArray(responsesData.data.Company)) {

              self.model.CompanyOptions = [
                {value: "", text: '[Chọn đơn vị truy cập]'}
              ];
              _.forEach(responsesData.data.Company, function (value, key) {
                let tmpObj = {};
                tmpObj.value = value.CompanyID;
                tmpObj.text = value.CompanyName;
                self.model.CompanyOptions.push(tmpObj);
              });
            }

            if (_.isArray(responsesData.data.Status)) {
              self.model.StatusOption = [];
              _.forEach(responsesData.data.Status, function (status, key) {
                let tmpObj = {};
                tmpObj.value = status.StatusID;
                tmpObj.text = status.StatusName;
                self.model.StatusOption.push(tmpObj);
              });

              // TODO: set default option for status
              if (!self.model.StatusID && self.model.StatusOption[0]) {
                self.model.StatusID = self.model.StatusOption[0].value;
              }
            }

            if (responsesData.data.StatusItem) {
              self.model.StatusValueOption = [];
              _.forEach(responsesData.data.StatusItem, function (statusItem, key) {
                let tmpObj = {};
                tmpObj.value = statusItem.StatusValue;
                tmpObj.text = statusItem.StatusDescription;
                tmpObj.StatusID = statusItem.StatusID;
                self.model.StatusValueOption.push(tmpObj);
              });
              self.onChangeStatus();
            }

            // task workflow option
            if (self.model.TaskWorkflowOption) {
              self.model.TaskWorkflowOption = [];
              self.model.TaskWorkflow = responsesData.data.TaskWorkflow;
              _.forEach(responsesData.data.TaskWorkflow, function (workflow, key) {
                let tmpObj = {};
                tmpObj.id = workflow.WFID;
                tmpObj.text = workflow.WFName;
                self.model.TaskWorkflowOption.push(tmpObj);
              });
            }

            if (!_.isEmpty(self.itemCopy)) {
              self.model.TaskName = self.itemCopy.TaskName;
              self.model.ParentID = self.itemCopy.ParentID;
              self.model.ParentName = self.itemCopy.ParentName;
              self.model.ParentNo = self.itemCopy.ParentNo;
              self.model.TaskDescription = self.itemCopy.TaskDescription;
              self.model.Priority = self.itemCopy.Priority;
              self.model.AccessType = self.itemCopy.AccessType;
              self.model.PublicCompanyID = self.itemCopy.PublicCompanyID;
              self.model.UomID = self.itemCopy.UomID;
              self.model.CalendarTypeID = self.itemCopy.CalendarTypeID;
              self.model.StartDate = self.itemCopy.StartDate;
              self.model.DueDate = self.itemCopy.DueDate;
              self.model.Duration = self.itemCopy.Duration;
              self.model.EstimatedQuantity = __.convertNumberToText(self.itemCopy.EstimatedQuantity);
              self.model.statusHour = self.itemCopy.statusHour;
              self.model.DoneNowType = self.itemCopy.DoneNowType;
              self.model.Type = self.itemCopy.Type;
              self.model.StatusID = self.itemCopy.StatusID;
              if (responsesData.data.TaskDataflow) {
                self.model.WFID = responsesData.data.TaskDataflow.WFID;
              }
            }

            if (!_.isEmpty(self.itemParent)) {
              self.model.ParentID = self.itemParent.TaskID;
              self.model.ParentNo = self.itemParent.TaskNo;
              self.model.ParentName = self.itemParent.TaskName;
            }
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      handleSubmitForm() {
        let self = this;
        let StartDate = this.model.StartDate;
        let DueDate = this.model.DueDate;
        if (this.model.StartDate == '__/__/____') {
          StartDate = '';
        }
        if (this.model.DueDate == '__/__/____') {
          DueDate = '';
        }
        let status = _.find(this.model.StatusOption, ['value', Number(this.model.StatusID)]);
        // let statusValue = _.find(this.model.StatusValueOption, ['value', Number(this.model.StatusValue)]);
        let statusValue = _.find(this.model.StatusValueOption, {
          StatusID: this.model.StatusID,
          value: this.model.StatusValue
        });

        if (!status) {
          this.$bvToast.toast('Loại trạng thái không được để trống', {
            title: 'Thông báo',
            variant: 'danger',
            solid: true
          });
          return;
        }

        let requestData = {
          method: 'post',
          url: StoreApi,
          data: {
            TaskNo: this.model.TaskNo,
            TaskName: this.model.TaskName,
            ParentID: this.model.ParentID,
            ParentName: this.model.ParentName,
            ParentNo: this.model.ParentNo,
            Priority: this.model.Priority,
            AccessType: this.model.AccessType,
            PublicCompanyID: this.model.PublicCompanyID,
            TaskDescription: this.model.TaskDescription,
            UomID: this.model.UomID,
            CalendarTypeID: this.model.CalendarTypeID,
            StartDate: StartDate,
            DueDate: DueDate,
            Duration: this.model.Duration,
            EstimatedQuantity: this.model.EstimatedQuantity,
            DoneNowType: this.model.DoneNowType,
            Type: this.model.Type,
            WFID: this.model.WFID,
            StatusID: this.model.StatusID,
            StatusName: (status) ? status.text : '',
            StatusValue: (statusValue) ? statusValue.value : null,
            StatusDescription: (statusValue) ? statusValue.text : ''
          }
        };

        if (this.model.TaskCate && this.model.TaskCate.length) {
          requestData.data.TaskCate = this.model.TaskCate;
        }

        if (!_.isEmpty(this.itemCopy)) {
          requestData.data.CopyID = this.itemCopy.TaskID;
        }

        // edit user
        if (this.idParams) {
          requestData.data.ItemID = this.idParams;
          requestData.url = UpdateApi + '/' + this.idParams;
        }
        self.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            if (self.idParams) {
              self.$_storeTaskNotice(self.idParams, 'edit');
            }

            if (!self.idParams && responsesData.data) {
              self.idParams = responsesData.data;
              self.$_storeTaskNotice(responsesData.data, 'addnew');
            }

            if (responsesData.IsDataflow) {
              self.$_storeTaskDataflowNotice(responsesData.data, 'addnew');
            }

            if (self.stage.disableWorkflow) {
              self.$router.push({
                name: 'task-dataflow',
                params: {WFID: self.model.WFID}
              });
            } else {
              self.$router.push({
                name: ViewRouter,
                params: {id: self.idParams, message: 'Bản ghi đã được cập nhật!'}
              });
            }

          } else {
            let htmlErrors = __.renderErrorApiHtml(responsesData.data);
            Swal.fire({
              icon: 'error',
              title: 'Thông báo',
              text: htmlErrors,
              confirmButtonText: 'Đóng'
            })
          }

          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          Swal.fire(
            'Thông báo',
            'Không kết nối được với máy chủ',
            'error'
          )
          self.$store.commit('isLoading', false);
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
        if (this.model.CalendarTypeID && this.model.StartDate && this.model.DueDate) {
          let self = this;
          let urlApi = '/task/api/task/get-hour';
          let requestData = {
            method: 'post',
            url: urlApi,
            data: {
              StartDate: self.model.StartDate,
              DueDate: self.model.DueDate,
              CalendarTypeID: self.model.CalendarTypeID,
            },

          };
          this.$store.commit('isLoading', true);
          ApiService.customRequest(requestData).then((response) => {
            let dataResponse = response.data;

            if (dataResponse.status === 1) {
              this.model.Duration = dataResponse.data;
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
      onSelectTaskWorkflow(data){
        let workflow = _.find(this.model.TaskWorkflow, ['WFID', Number(this.model.WFID)]);
        if (workflow) {
          this.model.jsonFlowchart = workflow.JsonFlowchart;
        }
      },
      onChangeStatus() {
        let statusValueOption = _.filter(this.model.StatusValueOption, ['StatusID', this.model.StatusID]);
        if (statusValueOption && statusValueOption.length) {
          this.model.StatusValue = statusValueOption[0].value;
        }
      },
      getStatusValue() {}
    },
    watch: {
      idParams() {
        this.fetchData();
      },
      'model.CalendarTypeID'() {
        this.getHour();
      },
      'model.StartDate'() {
        if (__.convertDate(this.model.StartDate) > __.convertDate(this.model.DueDate)) {
          this.model.StartDate = this.model.DueDate;
        }
        this.getHour();
        this.model.statusHour = 1;
      },
      'model.DueDate'() {
        if (__.convertDate(this.model.StartDate) > __.convertDate(this.model.DueDate)) {
          this.model.DueDate = this.model.StartDate;
        }
        if (this.model.statusHour == 0) {
          this.getHour();
        }
      },
      'model.ParentID'() {
        let self = this;
        let urlApi = this.api;
        let requestData = {
          method: 'post',
          url: '/listing/api/common/auto-child',
          data: {
            per_page: 10,
            page: this.currentPage,
            table: 'task',
            ParentID: this.model.ParentID,
          },

        };

        // if (this.modelSearch.CompanyNo.trim() !== '') {
        //     requestData.data.CompanyNo = this.modelSearch.CompanyNo;
        // }

        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let dataResponse = response.data;
          this.model.TaskNo = dataResponse.data;
          self.$store.commit('isLoading', false);
        }, (error) => {
          self.$store.commit('isLoading', false);
        });

      },
    }
  }
</script>

<style lang="css">
  .btn-footer {
    margin-right: 10px;
    color: #333;
    background: #e9ebec;
  }

  .btn-footer:hover {
    color: #333;
    background: #e9ebec;
  }
  .task-indicator .select2-container {
    width: 100% !important;
  }
</style>

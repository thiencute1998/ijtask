<template>
    <div class="main-entry component-workflow">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                  <b-col class="col-md-18">
                    <div class="main-header-item main-header-name">
                      <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Quy trình công việc<span v-if="model.WFName">:</span> {{model.WFName}}</span>
                      <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Quy trình công việc<span v-if="model.WFName">:</span> {{model.WFName}}</span>
                    </div>
                  </b-col>
                  <b-col class="col-md-6"></b-col>
                </b-row>
                <b-row>
                  <b-col class="col-md-12">
                    <div class="main-header-item main-header-actions">
                      <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="handleSubmitForm"><i class="fa fa-check-square-o"></i> Lưu</b-button>
<!--                      <b-button v-if="idParams" type="submit" variant="primary" class="main-header-action mr-2" @click="handleSubmitFormSave()"><i class="fa fa-check-square-o"></i> Lưu</b-button>-->
                      <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onBackToList"><i class="fa fa-ban"></i> Hủy</b-button>
                    </div>
                  </b-col>
                  <b-col class="col-md-12">
                    <div class="main-header-item main-header-icons">
                      <div class="main-header-collapse">
                        <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen=true />
                      </div>
                    </div>
                  </b-col>
                </b-row>
            </div>
        </div>
        <div class="main-body main-body-view-action">
<!--          <vue-perfect-scrollbar class="scroll-area" :settings="$store.state.psSettings">-->
            <div class="container-fluid">
              <b-card>
                <div class="form-group row align-items-center">
                  <div class="col-lg-2 col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên</div>
                  <div class="col-lg-18 col-md-16 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
                      <input v-model="model.WFName" type="text" class="form-control" placeholder="Tên quy trình công việc"/>
                  </div>
                  <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                    <span>Mã số</span>
                    <input type="text" v-model="model.WFNo" class="form-control" placeholder="Mã số"/>
                  </div>
                </div>

                <div class="workflow-view mb-3">
                  <b-card body-class="p-0">
                    <b-tabs card>
                      <b-tab title="Công việc" @click="onClickTabWorkflowItem" active>
                        <b-card-text>
                          <div class="table-responsive">
                            <table class="table b-table table-sm table-bordered table-editable table-column-resizable">
                              <thead>
                              <tr>
                                <th scope="col" class="text-center" style="width: 3%">STT</th>
                                <th scope="col" class="text-center" style="width: 14%; min-width: 100px">Loại chức năng</th>
                                <th scope="col" class="text-center" style="width: 20%; min-width: 200px">Chức năng</th>
                                <th scope="col" class="text-center" style="width: 40%; min-width: 200px">Diễn giải</th>
                                <th scope="col" class="text-center" style="width: 20%; min-width: 150px">Loại trạng thái</th>
<!--                                <th scope="col" class="text-center" style="width: 15%; min-width: 150px">Người thực hiện</th>-->
                                <th scope="col" class="text-center" style="width: 3%">
                                  <i class="fa fa-link" title="Ràng buộc" style="font-size: 18px; cursor: pointer;"></i>
                                </th>
                                <th scope="col" class="text-center" style="width: 3%; min-width: 40px">
<!--                                    <i @click="onAddFieldOnTable()" class="fa fa-plus" title="Thêm chức năng" style="cursor: pointer; font-size: 16px; padding: 0 5px"></i>-->
                                </th>
                              </tr>
                              </thead>
                              <draggable v-model="model.WorkflowItem" tag="tbody" draggable=".draggable">
                                  <tr :class="[(key !== 0) ? 'draggable' : '']" v-for="(field, key) in model.WorkflowItem">
                                    <td class="text-center" :style="[(key === 0) ? {cursor: 'default'} : {cursor: 'move'}]">
                                      <span>{{key + 1}}</span>
                                    </td>

                                     <td>
                                        <b-form-select
                                          v-if="key !== 0"
                                          :options="model.ProcessTypeOption"
                                          v-model="model.WorkflowItem[key].ProcessType"
                                          @input="onSelectProcessType($event, key)">
                                        </b-form-select>
                                        <div style="padding: 0.275rem 1.75rem 0.275rem 0.75rem;" v-if="key === 0">{{model.ProcessType[4]}}</div>
                                     </td>
                                    <td style="width: 15%">
                                      <Select2
                                              v-if="model.WorkflowItem[key].ProcessType == 1"
                                              v-model="model.WorkflowItem[key].FeatureID"
                                              :options="model.featureOption"
                                              :settings="{allowClear: true, placeholder: {id: 0, text: '-- Chọn chức năng --'}}"
                                              @select="onSelectedFeature($event, key)"></Select2>
                                      <div class="disabled" v-else>
                                        <b-form-input disabled style="border-radius: unset"></b-form-input>
                                      </div>
                                    </td>
                                    <td>
                                      <b-form-input v-if="key !== 0" type="text" v-model="model.WorkflowItem[key].WFItemName" @blur="renderWorkflowItemOption"></b-form-input>
                                      <span class="ml-2" v-if="key === 0">{{model.WorkflowItem[key].WFItemName}}</span>
                                    </td>

                                    <td>
                                      <Select2
                                        v-if="(key !== 0 && field.ProcessType !== 5)"
                                        v-model="model.WorkflowItem[key].FeatureStatusID"
                                        :options="model.SysFeatureStatusOption | filterFeatureStatus(field.FeatureID)"
                                        :settings="{allowClear: true, placeholder: {id: 0, text: 'Chọn trạng thái'}}">
                                      </Select2>
                                    </td>
<!--                                    <td>-->
<!--                                      <ijcore-users-icon :all-users="model.allEmployee" filter-name="EmployeeID" :filter-value="model.WorkflowItem[key].Employee" :main-responsible-person="model.WorkflowItem[key].ResponseEmployee" :number="5"></ijcore-users-icon>-->
<!--                                    </td>-->
                                    <td class="text-center">
                                      <div v-if="key !== 0" title="Thêm ràng buộc" @click="onToggleModal(field)" style="cursor: pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg>
                                      </div>
                                    </td>
                                    <td class="text-center">
                                      <i v-if="key !== 0" @click="onDeleteFieldOnTable(field)" class="fa fa-trash-o" title="Xóa" style="font-size: 18px; cursor: pointer"></i>
<!--                                      <i v-if="key !== 0 && idParams" @click="handleSubmitFormSave(key)" class="fa fa-floppy-o" title="Lưu" style="font-size: 18px; cursor: pointer"></i>-->
                                    </td>
                                  </tr>
                                </draggable>
                            </table>
                            <a @click="onAddFieldOnTable()" class="new-row">
                              <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
                            </a>
                          </div>
                        </b-card-text>
                      </b-tab>
                      <b-tab title="Sơ đồ" @click="onClickTabFlowchart">
                        <b-card-text>
                          <div class="workflow-design" style="min-height: 300px;">
                            <ijcore-flowchart ref="flowchart" :employee-option="model.employeeOption" :sys-feature-status="model.SysFeatureStatus" :is-draggable="true" v-if="stage.showFlowchart" v-model="model.JsonFlowchart"></ijcore-flowchart>
                          </div>
                        </b-card-text>
                      </b-tab>
                    </b-tabs>
                  </b-card>
                </div>
              </b-card>
            </div>
<!--          </vue-perfect-scrollbar>-->
        </div>

<!--      <ijcore-select2 :options="[{ id: 1, text: 'Hello' }, { id: 2, text: 'World' }]" v-model="model.nodeSelected"></ijcore-select2>-->
<!--      <ijcore-select2-->
<!--        :options="model.featureOption"-->
<!--        v-model="test"-->
<!--        :settings="{allowClear: true, placeholder: {id: 0, text: 'Chọn chức năng'}}"-->
<!--      ></ijcore-select2>-->

      <b-modal ref="modal-flowchart" id="modal-flowchart" ok-title="Lưu" cancel-title="Hủy" size="lg" @ok="onSaveModalConstraint" @cancel="onCancelModalConstraint($event)" @hide="onHideModalConstraint($event)">
        <template slot="modal-title">
          Tạo ràng buộc: {{getNameWorkflowItemSelected()}}
        </template>
        <table class="table b-table table-sm table-bordered table-editable">
          <thead>
          <tr>
            <th scope="col" class="text-center" style="width: 25%">Chức năng trước</th>
<!--            <th scope="col" class="text-center" style="width: 20%" title="Giá trị chức năng trước">Giá trị</th>-->
            <th scope="col" class="text-center" style="width: 20%" title="Mô tả liên kết">Mô tả</th>
            <th scope="col" class="text-center" style="width: 20%" title="Ràng buộc liên kết">Ràng buộc</th>
            <th scope="col" class="text-center" style="width: 6%; min-width: 80px">Hướng bắt đầu</th>
            <th scope="col" class="text-center" style="width: 6%; min-width: 80px">Hướng kết thúc</th>
            <th scope="col" class="text-center" style="width: 3%; min-width: 40px"></th>
          </tr>
          </thead>
          <tbody>
            <tr v-if="constraint.WFItemID == model.WFItemIDSelected" v-for="(constraint, key) in model.WorkflowConstraint">
              <td>
                <Select2
                  v-model="model.WorkflowConstraint[key].WFPreItemID"
                  @select="onSelectedWFPreItem($event, key)"
                  :options="model.WorkflowItemOption"
                  :settings="{allowClear: true, minimumResultsForSearch: -1, placeholder: {id: 0, text: 'Chọn chức năng'}}">
                </Select2>

<!--                <ijcore-select2-->
<!--                  :options="model.WorkflowItemOption"-->
<!--                  v-model="model.WorkflowConstraint[key].WFPreItemID"-->
<!--                  :settings="{allowClear: true, placeholder: {id: 0, text: 'Chọn chức năng'}}"-->
<!--                ></ijcore-select2>-->
              </td>
<!--              <td>-->
<!--                <b-form-input type="text" v-model="model.WorkflowConstraint[key].WFPreItemValue"></b-form-input>-->
<!--              </td>-->
              <td>
                <b-form-input type="text" v-model="model.WorkflowConstraint[key].ConstraintLabel"></b-form-input>
              </td>
              <td>
                <b-form-select
                  :options="model.ConstraintConditionOption"
                  v-model="model.WorkflowConstraint[key].ConstraintCondition">
                </b-form-select>
              </td>
              <td>
                <b-form-select
                  :options="[{value: 1, text: 'Trên'},{value: 2, text: 'Dưới'}, {value: 3, text: 'Trái'}, {value: 4, text: 'Phải'}]"
                  v-model="model.WorkflowConstraint[key].SourceAnchors">
                </b-form-select>
              </td>
              <td>
                <b-form-select
                  :options="[{value: 1, text: 'Trên'},{value: 2, text: 'Dưới'}, {value: 3, text: 'Trái'}, {value: 4, text: 'Phải'}]"
                  v-model="model.WorkflowConstraint[key].TargetAnchors">
                </b-form-select>
              </td>
              <td class="text-center">
                <i @click="onDeleteConstraintOnField(constraint)" class="fa fa-trash-o" title="Xóa" style="font-size: 18px; cursor: pointer"></i>
              </td>
          </tr>
          </tbody>
        </table>
        <a @click="onAddConstraintOfField()" class="new-row">
          <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
        </a>

          <template v-slot:modal-footer="{ ok, cancel, hide }">
              <!-- Emulate built in modal footer ok and cancel button actions -->
<!--              <b-button class="mr-2" variant="primary" @click="ok()">-->
<!--                  Lưu-->
<!--              </b-button>-->
              <b-button variant="primary" @click="hide()">
                  Đóng
              </b-button>
          </template>

      </b-modal>
    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import Swal from 'sweetalert2';
    import 'sweetalert2/src/sweetalert2.scss';
    import MaskedInput from 'vue-text-mask';
    import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
    import Select2 from 'v-select2-component';
    import IjcoreFlowchart from '@/components/IjcoreFlowchart';
    import {PermissionService} from "@/services/permission.service";
    import draggable from 'vuedraggable';
    import ColumnResizer from 'column-resizer';
    import IjcoreUsersIcon from '@/components/IjcoreUsersIcon';

    const ListRouter = 'sysadmin-workflow';
    const EditRouter = 'sysadmin-workflow-edit';
    const CreateRouter = 'sysadmin-workflow-create';
    const ViewRouter = 'sysadmin-workflow-view';
    const ViewApi = 'sysadmin/api/workflow/view';
    const EditApi = 'sysadmin/api/workflow/edit';
    const CreateApi = 'sysadmin/api/workflow/create';
    const StoreApi = 'sysadmin/api/workflow/store';
    const UpdateApi = 'sysadmin/api/workflow/update';
    const ListApi = 'sysadmin/api/workflow';

    const dataTypeOption = {
        1: 'Số',
        2: 'Kí tự',
        3: 'Ngày',
        4: 'Ngày giờ',
        5: 'Có/Không',
        6: 'Đúng/Sai'
    };

    const Permission = PermissionService.getPermission();

    const DataTypeOption = [
      {value: 1, text: 'Số'},
      {value: 2, text: 'Kí tự'},
      {value: 3, text: 'Ngày'},
      {value: 4, text: 'Ngày giờ'},
      {value: 5, text: 'Có/Không'},
      {value: 6, text: 'Đúng/Sai'}
    ];

    export default {
      name: 'sysadmin-tcatelist-view',
      data () {
        return {
          test: 1,
          idParams: this.idParamsProps,
          reqParams: this.reqParamsProps,
          model: {
            WFName: '',
            WFNo: '',
            Prefix: '',
            Suffix: '',
            JsonFlowchart: {
              node: [],
              constraint: []
            },
            Inactive: '',
            ConstraintType: {},
            ConstraintTypeOption: [],
            ProcessType: {},
            ProcessTypeOption: [],
            WorkflowItem: [],
            WorkflowItemOption: [],
            ConstraintCondition: {},
            ConstraintConditionOption: [],

            WorkflowConstraint: [],
            WFItemIDSelected: 0,
            resetWorkflowConstraint: [],

            SysFeatureStatus: [],
            SysFeatureStatusOption: [],

            allEmployee: [],
            employeeOption: [],
            // workflowEmployee: [],

            feature: [],
            featureOption: [],
            maxItemID: 0,
            maxConstraintID: 0,
          },
          stage: {
            updatedData: false,
            showFlowchart: false,

            countConstraint: 0,
            modalShow: false,
            tabActive: this.tabActive,
            isRenderFlowchart: false
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
        itemCopy: {
          type: Object,
          default: function () {
            return {}
          }
        },
        tabActive: {
          type: Number,
          default: 1
        }
      },

      components: {
        MaskedInput,
        Select2,
        IjcoreFlowchart,
        draggable,
        IjcoreUsersIcon
      },
      beforeCreate() {},
      mounted() {
        this.fetchData();
        // if (document.querySelector('.table-column-resizable')) {
        //   new ColumnResizer(
        //       document.querySelector('.table-column-resizable'),{resizeMode: 'overflow'}
        //   );
        // }
        if (!this.model.WorkflowItem.length) {
          this.onAddFieldOnTable();
        }
      },
      updated() {
        this.stage.updatedData = true;
      },
      computed: {
        itemNo(){
          let index = 0;
          index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
          return index;
        },
        dataTypeOption(){
          return DataTypeOption;
        },

      },
      filters:{
        filterFeatureStatus(value, FeatureID){
          if (FeatureID) {
            let status = _.filter(value, ['FeatureID', FeatureID]);
            if (status && status.length) {
              return status;
            }
          }
          return value;
        }
      },
      methods: {
        onDebugger(){
          return this.model.SysFeatureStatusOption;
        },
        getNameWorkflowItemSelected(){
          let itemSelected = _.find(this.model.WorkflowItem, ['WFItemID', this.model.WFItemIDSelected]);
          if (itemSelected && itemSelected.ProcessType == '3' && _.isEmpty(itemSelected.WFItemName)) {
            return 'Chức năng điều kiện';
          }
          if (itemSelected) return __.stripHtml(itemSelected.WFItemName);
          return '';
        },
        fetchData() {
          let self = this;
          let urlApi = CreateApi;
          let requestData = {
              method: 'get',
              data: {}
          };
          // Api edit user
          if(this.idParams){
              urlApi = EditApi + '/' + this.idParams;
              requestData.data.id = this.idParams;
          }
          requestData.url = urlApi;
          this.$store.commit('isLoading', true);

          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            let responsesData = responses.data;

            // copy item
            if (!self.idParams && !_.isEmpty(self.itemCopy)) {
              responsesData.data.data = self.itemCopy.data.data;
              responsesData.data.feature = self.itemCopy.data.feature;
              responsesData.data.ConstraintType = self.itemCopy.data.ConstraintType;
              responsesData.data.ProcessType = self.itemCopy.data.ProcessType;
              if (self.itemCopy.data.WorkflowItem) self.model.WorkflowItem = self.itemCopy.data.WorkflowItem;
              if (self.itemCopy.data.WorkflowConstraint) self.model.WorkflowConstraint = self.itemCopy.data.WorkflowConstraint;
              if (self.itemCopy.data.SysFeatureStatus) self.model.SysFeatureStatus = self.itemCopy.data.SysFeatureStatus;
            }
            if (responsesData.status === 1) {
              self.model.WFName = responsesData.data.data.WFName;
              self.model.WFNo = responsesData.data.data.WFNo;
              if (responsesData.data.data.JsonFlowchart) {
                let jsonFlowchart = JSON.parse(responsesData.data.data.JsonFlowchart);
                if (_.isObject(jsonFlowchart)) {
                  if (jsonFlowchart.node) self.model.JsonFlowchart.node = jsonFlowchart.node;
                  if (jsonFlowchart.constraint) self.model.JsonFlowchart.constraint = jsonFlowchart.constraint;
                }
              }
              self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;
              self.model.feature = responsesData.data.feature;
              self.model.ConstraintType = responsesData.data.ConstraintType;
              self.model.ProcessType = responsesData.data.ProcessType;
              self.model.ConstraintCondition = responsesData.data.ConstraintCondition;
              if (responsesData.data.WorkflowItem) self.model.WorkflowItem = responsesData.data.WorkflowItem;
              if (responsesData.data.WorkflowConstraint) self.model.WorkflowConstraint = responsesData.data.WorkflowConstraint;
              if (responsesData.data.SysFeatureStatus) self.model.SysFeatureStatus = responsesData.data.SysFeatureStatus;
              // self.model.WorkflowConstraint = [];

                // set task_workflow_employee
                if (responsesData.data.Employee) {
                    self.model.allEmployee = responsesData.data.Employee;
                    self.model.employeeOption = [];
                    _.forEach(self.model.allEmployee, function (employee, key) {
                        let tmpObj = {};
                        tmpObj.id = employee.EmployeeID;
                        tmpObj.text = employee.EmployeeName;
                        self.model.employeeOption.push(tmpObj);
                    });
                }
                // if (responsesData.data.WorkflowEmployee) {
                //     self.model.workflowEmployee = responsesData.data.WorkflowEmployee;
                // }

              // set mã quy trình công việc
              if (!self.idParams && responsesData.data.auto) {
                  self.model.WFNo = responsesData.data.auto;
              }

              self.model.featureOption = [];
              _.forEach(self.model.feature, function (feature, key) {
                let tmpObj = {};
                tmpObj.id = feature.FeatureID;
                tmpObj.text = feature.FeatureName;
                tmpObj.FeatureKey = feature.FeatureKey;
                self.model.featureOption.push(tmpObj);
              });

              // set LineIDTemp and start node and end node
              _.forEach(self.model.WorkflowItem, function (item, key) {
                if (!item.LineIDTemp) self.model.WorkflowItem[key].LineIDTemp = item.WFItemID;
                self.model.WorkflowItem[key].IsTaskFeature = (item.IsTaskFeature == 1) ? true : false;
                // start node
                if (key === 0) {
                  self.model.WorkflowItem[key].FeatureID = null;
                  self.model.WorkflowItem[key].WFItemName = 'Bắt đầu';
                  self.model.WorkflowItem[key].ProcessType = 4;
                }
                // if (key === (self.model.WorkflowItem.length - 1)) {
                //   self.model.WorkflowItem[key].FeatureID = null;
                //   self.model.WorkflowItem[key].WFItemName = 'Kết thúc';
                //   self.model.WorkflowItem[key].ProcessType = 1;
                // }
              });

              // update WFItemID for Node JsonFlowchart
              _.forEach(self.model.JsonFlowchart.node, function (node, key) {
                // let workFlowItem = _.find(self.model.WorkflowItem, ['LineIDTemp', node.WFItemID]);
                // console.log(node);
                // console.log(workFlowItem);
                // if (workFlowItem) self.model.JsonFlowchart.node[key].WFItemID = workFlowItem.WFItemID;

                // update key for node condition and
                // let WFItemIDAnd = Number(node.WFItemID) - 6000;
                // let workflowItemAnd = _.find(self.model.WorkflowItem, ['LineIDTemp', WFItemIDAnd]);
                // if (workflowItemAnd) {
                //   self.model.JsonFlowchart.node[key].WFItemID = Number(workflowItemAnd.LineIDTemp) + 6000;
                // }

                // update key for node condition or
                // let WFItemIDOr = Number(node.WFItemID) - 8000;
                // let workflowItemOr = _.find(self.model.WorkflowItem, ['LineIDTemp', WFItemIDOr]);
                // if (workflowItemOr) {
                //   self.model.JsonFlowchart.node[key].WFItemID = Number(workflowItemOr.LineIDTemp) + 8000;
                // }

                // update employee
                let _indexWorkflowItem = _.findIndex(self.model.WorkflowItem, ['LineIDTemp', node.LineIDTemp]);
                if (_indexWorkflowItem > 0) {
                  if (node.Employee) self.model.WorkflowItem[_indexWorkflowItem].Employee = node.Employee;
                  if (node.ResponseEmployee) self.model.WorkflowItem[_indexWorkflowItem].ResponseEmployee = node.ResponseEmployee;
                }
              });

              _.forEach(self.model.WorkflowConstraint, function (constraint, key) {
                if (Number(constraint.LineID) > self.model.maxConstraintID) self.model.maxConstraintID = Number(constraint.LineID);
              });


              // set options task workflow item
              this.renderWorkflowItemOption();

              // set options constraint type
              self.model.ConstraintTypeOption = [];
              _.forEach(self.model.ConstraintType, function (constraint, key) {
                let tmpObj = {};
                tmpObj.value = key;
                tmpObj.text = constraint;
                self.model.ConstraintTypeOption.push(tmpObj);
              });

              // set options ProcessType
              self.updateProcessTypeOption();

              // set options ConstraintCondition
              self.model.ConstraintConditionOption = [];
              _.forEach(self.model.ConstraintCondition, function (condition, key) {
                let tmpObj = {};
                tmpObj.value = key;
                tmpObj.text = condition;
                self.model.ConstraintConditionOption.push(tmpObj);
              });

              // set option SysFeatureStatus
              this.renderSysFeatureStatusOption();
            }
            self.$store.commit('isLoading', false);
          }, (error) => {
            console.log(error);
            self.$store.commit('isLoading', false);
          });
        },
        onToggleModal(field){
          let self = this;
          this.stage.countConstraint = 0;
          this.model.WFItemIDSelected = field.WFItemID;

          this.model.resetWorkflowConstraint = _.filter(this.model.WorkflowConstraint, ['WFItemID', this.model.WFItemIDSelected]);
          if (!this.model.resetWorkflowConstraint.length) {
            this.onAddConstraintOfField();
          }
          // disable Task workflow item
          this.updateWorkflowItemOption();
          this.$refs['modal-flowchart'].show();
        },
        onSaveModalConstraint(){
          this.$bvToast.toast('Đã lưu ràng buộc', {
            title: 'Thông báo',
            variant: 'success',
            solid: true
          });
        },
        onCancelModalConstraint(e){
          this.onResetModalConstraint();
          e.preventDefault();
        },
        onResetModalConstraint(){
          let self = this;
          _.remove(this.model.WorkflowConstraint, ['WFItemID', this.model.WFItemIDSelected]);
          _.forEach(this.model.resetWorkflowConstraint, function (constraint, key) {
            self.model.WorkflowConstraint.push(constraint);
          });
        },
        onHideModalConstraint(e){
          if (e.trigger === 'backdrop' || e.trigger === 'headerclose') {
            this.onResetModalConstraint();
          }
        },
        renderWorkflowItemOption(){
          let self = this, countCondition = 1;
          this.model.WorkflowItemOption = [];
          _.forEach(this.model.WorkflowItem, function (item, key) {
            // set max item id
            if (Number(item.WFItemID) > self.model.maxItemID) self.model.maxItemID = Number(item.WFItemID);
            let tmpObj = {};
            tmpObj.id = item.WFItemID;
            tmpObj.text = item.WFItemName;
            if (item.WFItemName && item.WFItemName !== '' && item.ProcessType != 5) self.model.WorkflowItemOption.push(tmpObj);
            if (item.ProcessType == '3' &&  _.isEmpty(item.WFItemName)) {
              let stt = key + 1;
              tmpObj.text = 'Điều kiện (STT: ' + stt + ')';
              countCondition++;
              self.model.WorkflowItemOption.push(tmpObj);
            }

          });
        },
        renderSysFeatureStatusOption(FeatureID = null){
          let sysFeatureStatusList = this.model.SysFeatureStatus;
          let self = this;
          if (FeatureID !== null) {
            sysFeatureStatusList = _.filter(this.model.SysFeatureStatus, ['FeatureID', FeatureID]);
          }

          this.model.SysFeatureStatusOption = [];
          _.forEach(sysFeatureStatusList, function (status, key) {
            let tmpObj = {};
            tmpObj.id = status.StatusID;
            tmpObj.text = status.StatusName;
            tmpObj.FeatureID = status.FeatureID;
            self.model.SysFeatureStatusOption.push(tmpObj);
          });

          return self.model.SysFeatureStatusOption;
        },
        updateWorkflowItemOption(){
          let self = this;
          let workflowItemSelected = _.find(this.model.WorkflowItem, ['WFItemID', this.model.WFItemIDSelected]);
          _.forEach(this.model.WorkflowItemOption, function (item, key) {
            // validate không lặp ràng buộc
            let checkExist = _.find(self.model.WorkflowConstraint, {
              WFItemID: self.model.WFItemIDSelected,
              WFPreItemID: item.id
            });

            if (_.isObject(checkExist)) {
              self.model.WorkflowItemOption[key].disabled = true;
            } else {
              self.model.WorkflowItemOption[key].disabled = false;
            }

            //validate dữ liệu cho chức năng điều kiện
            let workflowItem = _.find(self.model.WorkflowItem, ['WFItemID', Number(item.id)]);
            if (workflowItem.ProcessType == 3) {
              let allConstraintCondition = _.filter(self.model.WorkflowConstraint, ['WFPreItemID', Number(workflowItem.WFItemID)]);
              if (allConstraintCondition.length > 1) {
                self.model.WorkflowItemOption[key].disabled = true;
              } else {
                if (!_.isObject(checkExist)) self.model.WorkflowItemOption[key].disabled = false;
              }
            }

            // TODO: validate điều kiện cho chức năng kết thúc - kiểu tra nếu có điều kiện and hoặc or thì disable
            // if (workflowItemSelected.ProcessType == 5) {
            //   let isDisabled = false;
            //   let allConstraintOfItem = _.filter(self.model.WorkflowConstraint, ['WFPreItemID', Number(item.id)]);
            //   console.log(allConstraintOfItem);
            //   _.forEach(allConstraintOfItem, function (constraint, key) {
            //     if (constraint.ConstraintCondition == 1 || constraint.ConstraintCondition == 2) {
            //       // isDisabled = true;
            //       return false;
            //     }
            //   });
            //   if (isDisabled) {
            //     self.model.WorkflowItemOption[key].disabled = true;
            //   } else {
            //     self.model.WorkflowItemOption[key].disabled = false;
            //   }
            // }

          });

          // validate điều kiện cho chức năng bắt đầu
          let allConstraintWorkflowItem = _.filter(this.model.WorkflowConstraint, ['WFItemID', this.model.WFItemIDSelected]);
          if (allConstraintWorkflowItem.length > 1) {
            let startWorkflowItem = _.find(this.model.WorkflowItem, ['ProcessType', 4]);
            let _indexOfStart = _.findIndex(this.model.WorkflowItemOption, ['id', startWorkflowItem.WFItemID]);
            if (this.model.WorkflowItemOption[_indexOfStart]) this.model.WorkflowItemOption[_indexOfStart].disabled = true;
          }


          let taskWorkflowItemOption = this.model.WorkflowItemOption;
          this.model.WorkflowItemOption = [];
          _.forEach(taskWorkflowItemOption, function (option, key) {
            self.model.WorkflowItemOption.push(option);
          });
          this.$forceUpdate();

        },
        updateProcessTypeOption(){
          let self = this;
          this.model.ProcessTypeOption = [];
          _.forEach(this.model.ProcessType, function (process, key) {
            let tmpObj = {};
            tmpObj.value = key;
            tmpObj.text = process;
            if (key == 4) {
              let startNode = _.filter(self.model.WorkflowItem, ['ProcessType', 4]);
              if (startNode.length) tmpObj.disabled = true;
            }
            self.model.ProcessTypeOption.push(tmpObj);
          });

        },

        /**
         * @desc - NodeType :
         *          + 1 ~ nút thuộc loại bình thường
         *          + 2 ~ nút thuộc loại and
         *          + 3 ~ nút thuộc loại or
         *          + 4 ~ nút thuộc loại rẽ nhánh
         *
         */
        renderJsonFlowchart(){
          let self = this, jsonFlowchart = this.model.JsonFlowchart, positionX = 0;
          this.model.JsonFlowchart = {
            node: [],
            constraint: []
          };

          // render constraint
          _.forEach(this.model.WorkflowConstraint, function (constraint, key) {
            let tmpObj = constraint;
            tmpObj.WFItemID = Number(constraint.WFItemID);
            tmpObj.WFPreItemID = Number(constraint.WFPreItemID);
            tmpObj.ConstraintCondition = Number(constraint.ConstraintCondition);
            tmpObj.SourceAnchors = Number(constraint.SourceAnchors);
            tmpObj.TargetAnchors = Number(constraint.TargetAnchors);
            self.model.JsonFlowchart.constraint.push(tmpObj);
          });

          //render node
          _.forEach(self.model.WorkflowItem, function (item, key) {
            let tmpObj = {};
            tmpObj.WFItemID = Number(item.WFItemID);
            tmpObj.WFItemName = item.WFItemName;
            tmpObj.LineIDTemp = item.LineIDTemp;
            tmpObj.FeatureID = Number(item.FeatureID);
            tmpObj.ProcessType = Number(item.ProcessType);
            tmpObj.STT = key + 1;
            tmpObj.NodeID = 'flowchart-window-' + item.WFItemID;
            tmpObj.NodeConnect = '-window-' + item.WFItemID;
            tmpObj.NodeType = 1;
            if (item.FeatureStatusID) tmpObj.FeatureStatusID = item.FeatureStatusID;

              // let employeeIDs = [];
              // let responseEmployeeID = null;
              // let workflowItemEmployee = _.filter(self.model.workflowEmployee, ['WFItemID', item.WFItemID]);
              // _.forEach(workflowItemEmployee, function (employee, key) {
              //     employeeIDs.push(employee.EmployeeID);
              //     if (employee.IsMainResponsiblePerson) responseEmployeeID = employee.EmployeeID;
              // });
              // tmpObj.Employee = employeeIDs;
              // tmpObj.ResponseEmployee = responseEmployeeID;

            // get link of feature
            if (item.href && item.href !== '') {
              tmpObj.href = item.href;
            }else {
              if (item.FeatureID && Permission.menuLeftArr) {
                let menuFeatureObj = _.find(Permission.menuLeftArr, ['FeatureID', item.FeatureID]);

                if (menuFeatureObj) {
                  if (menuFeatureObj.RouterFrontEnd) tmpObj.href = menuFeatureObj.RouterFrontEnd;
                }
              }
            }

            let oldNode = _.find(jsonFlowchart.node, ['WFItemID', item.WFItemID]);
            if (_.isObject(oldNode)) {
              tmpObj.PositionX = (oldNode.PositionX) ? oldNode.PositionX : 0;
              tmpObj.PositionY = (oldNode.PositionY) ? oldNode.PositionY : 0;
              if (oldNode.href) tmpObj.href = oldNode.href;

              tmpObj.Employee = (oldNode.Employee) ? oldNode.Employee : [];
              tmpObj.ResponseEmployee = (oldNode.ResponseEmployee) ? oldNode.ResponseEmployee : null;
            } else {
              tmpObj.PositionX = positionX;
              tmpObj.PositionY = 0;
              positionX += 50;
              tmpObj.Employee = [];
              tmpObj.ResponseEmployee = null;
            }
            self.model.JsonFlowchart.node.push(tmpObj);

            // Lấy liên kết or và and
            // let constraintAnds = _.filter(self.model.WorkflowConstraint, {
            //   WFItemID: item.WFItemID,
            //   ConstraintCondition: 1
            // });
            // let constraintOrs = _.filter(self.model.WorkflowConstraint, {
            //   WFItemID: item.WFItemID,
            //   ConstraintCondition: 2
            // });
            // let constraintSwitches = _.filter(self.model.WorkflowConstraint, {
            //   WFItemID: item.WFItemID,
            //   ConstraintCondition: 4
            // });

            // Thêm liên kết and
            // if (constraintAnds.length > 1 || (constraintAnds.length === 1 && constraintOrs.length >= 1) || (constraintAnds.length === 1 && constraintSwitches.length >= 1)) {
            //   // set tmpNodeAnd
            //   let tmpNodeAnd = {};
            //   // self.model.maxItemID += 1;
            //   // tmpNodeAnd.WFItemID = Number(item.WFItemID) + 6000;
            //   tmpNodeAnd.WFItemID = Number(item.LineIDTemp) + 6000;
            //   tmpNodeAnd.WFItemName = 'Ràng buộc gộp';
            //   tmpNodeAnd.ProcessType = 1;
            //   tmpNodeAnd.NodeID = 'flowchart-window-' + tmpNodeAnd.WFItemID;
            //   tmpNodeAnd.NodeConnect = '-window-' + tmpNodeAnd.WFItemID;
            //   tmpNodeAnd.NodeType = 2;
            //
            //   let oldNodeAdd = _.find(jsonFlowchart.node, ['WFItemID', tmpNodeAnd.WFItemID]);
            //   if (_.isObject(oldNodeAdd)) {
            //     tmpNodeAnd.PositionX = (oldNodeAdd.PositionX) ? oldNodeAdd.PositionX : 0;
            //     tmpNodeAnd.PositionY = (oldNodeAdd.PositionY) ? oldNodeAdd.PositionY : 0;
            //   } else {
            //     tmpNodeAnd.PositionX = positionX;
            //     tmpNodeAnd.PositionY = 0;
            //     positionX += 50;
            //   }
            //   self.model.JsonFlowchart.node.push(tmpNodeAnd);
            //   // self.model.JsonFlowchart.node = __.insertBeforeKey(self.model.JsonFlowchart.node, self.model.JsonFlowchart.node.length - 1, tmpNodeAnd);
            //
            //   // re render constraint for condition
            //   // add constraint
            //   _.forEach(constraintAnds, function (constraintAnd, key) {
            //     let tmpConstraint = {};
            //     self.model.maxConstraintID += 1;
            //     tmpConstraint.LineID = self.model.maxConstraintID;
            //     tmpConstraint.WFItemID = tmpNodeAnd.WFItemID;
            //     tmpConstraint.WFPreItemID = constraintAnd.WFPreItemID;
            //     tmpConstraint.WFPreItemValue = '';
            //     tmpConstraint.ConstraintType = 1;
            //     tmpConstraint.ConstraintLabel = '';
            //     tmpConstraint.ConstraintCondition = 4;
            //
            //     self.model.JsonFlowchart.constraint.push(tmpConstraint);
            //     // remove old constraint
            //     _.remove(self.model.JsonFlowchart.constraint, ['LineID', constraintAnd.LineID]);
            //   });
            //   // add constraint with constraint and
            //   let tmpConstraintWith = {};
            //   self.model.maxConstraintID += 1;
            //   tmpConstraintWith.LineID = self.model.maxConstraintID;
            //   tmpConstraintWith.WFItemID = item.WFItemID;
            //   tmpConstraintWith.WFPreItemID = tmpNodeAnd.WFItemID;
            //   tmpConstraintWith.WFPreItemValue = '';
            //   tmpConstraintWith.ConstraintType = 1;
            //   tmpConstraintWith.ConstraintLabel = '';
            //   tmpConstraintWith.ConstraintCondition = 4;
            //   self.model.JsonFlowchart.constraint.push(tmpConstraintWith);
            // }

            // Thêm liên kết or
            // if (constraintOrs.length > 1 || (constraintAnds.length >= 1 && constraintOrs.length === 1) || (constraintSwitches.length >= 1 && constraintOrs.length === 1)) {
            //   // set tmpNodeAnd
            //   let tmpNodeOr = {};
            //   // self.model.maxItemID += 1;
            //   // tmpNodeOr.WFItemID = Number(item.WFItemID) + 8000;
            //   tmpNodeOr.WFItemID = Number(item.LineIDTemp) + 8000;
            //   tmpNodeOr.WFItemName = 'Ràng buộc hoặc';
            //   tmpNodeOr.ProcessType = 1;
            //   tmpNodeOr.NodeID = 'flowchart-window-' + tmpNodeOr.WFItemID;
            //   tmpNodeOr.NodeConnect = '-window-' + tmpNodeOr.WFItemID;
            //   tmpNodeOr.NodeType = 3;
            //
            //   let oldNodeOr = _.find(jsonFlowchart.node, ['WFItemID', tmpNodeOr.WFItemID]);
            //   if (_.isObject(oldNodeOr)) {
            //     tmpNodeOr.PositionX = (oldNodeOr.PositionX) ? oldNodeOr.PositionX : 0;
            //     tmpNodeOr.PositionY = (oldNodeOr.PositionY) ? oldNodeOr.PositionY : 0;
            //   } else {
            //     tmpNodeOr.PositionX = positionX;
            //     tmpNodeOr.PositionY = 0;
            //     positionX += 50;
            //   }
            //   self.model.JsonFlowchart.node.push(tmpNodeOr);
            //
            //   // re render constraint for condition
            //   // add constraint
            //   _.forEach(constraintOrs, function (constraintOr, key) {
            //     let tmpConstraint = {};
            //     self.model.maxConstraintID += 1;
            //     tmpConstraint.LineID = self.model.maxConstraintID;
            //     tmpConstraint.WFItemID = tmpNodeOr.WFItemID;
            //     tmpConstraint.WFPreItemID = constraintOr.WFPreItemID;
            //     tmpConstraint.WFPreItemValue = '';
            //     tmpConstraint.ConstraintType = 1;
            //     tmpConstraint.ConstraintLabel = '';
            //     tmpConstraint.ConstraintCondition = 4;
            //
            //     self.model.JsonFlowchart.constraint.push(tmpConstraint);
            //     // remove old constraint
            //     _.remove(self.model.JsonFlowchart.constraint, ['LineID', constraintOr.LineID]);
            //   });
            //   // add constraint with constraint and
            //   let tmpConstraintWith = {};
            //   self.model.maxConstraintID += 1;
            //   tmpConstraintWith.LineID = self.model.maxConstraintID;
            //   tmpConstraintWith.WFItemID = item.WFItemID;
            //   tmpConstraintWith.WFPreItemID = tmpNodeOr.WFItemID;
            //   tmpConstraintWith.WFPreItemValue = '';
            //   tmpConstraintWith.ConstraintType = 1;
            //   tmpConstraintWith.ConstraintLabel = '';
            //   tmpConstraintWith.ConstraintCondition = 4;
            //   self.model.JsonFlowchart.constraint.push(tmpConstraintWith);
            // }

            // Thêm liên kết rẽ nhánh
            // if (constraintSwitches.length > 1 || (constraintAnds.length >= 1 && constraintSwitches.length === 1) || (constraintOrs >= 1 && constraintSwitches === 1)) {
            //   // set tmpNodeAnd
            //   let tmpNodeSwitch = {};
            //   // self.model.maxItemID += 1;
            //   // tmpNodeSwitch.WFItemID = Number(item.WFItemID) + 10000;
            //   tmpNodeSwitch.WFItemID = Number(item.LineIDTemp) + 10000;
            //   tmpNodeSwitch.WFItemName = 'Ràng buộc rẽ nhánh';
            //   tmpNodeSwitch.ProcessType = 1;
            //   tmpNodeSwitch.NodeID = 'flowchart-window-' + tmpNodeSwitch.WFItemID;
            //   tmpNodeSwitch.NodeConnect = '-window-' + tmpNodeSwitch.WFItemID;
            //   tmpNodeSwitch.NodeType = 4;
            //
            //   let oldNodeSwitch = _.find(jsonFlowchart.node, ['WFItemID', tmpNodeSwitch.WFItemID]);
            //   if (_.isObject(oldNodeSwitch)) {
            //     tmpNodeSwitch.PositionX = (oldNodeSwitch.PositionX) ? oldNodeSwitch.PositionX : 0;
            //     tmpNodeSwitch.PositionY = (oldNodeSwitch.PositionY) ? oldNodeSwitch.PositionY : 0;
            //   } else {
            //     tmpNodeSwitch.PositionX = positionX;
            //     tmpNodeSwitch.PositionY = 0;
            //     positionX += 50;
            //   }
            //   self.model.JsonFlowchart.node.push(tmpNodeSwitch);
            //
            //   // re render constraint for condition
            //   // add constraint
            //   _.forEach(constraintSwitches, function (constraintSwitch, key) {
            //     let tmpConstraint = {};
            //     self.model.maxConstraintID += 1;
            //     tmpConstraint.LineID = self.model.maxConstraintID;
            //     tmpConstraint.WFItemID = tmpNodeSwitch.WFItemID;
            //     tmpConstraint.WFPreItemID = constraintSwitch.WFPreItemID;
            //     tmpConstraint.WFPreItemValue = '';
            //     tmpConstraint.ConstraintType = 1;
            //     tmpConstraint.ConstraintLabel = '';
            //     tmpConstraint.ConstraintCondition = 4;
            //
            //     self.model.JsonFlowchart.constraint.push(tmpConstraint);
            //     // remove old constraint
            //     _.remove(self.model.JsonFlowchart.constraint, ['LineID', constraintSwitch.LineID]);
            //   });
            //   // add constraint with constraint and
            //   let tmpConstraintWith = {};
            //   self.model.maxConstraintID += 1;
            //   tmpConstraintWith.LineID = self.model.maxConstraintID;
            //   tmpConstraintWith.WFItemID = item.WFItemID;
            //   tmpConstraintWith.WFPreItemID = tmpNodeSwitch.WFItemID;
            //   tmpConstraintWith.WFPreItemValue = '';
            //   tmpConstraintWith.ConstraintType = 1;
            //   tmpConstraintWith.ConstraintLabel = '';
            //   tmpConstraintWith.ConstraintCondition = 4;
            //   self.model.JsonFlowchart.constraint.push(tmpConstraintWith);
            // }
          });

          this.stage.isRenderFlowchart = true;
        },
        onNavigationItem(type) {
          let currentIndex = this.reqParams.idsArray.indexOf(this.idParams);
          let newIndex = (type === 'prev') ? currentIndex - 1 : currentIndex + 1;

          if (newIndex === (this.reqParams.idsArray.length) && (this.reqParams.currentPage != this.reqParams.lastPage)) {
            this.reqParams.currentPage = this.reqParams.currentPage + 1;
            this.getItemIds(type);
          } else if (newIndex < 0 && this.reqParams.currentPage > 1){
            this.reqParams.currentPage = this.reqParams.currentPage - 1;
            this.getItemIds(type);
          }
          else {
            this.idParams = (this.reqParams.idsArray[newIndex]) ? this.reqParams.idsArray[newIndex] : this.idParams;
          }
        },
        getItemIds(type){
            let self = this;
            let requestData = {
                method: 'post',
                url: ListApi,
                data: {
                    per_page: this.reqParams.perPage,
                    page: this.reqParams.currentPage,
                    type: 'only-id'
                }
            };

            if (this.reqParams.search.WFName !== '') {
                requestData.data.WFName = this.reqParams.search.WFName;
            }

            this.$store.commit('isLoading', true);
            ApiService.customRequest(requestData).then((response) => {
                let dataResponse = response.data;
                if (dataResponse.status === 1) {
                  self.reqParams.total = dataResponse.data.total;
                  self.reqParams.perPage = String(dataResponse.data.per_page);
                  self.reqParams.currentPage = dataResponse.data.current_page;

                  this.reqParams.idsArray = [];
                  _.forEach(dataResponse.data.data, function (value, key) {
                      self.reqParams.idsArray.push(value.WFID);
                  });
                  (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
                }
                self.$store.commit('isLoading', false);
            }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
            });

        },
        onSelectedFeature(event, key){
          this.model.WorkflowItem[key].WFItemName = event.text;
          this.model.WorkflowItem[key].ProcessType = 1;
          this.model.WorkflowItem[key].FeatureID = Number(event.id);
          this.model.WorkflowItem[key].FeatureKey = (event.FeatureKey) ? event.FeatureKey : null;
          this.model.WorkflowItem[key].FeatureName = event.text;
          this.renderWorkflowItemOption();
        },
        onSelectProcessType(event, key){
          this.model.WorkflowItem[key].ProcessType = Number(event);
            this.model.WorkflowItem[key].FeatureID = null;
            this.model.WorkflowItem[key].FeatureKey = null;
            this.model.WorkflowItem[key].FeatureName = null;
          this.renderWorkflowItemOption();
        },
        onClickTabWorkflowItem(){
          this.stage.showFlowchart = false;
          this.stage.tabActive = 1;
          // if (this.$refs.flowchart) {
          //   let jsonFlowchart = this.$refs.flowchart.saveFlowchart();
          //
          // }
          this.updateJsonFlowchart();
        },
        updateJsonFlowchart(){
          let self = this;
          if (this.$refs.flowchart) {
            // update json flowchart
            this.$refs.flowchart.saveFlowchart();
            _.forEach(this.model.JsonFlowchart.node, function (node, key) {
              let _indexOfWorkflowItem = _.findIndex(self.model.WorkflowItem, ['WFItemID', node.WFItemID]);
              if (self.model.WorkflowItem[_indexOfWorkflowItem]) {
                let tmpWorkflowItem = self.model.WorkflowItem[_indexOfWorkflowItem];
                if (node.FeatureStatusID) tmpWorkflowItem.FeatureStatusID = node.FeatureStatusID;
                if (node.href) tmpWorkflowItem.href = node.href;
                (node.Employee) ? tmpWorkflowItem.Employee = node.Employee : tmpWorkflowItem.Employee = [];
                (node.ResponseEmployee) ? tmpWorkflowItem.ResponseEmployee = node.ResponseEmployee : tmpWorkflowItem.ResponseEmployee = null;
                self.model.WorkflowItem[_indexOfWorkflowItem] = tmpWorkflowItem;
              }
            });
          }
        },
        onClickTabFlowchart(){
          this.renderJsonFlowchart();
          this.stage.showFlowchart = true;
          this.stage.tabActive = 2;
        },
        onAddFieldOnTable() {
          let fieldObj = {}, updateProcessType = false;
          this.model.maxItemID += 1;
          fieldObj.WFItemID = this.model.maxItemID;
          fieldObj.LineIDTemp = fieldObj.WFItemID;
          fieldObj.WFItemName = '';
          fieldObj.FeatureID = 0;
          fieldObj.IsTaskFeature = false;
          fieldObj.ProcessType = 2;
          fieldObj.NOrder = null;

          if (this.model.WorkflowItem.length === 0) {
            fieldObj.WFItemName = 'Bắt đầu';
            fieldObj.ProcessType = 4;
            updateProcessType = true
          }
          this.model.WorkflowItem.push(fieldObj);

          if (updateProcessType) this.updateProcessTypeOption();
          this.renderWorkflowItemOption();
          this.$forceUpdate();
        },
        onDeleteFieldOnTable(field){
          // remove field in fieldOnTableReq
          let fieldExist = _.find(this.model.WorkflowItem, ['WFItemID', field.WFItemID]);
          if (_.isObject(fieldExist)) {
            _.remove(this.model.WorkflowItem, ['WFItemID', field.WFItemID]);
          }
          this.renderWorkflowItemOption();
          this.$forceUpdate();
        },
        onAddConstraintOfField(){
          let constraintObj = {};
          let taskWorkflowItem = _.find(this.model.WorkflowItem, ['WFItemID', this.model.WFItemIDSelected]);
          if (!_.isObject(taskWorkflowItem)) return;
          if (taskWorkflowItem.ProcessType == '3') {
            let allConstraint = _.filter(this.model.WorkflowConstraint, ['WFItemID', this.model.WFItemIDSelected]);
            if (allConstraint.length > 0) {
              this.$bvToast.toast('Chức năng điều kiện chỉ có một ràng buộc liên kết trước', {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
              return;
            }
          }
          // validate điều kiện nếu có liên kết với nút bắt đầu thì chỉ có 1 ràng buộc
          let startWorkflowItem = _.find(this.model.WorkflowItem, ['ProcessType', 4]);
          if (startWorkflowItem) {
            let allConstraintWithStart = _.filter(this.model.WorkflowConstraint, {
              'WFItemID': this.model.WFItemIDSelected,
              'WFPreItemID': startWorkflowItem.WFItemID
            });

            if (allConstraintWithStart.length) {
              this.$bvToast.toast('Chỉ có một ràng buộc với chức năng bắt đầu', {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
              // return;
            }
          }

          this.model.maxConstraintID += 1;
          constraintObj.LineID = this.model.maxConstraintID;
          constraintObj.WFItemID = taskWorkflowItem.WFItemID;
          constraintObj.WFPreItemID = 0;
          constraintObj.WFPreItemValue = '';
          constraintObj.ConstraintType = 1;
          constraintObj.ConstraintLabel = '';
          constraintObj.ConstraintCondition = 1;
          constraintObj.SourceAnchors = 4;
          constraintObj.TargetAnchors = 3;

          this.model.WorkflowConstraint.push(constraintObj);
          this.updateWorkflowItemOption();
          this.$forceUpdate();
        },
        onDeleteConstraintOnField(constraint){
          let constraintExist = _.find(this.model.WorkflowConstraint, ['LineID', constraint.LineID]);
          if (_.isObject(constraintExist)) _.remove(this.model.WorkflowConstraint, ['LineID', constraint.LineID]);
          this.updateWorkflowItemOption();
          this.$forceUpdate();
        },
        onSelectedWFPreItem(event, key){
          let indexOption = _.findIndex(this.model.WorkflowItemOption, ['id', Number(event.id)]);
          this.model.WorkflowConstraint[key].WFPreItemID = Number(event.id);
          if (this.model.WorkflowItemOption[indexOption]) {
            let tmpObj = this.model.WorkflowItemOption[indexOption];
            tmpObj.disabled = true;
            this.model.WorkflowItemOption[indexOption] = tmpObj;
          }
          this.updateWorkflowItemOption();
        },
        handleSubmitForm(){
          let self = this;
          if (!this.stage.isRenderFlowchart) {
            this.renderJsonFlowchart();
          }
          this.updateJsonFlowchart();
          _.forEach(this.model.WorkflowItem, function (item, key) {
            self.model.WorkflowItem[key].LineIDTemp = item.WFItemID;
            self.model.WorkflowItem[key].IsTaskFeature = (item.IsTaskFeature) ? 1 : 0;
            self.model.WorkflowItem[key].NOrder = key + 1;
            self.model.WorkflowItem[key].FeatureStatusID = (self.model.WorkflowItem[key].FeatureStatusID) ? self.model.WorkflowItem[key].FeatureStatusID : null;
          });
          let requestData = {
              method: 'post',
              url: StoreApi,
              data: {
                master: {
                  WFName: this.model.WFName,
                  WFNo: this.model.WFNo,
                  Inactive: (this.model.Inactive) ? 1 : 0,
                  // JsonFlowchart: (this.$refs.flowchart) ? JSON.stringify(this.$refs.flowchart.saveFlowchart()) : JSON.stringify(this.model.JsonFlowchart)
                  JsonFlowchart: JSON.stringify(this.model.JsonFlowchart)
                },
                detail: this.model.WorkflowItem,
                constraint: this.model.WorkflowConstraint
              }
          };

          // edit user
          if (this.idParams) {
              requestData.data.master.WFID = this.idParams;
              requestData.url = UpdateApi + '/' + this.idParams;
          }
          this.$store.commit('isLoading', true);
          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
              let responsesData = responses.data;
              if (responsesData.status === 1) {
                if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
                let msg = 'Bản ghi đã được cập nhật!';
                if (responsesData.msg) {
                  msg = responsesData.msg;
                }
                self.$router.push({
                  name: ViewRouter,
                  params: {id: self.idParams, message: msg, tabActive: self.stage.tabActive}
                });
              } else {
                  // let htmlErrors = __.renderErrorApiHtml(responsesData.msg);
                  Swal.fire(
                      'Thông báo',
                      'Không được sửa quy trình đã có dữ liệu',
                      'error'
                  )
              }

              self.$store.commit('isLoading', false);
          }, (error) => {
              console.log(error);
              Swal.fire(
                'Thông báo',
                'Không kết nối được với máy chủ',
                'error'
              );
              self.$store.commit('isLoading', false);
          });
        },
        handleSubmitFormSave(){
          let self = this;
          if (!this.stage.isRenderFlowchart) {
            this.renderJsonFlowchart();
          }
          this.updateJsonFlowchart();

          _.forEach(this.model.WorkflowItem, function (item, key) {
            self.model.WorkflowItem[key].IsTaskFeature = (item.IsTaskFeature) ? 1 : 0;
            self.model.WorkflowItem[key].FeatureStatusID = (self.model.WorkflowItem[key].FeatureStatusID) ? self.model.WorkflowItem[key].FeatureStatusID : null;
          });

          let requestData = {
            method: 'post',
            url: UpdateApi + '/' + this.idParams,
            data: {
              master: {
                WFID: this.WFID,
                WFName: this.model.WFName,
                WFNo: this.model.WFNo,
                Inactive: (this.model.Inactive) ? 1 : 0,
                JsonFlowchart: JSON.stringify(this.model.JsonFlowchart)
              },
              detail: this.model.WorkflowItem,
              constraint: this.model.WorkflowConstraint
            }
          };

          this.$store.commit('isLoading', true);
          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            let responsesData = responses.data;

            if (responsesData.status === 1) {
              self.$router.push({
                  name: ViewRouter,
                  params: {id: self.idParams, message: 'Bản ghi đã được cập nhật!', tabActive: self.stage.tabActive}
              });
              return;
              // this.$bvToast.toast('Bản ghi đã được cập nhật', {
              //     title: 'Thông báo',
              //     variant: 'success',
              //     solid: true
              // });
            } else {
              this.$bvToast.toast('Có lỗi xảy ra', {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
            }

            self.$store.commit('isLoading', false);
          }, (error) => {
            self.$store.commit('isLoading', false);
          });

        },
        onEditClicked(){
            this.$router.push({
                name: EditRouter,
                params: {id: this.idParams, req: this.reqParams}
            });
        },
        onCreateClicked(){
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
        autoCorrectedDatePipe: () => { return createAutoCorrectedDatePipe('dd/mm/yyyy') },
        autoCorrectedDateTimePipe: () => {return createAutoCorrectedDatePipe('dd/mm/yyyy hh:mm')},
      },
      watch: {
        idParams() {
          this.fetchData();
        },
      }
    }
</script>
<style lang="css">
  .component-workflow .v-select .dropdown-menu {
      max-height: 170px !important;
  }
  .component-workflow .custom-align {
      flex: 0 0 12.3%;
  }
  .component-workflow .select2-container--default .select2-selection--single {
    border: none;
  }
  .component-workflow .table thead th {
    vertical-align: middle;
  }
  .component-workflow #modal-flowchart .select2-container {
    width: 100% !important;
  }
  .component-workflow .select2-container--default .select2-selection--single {
    border: none;
  }
  #modal-flowchart .select2-container {
    width: 100% !important;
  }
  #modal-flowchart .select2-container--default .select2-selection--single {
    border: none;
  }
  #modal-flowchart .modal-body {
    padding: 1rem;
  }
</style>

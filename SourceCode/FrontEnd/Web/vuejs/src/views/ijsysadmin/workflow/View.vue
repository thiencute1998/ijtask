<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                      <div class="main-header-item main-header-name">
                        <span class="d-flex align-items-center"><i class="icon-eye icon mr-2"></i> Quy trình công việc: {{model.WFName}}</span>
                      </div>
                    </b-col>
                    <b-col class="col-md-6"></b-col>
                </b-row>
                <b-row>
                  <b-col class="col-md-12 col-sm-12 col-24 mb-2 mb-sm-0 mb-md-0">
                    <div class="main-header-item main-header-actions ">
                          <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onCreateClicked"><i class="fa fa-plus"></i> Thêm</b-button>
                          <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="onEditClicked"><i class="fa fa-edit"></i> Sửa</b-button>
                          <b-dropdown variant="primary" id="dropdown-actions" class="main-header-action mr-2" text="Thao tác">
                            <b-dropdown-item @click="handleDeleteItem">Xóa</b-dropdown-item>
                            <b-dropdown-item @click="handleCopyItem">Nhân bản</b-dropdown-item>
                            <b-dropdown-item>Chia sẻ</b-dropdown-item>
                            <b-dropdown-item>Chat</b-dropdown-item>
                            <b-dropdown-item>Zalo</b-dropdown-item>
                            <b-dropdown-item>Phân quyền</b-dropdown-item>
                          </b-dropdown>
                        </div>
                  </b-col>
                  <b-col class="col-md-12 col-sm-12 col-24">
                    <div class="main-header-item main-header-icons">
                            <div class="main-header-item-counter ml-auto mr-3" v-if="reqParams">
                                <span>{{itemNo}} - {{reqParams.idsArray.length + this.reqParams.perPage * (this.reqParams.currentPage - 1)}}</span>
                                /
                                <span>{{reqParams.total}}</span>
                            </div>
                            <b-button-group id="main-header-views" class="main-header-views">
                              <b-button id="tooltip-view-prev" @click="onNavigationItem('prev')" v-if="reqParams" class="main-header-view"><i class="fa fa-angle-left"></i></b-button>
                              <b-button id="tooltip-view-next" @click="onNavigationItem('next')" v-if="reqParams" class="main-header-view"><i class="fa fa-angle-right"></i></b-button>
                              <b-button id="tooltip-view-back-to-list" @click="onBackToList" class="main-header-view"><i class="fa fa-list"></i></b-button>
                              <b-tooltip container="main-header-views" target="tooltip-view-back-to-list">Danh sách</b-tooltip>
                              <b-tooltip container="main-header-views" target="tooltip-view-prev">Trước</b-tooltip>
                              <b-tooltip container="main-header-views" target="tooltip-view-next">Sau</b-tooltip>
                            </b-button-group>
                            <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
                                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen=true />
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
                <div class="form-body">
                  <div class="form-group row align-items-center">
                    <div class="col-lg-2 col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" for="WFName" style="white-space: nowrap">Tên</div>
                    <div class="col-lg-18 col-md-16 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
                      {{model.WFName}}
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                      <span>Mã số</span>
                      {{model.WFNo}}
                    </div>

                  </div>
                  <div class="workflow-view mb-3">
                    <b-card body-class="p-0">
                      <b-tabs card>
                        <b-tab title="Công việc" @click="onClickTabWorkflowItem" :active="(stage.tabActive === 1) ? true : false">
                          <div class="table-responsive">
                            <table class="table b-table table-sm table-bordered table-column-resizable">
                              <thead>
                              <tr>
                                <th scope="col" class="text-center" style="width: 4%">STT</th>
                                  <th scope="col" class="text-center" style="width: 12%; min-width: 100px">Loại chức năng</th>
                                <th scope="col" class="text-center" style="width: 20%; min-width: 200px">Chức năng</th>
                                <th scope="col" class="text-center" style="width: 40%; min-width: 200px">Diễn giải</th>
                                <th scope="col" class="text-center" style="width: 25%; min-width: 150px">Người thực hiện</th>
                                <th scope="col" class="text-center" style="width: 15%; min-width: 150px">Loại trạng thái</th>
                              </tr>
                              </thead>
                              <tbody>
                              <tr v-for="(field, key) in model.WorkflowItem">
                                <td><span class="table-padding-x">{{key + 1}}</span></td>
                                <td><span class="table-padding-x">{{model.ProcessType[field.ProcessType]}}</span></td>
                                <td style="width: 15%"><span class="table-padding-x">{{getFeatureNameByID(field.FeatureID)}}</span></td>
                                <td><span class="table-padding-x" :title="field.WFItemName | stripHtml">{{field.WFItemName | stripHtml}}</span></td>
                                <td>
<!--                                    <div class="user-icons table-padding-x">-->
<!--                                        <div class="user-icon" :class="[(user.IsMainResponsiblePerson) ? 'user-main-response' : '']" v-if="key < 3" :title="getTitleEmployee(user)" v-for="(user, key) in filterWorkflowEmployee(field.WFItemID)">-->
<!--                                            <img :src="$store.state.appRootApi + user.Avata" :alt="user.EmployeeName"/>-->
<!--                                        </div>-->
<!--                                        <div class="user-plus" v-if="filterWorkflowEmployee(field.WFItemID).length > 3">+{{filterWorkflowEmployee(field.WFItemID).length - 3}}</div>-->
<!--                                    </div>-->
                                    <ijcore-users-icon :all-users="model.WorkflowEmployee" filter-name="WFItemID" :filter-value="field.WFItemID" :number="6"></ijcore-users-icon>
                                </td>
                                <td> <div class="table-padding-x" :title="getStatusNameByID(field.FeatureStatusID)">{{getStatusNameByID(field.FeatureStatusID)}}</div></td>
                              </tr>
                              </tbody>
                            </table>
                          </div>
                        </b-tab>
                        <b-tab title="Sơ đồ" @click="onClickTabFlowchart" :active="(stage.tabActive !== 1) ? true : false">
                          <div class="workflow-design" style="min-height: 300px;" v-if="stage.showFlowchart">
                            <ijcore-flowchart ref="flowchart" v-if="stage.showFlowchart" v-model="model.JsonFlowchart"></ijcore-flowchart>
                          </div>
                        </b-tab>
                      </b-tabs>
                    </b-card>
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
    import IjcoreFlowchart from '@/components/IjcoreFlowchart';
    import {PermissionService} from "@/services/permission.service";
    import ColumnResizer from 'column-resizer';
    import IjcoreUsersIcon from '@/components/IjcoreUsersIcon';

    const ListRouter = 'sysadmin-workflow';
    const EditRouter = 'sysadmin-workflow-edit';
    const CreateRouter = 'sysadmin-workflow-create';
    const ViewApi = 'sysadmin/api/workflow/view';
    const ListApi = 'sysadmin/api/workflow';
    const DeleteApi = 'sysadmin/api/workflow/delete';

    const Permission = PermissionService.getPermission();

    export default {
        name: 'sysadmin-view-vendor',
        data () {
            return {
                idParams: this.$route.params.id,
                reqParams: this.$route.params.req,
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

                  WorkflowEmployee: [],

                  WorkflowConstraint: [],
                  WFItemIDSelected: 0,

                  SysFeatureStatus: [],

                  feature: [],
                  featureOption: [],
                  maxItemID: 0,
                  maxConstraintID: 0,

                },
                defaultModel: {},
                stage: {
                    updatedData: false,
                    message: (this.$route.params.message) ? this.$route.params.message : '',
                    showFlowchart: false,
                    tabActive: (this.$route.params.tabActive) ? this.$route.params.tabActive : 1
                }
            }

        },

        components: {
          IjcoreFlowchart,
            IjcoreUsersIcon
        },
        beforeCreate() {
            if (!this.$route.params.id) {
                this.$router.push({name: ListRouter});
            }
        },
        mounted() {
          this.fetchData();
            if (document.querySelector('.table-column-resizable')) {
                new ColumnResizer(
                    document.querySelector('.table-column-resizable'),{resizeMode: 'overflow'}
                );
            }

          // hiển thị thông báo
          if (this.stage.message && this.stage.message !== '') {
            this.$bvToast.toast(this.stage.message, {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });
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
            }
        },
        methods: {
          fetchData() {
              if (this.idParams == 0 || _.isUndefined(this.idParams)) {
                  return false;
              }
              let self = this;
              let urlApi = '';
              let requestData = {
                  method: 'get',
              };
              // Api edit user
              if(this.idParams){
                  urlApi = ViewApi + '/' + this.idParams;
                  let data = {
                      id: this.idParams
                  };
                  requestData.data = data;
              }
              requestData.url = urlApi;
              this.$store.commit('isLoading', true);
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                  let responsesData = responses.data;
                  self.defaultModel = responsesData;
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
                    if (responsesData.data.WorkflowEmployee) self.model.WorkflowEmployee = responsesData.data.WorkflowEmployee;

                    _.forEach(self.model.WorkflowItem, function (item, key) {
                      self.model.WorkflowItem[key].IsTaskFeature = (item.IsTaskFeature == 1) ? true : false;
                    });

                    // update WFItemID for Node JsonFlowchart
                    _.forEach(self.model.JsonFlowchart.node, function (node, key) {
                      // let workFlowItem = _.find(self.model.WorkflowItem, ['LineIDTemp', node.WFItemID]);
                      // if (workFlowItem) self.model.JsonFlowchart.node[key].WFItemID = workFlowItem.WFItemID;

                      // update key for node condition and
                      // let WFItemIDAnd = Number(node.WFItemID) - 6000;
                      // let workflowItemAnd = _.find(self.model.WorkflowItem, ['LineIDTemp', WFItemIDAnd]);
                      // if (workflowItemAnd) {
                      //   // self.model.JsonFlowchart.node[key].WFItemID = Number(workflowItemAnd.WFItemID) + 6000;
                      //   self.model.JsonFlowchart.node[key].WFItemID = Number(workflowItemAnd.LineIDTemp) + 6000;
                      // }

                      // update key for node condition or
                      // let WFItemIDOr = Number(node.WFItemID) - 8000;
                      // let workflowItemOr = _.find(self.model.WorkflowItem, ['LineIDTemp', WFItemIDOr]);
                      // if (workflowItemOr) {
                      //   // self.model.JsonFlowchart.node[key].WFItemID = Number(workflowItemOr.WFItemID) + 8000;
                      //   self.model.JsonFlowchart.node[key].WFItemID = Number(workflowItemOr.LineIDTemp) + 8000;
                      // }
                    });

                    // set options feature
                    self.model.featureOption = [];
                    _.forEach(self.model.feature, function (feature, key) {
                      let tmpObj = {};
                      tmpObj.id = feature.FeatureID;
                      tmpObj.text = feature.FeatureName;
                      self.model.featureOption.push(tmpObj);
                    });
                    self.renderJsonFlowchart();
                  }

                  if (self.stage.tabActive !== 1) {
                      self.stage.showFlowchart = true;
                  }
                  self.$store.commit('isLoading', false);
              }, (error) => {
                  console.log(error);
                  self.$store.commit('isLoading', false);
              });
          },
          getFeatureNameByID(featureID){
            let featureObj = _.find(this.model.feature, ['FeatureID', featureID]);
            if (_.isObject(featureObj)) return featureObj.FeatureName;
            return '';
          },
          getStatusNameByID(featureStatusID){
            let status = _.find(this.model.SysFeatureStatus, ['StatusID', featureStatusID]);
            if (!_.isEmpty(status)) return status.StatusName;
            return '';
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
              self.model.JsonFlowchart.constraint.push(tmpObj);
            });

            //render node
            _.forEach(this.model.WorkflowItem, function (item, key) {
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
              } else {
                tmpObj.PositionX = positionX;
                tmpObj.PositionY = 0;
                positionX += 50;
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
          },
          getTitleEmployee(employee){
              let title = employee.EmployeeName;
              if (employee.IsMainResponsiblePerson) {
                  title += ' - Là người chịu trách nhiệm chính';
              }
              return title;
          },
          filterWorkflowEmployee(WFItemID){
              let workflowEmployee = _.filter(this.model.WorkflowEmployee, ['WFItemID', WFItemID]);
              return workflowEmployee;
          },
          onClickTabFlowchart(){
              this.stage.tabActive = 2;
              this.stage.showFlowchart = true;
          },
          onClickTabWorkflowItem(){
              this.stage.tabActive = 1;
              this.stage.showFlowchart = false;
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
                  self.$store.commit('isLoading', false);
                  console.log(error);
              });

          },
          onEditClicked(){
              this.$router.push({
                  name: EditRouter,
                  params: {id: this.idParams, req: this.reqParams, tabActive: this.stage.tabActive}
              });
          },
          onCreateClicked(){
              this.$router.push({name: CreateRouter});
          },
          onBackToList(message = '') {
              if (_.isString(message)) {
                  this.$router.push({name: ListRouter, params: {message: message}});
              } else {
                  this.$router.push({name: ListRouter});
              }

          },
          handleCopyItem(){
              this.$router.push({name: CreateRouter, params: {itemCopy: this.defaultModel}});
          },
          updateModel() {
              if (this.stage.updatedData) {
                  this.$forceUpdate();
              }
          },
          handleDeleteItem() {
              let self = this;
              let title = 'Bạn có muốn xóa bản ghi?';
              Swal.fire({
                  title: title,
                  text: 'Bạn sẽ không thể khôi phục thông tin bản ghi!',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Đồng ý',
                  cancelButtonText: 'Hủy bỏ'
              }).then((result) => {
                  if (result.value) {
                      let requestData = {
                          method: 'post',
                          url: DeleteApi + '/' + self.idParams,
                          data: {
                              array_id: [self.idParams],
                          },
                      };

                      ApiService.setHeader();
                      ApiService.customRequest(requestData).then((response) => {
                          let responseData = response.data;
                          if (responseData.status === 1) {
                              self.onBackToList('Bản ghi đã được xóa');
                          } else {
                              Swal.fire(
                                  'Có lỗi',
                                  '',
                                  'error'
                              );
                              console.log(response);
                          }
                      }, (error) => {
                          console.log(error);

                      });

                  }
              });
          },
        },
        watch: {
            idParams() {
                this.fetchData();
            },
            'model.flowchart' (){
              alert('aa');
            }
        }
    }
</script>

<style lang="css"></style>

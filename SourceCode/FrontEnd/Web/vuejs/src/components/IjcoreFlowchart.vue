<template>
<!--
  =========================================
  /**
  * @desc - NodeType :
  *          + 1 ~ nút thuộc loại bình thường
  *          + 2 ~ nút thuộc loại and WFItemID += 6000
  *          + 3 ~ nút thuộc loại or WFItemID += 8000
  *          + 4 ~ nút thuộc loại rẽ nhánh WFItemID += 10000

          - ProcessType:
  *          + 1 ~ Bên trong
  *          + 2 ~ Bên ngoài
  *          + 3 ~ Điều kiện
  *          + 4 ~ Bắt đầu
  *          + 5 ~ Kết thúc
  *
  */
  =========================================
  -->
  <div class="jtk-task-main">
    <!-- demo -->
    <div class="jtk-task-canvas canvas-wide flowchart-task jtk-surface jtk-surface-nopan" id="canvas">
      <div :title="(isDraggable && node.STT && key !== 0) ? 'STT ' + node.STT : ''"
           @click="onClickNode($event, node)"
           @dblclick="onDbClickNode($event, node)"
           class="jtk-node"
           :class="[(node.ProcessType === 3 && node.NodeType === 1) ? 'jtk-node-special' : '',
           (node.ProcessType !== 3 && node.NodeType === 1 && key !== 0 && node.ProcessType !== 5) ? 'window' : '',
           (node.NodeType === 3 && key !== 0) ? 'node-condition node-condition-or' : '',
           (node.NodeType === 2 && key !== 0) ? 'node-condition node-condition-and' : '',
           (node.NodeType === 4 && key !== 0) ? 'node-condition node-condition-switch' : '',
           (key === 0) ? 'node-start' : '',
           (node.ProcessType === 5) ? 'node-end' : '',
           (node.inActive && key !== 0 && !node.isNextTask) ? 'node-inActive' : '',
           ((!node.inActive || key === 0 || node.isNextTask) && node.ProcessType !== 3) ? 'node-active' : ''
           ]"
           :id="node.NodeID" v-for="(node, key) in value.node">
        <div :id="'popover-target-dataflow-' + key" class="d-flex align-items-center justify-content-center" @contextmenu.prevent="$refs.contextmenuNode.open($event, node)">
          <div v-if="node.ProcessType !== 3 && node.ProcessType !== 5 && node.ProcessType !== 4 && node.NodeType === 1 && key !== 0">
            <strong v-html="node.WFItemName"></strong>
          </div>
          <b-popover :target="'popover-target-dataflow-' + key" triggers="hover" placement="left" v-if="node.TaskID">
            <template #title>{{node.TaskName | stripHtml}}</template>
            <div>- Trạng thái: {{node.StatusDescription}}</div>
            <div>- Người thực hiện: <ijcore-users-icon :all-users="taskAssignUsers" filter-name="TaskID" :filter-value="node.TaskID" :number="6"></ijcore-users-icon></div>
            <div>- Ngày bắt đầu: {{node.StartDate | convertServerDateToClientDate}}</div>
            <div>- Hạn hoàn thành: {{node.DueDate | convertServerDateToClientDate}}</div>
            <div>- Phần trăm hoàn thành: <b-badge :variant="(node.PercentCompleted <= 0) ? 'warning' : (node.PercentCompleted > 0 && node.PercentCompleted < 100) ? 'primary' : 'success'">{{node.PercentCompleted}}</b-badge></div>
          </b-popover>
        </div>

        <div v-if="isDataflow && node.NodeType === 1
              && node.ProcessType !== 5 && node.ProcessType !== 3 && key !== 0
              && (!node.inActive || node.isNextTask) && isFinishDataflow !== 1"
             :class="[(!node.isCompleted && !node.isConstraintSwitch && !node.isNextTask) ? 'd-none' : '']"
             class="node-progress-icon">
          <div class="progress-icon-item icon-item-forward" @click="onClickUpdateExecution(node)" v-if="node.isNextTask || !node.isCompleted">
            <i title="Thực hiện bước này" class="fa fa-forward" v-if="node.isConstraintSwitch || node.isNextTask"></i>
<!--            <i title="Cập nhật tình trạng thực hiện" class="fa fa-pencil" v-if="!node.isConstraintSwitch && !node.isNextTask"></i>-->
          </div>
          <div class="progress-icon-item icon-item-undo" @click="onClickRedoExecution(node)" v-if="!node.inActive && node.isCompleted">
            <i title="Cập nhật lại tình trạng thực hiện" class="fa fa-undo"></i>
          </div>
        </div>


        <i v-if="node.NodeType === 2 && key !== 0" class="fa fa-plus"></i>
<!--        <i v-if="node.NodeType == 3 && key !== 0 && key !== (value.node.length - 1)" class="fa fa-arrows-alt"></i>-->
        <img v-if="node.NodeType === 3 && key !== 0" src="img/flowchart/merge.png" alt="merge"/>
        <img v-if="node.NodeType === 4 && key !== 0" src="img/flowchart/fork.png" alt="merge"/>
        <div v-if="node.ProcessType === 3 && node.NodeType === 1 && key !== 0" class="node-condition node-condition-if">
          <div class="diamond"></div>
        </div>
        <div v-if="node.ProcessType === 3 && node.NodeType === 1 && key !== 0" class="text">{{node.WFItemName}}</div>
        <div v-if="key === 0" class="d-flex align-items-center justify-content-center" style="width: 100%; height: 100%; position: absolute" @click="onClickStartNode(node)" @contextmenu.prevent="$refs.contextmenuStartNode.open($event)">
            <i class="fa fa-play"></i>
        </div>
        <div class="d-flex align-items-center justify-content-center" style="width: 100%; height: 100%; position: absolute"
            v-if="node.ProcessType === 5"
            @click="onClickEndNode(node)">
            <i class="fa fa-step-forward"></i>
        </div>
      </div>

      <b-modal v-if="isDraggable" ref="modal-feature-status" id="modal-feature-status" ok-title="Lưu" cancel-title="Hủy" size="lg" @ok="onSaveModalFeatureStatus" @cancel="onCancelModalFeatureStatus($event)" @hide="onHideModalFeatureStatus($event)">
        <template slot="modal-title">
          {{getNameOfNode()}}
        </template>

        <template v-slot:modal-footer="{ ok, cancel, hide }">
          <!-- Emulate built in modal footer ok and cancel button actions -->
<!--          <b-button class="mr-2" variant="primary" @click="ok()">-->
<!--            Lưu-->
<!--          </b-button>-->
          <b-button variant="primary" @click="ok()">
            Đồng ý
          </b-button>
        </template>

        <div class="row mb-2">
          <div class="col-lg-6 d-lg-flex align-items-center">Loại trạng thái</div>
          <div class="col-lg-18">
            <b-form-select
              v-model="model.FeatureStatusID"
              :options="model.FeatureStatusOption"
              @input="onSelectFeatureStatus($event)">
            </b-form-select>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-lg-6 d-lg-flex align-items-center">Link</div>
          <div class="col-lg-18">
            <b-form-input v-model="model.FeatureLink"></b-form-input>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-lg-6 d-lg-flex align-items-center">Người thực hiện</div>
          <div class="col-lg-18">
            <Select2 v-model="model.Employee" :settings="{multiple: true}" :options="EmployeeOption" @change="onChangeEmployee"></Select2>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6 d-lg-flex align-items-center">Người chịu trách nhiệm chính</div>
          <div class="col-lg-18">
            <b-form-select
                    v-model="model.ResponseEmployee"
                    :options="model.ResponseEmployeeOption">
            </b-form-select>
          </div>
        </div>

      </b-modal>
      <vue-context ref="contextmenuStartNode" class="contextmenu contextmenu-start-node" v-if="WFID">
        <template>
          <li><a role="menuitem" @click="onCreateDataflow">Tạo luồng công việc</a></li>
          <li><a role="menuitem" @click="onSelectDataflow">Chọn luồng công việc</a></li>
          <li><a role="menuitem" @click="onEditWorkflow">Sửa quy trình</a></li>
          <li><a role="menuitem" v-if="isDataflow" @click="onViewDataflow">Xem thông tin chung luồng công việc</a></li>
        </template>
      </vue-context>
      <vue-context ref="contextmenuNode" class="contextmenu contextmenu-node" v-if="isDataflow">
        <template slot-scope="node">
          <li><a role="menuitem" @click="onShowModalTask(1, node.data)">Công việc</a></li>
          <li v-if="node && node.data && node.data.FeatureID">
            <a role="menuitem" @click="onShowModalTrans(node.data)">
              Chứng từ
<!--              <span v-if="node.data.TransID">Xem chứng từ</span>-->
<!--              <span v-else>Tạo mới chứng từ</span>-->
            </a>
          </li>
          <li v-if="node && node.data && node.data.FeatureID"><a role="menuitem" @click="onShowModalLinkTrans(node.data)">Liên kết chứng từ</a></li>
          <b-dropdown-divider></b-dropdown-divider>
          <li><a role="menuitem" @click="onShowModalTask(2, node.data)">Thông tin chung</a></li>
          <li><a role="menuitem" @click="onShowModalTask(3, node.data)">Danh mục liên kết</a></li>
          <li><a role="menuitem" @click="onShowModalTask(4, node.data)">Giao việc</a></li>
          <li><a role="menuitem" @click="onShowModalTask(5, node.data)">Yêu cầu</a></li>
          <li><a role="menuitem" @click="onShowModalTask(6, node.data)">Thực hiện</a></li>
          <li><a role="menuitem" @click="onShowModalTask(7, node.data)">Kiểm tra</a></li>
          <li><a role="menuitem" @click="onShowModalTask(8, node.data)">Chi phí</a></li>
          <li><a role="menuitem" @click="onShowModalTask(9, node.data)">Tệp</a></li>
          <li><a role="menuitem" @click="onShowModalTask(10, node.data)">Phim</a></li>
        </template>
      </vue-context>
    </div>
  </div>
</template>

<script>
  import jsPlumb from 'jsplumb';
  import Select2 from 'v-select2-component';
  import { VueContext } from 'vue-context';
  import IjcoreUsersIcon from '@/components/IjcoreUsersIcon';

let instance = {};
export default {
  name: "Flowchart",
  data: function() {
    return {
      model: {
        FeatureStatusID: null,
        FeatureStatusOption: [],
        FeatureLink: '',
        nodeSelected: {},
        Employee: [],
        ResponseEmployee: null,
        ResponseEmployeeOption: []
      },
      resetDefault: {
        FeatureLink: '',
        FeatureStatusID: null,
        Employee: [],
        ResponseEmployee: null
      }
    };
  },
  props: {
    value: {
      type: Object,
      default (){
        return {
          node: [],
          constraint: []
        };
      }
    },
    isDraggable: {
      type: Boolean,
      default: false
    },
    sysFeatureStatus: {
      type: Array,
      default() {
        return [];
      }
    },
    EmployeeOption: {
      type: Array,
      default() {
        return [];
      }
    },
    isDataflow:{
      type: Boolean,
      default: false
    },
    isFinishDataflow: {
        type: Number,
        default: 0
    },
    dataflowArray:{
      type: Array,
      default(){
        return [];
      }
    },
    taskAssignUsers: [Array, Object],
    WFID: {
      type: Number,
      default: null
    }

  },
  components:{
    Select2,
    VueContext,
    IjcoreUsersIcon
  },
  created(){
    if (this.isDataflow) {
      this.updateStatusNodes();
    } else {
      let self = this;
      _.forEach(this.value.node, function (node, key) {
        delete self.value.node[key].isNextTask;
        delete self.value.node[key].isCompleted;
        delete self.value.node[key].inActive;
      });
    }
  },
  mounted() {
    this.renderFlowchart();
  },
  computed: {
    test1(){
      return true
    }
  },
  methods: {
    renderFlowchart(){
      let self = this;
      jsPlumb.jsPlumb.ready(function () {
        // load node position
        self.loadFlowchart();
        instance = window.jsp = jsPlumb.jsPlumb.getInstance({
          // default drag options
          DragOptions: { cursor: 'pointer', zIndex: 2000 },
          // the overlays to decorate each connection with.  note that the label overlay uses a function to generate the label text; in this
          // case it returns the 'labelText' member that we set on each connection in the 'init' method below.
          ConnectionOverlays: [
            // [ "Arrow", {
            //   location: 1,
            //   visible:true,
            //   width:11,
            //   length:11,
            //   id:"ARROW",
            //   events:{
            //     click:function() {
            //       console.log('Arrow');
            //     }
            //   }
            // }],
            [ "Label", {
              location: 0.3,
              id: "label",
              cssClass: "aLabel",
              events:{
                tap:function() {}
              }
            }
            ]
          ],
          Container: "canvas"
        });

        // var basicType = {
        //   connector: "StateMachine",
        //   paintStyle: { stroke: "red", strokeWidth: 4 },
        //   hoverPaintStyle: { stroke: "blue" },
        //   overlays: [
        //     "Arrow"
        //   ]
        // };
        // instance.registerConnectionType("basic", basicType);

        // this is the paint style for the connecting lines..
        var connectorPaintStyle = {
            strokeWidth: 2,
            stroke: "#00a2e8",
            joinstyle: "round",
            outlineStroke: "white",
            outlineWidth: 2
          },
          // .. and this is the hover style.
          connectorHoverStyle = {
            strokeWidth: 3,
            stroke: "#216477",
            outlineWidth: 5,
            outlineStroke: "white"
          },
          endpointHoverStyle = {
            fill: "#216477",
            stroke: "#216477"
          },
          // the definition of source endpoints (the small blue ones)
          sourceEndpoint = {
            // endpoint: "Dot",
            endpoint: "Blank",
            // paintStyle: {
            //   stroke: "#7AB02C",
            //   fill: "transparent",
            //   radius: 7,
            //   strokeWidth: 1
            // },
            isSource: true,
            // connector: [ "Flowchart", { stub: [40, 60], gap: 0, cornerRadius: 5, alwaysRespectStubs: true}],
            connector: [ "Flowchart", { stub: [10, 10], gap: 0, cornerRadius: 5, alwaysRespectStubs: true}],
            connectorStyle: connectorPaintStyle,
            hoverPaintStyle: endpointHoverStyle,
            connectorHoverStyle: connectorHoverStyle,
            dragOptions: {},
            maxConnections: -1,
            overlays: [
              [ "Label", {
                location: [0.5, 1.5],
                label: "Drag",
                cssClass: "endpointSourceLabel",
                visible:false
              } ]
            ]
          },
          // the definition of target endpoints (will appear when the user drags a connection)
          targetEndpoint = {
            endpoint: "Blank",
            paintStyle: { fill: "#7AB02C", radius: 7 },
            hoverPaintStyle: endpointHoverStyle,
            maxConnections: -1,
            dropOptions: { hoverClass: "hover", activeClass: "active" },
            isTarget: true,
            overlays: [
              [ "Label", { location: [0.5, -0.5], label: "Drop", cssClass: "endpointTargetLabel", visible:false } ]
            ]
          },
          init = function (connection) {
            // connection.getOverlay("label").setLabel(connection.sourceId.substring(15) + "-" + connection.targetId.substring(15));
            let sourceNode = _.find(self.value.node, ['NodeID', connection.sourceId]);
            let targetNode = _.find(self.value.node, ['NodeID', connection.targetId]);

            let constraint = _.find(self.value.constraint, {WFItemID: targetNode.WFItemID, WFPreItemID: sourceNode.WFItemID});
            if (constraint && !_.isEmpty(constraint)){
              if (!_.isEmpty(constraint.ConstraintLabel)) {
                connection.getOverlay("label").setLabel(constraint.ConstraintLabel);
              } else {
                connection.getOverlay('label').cssClass = 'd-none';
                // connection.getOverlay("label").setLabel('<i class="fa fa-user"></i>');
              }
            }
            // if (sourceNode.ProcessType != '2' && targetNode.ProcessType == '2') {
            //   connection.endpoints[1].anchor.x -= 0.2;
            // }
          };

        var _addEndpoints = function (toId, sourceAnchors, targetAnchors) {
          for (var i = 0; i < sourceAnchors.length; i++) {
            var sourceUUID = toId + sourceAnchors[i];
            instance.addEndpoint(toId, sourceEndpoint, {
              anchor: sourceAnchors[i], uuid: sourceUUID
            });
          }
          // for (var j = 0; j < targetAnchors.length; j++) {
          //   var targetUUID = toId + targetAnchors[j];
          //   instance.addEndpoint(toId, targetEndpoint, {
          //     anchor: targetAnchors[j],
          //     uuid: targetUUID,
          //   });
          //   // instance.addEndpoint(toId, { anchor: targetAnchors[j], uuid: targetUUID });
          // }
        };

        // suspend drawing and initialise.
        instance.batch(function () {
          // _addEndpoints("Window5", ["RightMiddle"], ["LeftMiddle"]);
          // _addEndpoints("Window4", ["TopCenter", "BottomCenter"], ["LeftMiddle", "RightMiddle"]);

          // custom
          _.forEach(self.value.node, function (node, key) {
            if (node.ProcessType != '3') {
              _addEndpoints(node.NodeID, ["RightMiddle", "TopCenter", "BottomCenter", "LeftMiddle"], ["LeftMiddle"]);
            } else {
              _addEndpoints(node.NodeID, ["RightMiddle", "TopCenter", "BottomCenter", "LeftMiddle"], ["LeftMiddle"]);
            }
          });

          // listen for new connections; initialise them the same way we initialise the connections at startup.
          instance.bind("connection", function (connInfo, originalEvent) {
            init(connInfo.connection);
          });

          // connect a few up
          // instance.connect({uuids: ["Window5BottomCenter", "Window3TopCenter"]});

          let switchCondition = true;
          _.forEach(self.value.constraint, function (constraint, key) {
            let node = _.find(self.value.node, ['WFItemID', constraint.WFItemID]);
            let preNode = _.find(self.value.node, ['WFItemID', Number(constraint.WFPreItemID)]);
            let cssClass = '';
            // set inactive for connection
            if (self.isDataflow && ((node && node.inActive && !node.isNextTask) || !preNode.isCompleted)) {
              cssClass = 'connection-inActive';
            }

            if (preNode && node) {
              let sourceAnchors = preNode.NodeID + 'RightMiddle';
              let targetAnchors = node.NodeID + 'LeftMiddle';
              if (constraint.SourceAnchors === 1) {
                sourceAnchors = preNode.NodeID + 'TopCenter';
              }else if (constraint.SourceAnchors === 2) {
                sourceAnchors = preNode.NodeID + 'BottomCenter';
              }else if (constraint.SourceAnchors === 3) {
                sourceAnchors = preNode.NodeID + 'LeftMiddle';
              }else {
                sourceAnchors = preNode.NodeID + 'RightMiddle';
              }

              if (constraint.TargetAnchors === 1) {
                targetAnchors = node.NodeID + 'TopCenter';
              }else if (constraint.TargetAnchors === 2) {
                targetAnchors = node.NodeID + 'BottomCenter';
              }else if (constraint.TargetAnchors === 3) {
                targetAnchors = node.NodeID + 'LeftMiddle';
              }else {
                targetAnchors = node.NodeID + 'RightMiddle';
              }

              if (node.NodeType === 1) {
                if (preNode.ProcessType != '3') {
                  // instance.connect({uuids: [preNode.NodeID + 'RightMiddle', node.NodeID + 'LeftMiddle']});
                  instance.connect({
                    // uuids: [preNode.NodeID + 'RightMiddle', node.NodeID + 'LeftMiddle'],
                    uuids: [sourceAnchors, targetAnchors],
                    overlays: [
                      // "Arrow",
                      // [ "Label", { label:"foo", location:0.25, id:"myLabel" } ]
                      ["Arrow", {
                        location: 1,
                        visible: true,
                        width: 8,
                        length: 8,
                        id: "ARROW",
                      }]],
                    cssClass: cssClass
                  });

                  // instance.connect({
                  //   source: preNode.NodeID,
                  //   target: node.NodeID,
                  //   anchors:["RightMiddle", "LeftMiddle" ],
                  //   endpoint:"Blank",
                  //   // endpointStyle:{ fillStyle: "yellow" }
                  //   connector: [ "Flowchart", { stub: [10, 10], gap: 0, cornerRadius: 5, alwaysRespectStubs: true}],
                  //   overlays:[
                  //     // "Arrow",
                  //     // [ "Label", { label:"foo", location:0.25, id:"myLabel" } ]
                  //     [ "Arrow", {
                  //       location: 1,
                  //       visible:true,
                  //       width:8,
                  //       length:8,
                  //       id:"ARROW",
                  //     }],
                  //   ],
                  // });

                } else {
                  // if (switchCondition) {
                  //   instance.connect({
                  //     uuids: [preNode.NodeID + 'RightMiddle', node.NodeID + 'LeftMiddle'],
                  //     overlays: [
                  //       ["Arrow", {
                  //         location: 1,
                  //         visible: true,
                  //         width: 8,
                  //         length: 8,
                  //         id: "ARROW",
                  //       }]],
                  //     cssClass: cssClass
                  //   });
                  // } else {
                  //   instance.connect({
                  //     uuids: [preNode.NodeID + 'BottomCenter', node.NodeID + 'LeftMiddle'],
                  //     overlays: [
                  //       ["Arrow", {
                  //         location: 1,
                  //         visible: true,
                  //         width: 8,
                  //         length: 8,
                  //         id: "ARROW",
                  //       }]],
                  //     cssClass: cssClass
                  //   });
                  // }
                  // switchCondition = !switchCondition;

                  instance.connect({
                    // uuids: [preNode.NodeID + 'RightMiddle', node.NodeID + 'LeftMiddle'],
                    uuids: [sourceAnchors, targetAnchors],
                    overlays: [
                      // "Arrow",
                      // [ "Label", { label:"foo", location:0.25, id:"myLabel" } ]
                      ["Arrow", {
                        location: 1,
                        visible: true,
                        width: 8,
                        length: 8,
                        id: "ARROW",
                      }]],
                    cssClass: cssClass
                  });
                }
              } else {
                instance.connect({uuids: [preNode.NodeID + 'RightMiddle', node.NodeID + 'LeftMiddle'], cssClass: cssClass});
              }
            }
          });

          // make all the window divs draggable
          if (self.isDraggable) instance.draggable(jsPlumb.jsPlumb.getSelector(".flowchart-task .jtk-node"), {grid: [1, 1]});
          // THIS task ONLY USES getSelector FOR CONVENIENCE. Use your library's appropriate selector
          // method, or document.querySelectorAll:
          //jsPlumb.draggable(document.querySelectorAll(".window"), { grid: [20, 20] });

          //
          // listen for clicks on connections, and offer to delete connections on click.
          //
          instance.bind("click", function (connection, originalEvent) {
            // if (confirm("Delete connection from " + conn.sourceId + " to " + conn.targetId + "?"))
            //   instance.detach(conn);
          });

          //
          // instance.bind("connectionDrag", function (connection) {
          //   console.log("connection " + connection.id + " is being dragged. suspendedElement is ", connection.suspendedElement, " of type ", connection.suspendedElementType);
          // });
          // //
          // instance.bind("connectionDragStop", function (connection) {
          //   console.log("connection " + connection.id + " was dragged");
          // });
          instance.bind("connectionMoved", function (connection) {
            console.log("connection " + connection.id + " was dragged");
          });
          //
          instance.bind("connectionMoved", function (params) {
            console.log("connection " + params.connection.id + " was moved");
          });
        });
        jsPlumb.jsPlumb.fire("jsPlumbTaskLoaded", instance);
      });
      // this.saveFlowchart();
    },
    getNameOfNode(){
      return __.stripHtml(this.model.nodeSelected.WFItemName);
    },
    // TODO: Add logic when click
    onClickNode(event, node) {
      if (node.ProcessType === 5 || node.ProcessType === 4 || this.isLockedDataflow()) return;
      if (this.isDataflow) {
        if ((event.target && event.target.className.includes('icon-item-undo')) || (event.target && event.target.parentNode.className.includes('icon-item-undo'))) return;
        if ((event.target && event.target.className.includes('icon-item-forward')) || (event.target && event.target.parentNode.className.includes('icon-item-forward'))) return;

        // TODO: remove this
        if (node.href && node.href !== '') {
          window.open(node.href, '_blank');
        }
        // this.onClickUpdateExecution(node);
      }else {
        if (this.isDraggable) {
          event.preventDefault();
          return false;
        } else {
          if (node.href && node.href !== '') {
            window.open(node.href, '_blank');
          }
        }
      }
    },
    onDbClickNode(event, node){
      if (!this.isDraggable) {
        event.preventDefault();
        return;
      }

      let self = this;
      this.model.nodeSelected = node;
      if (node.href && node.href !== '') {
        this.model.FeatureLink = node.href;
        this.resetDefault.FeatureLink = this.model.FeatureLink;
      }
      if (node.FeatureStatusID) {
        this.model.FeatureStatusID = node.FeatureStatusID;
        this.resetDefault.FeatureStatusID = node.FeatureStatusID;
      }

      if (node.Employee) {
        this.model.Employee = node.Employee;
        this.model.ResponseEmployee = node.ResponseEmployee;
        this.resetDefault.Employee = node.Employee;
        this.resetDefault.ResponseEmployee = node.ResponseEmployee;
        this.renderResponseEmployeeOption();
      } else {
        this.model.Employee = [];
        this.model.ResponseEmployee = null;
      }

      this.model.FeatureStatusOption = [];
      if (node.ProcessType === 1) {
        // let allFeatureStatus = _.filter(this.sysFeatureStatus, {
        //   FeatureID: Number(node.FeatureID),
        //   Inactive: 0
        // });
        let allFeatureStatus = this.sysFeatureStatus;
        _.forEach(allFeatureStatus, function (featureStatus, key) {
          let tmpObj = {};
          tmpObj.value = featureStatus.StatusID;
          tmpObj.text = featureStatus.StatusName;
          self.model.FeatureStatusOption.push(tmpObj);
        });
      } else if (node.ProcessType === 2) {
        _.forEach(this.sysFeatureStatus, function (featureStatus, key) {
          let tmpObj = {};
          tmpObj.value = featureStatus.StatusID;
          tmpObj.text = featureStatus.StatusName;
          self.model.FeatureStatusOption.push(tmpObj);
        });
      } else {
        return;
      }

      if (!this.model.FeatureStatusID && this.model.FeatureStatusOption[0]) {
        this.model.FeatureStatusID = this.model.FeatureStatusOption[0].value;
      }

      this.$refs['modal-feature-status'].show();
    },
    onCancelModalFeatureStatus(e){
      this.model.FeatureStatusID = this.resetDefault.FeatureStatusID;
      this.model.Employee = this.resetDefault.Employee;
      this.model.ResponseEmployee = this.resetDefault.ResponseEmployee;
      this.model.FeatureLink = this.resetDefault.FeatureLink;
      this.renderResponseEmployeeOption();
      e.preventDefault();
    },
    onHideModalFeatureStatus(event){

    },
    onSelectFeatureStatus(event){
      // let _indexOfNode = _.findIndex(this.value.node, ['WFItemID', this.model.nodeSelected.WFItemID]);
      // if (this.value.node[_indexOfNode]) {
      //   this.value.node[_indexOfNode].FeatureStatusID = Number(event);
      // }
    },
    onChangeEmployee(){
      this.renderResponseEmployeeOption();
    },

    renderResponseEmployeeOption(){
      let self = this;
      this.model.ResponseEmployeeOption = [];
      _.forEach(this.model.Employee, function (value, key) {
        let employee = _.find(self.EmployeeOption, ['id', Number(value)]);
        let tmpObj = {};
        if (_.isObject(employee)) {
          tmpObj.value = employee.id;
          tmpObj.text = employee.text;
          self.model.ResponseEmployeeOption.push(tmpObj);
        }
      });
    },

    onSaveModalFeatureStatus(){
      let _indexOfNode = _.findIndex(this.value.node, ['WFItemID', this.model.nodeSelected.WFItemID]);
      if (this.value.node[_indexOfNode]) {
        let tmpNode = this.value.node[_indexOfNode];
        tmpNode.FeatureStatusID = Number(this.model.FeatureStatusID);
        tmpNode.Employee = this.model.Employee;
        tmpNode.ResponseEmployee = this.model.ResponseEmployee;
        tmpNode.href = this.model.FeatureLink;
        this.value.node[_indexOfNode] = tmpNode;
      }
    },
    saveFlowchart(){
      let self = this;
      $(".jtk-node").each(function (idx, elem) {
        let $elem = $(elem);
        // var endpoints = instance.getEndpoints($elem.attr('id'));
        // console.log('endpoints of '+$elem.attr('id'));
        let elemID = $elem.attr('id');
        let _indexOfNode = _.findIndex(self.value.node, ['NodeID', elemID]);
        if (self.value.node[_indexOfNode]) {
          self.value.node[_indexOfNode].PositionX = (parseInt($elem.css("left"), 10) > 0) ? parseInt($elem.css("left"), 10) : 0;
          self.value.node[_indexOfNode].PositionY = (parseInt($elem.css("top"), 10) > 0) ? parseInt($elem.css("top"), 10) : 0;
        }

      });

      this.$emit('input', this.value);
      return this.value;
    },
    loadFlowchart(){
      _.forEach(this.value.node, function (node, key) {
        if ($('#' + node.NodeID).length) {
          $('#' + node.NodeID).css({
            left: (node.PositionX) ? node.PositionX: 0,
            top: (node.PositionY) ? node.PositionY : 0
          });
        }
      });
    },
    updateStatusNodes(){
      let self = this;
      _.forEach(this.value.node, function (node, key) {
        self.value.node[key].isNextTask = false;
        let itemExist = _.find(self.dataflowArray, ['LineIDTemp', node.LineIDTemp]);
        if (!itemExist) {
          itemExist = _.find(self.dataflowArray, ['WFItemID', node.WFItemID]);
        }
        if (key === 0){
          self.value.node[key].isCompleted = true;
        }
        if (_.isObject(itemExist)) {
          self.value.node[key].inActive = false;
          if (itemExist.StatusCompleted) {
            self.value.node[key].isCompleted = true;
          } else {
            self.value.node[key].isCompleted = false;
          }

          // update Feature
          if (!self.value.node[key].FeatureID) {
            if (itemExist.FeatureID) self.value.node[key].FeatureID = itemExist.FeatureID;
            if (itemExist.FeatureKey) self.value.node[key].FeatureKey = itemExist.FeatureKey;
            if (itemExist.FeatureName) self.value.node[key].FeatureName = itemExist.FeatureName;
          }

          if (itemExist.TransID) {
            self.value.node[key].TransID = itemExist.TransID;
            self.value.node[key].TransNo = itemExist.TransNo;
            self.value.node[key].TransComment = itemExist.TransComment;
          }

          // update task
          if (itemExist.TaskID) {
            self.value.node[key].TaskID = itemExist.TaskID;
            self.value.node[key].TaskName = itemExist.TaskName;
            self.value.node[key].StatusDescription = itemExist.StatusDescription;
            self.value.node[key].StartDate = itemExist.StartDate;
            self.value.node[key].DueDate = itemExist.DueDate;
            self.value.node[key].PercentCompleted = itemExist.PercentCompleted;
          }

        } else {
          self.value.node[key].inActive = true;
        }
      });

      _.forEach(this.value.node, function (node, key) {
        // status for node condition and
        let allConstraintsAnd = _.filter(self.value.constraint, {
          WFItemID: node.WFItemID,
          NodeType: 2
        });
        let activeAnd = true;
        if (allConstraintsAnd && allConstraintsAnd.length) {
          _.forEach(allConstraintsAnd, function (constraintAnd, key) {
            let preDataflow = _.find(self.dataflowArray, ['LineIDTemp', constraintAnd.WFPreItemID]);
            if (!preDataflow) {
              preDataflow = _.find(self.dataflowArray, ['WFItemID', constraintAnd.WFPreItemID]);
            }
            if (_.isObject(preDataflow)) {
              if (!preDataflow.StatusCompleted) {
                activeAnd = false;
                return false;
              }
            } else {
              activeAnd = false;
              return false;
            }
          });
          if (activeAnd) {
            self.value.node[key].inActive = false;
            self.value.node[key].isCompleted = true;
          }
        }

        // status for node condition or
        let allConstraintsOr = _.filter(self.value.constraint, {
          WFItemID: node.WFItemID
        });
        let activeOr = true;
        if (allConstraintsOr && allConstraintsOr.length) {
          _.forEach(allConstraintsOr, function (constraintOr, key) {
            let preDataflow = _.find(self.dataflowArray, ['LineIDTemp', constraintOr.WFPreItemID]);
            if (!preDataflow) {
              preDataflow = _.find(self.dataflowArray, ['WFItemID', constraintOr.WFPreItemID]);
            }
            if (_.isObject(preDataflow)) {
              if (!preDataflow.StatusCompleted) {
                activeOr = false;
              } else {
                activeOr = true;
                return false;
              }
            } else {
              activeOr = false;
            }
          });
          if (activeOr) {
            self.value.node[key].inActive = false;
            self.value.node[key].isCompleted = true;
          }
        }

        // status for node condition if

      });

      // status for node condition and
      // let allNodeAnd = _.filter(this.value.node, function (node) {
      //   return node.WFItemID > 6000 && node.WFItemID < 8000;
      // });
      // _.forEach(allNodeAnd, function (nodeAnd, key) {
      //   let allConstraintsAnd = _.filter(self.value.constraint, ['WFItemID', nodeAnd.WFItemID]);
      //   let nodeIndex = _.findIndex(self.value.node, ['WFItemID', nodeAnd.WFItemID]);
      //   let activeAnd = true;
      //
      //   _.forEach(allConstraintsAnd, function (constraintAnd, key) {
      //     let preDataflow = _.find(self.dataflowArray, ['LineIDTemp', constraintAnd.WFPreItemID]);
      //     if (!preDataflow) {
      //       preDataflow = _.find(self.dataflowArray, ['WFItemID', constraintAnd.WFPreItemID]);
      //     }
      //     if (_.isObject(preDataflow)) {
      //       if (!preDataflow.StatusCompleted) {
      //         activeAnd = false;
      //         return false;
      //       }
      //     } else {
      //       activeAnd = false;
      //       return false;
      //     }
      //   });
      //   if (activeAnd) {
      //     self.value.node[nodeIndex].inActive = false;
      //     self.value.node[nodeIndex].isCompleted = true;
      //   }
      // });

      // status for node condition or
      // let allNodeOr = _.filter(this.value.node, function (node) {
      //   return node.WFItemID > 8000 && node.WFItemID < 10000;
      // });
      // _.forEach(allNodeOr, function (nodeOr, key) {
      //   let allConstraintsOr = _.filter(self.value.constraint, ['WFItemID', nodeOr.WFItemID]);
      //   let nodeIndex = _.findIndex(self.value.node, ['WFItemID', nodeOr.WFItemID]);
      //   let activeOr = true;
      //
      //   _.forEach(allConstraintsOr, function (constraintOr, key) {
      //     let preDataflow = _.find(self.dataflowArray, ['LineIDTemp', constraintOr.WFPreItemID]);
      //     if (!preDataflow) {
      //       preDataflow = _.find(self.dataflowArray, ['WFItemID', constraintOr.WFPreItemID]);
      //     }
      //     if (_.isObject(preDataflow)) {
      //       if (!preDataflow.StatusCompleted) {
      //         activeOr = false;
      //       } else {
      //         activeOr = true;
      //         return false;
      //       }
      //     } else {
      //       activeOr = false;
      //     }
      //   });
      //   if (activeOr) {
      //     self.value.node[nodeIndex].inActive = false;
      //     self.value.node[nodeIndex].isCompleted = true;
      //   }
      // });

      // status for node condition if
      let allNodeIf = _.filter(this.value.node, function (node) {
        return node.WFItemID > 10000;
      });

      // status for current node
      // kiểm tra nút chọn nút tiếp theo
      let processingDataflow = _.last(this.dataflowArray);
      let processingNode = _.find(this.value.node, ['LineIDTemp', processingDataflow.LineIDTemp]);
      if (!processingNode) {
        processingNode = _.find(this.value.node, ['WFItemID', processingDataflow.WFItemID]);
      }
      let nextConstraints = _.filter(this.value.constraint, ['WFPreItemID', processingDataflow.LineIDTemp]);
      if (!nextConstraints.length) {
        nextConstraints = _.filter(this.value.constraint, ['WFPreItemID', processingDataflow.WFItemID]);
      }

      // nếu là chức năng điều kiện
      if (processingNode && processingNode.ProcessType === 3) {
        _.forEach(nextConstraints, function (nextConstraint, key) {
          let nextIndex = _.findIndex(self.value.node, ['WFItemID', nextConstraint.WFItemID]);
          self.value.node[nextIndex].inActive = false;
          self.value.node[nextIndex].isConstraintSwitch = true;
        });
      }

      // check trường hợp điều kiện rẽ nhánh
      if (processingDataflow.StatusCompleted) {
        // next constraint
        _.forEach(nextConstraints, function (nextConstraint, key) {
          let checkCreate = true;
          // kiểm trả xem tất cả các ràng buộc "and" trước đã hoàn thành chưa
          let allConstraintsAnd = [];

          // constraint and
          let realNextNode = null;

            // if (nextConstraint.WFItemID > 6000 && nextConstraint.WFItemID < 8000) {
            //   let realConstraint = _.find(self.value.constraint, ['WFPreItemID', nextConstraint.WFItemID]);
            //   realNextNode = _.find(self.value.node, ['WFItemID', realConstraint.WFItemID]);
            //   allConstraintsAnd = _.filter(self.value.constraint, ['WFItemID', realNextNode.LineIDTemp + 6000]);
            // } else {
            //   allConstraintsAnd = _.filter(self.value.constraint, {
            //     'WFItemID': nextConstraint.WFItemID,
            //     ConstraintCondition: 1
            //   });
            //   realNextNode = nextConstraint;
            // }
          allConstraintsAnd = _.filter(self.value.constraint, {
            'WFItemID': nextConstraint.WFItemID,
            ConstraintCondition: 1
          });
          realNextNode = nextConstraint;
          let nextIndex = _.findIndex(self.value.node, ['LineIDTemp', realNextNode.WFItemID]);
          if (nextIndex < 0) {
            nextIndex = _.findIndex(self.value.node, ['WFItemID', realNextNode.WFItemID]);
          }

          _.forEach(allConstraintsAnd, function (constraintAnd, key) {
            let preDataflow = _.find(self.dataflowArray, ['LineIDTemp', constraintAnd.WFPreItemID]);
            if (!preDataflow) {
              preDataflow = _.find(self.dataflowArray, ['WFItemID', constraintAnd.WFPreItemID]);
            }
            if (_.isObject(preDataflow)) {
              if (!preDataflow.StatusCompleted) {
                checkCreate = false;
              }
            }else {
                let preNodeIndex = _.findIndex(self.value.node, ['LineIDTemp', constraintAnd.WFPreItemID]);
                if (preNodeIndex < 0) {
                  preNodeIndex = _.findIndex(self.value.node, ['WFItemID', constraintAnd.WFPreItemID]);
                }
                if (!self.value.node[preNodeIndex].isCompleted) {
                    self.value.node[preNodeIndex].isNextTask = true;
                }
                checkCreate = false;
            }
          });

          if (checkCreate) {
            self.value.node[nextIndex].isNextTask = true;
              if (nextConstraint.ConstraintCondition === 3) {
                  self.value.node[nextIndex].isConstraintSwitch = true;
              }
          }
        });
      }
    },
    onClickUpdateExecution(node) {
      let dataflow = _.find(this.dataflowArray, ['LineIDTemp', node.LineIDTemp]);
      if (!dataflow) {
        dataflow = _.find(this.dataflowArray, ['WFItemID', node.WFItemID]);
      }

      if (!_.isObject(dataflow)) {
        let newDataflow = {};
        newDataflow.TaskID = null;
        newDataflow.LineIDTemp = node.LineIDTemp;
        newDataflow.WFItemID = node.WFItemID;
        this.$emit('onCreateFeature', newDataflow);
      } else {
        // this.$emit('onShowModalUpdate', dataflow);
        this.onShowModalTask(6, node);
      }
    },
    onClickRedoExecution(node){
      this.$emit('onRedoExecution', node);
    },
    onClickStartNode(nodeStart){
      if (!this.isLockedDataflow()) this.$emit('onStartDataflow', nodeStart);
    },
    onContextmenuStartNode(){
      this.$emit('onStartContextmenu');
    },
    onCreateDataflow(){
      this.$emit('onCreateDataflow');
    },
    onSelectDataflow(){
      $('input[name="input-dataflow"]').trigger('click');
      this.$emit('onSelectDataflow');
    },
    onEditWorkflow(){
      this.$emit('onEditWorkflow');
    },
    onViewDataflow(){},
    onShowModalTask(type, node){
      if (this.isLockedDataflow()) return;
      let dataflow = _.find(this.dataflowArray, ['LineIDTemp', node.LineIDTemp]);
      if (!dataflow) {
        dataflow = _.find(this.dataflowArray, ['WFItemID', node.WFItemID]);
      }

      if (dataflow) {
        this.$emit('onShowModalTask', {type: type, dataflow: dataflow});
      }
    },
    onShowModalTrans(node){
      let dataflow = _.find(this.dataflowArray, ['LineIDTemp', node.LineIDTemp]);
      if (!dataflow) {
        dataflow = _.find(this.dataflowArray, ['WFItemID', node.WFItemID]);
      }
      if (dataflow) {
        this.$emit('onShowModalTrans', dataflow);
      }
    },
    onShowModalLinkTrans(node){
      let dataflow = _.find(this.dataflowArray, ['LineIDTemp', node.LineIDTemp]);
      if (!dataflow) {
        dataflow = _.find(this.dataflowArray, ['WFItemID', node.WFItemID]);
      }
      if (dataflow) {
        this.$emit('onShowModalLinkTrans', dataflow);
      }
    },
    onClickEndNode(nodeEnd){
      if (!this.isLockedDataflow()) this.$emit('onEndDataflow', nodeEnd);
    },
    isLockedDataflow() {
      let check = false;
      if (this.isFinishDataflow === 1) {
        this.$bvToast.toast('Quy trình đã bị khóa', {
          title: 'Thông báo',
          variant: 'warning',
          solid: true
        });
        check = true;
      }

      return check;
    }
  },
  watch: {
    value() {}
  }
};
</script>
<style src="jsplumb/css/jsplumbtoolkit-defaults.css"></style>
<style>
  #modal-feature-status .select2-container {
    width: 100% !important;
  }
</style>

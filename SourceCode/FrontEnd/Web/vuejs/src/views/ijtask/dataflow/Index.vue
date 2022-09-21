<template>
    <div>
        <div class="main-entry component-dataflow">
            <div class="main-header">
                <div class="main-header-padding">
                    <b-row class="mb-2">
                        <b-col md="6" class="col-24">
                            <div class="main-header-item main-header-name">
                                <span class="text-capitalize"><i class="fa fa-list-ol mr-2"></i>Luồng công việc</span>
                            </div>
                        </b-col>
                        <b-col md="6" class="col-24"></b-col>
                    </b-row>

                    <b-row class="mb-2">
                        <b-col class="col-lg-20 col-md-24 col-sm-24 col-24 mb-2 mb-lg-0 d-lg-flex justify-content-start align-items-center">
                            <div class="main-header-item main-header-actions flex-wrap">
                                <b-button class="main-header-action mr-2" variant="primary" size="md" @click="onCreateDataflow">
                                    <i class="fa fa-plus"></i> Thêm
                                </b-button>
                                <b-dropdown variant="primary" id="dropdown-actions" class="main-header-action mr-2" text="Thao tác">
                                    <b-dropdown-item>In</b-dropdown-item>
                                    <li class="dropdown b-dropdown dropright" >
                                        <a class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" data-toggle="dropdown" href="#">Nhập</a>
                                        <ul role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-menu m-0">
                                            <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Excel</a></li>
                                            <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Cvs</a></li>
                                            <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Xml</a></li>
                                            <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Json</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown b-dropdown dropright">
                                        <a class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" data-toggle="dropdown" href="#">Xuất</a>
                                        <ul role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-menu m-0">
                                            <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Excel</a></li>
                                            <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Cvs</a></li>
                                            <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Xml</a></li>
                                            <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Json</a></li>
                                        </ul>
                                    </li>
                                </b-dropdown>
                                <ijcore-select-dropdown class="mr-4 dropdown-workflow" v-model="model.WFID" :options="model.workflowOption" placeholder="Chọn quy trình" @selected="onChangeWorkflow($event)"></ijcore-select-dropdown>
                                <div :title="model.WFName"><strong class="text-nowrap">{{model.WFName}}</strong></div>
                            </div>
                        </b-col>
                        <b-col class="col-lg-4 col-md-24 col-sm-24 col-24">
                          <div class="main-header-item main-header-icons">
                            <b-button-group id="main-header-views" class="main-header-views">
                              <b-button id="tooltip-view-back-to-list" @click="onBackToList" class="main-header-view"><i class="fa fa-list"></i></b-button>
                              <b-tooltip container="main-header-views" target="tooltip-view-back-to-list">Danh sách</b-tooltip>
                            </b-button-group>
                            <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
                              <sidebar-toggle class="d-md-down-none btn" display="lg" :defaultOpen=true />
                            </div>
                          </div>
                        </b-col>
                    </b-row>

                </div>
            </div>
            <div class="main-body">
                <div class="main-dataflow">
                    <div class="dataflow-name" style="background: #fff; padding: 1rem; font-weight: 500" v-if="model.taskArray.length === 1 || (model.taskArray.length !== 1 && stage.activeTab !== 1)">
                        {{model.taskArray[0].TaskName}}
                    </div>
                    <div ref="flowchartDiagram" class="flowchart-diagram">
                        <ijcore-flowchart
                            ref="flowchart"
                            v-if="stage.showFlowchart && model.taskArray.length !== 1"
                            :WFID="Number(model.WFID)"
                            @onStartDataflow="onStartDataflow"
                            @onEditWorkflow="onEditWorkflow"
                            @onCreateDataflow="onCreateDataflow"
                            v-model="model.jsonFlowchart"></ijcore-flowchart>
                        <ijcore-flowchart
                            ref="flowchart"
                            v-if="stage.showFlowchart && model.taskArray.length === 1"
                            :is-dataflow="true"
                            :dataflow-array="model.dataflowArray"
                            :is-finish-dataflow="model.taskArray[0].Locked"
                            :task-assign-users="model.taskAssignUsers"
                            :WFID="Number(model.WFID)"
                            @onShowModalUpdate="showModalExecutionForm"
                            @onCreateFeature="onCreateFeature"
                            @onStartDataflow="onStartDataflow"
                            @onEndDataflow="onEndDataflow"
                            @onRedoExecution="onRedoExecution"
                            @onEditWorkflow="onEditWorkflow"
                            @onCreateDataflow="onCreateDataflow"
                            @onClickNode="onClickNode"
                            @onSelectDataflow="onSelectDataflow"
                            @onShowModalTask="onShowModalTask"
                            @onShowModalTrans="onShowModalTrans"
                            @onShowModalLinkTrans="onShowModalLinkTrans"
                            v-model="model.jsonFlowchart"></ijcore-flowchart>
                    </div>
                    <div class="entry-dataflow" id="entry-dataflow">
                        <b-card no-body>
                            <b-tabs card>
                                <b-tab :active="stage.activeTab === 1" @click="stage.activeTab = 1" title="Luồng công việc">
                                    <div class="task-dataflow" v-if="model.WFID">
                                        <div class="dataflow-filter mb-4 d-flex align-items-center justify-content-between flex-lg-nowrap flex-md-wrap flex-sm-wrap flex-wrap">
                                            <div style="flex: 1 1 auto" class="dataflow-filter-left mr-lg-2 mb-lg-0 mr-md-0 mb-md-2 mb-sm-2 mb-2">
                                                <ijcore-modal-search-input
                                                        v-model="model.filterDataflow"
                                                        :select-fields-api="[
                                                        {field: 'TaskID', showInTable: false, key: 'TaskID'},
                                                        {field: 'DFKey', fieldForSelected: 'id', showInTable: false, key: 'DFKey'},
                                                        {field: 'TaskName', fieldForSelected: 'name', showInTable: true, label: 'Luồng công việc', key: 'TaskName', sortable: true, thClass: 'd-none'}
                                                    ]"
                                                        :search-fields-api="[{field: 'TaskName', placeholder: 'Nhập tên luồng công việc', name: 'TaskName', class: '', style: ''},]"
                                                        tableApi="contract"
                                                        :request-data="{WFID: model.WFID}"
                                                        ref="myModalSearchInput"
                                                        id-modal="myModalSearchInput"
                                                        :url-api="appRootApi + '/task/api/dataflow/getAllDataflow'"
                                                        name-input="input-dataflow"
                                                        title-modal="Luồng công việc" size-modal="lg">
                                                </ijcore-modal-search-input>
                                            </div>

                                            <div class="d-flex align-items-center dataflow-filter-right flex-wrap flex-sm-wrap flex-md-nowrap flex-lg-nowrap">
                                                <ijcore-date-range class="mr-0 mr-lg-2 mr-md-2 mr-sm-0 mb-2 mb-sm-2 mb-md-0 mb-lg-0" v-model="model.filterDataflowDateRange"></ijcore-date-range>
                                                <b-form-select class="mr-0 mr-lg-2 mr-md-2 mr-sm-0 mb-2 mb-sm-2 mb-md-0 mb-lg-0" v-model="model.filterDataflowStatus" :options="[{value: null, text: 'Chọn trạng thái'}, {value: 1, text: 'Chưa thực hiện'}, {value: 2, text: 'Đang thực hiện'}, {value: 3, text: 'Đã hoàn thành'}, {value: 4, text: 'Đang đợi ý kiến'}, {value: 5, text: 'Từ chối'}]"></b-form-select>
                                                <ijcore-select2-server
                                                        v-model="model.filterTaskAssignUser"
                                                        :url="appRootApi + '/task/api/common/get-employee'"
                                                        id-name="EmployeeID"
                                                        text-name="EmployeeName"
                                                        placeholder="Chọn người thực hiện"
                                                        :allowClear="true"
                                                        :delay="500">
                                                </ijcore-select2-server>
                                            </div>
                                        </div>

                                        <div class="dataflow-item mb-2" v-for="(task, key) in model.taskArray">
                                            <div class="row mb-2">
                                                <div class="col-2 col-lg-1"></div>
                                                <div class="col-22 col-lg-22" style="font-weight: 500">
                                            <span class="task-name" style="cursor: pointer" @contextmenu.prevent="$refs.contextmenuTask.open($event, task)">
                                                {{task.TaskName}}
                                            </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-8 col-lg-2 col-md-4 col-sm-2">
                                                    <div class="task-icons">
                                                        <span class="mr-2">
                                                            <i class="fa fa-question" title="Chưa thực hiện" v-if="task.Status === 1 || task.Status === null"></i>
                                                            <i class="fa fa-assistive-listening-systems" title="Đang chờ" v-if="task.Status === 2"></i>
                                                            <i class="fa fa-spinner" title="Đang thực hiện" v-if="task.Status === 3"></i>
                                                            <i class="fa fa-check" title="Đã hoàn thành" v-if="task.Status === 6"></i>
                                                            <i class="fa fa-times" title="Hủy bỏ" v-if="task.Status === 5"></i>

                                                        </span>
                                                        <span @click="onShowTabTaskComment(task)"><i class="fa fa-commenting-o" title="Thảo luận"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-16 col-lg-22 col-md-20 col-sm-20">
                                                  <div class="stepwizard">
                                                    <div v-if="dataflow.ProcessType !== 3 && dataflow.ProcessType !== 4 && dataflow.ProcessType !== 5" class="stepwizard-step" v-for="(dataflow, index) in filterDataflowByTask(task)">
                                                      <button :id="'popover-target-' + dataflow.TaskID" :title="dataflow.FeatureStatusTitle" type="button" class="btn btn-primary btn-circle" @click="onShowModalTask({type: 6, dataflow: dataflow})" @contextmenu.prevent="$refs.contextmenuTaskChildren.open($event, {task: task, taskChild: dataflow})">
<!--                                                                <i class="fa fa-clock-o" v-if="dataflow.Status === 2"></i>-->
                                                          <i class="fa fa-spinner" v-if="!dataflow.StatusCompleted"></i>
<!--                                                                <i class="fa fa-play" v-if="dataflow.Status === 4"></i>-->
<!--                                                                <i class="fa fa-times" v-if="dataflow.Status === 5"></i>-->
                                                          <i class="fa fa-check" v-if="dataflow.StatusCompleted"></i>
                                                      </button>
                                                      <p v-html="dataflow.WFItemName"></p>
                                                      <b-popover :target="'popover-target-' + dataflow.TaskID" triggers="hover" placement="left">
                                                        <template #title>{{dataflow.TaskName | stripHtml}}</template>
                                                        <div>- Trạng thái: {{dataflow.StatusDescription}}</div>
                                                        <div>- Người thực hiện: <ijcore-users-icon :all-users="model.taskAssignUsers" filter-name="TaskID" :filter-value="dataflow.TaskID" :number="6"></ijcore-users-icon></div>
                                                        <div>- Ngày bắt đầu: {{dataflow.StartDate | convertServerDateToClientDate}}</div>
                                                        <div>- Hạn hoàn thành: {{dataflow.DueDate | convertServerDateToClientDate}}</div>
                                                        <div>- Phần trăm hoàn thành: <b-badge :variant="(dataflow.PercentCompleted <= 0) ? 'warning' : (dataflow.PercentCompleted > 0 && dataflow.PercentCompleted < 100) ? 'primary' : 'success'">{{dataflow.PercentCompleted}}</b-badge></div>
                                                      </b-popover>
                                                    </div>
                                                  </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="dataflow-pagination" v-if="model.WFID && stage.showDataflowPagination">
                                            <b-pagination
                                                    v-model="dataflowCurrentPage"
                                                    :total-rows="rows"
                                                    :per-page="dataflowPerPage"
                                                    aria-controls="my-table"
                                                    size="md"
                                            ></b-pagination>
                                        </div>
                                    </div>
                                </b-tab>
                                <b-tab :active="stage.activeTab === 2" @click="onShowTabTaskDetail(model.taskArray[0])" title="Đầu mục công việc">
                                    <div class="task-detail" v-if="model.taskSelected">
                                        <div class="task-parent" v-if="model.taskDetail[0]">
                                            <div class="row mb-2">
                                                <div class="col-lg-12 col-md-24 col-sm-24 col-24">
                                                    <div>
                                                        <span style="width: 15%;" class="mr-2 d-inline-block">Trạng thái</span>
                                                        <span>{{constant.taskStatus[model.taskDetail[0].Status]}}</span>
                                                    </div>
                                                    <div>
                                                        <span style="width: 15%;" class="mr-2 d-inline-block">Ngày tạo</span>
                                                        <span>{{model.taskDetail[0].CreateDate | convertServerDateToClientDate}}</span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-24 col-sm-24 col-24">
                                                    <div>
                                                        <span style="width: 30%" class="mr-2 d-inline-block">Ngày bắt đầu</span>
                                                        <span>{{model.taskDetail[0].StartDate | convertServerDateToClientDate}}</span>
                                                    </div>
                                                    <div>
                                                        <span style="width: 30%" class="mr-2 d-inline-block">Ngày hoàn thành</span>
                                                        <span>{{model.taskDetail[0].DueDate | convertServerDateToClientDate}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table b-table table-sm table-bordered">
                                            <thead>
                                            <tr>
                                                <th scope="col" class="text-center">Tên công việc</th>
                                                <th scope="col" style="width: 10%" class="text-center">Ưu tiên</th>
                                                <th scope="col" style="width: 10%" class="text-center">Ngày tạo</th>
                                                <th scope="col" style="width: 10%" class="text-center">Ngày bắt đầu</th>
                                                <th scope="col" style="width: 10%" class="text-center">Ngày hoàn thành</th>
                                                <th scope="col" style="width: 12%" class="text-center">Người thực hiện</th>
                                                <th scope="col" style="width: 10%" class="text-center">Tình trạng</th>
                                                <th scope="col" style="width: 5%" class="text-center" title="Tỉ lệ hoàn thành">% HT</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-if="index !== 0 && detail.ProcessType !== 3" v-for="(detail, index) in model.taskDetail">
                                                <td :title="detail.TaskName">{{detail.TaskName | stripHtml}}</td>
                                                <td>{{constant.taskPriority[detail.Priority]}}</td>
                                                <td>{{detail.CreateDate | convertServerDateToClientDate}}</td>
                                                <td>{{detail.StartDate | convertServerDateToClientDate}}</td>
                                                <td>{{detail.DueDate | convertServerDateToClientDate}}</td>
                                                <td>
                                                    <ijcore-users-icon :all-users="model.taskAssignUsers" filter-name="TaskID" :filter-value="detail.TaskID" :number="4"></ijcore-users-icon>
                                                </td>
                                                <td>
                                                    <span v-if="detail.PercentCompleted <= 0">Chưa thực hiện</span>
                                                    <span v-if="detail.PercentCompleted > 0 && detail.PercentCompleted < 100">Đang thực hiện</span>
                                                    <span v-if="detail.PercentCompleted >= 100">Đã hoàn thành</span>
                                                </td>
                                                <td :title="detail.PercentCompleted">
                                                  <div class="d-inline-flex align-items-center justify-content-end">
                                                    <b-badge :variant="(detail.PercentCompleted <= 0) ? 'warning' : (detail.PercentCompleted > 0 && detail.PercentCompleted < 100) ? 'primary' : 'success'">{{detail.PercentCompleted}}</b-badge>
                                                  </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </b-tab>
                                <b-tab :active="stage.activeTab === 3" @click="onShowTabTaskSchedule(model.taskArray[0])" title="Nhật trình công việc">
                                    <div class="task-timeline" v-if="model.taskSelected">
                                        <ul class="timeline">
                                            <li v-for="(schedule, index) in model.taskSchedule">
                                                <div class="timeline-date">{{schedule.TransDate | convertTimeToDateName}}</div>
                                                <div class="d-flex">
                                                    <div class="timeline-time mr-2">{{schedule.TransDate | convertTimeFromDatetime}}</div>
                                                    <div class="user-icons" v-if="schedule.EmployeeID">
                                                        <div class="user-icon" :title="schedule.EmployeeName">
                                                            <img :src="$store.state.appRootApi + schedule.Avata" :alt="schedule.EmployeeName"/>
                                                        </div>
                                                    </div>
                                                    <ijcore-users-icon v-else :all-users="model.taskScheduleUsers" filter-name="TaskID" :filter-value="schedule.TaskID" :number="6"></ijcore-users-icon>
                                                </div>
                                                <div class="timeline-name"><span>{{schedule.WFItemName}}</span></div>
                                                <div class="timeline-status">
                                                    <span>{{schedule.StatusDescription}}</span>
                                                </div>
                                                <div class="timeline-description">
                                                    <span>{{schedule.Description}}</span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </b-tab>
                                <b-tab :active="stage.activeTab === 4" @click="onShowTabTaskComment(model.taskArray[0])" title="Trao đổi">
                                    <div class="task-comment" v-if="model.taskComment">
                                        <div class="mb-2">
                                        </div>
                                        <div class="task-comment-feature mb-4 d-flex align-items-center">
                                            <ijcore-select-dropdown class="mr-4" v-model="model.taskCommentID" :options="model.taskCommentOption" placeholder="Công việc" @selected="onChangeTaskComment($event)"></ijcore-select-dropdown>
                                            <span class="text-bold" v-if="model.taskComment.TaskID !== model.taskSelected.TaskID">{{model.taskComment.TaskName | stripHtml}}</span>
                                        </div>
                                        <chat-category-comment
                                          :CategoryKey="{task: model.taskComment.TaskID}"
                                          Category="task" :CategoryID="model.taskComment.TaskID"
                                          :is-dataflow="true"
                                          v-if="stage.activeTab === 4"></chat-category-comment>
                                    </div>
                                </b-tab>
                            </b-tabs>
                        </b-card>
                    </div>
                </div>
                <vue-context ref="contextmenuTask" class="contextmenu contextmenu-task">
                    <template slot-scope="task">
                        <li>
                            <a role="menuitem" @click.prevent="onShowTabTaskDetail(task.data)">Đầu mục công việc</a>
                        </li>
                        <li>
                            <a role="menuitem" @click.prevent="onShowTabTaskSchedule(task.data)">Nhật trình công việc</a>
                        </li>
                        <li>
                            <a role="menuitem" @click.prevent="onShowTabTaskComment(task.data)">Trao đổi</a>
                        </li>
                    </template>
                </vue-context>
                <vue-context ref="contextmenuTaskChildren">
                    <template slot-scope="params">
                        <li><a role="menuitem" @click="onShowModalTask({type: 1, dataflow: params.data.taskChild})">Công việc</a></li>
                        <b-dropdown-divider></b-dropdown-divider>
                        <li><a role="menuitem" @click="onShowModalTask({type: 2, dataflow: params.data.taskChild})">Thông tin chung</a></li>
                        <li><a role="menuitem" @click="onShowModalTask({type: 3, dataflow: params.data.taskChild})">Danh mục liên kết</a></li>
                        <li><a role="menuitem" @click="onShowModalTask({type: 4, dataflow: params.data.taskChild})">Giao việc</a></li>
                        <li><a role="menuitem" @click="onShowModalTask({type: 5, dataflow: params.data.taskChild})">Yêu cầu</a></li>
                        <li><a role="menuitem" @click="onShowModalTask({type: 6, dataflow: params.data.taskChild})">Thực hiện</a></li>
                        <li><a role="menuitem" @click="onShowModalTask({type: 7, dataflow: params.data.taskChild})">Kiểm tra</a></li>
                        <li><a role="menuitem" @click="onShowModalTask({type: 8, dataflow: params.data.taskChild})">Chi phí</a></li>
                        <li><a role="menuitem" @click="onShowModalTask({type: 9, dataflow: params.data.taskChild})">Tệp</a></li>
                        <li><a role="menuitem" @click="onShowModalTask({type: 10, dataflow: params.data.taskChild})">Phim</a></li>
                    </template>
                </vue-context>
            </div>
            <div class="main-footer">
                <div class="d-flex flex-wrap justify-content-between align-items-center m-0">
                    <div class="main-footer-pagination">
                        <div class="overflow-auto"></div>
                    </div>
                    <div class="total-item"></div>
                </div>
            </div>
        </div>
        <task-general
          v-model="model.TaskModal"
          v-if="stage.showModalTaskGeneral" title="Công việc" :is-dataflow="true"
          @onReloadDataflow="fetchDataflow(true)"
          :per="model.TaskPer"
          @onHideModalTask="onHideModalTask"></task-general>
        <task-datalist
          v-model="model.TaskLinkModal"
          v-if="stage.showModalTaskLink"
          :is-dataflow="true"
          :title="'Danh mục liên kết'"
          :Task="model.TaskModal"
          :per="model.TaskPerLink"
          @onHideModalTask="onHideModalTask"></task-datalist>

        <task-assign
          v-if="stage.showModalTaskAssign"
          v-model="model.TaskAssign"
          :title="'Giao việc'"
          :is-dataflow="true"
          :Task="model.TaskModal"
          :isNew="true"
          @onHideModalTask="onHideModalTask"
          :isForm="true">
        </task-assign>

        <task-request
          v-model="model.TaskRequestModal"
          v-if="stage.showModalTaskRequest"
          :is-dataflow="true"
          :title="'Yêu cầu'"
          :per="model.TaskPerRequest"
          :Task="model.TaskModal" @onHideModalTask="onHideModalTask"></task-request>
        <task-check-list
          v-model="model.TaskCheckListModal"
          v-if="stage.showModalTaskCheckList"
          is-dataflow="true"
          :title="'Kiểm tra'"
          :per="model.TaskPerChecklist"
          :Task="model.TaskModal" @onHideModalTask="onHideModalTask"></task-check-list>
        <b-modal title="Thực hiện" ref="modalTaskExecution" id="modal-task-execution" size="xl">
            <task-execution-content
              v-model="model.TaskExecutionTransModal"
              :Task="model.TaskModal"
              :per="model.TaskPerExecution"
              :perGen="model.TaskPer"
              title="Thực hiện"
              @onReloadDataflow="fetchDataflow(true)"
              @onHideModalTask="onHideModalTask"
              :is-dataflow="true"
              :task-status="model.TaskStatusModal"></task-execution-content>
            <template v-slot:modal-footer="{ ok, cancel, hide }">
                <b-button class="mr-2" variant="primary" @click="showModalExecutionForm">
                    Thêm
                </b-button>
                <b-button variant="primary" @click="hide()">
                    Đóng
                </b-button>
            </template>
        </b-modal>
        <task-execution-form
                v-if="stage.showModalTaskExecution"
                v-model="model.TaskExecutionTransModal"
                :is-dataflow="true"
                title="Thực hiện"
                :Task="model.TaskModal"
                :per="model.TaskPerExecution" :perGen="model.TaskPer"
                @onReloadDataflow="fetchDataflow(true)"
                :TaskStatus="model.TaskStatusModal"
                @onHideModalTask="onHideModalTask"
                :addnew="true"></task-execution-form>

        <b-modal title="Chi phí" ref="modalTaskExpense" id="modal-task-expense" size="xl">
            <div class="form-group row">
                <div class="col-md-24 m-0">
                    <table class="not-border">
                        <thead>
                        <tr class="text-left">
                            <th class="pr-3">Loại</th>
                            <th class="pr-3">Ngày</th>
                            <th class="pr-3">Nội dung chi</th>
                            <th class="pr-3">Đơn vị tính</th>
                            <th class="pr-3">Số lượng</th>
                            <th class="pr-3">Đơn giá</th>
                            <th class="pr-3">Thành tiền</th>
                            <th class="pr-3">Thuế suất</th>
                            <th class="pr-3">Tiền thuế</th>
                            <th class="td-action"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, key) in model.TaskExpenseTransDetailModal">
                            <td class="pr-3">{{TransTypeText[item.TransType]}}</td>
                            <td class="pr-3">{{item.TransDate}}</td>
                            <td class="pr-3">{{item.Description}}</td>
                            <td class="pr-3">{{item.UomName}}</td>
                            <td class="pr-3">{{item.Quantity}}</td>
                            <td class="pr-3">{{item.UnitPrice}}</td>
                            <td class="pr-3">{{item.Amount}}</td>
                            <td class="pr-3">{{item.TaxRate}}</td>
                            <td class="pr-3">{{item.TaxAmount}}</td>
                            <td class="right-absolute">
                                <task-expense
                                  v-model="model.TaskExpenseTransModal"
                                  :TaskExpenseTrans="model.TaskExpenseTransModal[item.TransID]"
                                  :title="'Chi phí'"
                                  :per="model.TaskPerExpense" :per-detail="model.TaskPerExpenseDetail"
                                  :Task="model.TaskModal"
                                  :isNew="false" v-if="item.first" @transferExpense="updateExpense">
                                </task-expense>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <template v-slot:modal-footer="{ ok, cancel, hide }">
                <b-button class="mr-2" variant="primary" @click="showModalExpenseForm">
                    Thêm
                </b-button>
                <b-button variant="primary" @click="hide()">
                    Đóng
                </b-button>
            </template>
        </b-modal>
        <task-expense v-model="model.TaskExpenseTransModal"
                      v-if="stage.showModalTaskExpense && model.TaskPerExpense['Edit']"
                      :TaskExpenseTrans="model.TaskExpenseTransModal"
                      :title="'Chi phí'"
                      :Task="model.TaskModal"
                      :is-dataflow="true"
                      @onHideModalTask="onHideModalTask"
                      :isNew="true" @transferExpense="updateExpense">
        </task-expense>

        <b-modal title="Phim" ref="modalTaskVideo" id="modal-task-video" size="xl">
            <div class="form-group row">
                <div class="col-md-24 m-0">
                    <task-video-content v-model="model.TaskVideoModal" :Task="model.TaskModal" :per="model.TaskPerVideo">
                    </task-video-content>
                </div>
            </div>
            <template v-slot:modal-footer="{ ok, cancel, hide }">
                <b-button class="mr-2" variant="primary" @click="showModalVideoForm">
                    <ijcore-upload-multiple-video v-on:changed="changeVideo" :isIcon="true"></ijcore-upload-multiple-video>
                </b-button>
                <b-button variant="primary" @click="hide()">
                    Đóng
                </b-button>
            </template>
        </b-modal>

        <b-modal title="Tệp" ref="modalTaskFile" id="modal-task-file" size="xl">
            <div class="form-group row">
                <div class="col-md-24 m-0">
                    <task-file-content v-model="model.TaskFileModal" :Task="model.TaskModal" :per="model.TaskPerFile">
                    </task-file-content>
                </div>
            </div>
            <template v-slot:modal-footer="{ ok, cancel, hide }">
                <b-button class="mr-2" variant="primary" @click="showModalFileForm">
                    <ijcore-upload-multiple-file v-on:changed="changeFile" :isIcon="true"></ijcore-upload-multiple-file>
                </b-button>
                <b-button variant="primary" @click="hide()">
                    Đóng
                </b-button>
            </template>
        </b-modal>

      <modal-link-trans :toggle="stage.onShowModalLinkTrans" :dataflow-array="model.dataflowArray" :dataflow-trans="model.dataflowTrans"></modal-link-trans>
    </div>
</template>


<script>
    import ApiService from '@/services/api.service';
    import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
    import Select2 from 'v-select2-component';
    import IjcoreFlowchart from '@/components/IjcoreFlowchart';
    import { VueContext } from 'vue-context';
    import IjcoreModalSearchInput from '@/components/IjcoreModalSearchInput';
    import IjcoreDateRange from '@/components/IjcoreDateRange';
    import IjcoreSelect2Server from '@/components/IjcoreSelect2Server';
    import IjcoreDateTimePicker from '@/components/IjcoreDateTimePicker';
    import VueContextCss from 'vue-context/dist/css/vue-context.css';
    // import TaskComment from '../task/TaskComment';
    import ChatCategoryComment from "../../apps/chat/partials/ChatCategoryComment";
    import IjcoreSelectDropdown from '@/components/IjcoreSelectDropdown';
    import TaskExecutionContent from '@/views/ijtask/task/partials/TaskExecutionContent';
    import TaskExecutionForm from '@/views/ijtask/task/partials/TaskExecutionForm';
    import TaskGeneral from '@/views/ijtask/task/TaskGeneral';
    import TaskLink from '@/views/ijtask/task/TaskLink';
    import TaskRequest from '@/views/ijtask/task/TaskRequest';
    import TaskCheckList from '@/views/ijtask/task/TaskCheckList';
    import TaskExpense from '@/views/ijtask/task/TaskExpense';
    import TaskVideoContent from '@/views/ijtask/task/partials/TaskVideoContent';
    import IjcoreUploadMultipleVideo from "@/components/IjcoreUploadMultipleVideo";
    import TaskFileContent from '@/views/ijtask/task/partials/TaskFileContent';
    import IjcoreUploadMultipleFile from "@/components/IjcoreUploadMultipleFile";
    import IjcoreUsersIcon from '@/components/IjcoreUsersIcon';
    import TaskAssign from "@/views/ijtask/task/TaskAssign";
    import TaskAssignContent from "@/views/ijtask/task/partials/TaskAssignContent";
    import ModalLinkTrans from "./partials/ModalLinkTrans";


    // TODO import Task
    // import

    const DataflowApi = 'task/api/dataflow';
    const GetWorkflowApi = 'task/api/dataflow/workflow';
    const TaskDetailApi = 'task/api/dataflow/taskDetail';
    const TaskScheduleApi = 'task/api/dataflow/taskSchedule';
    const CreateFeature = 'task/api/dataflow/createFeature';
    const EndDataflow = 'task/api/dataflow/endDataflow';

    const TaskPriority = {
        1: 'Khẩn cấp',
        2: 'Cao',
        3: 'Trung bình',
        4: 'Thấp'
    };
    const TaskStatus = {
        1: 'Chưa thực hiện',
        2: 'Đang thực hiện',
        3: 'Đã hoàn thành',
        4: 'Đang đợi ý kiến',
        5: 'Từ chối'
    };

    export default {
        name: 'task-dataflow',
        data() {
          return {
            dataflowPerPage: 10,
            dataflowCurrentPage: 1,
            dataflowTotalRows: 0,
            TransTypeText:{'1' : "Kế hoạch", '2' : "Thực tế"},
            idWorkflow: (this.$route.params.idWorkflow) ? Number(this.$route.params.idWorkflow) : null,
            idDataflow: (this.$route.params.idDataflow) ? Number(this.$route.params.idDataflow) : null,
            notice: (this.$route.query.notice) ? this.$route.query.notice : null,
            model:{
              taskSelected: null,
              taskChildSelected: null,

              WFID: (this.$route.params.WFID) ? this.$route.params.WFID : null,
              WFName: '',
              workflowOption: [],
              jsonFlowchart: null,
              taskArray: [],
              dataflowArray: [],
              dataflowArrayCurrentTask: [],

              taskDetail: [],
              taskAssignUsers: [],

              taskSchedule: [],
              taskScheduleUsers: [],

              filterDataflow: null,
              filterDataflowDateRange: null,
              filterDataflowStatus: null,
              filterTaskAssignUser: null,

              featureStatusValueOption: [],
              featureStatusValue: null,
              featureStatusDescription: '',
              statusDate: '',

              taskCommentOption: [],
              taskComment: null,
              taskCommentID: null,
              taskCommentName: '',

              TaskModal: {},
              TaskIDSelected: {},
              TaskLinkModal: {},
              TaskRequestModal: {},
              TaskCheckListModal: {},
              TaskExpenseTransModal: {},
              TaskExecutionTransModal: {},
              TaskExpenseTransDetailModal: {},
              TaskStatusModal: {},
              TaskFileModal: {},
              TaskVideoModal: {},
              TaskAssign: {},

              TaskPer : {},
              TaskPerAssign : {},
              TaskPerCate : {},
              TaskPerLink : {},
              TaskPerExecution : {},
              TaskPerChecklist : {},
              TaskPerValuation : {},
              TaskPerExpense : {},
              TaskPerExpenseDetail : {},
              TaskPerFile : {},
              TaskPerRequest : {},
              TaskPerVideo : {},

              ViewPerExpenseDetailTransType: true,
              ViewPerExpenseDetailTransDate: true,
              ViewPerExpenseDetailExpenseID: true,
              ViewPerExpenseDetailDescription: true,
              ViewPerExpenseDetailUomID: true,
              ViewPerExpenseDetailQuantity: true,
              ViewPerExpenseDetailUnitPrice: true,
              ViewPerExpenseDetailAmount: true,
              ViewPerExpenseDetailTaxRate: true,
              ViewPerExpenseDetailTaxAmount: true,

              dataflowTrans: null
            },
            stage: {
              showFlowchart: false,
              activeTab: 1,
              showDataflowPagination: false,
              showModalTaskExecution: false,
              showModalTaskGeneral: false,
              showModalTaskLink: false,
              showModalTaskRequest: false,
              showModalTaskCheckList: false,
              showModalTaskExpense: false,
              showModalTaskVideo: false,
              showModalTaskFile: false,
              showModalTaskAssign: false,
              editTaskAssign: false,
              onShowModalLinkTrans: false
            },
            constant: {
              taskPriority: TaskPriority,
              taskStatus: TaskStatus
            }
          }
        },
        components:{
          Select2,
          IjcoreFlowchart,
          VueContext,
          IjcoreModalSearchInput,
          IjcoreDateRange,
          IjcoreSelect2Server,
          IjcoreDateTimePicker,
          // TaskComment,
          ChatCategoryComment,
          IjcoreSelectDropdown,
          TaskExecutionContent,
          TaskExecutionForm,
          TaskGeneral,
          TaskLink,
          TaskRequest,
          TaskCheckList,
          TaskExpense,
          TaskVideoContent,
          IjcoreUploadMultipleVideo,
          TaskFileContent,
          IjcoreUploadMultipleFile,
          IjcoreUsersIcon,
          TaskAssign,
          TaskAssignContent,
          ModalLinkTrans
        },

        computed: {
          appRootApi(){
            return process.env.VUE_APP_ROOT_API;
          },
          rows() {
            return this.dataflowTotalRows
          },
        },
        created() {
          this.fetchData();
        },
        updated() {},
        mounted() {},
        methods: {

          handleDebugger(data){
          },

          fetchData(){
              let self = this;
              let requestData = {
                method: 'get',
                url: GetWorkflowApi,
                data: {},
              };
              this.$store.commit('isLoading', true);
              ApiService.customRequest(requestData).then((response) => {
                self.$store.commit('isLoading', false);
                let dataResponse = response.data;
                if (dataResponse.status === 1) {
                  dataResponse.data.workflow = _.uniqBy(dataResponse.data.workflow, 'WFID');
                  self.model.workflowOption = [];
                  _.forEach(dataResponse.data.workflow, function (workflow, key) {
                      let tmpObj = {};
                      tmpObj.id = workflow.WFID;
                      tmpObj.text = workflow.WFName;
                      self.model.workflowOption.push(tmpObj);
                  });
                }

                if (self.idWorkflow) {
                  self.model.WFID = self.idWorkflow;
                }

              }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
                Swal.fire({
                  title: 'Thông báo',
                  text: 'Không kết nối được với máy chủ',
                  confirmButtonText: 'Đóng'
                });
              });

              if (this.model.WFID) {
                  this.fetchDataflow(true);
                  let workflow = _.find(self.model.workflowOption, ['id', Number(this.model.WFID)]);
                  if (workflow) {
                      this.model.WFName = workflow.text;
                  }
              }
          },
          fetchDataflow(jsonFlowchart = false){
              let self = this;
              if (!this.model.WFID) {
                  this.$bvToast.toast('Yêu cầu chọn quy trình', {
                      title: 'Thông báo',
                      variant: 'warning',
                      solid: true
                  });
                  return
              }
              let requestData = {
                  method: 'post',
                  url: DataflowApi,
                  data: {
                      jsonFlowchart: jsonFlowchart,
                      per_page: this.dataflowPerPage,
                      page: this.dataflowCurrentPage,
                      WFID: this.model.WFID,
                      DFKey: (this.model.filterDataflow && this.model.filterDataflow.DFKey) ? this.model.filterDataflow.DFKey : 0,
                      dateRange: (!_.isEmpty(this.model.filterDataflowDateRange)) ? this.model.filterDataflowDateRange : null,
                      Status: (this.model.filterDataflowStatus) ? this.model.filterDataflowStatus : null,
                      EmployeeID: (this.model.filterTaskAssignUser) ? this.model.filterTaskAssignUser : null
                  },
              };
              this.$store.commit('isLoading', true);
              this.stage.showFlowchart = false;
              ApiService.customRequest(requestData).then((response) => {
                  self.$store.commit('isLoading', false);
                  self.stage.showFlowchart = true;
                  let responseData = response.data;
                  if (responseData.status === 1) {
                    if (jsonFlowchart && responseData.data.JsonFlowchart) {
                      self.model.jsonFlowchart = JSON.parse(responseData.data.JsonFlowchart);
                      self.resetPositionFlowchart();
                    }

                    self.dataflowCurrentPage = responseData.data.task.current_page;
                    self.dataflowPerPage = String(responseData.data.task.per_page);
                    self.dataflowTotalRows = responseData.data.task.total;
                    if (responseData.data.task.total <= responseData.data.task.per_page) {
                      self.stage.showDataflowPagination = false;
                    } else {
                      self.stage.showDataflowPagination = true;
                    }

                    self.model.taskArray = responseData.data.task.data;
                    self.model.dataflowArray = responseData.data.dataflow;
                    if (responseData.data.taskAssignUsers) {
                      self.model.taskAssignUsers = responseData.data.taskAssignUsers;
                    }

                    if (jsonFlowchart) {
                      if (self.idDataflow) {
                        self.model.filterDataflow = {
                          DFKey: self.idDataflow
                        };
                      }
                    }

                    if (self.notice) {
                      if (self.notice.TypeAction === 1) {
                        self.stage.activeTab = 4;
                        self.onShowTabTaskComment(self.model.taskArray[0]);
                        self.onChangeTaskComment({id: self.notice.CategoryID});
                      }
                      self.notice = null;
                    }


                  }
              }, (error) => {
                  console.log(error);
                  self.$store.commit('isLoading', false);
                Swal.fire({
                  title: 'Thông báo',
                  text: 'Không kết nối được với máy chủ',
                  confirmButtonText: 'Đóng'
                });
              });
          },
          onSelectWorkflow(selected){
              this.model.WFName = (selected.text) ? selected.text : '';
              this.fetchDataflow(true);
          },
          onChangeWorkflow(data){
              // this.model.filterDataflow = null;
          },

          resetPositionFlowchart(){
            let minTop = 10000, self = this, maxHeight = 0;
            _.forEach(this.model.jsonFlowchart.node, function (node, key) {
              if (node.PositionY < minTop) {
                minTop = node.PositionY;
              }
            });
            minTop = minTop - 40;

            _.forEach(this.model.jsonFlowchart.node, function (node, key) {
              self.model.jsonFlowchart.node[key].PositionY = self.model.jsonFlowchart.node[key].PositionY - minTop;
              if (self.model.jsonFlowchart.node[key].PositionY > maxHeight) {
                maxHeight = self.model.jsonFlowchart.node[key].PositionY
              }
            });

            // set height for div
            if ($('.flowchart-diagram').length) $('.flowchart-diagram').height(maxHeight + 100);
          },
          onShowTabTaskDetail(task = null){
            if (!task) return;
            this.model.taskSelected = task;
            this.stage.activeTab = 2;
            let self = this;

            let requestData = {
              method: 'post',
              url: TaskDetailApi,
              data: {
                TaskID: task.ConstraintFieldValue,
                WFID: task.WFID,
                DFKey: task.DFKey
              },
            };
            this.$store.commit('isLoading', true);
            ApiService.customRequest(requestData).then((response) => {
              self.$store.commit('isLoading', false);
              let responseData = response.data;
              if (responseData.status === 1) {
                self.model.taskDetail = responseData.data.taskDetail;

                // set task detail assign user
                // self.model.taskAssignUsers = responseData.data.taskAssignUsers;
              }
            }, (error) => {
              console.log(error);
              self.$store.commit('isLoading', false);
              Swal.fire({
                title: 'Thông báo',
                text: 'Không kết nối được với máy chủ',
                confirmButtonText: 'Đóng'
              });
            });
          },
          // TODO hoàn thiện chức năng nhật trình công việc
          onShowTabTaskSchedule(task = null){
              if (!task) return;
              this.model.taskSelected = task;
              this.stage.activeTab = 3;
              let self = this;

              let requestData = {
                  method: 'post',
                  url: TaskScheduleApi,
                  data: {
                      TaskID: task.TaskID,
                      WFID: task.WFID,
                      DFKey: task.DFKey
                  },
              };
              this.$store.commit('isLoading', true);
              ApiService.customRequest(requestData).then((response) => {
                  self.$store.commit('isLoading', false);
                  let responseData = response.data;
                  if (responseData.status === 1) {
                      self.model.taskSchedule = responseData.data.taskSchedule;
                      self.model.taskScheduleUsers = responseData.data.taskScheduleUsers;
                  }
              }, (error) => {
                  console.log(error);
                  self.$store.commit('isLoading', false);
                Swal.fire({
                  title: 'Thông báo',
                  text: 'Không kết nối được với máy chủ',
                  confirmButtonText: 'Đóng'
                });
              });

          },
          onShowTabTaskComment(task = null){
              if (!task) return;
              this.stage.activeTab = 4;
              this.model.taskSelected = task;
              this.model.taskComment = this.model.taskSelected;
              let self = this;
              let taskDataflow = _.filter(this.model.dataflowArray, ['DFKey', task.DFKey]);
              this.model.taskCommentOption = [{id: task.TaskID, text: task.TaskName}];
              _.forEach(taskDataflow, function (dataflow, key) {
                  let tmpObj = {};
                  tmpObj.id = dataflow.TaskID;
                  tmpObj.text = __.stripHtml(dataflow.TaskName);
                  self.model.taskCommentOption.push(tmpObj);
              });

          },
          onChangeTaskComment(item){
              let taskComment = _.find(this.model.dataflowArray, ['TaskID', Number(item.id)]);
              if (taskComment) {
                  this.model.taskComment = taskComment;
              } else {
                  this.model.taskComment = this.model.taskSelected;
              }
          },
          onShowModalUpdateStatus(data){
              if (data.task.Locked) {
                  Swal.fire({
                      title: 'Thông báo',
                      text: 'Luồng công việc đã được khóa',
                      confirmButtonText: 'Đóng'
                  });
              } else {
                  this.model.taskSelected = data.task;
                  this.model.taskChildSelected = data.taskChild;
                  this.stage.showModalTaskExecution = true;
              }

          },
          showModalExecutionForm(){
              this.model.taskSelected = this.model.taskArray[0];
              this.stage.showModalTaskExecution = true;
          },
          showModalExpenseForm(){
              this.model.taskSelected = this.model.taskArray[0];
              this.stage.showModalTaskExpense = true;
          },
          showModalVideoForm(){
              this.model.taskSelected = this.model.taskArray[0];
              this.stage.showModalTaskVideo = true;
          },
          showModalFileForm(){
              this.model.taskSelected = this.model.taskArray[0];
              this.stage.showModalTaskFile = true;
          },
          onRedoExecution(node){
              let dataflow = _.find(this.model.dataflowArray, ['WFItemID', node.WFItemID]);
              let self = this;
              if (!dataflow) return;
              let requestData = {
                  url: 'task/api/dataflow/redoDataflow',
                  method: 'post',
                  data: {
                      WFID: dataflow.WFID,
                      WFItemID: dataflow.WFItemID,
                      DFKey: dataflow.DFKey,
                      DFID: dataflow.DFID
                  }
              };
              this.$store.commit('isLoading', true);
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                if (responsesData.status === 1) {
                  self.$bvToast.toast('Cập nhật thành công', {
                      title: 'Thông báo',
                      variant: 'success',
                      solid: true
                  });
                  self.fetchDataflow(true);
                }else if (responsesData.status === 2) {
                  self.$bvToast.toast(responsesData.msg, {
                    title: 'Thông báo',
                    variant: 'warning',
                    solid: true
                  });
                  self.fetchDataflow(true);
                }

                self.$store.commit('isLoading', false);
              }, (error) => {
                  console.log(error);
                  self.$store.commit('isLoading', false);
                Swal.fire({
                  title: 'Thông báo',
                  text: 'Không kết nối được với máy chủ',
                  confirmButtonText: 'Đóng'
                });
              });

          },
          onCreateFeature(params){
              let self = this;
              let requestData = {
                  method: 'post',
                  url: CreateFeature,
                  data: {
                    WFID: this.model.taskArray[0].WFID,
                    DFKey: this.model.taskArray[0].DFKey,
                    TaskIDParent: this.model.taskArray[0].TaskID,
                    LineIDTemp: params.LineIDTemp,
                    WFItemID: params.WFItemID
                  },
              };
              this.$store.commit('isLoading', true);
              ApiService.customRequest(requestData).then((response) => {
                  self.$store.commit('isLoading', false);
                  let responseData = response.data;

                  if (responseData.status === 1) {
                      self.fetchDataflow(false);
                      this.$bvToast.toast('Cập nhật thành công', {
                          title: 'Thông báo',
                          variant: 'success',
                          solid: true
                      });
                  } else {
                      this.$bvToast.toast('Cập nhật không thành công', {
                          title: 'Thông báo',
                          variant: 'warning',
                          solid: true
                      });
                  }
              }, (error) => {
                  console.log(error);
                  self.$store.commit('isLoading', false);
                Swal.fire({
                  title: 'Thông báo',
                  text: 'Không kết nối được với máy chủ',
                  confirmButtonText: 'Đóng'
                });
              });
          },
          onStartDataflow(nodeStart){
          },
          onShowModalTask(data){
            let dataflow = data.dataflow;
            let taskParent = _.find(this.model.taskArray, ['DFKey', dataflow.DFKey]);
            if (taskParent && taskParent.Locked === 1) {
              this.$bvToast.toast('Quy trình đã bị khóa', {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
              return;
            }
            if (data.type === 1) {
              let router = this.$router.resolve({
                name: 'task-task-view',
                params: {id: dataflow.TaskID}
              });
              window.open(router.href, '_blank');
              return;
            }

            let getData = this.stage.editTaskAssign && (data.type !== 4);

            if (this.model.TaskIDSelected === dataflow.TaskID && data.type !== 6 && !getData) {
              switch (data.type) {
                // Thông tin chung
                case 2:
                  this.stage.showModalTaskGeneral = true;
                  break;
                // Danh mục liên kết
                case 3:
                  this.stage.showModalTaskLink = true;
                  break;
                // Giao việc
                case 4:
                  this.stage.editTaskAssign = true;
                  this.stage.showModalTaskAssign = true;
                  break;
                // Yêu cầu
                case 5:
                  this.stage.showModalTaskRequest = true;
                  break;
                // Thực hiện
                case 6:
                  this.$bvModal.show('modal-task-execution');
                  break;
                // Kiểm tra
                case 7:
                  this.stage.showModalTaskCheckList = true;
                  break;
                // Chi phí
                case 8:
                  this.$bvModal.show('modal-task-expense');
                  break;
                // Tệp
                case 9:
                  this.$bvModal.show('modal-task-file');
                  break;
                // Phim
                case 10:
                  this.$bvModal.show('modal-task-video');
                  break;
              }
            } else {
              let self = this;
              this.model.TaskIDSelected = dataflow.TaskID;

              let requestData = {
                url: 'task/api/task/view' + '/' + dataflow.TaskID,
                method: 'get',
                data: {
                    id: this.idParams
                }
              };
              this.$store.commit('isLoading', true);
              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                if (responsesData.status === 1) {
                  self.model.TaskPer = responsesData.data.PerAll['TaskPer'];
                  self.model.TaskPerAssign = responsesData.data.PerAll['TaskPerAssign'];
                  self.model.TaskPerCate = responsesData.data.PerAll['TaskPerCate'];
                  self.model.TaskPerLink = responsesData.data.PerAll['TaskPerLink'];
                  self.model.TaskPerExecution = responsesData.data.PerAll['TaskPerExecution'];
                  self.model.TaskPerChecklist = responsesData.data.PerAll['TaskPerChecklist'];
                  self.model.TaskPerValuation = responsesData.data.PerAll['TaskPerValuation'];
                  self.model.TaskPerExpense = responsesData.data.PerAll['TaskPerExpense'];
                  self.model.TaskPerExpenseDetail = responsesData.data.PerAll['TaskPerExpenseDetail'];

                  self.model.ViewPerExpenseDetailTransType = __.perViewColumn(self.model.TaskPerExpenseDetail, 'TransType');
                  self.model.ViewPerExpenseDetailTransDate = __.perViewColumn(self.model.TaskPerExpenseDetail, 'TransDate');
                  self.model.ViewPerExpenseDetailExpenseID = __.perViewColumn(self.model.TaskPerExpenseDetail, 'ExpenseID');
                  self.model.ViewPerExpenseDetailDescription = __.perViewColumn(self.model.TaskPerExpenseDetail, 'Description');
                  self.model.ViewPerExpenseDetailUomID = __.perViewColumn(self.model.TaskPerExpenseDetail, 'UomID');
                  self.model.ViewPerExpenseDetailQuantity = __.perViewColumn(self.model.TaskPerExpenseDetail, 'Quantity');
                  self.model.ViewPerExpenseDetailUnitPrice = __.perViewColumn(self.model.TaskPerExpenseDetail, 'UnitPrice');
                  self.model.ViewPerExpenseDetailAmount = __.perViewColumn(self.model.TaskPerExpenseDetail, 'Amount');
                  self.model.ViewPerExpenseDetailTaxRate = __.perViewColumn(self.model.TaskPerExpenseDetail, 'TaxRate');
                  self.model.ViewPerExpenseDetailTaxAmount = __.perViewColumn(self.model.TaskPerExpenseDetail, 'TaxAmount');

                  self.model.TaskPerFile = responsesData.data.PerAll['TaskPerFile'];
                  self.model.TaskPerRequest = responsesData.data.PerAll['TaskPerRequest'];
                  self.model.TaskPerVideo = responsesData.data.PerAll['TaskPerVideo'];

                  let Task = {};
                  Task = responsesData.data.Task;
                  Task.StartDate = __.convertDateToString(Task.StartDate);
                  Task.DueDate = __.convertDateToString(Task.DueDate);
                  Task.statusHour = 0;
                  Task.PriorityOptions = responsesData.data.PriorityOptions;
                  Task.AccessTypeOptions = responsesData.data.AccessTypeOptions;
                  Task.TotalActualQuantity = __.convertNumberToText(Task.TotalActualQuantity);
                  Task.OldTotalActualQuantity = Task.TotalActualQuantity;
                  Task.ActualQuantity = __.convertNumberToText(Task.ActualQuantity);
                  Task.OldActualQuantity = Task.ActualQuantity;
                  Task.PercentCompleted = __.convertNumberToText(Task.PercentCompleted);
                  Task.EstimatedQuantity = __.convertNumberToText(Task.EstimatedQuantity);

                  Task.DFKey = dataflow.DFKey;
                  Task.DFID = dataflow.DFID;
                  Task.WFID = dataflow.WFID;
                  Task.WFItemID = dataflow.WFItemID;
                  Task.TaskParent = self.model.taskArray[0];


                  if (Task.CalMethod == 1) {
                    Task.CalMethodName = "Theo thời gian";
                    Task.isActualQuantity = true;
                  } else {
                    Task.CalMethodName = "Theo khối lượng";
                    Task.isActualQuantity = false;
                  }
                  Task.CompanyOptions = [];
                  _.forEach(responsesData.data.CompanyOptions, function (value, key) {
                    let tmpObj = {};
                    tmpObj.value = value.CompanyID;
                    tmpObj.text = value.CompanyName;
                    Task.CompanyOptions.push(tmpObj);
                  });
                  if (Task.CompanyID) {
                    let company = _.find(Task.CompanyOptions, ['value', Task.CompanyID]);
                    if (company) {
                      Task.CompanyName = company.text;
                    }
                  }
                  Task.UomOptions = [];
                  _.forEach(responsesData.data.UomOptions, function (value, key) {
                    let tmpObj = {};
                    tmpObj.value = value.UomID;
                    tmpObj.text = value.UomName;
                    Task.UomOptions.push(tmpObj);
                  });

                  let uom = _.find(Task.UomOptions, ['value', Task.UomID]);
                  if (uom) {
                    Task.UomName = uom.text;
                  }

                  Task.CalendarOptions = [];
                  _.forEach(responsesData.data.CalendarOptions, function (value, key) {
                    let tmpObj = {};
                    tmpObj.value = value.CalendarTypeID;
                    tmpObj.text = value.CalendarName;
                    Task.CalendarOptions.push(tmpObj);
                  });
                  let calendar = _.find(Task.CalendarOptions, ['value', Task.CalendarTypeID]);
                  if (calendar) {
                    Task.CalendarName = calendar.text;
                  }

                  Task.StatusValueOption = [];
                  _.forEach(responsesData.data.StatusValueOption, function (value, key) {
                    let tmpObj = {};
                    tmpObj.value = value.StatusValue;
                    tmpObj.text = value.StatusDescription;
                    Task.StatusValueOption.push(tmpObj);
                  });

                  self.model.TaskModal = Task;
                  self.model.TaskLink = responsesData.data.TaskLink;

                  self.$set(self.model.TaskModal, 'TaskCate', []);
                  if (responsesData.data.TaskCate) {
                    _.forEach(responsesData.data.TaskCate, function (taskCate, key) {
                      let tmpCate = {};
                      if (taskCate.CateID) {
                        let cateList = _.find(responsesData.data.TaskCateList, ['CateID', taskCate.CateID]);
                        if (cateList) {
                          tmpCate.CateID = cateList.CateID;
                          tmpCate.CateName = cateList.CateName;
                        }
                      }

                      if (taskCate.CateValue) {
                        let cateValue = _.find(responsesData.data.TaskCateValue, {
                          CateID: taskCate.CateID,
                          CateValue: taskCate.CateValue
                        });
                        if (cateValue) {
                          tmpCate.CateValue = taskCate.CateValue;
                          tmpCate.Description = cateValue.Description;
                        }
                      }
                      self.$set(self.model.TaskModal, self.model.TaskModal.length, tmpCate);
                      // self.Task.TaskCate.push(tmpCate);
                    });
                  }

                  self.model.TaskAssign = responsesData.data.TaskAssign;
                  _.forEach(self.model.TaskAssign, function (item, key) {
                    self.model.TaskAssign[key].StartDate = __.convertDateToString(item.StartDate);
                    self.model.TaskAssign[key].DueDate = __.convertDateToString(item.DueDate);
                    self.model.TaskAssign[key].EstimatedHour = __.convertNumberToText(item.EstimatedHour);
                    self.model.TaskAssign[key].PersonAssign = [];
                    if (self.model.TaskAssign[key].IsAssignee == 1) {
                      self.model.TaskAssign[key].PersonAssign.push(1);
                    }
                    if (self.model.TaskAssign[key].IsMainResponsiblePerson == 1) {
                      self.model.TaskModal.IsMainResponsiblePerson = key;
                      self.model.TaskAssign[key].PersonAssign.push(2);
                    }
                    if (self.model.TaskAssign[key].IsResponsiblePerson == 1) {
                      self.model.TaskAssign[key].PersonAssign.push(3);
                    }
                    if (self.model.TaskAssign[key].IsChecker == 1) {
                      self.model.TaskAssign[key].PersonAssign.push(4);
                    }
                    if (self.model.TaskAssign[key].IsFollower == 1) {
                      self.model.TaskAssign[key].PersonAssign.push(5);
                    }
                  });

                  self.model.TaskRequestModal = responsesData.data.TaskRequest;
                  _.forEach(self.model.TaskRequestModal, function (item, key) {
                    self.model.TaskRequestModal[key].RequestDate = __.convertDateTimeToString(item.RequestDate);
                  });
                  self.model.TaskCheckListModal = responsesData.data.TaskCheckList;
                  _.forEach(self.model.TaskCheckListModal, function (item, key) {
                    self.model.TaskCheckListModal[key].CompletedDate = __.convertDateTimeToString(item.CompletedDate);
                  });

                  self.model.TaskExecutionTransModal = responsesData.data.TaskExecutionTrans;
                  _.forEach(self.model.TaskExecutionTransModal, function (item, key) {
                    self.model.TaskExecutionTransModal[key].TransDate = __.convertDateTimeToString(item.TransDate);
                    self.model.TaskExecutionTransModal[key].ActualHour = __.convertNumberToText(item.ActualHour);
                    self.model.TaskExecutionTransModal[key].ActualQuantity = __.convertNumberToText(item.ActualQuantity);
                  });

                  self.model.TaskExpenseTransDetailModal = responsesData.data.TaskExpenseTransItem;
                  _.forEach(self.model.TaskExpenseTransDetailModal, function (item, key) {
                    self.model.TaskExpenseTransDetailModal[key].TransDate = __.convertDateToString(item.TransDate);
                    self.model.TaskExpenseTransDetailModal[key].Quantity = __.convertNumberToText(item.Quantity);
                    self.model.TaskExpenseTransDetailModal[key].UnitPrice = __.convertNumberToText(item.UnitPrice);
                    self.model.TaskExpenseTransDetailModal[key].Amount = __.convertNumberToText(item.Amount);
                    self.model.TaskExpenseTransDetailModal[key].TaxRate = __.convertNumberToText(item.TaxRate);
                    self.model.TaskExpenseTransDetailModal[key].TaxAmount = __.convertNumberToText(item.TaxAmount);
                  });

                  _.forEach(responsesData.data.TaskExpenseTrans, function (item, key) {
                    self.model.TaskExpenseTransModal[item.TransID] = {};
                    item.TransDate = __.convertDateToString(item.TransDate);
                    item.Quantity = __.convertNumberToText(item.Quantity);
                    item.UnitPrice = __.convertNumberToText(item.UnitPrice);
                    item.Amount = __.convertNumberToText(item.Amount);
                    item.TaxRate = __.convertNumberToText(item.TaxRate);
                    item.TaxAmount = __.convertNumberToText(item.TaxAmount);
                    self.model.TaskExpenseTransModal[item.TransID].master = item;
                  });
                  let TransID = false;
                  let arrObject = [];
                  _.forEach(responsesData.data.TaskExpenseTransItem, function (item, key) {
                    if (!TransID) {
                      TransID = item.TransID;
                    }
                    if (TransID != item.TransID) {
                      self.model.TaskExpenseTransModal[TransID].detail = arrObject;
                      arrObject = [];
                      TransID = item.TransID;
                    }
                    self.model.TaskExpenseTransDetailModal[key].first = true;
                    arrObject.push(item);
                  });
                  if (TransID) {
                    self.model.TaskExpenseTransModal[TransID].detail = arrObject;
                  }
                  self.model.TaskFileModal = responsesData.data.TaskFile;
                  _.forEach(self.model.TaskFileModal, function (item, key) {
                    self.model.TaskFileModal[key].DateModified = __.convertDateTimeToString(item.DateModified);
                    self.model.TaskFileModal[key].changeFile = 0;
                    self.model.TaskFileModal[key].changeData = 0;
                  });
                  self.model.TaskVideoModal = responsesData.data.TaskVideo;
                  _.forEach(self.model.TaskVideoModal, function (item, key) {
                    self.model.TaskVideoModal[key].DateModified = __.convertDateTimeToString(item.DateModified);
                  });


                  self.model.TaskStatusModal = [];
                  _.forEach(responsesData.data.TaskStatus, function (value, key) {
                    let tmpObj = {};
                    tmpObj.value = value.StatusValue;
                    tmpObj.text = value.StatusDescription;
                    tmpObj.PercentCompleted = value.PercentCompleted;
                    tmpObj.ExecutionStatus = value.ExecutionStatus;
                    self.model.TaskStatusModal.push(tmpObj);
                  });
                  switch (data.type) {
                    // Thông tin chung
                    case 2:
                      self.stage.showModalTaskGeneral = true;
                      break;
                    // Danh mục liên kết
                    case 3:
                      self.stage.showModalTaskLink = true;
                      break;
                    // Giao việc
                    case 4:
                      this.stage.editTaskAssign = true;
                      this.stage.showModalTaskAssign = true;
                      break;
                    // Yêu cầu
                    case 5:
                      self.stage.showModalTaskRequest = true;
                      break;
                    // Thực hiện
                    case 6:
                      self.$bvModal.show('modal-task-execution');
                      break;
                    // Kiểm tra
                    case 7:
                      self.stage.showModalTaskCheckList = true;
                      break;
                    // Chi phí
                    case 8:
                      self.$bvModal.show('modal-task-expense');
                      break;
                    // Tệp
                    case 9:
                      self.$bvModal.show('modal-task-file');
                      break;
                    // Phim
                    case 10:
                      self.$bvModal.show('modal-task-video');
                      break;
                  }
                } else {
                  let msg = responsesData.msg;
                  this.$bvToast.toast(msg, {
                    title: 'Thông báo',
                    variant: 'danger',
                    solid: true
                  });
                }

                self.$store.commit('isLoading', false);
              }, (error) => {
                console.log(error);
                self.$store.commit('isLoading', false);
                Swal.fire({
                  title: 'Thông báo',
                  text: 'Không kết nối được với máy chủ',
                  confirmButtonText: 'Đóng'
                });
              });
            }

          },
          onShowModalTrans(data){
            let routerName = '';

            if (data.TransID) {
              switch (data.FeatureKey){
                // ước
                case 'SBPESTIMATEPLAN':
                  routerName = 'statebudgetplanning-sbpestimateplan-view'
                  break;
                // Lập
                case 'SBPMAKEPLAN':
                  routerName = 'statebudgetplanning-sbpestimateplan-view'
                  break;
                // Xem xét
                case 'SBPREVIEWPLAN':
                  routerName = 'statebudgetplanning-sbpestimateplan-view'
                  break;
                // Phê duyệt
                case 'SBPAPPROVALPLAN':
                  routerName = 'statebudgetplanning-sbpestimateplan-view'
                  break;
                // Giao dự toán
                case 'SBPASSIGNPLAN':
                  routerName = 'statebudgetplanning-sbpestimateplan-view'
                  break;

                // Cấp dự toán
                case 'SBPGIVEPLAN':
                  routerName = 'statebudgetplanning-sbpestimateplan-view'
                  break;
                // Phân bổ
                case 'SBPREGUPLAN':
                  routerName = 'statebudgetplanning-sbpestimateplan-view'
                  break;
                default:
                  break;
              }
              // this.$router.push({
              //   path: routerName + '/' + data.TransID,
              // })

              let router = this.$router.resolve({
                name: routerName,
                params: {id: data.TransID}
              });
              window.open(router.href, '_blank');
              return;

            }else {
              switch (data.FeatureKey){
                // ước
                case 'SBPESTIMATEPLAN':
                  routerName = 'statebudgetplanning-sbpestimateplan-create'
                  break;
                  // Lập
                case 'SBPMAKEPLAN':
                  routerName = 'statebudgetplanning-sbpmakeplan-create'
                  break;
                  // Xem xét
                case 'SBPREVIEWPLAN':
                  routerName = 'statebudgetplanning-sbpreviewplan-create'
                  break;
                  // Phê duyệt
                case 'SBPAPPROVALPLAN':
                  routerName = 'statebudgetplanning-sbpapprovalplan-create'
                  break;
                  // Giao dự toán
                case 'SBPASSIGNPLAN':
                  routerName = 'statebudgetplanning-sbpassignplan-create'
                  break;

                // Cấp dự toán
                case 'SBPGIVEPLAN':
                  routerName = 'statebudgetplanning-sbpgiveplan-create'
                  break;
                  // Phân bổ
                case 'SBPREGUPLAN':
                  routerName = 'statebudgetplanning-sbpreguplan-create'
                  break;
                default:
                  break;
              }

              if (routerName) {
                this.$router.push({
                  name: routerName,
                  params: {
                    WFID: data.WFID,
                    WFName: this.model.WFName,
                    WFItemID: data.WFItemID,
                    WFItemName: data.WFItemName,
                    DFID: Number(data.DFID),
                    DFKey: Number(data.DFKey)
                  }
                });
              }
            }
          },
          onShowModalLinkTrans(data){
            this.model.dataflowTrans = data;
            this.stage.onShowModalLinkTrans = !this.stage.onShowModalLinkTrans;
          },
          onHideModalTask(){
            if (!this.stage.showModalTaskAssign) {
              this.stage.editTaskAssign = false;
            }
            this.stage.showModalTaskGeneral = false;
            this.stage.showModalTaskLink = false;
            this.stage.showModalTaskAssign = false;
            this.stage.showModalTaskRequest = false;
            this.stage.showModalTaskCheckList = false;
            this.stage.showModalTaskExpense = false;
            this.stage.showModalTaskExecution = false;
          },
          onSelectDataflow(){
          },
          onEditWorkflow(){
              if (this.model.WFID) {
                this.$router.push({
                  name: 'sysadmin-workflow-edit',
                  params: {id: this.model.WFID}
                });
              }
          },
          onCreateDataflow(){
              this.$router.push({
                  name: 'task-task-create',
                  params: {WFID: this.model.WFID}
              });
              // let router = this.$router.resolve({
              //     name: 'task-task-create',
              //     params: {WFID: this.model.WFID}
              // });
          },
          onEndDataflow(nodeEnd){
            let self = this;
            let requestData = {
              method: 'post',
              url: EndDataflow,
              data: {
                WFID: this.model.taskArray[0].WFID,
                DFKey: this.model.taskArray[0].DFKey,
                TaskIDParent: this.model.taskArray[0].TaskID,
                LineIDTemp: nodeEnd.LineIDTemp,
                WFItemID: nodeEnd.WFItemID
              },
            };
            this.$store.commit('isLoading', true);
            ApiService.customRequest(requestData).then((response) => {
              self.$store.commit('isLoading', false);
              let responseData = response.data;
              if (responseData.status === 1) {
                this.$bvToast.toast('Đã khóa quy trình công việc', {
                  title: 'Thông báo',
                  variant: 'success',
                  solid: true
                });
              } else {
                this.$bvToast.toast('Khóa quy trình công việc thất bại', {
                  title: 'Thông báo',
                  variant: 'warning',
                  solid: true
                });
              }
            }, (error) => {
              console.log(error);
              self.$store.commit('isLoading', false);
              Swal.fire({
                title: 'Thông báo',
                text: 'Không kết nối được với máy chủ',
                confirmButtonText: 'Đóng'
              });
            });
          },
          onClickNode(node){
              console.log('on click node');
          },
          getTitleEmployee(employee){
              let title = employee.EmployeeName;
              if (employee.IsMainResponsiblePerson) {
                  title += ' - Là người chịu trách nhiệm chính';
              }
              return title;
          },
          filterTaskAssignUser(taskID){
              if (taskID) return _.filter(this.model.taskAssignUsers, ['TaskID', taskID]);
              return this.model.taskAssignUsers;
          },
          filterDataflowByTask(task){
              if (!task) return [];
              return _.filter(this.model.dataflowArray, ['DFKey', task.DFKey]);
          },
          filterTaskScheduleUser(TaskID){
              if (TaskID) return _.filter(this.model.taskScheduleUsers, ['TaskID', TaskID]);
              return [];
          },
          handleExportExcel() {
              // Todo: handle export excel
              alert('excel');
          },
          handleExportPrint() {
              // Todo: handle export print
              alert('print');
          },
          autoCorrectedDatePipe: () => { return createAutoCorrectedDatePipe('dd/mm/yyyy') },
          scrollHandle(evt){},

          updateExpense(PTaskExpenseTrans, PTaskExpenseTransItem){
              let self = this;
              self.model.TaskExpenseTransDetailModal = PTaskExpenseTransItem;

              _.forEach(PTaskExpenseTrans, function (item, key) {
                  self.model.TaskExpenseTransModal[item.TransID] = {};
                  item.TransDate = __.convertDateToString(item.TransDate);
                  item.Quantity = __.convertNumberToText(item.Quantity);
                  item.UnitPrice = __.convertNumberToText(item.UnitPrice);
                  item.Amount = __.convertNumberToText(item.Amount);
                  item.TaxRate = __.convertNumberToText(item.TaxRate);
                  item.TaxAmount = __.convertNumberToText(item.TaxAmount);
                  self.model.TaskExpenseTransModal[item.TransID].master = item;
              });
              let TransID = false;
              let arrObject = [];
              _.forEach(PTaskExpenseTransItem, function (item, key) {
                  item.TransDate = __.convertDateToString(item.TransDate);
                  item.Quantity = __.convertNumberToText(item.Quantity);
                  item.UnitPrice = __.convertNumberToText(item.UnitPrice);
                  item.Amount = __.convertNumberToText(item.Amount);
                  item.TaxRate = __.convertNumberToText(item.TaxRate);
                  item.TaxAmount = __.convertNumberToText(item.TaxAmount);
                  if(!TransID){
                      TransID = item.TransID;
                      self.model.TaskExpenseTransDetailModal[key].first = true;
                  }
                  if(TransID!=item.TransID){
                      self.model.TaskExpenseTransModal[TransID].detail = arrObject;
                      arrObject = [];
                      TransID = item.TransID;
                      self.model.TaskExpenseTransDetailModal[key].first = true;
                  }
                  arrObject.push(item);
              });
              if(TransID){
                  self.model.TaskExpenseTransModal[TransID].detail = arrObject;
              }
          },
          changeFile(files){
              let self = this;
              let dateC = __.convertDateTimeToString(new Date());
              for (var i = 0; i < files.length; i++){
                  self.$store.commit('isLoading', true);
                  var file = files[i];
                  let formData = new FormData();
                  formData.append('LineID', '');
                  formData.append('FileUpload', file);
                  formData.append('TaskID', self.model.TaskModal.TaskID);
                  formData.append('FileName', file.name);
                  formData.append('Description', file.name);
                  formData.append('DocID', '');
                  formData.append('DocNo', '');
                  formData.append('DocName', '');
                  formData.append('FileType', file.name.split('.').pop());
                  formData.append('FileSize', file.size);
                  formData.append('DateModified', dateC);
                  formData.append('UserModified', '');
                  formData.append('changeFile', 1);
                  formData.append('changeData', 1);

                  let currentObj = this;
                  const config = {
                      headers: {
                          'content-type': 'multipart/form-data',
                      }
                  };

                  // send upload request
                  axios.post('task/api/task/task-upload-file/' + self.model.TaskModal.TaskID, formData, config)
                      .then(function (response) {
                          currentObj.success = response.data.success;
                          currentObj.filename = "";
                          let dataR = response.data.data;
                          self.model.TaskFileModal.push({
                              LineID: dataR.LineID,
                              FileUpload: file,
                              TaskID : dataR.TaskID,
                              FileID : dataR.FileID,
                              FileName : dataR.FileName,
                              Description : dataR.FileName,
                              FileType : dataR.FileType,
                              FileSize : dataR.FileSize,
                              DateModified :dateC,
                              UserModified : dataR.UserModified,
                              Link : dataR.Link,
                              DateModifiedRoot : '',
                              FileNameRoot : '',
                              DocID : '',
                              DocNo : '',
                              DocName : '',
                              changeFile : 0,//Đã thay đổi file
                              changeData : 0
                          });
                          self.$store.commit('isLoading', false);
                      })
                      .catch(function (error) {
                          // currentObj.output = error;
                      });
              }

          },
          changeVideo(videos){
              let self = this;
              let dateC = __.convertDateTimeToString(new Date());
              for (var i = 0; i < videos.length; i++){
                  self.$store.commit('isLoading', true);
                  var video = videos[i];
                  let formData = new FormData();
                  formData.append('LineID', '');
                  formData.append('VideoUpload', video);
                  formData.append('TaskID', self.model.TaskModal.TaskID);
                  formData.append('VideoName', video.name);
                  formData.append('Description', video.name);
                  formData.append('DocID', '');
                  formData.append('DocNo', '');
                  formData.append('DocName', '');
                  formData.append('VideoType', video.name.split('.').pop());
                  formData.append('VideoSize', video.size);
                  formData.append('DateModified', dateC);
                  formData.append('UserModified', '');
                  formData.append('changeVideo', 1);
                  formData.append('changeData', 1);

                  let currentObj = this;
                  const config = {
                      headers: {
                          'content-type': 'multipart/form-data',
                      }
                  };

                  // send upload request
                  axios.post('task/api/task/task-upload-video/' + self.model.TaskModal.TaskID, formData, config)
                      .then(function (response) {
                          currentObj.success = response.data.success;
                          currentObj.videoname = "";
                          let dataR = response.data.data;
                          self.model.TaskVideoModal.push({
                              LineID: dataR.LineID,
                              VideoUpload: video,
                              TaskID : dataR.TaskID,
                              FileID : dataR.FileID,
                              VideoName : dataR.VideoName,
                              Description : dataR.VideoName,
                              VideoType : dataR.VideoType,
                              VideoSize : dataR.VideoSize,
                              DateModified :dateC,
                              UserModified : dataR.UserModified,
                              Link : dataR.Link,
                              DateModifiedRoot : '',
                              FileNameRoot : '',
                              DocID : '',
                              DocNo : '',
                              DocName : '',
                              changeVideo : 0,//Đã thay đổi file
                              changeData : 0
                          });
                          self.$store.commit('isLoading', false);
                      })
                      .catch(function (error) {
                          // currentObj.output = error;
                      });
              }

          },

          addHashToLocation() {
            let url = '#/task/dataflow';
            if (this.idWorkflow) {
              url += '/' + this.idWorkflow;
              if (this.idDataflow) {
                url += '/' + this.idDataflow;
              }
            }
            history.pushState(
              {},
              null,
              url
            );
          },
          onBackToList(){
            this.$router.push({
              name: 'task-dataflow-list'
            });
          }

        },
        watch: {
            dataflowCurrentPage() {
                this.fetchDataflow(false);
            },
            'model.filterDataflow': {
                handler: function (after, before) {
                    // Changes detected. Do work...
                  this.fetchDataflow(false);
                  this.idDataflow = this.model.filterDataflow.DFKey;
                  this.addHashToLocation();
                },
                deep: true
            },
            'model.filterDataflowDateRange': {
                handler: function (after, before) {
                    // Changes detected. Do work...
                    this.fetchDataflow(false);
                },
                deep: true
            },
            'model.filterTaskAssignUser'(){
                this.fetchDataflow(false);
            },
            'model.filterDataflowStatus'(){
                this.fetchDataflow(false);
            },
            'model.WFID'(){
              let workflow = _.find(this.model.workflowOption, ['id', this.model.WFID]);
              if (workflow) {
                this.model.WFName = (workflow) ? workflow.text : '';
                this.stage.activeTab = 1;
                this.fetchDataflow(true);
              } else {
                this.$bvToast.toast('Không tồn tại quy trình', {
                  title: 'Thông báo',
                  variant: 'warning',
                  solid: true
                });
              }
              this.idWorkflow = this.model.WFID;
              this.addHashToLocation();
            }
        }
    }
</script>

<style lang="css">
    .main-footer-pagination ul {
        margin-bottom: 0;
    }
    .component-dataflow .main-header-actions{
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }
    .select2-container {
        min-width: 200px;
    }
    .dataflow-filter .select2-container {
        width: 100% !important;
    }
    .main-dataflow {
        width: 100%;
        height: 100%;
        padding: 10px 15px;
    }
    .flowchart-diagram {
        height: 40%;
        padding: 15px 0;
        background: #fff;
    }
    .component-dataflow .jtk-task-main, .component-dataflow .jtk-task-canvas{
        height: 100%;
    }
    .component-dataflow .jtk-task-canvas {
        border: none;
    }

    /*======================= process step ===============================*/
    .stepwizard-step p {
        margin-top: 10px;
    }
    .stepwizard {
        display: flex;
        width: 100%;
        position: relative;
        overflow-x: auto;
        padding: 5px 0;
    }
    .stepwizard-step:before{
        top: 14px;
        bottom: 0;
        position: absolute;
        content: " ";
        width: 100%;
        height: 1px;
        background-color: #ccc;
        z-index: 1;
    }
    .stepwizard-step button {
        position: relative;
        z-index: 2;
    }
    .stepwizard-step button[disabled] {
        opacity: 1 !important;
        filter: alpha(opacity=100) !important;
    }
    .stepwizard-step {
        text-align: center;
        position: relative;
        padding-right: 60px;
        width: 160px;
        min-width: 160px;
    }
    .stepwizard .stepwizard-step:last-child:before {
        display: none;
    }
    .btn-circle {
        width: 30px;
        height: 30px;
        text-align: center;
        padding: 6px 0;
        font-size: 12px;
        line-height: 1.428571429;
        border-radius: 15px;
    }

    /*====================== icon task ======================================*/
    .task-icons {
        padding-top: 5px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .task-icons span {
        /*padding: 5px 8px;*/
        border: 1px solid #ccc;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 30px;
        height: 30px;
    }
    .task-icons i{
        font-size: 18px;
    }
    .dataflow-pagination .pagination {
        margin-bottom: 0;
    }

    .timeline .timeline-description span{
      white-space: pre-wrap
    }

    .component-dataflow .dropdown-workflow .dropdown-item {
      white-space: normal;
    }
    .component-dataflow .dropdown-workflow .dropdown-menu {
      min-width: 350px;
    }

    /*======================================== responsive =====================================*/
    @media screen and (max-width: 991px){
        .component-dataflow .dataflow-filter-left, .component-dataflow .dataflow-filter-right{
            flex: 1 1 100%;
        }
    }

</style>

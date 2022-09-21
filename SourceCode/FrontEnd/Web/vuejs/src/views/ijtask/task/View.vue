<template>
  <div class="main-entry component-task-detail" ref="component-task-detail" @mouseover="updateEvent">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Công việc: {{Task.TaskName}}</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onCreateClicked"><i
                class="fa fa-plus"></i> Thêm
              </b-button>
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
          <b-col class="col-md-12">
            <div class="main-header-item main-header-icons">
              <div class="main-header-item-counter ml-auto mr-3" v-if="reqParams">
                <span>{{itemNo}} / {{reqParams.total}}</span>
              </div>
              <b-button-group id="main-header-views" class="main-header-views">
                <b-button id="tooltip-view-prev" @click="onNavigationItem('prev')" v-if="reqParams"
                          class="main-header-view"><i class="fa fa-angle-left"></i></b-button>
                <b-button id="tooltip-view-next" @click="onNavigationItem('next')" v-if="reqParams"
                          class="main-header-view"><i class="fa fa-angle-right"></i></b-button>
                <b-button id="tooltip-view-back-to-list" @click="onBackToList" class="main-header-view"><i
                  class="fa fa-list"></i></b-button>
                <b-tooltip container="main-header-views" target="tooltip-view-back-to-list">Danh sách</b-tooltip>
                <b-tooltip container="main-header-views" target="tooltip-view-prev">Trước</b-tooltip>
                <b-tooltip container="main-header-views" target="tooltip-view-next">Sau</b-tooltip>
              </b-button-group>
              <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen="true"/>
              </div>

            </div>
          </b-col>
        </b-row>
      </div>

    </div>
    <div class="main-body main-body-view-action">
<!--      <vue-perfect-scrollbar class="scroll-area" :settings="$store.state.psSettings">-->
        <div class="container-fluid">
          <b-card>
            <div class="form-body">
              <div class="form-group row ij-line-head" id="task-detail-general-info" style="margin-top: 0px !important;">
                <label class="col-md-4 m-0" @click="showGeneralInfo = !showGeneralInfo">Thông tin chung</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <TaskGeneral v-model="this.Task" :title="'Công việc'" :per="TaskPer">
                    </TaskGeneral>
                    <a @click="showGeneralInfo = !showGeneralInfo" class="ij-a-icon">
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="showGeneralInfo"
                         title="Thu gọn"></i>
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="!showGeneralInfo"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2 ij-content-view" v-model="showGeneralInfo">
                <TaskGeneralContent v-model="this.Task" :per="TaskPer"></TaskGeneralContent>
              </b-collapse>

              <div class="form-group row ij-line-head" id="task-detail-link" v-if="TaskPerLink['Access']">
                <label class="col-md-4 m-0" @click="showTaskLink = !showTaskLink">Liên kết</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <TaskLink v-model="TaskLink" :title="'Liên kết'" :Task="Task" :per="TaskPerLink"></TaskLink>
                    <a @click="showTaskLink = !showTaskLink" class="ij-a-icon">
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="showTaskLink" title="Thu gọn"></i>
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="!showTaskLink" title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showTaskLink" v-if="TaskPerLink['Access']">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <TaskLinkContent v-model="TaskLink" :per="TaskPerLink"></TaskLinkContent>
                  </div>
                </div>
              </b-collapse>

              <div class="form-group row ij-line-head" id="task-detail-assign">
                <label class="col-md-4 m-0" @click="showTaskAssign = !showTaskAssign">Giao việc</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <TaskAssign v-model="TaskAssign" :title="'Giao việc: ' + Task.TaskName" :Task="Task" :isNew="true" :isForm="true"></TaskAssign>
                    <a @click="showTaskAssign = !showTaskAssign" class="ij-a-icon">
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="showTaskAssign" title="Thu gọn"></i>
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="!showTaskAssign" title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showTaskAssign">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <TaskAssignContent v-model="TaskAssign" :Task="Task">
                    </TaskAssignContent>
                  </div>
                </div>
              </b-collapse>

              <div class="form-group row ij-line-head" v-if="TaskPerAssign['Access']">
                <label class="col-md-4 m-0" @click="showTaskPlan = !showTaskPlan">Kế hoạch</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <TaskPlanTransForm v-model="TaskPlanTrans" :title="'Kế hoạch'" :Task="Task"
                     :per="TaskPerPlan" :perGen="TaskPer" :addnew="true"
                     :TaskAssign="TaskAssign"
                    >
                    </TaskPlanTransForm>
                    <a @click="showTaskPlan = !showTaskPlan" class="ij-a-icon">
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="showTaskPlan" title="Thu gọn"></i>
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="!showTaskPlan" title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showTaskPlan" v-if="TaskPerAssign['Access']">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <div class="table-responsive">
                      <table class="not-border">
                        <thead>
                        <tr class="text-left">
                          <th class="pr-3">Ngày lập</th>
                          <th class="pr-3">Ngày BĐ</th>
                          <th class="pr-3">Hạn HT</th>
                          <th class="pr-3">Khối lượng</th>
                          <th class="pr-3">Diễn giải</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, key) in TaskPlanTrans">
                          <td class="pr-3">{{item.TransDate|convertDateToString}}</td>
                          <td class="pr-3">{{item.StartDate|convertDateToString}}</td>
                          <td class="pr-3">{{item.DueDate|convertDateToString}}</td>
                          <td class="pr-3">{{item.EstimatedQuantity|convertNumberToText}}</td>
                          <td class="pr-3">{{item.TransComment}}</td>
                          <td style="text-align: center;width: 53px;right: 0px;" class="right-absolute">
                            <TaskPlanTransForm
                              v-model="TaskPlanTrans" :title="'Kế hoạch'" :Task="Task" :TaskPlanTransProp="TaskPlanTrans"
                              :per="TaskPerPlan" :perGen="TaskPer" :addnew="false"
                              :TaskAssign="TaskAssign" :TransDate="item.TransDate" @changed="updateTaskPlanTrans"></TaskPlanTransForm>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </b-collapse>


              <div class="form-group row ij-line-head" id="task-detail-indicator" >
                <label class="col-md-4 m-0" @click="showTaskIndicator = !showTaskIndicator">Chỉ tiêu ĐGCV</label>
                <div class="col-md-20 float-right">
                  <div class="float-right d-flex">
                    <task-modal-search-indicator
                      v-model="TaskIndicator"
                      :task="Task"
                      ref="myModalSearchInputIndicator"
                      @onSubmitSearch="fetchData"
                      @onClear="fetchData"
                      title-modal="Chỉ tiêu ĐGCV" size-modal="xl"
                      id-modal="myModalSearchInputIndicator">
                    </task-modal-search-indicator>
                    <a @click="showTaskIndicator = !showTaskIndicator" class="ij-a-icon">
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="showTaskIndicator"
                         title="Thu gọn"></i>
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="!showTaskIndicator"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>

              <b-collapse class="mt-2" v-model="showTaskIndicator">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <div class="table-responsive">
                      <table class="not-border">
                        <thead>
                        <tr class="text-left">
                          <th class="pr-3">Chỉ tiêu</th>
                          <th class="pr-3">Nhân viên</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, key) in TaskIndicator">
                          <td class="pr-3" v-if="item.Indicator">{{item.Indicator.IndicatorName}}</td>
                          <td class="pr-3">
                            <div class="d-flex align-items-center">
                              <div v-for="(employee, key) in item.Employee">
                                <span v-if="employee">
                                  {{employee.EmployeeName}}
                                </span>
                                <span v-if="key !== (item.Employee.length - 1)">, </span>
                              </div>
                            </div>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </b-collapse>


              <div class="form-group row ij-line-head" id="task-detail-request" v-if="TaskPerRequest['Access']">
                <label class="col-md-4 m-0" @click="showTaskRequest = !showTaskRequest">Yêu cầu</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <TaskRequest v-model="TaskRequest" :title="'Yêu cầu'" :Task="Task" :per="TaskPerRequest"></TaskRequest>
                    <a @click="showTaskRequest = !showTaskRequest" class="ij-a-icon">
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="showTaskRequest" title="Thu gọn"></i>
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="!showTaskRequest" title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showTaskRequest" v-if="TaskPerRequest['Access']">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <TaskRequestContent v-model="TaskRequest" :per="TaskPerRequest">
                    </TaskRequestContent>
                  </div>
                </div>
              </b-collapse>

              <div class="form-group row ij-line-head" id="task-detail-execution" v-if="TaskPerExecution['Access']">
                <label class="col-md-4 m-0" @click="showTaskExecution = !showTaskExecution">Thực hiện</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <TaskExecutionForm v-model="TaskExecutionTrans" :title="'Thực hiện'" :Task="Task"
                                       :per="TaskPerExecution" :perGen="TaskPer"
                                       :TaskStatus="TaskStatus" :addnew="true"></TaskExecutionForm>
                    <a @click="showTaskExecution = !showTaskExecution" class="ij-a-icon">
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="showTaskExecution"
                         title="Thu gọn"></i>
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="!showTaskExecution"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showTaskExecution" v-if="TaskPerExecution['Access']">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <TaskExecutionContent
                      v-model="TaskExecutionTrans" :TaskStatus="this.TaskStatus" :Task="Task"
                      :per="TaskPerExecution" :perGen="TaskPer">
                    </TaskExecutionContent>
                  </div>
                </div>
              </b-collapse>

              <div class="form-group row ij-line-head" id="task-detail-check-list" v-if="TaskPerChecklist['Access']">
                <label class="col-md-4 m-0" @click="showTaskCheckList = !showTaskCheckList">Kiểm tra</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">

                    <TaskCheckList v-model="TaskCheckList" :title="'Kiểm tra'" :Task="Task" :per="TaskPerChecklist">
                    </TaskCheckList>
                    <a @click="showTaskCheckList = !showTaskCheckList" class="ij-a-icon">
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="showTaskCheckList" title="Thu gọn"></i>
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="!showTaskCheckList" title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showTaskCheckList" v-if="TaskPerChecklist['Access']">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <TaskCheckListContent v-model="TaskCheckList" :per="TaskPerChecklist">
                    </TaskCheckListContent>
                  </div>
                </div>
              </b-collapse>

              <div class="form-group row ij-line-head" id="task-detail-evaluation" v-if="TaskPerValuation['Access']">
                <label class="col-md-4 m-0" @click="showTaskValuation = !showTaskValuation">Đánh giá</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <div class="evaluation-content" style="position: absolute; right: 56px; bottom: 0px;float: left;">
                      <b-form-select style=" width: 90px;margin-right: 15px;float: left;" v-model="FrequencyType"
                                     @change="changeFrequencyType" :options="[
                                        {value: 6, text: 'Ngày'},
                                        {value: 5, text: 'Tuần'},
                                        {value: 4, text: 'Tháng'},
                                        {value: 3, text: 'Quý'},
                                        {value: 2, text: '6 tháng'},
                                        {value: 1, text: 'Năm'},
                                        {value: 7, text: 'Vụ việc'},
                                        ]"
                                     :settings="{multiple: true, allowClear: true, placeholder: {id: 0, text: 'Chọn tần suất'}}">
                      </b-form-select>
                      <b-form-select v-model="FrequencyYear" v-if="FrequencyType==1" style="width: 80px;"
                                     :options="FrequencyYearOptions" @change="changeFrequencyType">
                      </b-form-select>
                      <IjcoreDatePicker class="datepicker-fill-content"  style="float: left;margin-right: 15px;" v-model="FrequencyFromDate" @input="fetchDataEvaluation" v-if="FrequencyType==6">
                      </IjcoreDatePicker>
                      <IjcoreDatePicker class="datepicker-fill-content" style="float: left;" v-model="FrequencyToDate" @input="fetchDataEvaluation" v-if="FrequencyType==6">
                      </IjcoreDatePicker>
                      <b-form-select v-model="Frequency6Month" v-if="FrequencyType==2" style="width: 150px;" :options="[
                                        {value: 1, text: 'Đầu năm/'+FrequencyYear},
                                        {value: 2, text: 'Cuối năm/'+FrequencyYear},]" @change="fetchDataEvaluation">
                      </b-form-select>

                      <b-form-select v-model="FrequencyQuarter" v-if="FrequencyType==3" style="width: 120px;" :options="[
                                        {value: 1, text: 'Quý I/'+FrequencyYear},
                                        {value: 2, text: 'Quý II/'+FrequencyYear},
                                        {value: 3, text: 'Quý III/'+FrequencyYear},
                                        {value: 4, text: 'Quý IV/'+FrequencyYear},]" @change="fetchDataEvaluation">
                      </b-form-select>

                      <b-form-select v-model="FrequencyMonth" v-if="FrequencyType==4" style="width: 150px;" :options="[
                                        {value: 1, text: 'Tháng 01/'+FrequencyYear},
                                        {value: 2, text: 'Tháng 02/'+FrequencyYear},
                                        {value: 3, text: 'Tháng 03/'+FrequencyYear},
                                        {value: 4, text: 'Tháng 04/'+FrequencyYear},
                                        {value: 5, text: 'Tháng 05/'+FrequencyYear},
                                        {value: 6, text: 'Tháng 06/'+FrequencyYear},
                                        {value: 7, text: 'Tháng 07/'+FrequencyYear},
                                        {value: 8, text: 'Tháng 08/'+FrequencyYear},
                                        {value: 9, text: 'Tháng 09/'+FrequencyYear},
                                        {value: 10, text: 'Tháng 10/'+FrequencyYear},
                                        {value: 11, text: 'Tháng 11/'+FrequencyYear},
                                        {value: 12, text: 'Tháng 12/'+FrequencyYear},]" @change="fetchDataEvaluation">
                      </b-form-select>

                      <b-form-select v-model="FrequencyWeek" v-if="FrequencyType==5" style="width: 140px;"
                                     :options="OptionWeekCurrentYear" @change="fetchDataEvaluation">
                      </b-form-select>
                    </div>
                    <TaskValuationForm v-model="TaskEvaluation1Job"
                                       :title="'Đánh giá'" :Task="Task" :per="TaskPerValuation" :perGen="TaskPer"
                                       :TaskStatus="TaskStatus"
                                       :CurrentDateTemp="CurrentDate"
                                       :FrequencyTypeTemp="FrequencyType"
                                       :FrequencyYearTemp="FrequencyYear"
                                       :Frequency6MonthTemp="Frequency6Month"
                                       :FrequencyQuarterTemp="FrequencyQuarter"
                                       :FrequencyMonthTemp="FrequencyMonth"
                                       :FrequencyWeekTemp="FrequencyWeek"
                                       :FrequencyFromDateTemp="FrequencyFromDate"
                                       :FrequencyToDateTemp="FrequencyToDate"
                                       :FrequencyYearOptionsTemp="FrequencyYearOptions"
                                       :OptionWeekCurrentYearTemp="OptionWeekCurrentYear"
                                       :TaskEvaluator1Job="TaskEvaluator1Job" :addnew="true"
                                       @changed="emitEvaluationForm">
                    </TaskValuationForm>
                    <a @click="showTaskValuation = !showTaskValuation" class="ij-a-icon">
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="showTaskValuation"
                         title="Thu gọn"></i>
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="!showTaskValuation"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showTaskValuation" v-if="TaskPerValuation['Access']">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <TaskValuationContent ref="TaskValuationContent" v-model="TaskEvaluation1Job"
                                          :TaskEvaluation1JobArr="TaskEvaluation1JobArr"
                                          :TaskEvaluator1Job="TaskEvaluator1Job"
                                          :ScaleRateItem="this.ScaleRateItem" :TaskStatus="this.TaskStatus" :Task="Task"
                                          :CurrentDateTemp="CurrentDate"
                                          :FrequencyTypeTemp="FrequencyType"
                                          :FrequencyYearTemp="FrequencyYear"
                                          :Frequency6MonthTemp="Frequency6Month"
                                          :FrequencyQuarterTemp="FrequencyQuarter"
                                          :FrequencyMonthTemp="FrequencyMonth"
                                          :FrequencyWeekTemp="FrequencyWeek"
                                          :FrequencyFromDateTemp="FrequencyFromDate"
                                          :FrequencyToDateTemp="FrequencyToDate"
                                          :FrequencyYearOptionsTemp="FrequencyYearOptions"
                                          :OptionWeekCurrentYearTemp="OptionWeekCurrentYear"
                                          :per="TaskPerValuation" :perGen="TaskPer"
                                          @changed="emitEvaluationForm">
                    </TaskValuationContent>
                  </div>
                </div>
              </b-collapse>


              <div class="form-group row ij-line-head" id="task-detail-expense" v-if="TaskPerExpenseDetail['Access']">
                <label class="col-md-4 m-0" @click="showTaskExpense = !showTaskExpense">Chi phí</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <TaskExpense v-model="TaskExpenseTrans" :TaskExpenseTrans="TaskExpenseTrans" :title="'Chi phí'"
                                 v-if="TaskPerExpense['Edit']"
                                 :Task="Task" :isNew="true" @transferExpense="updateExpense" :per="TaskPerExpense"
                                 :perDetail="TaskPerExpenseDetail">
                    </TaskExpense>
                    <a @click="showTaskExpense = !showTaskExpense" class="ij-a-icon">
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="showTaskExpense"
                         title="Thu gọn"></i>
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="!showTaskExpense"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showTaskExpense" v-if="TaskPerExpenseDetail['Access']">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <table class="not-border">
                      <thead>
                      <tr class="text-left">
                        <th class="pr-3" v-if="ViewPerExpenseDetailTransType">Loại</th>
                        <th class="pr-3" v-if="ViewPerExpenseDetailTransDate">Ngày</th>
                        <th class="pr-3" v-if="ViewPerExpenseDetailDescription">Nội dung chi</th>
                        <th class="pr-3" v-if="ViewPerExpenseDetailUomID">Đơn vị tính</th>
                        <th class="pr-3" v-if="ViewPerExpenseDetailQuantity">Số lượng</th>
                        <th class="pr-3" v-if="ViewPerExpenseDetailUnitPrice">Đơn giá</th>
                        <th class="pr-3" v-if="ViewPerExpenseDetailAmount">Thành tiền</th>
                        <th class="pr-3" v-if="ViewPerExpenseDetailTaxRate">Thuế suất</th>
                        <th class="pr-3" v-if="ViewPerExpenseDetailTaxAmount">Tiền thuế</th>
                        <th class="td-action"></th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr v-for="(item, key) in this.TaskExpenseTransDetail">
                        <td class="pr-3" v-if="ViewPerExpenseDetailTransType">{{TransTypeText[item.TransType]}}</td>
                        <td class="pr-3" v-if="ViewPerExpenseDetailTransDate">{{item.TransDate}}</td>
                        <td class="pr-3" v-if="ViewPerExpenseDetailDescription">{{item.Description}}</td>
                        <td class="pr-3" v-if="ViewPerExpenseDetailUomID">{{item.UomName}}</td>
                        <td class="pr-3" v-if="ViewPerExpenseDetailQuantity">{{item.Quantity|convertNumberToText}}</td>
                        <td class="pr-3" v-if="ViewPerExpenseDetailUnitPrice">{{item.UnitPrice|convertNumberToText}}
                        </td>
                        <td class="pr-3" v-if="ViewPerExpenseDetailAmount">{{item.Amount|convertNumberToText}}</td>
                        <td class="pr-3" v-if="ViewPerExpenseDetailTaxRate">{{item.TaxRate|convertNumberToText}}</td>
                        <td class="pr-3" v-if="ViewPerExpenseDetailTaxAmount">{{item.TaxAmount|convertNumberToText}}
                        </td>
                        <td class="right-absolute">
                          <TaskExpense v-model="TaskExpenseTrans" :TaskExpenseTrans="TaskExpenseTrans[item.TransID]"
                                       :per="TaskPerExpense" :per-detail="TaskPerExpenseDetail"
                                       :title="'Chi phí'" :Task="Task" :isNew="false"
                                       v-if="item.first && TaskPerExpense['Edit']"
                                       @transferExpense="updateExpense">
                          </TaskExpense>
                        </td>
                      </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </b-collapse>

              <div class="form-group row ij-line-head" id="task-detail-file" v-if="TaskPerFile['Access']">
                <label class="col-md-4 m-0" @click="showTaskFile = !showTaskFile">Tệp</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <span @click="downloadAllFile" style="font-size: 18px; cursor: pointer" title="Tải tất cả tệp"><i class="fa fa-download"></i></span>
                    <IjcoreUploadMultipleFile
                      v-on:changed="changeFile" :isIcon="true"
                      v-if="TaskPerFile['Edit']"></IjcoreUploadMultipleFile>
                    <a @click="showTaskFile = !showTaskFile" class="ij-a-icon">
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="showTaskFile" title="Thu gọn"></i>
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="!showTaskFile" title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showTaskFile" v-if="TaskPerFile['Access']">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <TaskFileContent v-model="TaskFile" :Task="Task" :per="TaskPerFile">
                    </TaskFileContent>
                  </div>
                </div>
              </b-collapse>

              <div class="form-group row ij-line-head" id="task-detail-video" v-if="TaskPerVideo['Access']">
                <label class="col-md-4 m-0" @click="showTaskVideo = !showTaskVideo">Phim</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <IjcoreUploadMultipleVideo v-on:changed="changeVideo" :isIcon="true" v-if="TaskPerVideo['Edit']"></IjcoreUploadMultipleVideo>
                    <a @click="showTaskVideo = !showTaskVideo" class="ij-a-icon">
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="showTaskVideo" title="Thu gọn"></i>
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="!showTaskVideo" title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showTaskVideo" v-if="TaskPerVideo['Access']">
                <div class="form-group row">
                  <div class="col-md-24 m-0">
                    <TaskVideoContent v-model="TaskVideo" :Task="Task" :per="TaskPerVideo">
                    </TaskVideoContent>
                  </div>
                </div>
              </b-collapse>

              <div class="form-group row ij-line-head" id="task-detail-comment">
                <label class="col-md-4 m-0" @click="showTaskComment = !showTaskComment">Thảo luận</label>
                <div class="col-md-20 float-right">
                  <div class="float-right">
                    <a @click="showTaskComment = !showTaskComment" class="ij-a-icon">
                      <i class="fa fa-expand ij-icon expand-compress" aria-hidden="true" v-if="showTaskComment"
                         title="Thu gọn"></i>
                      <i class="fa fa-compress ij-icon expand-compress" aria-hidden="true" v-if="!showTaskComment"
                         title="Mở rộng"></i>
                    </a>
                  </div>
                </div>
              </div>
              <b-collapse class="mt-2" v-model="showTaskComment">
                <div class="form-group row">
                  <div class="col-lg-24">
                    <chat-category-comment v-if="showTaskComment" :CategoryKey="{task: idParams}" Category="task" :CategoryID="idParams"></chat-category-comment>
                  </div>
                </div>
              </b-collapse>
            </div>
          </b-card>
        </div>
<!--      </vue-perfect-scrollbar>-->
    </div>
  </div>
</template>

<script>
  import ApiService from '@/services/api.service';
  import Swal from 'sweetalert2';
  import 'sweetalert2/src/sweetalert2.scss';
  import TaskGeneral from "./TaskGeneral";
  import TaskGeneralContent from "./partials/TaskGeneralContent";
  import TaskLinkContent from "./partials/TaskLinkContent";
  import TaskAssignContent from "./partials/TaskAssignContent";
  import TaskRequestContent from "./partials/TaskRequestContent";
  import TaskExecutionContent from "./partials/TaskExecutionContent";
  import TaskCheckListContent from "./partials/TaskCheckListContent";
  import TaskExpenseContent from "./partials/TaskExpenseContent";
  import TaskFileContent from "./partials/TaskFileContent";
  import TaskVideoContent from "./partials/TaskVideoContent";
  import TaskAssign from "./TaskAssign";
  import TaskLink from "./TaskLink";
  import TaskRequest from "./TaskRequest";
  import TaskCheckList from "./TaskCheckList";
  import TaskExpense from "./TaskExpense";
  import TaskFile from "./TaskFile";
  import TaskVideo from "./TaskVideo";
  import TaskExecutionForm from "./partials/TaskExecutionForm";
  import IjcoreUploadInputMultipleFile from "../../../components/IjcoreUploadInputMultipleFile";
  import IjcoreUploadMultipleFile from "../../../components/IjcoreUploadMultipleFile";
  import IjcoreUploadMultipleVideo from "../../../components/IjcoreUploadMultipleVideo";
  import TaskValuation from "./TaskValuation";
  import TaskValuationContent from "./partials/TaskValuationContent";
  import TaskValuationForm from "./partials/TaskValuationForm";
  import TaskModalSearchIndicator from './partials/TaskModalSearchIndicator';
  import TaskComment from "./TaskComment";
  import ChatCategoryComment from "../../apps/chat/partials/ChatCategoryComment";
  import moment from 'moment';
  import IjcoreDatePicker from "../../../components/IjcoreDatePicker";
  import TaskPlanTransForm from "./partials/TaskPlanTransForm";

  const ListRouter = 'task-task-list';
  const EditRouter = 'task-task-edit';
  const CreateRouter = 'task-task-create';
  const ViewRouter = 'task-task-view';
  const DetailApi = 'task/api/task/view';
  const EditApi = 'task/api/task/edit';
  const CreateApi = 'task/api/task/create';
  const StoreApi = 'task/api/task/store';
  const UpdateApi = 'task/api/task/update';
  const DeleteApi = 'task/api/task/delete';
  const ListApi = 'task/api/task';
  let WorkdateTemp = localStorage.getItem('workdate');
  let Workdate = WorkdateTemp.split("/");
  let CurrentDateTemp = new Date(Workdate[2], Workdate[1] - 1, Workdate[0]);
  export default {
    name: 'listing-detail-task',
    data() {
      return {
        showGeneralInfo: true,
        showTaskLink: false,
        showTaskAssign: false,
        showTaskRequest: false,
        showTaskPlan: false,
        showTaskExecution: false,
        showTaskCheckList: false,
        showTaskValuation: false,
        showTaskExpense: false,
        showTaskFile: false,
        showTaskVideo: false,
        showTaskComment: false,
        initTaskComment: false,
        showTaskIndicator: false,
        idParams: this.$route.params.id,
        reqParams: this.$route.params.req,
        TransTypeText: {'1': "Kế hoạch", '2': "Thực tế"},
        FrequencyType: 6,
        CurrentDate: CurrentDateTemp,
        FrequencyYear: CurrentDateTemp.getFullYear(),
        FrequencyYearOptions: {},
        Frequency6Month: CurrentDateTemp.getMonth() + 1 > 6 ? 2 : 1,
        FrequencyQuarter: Math.ceil((CurrentDateTemp.getMonth() + 1) / 3),
        FrequencyMonth: CurrentDateTemp.getMonth() + 1,
        FrequencyWeek: {},
        FrequencyFromDate: '',//localStorage.getItem('workdate'),
        FrequencyToDate: '',//localStorage.getItem('workdate'),
        OptionWeekCurrentYear: {},
        model: {
          customerCodeName: '',
          parentID: '',
          parentName: '',
          customerCodeValue: [],
          customerCodeList: [],
          dataTypeOption: {
            1: 'Số',
            2: 'Kí tự',
            3: 'Ngày',
            4: 'Ngày giờ',
            5: 'Có/Không',
            6: 'Đúng/Sai'
          }
        },
        Task: {
          TaskID: '',
          TaskNo: '',
          TaskName: '',
          ParentID: '',
          ParentName: '',
          Level: '',
          Detail: '',
          UomID: '',
          UomName: '',
          TaskDescription: '',
          Priority: '',
          PriorityName: '',
          Status: '',
          AccessType: '',
          AccessTypeName: '',
          CreateDate: '',
          CreateEmployeeID: '',
          AssignEmployeeID: '',
          ResponEmployeeID: '',
          CheckEmployeeID: '',
          StartDate: '',
          DueDate: '',
          Duration: '',
          EstimatedQuantity: '',
          ActualCompletedDate: '',
          ActualWork: '',
          ActualQuantity: '',
          OldActualQuantity: '',
          TotalActualQuantity: '',
          CalMethod: '',
          CalMethodName: '',
          isActualQuantity: false,
          PercentCompleted: '',
          PerformingDescription: '',
          NOrder: '',
          Inactive: '',
          TaskCodeID: '',
          CalendarTypeID: '',
          CalendarName: '',
          CompanyID: '',
          CompanyName: '',
          PublicCompanyID: '',
          NumberValue: '',
          Prefix: '',
          Suffix: '',
          StatusValue: '',
          StatusDescription: '',
          statusHour: '',
          PriorityOptions: [],
          AccessTypeOptions: [],
          CompanyOptions: [],
          CalendarOptions: [],
          UomOptions: [],
          StatusValueOption: [],
          TaskCate: [],
          TaskCateListOption: [],
          TaskCateValueOption: [],

          IsMainResponsiblePerson: -1
        },
        TaskLink: {},
        TaskAssign: {},
        TaskRequest: {},
        TaskPlanTrans: {},
        TaskExecutionTrans: {},
        // TaskValuation: {},
        TaskEvaluation1Job: {},
        ScaleRateItem: {},
        TaskEvaluation1JobArr: {},
        TaskEvaluator1Job: {},
        TaskExpenseTransDetail: {},
        TaskExpenseTrans: {
          master: {TransType: '', TransDate: '', Comment: '', TransID: ''},
          detail: []
        },
        TaskCheckList: {},
        TaskFile: {},
        TaskVideo: {},
        TaskStatus: {},
        defaultModel: {},
        stage: {
          updatedData: false,
          message: (this.$route.params.message) ? this.$route.params.message : ''
        },
        TaskPer: {},
        TaskPerAssign: {},
        TaskPerCate: {},
        TaskPerLink: {},
        TaskPerPlan: {},
        TaskPerExecution: {},
        TaskPerChecklist: {},
        TaskPerValuation: {},
        TaskPerExpense: {},
        TaskPerExpenseDetail: {},
        TaskPerFile: {},
        TaskPerRequest: {},
        TaskPerVideo: {},

        TaskIndicator: [],

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
      }

    },

    components: {
      TaskPlanTransForm,
      IjcoreDatePicker,
      TaskValuationForm,
      TaskValuationContent,
      TaskValuation,
      IjcoreUploadMultipleVideo,
      IjcoreUploadMultipleFile,
      TaskExecutionForm,
      TaskVideo,
      TaskFile,
      TaskExpense,
      TaskCheckList,
      TaskRequest,
      TaskLink,
      TaskAssign,
      TaskVideoContent,
      TaskFileContent,
      TaskExpenseContent,
      TaskCheckListContent,
      TaskExecutionContent,
      TaskRequestContent,
      TaskAssignContent,
      TaskLinkContent,
      TaskGeneralContent,
      TaskGeneral,
      TaskModalSearchIndicator,
      TaskComment,
      ChatCategoryComment
    },
    beforeCreate() {
      if (!this.$route.params.id) {
        this.$router.push({name: ListRouter});
      }
    },
    mounted() {
      this.fetchData();
      // hiển thị thông báo
      if (this.stage.message && this.stage.message !== '') {
        this.$bvToast.toast(this.stage.message, {
          title: 'Thông báo',
          variant: 'success',
          solid: true
        });
      }
    },
    created() {
    },
    updated() {
      this.stage.updatedData = true;
    },
    computed: {
      itemNo() {
        let index = 0;
        index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
        return index;
      }
    },
    methods: {
      updateTaskPlanTrans(value){
        this.TaskPlanTrans = value;
        this.$forceUpdate()
      },
      emitEvaluationForm($FrequencyType, $TransDate, $FrequencyWeek){
        let self = this;
        self.FrequencyType = $FrequencyType;
        self.TransDate = $TransDate;
        let TransDateArr = $TransDate.split("/");
        self.FrequencyYear = TransDateArr[2];
        switch (self.FrequencyType) {
          case 2:
            self.Frequency6Month = Math.round(TransDateArr[1]/6);
            break;
          case 3:
            self.FrequencyQuarter = Math.round(TransDateArr[1]/3);
            break;
          case 4:
            self.FrequencyMonth = parseInt(TransDateArr[1])
            break;
          case 5:
            self.FrequencyWeek = $FrequencyWeek
            break;
          case 6:
            self.FrequencyFromDate = '';
            self.FrequencyToDate = '';
            break;
          case 7:
            self.FrequencyFromDate = '';
            self.FrequencyToDate = '';
            break;
          default:
            break;
        }
        self.fetchDataEvaluation();
        // this.$refs['TaskValuationContent'].updateFromForm();
        self.$forceUpdate()
      },
      updateEvent(){
        if(WorkdateTemp != localStorage.getItem('workdate')){
          WorkdateTemp = localStorage.getItem('workdate');
          Workdate = WorkdateTemp.split("/");
          CurrentDateTemp = new Date(Workdate[2], Workdate[1] - 1, Workdate[0]);
          this.CurrentDate = CurrentDateTemp
          this.FrequencyYear = CurrentDateTemp.getFullYear()
          this.Frequency6Month = CurrentDateTemp.getMonth() + 1 > 6 ? 2 : 1
          this.FrequencyQuarter = Math.ceil((CurrentDateTemp.getMonth() + 1) / 3)
          this.FrequencyMonth = CurrentDateTemp.getMonth() + 1
          this.FrequencyFromDate = localStorage.getItem('workdate')
          this.FrequencyToDate = localStorage.getItem('workdate')
          this.FrequencyYearOptions = [];
          for(var i = this.FrequencyYear - 5; i<= this.FrequencyYear + 5; i++){
            let obj = {
              value: i,
              text: i
            };
            this.FrequencyYearOptions.push(obj)
          }
          this.getCurrentWeek()
          this.$forceUpdate()
          this.fetchDataEvaluation()
        }
      },
      getCurrentWeek() {
        var start = moment(this.CurrentDate.getFullYear() + "-01-01", "YYYY-MM-DD");
        var end = moment(this.CurrentDate.getFullYear() + "-" + (this.CurrentDate.getMonth() + 1) + "-" + this.CurrentDate.getDate(), "YYYY-MM-DD");
        var end_year = moment(this.CurrentDate.getFullYear() + "-12-31", "YYYY-MM-DD");
        let NumberWeekInCurentYear = Math.ceil((end_year.diff(start, 'days') - end_year.day())/7) + 1;
        this.OptionWeekCurrentYear = [];
        let self = this;
        for (var i = 1; i <= NumberWeekInCurentYear; i++) {
          let txt = 'Tuần ' + i + '/'+self.FrequencyYear;
          if(i < 10){
            txt = 'Tuần 0' + i + '/'+self.FrequencyYear;
          }
          let obj = {
            value: i,
            text: txt
          };
          self.OptionWeekCurrentYear.push(obj)
        }
        self.FrequencyWeek = Math.ceil((end.diff(start, 'days') - end_year.day())/7) + 1;
      },
      changeFrequencyType() {
        if(this.FrequencyType != 1){
          this.FrequencyYear = this.CurrentDate.getFullYear()
        }
        this.fetchDataEvaluation()
      },
      fetchDataEvaluation() {
        let self = this;
        let urlApi = 'task/api/task/fetch-evaluation-1job-content';
        let requestData = {
          method: 'post',
        };
        let data = {
          TaskID: self.Task.TaskID,
          EmployeeID: self.EmployeeIDTemp?self.EmployeeIDTemp:self.EmployeeID,
          TransDate: self.TransDateTemp?self.TransDateTemp:self.TransDate,
          FrequencyType: self.FrequencyType
        };
        if(self.FrequencyType == 1){
          data.FrequencyYear = self.FrequencyYear
        }
        switch (self.FrequencyType) {
          case 1:
            data.FrequencyYear = self.FrequencyYear;
            break;
          case 2:
            data.FrequencyYear = self.FrequencyYear;
            data.Frequency6Month = self.Frequency6Month;
            break;
          case 3:
            data.FrequencyYear = self.FrequencyYear;
            data.FrequencyQuarter = self.FrequencyQuarter;
            break;
          case 4:
            data.FrequencyYear = self.FrequencyYear;
            data.FrequencyMonth = self.FrequencyMonth;
            break;
          case 5:
            data.FrequencyYear = self.FrequencyYear;
            data.FrequencyWeek = self.FrequencyWeek;
            //Đánh giá theo tuần
            var start = moment(self.FrequencyYear + "-01-01", "YYYY-MM-DD");
            var numberDay = 8 - start.day() + (self.FrequencyWeek-1)*7;
            var startTemp = start;
            data.TransDate = start.add('days', numberDay).format('DD/MM/YYYY');
            var ArrTransDateTemp = data.TransDate.split("/");
            if(ArrTransDateTemp[2] != undefined && ArrTransDateTemp[2] > self.FrequencyYear){
              data.TransDate = '31/12/'+self.FrequencyYear;
            }
            break;
          case 6:
            data.FrequencyYear = self.FrequencyYear;
            data.FrequencyFromDate = self.FrequencyFromDate;
            data.FrequencyToDate = self.FrequencyToDate;
            break;
          case 7:
            data.FrequencyYear = self.FrequencyYear;
            data.Frequency6Month = self.Frequency6Month;
            break;
          default:
            break;
        }
        requestData.data = data;
        requestData.url = urlApi;
        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            ///////////////////////////////////////////////////////
            self.TaskEvaluation1Job = responsesData.data.TaskEvaluation1Job;
            self.TaskEvaluation1JobArr = {};
            _.forEach(self.TaskEvaluation1Job, function (item, key) {
              if(item.EvaluatorID){
                self.TaskEvaluation1JobArr[item.EmployeeID + "_" + item.TransDate + "_" + item.IndicatorID + "_" + item.EvaluatorID] = item;

                self.TaskEvaluator1Job[item.EvaluatorID] = {
                  EvaluatorID: item.EvaluatorID,
                  EvaluatorName: item.EvaluatorName
                };
              }
            });
            self.$refs['TaskValuationContent'].updatePoint(self.TaskEvaluation1Job);
            self.$forceUpdate();
          } else {
          }

          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      fetchData() {
        this.getCurrentWeek();
        this.FrequencyYearOptions = [];
        for(let i = this.FrequencyYear - 5; i<= this.FrequencyYear + 5; i++){
          let obj = {
            value: i,
            text: i
          };
          this.FrequencyYearOptions.push(obj)
        }
        if (this.idParams == 0 || _.isUndefined(this.idParams)) {
          return false;
        }
        let self = this;
        let urlApi = '';
        let requestData = {
          method: 'get',
        };
        // Api edit user
        if (this.idParams) {
          urlApi = DetailApi + '/' + this.idParams;
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
          self.defaultModel = responsesData.data.Task;
          if (responsesData.status === 1) {
            self.TaskPer = responsesData.data.PerAll['TaskPer'];
            self.TaskPerAssign = responsesData.data.PerAll['TaskPerAssign'];
            self.TaskPerCate = responsesData.data.PerAll['TaskPerCate'];
            self.TaskPerLink = responsesData.data.PerAll['TaskPerLink'];
            self.TaskPerExecution = responsesData.data.PerAll['TaskPerExecution'];
            self.TaskPerPlan = responsesData.data.PerAll['TaskPerPlan'];
            self.TaskPerChecklist = responsesData.data.PerAll['TaskPerChecklist'];
            self.TaskPerValuation = responsesData.data.PerAll['TaskPerValuation'];
            self.TaskPerExpense = responsesData.data.PerAll['TaskPerExpense'];
            self.TaskPerExpenseDetail = responsesData.data.PerAll['TaskPerExpenseDetail'];

            self.ViewPerExpenseDetailTransType = __.perViewColumn(self.TaskPerExpenseDetail, 'TransType')
            self.ViewPerExpenseDetailTransDate = __.perViewColumn(self.TaskPerExpenseDetail, 'TransDate')
            self.ViewPerExpenseDetailExpenseID = __.perViewColumn(self.TaskPerExpenseDetail, 'ExpenseID')
            self.ViewPerExpenseDetailDescription = __.perViewColumn(self.TaskPerExpenseDetail, 'Description')
            self.ViewPerExpenseDetailUomID = __.perViewColumn(self.TaskPerExpenseDetail, 'UomID')
            self.ViewPerExpenseDetailQuantity = __.perViewColumn(self.TaskPerExpenseDetail, 'Quantity')
            self.ViewPerExpenseDetailUnitPrice = __.perViewColumn(self.TaskPerExpenseDetail, 'UnitPrice')
            self.ViewPerExpenseDetailAmount = __.perViewColumn(self.TaskPerExpenseDetail, 'Amount')
            self.ViewPerExpenseDetailTaxRate = __.perViewColumn(self.TaskPerExpenseDetail, 'TaxRate')
            self.ViewPerExpenseDetailTaxAmount = __.perViewColumn(self.TaskPerExpenseDetail, 'TaxAmount')

            self.TaskPerFile = responsesData.data.PerAll['TaskPerFile'];
            self.TaskPerRequest = responsesData.data.PerAll['TaskPerRequest'];
            self.TaskPerVideo = responsesData.data.PerAll['TaskPerVideo'];
            self.Task = responsesData.data.Task;
            self.Task.StartDate = __.convertDateToString(self.Task.StartDate);
            self.Task.DueDate = __.convertDateToString(self.Task.DueDate);
            self.Task.statusHour = 0;
            self.Task.PriorityOptions = responsesData.data.PriorityOptions;
            self.Task.AccessTypeOptions = responsesData.data.AccessTypeOptions;
            self.Task.OldTotalActualQuantity = self.Task.TotalActualQuantity;
            self.Task.OldActualQuantity = self.Task.ActualQuantity;

            // self.Task.TaskCate = [];
            self.$set(self.Task, 'TaskCate', []);
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
                self.$set(self.Task.TaskCate, self.Task.TaskCate.length, tmpCate);
                // self.Task.TaskCate.push(tmpCate);
              });
            }

            if (self.Task.CalMethod == 1) {
              self.Task.CalMethodName = "Theo thời gian";
              self.Task.isActualQuantity = true;
            } else {
              self.Task.CalMethodName = "Theo khối lượng";
              self.Task.isActualQuantity = false;
            }
            self.Task.CompanyOptions = [];
            _.forEach(responsesData.data.CompanyOptions, function (value, key) {
              let tmpObj = {};
              tmpObj.value = value.CompanyID;
              tmpObj.text = value.CompanyName;
              self.Task.CompanyOptions.push(tmpObj);
            });
            if (self.Task.CompanyID) {
              self.Task.CompanyName = _.find(self.Task.CompanyOptions, ['value', self.Task.CompanyID]).text;
            }
            self.Task.UomOptions = [];
            _.forEach(responsesData.data.UomOptions, function (value, key) {
              let tmpObj = {};
              tmpObj.value = value.UomID;
              tmpObj.text = value.UomName;
              self.Task.UomOptions.push(tmpObj);
            });

            self.TaskStatus = [];
            _.forEach(responsesData.data.TaskStatus, function (value, key) {
              let tmpObj = {};
              tmpObj.value = value.StatusValue;
              tmpObj.text = value.StatusDescription;
              tmpObj.PercentCompleted = value.PercentCompleted;
              tmpObj.ExecutionStatus = value.ExecutionStatus;
              self.TaskStatus.push(tmpObj);
            });
            let taskUom = _.find(self.Task.UomOptions, ['value', self.Task.UomID]);
            if (taskUom) {
              self.Task.UomName = taskUom.text;
            }


            self.Task.CalendarOptions = [];
            _.forEach(responsesData.data.CalendarOptions, function (value, key) {
              let tmpObj = {};
              tmpObj.value = value.CalendarTypeID;
              tmpObj.text = value.CalendarName;
              self.Task.CalendarOptions.push(tmpObj);
            });
            let taskCalendar = _.find(self.Task.CalendarOptions, ['value', self.Task.CalendarTypeID]);
            if (taskCalendar) {
              self.Task.CalendarName = taskCalendar.text;
            }

            self.Task.StatusValueOption = [];
            _.forEach(responsesData.data.StatusValueOption, function (value, key) {
              let tmpObj = {};
              tmpObj.value = value.StatusValue;
              tmpObj.text = value.StatusDescription;
              self.Task.StatusValueOption.push(tmpObj);
            });

            self.TaskLink = responsesData.data.TaskLink;

            self.TaskAssign = responsesData.data.TaskAssign;
            _.forEach(self.TaskAssign, function (item, key) {
              self.TaskAssign[key].StartDate = __.convertDateToString(item.StartDate);
              self.TaskAssign[key].DueDate = __.convertDateToString(item.DueDate);
              self.TaskAssign[key].EstimatedHour = item.EstimatedHour;
              self.TaskAssign[key].PersonAssign = [];
              if (self.TaskAssign[key].IsAssignee == 1) {
                self.TaskAssign[key].PersonAssign.push(1);
              }
              if (self.TaskAssign[key].IsMainResponsiblePerson == 1) {
                self.Task.IsMainResponsiblePerson = key;
                self.TaskAssign[key].PersonAssign.push(2);
              }
              if (self.TaskAssign[key].IsResponsiblePerson == 1) {
                self.TaskAssign[key].PersonAssign.push(3);
              }
              if (self.TaskAssign[key].IsChecker == 1) {
                self.TaskAssign[key].PersonAssign.push(4);
              }
              if (self.TaskAssign[key].IsFollower == 1) {
                self.TaskAssign[key].PersonAssign.push(5);
              }
              if (self.TaskAssign[key].IsExecutor == 1) {
                self.TaskAssign[key].PersonAssign.push(6);
              }
            });
            self.TaskRequest = responsesData.data.TaskRequest;
            _.forEach(self.TaskRequest, function (item, key) {
              self.TaskRequest[key].RequestDate = __.convertDateTimeToString(item.RequestDate);
            });
            if (self.TaskRequest && self.TaskRequest.length) {
              self.showTaskRequest = true;
            }

            self.TaskPlanTrans = responsesData.data.TaskPlanTrans;
            _.forEach(self.TaskPlanTrans, function (item, key) {
              self.TaskPlanTrans[key].ActualHour = item.ActualHour;
              self.TaskPlanTrans[key].ActualQuantity = item.ActualQuantity;
            });
            self.TaskExecutionTrans = responsesData.data.TaskExecutionTrans;
            if (self.TaskExecutionTrans && self.TaskExecutionTrans.length) {
              self.showTaskExecution = true;
            }
            _.forEach(self.TaskExecutionTrans, function (item, key) {
              self.TaskExecutionTrans[key].TransDate = __.convertDateTimeToString(item.TransDate);
              self.TaskExecutionTrans[key].ActualHour = item.ActualHour;
              self.TaskExecutionTrans[key].ActualQuantity = item.ActualQuantity;
            });
            self.TaskCheckList = responsesData.data.TaskCheckList;
            _.forEach(self.TaskCheckList, function (item, key) {
              self.TaskCheckList[key].CompletedDate = __.convertDateTimeToString(item.CompletedDate);
            });
            // self.TaskValuation = responsesData.data.TaskValuation;
            self.TaskEvaluation1Job = responsesData.data.TaskEvaluation1Job;
            _.forEach(self.TaskEvaluation1Job, function (item, key) {
              if(item.EvaluatorID){
                self.TaskEvaluation1JobArr[item.EmployeeID + "_" + item.TransDate + "_" + item.IndicatorID + "_" + item.EvaluatorID] = item;
                self.TaskEvaluator1Job[item.EvaluatorID] = {
                  EvaluatorID: item.EvaluatorID,
                  EvaluatorName: item.EvaluatorName
                };
              }
            });
            self.ScaleRateItem = [];
            _.forEach(responsesData.data.ScaleRateItem, function (value, key) {
              if (self.ScaleRateItem[value.ScaleRateID] === undefined) {
                self.ScaleRateItem[value.ScaleRateID] = [];
              }
              let tmpObj = value;
              tmpObj.value = value.LevelInt;
              tmpObj.text = value.LevelChar;
              self.ScaleRateItem[value.ScaleRateID].push(tmpObj)
            });
            self.TaskExpenseTransDetail = responsesData.data.TaskExpenseTransItem;

            _.forEach(self.TaskExpenseTransDetail, function (item, key) {
              self.TaskExpenseTransDetail[key].TransDate = item.TransDate;
              self.TaskExpenseTransDetail[key].Quantity = item.Quantity;
              self.TaskExpenseTransDetail[key].UnitPrice = item.UnitPrice;
              self.TaskExpenseTransDetail[key].Amount = item.Amount;
              self.TaskExpenseTransDetail[key].TaxRate = item.TaxRate;
              self.TaskExpenseTransDetail[key].TaxAmount = item.TaxAmount;
            });
            _.forEach(responsesData.data.TaskExpenseTrans, function (item, key) {
              self.TaskExpenseTrans[item.TransID] = {};
              item.TransDate = __.convertDateToString(item.TransDate);
              self.TaskExpenseTrans[item.TransID].master = item;
            });
            let TransID = false;
            let arrObject = [];
            _.forEach(responsesData.data.TaskExpenseTransItem, function (item, key) {
              if (!TransID) {
                TransID = item.TransID;
              }
              if (TransID != item.TransID) {
                self.TaskExpenseTrans[TransID].detail = arrObject;
                arrObject = [];
                TransID = item.TransID;
              }
              self.TaskExpenseTransDetail[key].first = true;
              arrObject.push(item);
            });
            if (TransID) {
              self.TaskExpenseTrans[TransID].detail = arrObject;
            }
            self.TaskFile = responsesData.data.TaskFile;
            _.forEach(self.TaskFile, function (item, key) {
              self.TaskFile[key].DateModified = __.convertDateTimeToString(item.DateModified);
              self.TaskFile[key].changeFile = 0;
              self.TaskFile[key].changeData = 0;
            });
            self.TaskVideo = responsesData.data.TaskVideo;
            _.forEach(self.TaskVideo, function (item, key) {
              self.TaskVideo[key].DateModified = __.convertDateTimeToString(item.DateModified);
            });

            // indicator
            self.TaskIndicator = [];
            if (responsesData.data.Indicator) {
              let indicatorEmployee = responsesData.data.IndicatorEmployee;
              _.forEach(responsesData.data.Indicator, function (indicator, key) {
                let tmpObj = {
                  Indicator: null,
                  EmployeeIDs: [],
                  Employee: []
                };
                let tmpIndicator = {};
                tmpIndicator.TableItemID = indicator.TableItemID;
                tmpIndicator.IndicatorID = indicator.IndicatorID;
                tmpIndicator.IndicatorNo = indicator.IndicatorNo;
                tmpIndicator.IndicatorName = indicator.IndicatorName;
                tmpIndicator.IndicatorCalMethod = indicator.IndicatorCalMethod;
                tmpIndicator.ScaleRateID = indicator.ScaleRateID;
                tmpIndicator.ScaleRateName = indicator.ScaleRateName;
                tmpObj.Indicator = tmpIndicator;

                let employeeMap = _.filter(indicatorEmployee, ['MapID', indicator.MapID]);
                _.forEach(employeeMap, function (employee, key) {
                  tmpObj.EmployeeIDs.push(employee.EmployeeID);
                  tmpObj.Employee.push({
                    EmployeeID: employee.EmployeeID,
                    EmployeeName: employee.EmployeeName
                  });
                });

                self.TaskIndicator.push(tmpObj);
              });
            }
          } else if(responsesData.status === 2){
            self.onBackToList(responsesData.msg, 'warning');
          }
          self.scrollToTypeAction(self.$route.params.TypeAction);
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },

      onNavigationItem(type) {
        let currentIndex = this.reqParams.idsArray.indexOf(this.idParams);
        let newIndex = (type === 'prev') ? currentIndex - 1 : currentIndex + 1;

        if (newIndex === (this.reqParams.idsArray.length) && (this.reqParams.currentPage != this.reqParams.lastPage)) {
          this.reqParams.currentPage = this.reqParams.currentPage + 1;
          this.getItemIds(type);
        } else if (newIndex < 0 && this.reqParams.currentPage > 1) {
          this.reqParams.currentPage = this.reqParams.currentPage - 1;
          this.getItemIds(type);
        } else {
          this.idParams = (this.reqParams.idsArray[newIndex]) ? this.reqParams.idsArray[newIndex] : this.idParams;
        }
      },
      getItemIds(type) {
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

        if (this.reqParams.search.customerCodeName !== '') {
          requestData.data.CustomerCodeName = this.reqParams.search.customerCodeName;
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
              self.reqParams.idsArray.push(value.CustomerCodeID);
            });

            (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          self.$store.commit('isLoading', false);
          console.log(error);
        });

      },
      handleCopyItem() {
        this.$router.push({name: CreateRouter, params: {itemCopy: this.defaultModel}});
      },
      onEditClicked() {
        this.$router.push({
          name: EditRouter,
          params: {id: this.idParams, req: this.reqParams}
        });
      },
      onCreateClicked() {
        this.$router.push({name: CreateRouter});
      },
      onBackToList(message = '', variantMessage = 'success') {
        if (_.isString(message)) {
          this.$router.push({name: ListRouter, params: {message: message, variantMessage: variantMessage}});
        } else {
          this.$router.push({name: ListRouter});
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
              url: DeleteApi,
              data: {
                array_id: [self.idParams],
              },
            };

            ApiService.setHeader();
            this.$store.commit('isLoading', true);
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
              }
            }, (error) => {
              console.log(error);

            });

            this.$store.commit('isLoading', false);
          }
        });
      },
      updateModel() {
        if (this.stage.updatedData) {
          this.$forceUpdate();
        }
      },
      updateExpense(PTaskExpenseTrans, PTaskExpenseTransItem) {
        let self = this;
        self.TaskExpenseTransDetail = PTaskExpenseTransItem;

        _.forEach(PTaskExpenseTrans, function (item, key) {
          self.TaskExpenseTrans[item.TransID] = {};
          item.TransDate = __.convertDateToString(item.TransDate);
          self.TaskExpenseTrans[item.TransID].master = item;
        });
        let TransID = false;
        let arrObject = [];
        _.forEach(PTaskExpenseTransItem, function (item, key) {
          if (!TransID) {
            TransID = item.TransID;
            self.TaskExpenseTransDetail[key].first = true;
          }
          if (TransID != item.TransID) {
            self.TaskExpenseTrans[TransID].detail = arrObject;
            arrObject = [];
            TransID = item.TransID;
            self.TaskExpenseTransDetail[key].first = true;
          }
          arrObject.push(item);
        });
        if (TransID) {
          self.TaskExpenseTrans[TransID].detail = arrObject;
        }
      },
      changeFile(files) {
        let self = this;
        let dateC = __.convertDateTimeToString(new Date());
        for (var i = 0; i < files.length; i++) {
          self.$store.commit('isLoading', true);
          var file = files[i];
          let formData = new FormData();
          formData.append('LineID', '');
          formData.append('FileUpload', file);
          formData.append('TaskID', self.Task.TaskID);
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
          axios.post('task/api/task/task-upload-file/' + self.Task.TaskID, formData, config)
            .then(function (response) {
              let responseData = response.data;
              if (responseData.status === 1) {
                currentObj.success = response.data.success;
                currentObj.filename = "";
                let dataR = response.data.data;
                self.TaskFile.push({
                  LineID: dataR.LineID,
                  FileUpload: file,
                  TaskID: dataR.TaskID,
                  FileID: dataR.FileID,
                  FileName: dataR.FileName,
                  Description: dataR.FileName,
                  FileType: dataR.FileType,
                  FileSize: dataR.FileSize,
                  DateModified: dateC,
                  UserModified: dataR.UserModified,
                  Link: dataR.Link,
                  DateModifiedRoot: '',
                  FileNameRoot: '',
                  DocID: '',
                  DocNo: '',
                  DocName: '',
                  changeFile: 0,//Đã thay đổi file
                  changeData: 0
                });

                self.$bvToast.toast('Tải lên thành công', {
                  title: 'Thông báo',
                  variant: 'success',
                  solid: true
                });

                if ($('.component-dataflow').length) {
                  self.$_storeTaskDataflowNotice(self.Task.TaskID, 'updateFile');
                } else {
                  self.$_storeTaskNotice(self.Task.TaskID, 'updateFile');
                }

              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Thông báo',
                  text: responseData.msg,
                  confirmButtonText: 'Đóng'
                });
              }

              self.showTaskFile = true;
              self.$store.commit('isLoading', false);
            })
            .catch(function (error) {
              console.log(error);
              self.$store.commit('isLoading', false);
              self.$bvToast.toast('Tải lên thất bại', {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
            });
        }

      },
      downloadAllFile(){
        let self = this;
        let requestData = {
          url: 'task/api/task/download-all-file/' + this.Task.TaskID,
          method: 'get',
          data: {}
        };

        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          self.$store.commit('isLoading', false);
          let responsesData = responses.data;

          if (responsesData.status === 1) {
            let link = document.createElement('a');
            link.href = self.$store.state.appRootApi + responsesData.data;
            link.download = 'Archive.zip';
            link.click();
          } else {
            self.$bvToast.toast(responsesData.msg, {
              title: 'Thông báo',
              variant: 'warning',
              solid: true
            });
          }
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      changeVideo(videos) {
        let self = this;
        let dateC = __.convertDateTimeToString(new Date());
        for (var i = 0; i < videos.length; i++) {
          self.$store.commit('isLoading', true);
          var video = videos[i];
          let formData = new FormData();
          formData.append('LineID', '');
          formData.append('VideoUpload', video);
          formData.append('TaskID', self.Task.TaskID);
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
          axios.post('task/api/task/task-upload-video/' + self.Task.TaskID, formData, config)
            .then(function (response) {
              let responseData = response.data;
              if (responseData.status === 1) {
                currentObj.success = response.data.success;
                currentObj.videoname = "";
                let dataR = response.data.data;
                self.TaskVideo.push({
                  LineID: dataR.LineID,
                  VideoUpload: video,
                  TaskID: dataR.TaskID,
                  FileID: dataR.FileID,
                  VideoName: dataR.VideoName,
                  Description: dataR.VideoName,
                  VideoType: dataR.VideoType,
                  VideoSize: dataR.VideoSize,
                  DateModified: dateC,
                  UserModified: dataR.UserModified,
                  Link: dataR.Link,
                  DateModifiedRoot: '',
                  FileNameRoot: '',
                  DocID: '',
                  DocNo: '',
                  DocName: '',
                  changeVideo: 0,//Đã thay đổi file
                  changeData: 0
                });

                self.$bvToast.toast('Tải lên thành công', {
                  title: 'Thông báo',
                  variant: 'success',
                  solid: true
                });

                if ($('.component-dataflow').length) {
                  self.$_storeTaskDataflowNotice(self.Task.TaskID, 'updateVideo');
                } else {
                  self.$_storeTaskNotice(self.Task.TaskID, 'updateVideo');
                }

              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Thông báo',
                  text: responseData.msg,
                  confirmButtonText: 'Đóng'
                });
              }
              self.showTaskVideo = true;
              self.$store.commit('isLoading', false);
            })
            .catch(function (error) {
              console.log(error);
              self.$store.commit('isLoading', false);
              self.$bvToast.toast('Tải lên thất bại', {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
            });
        }

      },
      scrollToTypeAction(TypeAction){
        let self = this;
        if (TypeAction) {
          this.$nextTick(() => {
            let id = '';
            switch (TypeAction) {
              case 1:
                id = 'task-detail-comment';
                self.showTaskComment = true;
                break;
              case 2:
                id = 'task-detail-general-info';
                break;
              case 3:
                id = 'task-detail-general-info';
                break;
              case 4:
                id = 'task-detail-assign';
                break;
              case 5:
                id = 'task-detail-execution';
                break;
              case 6:
                id = 'task-detail-check-list';
                break;
              case 7:
                id = 'task-detail-expense';
                break;
              case 8:
                id = 'task-detail-assign';
                break;
              case 9:
                id = 'task-detail-file';
                break;
              case 10:
                id = 'task-detail-video';
                break;
              case 11:
                id = 'task-detail-general-info';
                break;
              case 12:
                id = 'task-detail-indicator';
                break;
              case 13:
                id = 'task-detail-evaluation';
                break;
              default:
                id = 'task-detail-general-info';
                break;

            }
            if ($('#' + id).length) {
              $('.main-body')[0].scrollTop = $('#' + id)[0].offsetTop;
            }
          });
        }
      }
    },
    watch: {
      idParams() {
        this.fetchData();
      },

      '$route' (to, from) {
        this.idParams = this.$route.params.id;
      },

      '$store.state.notification.reload'(){
        this.fetchData();
        // this.scrollToTypeAction(this.$store.state.notification.TypeAction);
      },

      'Task.Priority'() {
        this.Task.PriorityName = this.Task.PriorityOptions[this.Task.Priority];
        //this.Task.PriorityName = _.find(this.Task.PriorityOptions, ['value', this.Task.Priority]).text;
      },
      'Task.AccessType'() {
        this.Task.AccessTypeName = this.Task.AccessTypeOptions[this.Task.AccessType];
        //this.Task.PriorityName = _.find(this.Task.PriorityOptions, ['value', this.Task.Priority]).text;
      },
      'Task.UomID'() {
        if (this.Task.UomID) {
          let uom = _.find(this.Task.UomOptions, ['value', this.Task.UomID]);
          if (uom) {
            this.Task.UomName = uom.text;
          }
        }
      },
      'Task.CompanyID'() {
        if (this.Task.CompanyID) {
          let company = _.find(this.Task.CompanyOptions, ['value', this.Task.CompanyID]);
          if (company) {
            this.Task.CompanyName = company.text;
          }
        }
      },
      'Task.CalendarTypeID'() {
        if (this.Task.CalendarTypeID) {
          let calendar = _.find(this.Task.CalendarOptions, ['value', this.Task.CalendarTypeID]);
          if (calendar) {
            this.Task.CalendarName = calendar.text;
          }
        }
      },
      showTaskComment() {
        if (this.showTaskComment) {
          if (!this.initTaskComment) {
            this.initTaskComment = true;
          }
        }
      }
    },
    destroyed() {
      this.$store.commit('notification', {CategoryID: null});
    },
    filters: {
      convertDateToString: function (value) {
        return __.convertDateToString(value);
      }
    }
  }
</script>


<style lang="css">
  .ij-icon {
    font-size: 18px;
    padding-left: 10px;
  }

  .ij-a-icon:hover {
    cursor: pointer;
  }

  .ij-content-view {
    margin-bottom: 10px;
  }

  .ij-content-view .form-group {
    margin-bottom: 3px;
  }

  .ij-line-head {
    border-bottom: 1px solid #e6e2e2;
    margin-left: 0;
    margin-right: 0;
  }

  .ij-line-head label {
    padding-left: 0 !important;
    padding-right: 0 !important;
    font-size: 16px;
    cursor: pointer;
  }

  .not-border th, .not-border td {
    border: none;
  }

  .sss {
    max-width: none !important;
    white-space: nowrap;
    display: table-caption;
  }

  .right-absolute {
    position: absolute;
    right: 35px;
  }

  .float-right {
    padding-right: 0;
  }
  .evaluation-content .datepicker-fill-content{
    width: 120px !important;
  }
  .datepicker-fill-content .mx-input-wrapper{
    width: 120px !important;
  }
</style>

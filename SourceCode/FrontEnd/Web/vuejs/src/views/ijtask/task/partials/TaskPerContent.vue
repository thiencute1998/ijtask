<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Phân quyền">
    <i class="fa fa-shield ij-icon" aria-hidden="true"></i>
    <b-modal ref="modal" id="modal-form-input-assign" scrollable size="md">
      <template slot="modal-title">
        <i class="icon-eye icon" v-if="!isForm"></i><i class="fa fa-edit" v-if="isForm"></i> Phân quyền
      </template>
      <div class="table-responsive">
        <table class="not-border">
          <thead>
          <tr class="text-left">
            <th class="pr-3">Chức năng</th>
            <th class="pr-3">Xem</th>
            <th class="pr-3">Trường xem</th>
            <th class="pr-3">Sửa</th>
            <th class="pr-3">Trường sửa</th>
            <th class="pr-3">Xóa</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td class="pr-3">Thông tin chung</td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm" v-model="AccessGen" @change="changeAccessAll(AccessGen, 'Gen')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem thông tin chung'" :disable="!AccessGen" v-model="FieldGenAccess" @updateFromChild="updateParent" :field-model="'FieldGenAccess'" :table="'task'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa thông tin chung'" :disable="!AccessGen" v-model="FieldGenAccess" :table="'task'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessGen" v-model="EditGen" @change="changeEditAll(EditGen,'FieldGenEdit')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem thông tin chung'" :disable="!EditGen" v-model="FieldGenEdit" @updateFromChild="updateParent" :field-model="'FieldGenEdit'" :table="'task'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa thông tin chung'" :disable="!EditGen" v-model="FieldGenEdit" :table="'task'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessGen" v-model="DeleteGen">
              </b-form-checkbox>
            </td>
          </tr>

          <tr>
            <td class="pr-3">Loại công việc</td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm" v-model="AccessCate" @change="changeAccessAll(AccessCate, 'Cate')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem loai công việc'" :disable="!AccessCate" v-model="FieldCateAccess" @updateFromChild="updateParent" :field-model="'FieldCateAccess'" :table="'task_cate'" :table-per="'task_per_cate'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa loai công việc'" :disable="!AccessCate" v-model="FieldCateAccess" :table="'task_cate'" :table-per="'task_per_cate'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessCate" v-model="EditCate" @change="changeEditAll(EditCate,'FieldCateEdit')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem loai công việc'" :disable="!EditCate" v-model="FieldCateEdit" @updateFromChild="updateParent" :field-model="'FieldCateEdit'" :table="'task_cate'" :table-per="'task_per_cate'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa loai công việc'" :disable="!EditCate" v-model="FieldCateEdit" :table="'task_cate'" :table-per="'task_per_cate'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessCate" v-model="DeleteCate">
              </b-form-checkbox>
            </td>
          </tr>

          <tr>
            <td class="pr-3">Giao việc</td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm" v-model="AccessAssign" @change="changeAccessAll(AccessAssign, 'Assign')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem giao việc'" :disable="!AccessAssign" v-model="FieldAssignAccess" @updateFromChild="updateParent" :field-model="'FieldAssignAccess'" :table="'task_assign'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa giao việc'" :disable="!AccessAssign" v-model="FieldAssignAccess" :table="'task_assign'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessAssign" v-model="EditAssign" @change="changeEditAll(EditAssign,'FieldAssignEdit')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem giao việc'" :disable="!EditAssign" v-model="FieldAssignEdit" @updateFromChild="updateParent" :field-model="'FieldAssignEdit'" :table="'task_assign'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa giao việc'" :disable="!EditAssign" v-model="FieldAssignEdit" :table="'task_assign'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessAssign" v-model="DeleteAssign">
              </b-form-checkbox>
            </td>
          </tr>


          <tr>
            <td class="pr-3">Danh mục liên kết</td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm" v-model="AccessLink" @change="changeAccessAll(AccessLink, 'Link')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem danh mục liên kết'" :disable="!AccessLink" v-model="FieldLinkAccess" @updateFromChild="updateParent" :field-model="'FieldLinkAccess'" :table="'task_link'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa danh mục liên kết'" :disable="!AccessLink" v-model="FieldLinkAccess" :table="'task_link'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessLink" v-model="EditLink" @change="changeEditAll(EditLink,'FieldLinkEdit')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem danh mục liên kết'" :disable="!EditLink" v-model="FieldLinkEdit" @updateFromChild="updateParent" :field-model="'FieldLinkEdit'" :table="'task_link'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa danh mục liên kết'" :disable="!EditLink" v-model="FieldLinkEdit" :table="'task_link'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessLink" v-model="DeleteLink">
              </b-form-checkbox>
            </td>
          </tr>


          <tr>
            <td class="pr-3">Thực hiện</td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm" v-model="AccessExecution" @change="changeAccessAll(AccessExecution, 'Execution')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem thực hiện'" :disable="!AccessExecution" v-model="FieldExecutionAccess" @updateFromChild="updateParent" :field-model="'FieldExecutionAccess'" :table="'task_execution_trans'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa thực hiện'" :disable="!AccessExecution" v-model="FieldExecutionAccess" :table="'task_execution_trans'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessExecution" v-model="EditExecution" @change="changeEditAll(EditExecution,'FieldExecutionEdit')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem thực hiện'" :disable="!EditExecution" v-model="FieldExecutionEdit" @updateFromChild="updateParent" :field-model="'FieldExecutionEdit'" :table="'task_execution_trans'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa thực hiện'" :disable="!EditExecution" v-model="FieldExecutionEdit" :table="'task_execution_trans'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessExecution" v-model="DeleteExecution">
              </b-form-checkbox>
            </td>
          </tr>


          <tr>
            <td class="pr-3">Kiểm tra</td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm" v-model="AccessChecklist" @change="changeAccessAll(AccessChecklist, 'Checklist')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem kiểm tra'" :disable="!AccessChecklist" v-model="FieldChecklistAccess" @updateFromChild="updateParent" :field-model="'FieldChecklistAccess'" :table="'task_checklist_trans'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa kiểm tra'" :disable="!AccessChecklist" v-model="FieldChecklistAccess" :table="'task_checklist_trans'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessChecklist" v-model="EditChecklist" @change="changeEditAll(EditChecklist,'FieldChecklistEdit')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem kiểm tra'" :disable="!EditChecklist" v-model="FieldChecklistEdit" @updateFromChild="updateParent" :field-model="'FieldChecklistEdit'" :table="'task_checklist_trans'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa kiểm tra'" :disable="!EditChecklist" v-model="FieldChecklistEdit" :table="'task_checklist_trans'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessChecklist" v-model="DeleteChecklist">
              </b-form-checkbox>
            </td>
          </tr>

          <tr>
            <td class="pr-3">Đánh giá</td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm" v-model="AccessValuation" @change="changeAccessAll(AccessValuation, 'Valuation')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem đánh giá'" :disable="!AccessValuation" v-model="FieldValuationAccess" @updateFromChild="updateParent" :field-model="'FieldValuationAccess'" :table="'task_evaluation_1job_trans'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa đánh giá'" :disable="!AccessValuation" v-model="FieldValuationAccess" :table="'task_evaluation_1job_trans'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessValuation" v-model="EditValuation" @change="changeEditAll(EditValuation,'FieldValuationEdit')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem đánh giá'" :disable="!EditValuation" v-model="FieldValuationEdit" @updateFromChild="updateParent" :field-model="'FieldValuationEdit'" :table="'task_evaluation_1job_trans'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa đánh giá'" :disable="!EditValuation" v-model="FieldValuationEdit" :table="'task_evaluation_1job_trans'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessValuation" v-model="DeleteValuation">
              </b-form-checkbox>
            </td>
          </tr>
          <tr>
            <td class="pr-3">Chi phí</td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm" v-model="AccessExpense" @change="changeAccessAll(AccessExpense, 'Expense')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem chi phí'" :disable="!AccessExpense" v-model="FieldExpenseAccess" @updateFromChild="updateParent" :field-model="'FieldExpenseAccess'" :table="'task_expense_trans'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa chi phí'" :disable="!AccessExpense" v-model="FieldExpenseAccess" :table="'task_expense_trans'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessExpense" v-model="EditExpense" @change="changeEditAll(EditExpense,'FieldExpenseEdit')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem chi phí'" :disable="!EditExpense" v-model="FieldExpenseEdit" @updateFromChild="updateParent" :field-model="'FieldExpenseEdit'" :table="'task_expense_trans'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa chi phí'" :disable="!EditExpense" v-model="FieldExpenseEdit" :table="'task_expense_trans'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessExpense" v-model="DeleteExpense">
              </b-form-checkbox>
            </td>
          </tr>

          <tr>
            <td class="pr-3">Chi phí chi tiết</td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm" v-model="AccessExpenseDetail" @change="changeAccessAll(AccessExpenseDetail, 'ExpenseDetail')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem chi phí chi tiết'" :disable="!AccessExpenseDetail" v-model="FieldExpenseDetailAccess" @updateFromChild="updateParent" :field-model="'FieldExpenseDetailAccess'" :table="'task_expense_trans_item'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa chi phí chi tiết'" :disable="!AccessExpenseDetail" v-model="FieldExpenseDetailAccess" :table="'task_expense_trans_item'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessExpenseDetail" v-model="EditExpenseDetail" @change="changeEditAll(EditExpenseDetail,'FieldExpenseDetailEdit')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem chi phí chi tiết'" :disable="!EditExpenseDetail" v-model="FieldExpenseDetailEdit" @updateFromChild="updateParent" :field-model="'FieldExpenseDetailEdit'" :table="'task_expense_trans_item'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa chi phí chi tiết'" :disable="!EditExpenseDetail" v-model="FieldExpenseDetailEdit" :table="'task_expense_trans_item'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessExpenseDetail" v-model="DeleteExpenseDetail">
              </b-form-checkbox>
            </td>
          </tr>



          <tr>
            <td class="pr-3">Yêu cầu</td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm" v-model="AccessRequest" @change="changeAccessAll(AccessRequest, 'Request')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem yêu cầu'" :disable="!AccessRequest" v-model="FieldRequestAccess" @updateFromChild="updateParent" :field-model="'FieldRequestAccess'" :table="'task_request'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa yêu cầu'" :disable="!AccessRequest" v-model="FieldRequestAccess" :table="'task_request'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessRequest" v-model="EditRequest" @change="changeEditAll(EditRequest,'FieldRequestEdit')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem yêu cầu'" :disable="!EditRequest" v-model="FieldRequestEdit" @updateFromChild="updateParent" :field-model="'FieldRequestEdit'" :table="'task_request'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa yêu cầu'" :disable="!EditRequest" v-model="FieldRequestEdit" :table="'task_request'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessRequest" v-model="DeleteRequest">
              </b-form-checkbox>
            </td>
          </tr>


          <tr>
            <td class="pr-3">Tệp</td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm" v-model="AccessFile" @change="changeAccessAll(AccessFile, 'File')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem tệp'" :disable="!AccessFile" v-model="FieldFileAccess" @updateFromChild="updateParent" :field-model="'FieldFileAccess'" :table="'task_file'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa tệp'" :disable="!AccessFile" v-model="FieldFileAccess" :table="'task_file'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessFile" v-model="EditFile" @change="changeEditAll(EditFile,'FieldFileEdit')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem tệp'" :disable="!EditFile" v-model="FieldFileEdit" @updateFromChild="updateParent" :field-model="'FieldFileEdit'" :table="'task_file'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa tệp'" :disable="!EditFile" v-model="FieldFileEdit" :table="'task_file'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessFile" v-model="DeleteFile">
              </b-form-checkbox>
            </td>
          </tr>


          <tr>
            <td class="pr-3">Phim</td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm" v-model="AccessVideo" @change="changeAccessAll(AccessVideo, 'Video')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem phim'" :disable="!AccessVideo" v-model="FieldVideoAccess" @updateFromChild="updateParent" :field-model="'FieldVideoAccess'" :table="'task_video'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa phim'" :disable="!AccessVideo" v-model="FieldVideoAccess" :table="'task_video'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessVideo" v-model="EditVideo" @change="changeEditAll(EditVideo,'FieldVideoEdit')">
              </b-form-checkbox>
            </td>
            <td class="pr-3">
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền xem phim'" :disable="!EditVideo" v-model="FieldVideoEdit" @updateFromChild="updateParent" :field-model="'FieldVideoEdit'" :table="'task_video'" :table-per="'task_per'" v-if="isForm" :isForm="true"></TaskPerModuleDetail>
              <TaskPerModuleDetail :Task="Task" :title="'Phân quyền sửa phim'" :disable="!EditVideo" v-model="FieldVideoEdit" :table="'task_video'" :table-per="'task_per'" v-if="!isForm" :isForm="false"></TaskPerModuleDetail>
            </td>
            <td class="pr-3 text-center">
              <b-form-checkbox :disabled="!isForm||!AccessVideo" v-model="DeleteVideo">
              </b-form-checkbox>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()" v-if="!isForm"
          >
            Sửa
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdate()" v-if="isForm">
            Lưu
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="" v-if="isForm">
            Hủy
          </b-button>
          <b-button
            variant="primary"
            size="md"
            class="float-left mr-2"
            @click="onHideModal()"
          >
            Đóng
          </b-button>
        </div>
      </template>

    </b-modal>
  </a>
</template>

<script>
  import ApiService from '@/services/api.service';
  import TaskPerModuleDetail from "./TaskPerModuleDetail";

  export default {
    name: 'TaskPerContent',
    components: {TaskPerModuleDetail},
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {
        isForm: false,
        LineIDTaskPer: '',
        AccessGen: false,
        EditGen: false,
        DeleteGen: false,
        FieldGenEdit: '',
        FieldGenAccess: '',

        LineIDTaskPerCate: '',
        AccessCate: false,
        EditCate: false,
        DeleteCate: false,
        FieldCateEdit: '',
        FieldCateAccess: '',

        LineIDTaskPerAssign: '',
        AccessAssign: false,
        EditAssign: false,
        DeleteAssign: false,
        FieldAssignEdit: '',
        FieldAssignAccess: '',

        LineIDTaskPerLink: '',
        AccessLink: false,
        EditLink: false,
        DeleteLink: false,
        FieldLinkEdit: '',
        FieldLinkAccess: '',

        LineIDTaskPerExecution: '',
        AccessExecution: false,
        EditExecution: false,
        DeleteExecution: false,
        FieldExecutionEdit: '',
        FieldExecutionAccess: '',

        LineIDTaskPerChecklist: '',
        AccessChecklist: false,
        EditChecklist: false,
        DeleteChecklist: false,
        FieldChecklistEdit: '',
        FieldChecklistAccess: '',

        LineIDTaskPerValuation: '',
        AccessValuation: false,
        EditValuation: false,
        DeleteValuation: false,
        FieldValuationEdit: '',
        FieldValuationAccess: '',

        LineIDTaskPerExpense: '',
        AccessExpense: false,
        EditExpense: false,
        DeleteExpense: false,
        FieldExpenseEdit: '',
        FieldExpenseAccess: '',

        LineIDTaskPerExpenseDetail: '',
        AccessExpenseDetail: false,
        EditExpenseDetail: false,
        DeleteExpenseDetail: false,
        FieldExpenseDetailEdit: '',
        FieldExpenseDetailAccess: '',

        LineIDTaskPerRequest: '',
        AccessRequest: false,
        EditRequest: false,
        DeleteRequest: false,
        FieldRequestEdit: '',
        FieldRequestAccess: '',

        LineIDTaskPerFile: '',
        AccessFile: false,
        EditFile: false,
        DeleteFile: false,
        FieldFileEdit: '',
        FieldFileAccess: '',

        LineIDTaskPerVideo: '',
        AccessVideo: false,
        EditVideo: false,
        DeleteVideo: false,
        FieldVideoEdit: '',
        FieldVideoAccess: '',
      }
    },
    created() {
    },
    mounted() {
    },
    methods: {
      changeAccessAll(checked, DataUpdate){
        if(!checked){
          this['Field'+DataUpdate+'Access'] = 'all';
        }else{
          this['Field'+DataUpdate+'Access'] = '';
          this['Field'+DataUpdate+'Edit'] = '';
          this['Delete'+DataUpdate] = false;
          this['Edit'+DataUpdate] = false;
        }
      },
      changeEditAll(checked, DataUpdate){
        if(!checked){
          this[DataUpdate] = 'all';
        }else{
          this[DataUpdate] = '';
        }
      },
      updateParent(data, FieldData){
        this[FieldData] = data;
      },
      fetchData() {
        let self = this;
        let urlApi = '/task/api/task/task-per-content';
        let requestData = {
          method: 'post',
          url: urlApi,
          data: {
            EmployeeID: self.EmployeeID,
            TaskID: self.Task.TaskID
          },
        };

        // if (this.modelSearch.CompanyNo.trim() !== '') {
        //     requestData.data.CompanyNo = this.modelSearch.CompanyNo;
        // }

        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let dataResponse = response.data;
          if (dataResponse.status === 1) {
            if(dataResponse.data.TaskPer){
              let dataResult = dataResponse.data;
              self.LineIDTaskPer = dataResult.TaskPer['LineID'];
              self.FieldGenAccess = dataResult.TaskPer['AccessField'];
              self.FieldGenEdit = dataResult.TaskPer['EditField'];
              if(dataResult.TaskPer['Access'] == 1){
                self.AccessGen = true;
              }else{
                self.AccessGen = false;
              }
              if(dataResult.TaskPer['Edit'] == 1){
                self.EditGen = true;
              }else{
                self.EditGen = false;
              }
              if(dataResult.TaskPer['Delete'] == 1){
                self.DeleteGen = true;
              }else{
                self.DeleteGen = false;
              }
            }else{
              self.FieldGenAccess = '';
              self.FieldGenEdit = '';
              self.AccessGen = false;
              self.EditGen = false;
              self.DeleteGen = false;
            }

            if(dataResponse.data.TaskPerCate){
              let dataResult = dataResponse.data;
              self.LineIDTaskPerCate = dataResult.TaskPerCate['LineID'];
              self.FieldCateAccess = dataResult.TaskPerCate['AccessField'];
              self.FieldCateEdit = dataResult.TaskPerCate['EditField'];
              if(dataResult.TaskPerCate['Access'] == 1){
                self.AccessCate = true;
              }else{
                self.AccessCate = false;
              }
              if(dataResult.TaskPerCate['Edit'] == 1){
                self.EditCate = true;
              }else{
                self.EditCate = false;
              }
              if(dataResult.TaskPerCate['Delete'] == 1){
                self.DeleteCate = true;
              }else{
                self.DeleteCate = false;
              }
            }else{
              self.FieldCateAccess = '';
              self.FieldCateEdit = '';
              self.AccessCate = false;
              self.EditCate = false;
              self.DeleteCate = false;
            }


            if(dataResponse.data.TaskPerAssign){
              let dataResult = dataResponse.data;
              self.LineIDTaskPerAssign = dataResult.TaskPerAssign['LineID'];
              self.FieldAssignAccess = dataResult.TaskPerAssign['AccessField'];
              self.FieldAssignEdit = dataResult.TaskPerAssign['EditField'];
              if(dataResult.TaskPerAssign['Access'] == 1){
                self.AccessAssign = true;
              }else{
                self.AccessAssign = false;
              }
              if(dataResult.TaskPerAssign['Edit'] == 1){
                self.EditAssign = true;
              }else{
                self.EditAssign = false;
              }
              if(dataResult.TaskPerAssign['Delete'] == 1){
                self.DeleteAssign = true;
              }else{
                self.DeleteAssign = false;
              }
            }else{
              self.FieldAssignAccess = '';
              self.FieldAssignEdit = '';
              self.AccessAssign = false;
              self.EditAssign = false;
              self.DeleteAssign = false;
            }

            if(dataResponse.data.TaskPerLink){
              let dataResult = dataResponse.data;
              self.LineIDTaskPerLink = dataResult.TaskPerLink['LineID'];
              self.FieldLinkAccess = dataResult.TaskPerLink['AccessField'];
              self.FieldLinkEdit = dataResult.TaskPerLink['EditField'];
              if(dataResult.TaskPerLink['Access'] == 1){
                self.AccessLink = true;
              }else{
                self.AccessLink = false;
              }
              if(dataResult.TaskPerLink['Edit'] == 1){
                self.EditLink = true;
              }else{
                self.EditLink = false;
              }
              if(dataResult.TaskPerLink['Delete'] == 1){
                self.DeleteLink = true;
              }else{
                self.DeleteLink = false;
              }
            }else{
              self.FieldLinkAccess = '';
              self.FieldLinkEdit = '';
              self.AccessLink = false;
              self.EditLink = false;
              self.DeleteLink = false;
            }



            if(dataResponse.data.TaskPerExecution){
              let dataResult = dataResponse.data;
              self.LineIDTaskPerExecution = dataResult.TaskPerExecution['LineID'];
              self.FieldExecutionAccess = dataResult.TaskPerExecution['AccessField'];
              self.FieldExecutionEdit = dataResult.TaskPerExecution['EditField'];
              if(dataResult.TaskPerExecution['Access'] == 1){
                self.AccessExecution = true;
              }else{
                self.AccessExecution = false;
              }
              if(dataResult.TaskPerExecution['Edit'] == 1){
                self.EditExecution = true;
              }else{
                self.EditExecution = false;
              }
              if(dataResult.TaskPerExecution['Delete'] == 1){
                self.DeleteExecution = true;
              }else{
                self.DeleteExecution = false;
              }
            }else{
              self.FieldExecutionAccess = '';
              self.FieldExecutionEdit = '';
              self.AccessExecution = false;
              self.EditExecution = false;
              self.DeleteExecution = false;
            }



            if(dataResponse.data.TaskPerChecklist){
              let dataResult = dataResponse.data;
              self.LineIDTaskPerChecklist = dataResult.TaskPerChecklist['LineID'];
              self.FieldChecklistAccess = dataResult.TaskPerChecklist['AccessField'];
              self.FieldChecklistEdit = dataResult.TaskPerChecklist['EditField'];
              if(dataResult.TaskPerChecklist['Access'] == 1){
                self.AccessChecklist = true;
              }else{
                self.AccessChecklist = false;
              }
              if(dataResult.TaskPerChecklist['Edit'] == 1){
                self.EditChecklist = true;
              }else{
                self.EditChecklist = false;
              }
              if(dataResult.TaskPerChecklist['Delete'] == 1){
                self.DeleteChecklist = true;
              }else{
                self.DeleteChecklist = false;
              }
            }else{
              self.FieldChecklistAccess = '';
              self.FieldChecklistEdit = '';
              self.AccessChecklist = false;
              self.EditChecklist = false;
              self.DeleteChecklist = false;
            }



            if(dataResponse.data.TaskPerValuation){
              let dataResult = dataResponse.data;
              self.LineIDTaskPerValuation = dataResult.TaskPerValuation['LineID'];
              self.FieldValuationAccess = dataResult.TaskPerValuation['AccessField'];
              self.FieldValuationEdit = dataResult.TaskPerValuation['EditField'];
              if(dataResult.TaskPerValuation['Access'] == 1){
                self.AccessValuation = true;
              }else{
                self.AccessValuation = false;
              }
              if(dataResult.TaskPerValuation['Edit'] == 1){
                self.EditValuation = true;
              }else{
                self.EditValuation = false;
              }
              if(dataResult.TaskPerValuation['Delete'] == 1){
                self.DeleteValuation = true;
              }else{
                self.DeleteValuation = false;
              }
            }else{
              self.FieldValuationAccess = '';
              self.FieldValuationEdit = '';
              self.AccessValuation = false;
              self.EditValuation = false;
              self.DeleteValuation = false;
            }



            if(dataResponse.data.TaskPerExpense){
              let dataResult = dataResponse.data;
              self.LineIDTaskPerExpense = dataResult.TaskPerExpense['LineID'];
              self.FieldExpenseAccess = dataResult.TaskPerExpense['AccessField'];
              self.FieldExpenseEdit = dataResult.TaskPerExpense['EditField'];
              if(dataResult.TaskPerExpense['Access'] == 1){
                self.AccessExpense = true;
              }else{
                self.AccessExpense = false;
              }
              if(dataResult.TaskPerExpense['Edit'] == 1){
                self.EditExpense = true;
              }else{
                self.EditExpense = false;
              }
              if(dataResult.TaskPerExpense['Delete'] == 1){
                self.DeleteExpense = true;
              }else{
                self.DeleteExpense = false;
              }
            }else{
              self.FieldExpenseAccess = '';
              self.FieldExpenseEdit = '';
              self.AccessExpense = false;
              self.EditExpense = false;
              self.DeleteExpense = false;
            }



            if(dataResponse.data.TaskPerExpenseDetail){
              let dataResult = dataResponse.data;
              self.LineIDTaskPerExpenseDetail = dataResult.TaskPerExpenseDetail['LineID'];
              self.FieldExpenseDetailAccess = dataResult.TaskPerExpenseDetail['AccessField'];
              self.FieldExpenseDetailEdit = dataResult.TaskPerExpenseDetail['EditField'];
              if(dataResult.TaskPerExpenseDetail['Access'] == 1){
                self.AccessExpenseDetail = true;
              }else{
                self.AccessExpenseDetail = false;
              }
              if(dataResult.TaskPerExpenseDetail['Edit'] == 1){
                self.EditExpenseDetail = true;
              }else{
                self.EditExpenseDetail = false;
              }
              if(dataResult.TaskPerExpenseDetail['Delete'] == 1){
                self.DeleteExpenseDetail = true;
              }else{
                self.DeleteExpenseDetail = false;
              }
            }else{
              self.FieldExpenseDetailAccess = '';
              self.FieldExpenseDetailEdit = '';
              self.AccessExpenseDetail = false;
              self.EditExpenseDetail = false;
              self.DeleteExpenseDetail = false;
            }



            if(dataResponse.data.TaskPerRequest){
              let dataResult = dataResponse.data;
              self.LineIDTaskPerRequest = dataResult.TaskPerRequest['LineID'];
              self.FieldRequestAccess = dataResult.TaskPerRequest['AccessField'];
              self.FieldRequestEdit = dataResult.TaskPerRequest['EditField'];
              if(dataResult.TaskPerRequest['Access'] == 1){
                self.AccessRequest = true;
              }else{
                self.AccessRequest = false;
              }
              if(dataResult.TaskPerRequest['Edit'] == 1){
                self.EditRequest = true;
              }else{
                self.EditRequest = false;
              }
              if(dataResult.TaskPerRequest['Delete'] == 1){
                self.DeleteRequest = true;
              }else{
                self.DeleteRequest = false;
              }
            }else{
              self.FieldRequestAccess = '';
              self.FieldRequestEdit = '';
              self.AccessRequest = false;
              self.EditRequest = false;
              self.DeleteRequest = false;
            }



            if(dataResponse.data.TaskPerFile){
              let dataResult = dataResponse.data;
              self.LineIDTaskPerFile = dataResult.TaskPerFile['LineID'];
              self.FieldFileAccess = dataResult.TaskPerFile['AccessField'];
              self.FieldFileEdit = dataResult.TaskPerFile['EditField'];
              if(dataResult.TaskPerFile['Access'] == 1){
                self.AccessFile = true;
              }else{
                self.AccessFile = false;
              }
              if(dataResult.TaskPerFile['Edit'] == 1){
                self.EditFile = true;
              }else{
                self.EditFile = false;
              }
              if(dataResult.TaskPerFile['Delete'] == 1){
                self.DeleteFile = true;
              }else{
                self.DeleteFile = false;
              }
            }else{
              self.FieldFileAccess = '';
              self.FieldFileEdit = '';
              self.AccessFile = false;
              self.EditFile = false;
              self.DeleteFile = false;
            }



            if(dataResponse.data.TaskPerVideo){
              let dataResult = dataResponse.data;
              self.LineIDTaskPerVideo = dataResult.TaskPerVideo['LineID'];
              self.FieldVideoAccess = dataResult.TaskPerVideo['AccessField'];
              self.FieldVideoEdit = dataResult.TaskPerVideo['EditField'];
              if(dataResult.TaskPerVideo['Access'] == 1){
                self.AccessVideo = true;
              }else{
                self.AccessVideo = false;
              }
              if(dataResult.TaskPerVideo['Edit'] == 1){
                self.EditVideo = true;
              }else{
                self.EditVideo = false;
              }
              if(dataResult.TaskPerVideo['Delete'] == 1){
                self.DeleteVideo = true;
              }else{
                self.DeleteVideo = false;
              }
            }else{
              self.FieldVideoAccess = '';
              self.FieldVideoEdit = '';
              self.AccessVideo = false;
              self.EditVideo = false;
              self.DeleteVideo = false;
            }
            self.isForm = false;
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
      onHideModal() {
        this.$refs['modal'].hide();
        this.isForm = false;
      },
      onToggleModal() {
        let self = this;
        self.FieldGenEdit = '';
        self.FieldGenAccess = '';
        this.currentPage = 1;
        this.$refs['modal'].show();
        this.fetchData();
      },
      onResetModal() {
      },
      onEdit() {
        this.isForm = true;
      },
      onUpdate() {
        this.$store.commit('isLoading', true);
        let self = this;
        const requestData = {
          method: 'post',
          url: 'task/api/task/task-per-update',
          data: {
            EmployeeID: self.EmployeeID,
            TaskID: self.Task.TaskID,
            LineIDTaskPer: self.LineIDTaskPer,
            AccessGen: self.AccessGen,
            EditGen: self.EditGen,
            DeleteGen: self.DeleteGen,
            FieldGenEdit: self.FieldGenEdit,
            FieldGenAccess: self.FieldGenAccess,

            LineIDTaskPerCate: self.LineIDTaskPerCate,
            AccessCate: self.AccessCate,
            EditCate: self.EditCate,
            DeleteCate: self.DeleteCate,
            FieldCateEdit: self.FieldCateEdit,
            FieldCateAccess: self.FieldCateAccess,

            LineIDTaskPerAssign: self.LineIDTaskPerAssign,
            AccessAssign: self.AccessAssign,
            EditAssign: self.EditAssign,
            DeleteAssign: self.DeleteAssign,
            FieldAssignEdit: self.FieldAssignEdit,
            FieldAssignAccess: self.FieldAssignAccess,

            LineIDTaskPerLink: self.LineIDTaskPerLink,
            AccessLink: self.AccessLink,
            EditLink: self.EditLink,
            DeleteLink: self.DeleteLink,
            FieldLinkEdit: self.FieldLinkEdit,
            FieldLinkAccess: self.FieldLinkAccess,

            LineIDTaskPerExecution: self.LineIDTaskPerExecution,
            AccessExecution: self.AccessExecution,
            EditExecution: self.EditExecution,
            DeleteExecution: self.DeleteExecution,
            FieldExecutionEdit: self.FieldExecutionEdit,
            FieldExecutionAccess: self.FieldExecutionAccess,

            LineIDTaskPerChecklist: self.LineIDTaskPerChecklist,
            AccessChecklist: self.AccessChecklist,
            EditChecklist: self.EditChecklist,
            DeleteChecklist: self.DeleteChecklist,
            FieldChecklistEdit: self.FieldChecklistEdit,
            FieldChecklistAccess: self.FieldChecklistAccess,

            LineIDTaskPerValuation: self.LineIDTaskPerValuation,
            AccessValuation: self.AccessValuation,
            EditValuation: self.EditValuation,
            DeleteValuation: self.DeleteValuation,
            FieldValuationEdit: self.FieldValuationEdit,
            FieldValuationAccess: self.FieldValuationAccess,

            LineIDTaskPerExpense: self.LineIDTaskPerExpense,
            AccessExpense: self.AccessExpense,
            EditExpense: self.EditExpense,
            DeleteExpense: self.DeleteExpense,
            FieldExpenseEdit: self.FieldExpenseEdit,
            FieldExpenseAccess: self.FieldExpenseAccess,

            LineIDTaskPerExpenseDetail: self.LineIDTaskPerExpenseDetail,
            AccessExpenseDetail: self.AccessExpenseDetail,
            EditExpenseDetail: self.EditExpenseDetail,
            DeleteExpenseDetail: self.DeleteExpenseDetail,
            FieldExpenseDetailEdit: self.FieldExpenseDetailEdit,
            FieldExpenseDetailAccess: self.FieldExpenseDetailAccess,

            LineIDTaskPerRequest: self.LineIDTaskPerRequest,
            AccessRequest: self.AccessRequest,
            EditRequest: self.EditRequest,
            DeleteRequest: self.DeleteRequest,
            FieldRequestEdit: self.FieldRequestEdit,
            FieldRequestAccess: self.FieldRequestAccess,

            LineIDTaskPerFile: self.LineIDTaskPerFile,
            AccessFile: self.AccessFile,
            EditFile: self.EditFile,
            DeleteFile: self.DeleteFile,
            FieldFileEdit: self.FieldFileEdit,
            FieldFileAccess: self.FieldFileAccess,


            LineIDTaskPerVideo: self.LineIDTaskPerVideo,
            AccessVideo: self.AccessVideo,
            EditVideo: self.EditVideo,
            DeleteVideo: self.DeleteVideo,
            FieldVideoEdit: self.FieldVideoEdit,
            FieldVideoAccess: self.FieldVideoAccess,
          }
        };
        // edit user
        requestData.data.ItemID = this.Task.TaskID;
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.LineIDTaskPer = responsesData.data;
            if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
            this.$bvToast.toast('Cập nhật thành công!', {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });

            this.isForm = false;
            self.$store.commit('isLoading', false);

            if ($('.component-dataflow').length) {
              self.$_storeTaskDataflowNotice(self.Task.TaskID, 'taskPer');
            }else {
              self.$_storeTaskNotice(self.Task.TaskID, 'taskPer');
            }

            self.onHideModal();

          } else {
            let htmlErrors = __.renderErrorApiHtml(responsesData.data);
            Swal.fire(
              'Thông báo',
              htmlErrors,
              'error'
            )
            self.$store.commit('isLoading', false);
          }

        }, (error) => {
          console.log(error);
          Swal.fire(
            'Thông báo',
            'Không kết nối được với máy chủ',
            'error'
          )
          self.$store.commit('isLoading', false);
        });
      }
    },
    watch: {
    },
    props: {
      value: {},
      title: {},
      name: {},
      api: {},
      Task: {},
      table: {},
      EmployeeID: {}
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

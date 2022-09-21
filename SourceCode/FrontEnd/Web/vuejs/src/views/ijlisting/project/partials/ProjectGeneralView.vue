<template>
  <div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Mã dự án: </div>
      <div class="col-md-4 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        {{value.ProjectNo}}
      </div>
      <div class="col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Mã Tabmis: </div>
      <div class="col-md-4 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        {{value.TabmisNo}}
      </div>
      <div class="col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Ngày cấp: </div>
      <div class="col-md-3 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        {{value.TabmisDate | convertServerDateToClientDate}}
      </div>
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Kỳ KHVTH: </div>
      <div class="col-md-3 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        {{(value.MPeriodOption)[value.MPeriodID]}}
      </div>
    </div>
    <div class="form-group row align-items-center">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên: </div>
      <div class="col-md-21 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        {{value.ProjectName}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="value.ParentID">
      <label class="col-md-3 m-0">Là mục con của: </label>
      <div class="col-md-21">
        {{value.ParentName}}
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Loại dự án: </label>
      <div class="col-md-15">
        <span v-for="(projectCate, key) in value.ProjectCate">
          {{projectCate.CateName}}: {{projectCate.Description}} <span v-if="key < (value.ProjectCate.length - 1)"> , </span>
        </span>
      </div>
      <label class="col-md-3 m-0">Quyền truy cập: </label>
      <div class="col-md-3">
        {{AccessTypeOptions[value.AccessType]}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0">Nhóm: </label>
      <div class="col-md-3 mb-3 mb-sm-0">
        {{value.GroupOption[value.Group]}}
      </div>

      <label class="col-md-3 m-0" title="Tên cơ quan quyết định đầu tư">Tên CQQĐĐT: </label>
      <div class="col-md-3 mb-3 mb-sm-0">
        {{value.InvestDecisionOrganName}}
      </div>
      <label class="col-md-3 m-0" >Chủ đầu tư: </label>
      <div class="col-md-3">
        {{value.InvestorName}}
      </div>
      <label class="col-md-3 m-0" title="Chương trình mục tiêu">CTMT: </label>
      <div class="col-md-3">
        {{value.ProgramName}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0">Cấp quản lý: </label>
      <div class="col-md-3 mb-3 mb-sm-0">
        {{ (value.ManagementLevelOption)[value.MPeriodID]}}
      </div>
      <label class="col-md-3 m-0">Chương: </label>
      <div class="col-md-3 mb-3 mb-sm-0">
        {{value.SbiChapterName}}
      </div>
      <label class="col-md-3 m-0">Loại khoản: </label>
      <div class="col-md-3">
        {{value.SbiCategoryName}}
      </div>
      <label class="col-md-3 m-0">Lĩnh vực: </label>
      <div class="col-md-3 mb-3 mb-sm-0">
        {{value.SectorName}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0">Tỉnh: </label>
      <div class="col-md-3 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
        {{value.Province.ProvinceName}}
      </div>
      <label class="col-md-3 m-0">Huyện: </label>
      <div class="col-md-3 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
        {{value.District.DistrictName}}
      </div>
      <label class="col-md-3 m-0">Xã: </label>
      <div class="col-md-3 ">
        {{value.Commune.CommuneName}}
      </div>
      <label class="col-md-3 m-0" title="Ban quản lý dư án">Ban QLDA: </label>
      <div class="col-md-3 mb-3 mb-sm-0">
        {{value.StateOrganName}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0" title="Ngày dự kiến khởi công">Ngày DKKC: </label>
      <div class="col-md-3 mb-3 mb-sm-0">
        {{value.ExpectedStartDate | convertServerDateToClientDate}}
      </div>
      <label class="col-md-3 m-0" title="Ngày dự kiến hoàn thành">Ngày DKHT: </label>
      <div class="col-md-3 mb-3 mb-sm-0">
        {{value.ExpectedFinishDate | convertServerDateToClientDate}}
      </div>
      <label class="col-md-3 m-0">Ngày khởi công: </label>
      <div class="col-md-3 mb-3 mb-sm-0">
        {{value.StartedDate | convertServerDateToClientDate}}
      </div>
      <label class="col-md-3 m-0">Ngày hoàn thành: </label>
      <div class="col-md-3">
        {{value.FinishedDate | convertServerDateToClientDate}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0" title="Ngày dự kiến bàn giao">Ngày DKBG: </label>
      <div class="col-md-3 mb-3 mb-sm-0">
        {{value.ExpectedHandoverDate | convertServerDateToClientDate}}
      </div>
      <label class="col-md-3 m-0" title="Ngày bàn giao">Ngày BG: </label>
      <div class="col-md-3">
        {{value.HandoverDate | convertServerDateToClientDate}}
      </div>
      <label class="col-md-3 m-0" title="Ngày quyết toán">Ngày QT: </label>
      <div class="col-md-3 mb-3 mb-sm-0">
        {{value.SettlementDate | convertServerDateToClientDate}}
      </div>
      <label class="col-md-3 m-0" title="Ngày ngừng theo dõi">Ngày NTD: </label>
      <div class="col-md-3 mb-3 mb-sm-0">
        {{value.ClosedDate | convertServerDateToClientDate}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0" title="Số quyết định đầu tư">Số QĐĐT: </label>
      <div class="col-md-3 mb-3 mb-sm-0">
        {{value.InvestdocNo}}
      </div>
      <label class="col-md-3 m-0" title="Ngày quyết định đầu tư">Ngày QĐĐT: </label>
      <div class="col-md-3">
        {{value.InvestdocDate | convertServerDateToClientDate}}
      </div>
      <label class="col-md-3 m-0" title="Số hiệp định">Số hiệp định: </label>
      <div class="col-md-3 mb-3 mb-sm-0">
        {{value.PacttdocNo}}
      </div>
      <label class="col-md-3 m-0" title="Ngày hiệp định">Ngày hiệp định: </label>
      <div class="col-md-3 mb-3 mb-sm-0">
        {{value.PacttdocDate | convertServerDateToClientDate}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0" title="Công suất thiết kế">Công suất TK: </label>
      <div class="col-md-9">
        {{value.CapableDesign}}
      </div>
      <label class="col-md-3 m-0" title="Công suất hoàn thành">Công suất HT: </label>
      <div class="col-md-9 mb-3 mb-sm-0">
        {{value.CapableFulfilling}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0" >Quy mô: </label>
      <div class="col-md-9">
        {{value.InvestScale}}
      </div>
      <label class="col-md-3 m-0" title="Địa điểm xây dựng">Địa điểm XD: </label>
      <div class="col-md-9 mb-3 mb-sm-0">
        {{value.BuildAddress}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0" >Mục tiêu: </label>
      <div class="col-md-9">
        {{value.Tarnget}}
      </div>
      <label class="col-md-3 m-0" >Tình trạng: </label>
      <div class="col-md-3">
        {{(value.StatusOption)[value.Status]}}
      </div>
      <label class="col-md-3 m-0" title="Phần trăm hoàn thành">%HT: </label>
      <div class="col-md-3">
        {{value.PercentCompleted}}
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3" title="Nguồn vốn sử dụng">Nguồn vốn SD</label>
      <div class="col-md-6">
        <span>{{value.UseCapital ? UseCapitalOptions[value.UseCapital].text : ''}}</span>
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Ghi chú: </div>
      <div class="col-md-21 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
        {{value.Note}}
      </div>
    </div>

  </div>
</template>
<script>
  export default {
    name: 'ProjectGeneralView',
    props: ['value','isDetail'],
    data(){
      return{
        AccessTypeOptions: {
          1 : 'Chia sẻ',
          2 : 'Công khai',
          3 : 'Riêng tư'
        },
        UseCapitalOptions : [
          {value: 1, text: 'Vốn trong nước'},
          {value: 2, text: 'Vốn ODA và vay ưu đãi'},
          {value: 3, text: 'Vốn vay nợ nước ngoài'},
          {value: 4, text: 'Vốn viện trợ không hoàn lại'},
          {value: 5, text: 'Vốn khác'},
        ],
      }
    }
  }
</script>

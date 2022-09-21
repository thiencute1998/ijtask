<template>
  <div>
    <div class="form-group row">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Họ: </div>
      <div class="col-md-5 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.FirstName}}
      </div>
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên đệm</div>
      <div class="col-md-5 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.MiddleName}}
      </div>
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên đệm</div>
      <div class="col-md-5 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.LastName}}
      </div>
    </div>
    <div class="form-group row">
      <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên đầy đủ</div>
      <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
        {{value.EmployeeName}}
      </div>
      <div class="col-md-6 col-sm-8 col-24 d-flex app-object-code">
        <span>Mã số</span>
        {{value.EmployeeNo}}
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 m-0">Loại nhân viên</label>
      <div class="col-md-21 m-0">
        <span v-for="(employeeCate, key) in value.EmployeeCate">
          {{employeeCate.CateName}} <span v-if="employeeCate.Description"> : {{employeeCate.Description}}</span>
          <br v-if="key < (value.EmployeeCate.length - 1) && value.EmployeeCate.length > 1">
        </span>
      </div>
    </div>
    <div class="form-group row align-items-center">
      <label class="col-md-3 m-0">Đơn vị</label>
      <div class="col-md-5">
        {{value.CompanyName}}
      </div>
      <label class="col-md-3 m-0">Phòng ban</label>
      <div class="col-md-5" id="department">
        {{value.DepartmentName}}
      </div>
      <label class="col-md-3 m-0">Chức vụ</label>
      <div class="col-md-5">
        {{value.PositionName}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0">Ngày sinh</label>
      <div class="col-md-9">
        {{value.BirthDay}}
      </div>
      <label class="col-md-3 m-0">ĐT cơ quan</label>
      <div class="col-md-5">
        {{value.OfficePhone}}
      </div>
      <label class="col-md-3 m-0">ĐT di động</label>
      <div class="col-md-5">
        {{value.HandPhone}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0">Email</label>
      <div class="col-md-5">
        {{value.Email}}
      </div>
      <label class="col-md-3 m-0">Facebook ID</label>
      <div class="col-md-5">
        {{value.FacebookID}}
      </div>
      <label class="col-md-3 m-0">Twitter ID</label>
      <div class="col-md-5">
        {{value.TwitterID}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0">Skype ID</label>
      <div class="col-md-5">
        {{value.SkypeID}}
      </div>
      <label class="col-md-3 m-0">Zalo ID</label>
      <div class="col-md-5">
        {{value.ZaloID}}
      </div>
      <label class="col-md-3 m-0">Người dùng</label>
      <div class="col-md-5">
        {{value.UserName}}
      </div>
    </div>
    <div class="form-group row align-items-center" v-if="isDetail">
      <label class="col-md-3 m-0" title="Chứng minh nhân dân/ căn cước công dân">CMND/CCCD</label>
      <div class="col-md-5">
        {{value.CitizenIdNo}}
      </div>
      <label class="col-md-3 m-0">Ngày cấp</label>
      <div class="col-md-5">
        {{value.CitizenIdDate}}
      </div>
      <label class="col-md-3 m-0">Nơi cấp</label>
      <div class="col-md-5">
        {{value.CitizenIdAt}}
      </div>
    </div>
    <div class="form-group row" v-if="isDetail">
      <label class="col-md-3">Ghi chú</label>
      <div class="col-md-20">
        {{value.Note}}
      </div>
    </div>

  </div>
</template>
<script>
  export default {
    name: 'CompanyGeneralView',
    props: ['value','isDetail'],
    data(){
      return{
      }
    }
  }
</script>

<template>
  <div class="animated fadeIn scroll-touch component-dashboard">
    <div class="container-fluid py-3 dashboard-container">
      <div>
        <div class="dashboard-header d-flex justify-content-between align-items-center mb-3">
          <div class="header-right"><span style="font-size: 21px">Bảng tin</span></div>
          <div class="header-user-type ml-auto">
            <b-form-select
              v-model="filter.UserType" @change="changeUserType"
              :options="[{value: 1, text: 'Bộ Tài chính'}, {value: 2, text: 'CQTW'}, {value: 3, text: 'Tỉnh'}, {value: 4, text: 'Huyện'}, {value: 5, text: 'Tổng hợp'}]">
            </b-form-select>
          </div>
          <div class="header-left">
            <ol class="breadcrumb float-sm-right m-0 pt-0 pb-0" style="background: transparent; border: none">
              <li class="breadcrumb-item" :class="[(query.muc === 'quanly-nsnn') ? 'active' : '']">
                <a href="#" v-if="query.muc !== 'quanly-nsnn'" @click="changeBreadcrumb($event, '/bang-tin?muc=quanly-nsnn')">Bảng tin</a>
                <span v-else>Bảng tin</span>
              </li>
              <li class="breadcrumb-item" v-if="query.muc === 'quanly-nsnn'" :class="[(query.muc && !query.theloai) ? 'active' : '']">
                <a href="#" @click="changeBreadcrumb($event, '/bang-tin?muc=quanly-nsnn')" v-if="query.muc && query.theloai && !query.chitiet">Chấp hành NSNN</a>
                <span v-else>Chấp hành NSNN</span>
              </li>
              <li class="breadcrumb-item" v-if="query.muc === 'quyettoan-nsnn'" :class="[(query.muc && !query.theloai) ? 'active' : '']">
                <a href="#" @click="changeBreadcrumb($event, '/bang-tin?muc=quyettoan-nsnn')" v-if="query.muc && query.theloai && !query.chitiet">Quyết toán NSNN</a>
                <span v-else>Quyết toán NSNN</span>
              </li>
              <li class="breadcrumb-item" v-if="query.muc === 'dutoan-nsnn'" :class="[(query.muc && !query.theloai) ? 'active' : '']">
                <a href="#" @click="changeBreadcrumb($event, '/bang-tin?muc=dutoan-nsnn')" v-if="query.muc && query.theloai && !query.chitiet">Dự toán NSNN</a>
                <span v-else>Dự toán NSNN</span>
              </li>

              <li class="breadcrumb-item" :class="[(query.muc && query.theloai && !query.chitiet) ? 'active' : '']" v-if="query.muc === 'quanly-nsnn' && query.theloai === 'tong-thu'">
                <a href="#" @click="changeBreadcrumb($event, '/bang-tin?muc=quanly-nsnn')" v-if="query.muc && query.theloai && query.chitiet">Tổng thu</a>
                <span v-else>Thu NSNN</span>
              </li>
              <li class="breadcrumb-item" :class="[(query.muc && query.theloai && !query.chitiet) ? 'active' : '']" v-if="query.muc === 'quanly-nsnn' && query.theloai === 'tong-chi'">
                <a href="#" @click="changeBreadcrumb($event, '/bang-tin?muc=quanly-nsnn')" v-if="query.muc && query.theloai && query.chitiet">Tổng chi</a>
                <span v-else>Chi NSNN</span>
              </li>
              <li class="breadcrumb-item" :class="[(query.muc && query.theloai && !query.chitiet) ? 'active' : '']" v-if="query.muc === 'dutoan-nsnn' && query.theloai === 'dt-thu'">
                <a href="#" @click="changeBreadcrumb($event, '/bang-tin?muc=dutoan-nsnn')" v-if="query.muc && query.theloai && query.chitiet">Dự toán thu</a>
                <span v-else>Dự toán thu </span>
              </li>
              <li class="breadcrumb-item" :class="[(query.muc && query.theloai && !query.chitiet) ? 'active' : '']" v-if="query.muc === 'dutoan-nsnn' && query.theloai === 'dt-chi'">
                <a href="#" @click="changeBreadcrumb($event, '/bang-tin?muc=dutoan-nsnn')" v-if="query.muc && query.theloai && query.chitiet">Dự toán chi</a>
                <span v-else>Dự toán chi </span>
              </li>
              <li class="breadcrumb-item" :class="[(query.muc && query.theloai && !query.chitiet) ? 'active' : '']" v-if="query.muc === 'quyettoan-nsnn' && query.theloai === 'tinh'">
                <a href="#" @click="changeBreadcrumb($event, '/bang-tin?muc=quyettoan-nsnn')" v-if="query.muc && query.theloai && query.chitiet">Tỉnh</a>
                <span v-else>Tỉnh</span>
              </li>
              <li class="breadcrumb-item" :class="[(query.muc && query.theloai && !query.chitiet) ? 'active' : '']" v-if="query.muc === 'quyettoan-nsnn' && query.theloai === 'huyen'">
                <a href="#" @click="changeBreadcrumb($event, '/bang-tin?muc=quyettoan-nsnn')" v-if="query.muc && query.theloai && query.chitiet">Huyện</a>
                <span v-else>Huyện</span>
              </li>
              <li class="breadcrumb-item" :class="[(query.muc && query.theloai && !query.chitiet) ? 'active' : '']" v-if="query.muc === 'quyettoan-nsnn' && query.theloai === 'xa'">
                <a href="#" @click="changeBreadcrumb($event, '/bang-tin?muc=quyettoan-nsnn')" v-if="query.muc && query.theloai && query.chitiet">Huyện</a>
                <span v-else>Xã</span>
              </li>

              <!--              <li class="breadcrumb-item active">Chi NSNN</li>-->
            </ol>
          </div>
        </div>
        <div class="row mb-3">
          <label class="col-md-1 col-1 col-sm-1 col-lg-1 col-xl-1 d-flex align-items-center m-0">Kỳ</label>
          <div class="col-3 col-md-3 col-sm-3 col-lg-3 col-xl-3">
            <b-form-select
              v-model="filter.PeriodID" @change="changePeriodType"
              :options="PeriodOption">
            </b-form-select>
          </div>
          <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3" v-if="filter.PeriodID !== 99 && filter.PeriodID">
            <b-form-select
              v-if="filter.PeriodID !== 5 && filter.PeriodID !== 99"
              v-model="filter.PeriodValue" @change="changePeriodValue"
              :options="PeriodValueOption">
            </b-form-select>
            <ijcore-date-picker v-model="filter.FromDate" style="width: 100%;" v-if="filter.PeriodID === 5" @input-date-picker="changeFromDate"></ijcore-date-picker>
          </div>
          <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3" v-if="filter.PeriodID === 99" title="Từ ngày">
            <ijcore-date-picker v-model="filter.FromDate" style="width: 100%;"></ijcore-date-picker>
          </div>
          <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3" v-if="filter.PeriodID === 99" title="Đến ngày">
            <ijcore-date-picker v-model="filter.ToDate" style="width: 100%;"></ijcore-date-picker>
          </div>

          <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3" v-if="filter.UserType === 1 || filter.UserType === 2 || filter.UserType === 5">
            <ijcore-modal-listing
              v-model="filter" title="Trung ương" placeholder-input="TW" api="/listing/api/common/list"
              FieldName="CenterName" FieldNo="CenterNo" FieldID="CenterID"
              table="center">
            </ijcore-modal-listing>
          </div>
          <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3" v-if="filter.UserType === 1 || filter.UserType === 5">
            <ijcore-modal-listing
              v-model="filter" title="Tỉnh" placeholder-input="Tỉnh" api="/listing/api/common/list"
              FieldName="ProvinceName" FieldNo="ProvinceNo" FieldID="ProvinceID"
              :key="filter.UserType"
              table="province">
            </ijcore-modal-listing>
          </div>
<!--          <div class="col-1 d-flex align-items-center">Huyện</div>-->
          <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3" v-if="filter.UserType === 3 || filter.UserType === 5">
            <ijcore-modal-listing
              v-model="filter" title="Huyện" placeholder-input="Huyện" api="/listing/api/common/list"
              :FieldWhere="{ProvinceID: filter.ProvinceID}"
              :key="filter.UserType"
              FieldName="DistrictName" FieldNo="DistrictNo" FieldID="DistrictID" table="district">
            </ijcore-modal-listing>
          </div>
<!--          <div class="col-1 d-flex align-items-center">Xã</div>-->
          <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3" v-if="filter.UserType === 3 || filter.UserType === 4 || filter.UserType === 5">
            <ijcore-modal-listing
              v-model="filter" title="Xã" placeholder-input="Xã" api="/listing/api/common/list"
              :FieldWhere="{ProvinceID: filter.ProvinceID, DistrictID: filter.DistrictID}"
              :key="filter.UserType"
              FieldName="CommuneName" FieldNo="CommuneNo" FieldID="CommuneID" table="commune">
            </ijcore-modal-listing>
          </div>
<!--          <div class="col-1 d-flex align-items-center">ĐV</div>-->
          <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3" v-if="filter.UserType === 1 || filter.UserType === 5">
            <ijcore-modal-listing
              v-model="filter" title="Ngành" placeholder-input="Ngành" api="/listing/api/common/list"
              :key="filter.UserType"
              FieldName="SectorName" FieldNo="SectorNo" FieldID="SectorID" table="sector">
            </ijcore-modal-listing>
          </div>
          <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3" v-if="filter.UserType === 1 || filter.UserType === 2 || filter.UserType === 3 || filter.UserType === 4 || filter.UserType === 5">
            <ijcore-modal-listing
              v-model="filter" title="Đơn vị" placeholder-input="Đơn vị" api="/listing/api/common/list"
              :key="filter.UserType"
              :FieldWhere="{ProvinceID: filter.ProvinceID, DistrictID: filter.DistrictID, CommuneID: filter.CommuneID}"
              FieldName="CompanyName" FieldNo="CompanyNo" FieldID="CompanyID" table="company">
            </ijcore-modal-listing>
          </div>
          <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3" v-if="query.muc === 'quanly-nsnn' && query.theloai === 'tong-thu'">
            <b-form-select v-model="filter.RevenueCateID" :options="filter.RevenueCateListOptions"></b-form-select>
          </div>
          <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3" v-if="query.muc === 'quanly-nsnn' && query.theloai === 'tong-chi'">
            <b-form-select v-model="filter.ExpenseCateID" :options="filter.ExpenseCateListOptions"></b-form-select>
          </div>
        </div>
      </div>
      <div class="dashboard-content">
        <div v-if="query.muc === 'dh'">
          <dashboard-module-task :date-range="filter.DateRange" :query="query"></dashboard-module-task>
        </div>
        <div v-if="query.muc === 'quanly-nsnn'">
          <dashboard-module-state-budget-revenues :filter="filter" :query="query"></dashboard-module-state-budget-revenues>
        </div>
        <div v-if="query.muc === 'dutoan-nsnn'">
          <dashboard-module-state-budget-estimates :filter="filter" :query="query"></dashboard-module-state-budget-estimates>
        </div>
        <div v-if="query.muc === 'quyettoan-nsnn'">
          <dashboard-module-state-budget-settlement :filter="filter" :query="query"></dashboard-module-state-budget-settlement>
        </div>
      </div>
     </div>
  </div>
</template>

<script>

import {TokenService} from '@/services/storage.service';
import IjcoreDateRange from "../components/IjcoreDateRange";
import IjcoreModalSearchInput from "../components/IjcoreModalSearchInput";
import IjcoreModalListing from "../components/IjcoreModalListing";
import DashboardModuleTask from "./dashboard/DashboardModuleTask";
import DashboardModuleStateBudgetRevenues from "./dashboard/DashboardModuleStateBudgetRevenues";
import DashboardModuleStateBudgetEstimates from "./dashboard/DashboardModuleStateBudgetEstimates";
import DashboardModuleStateBudgetSettlement from "./dashboard/DashboardModuleStateBudgetSettlement";
import DashboardModuleStateBudgetSettlementDetail from "./dashboard/DashboardModuleStateBudgetSettlementDetail";
import IjcoreDatePicker from "../components/IjcoreDatePicker";
import moment from "moment";
export default {
  name: 'dashboard',
  components: {
    IjcoreDateRange,
    IjcoreModalSearchInput,
    DashboardModuleTask,
    DashboardModuleStateBudgetRevenues,
    DashboardModuleStateBudgetEstimates,
    DashboardModuleStateBudgetSettlement,
    DashboardModuleStateBudgetSettlementDetail,
    IjcoreModalListing,
    IjcoreDatePicker,
  },
  data: function () {
    return {
      filter: {
        ProvinceName: 'Tỉnh Nam Định',
        ProvinceNo: '420000',
        ProvinceID: 36,
        DistrictName: '',
        DistrictNo: '',
        DistrictID: null,
        CommuneName: '',
        CommuneNo: '',
        CommuneID: null,
        CompanyName: '',
        CompanyNo: '',
        CompanyID: '',
        SectorID: null,
        SectorName: '',
        SectorNo: '',
        PeriodID: 1,
        PeriodName: '',
        PeriodValue: 2020,
        PeriodValueName: '',
        FromDate: null,
        ToDate: null,
        UserType: 3,
        RevenueCateID: null,
        RevenueCateListOptions: [{value: null, text: 'Chọn cơ cấu thu'},{value: 22, text: 'Thu nội địa'},{value: 33, text: 'Các khoản thu từ thuế hàng hóa trong nước'},
          {value: 34, text: 'Các khoản thu từ phí, lệ phí'},{value: 35, text: 'Các khoản thu khác'},{value: 26, text: 'Thu từ dầu thô và khí thiên nhiên'},
          {value: 27, text: 'Thu từ hoạt động xuất nhập khẩu'},{value: 28, text: 'Thu viện trợ'} ,{value: 29, text: 'Thu khác'}],
        ExpenseCateID: null,
        ExpenseCateListOptions: [{value: null, text: 'Chọn cơ cấu chi'},{value: 1, text: 'Chi đầu tư phát triển'},{value: 2, text: 'Chi đầu tư phát triển cho các dự án'},
          {value: 3, text: 'Chi đầu tư và hỗ trợ vốn cho DN'},{value: 4, text: 'Chi đầu tư phát triển khác'},{value: 5, text: 'Chi thường xuyên'},
          {value: 6, text: 'Chi các chương trình mục tiêu'},{value: 7, text: 'Chi trả nợ lãi do chính quyền địa phương vay'} ,{value: 8, text: 'Chi viện trợ'},
          {value: 9, text: 'Chi cải cách tiền lương'},{value: 10, text: 'Các khoản chi còn lại'}]
      },
      PeriodOption: [
        {value: null, text: 'Chọn kỳ'},
        {value: 1, text: 'Năm'},
        {value: 2, text: 'Quý'},
        {value: 3, text: 'Tháng'},
        {value: 4, text: 'Tuần'},
        {value: 5, text: 'Ngày'},
        {value: 6, text: '6 tháng'},
        {value: 7, text: '9 tháng'},
        {value: 8, text: '3 năm'},
        {value: 9, text: '5 năm'},
        {value: 10, text: '10 năm'},
        {value: 99, text: 'Tùy chọn'},
      ],
      PeriodValueOption: [],
      query: {
        muc: (this.$route.query && this.$route.query.muc) ? this.$route.query.muc : 'quanly-nsnn',
        theloai: (this.$route.query && this.$route.query.theloai) ? this.$route.query.theloai : '',
        chitiet: (this.$route.query && this.$route.query.chitiet) ? this.$route.query.chitiet : '',
        tinh: (this.$route.query && this.$route.query.tinh) ? this.$route.query.tinh : '',
        huyen: (this.$route.query && this.$route.query.huyen) ? this.$route.query.huyen : '',

      }
    }
  },
  mounted() {
    this.buildQuery();
    this.changePeriodType();
  },
  methods: {
    changePeriodType(){
      let self = this;
      let workDate = TokenService.getWorkdate();
      if (!workDate) {
        workDate = moment().format('L');
      }
      let momentWorkDate = moment(workDate, 'L');
      let yearWorkDate = momentWorkDate.get("year");
      let monthWorkDate = momentWorkDate.get('month');
      this.PeriodValueOption = [];
      switch (this.filter.PeriodID) {
        case 1:
          for (let i = 8; i >= 1; i--) {
            let year = Number(yearWorkDate) - i;
            let tmpObj = {};
            tmpObj.value = year;
            tmpObj.text = year;
            tmpObj.fromDate = moment([year]).startOf("year").format('L');
            tmpObj.toDate = moment([year]).endOf("year").format('L');
            self.PeriodValueOption.push(tmpObj);
          }
          this.PeriodValueOption.push({
            value: yearWorkDate,
            text: yearWorkDate,
            fromDate: moment([yearWorkDate]).startOf("year").format('L'),
            toDate: moment([yearWorkDate]).endOf("year").format('L')
          });
          for (let i = 1; i <= 8; i++) {
            let year = Number(yearWorkDate) + i;
            let tmpObj = {};
            tmpObj.value = Number(year);
            tmpObj.text = year;
            tmpObj.fromDate = moment([year]).startOf("year").format('L');
            tmpObj.toDate = moment([year]).endOf("year").format('L');
            self.PeriodValueOption.push(tmpObj);
          }
          this.filter.PeriodValue = Number(2020);
          break;
        case 2:
          this.PeriodValueOption.push({
            value: null,
            text: '-- Tùy chọn --'
          });
          this.PeriodValueOption.push({
            value: 1,
            text: 'Quý 1/' + yearWorkDate,
            fromDate: moment([yearWorkDate, 0]).startOf("months").format('L'),
            toDate: moment([yearWorkDate, 2]).endOf("months").format('L')
          });
          this.PeriodValueOption.push({
            value: 2,
            text: 'Quý 2/' + yearWorkDate,
            fromDate: moment([yearWorkDate, 3]).startOf("months").format('L'),
            toDate: moment([yearWorkDate, 5]).endOf("months").format('L')
          });
          this.PeriodValueOption.push({
            value: 3,
            text: 'Quý 3/' + yearWorkDate,
            fromDate: moment([yearWorkDate, 6]).startOf("months").format('L'),
            toDate: moment([yearWorkDate, 8]).endOf("months").format('L')
          });
          this.PeriodValueOption.push({
            value: 4,
            text: 'Quý 4/' + yearWorkDate,
            fromDate: moment([yearWorkDate, 9]).startOf("months").format('L'),
            toDate: moment([yearWorkDate, 11]).endOf("months").format('L')
          });
          this.filter.PeriodValue = null;
          break;
        case 3:
          self.PeriodValueOption.push({
            value: null,
            text: '-- Tùy chọn --'
          });
          for (let i = 1; i <= 12; i++) {
            self.PeriodValueOption.push({
              value: i,
              text: 'Tháng ' + i + '/' + yearWorkDate,
              fromDate: moment([yearWorkDate, i - 1]).startOf("months").format('L'),
              toDate: moment([yearWorkDate, i - 1]).endOf("months").format('L')
            });
          }
          this.filter.PeriodValue = null;
          break;
        case 4:
          self.PeriodValueOption.push({
            value: null,
            text: '-- Tùy chọn --'
          });
          for (let i = 1; i <= 52; i++) {
            self.PeriodValueOption.push({
              value: i,
              text: 'Tuần ' + i + '/' + yearWorkDate,
              fromDate: moment(workDate).week(i - 1).startOf('week').format('L'),
              toDate: moment(workDate).week(i-1).endOf('week').format('L')
            });
          }
          this.filter.PeriodValue = null;
          break;
        case 5:
          this.filter.FromDate = workDate;
          this.filter.ToDate = this.filter.FromDate;
          break;
        case 6:
          self.PeriodValueOption.push({
            value: 1,
            text: yearWorkDate + '/6th đầu',
            fromDate: moment([yearWorkDate, 0]).startOf("months").format('L'),
            toDate: moment([yearWorkDate, 5]).endOf("months").format('L')
          });
          self.PeriodValueOption.push({
            value: 2,
            text: yearWorkDate + '/6th cuối',
            fromDate: moment([yearWorkDate, 6]).startOf("months").format('L'),
            toDate: moment([yearWorkDate, 11]).endOf("months").format('L')
          });
          this.filter.PeriodValue = 1;
          // this.filter.FromDate = workDate;
          // this.filter.ToDate = workDate;
          break;
        case 7:
          this.PeriodValueOption.push({
            value: 1,
            text: (Number(yearWorkDate) - 1) + '/9 tháng',
            fromDate: moment([(Number(yearWorkDate) - 1), 0]).startOf("months").format('L'),
            toDate: moment([(Number(yearWorkDate) - 1), 8]).endOf("months").format('L')
          });
          this.PeriodValueOption.push({
            value: 2,
            text: (Number(yearWorkDate)) + '/9 tháng',
            fromDate: moment([yearWorkDate, 0]).startOf("months").format('L'),
            toDate: moment([yearWorkDate, 8]).endOf("months").format('L')
          });
          this.PeriodValueOption.push({
            value: 3,
            text: (Number(yearWorkDate) + 1) + '/9 tháng',
            fromDate: moment([(Number(yearWorkDate) + 1), 0]).startOf("months").format('L'),
            toDate: moment([(Number(yearWorkDate) + 1), 8]).endOf("months").format('L')
          });
          this.filter.PeriodValue = 2;
          break;
        case 8:
          this.PeriodValueOption.push({
            value: 1,
            text: (Number(yearWorkDate) - 3) + ' - ' + (Number(yearWorkDate) - 1),
            fromDate: moment([Number(yearWorkDate) - 3]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) - 1]).endOf("year").format('L')
          });
          this.PeriodValueOption.push({
            value: 2,
            text: (Number(yearWorkDate)) + ' - ' + (Number(yearWorkDate) + 2),
            fromDate: moment([Number(yearWorkDate)]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) + 1]).endOf("year").format('L')
          });
          this.PeriodValueOption.push({
            value: 3,
            text: (Number(yearWorkDate) + 3) + ' - ' + (Number(yearWorkDate) + 5),
            fromDate: moment([Number(yearWorkDate) + 3]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) + 5]).endOf("year").format('L')
          });
          this.filter.PeriodValue = 2;
          break;
        case 9:
          this.PeriodValueOption.push({
            value: 1,
            text: (Number(yearWorkDate) - 5) + ' - ' + (Number(yearWorkDate) - 1),
            fromDate: moment([Number(yearWorkDate) - 5]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) - 1]).endOf("year").format('L')
          });
          this.PeriodValueOption.push({
            value: 2,
            text: (Number(yearWorkDate)) + ' - ' + (Number(yearWorkDate) + 4),
            fromDate: moment([Number(yearWorkDate)]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) + 4]).endOf("year").format('L')
          });
          this.PeriodValueOption.push({
            value: 3,
            text: (Number(yearWorkDate) + 5) + ' - ' + (Number(yearWorkDate) + 9),
            fromDate: moment([Number(yearWorkDate) + 5]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) + 9]).endOf("year").format('L')
          });
          this.filter.PeriodValue = 2;
          break;
        case 10:
          this.PeriodValueOption.push({
            value: 1,
            text: (Number(yearWorkDate) - 10) + ' - ' + (Number(yearWorkDate) - 1),
            fromDate: moment([Number(yearWorkDate) - 10]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) - 1]).endOf("year").format('L')
          });
          this.PeriodValueOption.push({
            value: 2,
            text: (Number(yearWorkDate)) + ' - ' + (Number(yearWorkDate) + 9),
            fromDate: moment([Number(yearWorkDate)]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) + 9]).endOf("year").format('L')
          });
          this.PeriodValueOption.push({
            value: 3,
            text: (Number(yearWorkDate) + 10) + ' - ' + (Number(yearWorkDate) + 19),
            fromDate: moment([Number(yearWorkDate) + 10]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) + 19]).endOf("year").format('L')
          });
          this.filter.PeriodValue = 2;
          break;
        case 99:
          this.filter.FromDate = '';
          this.filter.ToDate = '';
          break;
        default:
          break;
      }
      this.changePeriodValue();
    },
    changeUserType(){
      this.filter.ProvinceID = 36;
      this.filter.ProvinceName = 'Tỉnh Nam Định';
      this.filter.ProvinceNo = '420000';
    },
    changePeriodValue(){
      let dateRange = _.find(this.PeriodValueOption, ['value', Number(this.filter.PeriodValue)]);
      if (dateRange) {
        this.filter.FromDate = dateRange.fromDate;
        this.filter.ToDate = dateRange.toDate;
      }
    },
    changeFromDate() {
      this.filter.FromDate = this.filter.ToDate;
    },
    buildQuery() {
      let queryTmp = {};
      if (this.query.muc) {
        queryTmp.muc = this.query.muc;
      }
      if (this.query.theloai) {
        queryTmp.theloai = this.query.theloai;
      }
      if (this.query.chitiet) {
        queryTmp.chitiet = this.query.chitiet;
      }
      if (this.query.tinh) {
        queryTmp.tinh = this.query.tinh;
      }
      if (this.query.huyen) {
        queryTmp.huyen = this.query.huyen;
      }
      this.$router.replace({query: queryTmp});
    },
    changeBreadcrumb(e, url){
      e.preventDefault();
      e.stopPropagation();
      this.$router.push({
        path: url,
      });
    }
  },
  watch: {
    'filter.ProvinceID'() {
      this.filter.DistrictID = null;
      this.filter.DistrictNo = '';
      this.filter.DistrictName = '';

      this.filter.CommuneID = null;
      this.filter.CommuneName = '';
      this.filter.CommuneNo = '';
    },
    'filter.DistrictID'() {
      this.filter.CommuneID = null;
      this.filter.CommuneName = '';
      this.filter.CommuneNo = '';
    },
    query: {
      handler(val){
        // do stuff
        this.buildQuery();
      },
      deep: true
    },
    '$store.state.menuTop.dashboard.tabNo'() {
      if (this.$store.state.menuTop.dashboard.tabNo === 1) {
        this.query.muc = 'dh';
        this.query.theloai = '';
        this.query.chitiet = '';
        this.query.tinh = '';
        this.query.huyen = '';
      }else if (this.$store.state.menuTop.dashboard.tabNo === 2) {
        this.query.muc = 'quanly-nsnn';
        this.query.theloai = '';
        this.query.chitiet = '';
        this.query.tinh = '';
        this.query.huyen = '';
      }else if (this.$store.state.menuTop.dashboard.tabNo === 17) {
        this.query.muc = 'quyettoan-nsnn';
        this.query.theloai = '';
        this.query.chitiet = '';
        this.query.tinh = '';
        this.query.huyen = '';
      }
      else if (this.$store.state.menuTop.dashboard.tabNo === 16) {
        this.query.muc = 'dutoan-nsnn';
        this.query.theloai = '';
        this.query.chitiet = '';
        this.query.tinh = '';
        this.query.huyen = '';
      }
    },
    '$route'(to, from){
      let query = this.$route.query;
      if (!this.query.muc) {
        this.$set(this.query, 'muc', '');
      }
      if (!this.query.theloai) {
        this.$set(this.query, 'theloai', '');
      }
      if (!this.query.chitiet) {
        this.$set(this.query, 'chitiet', '');
      }
      if (!this.query.tinh) {
        this.$set(this.query, 'tinh', '');
      }
      if (!this.query.huyen) {
        this.$set(this.query, 'huyen', '');
      }
      this.query.muc = '';
      this.query.theloai = '';
      this.query.chitiet = '';
      this.query.tinh = '';
      this.query.huyen = '';
      if (query && query.muc) {
        this.query.muc = query.muc;
        let tabNo = 1;
        if (query.muc === 'dh') {
          tabNo = 1;
        }else if (query.muc === 'quanly-nsnn') {
          tabNo = 2;
        }else if (query.muc === 'quyettoan-nsnn') {
          tabNo = 17;
        }
        else if (query.muc === 'dutoan-nsnn') {
          tabNo = 16;
        }
        this.$store.commit('menuTop', {dashboard: {tabNo: tabNo}});
      }
      if (query && query.theloai) {
        this.query.theloai = query.theloai;
      }
      if (query && query.chitiet) {
        this.query.chitiet = query.chitiet;
      }
      if (query && query.tinh) {
        this.query.tinh = query.tinh;
      }
      if (query && query.huyen) {
        this.query.huyen = query.huyen;
      }
    }
  }
}
</script>

<style>
  /* IE fix */
  #card-chart-01, #card-chart-02 {
    width: 100% !important;
  }
  .component-dashboard .card-body {
    cursor: pointer;
    padding: 1rem;
  }
  .component-dashboard .btn-group .btn-secondary {
    background: #fff;
  }
  .component-dashboard .card-deck {
    margin-left: -5px;
    margin-right: -5px;
  }
  .component-dashboard .card-deck .card {
    margin-left: 5px;
    margin-right: 5px;
  }
  .dashboard-container {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
  }
  .dashboard-content {
    overflow-x: hidden;
  }
  .icon-chart-full-width {
    position: absolute;
    top: 0;
    right: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border-radius: 4px;
    cursor: pointer;
  }
</style>

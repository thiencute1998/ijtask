<template>
  <div class="component-report-viewer">
    <div>
      <div class="main-header">
        <div class="main-header-padding">
          <b-row class="mb-2">
            <b-col class="col-md-12">
              <div class="main-header-item main-header-name">
                <span class="text-capitalize"><i class="fa fa-list mr-2"></i> Báo cáo</span>
              </div>
            </b-col>
            <b-col class="col-md-12">
              <div class="main-header-item main-header-icons">
                <b-button-group id="main-header-views" class="main-header-views">
                  <b-button id="tooltip-view-filter" v-b-toggle.collapse-main-header-filter title="Lọc" class="main-header-view"><i class="fa fa-filter"></i></b-button>
                </b-button-group>
                <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
                  <sidebar-toggle class="d-md-down-none btn" display="lg" :defaultOpen=true />
                </div>
              </div>
            </b-col>
          </b-row>
          <b-row class="mb-2">
            <b-col class="col-lg-12 col-md-24 col-sm-24 col-24 mb-2 mb-lg-0 d-lg-flex justify-content-start align-items-center">
              <div class="main-header-item main-header-actions"></div>
            </b-col>
            <b-col class="col-lg-12 col-md-24 col-sm-24 col-24"></b-col>
          </b-row>

            <div class="main-header-filter pt-2">
              <div class="row mb-2">

              </div>
              <div class="row mb-2">
                <label class="col-md-2 col-2 col-sm-2 col-lg-2 col-xl-2 d-flex m-0">Kỳ</label>
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

                <div class="col-4 mb-2">
                  <ijcore-modal-listing
                    v-model="filter" title="Chỉ thị"  api="/listing/api/common/list"
                    FieldName="DirectionName" FieldNo="DirectionNo" FieldID="DirectionID" table="direction">
                  </ijcore-modal-listing>
                </div>
                <div class="col-3 mb-2">
                  <ijcore-modal-listing
                    v-model="filter" title="Ngành"  api="/listing/api/common/list"
                    FieldName="SectorName" FieldNo="SectorNo" FieldID="SectorID" table="sector">
                  </ijcore-modal-listing>
                </div>
                <div class="col-3 mb-2">
                  <ijcore-modal-listing
                    v-model="filter" title="Tỉnh"  api="/listing/api/common/list"
                    FieldName="ProvinceName" FieldNo="ProvinceNo" FieldID="ProvinceID" table="province">
                  </ijcore-modal-listing>
                </div>
                <div class="col-3 mb-2">
                  <ijcore-modal-listing
                    v-model="filter" title="Huyện"  api="/listing/api/common/list"
                    :FieldWhere="{ProvinceID: filter.ProvinceID}"
                    FieldName="DistrictName" FieldNo="DistrictNo" FieldID="DistrictID" table="district">
                  </ijcore-modal-listing>
                </div>
                <div class="col-3 mb-2">
                  <ijcore-modal-listing
                    v-model="filter" title="Xã"  api="/listing/api/common/list"
                    :FieldWhere="{ProvinceID: filter.ProvinceID, DistrictID: filter.DistrictID}"
                    FieldName="CommuneName" FieldNo="CommuneNo" FieldID="CommuneID" table="commune">
                  </ijcore-modal-listing>
                </div>
                <div class="col-4 mb-2">
                  <ijcore-modal-listing
                    v-model="filter" title="Đơn vị"  api="/listing/api/common/list"
                    :FieldWhere="{ProvinceID: filter.ProvinceID, DistrictID: filter.DistrictID, CommuneID: filter.CommuneID}"
                    FieldName="CompanyName" FieldNo="CompanyNo" FieldID="CompanyID" table="company">
                  </ijcore-modal-listing>
                </div>
                <b-col>
                  <div class="main-action d-lg-flex justify-content-end">
                    <b-button variant="primary" @click="reloadReport" size="md">
                      <i class="fa fa-search"></i> Tìm
                    </b-button>
                  </div>
                </b-col>
              </div>

            </div>
        </div>
        </div>
      </div>
      <div class="main-body">
        <b-card class="m-0 border-0" body-class="py-0 px-0">
          <div class="content-body" style="height: 100%">
            <div class="content-body-list" style="">
              <report-viewer
                ref="report-viewer"
                report-folder-name="SBP/DH"
                report-name="SBP_B222_DH"
                :report-filter="filter"
                :report-data-api= Api>
              </report-viewer>
            </div>
          </div>
        </b-card>
      </div>
    </div>
</template>


<script>
import {TokenService} from '@/services/storage.service';
import ApiService from '@/services/api.service';
import ReportViewer from "@/views/report/ReportViewer";
import IjcoreDatePicker from "@/components/IjcoreDatePicker";
import IjcoreModalListing from "@/components/IjcoreModalListing";
import moment from "moment";

export default {
  name: 'listing-items',
  data() {
    return {
      filter: {
        Year: new Date().getFullYear(),
        CompanyID: null,
        CompanyName: '',
        CompanyNo: '',
        SectorNo: '',
        SectorName: '',
        SectorID: '',
        PeriodID: 1,
        PeriodName: '',
        PeriodValue: 2020,
        PeriodValueName: '',
        FromDate: null,
        ToDate: null,
        ProvinceName: '',
        ProvinceNo: '',
        ProvinceID: '',
        DistrictNo: '',
        DistrictName: '',
        DistrictID: '',
        CommuneNo: '',
        CommuneName: '',
        CommuneID: '',
        DirectionName: '',
        DirectionID: null,
        DirectionNo: ''
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

      },

      Api:"/report/api/StateBudgetPlanning/Operating/SBP_B222_DH",
      momentFormat: {
        //[optional] Date to String
        stringify: (date) => {
          return date ? moment(date).format('YYYY') : ''
        },
        //[optional]  String to Date
        parse: (value) => {
          return value ? moment(value, 'YYYY').toDate() : null
        },
        //[optional] getWeekNumber
        getWeek: (date) => {
          return // a number
        }
      }
    }
  },
  components:{
    ApiService,
    ReportViewer,
    IjcoreModalListing,
    IjcoreDatePicker
  },
  computed: {},
  created() {},
  updated() {},
  mounted() {
    this.buildQuery();
    this.changePeriodType();

  },
  methods: {
    reloadReport() {
      this.$refs['report-viewer'].loadReport(this.filter);
    },
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
  watch: {}
}
</script>

<style lang="css">
.component-report-viewer .card, .component-report-viewer .card-body{
  height: 100%;
}
.gcv-menu {
  height: auto;
}
.component-report-viewer .mx-datepicker {
  width: 85px;
}
</style>

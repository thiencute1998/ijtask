<template>
  <div class="component-report-viewer">
    <div class="main-entry">
      <div class="main-header">
        <div class="main-header-padding">
          <b-row>
            <b-col class="col-md-12">
              <div class="main-header-item main-header-name">
                <span class="text-capitalize"><i class="fa fa-list mr-2"></i> Báo cáo</span>
              </div>
            </b-col>
            <b-col class="col-md-12">
              <div class="main-header-item main-header-icons">
                <b-button-group id="main-header-views" class="main-header-views">
                  <b-button id="tooltip-view-filter" @click="openReportPara" title="Lọc" class="main-header-view"><i class="fa fa-filter"></i></b-button>
                </b-button-group>
                <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
                  <sidebar-toggle class="d-md-down-none btn" display="lg" :defaultOpen=true />
                </div>
              </div>
            </b-col>
          </b-row>
          <report-para ref="report-para" :report-i-d="233" v-model="filter" @reload-report="reloadReport"></report-para>
        </div>
      </div>
      <div class="main-body">
        <b-card class="m-0 border-0" body-class="py-0 px-0">
          <div class="content-body" style="height: 100%">
            <div class="content-body-list" style="height: 100%">
              <report-viewer
                ref="report-viewer"
                report-folder-name="SBP/3422016TTBTC"
                report-name="SBP_342_2016_TT_BTC_01"
                :report-filter="filter"
                report-data-api="/report/api/SBP3422016TTBTC/MB01">
              </report-viewer>
            </div>
          </div>
        </b-card>

      </div>
    </div>
  </div>
</template>


<script>
import ApiService from '@/services/api.service';
import ReportViewer from "@/views/report/ReportViewer";
import ReportPara from "@/views/report/ReportPara";
import DatePicker from 'vue2-datepicker';
import IjcoreModalListing from "@/components/IjcoreModalListing";
import moment from "moment";

export default {
  name: 'listing-items',
  data() {
    return {
      filter: {},
      YearOptions: [],
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
    ReportViewer,
    DatePicker,
    IjcoreModalListing,
    ReportPara
  },
  computed: {},
  created() {},
  updated() {},
  mounted() {
    let self = this;
    let currentYear = new Date().getFullYear();
    this.YearOptions = [{value: null, text: '-- Chọn năm báo cáo --'}];

    for (let i = 5; i >= 1; i--) {
      let year = Number(currentYear) - i;
      let tmpObj = {};
      tmpObj.value = year;
      tmpObj.text = year;
      self.YearOptions.push(tmpObj);
    }
    this.YearOptions.push({value: currentYear, text: currentYear});
    for (let i = 1; i <= 5; i++) {
      let year = Number(currentYear) + i;
      let tmpObj = {};
      tmpObj.value = Number(year);
      tmpObj.text = year;
      self.YearOptions.push(tmpObj);
    }
  },
  methods: {
    reloadReport() {
      this.$refs['report-viewer'].loadReport(this.filter);
    },
    openReportPara(){
      this.$refs['report-para'].openModal();
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

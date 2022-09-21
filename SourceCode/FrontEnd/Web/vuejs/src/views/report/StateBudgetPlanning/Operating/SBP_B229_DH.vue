<template>
  <div class="component-report-viewer">
    <div class="main-entry">
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

          <b-collapse id="collapse-main-header-filter">
            <div class="main-header-filter pt-2">
              <div class="row mb-2">
                <div class="col-2 mb-2">
                  <!--                  <date-picker v-model="filter.Year" :formatter="momentFormat" format="YYYY" type="year" placeholder="Chọn năm báo cáo"></date-picker>-->
                  <b-form-select v-model="filter.Year" :options="[
                    { value: null, text: '-- Chọn năm báo cáo --'},
                    { value: '2016', text: '2016'},
                    { value: '2017', text: '2017'},
                    { value: '2018', text: '2018'},
                    { value: '2019', text: '2019'},
                    { value: '2020', text: '2020'},
                    { value: '2021', text: '2021'},
                    { value: '2022', text: '2022'},
                    { value: '2023', text: '2023'},
                    { value: '2024', text: '2024'},
                    { value: '2025', text: '2025'},
                    { value: '2026', text: '2026'},
                    { value: '2027', text: '2027'},
                    { value: '2028', text: '2028'},
                  ]"></b-form-select>
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
          </b-collapse>

        </div>
      </div>
      <div class="main-body">
        <b-card class="m-0 border-0" body-class="py-0 px-0">
          <div class="content-body" style="height: 100%">
            <div class="content-body-list" style="height: 100%">
              <report-viewer
                ref="report-viewer"
                report-folder-name="SBP/DH"
                report-name="SBP_B229_DH"
                :report-filter="filter"
                :report-data-api= Api>
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
import DatePicker from 'vue2-datepicker';
import IjcoreModalListing from "@/components/IjcoreModalListing";

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
      Api:"/report/api/StateBudgetPlanning/Operating/SBP_B229_DH?XDEBUG_SESSION_START=PHPSTORM",
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
    IjcoreModalListing
  },
  computed: {},
  created() {},
  updated() {},
  mounted() {},
  methods: {
    reloadReport() {
      this.$refs['report-viewer'].loadReport(this.filter);
    },
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

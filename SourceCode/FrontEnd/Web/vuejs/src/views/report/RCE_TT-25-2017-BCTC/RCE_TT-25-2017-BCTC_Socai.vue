<template>
  <div class="component-report-viewer-rce">
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
<!--                <div class="col-2 mb-2"></div>-->
                <div class="col-8 mb-2">
                  <ijcore-date-range v-model="filter.DateRange" :type-default="23"></ijcore-date-range>
                </div>
                <div class="col-4 mb-2">
                  <ijcore-modal-listing
                    v-model="filter" title="Đơn vị" api="/listing/api/common/list"
                    FieldName="CompanyName" FieldNo="CompanyNo" FieldID="CompanyID" table="company">
                  </ijcore-modal-listing>
                </div>
                <div class="col-4 mb-2">
                  <ijcore-modal-listing
                    v-model="filter" title="tài khoản" api="/listing/api/common/list"
                    FieldName="AccountName" FieldNo="AccountNo" FieldID="AccountID" table="coa_con" :FieldNoConfig="{show: true, tdStyle: {width: '15%'}}">
                  </ijcore-modal-listing>
                </div>
                <b-col>
                  <div class="main-action d-lg-flex justify-content-end">
                    <b-button variant="primary" size="md" @click="reloadReport">
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
                report-folder-name="RCE_TT-25-2017-BCTC"
                report-name="RCE_TT-25-2017-BCTC_Socai"
                :report-filter="filter"
                report-data-api="/report/api/RCETT252017BCTC/RCETT252017BCTCSocai?XDEBUG_SESSION_START=PHPSTORM">
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
  import ReportViewer from "../ReportViewer";
  import IjcoreDateRange from "../../../components/IjcoreDateRange";
  import IjcoreModalListing from "../../../components/IjcoreModalListing";

  export default {
    name: 'listing-items',
    data() {
      return {
        filter: {
          DateRange: null,
          CompanyID: null,
          CompanyName: '',
          CompanyNo: '',
          AccountName: '',
          AccountID: null,
          AccountNo: '',
        }
      }
    },
    components:{
      ReportViewer,
      IjcoreDateRange,
      IjcoreModalListing
    },
    computed: {},
    created(){
      if(this.$route.query){
        let  f = new Date(this.$route.query.fromDate.replaceAll('-','/'))
        let t = new Date(this.$route.query.toDate.replaceAll('-','/'))
        this.filter.AccountNo = this.$route.query.conAccountNo;
        this.filter.CompanyID = this.$route.query.companyID;
        this.filter.DateRange = {
          fromDate: "01/01/2020",
          toDate : "01/01/12"
        };
        this.reloadReport();
      }
    },
    updated() {},
    mounted() {},
    methods: {
      reloadReport() {
        this.$refs['report-viewer'].loadReport(this.filter);
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
</style>

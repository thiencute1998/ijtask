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
                <div class="col-6 mb-2"></div>
                <b-col>
                  <div class="main-action d-lg-flex justify-content-end">
                    <b-button variant="primary" size="md">
                      <i class="fa fa-search" @click="reloadReport"></i> Tìm
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
                report-folder-name="StateBudgetPlanning/UTH/SBP_UTH_VPSoNN"
                report-name="SBP_UTH_VPSoNN_02"
                :report-filter="filter"
                report-data-api="/report/api/StateBudgetPlanning/UTH/SBPUTHVPSoNN/SBPUTHVPSoNN02">
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
import ReportViewer from "../../../ReportViewer";

export default {
  name: 'listing-items',
  data() {
    return {
      filter: {}
    }
  },
  components:{
    ReportViewer
  },
  computed: {},
  created() {},
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

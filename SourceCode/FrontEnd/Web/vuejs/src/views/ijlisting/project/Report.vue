<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="fa fa-print mr-2"></i> Dự án </span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onBackToList"><i class="fa fa-ban"></i> Hủy</b-button>
            </div>
          </b-col>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-icons">
              <div class="main-header-collapse">
                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen="true" />
              </div>
            </div>
          </b-col>
        </b-row>
      </div>

    </div>
    <div class="main-body">
      <ijcore-report-viewer-listing
        ref="report-viewer"
        report-name="project"
        :filter="filter"
        @changeReport="updateReport"
        report-data-api="listing/api/project/get-report-data?XDEBUG_SESSION_START=PHPSTORM"></ijcore-report-viewer-listing>
    </div>
    <div class="main-footer">
      <div class="d-lg-flex justify-content-between align-items-center m-0">
        <div class="main-footer-pagination">
          <div class="overflow-auto">
            <b-pagination
              v-model="filter.currentPage"
              :total-rows="rows"
              :per-page="filter.perPage"
              aria-controls="my-table"
              size="md"
            ></b-pagination>
          </div>
        </div>
        <div class="total-item">
          <span style="font-weight: 500">Tổng số: {{filter.totalRows}}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import IjcoreReportViewerListing from "../../../components/IjcoreReportViewerListing";
  import ApiService from '@/services/api.service';

  export default {
    name: 'listing-project-view',
    data () {
      return {
        filter: (this.$route.query) ? this.$route.query : {}
      }
    },
    computed: {
      rows() {
        return this.filter.totalRows;
      },
    },
    props: {
      idParamsProps: {
        type: Number,
        default: 0
      },
      reqParamsProps: {
        type: Object,
        default: function () {
          return {}
        }
      },
    },

    components: {
      IjcoreReportViewerListing
    },
    beforeCreate() {},
    mounted() {
      this.filter.exportData = true;
    },
    updated() {},
    methods: {
      onBackToList() {
        let query = this.$route.query;
        this.$router.push({
          name: 'listing-project',
          query: query
        });
      },
      updateReport(data){
        this.filter.totalRows = data.total;
        this.filter.perPage = String(data.per_page);
        this.filter.currentPage = data.current_page;
      },
      reloadReport() {
        this.$refs['report-viewer'].loadReport(this.filter);
      },
    },
    watch: {
      'filter.currentPage'() {
        this.reloadReport();
      }
    }
  }
</script>
<style lang="css"></style>

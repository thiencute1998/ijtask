<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="fa fa-print mr-2"></i> Công cụ dụng cụ</span>
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
      <ijcore-report-viewer-listing report-name="invest" :filter="filter" report-data-api="listing/api/invest/get-report-data"></ijcore-report-viewer-listing>
    </div>
  </div>
</template>

<script>
  import IjcoreReportViewerListing from "../../../components/IjcoreReportViewerListing";
  import ApiService from '@/services/api.service';

  export default {
    name: 'listing-invest-view',
    data () {
      return {
        filter: (this.$route.query) ? this.$route.query : {}
      }
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
    computed: {},
    methods: {
      onBackToList() {
        let query = this.$route.query;
        this.$router.push({
          name: 'listing-invest',
          query: query
        });
      },
    },
    watch: {}
  }
</script>
<style lang="css"></style>

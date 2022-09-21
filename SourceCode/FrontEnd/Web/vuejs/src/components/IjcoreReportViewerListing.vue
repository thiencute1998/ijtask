<template>
  <div class="animated">
    <div id="viewer-host">
      <JSViewer ref="reportViewer"></JSViewer>
    </div>
  </div>
</template>
<style type="text/css" scoped>
  #viewer-host {
    width: 100%;
    height: 100%;
  }
  .gcv-menu {
    height: auto;
  }
  @import "~@grapecity/activereports/styles/ar-js-ui.css";
  @import "~@grapecity/activereports/styles/ar-js-viewer.css";
</style>
<script>
  import { Viewer } from "@grapecity/activereports-vue";
  import "@grapecity/activereports/pdfexport";
  import "@grapecity/activereports/htmlexport";
  import "@grapecity/activereports/xlsxexport";

  // import '@grapecity/activereports/styles/ar-js-ui.css';
  // import '@grapecity/activereports/styles/ar-js-viewer.css';
  import ApiService from "../services/api.service";

  export default {
    name: 'report-viewer',
    props: {
      reportName: [String],
      reportDataApi: [String],
      filter: [Object, Array]
    },
    created(){},
    data() {
      return {
      }
    },
    components:{
      JSViewer: Viewer,
    },
    mounted() {
      console.log(this.filter)
      this.loadReport(this.filter);
    },

    methods: {
      async loadReport(filter = {}){
        let report = await this.reportTemplate(this.reportName);
        let reportData = await this.reportData(filter);
        this.$emit('changeReport',reportData);
        report.DataSources[0].ConnectionProperties.ConnectString = "jsondata=" + JSON.stringify(reportData);
        const viewer = this.$refs.reportViewer.Viewer();
        viewer.open(report);
      },
      async reportTemplate(name) {
        const requestData = {
          method: 'post',
          url: "listing/api/common/get-report-template",
          data: {
            name: name
          }
        };
        try {
          let response = await ApiService.customRequest(requestData);
          let responseData = response.data;
          if (responseData.status === 1) {
            return JSON.parse(responseData.data);
          }else if (responseData.status === 2) {
            this.$bvToast.toast(responseData.msg, {
              title: 'Thông báo',
              variant: 'warning',
              solid: true
            });
          }
        }catch (e) {
          console.log('error load report template');
          console.log(e);
        }

      },
      async reportData(filter) {
        const requestData = {
          method: 'post',
          url: this.reportDataApi,
          data: {
            exportData: filter.exportData,
            per_page: filter.perPage,
            page: filter.currentPage,
            filter: filter
          }
        };
        this.$store.commit('isLoading', true);
        try {
          const response = await ApiService.customRequest(requestData);
          if (response.data.status) {
            this.$store.commit('isLoading', false);
            return response.data.data;
          }
        }catch (e) {
          console.log('error load report data');
          console.log(e);
          this.$store.commit('isLoading', false);
        }
      }
    },
    watch: {
      model: {
        handler(val){
          // do stuff
        },
        deep: true
      }
    }
  }
</script>

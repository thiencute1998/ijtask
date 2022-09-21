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
  import ApiService from "../../services/api.service";

  export default {
    name: 'report-viewer',
    props: {
      reportFolderName: [String],
      reportName: [String],
      reportDataApi: [String]
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
      this.loadReport();
    },

    methods: {
      async loadReport(filter = {}){
        this.$store.commit('isLoading', true);
        let report = await this.reportTemplate();
        let reportData = await this.reportData(filter);
        this.$emit('changeReport',reportData);
        report.DataSources[0].ConnectionProperties.ConnectString = "jsondata=" + JSON.stringify(reportData);
        const viewer = this.$refs.reportViewer.Viewer();
        viewer.open(report);
        this.$store.commit('isLoading', false);
      },
      async reportTemplate() {
        const requestData = {
          method: 'post',
          url: "report/api/common/get-report-template",
          data: {
            reportFolderName: this.reportFolderName,
            reportName: this.reportName
          }
        };
        try {
          const response = await ApiService.customRequest(requestData);
          if (response.data.status) {
            return JSON.parse(response.data.data);
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
            filter: filter,
            per_page: filter.perPage,
            page: filter.currentPage
          }
        };
        try {
          const response = await ApiService.customRequest(requestData);
          if (response.data.status) {
            return response.data.data;
          } else {
            this.$bvToast.toast(response.data.msg, {
              title: 'Thông báo',
              variant: 'warning',
              solid: true
            });
            return response.data.data;
          }
        }catch (e) {
          console.log('error load report data');
          console.log(e);
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

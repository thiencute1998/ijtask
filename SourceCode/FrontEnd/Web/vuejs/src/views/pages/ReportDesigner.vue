<template>
  <div class="animated">

    <div id="designer-toolbar" class="container-fluid">
      <div class="row">
        <button type="button" class="btn btn-secondary btn-sm col-sm-2 ml-1" v-on:click="onDesignerOpen()" :style="{ display: designerHidden ? 'block' : 'none' }">Open Designer</button>
      </div>
    </div>

    <div id="designer-host" :style="{ display: designerHidden ? 'none' : 'block' }"></div>
    <div id="viewer-host" :style="{ display: designerHidden ? 'block' : 'none' }"><ReportViewer ref="reportViewer" /></div>
    <input type="file" v-on:change="onChangeFile" id="myFile" ref="file" class="form-control hidden">
  </div>
</template>
<style type="text/css" scoped>
  #designer-host, #viewer-host {
    width: 100%;
    height: 100vh;
  }
  .gcv-menu {
    height: auto;
  }
  @import "~@grapecity/activereports/styles/ar-js-ui.css";
  @import "~@grapecity/activereports/styles/ar-js-designer.css";
  @import "~@grapecity/activereports/styles/ar-js-viewer.css";
</style>
<script>
  import {
    Designer as ReportDesigner,
    templates,
  } from "@grapecity/activereports/reportdesigner";
  import { Viewer } from "@grapecity/activereports-vue";
  import { PageReport } from "@grapecity/activereports/core";
  import { exportDocument as PdfExport } from "@grapecity/activereports/pdfexport";

  // import '@grapecity/activereports/styles/ar-js-ui.css';
  // import '@grapecity/activereports/styles/ar-js-designer.css';
  // import '@grapecity/activereports/styles/ar-js-viewer.css';

  let resolveFunc = null;
  let designer = null;
  export default {
    name: 'report-designer',
    data() {
      return {
        reportStorage: new Map(),
        counter: 0,
        designerHidden: false,
        designerName: ''
      };
    },
    computed: {
      reportIds() {
        const ret = this.counter ? [...this.reportStorage.keys()] : [];
        return ret;
      },
    },
    components: {
      ReportViewer: Viewer,
    },
    mounted() {
      let self = this;
      designer = new ReportDesigner("#designer-host");
      designer.setActionHandlers({
        onCreate: () => {
          const reportId = `NewReport${this.counter + 1}`;
          this.counter++;
          return Promise.resolve({
            definition: templates.CPL,
            id: reportId,
            displayName: reportId,
          });
        },
        onRender: async (report) => {
          this.designerHidden = true;
          this.$refs.reportViewer.Viewer().open(report.definition);
        },
        onOpen: function () {
          self.$refs.file.click();
        },
        onSave: (info) => {
          const reportId = info.id || `NewReport${this.counter + 1}`;
          this.reportStorage.set(reportId, info.definition);
          this.counter++;
          return Promise.resolve({ displayName: reportId });
        },
        onSaveAs: (info) => {
          const reportId = `NewReport${this.counter + 1}`;
          this.reportStorage.set(reportId, info.definition);
          this.counter++;
          let fileName = (self.designerName) ? self.designerName : 'report';
          self.downloadObjectAsJson(info.definition, fileName);
          return Promise.resolve({ id: reportId, displayName: reportId });
        },
      });
    },
    methods: {
      onSelectReport(reportId) {
        if (resolveFunc) {
          $("#dlgOpen").modal("hide");
          resolveFunc({
            definition: this.reportStorage.get(reportId),
            id: reportId,
            displayName: reportId,
          });
          resolveFunc = null;
        }
      },

      // Function to download data to a file
      saveFile(data, filename, type) {
        let file = new Blob([data], {type: type});
        if (window.navigator.msSaveOrOpenBlob) // IE10+
          window.navigator.msSaveOrOpenBlob(file, filename);
        else { // Others
          let a = document.createElement("a"),
            url = URL.createObjectURL(file);
          a.href = url;
          a.download = filename;
          document.body.appendChild(a);
          a.click();
          setTimeout(function() {
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
          }, 0);
        }
      },
      onDesignerOpen() {
        this.designerHidden = false;
      },
      downloadObjectAsJson(exportObj, exportName){
        let dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(exportObj));
        let downloadAnchorNode = document.createElement('a');
        downloadAnchorNode.setAttribute("href",     dataStr);
        downloadAnchorNode.setAttribute("download", exportName + ".rdlx-json");
        document.body.appendChild(downloadAnchorNode); // required for firefox
        downloadAnchorNode.click();
        downloadAnchorNode.remove();
      },
      onChangeFile(e){
        let file = e.target.files[0];

        let fileName = file.name;
        let pieces = fileName.split('.');
        this.designerName = pieces[0];

        if (!file) return;
        let reader = new FileReader();
        reader.onload = function(e) {
          let contents = e.target.result;
          contents = JSON.parse(contents);
          // Display file content
          designer.setReport({definition: contents});
        };
        reader.readAsText(file);
      }
    }

  }
</script>

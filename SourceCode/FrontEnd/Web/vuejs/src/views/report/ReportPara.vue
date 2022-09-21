<template>
  <div class="animated">
    <b-modal ref="modal-report-para" id="modal-report-para" size="md" title="Tham số">

      <div class="table-responsive b-table-sticky-header b-table-sticky-custom m-0" style="max-height: none">
        <table class="table b-table table-sm table-bordered table-tree table-editable">
          <thead>
          <th style="width: 50%">Tham số</th>
          <th>Giá trị</th>
          </thead>
          <tbody>
          <tr v-for="(para, key) in paras">
            <td>
              <span class="px-2">{{para.ParaName}}</span>
            </td>
            <td>
              <report-para-value v-model="paras[key]" :paras="paras"></report-para-value>
            </td>
          </tr>
          </tbody>
        </table>
      </div>

      <template v-slot:modal-footer="{ ok, cancel, hide}">
        <b-button variant="primary" @click="reloadReport">
          Đồng ý
        </b-button>
      </template>
    </b-modal>
  </div>
</template>
<style type="text/css">
</style>
<script>
  import ApiService from "../../services/api.service";
  import IjcoreModalListing from "../../components/IjcoreModalListing";
  import ReportParaValue from "./ReportParaValue";
  import Dexie from "dexie";

  export default {
    name: 'report-para',
    props: {
      value: [Object, Array],
      ReportID: [Number, String]
    },
    created(){},
    data() {
      return {
        paras: []
      }
    },
    components:{
      IjcoreModalListing,
      ReportParaValue
    },
    mounted() {},

    methods: {
      async loadPara() {
        const db = new Dexie('Report');
        await db.open().then(() => {});
        this.paras = await db.table('rpt_report_para').where({ReportID: this.ReportID}).toArray();
      },
      async openModal(){
        if (!this.paras.length) {
          await this.loadPara();
        }
        this.$bvModal.show('modal-report-para');
      },
      reloadReport(){
        let self = this;
        // render filter
        _.forEach(this.paras, function (para, key) {
          if (para.TableID) {
            let TableID = para.TableID;
            let TableNo = para.TableNo;
            let TableName = para.TableName;
            if (para.ListingName) {
              TableID = para.ListingName + para.TableID;
              TableNo = para.ListingName + para.TableNo;
              TableName = para.ListingName + para.TableName;
            }
            self.value[TableID] = (para.ParaValueID) ? para.ParaValueID : para.ParaValue;
            if (TableNo) {
              self.value[TableNo] = para.ParaValueNo;
            }
            if (TableName) {
              self.value[TableName] = para.ParaValueName;
            }
          }
          if (para.FromDate) {
            self.value.FromDate = para.FromDate;
          }
          if (para.ToDate) {
            self.value.ToDate = para.ToDate;
          }
        });
        this.$emit('reload-report');
        this.$bvModal.hide('modal-report-para');
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

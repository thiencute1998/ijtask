<template>
  <div class="component-report-list">
    <div class="main-entry">
      <div class="main-header">
        <div class="main-header-padding">
          <b-row class="mb-2">
            <b-col class="col-md-12">
              <div class="main-header-item main-header-name">
                <span class="text-capitalize"><i class="fa fa-list mr-2"></i> Báo cáo</span>
              </div>
            </b-col>
            <b-col class="col-md-12"></b-col>
          </b-row>
          <b-row class="mb-2">
            <b-col class="col-lg-12 col-md-24 col-sm-24 col-24 mb-2 mb-lg-0 d-lg-flex justify-content-start align-items-center">
              <div class="main-header-item main-header-actions">
              </div>
              <div class="main-header-item main-header-search" style="flex: 1 1 auto">
                <div class="search-input">
                  <input v-model="ReportName" @keyup.enter="searchReport" type="text" placeholder="Tìm kiếm..."/>
                </div>
                <span class="search-icon" @click="searchReport"><i class="fa fa-search"></i></span>
              </div>
            </b-col>
            <b-col class="col-lg-12 col-md-24 col-sm-24 col-24">
              <div class="main-header-item main-header-icons">
                <b-button-group id="main-header-views" class="main-header-views">
                  <b-button id="tooltip-view-filter" v-b-toggle.collapse-main-header-filter title="Lọc" class="main-header-view"><i class="fa fa-filter"></i></b-button>
                </b-button-group>
                <b-dropdown id="dropdown-inactive" title="Trạng thái" menu-class="p-0" class="app-dropdown-center main-header-icon">
                  <template v-slot:button-content>
                    <!--                              <i class="fa fa-random m-0" v-if="stage.filterInactive === 2"></i>-->
                    <svg v-if="stage.filterInactive === 2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M18.6 6.62c-1.44 0-2.8.56-3.77 1.53L12 10.66 10.48 12h.01L7.8 14.39c-.64.64-1.49.99-2.4.99-1.87 0-3.39-1.51-3.39-3.38S3.53 8.62 5.4 8.62c.91 0 1.76.35 2.44 1.03l1.13 1 1.51-1.34L9.22 8.2C8.2 7.18 6.84 6.62 5.4 6.62 2.42 6.62 0 9.04 0 12s2.42 5.38 5.4 5.38c1.44 0 2.8-.56 3.77-1.53l2.83-2.5.01.01L13.52 12h-.01l2.69-2.39c.64-.64 1.49-.99 2.4-.99 1.87 0 3.39 1.51 3.39 3.38s-1.52 3.38-3.39 3.38c-.9 0-1.76-.35-2.44-1.03l-1.14-1.01-1.51 1.34 1.27 1.12c1.02 1.01 2.37 1.57 3.82 1.57 2.98 0 5.4-2.41 5.4-5.38s-2.42-5.37-5.4-5.37z"/><path fill="none" d="M0 0h24v24H0V0z"/></svg>
                    <i class="fa fa-circle-o m-0" v-if="stage.filterInactive === 0"></i>
                    <i class="fa fa-ban m-0" v-if="stage.filterInactive === 1"></i>
                  </template>
                  <b-dropdown-item @click="$_lists_handleFilterInactive(2)" title="Trạng thái">
                    <!--                              <i class="fa fa-random m-0"></i>-->
                    <svg xmlns="http://www.w3.org/2000/svg" style="fill: #73818f" width="16" height="16" viewBox="0 0 24 24"><path d="M18.6 6.62c-1.44 0-2.8.56-3.77 1.53L12 10.66 10.48 12h.01L7.8 14.39c-.64.64-1.49.99-2.4.99-1.87 0-3.39-1.51-3.39-3.38S3.53 8.62 5.4 8.62c.91 0 1.76.35 2.44 1.03l1.13 1 1.51-1.34L9.22 8.2C8.2 7.18 6.84 6.62 5.4 6.62 2.42 6.62 0 9.04 0 12s2.42 5.38 5.4 5.38c1.44 0 2.8-.56 3.77-1.53l2.83-2.5.01.01L13.52 12h-.01l2.69-2.39c.64-.64 1.49-.99 2.4-.99 1.87 0 3.39 1.51 3.39 3.38s-1.52 3.38-3.39 3.38c-.9 0-1.76-.35-2.44-1.03l-1.14-1.01-1.51 1.34 1.27 1.12c1.02 1.01 2.37 1.57 3.82 1.57 2.98 0 5.4-2.41 5.4-5.38s-2.42-5.37-5.4-5.37z"/><path fill="none" d="M0 0h24v24H0V0z"/></svg>
                  </b-dropdown-item>
                  <b-dropdown-item @click="$_lists_handleFilterInactive(0)" title="Đang hoạt động"><i class="fa fa-circle-o m-0"></i></b-dropdown-item>
                  <b-dropdown-item @click="$_lists_handleFilterInactive(1)" title="Ngừng hoạt động"><i class="fa fa-ban m-0"></i></b-dropdown-item>
                </b-dropdown>
                <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
                  <sidebar-toggle class="d-md-down-none btn" display="lg" :defaultOpen=true />
                </div>
              </div>
            </b-col>
          </b-row>
        </div>
      </div>
      <div class="main-body main-body-view-action">
        <div class="container-fluid h-100">
          <div class="d-flex flex-row h-100">
            <div class="table-responsive b-table-sticky-header b-table-sticky-custom m-0" style="max-height: none; flex: 0 0 60%">
              <table class="table b-table table-hover table-sm table-tree b-table-selectable b-table-select-multi">
                <thead>
                </thead>
                <tbody>
                <tr @click="selectedRow($event, tableItem)" :id="'table-item-' + tableItem.ReportID" v-show="tableItem.Show"  v-for="(tableItem, key) in reports">
                  <td class="bg-tree-tr pl-0 b-table-sticky-column" v-if="!List">
                    <span class="bg-tree-dot" :style="{'left': (level * 12) + 'px'}" v-for="level in reports[key].Level" ></span>
                    <div class="bg-tree-content bg-tree-td"
                         :style="{'margin-left': (tableItem.Level * 12 - 12) + 'px'}">
                      <span class="linkHover" style="padding-left: 20px "  :title="reports[key].ReportName" v-if="!reports[key].HaveChildren" >{{reports[key].ReportName}}</span>
                      <span style="padding-left: 20px" :title="reports[key].ReportName" v-if="reports[key].HaveChildren">{{reports[key].ReportName}}</span>
                      <i class="fa fa-plus-square-o bg-tree-icon-action" v-if="reports[key].HaveChildren" @click="onToggleChildNodes($event, tableItem)"></i>
                    </div>
                  </td>
                  <td v-if="List" >
                    <span style="margin-left: 20px; cursor: pointer"> {{reports[key].ReportName}}</span>
                  </td>
                </tr>

                </tbody>
              </table>
            </div>
            <div class="d-flex flex-column justify-content-between w-100 h-100">
              <div class="table-responsive b-table-sticky-header b-table-sticky-custom m-0" style="max-height: none">
                <table class="table b-table table-sm table-bordered table-tree table-editable">
                  <thead>
                  <th>Tham số</th>
                  <th>Giá trị</th>
                  </thead>
                  <tbody>
                  <tr v-for="(para, key) in rptReportPara">
                    <td>
                      <span class="px-2" v-if="para.ParaKey !== 'PERIODTYPE'">{{para.ParaName}}</span>
                      <b-form-select
                        v-else
                        v-model="rptReportPara[key].ParaValue"
                        :options="PeriodOption" @change="changePeriodOption(key)">
                      </b-form-select>
                    </td>
                    <td><report-para-value v-model="rptReportPara[key]" :paras="rptReportPara"></report-para-value></td>
                  </tr>
                  </tbody>
                </table>
              </div>
              <div class="para-action ml-auto mt-2">
                <b-button variant="primary mr-2" @click="updatePara">Cập nhật</b-button>
                <b-button variant="primary" @click="savePara">Lưu</b-button>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="main-footer">
        <div class="d-flex flex-wrap justify-content-between align-items-center m-0">
          <div class="main-footer-pagination">
            <div class="overflow-auto">
            </div>
          </div>
          <div>
            <b-dropdown dropup class="m-2"  variant="primary">
              <template #button-content>
                <span @click="viewReport">Xem</span>
              </template>
              <b-dropdown-item @click="viewReport">Báo cáo</b-dropdown-item>
              <b-dropdown-item href="#">Excel</b-dropdown-item>
              <b-dropdown-item href="#">In</b-dropdown-item>
              <b-dropdown-item href="#">Pdf</b-dropdown-item>
            </b-dropdown>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ApiService from '@/services/api.service';
import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
import 'v-calendar/lib/v-calendar.min.css';
import mixinLists from '@/mixins/lists';
import Select2 from "v-select2-component";
import ReportParaValue from "@/views/report/ReportParaValue";
import Dexie from "dexie";

export default {
  name: 'report-items',
  mixins: [mixinLists],
  data() {
    return {
      ReportName:'',
      List: false,
      filter: {
        DateRange: (this.$route.query && this.$route.query.DateRange) ? this.$route.query.DateRange : null,
      },
      reports: [],
      reportSelected: {},
      rptReportPara: [],
      rptPataOption: [],
      PeriodID: null,
      PeriodOption: [
        {value: null, text: 'Chọn chu kỳ'},
        {value: 1, text: 'Năm'},
        {value: 2, text: 'Quý'},
        {value: 3, text: 'Tháng'},
        {value: 4, text: 'Tuần'},
        {value: 5, text: 'Ngày'},
        {value: 6, text: '6 tháng'},
        {value: 7, text: '9 tháng'},
        {value: 8, text: '3 năm'},
        {value: 9, text: '5 năm'},
        {value: 10, text: '10 năm'}
      ],
    }
  },
  components: {
    Select2,
    ReportParaValue
  },
  computed: {},
  created() {

    this.settings.FieldID = 'ReportID';
    this.settings.Table = 'sys_report';
    this.settings.FieldInactive = 'Inactive';

    this.settings.ListApi = 'sysadmin/api/report';
    this.stage.viewType = 'tree';

    this.modelSearch = {

      ReportName: (this.$route.query && this.$route.query.ReportName) ? this.$route.query.ReportName : '',

    };
    this.fetchData();
  },
  updated() {
    this.stage.updatedData = true;
  },
  mounted() {
    this.$_lists_showMessage();
  },

  methods: {
    fetchData(){
      let self = this;
      let urlApi = this.settings.ListApi;
      let requestData = {
        method: 'post',
        url: urlApi,
        data: {
          per_page: 50,
          page: this.currentPage,
          viewType: this.stage.viewType,
        },

      };

      if (self.fullTextSearch !== '') {
        requestData.data.fullTextSearch = self.fullTextSearch;
      }

      if (self.ReportName !== ''){
        requestData.data.ReportName = self.ReportName;
        self.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((response) => {
          let responseData = response.data;
          if(responseData.status === 1) {
            _.forEach(responseData.data.data, function (val, key){
              val.Show = true;
              self.reports.push(val);
            });
            self.rptPataOption = [];
            if (responseData.data.RptPara) {
              _.forEach(responseData.data.RptPara, function (para, key) {
                para.id = para.ParaID;
                para.text = para.ParaName;
                self.rptPataOption.push(para);
              });
            }
          }

          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });

      } else {
        this.$store.commit('isLoading', true);
        self.List = false;
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((response) => {
          let responseData = response.data;
          if(responseData.status === 1) {
            _.forEach(responseData.data.data, function (val, key){
              if(val.Level===1){
                val.Show = true;
              } else {
                val.Show = false;
              }
              val.List = false;
              val.HaveChildren = (val.Detail) ? false : true;
              self.reports.push(val);
            });

            self.rptPataOption = [];
            if (responseData.data.RptPara) {
              _.forEach(responseData.data.RptPara, function (para, key) {
                para.id = para.ParaID;
                para.text = para.ParaName;
                self.rptPataOption.push(para);
              });
            }
          }

          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });

        // scroll to top perfect scroll

      }
      const container = document.querySelector('.b-table-sticky-header');
      if (container) container.scrollTop = 0;
    },
    onToggleChildNodes(e, itemParent) {
      let self = this;
      if (e && e.target.classList.contains('fa-minus-square-o')) {
        // close children
        e.target.classList.remove('fa-minus-square-o');
        e.target.classList.add('fa-plus-square-o');
        let allChildTableItem = this.getAllChildTableItem(itemParent, self.reports);
        if (allChildTableItem && allChildTableItem.length) {
          _.forEach(allChildTableItem, function (childTableItem, key) {
            let childTableItemID = childTableItem.ReportID;
            let indexItem = _.findIndex(self.reports, function (item){
              return item.ReportID == childTableItemID;
            });
            if (indexItem > -1) {
              self.reports[indexItem].Show = false;
            }
          });

        }
      } else {
        // open children
        e.target.classList.remove('fa-plus-square-o');
        e.target.classList.add('fa-minus-square-o');
        let allChildren = _.filter(this.reports, ['ParentID', itemParent.ReportID]);
        allChildren =  _.orderBy(allChildren, ['ReportID'], ['desc']);
        if (allChildren.length) {
          _.forEach(allChildren, function (childTableItem, key) {
            let indexItem = _.findIndex(self.reports, ['ReportID', childTableItem.ReportID]);
            self.reports.splice(indexItem, 1);
            let keyParent = _.findIndex(self.reports,  ['ReportID', itemParent.ReportID]);
            if (indexItem > -1 && keyParent > -1) {
              childTableItem.Show = true;
              self.reports = __.insertBeforeKey(self.reports, keyParent + 1, childTableItem);
              $('#table-item-' + self.reports[indexItem].ReportID + ' .bg-tree-icon-action').removeClass('fa-minus-square-o');
              $('#table-item-' + self.reports[indexItem].ReportID + ' .bg-tree-icon-action').addClass('fa-plus-square-o');
            }
          });
        }
      }
    },
    getAllChildTableItem(item, tableItemArr){
      let self = this, listChild = [];
      let allChild = _.filter(tableItemArr, ['ParentID', item.ReportID]);
      if (allChild.length) {
        allChild = _.orderBy(allChild, ['ReportID'], ['asc']);
        _.forEach(allChild, function (value, key) {
          listChild.push(value);
          if (_.filter(tableItemArr, ['ParentID', value.ReportID]).length) {
            let recursiveArr = self.getAllChildTableItem(value, tableItemArr);
            recursiveArr = _.orderBy(recursiveArr, ['ReportID', 'asc']);
            _.forEach(recursiveArr, function (recursive, key) {
              listChild.push(recursive);
            });
          }

        });
      }
      return listChild;
    },
    onViewOnTableKeyResult(TableItemID) {
      this.TableItemIDCurrent = TableItemID;
      this.$refs['ModalViewKeyResult'].show();
    },
    onViewOnTableFeedback(TableItemID) {
      this.TableItemIDCurrent = TableItemID;
      this.$refs['ModalViewFeedback'].show();
    },
    onHideModalViewKeyResult(){
      this.$refs['ModalViewKeyResult'].hide();
    },
    onHideModalViewFeedback(){
      this.$refs['ModalViewFeedback'].hide();
    },
    async selectedRow(e, report){
      let self = this;
      this.reportSelected = report;
      $('.b-table-selectable tr').removeClass('b-table-row-selected table-active');
      $('#table-item-' + report.ReportID).addClass('b-table-row-selected table-active');

      this.rptReportPara = [];
      if (!report.ReportID ||!report.Detail) {
        return;
      }

      let db = new Dexie("Report");
      if (db) {
        await db.open().then((db) => {
        }).catch('NoSuchDatabaseError', function (e) {
          // Database with that name did not exist
          console.log('Database not found');
        }).catch(function (e) {
          console.log("Oh uh: " + e);
        });
        if (db._allTables && db._allTables['rpt_report_para']) {
          let paras = await db.table('rpt_report_para').count();
          if (paras) {
            this.rptReportPara = await db.table('rpt_report_para').where({ReportID: report.ReportID}).toArray();
            _.forEach(this.rptReportPara, function (para, key) {
              if (!para.ParaValue) {
                self.$set(self.rptReportPara[key], 'ParaValue', null);
                self.$set(self.rptReportPara[key], 'ParaValueID', null);
                self.$set(self.rptReportPara[key], 'ParaValueName', '');
                self.$set(self.rptReportPara[key], 'ParaValueNo', '');
              }
            });
          }else {
            this.getReportPara(report);
          }
        }
      }
    },
    viewReport() {
      if (!this.reportSelected) {
        this.$bvToast.toast('Bạn chưa chọn báo cáo', {
          title: 'Thông báo',
          variant: 'warning',
          solid: true
        });
        return;
      }
      if(this.reportSelected.ReportLink != null){
        this.$router.push({
          path: this.reportSelected.ReportLink,
        });
      } else {
        this.$bvToast.toast('Đang cập nhật ...', {
          title: 'Thông báo',
          variant: 'warning',
          solid: true
        });
      }
    },
    getReportPara(report){
      let self = this;
      let requestData = {
        method: 'get',
        url: 'sysadmin/api/report/get-report-para',
        data: {},
      };

      ApiService.setHeader();
      ApiService.customRequest(requestData).then(async (response) => {
        let responseData = response.data;
        if(responseData.status === 1) {
          if (responseData.data.length) {
            const db = new Dexie('Report');
            await db.open().then(() => {});
            await db.table('rpt_report_para').clear();
            self.rptReportPara = [];
            await db.transaction('rw', 'rpt_report_para', async() => {
              _.forEach(responseData.data, async function (value, key) {
                // Make sure we have something in DB:
                if ((await db.table('rpt_report_para').where({LineID: value.LineID}).count()) === 0) {
                  let tmpObj = {};
                  tmpObj.LineID = value.LineID;
                  tmpObj.ReportID = value.ReportID;
                  tmpObj.ReportName = value.ReportName;
                  tmpObj.ParaID = value.ParaID;
                  tmpObj.ParaKey = value.ParaKey;
                  tmpObj.ParaName = value.ParaName;
                  tmpObj.NOrder = value.NOrder;
                  await db.table('rpt_report_para').add(tmpObj).catch(function (e) {
                    console.log("Error: " + (e.stack || e));
                  });
                }
                if (value.ReportID === report.ReportID) {
                  value.ParaValue = null;
                  value.ParaValueName = ''
                  value.ParaValueNo = '';
                  value.ParaValueID = null;
                  self.rptReportPara.push(value);
                }
              });

            }).then(() => {}).catch(e => {
              console.log(e.stack || e);
            });

          }
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });

    },
    async savePara() {
      if (this.rptReportPara && this.rptReportPara.length) {
        const db = new Dexie('Report');
        await db.open().then(() => {});
        if (db.table('rpt_report_para')) {
          _.forEach(this.rptReportPara, function (para, key) {
            db.table('rpt_report_para').where({LineID: para.LineID}).modify((value) => {
              value.ParaValue = para.ParaValue;
              value.ParaValueNo = para.ParaValueNo;
              value.ParaValueID = para.ParaValueID;
              value.ParaValueName = para.ParaValueName;
              if (value.ParaKey === 'PERIODTYPE') {
                value.ParaName = para.ParaName;
              }
            });
          });
          this.$bvToast.toast('Cập nhật thành công', {
            title: 'Thông báo',
            variant: 'success',
            solid: true
          });
        }
      }
    },
    updatePara(){
      let self = this;
      let requestData = {
        method: 'get',
        url: 'sysadmin/api/report/get-report-para',
        data: {},
      };

      let report = this.reportSelected;

      ApiService.setHeader();
      self.$store.commit('isLoading', true);
      ApiService.customRequest(requestData).then(async (response) => {
        let responseData = response.data;
        if(responseData.status === 1) {
          if (responseData.data.length) {
            const db = new Dexie('Report');
            await db.open().then(() => {});
            self.rptReportPara = [];
            await db.transaction('rw', 'rpt_report_para', async() => {
              _.forEach(responseData.data, async function (para, key) {
                let paraClient = await db.table('rpt_report_para').get({LineID: para.LineID});
                if (paraClient) {
                  if (para.ParaID !== paraClient.ParaID) {
                    db.table('rpt_report_para').where({LineID: para.LineID}).modify((value) => {
                      value.ParaID = para.ParaID;
                      value.ParaName = para.ParaName;
                      value.ParaValue = null;
                      value.ParaValueID = null;
                      value.ParaValueNo = '';
                      value.ParaValueName = '';
                      value.ParaValueNo = '';
                      value.ParaKey = para.ParaKey;
                      value.ReportID = para.ReportID;
                      value.ReportName = para.ReportName;
                      value.NOrder = para.NOrder;
                    });
                  }
                } else {
                    let tmpObj = {};
                    tmpObj.LineID = para.LineID;
                    tmpObj.ReportID = para.ReportID;
                    tmpObj.ReportName = para.ReportName;
                    tmpObj.ParaID = para.ParaID;
                    tmpObj.ParaKey = para.ParaKey;
                    tmpObj.ParaName = para.ParaName;
                    tmpObj.NOrder = para.NOrder;
                    await db.table('rpt_report_para').add(tmpObj).catch(function (e) {
                      console.log("Error: " + (e.stack || e));
                    });
                }

                if (para.ReportID === report.ReportID) {
                  para.ParaValue = (paraClient) ? paraClient.ParaValue : null;
                  para.ParaValueName = (paraClient) ? paraClient.ParaValueName : null;
                  para.ParaValueNo = (paraClient) ? paraClient.ParaValueNo : null;
                  para.ParaValueID = (paraClient) ? paraClient.ParaValueID : null;
                  self.rptReportPara.push(para);
                }
              });

            }).then(() => {}).catch(e => {
              console.log(e.stack || e);
            });

            this.$bvToast.toast('Cập nhật thành công', {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });
          }
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });
    },
    searchReport(){
        this.reports = [];
        this.fetchData();
    },
    handleExportPrint() {
      // Todo: handle export print
      alert('print');
    },
    autoCorrectedDatePipe: () => { return createAutoCorrectedDatePipe('dd/mm/yyyy') },
    scrollHandle(evt){},
    handleSubmitSearch(){
      this.currentPage = 1;
      this.stage.viewType = 'list';
      this.fetchData();
    },
    changePeriodOption(key) {
      let periodObj = _.find(this.PeriodOption, ['value', Number(this.rptReportPara[key].ParaValue)]);
      if (periodObj) {
        this.rptReportPara[key].ParaName = periodObj.text;
      } else {
        this.rptReportPara[key].ParaName = 'Kỳ báo cáo';
      }
    }

  },
  watch: {
    currentPage() {
      this.fetchData();
    },
  }
}
</script>

<style lang="css">
.table.b-table thead th {
  vertical-align: middle;
}
.bg-tree-tr{
  position: relative;
}
.bg-tree-content {
  display: flex;
  align-items: center;
  position: relative;
}
.bg-tree-content input {
  padding-left: 20px;
}
.bg-tree-td:before{
  display: inline-block;
  content: "";
  position: relative;
  top: 0;
  left: 13px;
  width: 16px;
  height: 0;
  border-top: 1px dotted #858585;
  z-index: 1;
}
.bg-tree-td:after{
  display: inline-block;
  content: "";
  position: absolute;
  top: 0;
  left: 12px;
  width: 1px;
  height: 100%;
  /*border-left: 1px dotted #858585;*/
  z-index: 2;
}
.bg-tree-dot {
  position: absolute;
  width: 1px;
  top: -2px;
  height: 100%;
  left: 12px;
}
.bg-tree-dot:before {
  display: inline-block;
  content: "";
  left: 0;
  width: 1px;
  height: 100%;
  border-left: 1px dotted #858585;
  z-index: 1;
}
.bg-tree-icon-action {
  position: absolute;
  left: 7px;
  background: #fff;
  z-index: 2;
  cursor: pointer;
}
.linkHover:hover, .linkHover:active{
  cursor: pointer;
}
</style>


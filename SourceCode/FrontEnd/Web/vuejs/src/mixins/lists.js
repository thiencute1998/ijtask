import ApiService from '@/services/api.service';
import Swal from 'sweetalert2';
import 'sweetalert2/src/sweetalert2.scss';
import {Core, PdfExport, XlsxExport, HtmlExport} from "@grapecity/activereports";
export default {
  data(){
    return {
      perPage: (this.$store.state.optionBehavior.perPage) ? this.$store.state.optionBehavior.perPage : null,
      currentPage: (this.$route.params && this.$route.params.currentPage) ? this.$route.params.currentPage : 1,
      itemsArray: [],
      totalRows: null,
      typeShow: 1,
      selected: [],

      modelSearch:{},
      fullTextSearch: '',

      paramsReqRouter: {
        idsArray: [],
        search: {},
        total: this.totalRows,
        perPage: this.perPage,
        currentPage: this.currentPage,
        lastPage: null,
        from: null,
        to: null,
        itemsArray: []
      },
      queryReqRouter: {},

      idsSelected: [],

      settings: {
        FieldID: '',
        Table: '',
        FieldInactive: 'Inactive',

        ListApi: '',
        DeleteApi: '',
        CreateRouter: '',
        EditRouter: '',
        ViewRouter: '',

        propsTable: {
          id: '',
          primaryKey: '',
          striped: true,
          bordered: true,
          borderless: false,
          outlined: false,
          dark: false,
          hover: true,
          small: true,
          fixed: true,
          responsive: true,
          stickyHeader: true,
          captionTop: false,
          tableVariant: '',
          tableClass: '',
          stacked: '',
          headVariant: '',
          threadClass: '',
          threadTrClass: {},
          footClone: false,
          footVariant: '',
          tfootClass: {},
          tfootTrClass: {},
          tbodyTrClass: {},
          tbodyClass: {},
          tbodyTransitionProps: {},
          tbodyTransitionHandlers: {}
          // Todo:: add more props for table
        },
      },
      stage: {
        updatedData: false,
        disableActions: true,
        viewType: (this.$store.state.optionBehavior.viewType) ? this.$store.state.optionBehavior.viewType : 'list',
        actionInactive: {
          inactive: 0,
          showInactive: false,
          arrayID: []
        },
        filterInactive: 2,
        message: (this.$route.params.message) ? this.$route.params.message : '',
        variantMessage: (this.$route.params.variantMessage) ? this.$route.params.variantMessage : 'success',
        isBackToList: false
      }
    }
  },
  methods:{
    fetchData(){},
    $_lists_onRowSelected(items){
      if (this.settings.FieldID === '') {
        console.log('Bạn chưa thiết lập trường khóa chính');
        return;
      }
      let self = this;
      (items.length) ? this.stage.disableActions = false : this.stage.disableActions = true;
      if (items.length) {
        this.stage.actionInactive.inactive = items[0].Inactive;
        this.stage.actionInactive.showInactive = true;
      }
      this.idsSelected = [];
      _.forEach(items, function (item, key) {
        self.idsSelected.push(item[self.settings.FieldID]);
        if (item[self.settings.FieldInactive] !== self.stage.actionInactive.inactive) {
          self.stage.actionInactive.showInactive = false;
        }
      });
      // set action inactive
      this.stage.actionInactive.arrayID = this.idsSelected;
    },
    $_lists_onToggleRowSelected(data){
      if (data.rowSelected) {
        this.$refs.selectableTable.unselectRow(data.index);
      } else {
        this.$refs.selectableTable.selectRow(data.index);
      }
    },
    $_lists_onToggleSelectAllRows(value){
      (value) ? this.$refs.selectableTable.selectAllRows() : this.$refs.selectableTable.clearSelected();
    },
    $_lists_handleChangeInActive(){
      let title = (this.stage.inactive == 0) ? 'Đang hoạt động' : 'Ngừng hoạt động';
      let self = this;
      Swal.fire({
        title: title,
        text: 'Xác nhận chuyển trạng thái bản ghi',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Đồng ý',
        cancelButtonText: 'Hủy bỏ'
      }).then((result) => {
        if (result.value) {
          let inactive = (this.stage.actionInactive.inactive == 0) ? 1 : 0;
          let requestData = {
            method: 'post',
            url: 'listing/api/common/status',
            data: {
              array_id: self.stage.actionInactive.arrayID,
              FieldID: self.settings.FieldID,
              table: self.settings.Table,
              Inactive: inactive,
              FieldUpdate: self.settings.FieldInactive
            },
          };
          self.$store.commit('isLoading', true);
          ApiService.customRequest(requestData).then((response) => {
            let responseData = response.data;
            if (responseData.status == '1') {
              self.$store.commit('isLoading', false);
              this.fetchData();
              this.$bvToast.toast('Bản ghi đã được cập nhật', {
                title: 'Thông báo',
                variant: 'success',
                solid: true
              });
            }

          }, (error) => {
            console.log(error);
            self.$store.commit('isLoading', false);
          });
        }
      });
    },
    $_lists_showMessage(){
      // hiển thị thông báo
      if (this.stage.message !== '') {
        this.$bvToast.toast(this.stage.message, {
          title: 'Thông báo',
          variant: this.stage.variantMessage,
          solid: true
        });
      }
    },
    $_lists_onToggleDropdownSubMenu(event){
      __.onToggleDropdownSubMenu(event);
    },
    $_lists_onToggleActionMajor() {
      let dropdownSubMenus = document.querySelectorAll('.dropdown-sub-menu');
      for (let j = 0; j < dropdownSubMenus.length; j++) {
        dropdownSubMenus[j].classList.remove('show');
      }
    },
    $_lists_handleEditItem(data) {
      let router = this.settings.EditRouter + '/' + data.item[this.settings.FieldID];
      this.$router.push({name: router, params: {req: this.paramsReqRouter}, query: this.queryReqRouter});
    },
    $_lists_handleAddNewItem() {
      this.$router.push({
        name: this.settings.CreateRouter,
        params: {req: this.paramsReqRouter},
        query: this.queryReqRouter
      });
    },
    $_lists_onRowClicked(item, index, event) {
      if (!event.target.classList.contains('checkbox-selected') && !(event.target.firstChild && event.target.firstChild.classList && event.target.firstChild.classList.contains('checkbox-selected'))) {
        this.$router.push({
          name: this.settings.ViewRouter,
          params: {id: item[this.settings.FieldID], req: this.paramsReqRouter},
          query: this.queryReqRouter
        });
      }
    },
    $_lists_onFieldClicked(item){
      this.$router.push({
        name: this.settings.ViewRouter,
        params: {id: item[this.settings.FieldID], req: this.paramsReqRouter}
      });
    },
    $_lists_handleSubmitSearch(){
      this.currentPage = 1;
      this.stage.viewType = 'list';
      this.fetchData();
    },
    $_lists_handleChangeTypeShow(type) {
      this.typeShow = type;
      this.$_lists_handleSubmitSearch();
    },
    $_lists_onRowClicked_view(item){
      this.vew_onToggleModal();
    },
    $_lists_handleDeleteItem() {
      let self = this;
      Swal.fire({
        title: '',
        text: 'Bạn sẽ không thể khôi phục thông tin bản ghi!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy bỏ'
      }).then((result) => {
        if (result.value) {

          let requestData = {
            method: 'post',
            url: self.settings.DeleteApi,
            data: {
              array_id: this.idsSelected,
            },
          };

          ApiService.setHeader();
          ApiService.customRequest(requestData).then((response) => {
            let dataResponse = response.data;
            if (dataResponse.status === 1) {
              self.$bvToast.toast('Bản ghi đã bị xóa', {
                title: 'Thông báo',
                variant: 'success',
                solid: true
              });

              _.forEach(self.idsSelected, function (id, key) {
                const index = self.itemsArray.findIndex(post => post[self.settings.FieldID] === id); // find the post index
                if (~index) // if the post exists in array
                  self.itemsArray.splice(index, 1); //delete the post
              });

              if (!self.itemsArray.length) {
                this.currentPage = 1;
              }

              self.totalRows = self.totalRows - self.idsSelected.length;

              // For more information about handling dismissals please visit
              // https://sweetalert2.github.io/#handling-dismissals
            } else {
              let msg = (dataResponse.msg) ? dataResponse.msg : '';
              Swal.fire(
                '',
                msg,
                'error'
              );
              console.log(response);

            }
          }, (error) => {
            console.log(error);

          });

        }
      });

    },
    $_lists_handleChangePerPage(perPage){
      this.perPage = String(perPage);
      this.$store.commit('optionBehavior', {'perPage': this.perPage});
      this.currentPage = 1;
      this.fetchData();
    },
    $_lists_setParamsReqRouter() {
      let self = this;
      // set id array
      this.paramsReqRouter.idsArray = [];
      _.forEach(this.itemsArray, function (value, key) {
        self.paramsReqRouter.idsArray.push(value[self.settings.FieldID]);
      });

      // set search
      _.forEach(this.modelSearch, function (value, key) {
        self.paramsReqRouter.search[key] = value;
      });

      // set pagination
      this.paramsReqRouter.total = this.totalRows;
      this.paramsReqRouter.perPage = this.perPage;
      this.paramsReqRouter.currentPage = this.currentPage;
      this.paramsReqRouter.totalRows = self.totalRows;
      this.paramsReqRouter.itemsArray = self.itemsArray;
    },
    $_lists_handleFilterInactive(inactive){
      this.stage.filterInactive = inactive;
      this.fetchData();
    },
    $_lists_handleFullTextSearch(){
      // Todo: setup full text search
      this.fetchData();
    },

    // report
    async $_lists_handleReportTemplate(name) {
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
        console.log(e);
      }

    },

    async $_lists_handleReportResponse(url, request = {}) {
      const requestData = {
        method: 'post',
        url: url,
        data: request
      };

      try {
        const response = await ApiService.customRequest(requestData);
        let responseData = response.data;
        if (responseData.status) {
          return responseData.data;
        }
      }catch (e) {
        console.log(e);
      }
    },

    async $_lists_handleDowloadExcel(reportJson, reportData, nameExcel = 'Danh mục'){
      const excelExportSettings = XlsxExport.XlsxSettings = {
        title: "Listing",
        author: "SmartBooks",
        keywords: "export, report",
        subject: "Report",
      };

      reportJson.DataSources[0].ConnectionProperties.ConnectString = "jsondata=" + JSON.stringify(reportData);
      const report = new Core.PageReport();
      await report.load(reportJson);
      const doc = await report.run();
      const result = await XlsxExport.exportDocument(doc, excelExportSettings);
      result.download(nameExcel);
    },
    async $_lists_handleDowloadPdf(reportJson, reportData, namePdf = 'Danh mục'){
      const pdfExportSettings = PdfExport.PdfSettings = {
        title: "Test document",
        author: "SmartBooks",
        keywords: "export, report",
        subject: "Report",
        pdfVersion: "1.4",
      };

      reportJson.DataSources[0].ConnectionProperties.ConnectString = "jsondata=" + JSON.stringify(reportData);
      const report = new Core.PageReport();
      await report.load(reportJson);
      const doc = await report.run();
      const result = await PdfExport.exportDocument(doc, pdfExportSettings);
      result.download(namePdf);
    },
  },
  watch:{
    'stage.viewType'(){
      this.$store.commit('optionBehavior', {'viewType': this.stage.viewType});
    }
  }
}

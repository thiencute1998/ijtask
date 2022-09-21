<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Định mức dự toán: {{model.NormTableName}}</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onCreateClicked"><i
                class="fa fa-plus"></i> Thêm
              </b-button>
              <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="onEditClicked"><i
                class="fa fa-edit"></i> Sửa
              </b-button>
              <b-dropdown variant="primary" id="dropdown-actions" class="main-header-action mr-2" text="Thao tác">
                <b-dropdown-item @click="handleExportReport">In</b-dropdown-item>
                <b-dropdown-item @click="handleCopyItem">Nhân bản</b-dropdown-item>
                <b-dropdown-item>Chia sẻ</b-dropdown-item>
                <b-dropdown-item>Chat</b-dropdown-item>
                <b-dropdown-item>Zalo</b-dropdown-item>
                <b-dropdown-item>Phân quyền</b-dropdown-item>
                <b-dropdown-item @click="handleDeleteItem">Xóa</b-dropdown-item>
              </b-dropdown>
            </div>
          </b-col>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-icons">
              <div class="main-header-item-counter ml-auto mr-3" v-if="reqParams">
                <span>{{itemNo}} - {{reqParams.idsArray.length + this.reqParams.perPage * (this.reqParams.currentPage - 1)}}</span>
                /
                <span>{{reqParams.total}}</span>
              </div>
              <b-button-group id="main-header-views" class="main-header-views">
                <b-button id="tooltip-view-prev" @click="onNavigationItem('prev')" v-if="reqParams"
                          class="main-header-view"><i class="fa fa-angle-left"></i></b-button>
                <b-button id="tooltip-view-next" @click="onNavigationItem('next')" v-if="reqParams"
                          class="main-header-view"><i class="fa fa-angle-right"></i></b-button>
                <b-button id="tooltip-view-back-to-list" @click="onBackToList" class="main-header-view"><i
                  class="fa fa-list"></i></b-button>
                <b-tooltip container="main-header-views" target="tooltip-view-back-to-list">Danh sách</b-tooltip>
                <b-tooltip container="main-header-views" target="tooltip-view-prev">Trước</b-tooltip>
                <b-tooltip container="main-header-views" target="tooltip-view-next">Sau</b-tooltip>
              </b-button-group>
              <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen="true"/>
              </div>

            </div>
          </b-col>
        </b-row>
      </div>

    </div>
    <div class="main-body main-body-view-action">
      <div class="container-fluid h-100">
          <b-card class="h-100" body-class="h-100">
            <div class="d-flex flex-column h-100">
              <div class="form-group row align-items-center mb-3">
                <label class="col-md-3 m-0">Tên bảng ĐMDTCT: </label>
                <div class="col-md-18">
                  {{model.NormTableName}}
                </div>
                <div class="col-lg-3 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                  <span>Mã số: </span>
                  {{model.NormTableNo}}
                </div>
              </div>
              <div class="form-group row align-items-center">
                <label class="col-md-3 m-0">Loại ĐMDT: </label>
                <div class="col-md-5">
                  <span v-if="model.NormType === 1">Định mức cơ sở</span>
                  <span v-if="model.NormType === 2">Định mức của ĐVSDNS</span>
                </div>
                <label class="col-md-3 m-0">Ngày ban hành: </label>
                <div class="col-md-3">
                  {{model.NormTableDate}}
                </div>
                <label class="col-md-3 m-0">Ngày hiệu lực: </label>
                <div class="col-md-3">
                  {{model.EffectiveDate}}
                </div>
              </div>
              <div class="form-group row align-items-center">
                <label class="col-md-3 m-0">Kỳ: </label>
                <div class="col-md-3">
                  {{model.PeriodName}}
                </div>
                <label class="col-md-2 m-0">Từ ngày: </label>
                <div class="col-md-3">
                  {{model.FromDate}}
                </div>
                <label class="col-md-2 m-0">Đến ngày: </label>
                <div class="col-md-3">
                  {{model.ToDate}}
                </div>
                <label class="col-md-3 m-0">Ngày hết hiệu lực: </label>
                <div class="col-md-3">
                  {{model.ExpirationDate}}
                </div>
              </div>
              <div class="table-responsive b-table-sticky-header b-table-sticky-custom m-0" style="max-height: none">
                <table class="table b-table table-sm table-bordered table-tree table-column-resizable table-view">
                  <thead>
                  <tr>
                    <th scope="col" style="width: 3%; min-width: 65px; background: #fff" class="text-center" title="Thu/Chi">STT</th>
                    <th scope="col" style="width: 3%; min-width: 65px; background: #fff" class="text-center" title="Thu/Chi">Loại</th>
                    <th scope="col" style="width: 6%; min-width: 100px; background: #fff" class="text-center" title="Định mức phân bổ dự toán">ĐMPBDT</th>
                    <th scope="col" style="width: 6%; min-width: 100px; background: #fff" class="text-center" title="Chỉ tiêu dự toán">CTDT</th>
                    <th scope="col" style="width: 6%; min-width: 100px; background: #fff" class="text-center b-r-0">Mã số</th>
                    <th scope="col" style="min-width: 400px; background: #fff; z-index: 11 !important;" class="text-center b-table-sticky-column">Tên chỉ tiêu</th>
                    <th scope="col" style="width: 8%; min-width: 100px; background: #fff" class="text-center">Đơn vị tính</th>
                    <th scope="col" style="width: 4%; min-width: 100px; background: #fff" class="text-center">Tiền tệ</th>
                    <th scope="col" style="width: 10%; min-width: 120px; background: #fff" class="text-center">Số lượng</th>
                    <th scope="col" style="width: 10%; min-width: 120px; background: #fff" class="text-center" title="Đơn giá áp dụng nguyên tệ">ĐG áp dụng NT</th>
                    <th scope="col" style="width: 10%; min-width: 120px;background: #fff" class="text-center" title="Định mức nguyên tệ">Định mức NT</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr :id="'table-item-' + tableItem.NormTableItemID" v-show="tableItem.Show" v-for="(tableItem, key) in model.NormTableItem">
                    <td>{{tableItem.NOrder}}</td>
                    <td>
                      <span v-if="tableItem.NormType === 1">Thu</span>
                      <span v-else-if="tableItem.NormType === 2">Chi</span>
                      <span v-else-if="tableItem.NormType === 3">Thu & Chi</span>
                    </td>
                    <td>{{tableItem.NormAllotLevelNo}}</td>
                    <td>{{tableItem.NormNo}}</td>
                    <td class="b-r-0">{{tableItem.NormTableItemNo}}</td>
                    <td class="bg-tree-tr pl-0 b-table-sticky-column" style="background: #fff">
                      <span class="bg-tree-dot" :style="{'left': (level * 12) + 'px'}" v-for="level in model.NormTableItem[key].Level"></span>
                      <div class="bg-tree-content bg-tree-td"
                           :style="{'margin-left': (tableItem.Level * 12 - 12) + 'px'}">
                        <span style="padding-left: 20px" :title="model.NormTableItem[key].NormTableItemName">{{model.NormTableItem[key].NormTableItemName}}</span>
                        <i class="fa fa-minus-square-o bg-tree-icon-action" v-if="model.NormTableItem[key].HaveChildren" @click="onToggleChildNodes($event, tableItem)"></i>
                      </div>
                    </td>
                    <td>{{tableItem.UomName}}</td>
                    <td>{{tableItem.CcyName}}</td>
                    <td>{{tableItem.Quantity}}</td>
                    <td class="text-right">{{tableItem.FCUnitPrice | convertNumberToText}}</td>
                    <td class="text-right">{{tableItem.FCAmount | convertNumberToText}}</td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </b-card>
        </div>
    </div>
  </div>

</template>

<script>
  import ApiService from '@/services/api.service';

  const ListRouter = 'listing-normtable';
  const EditRouter = 'listing-normtable-edit';
  const CreateRouter = 'listing-normtable-create';
  const ViewApi = 'listing/api/norm-table/view';
  const ListApi = 'listing/api/norm-table';
  const DeleteApi = 'listing/api/norm-table/delete';
  import ColumnResizer from 'column-resizer';

  export default {
    name: 'listing-normtable-view',
    data() {
      return {
        idParams: this.$route.params.id,
        reqParams: this.$route.params.req,
        model: {
          NormTableID: null,
          NormTableNo: '',
          NormType: 1,
          NormTableDate: null,
          EffectiveDate: null,
          ExpirationDate: null,
          PeriodID: null,
          PeriodName: '',
          FromDate: null,
          ToDate: null,
          NormTableName: '',
          Inactive: '',
          NormTableItem: []
        },
        RowItem: 0,
        stage: {
          message: (this.$route.params.message) ? this.$route.params.message : ''
        }
      }

    },

    components: {},
    beforeCreate() {
      if (!this.$route.params.id) {
        this.$router.push({name: ListRouter});
      }
    },
    mounted() {
      this.fetchData();

      if (document.querySelector('.table-column-resizable')) {
        new ColumnResizer(
          document.querySelector('.table-column-resizable'),{resizeMode: 'overflow'}
        );
      }

      // hiển thị thông báo
      if (this.stage.message && this.stage.message !== '') {
        this.$bvToast.toast(this.stage.message, {
          title: 'Thông báo',
          variant: 'success',
          solid: true
        });
      }
    },
    computed: {
      itemNo() {
        let index = 0;
        index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
        return index;
      }
    },
    methods: {
      fetchData() {
        if (this.idParams == 0 || _.isUndefined(this.idParams)) {
          return false;
        }
        let self = this;
        let requestData = {
          method: 'get',
          url: 'listing/api/norm-table/view/' + this.idParams,
          data: {}
        };
        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          self.defaultModel = responsesData;
          if (responsesData.status === 1) {
            self.model.NormTableID = responsesData.data.data.NormTableID;
            self.model.NormTableNo = responsesData.data.data.NormTableNo;
            self.model.NormTableName = responsesData.data.data.NormTableName;
            self.model.NormType = responsesData.data.data.NormType;
            self.model.NormTableDate = __.convertServerDateToClientDate(responsesData.data.data.NormTableDate);
            self.model.EffectiveDate = __.convertServerDateToClientDate(responsesData.data.data.EffectiveDate);
            self.model.ExpirationDate = __.convertServerDateToClientDate(responsesData.data.data.ExpirationDate);
            self.model.PeriodID = responsesData.data.data.PeriodID;
            self.model.PeriodName = responsesData.data.data.PeriodName;
            self.model.FromDate = __.convertServerDateToClientDate(responsesData.data.data.FromDate);
            self.model.ToDate = __.convertServerDateToClientDate(responsesData.data.data.ToDate);

            _.forEach(responsesData.data.NormTableItem, function (tableItem, key) {
              tableItem.Show = true;
              tableItem.HaveChildren = (tableItem.Detail) ? false : true;
              self.model.NormTableItem.push(tableItem);
            });
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      onToggleChildNodes(e, itemParent) {
        let self = this;
        if (e && e.target.classList.contains('fa-minus-square-o')) {
          // close children
          e.target.classList.remove('fa-minus-square-o');
          e.target.classList.add('fa-plus-square-o');
          let allChildTableItem = this.getAllChildTableItem(itemParent, this.model.NormTableItem);
          if (allChildTableItem && allChildTableItem.length) {
            _.forEach(allChildTableItem, function (childTableItem, key) {
              let indexItem = _.findIndex(self.model.NormTableItem, ['NormTableItemID', childTableItem.NormTableItemID]);
              if (indexItem > -1) {
                self.model.NormTableItem[indexItem].Show = false;
              }
            });
          }
        } else {
          // open children
          e.target.classList.remove('fa-plus-square-o');
          e.target.classList.add('fa-minus-square-o');
          let allChildren = _.filter(this.model.NormTableItem, ['ParentID', itemParent.NormTableItemID]);
          if (allChildren.length) {
            _.forEach(allChildren, function (childTableItem, key) {
              let indexItem = _.findIndex(self.model.NormTableItem, ['NormTableItemID', childTableItem.NormTableItemID]);
              if (indexItem > -1) {
                self.model.NormTableItem[indexItem].Show = true;
                $('#table-item-' + self.model.NormTableItem[indexItem].NormTableItemID + ' .bg-tree-icon-action').removeClass('fa-minus-square-o');
                $('#table-item-' + self.model.NormTableItem[indexItem].NormTableItemID + ' .bg-tree-icon-action').addClass('fa-plus-square-o');
              }
            });
          }
        }
      },
      getAllChildTableItem(item, tableItemArr){
        let self = this, listChild = [];
        let allChild = _.filter(tableItemArr, ['ParentID', item.NormTableItemID]);
        if (allChild.length) {
          allChild = _.orderBy(allChild, ['NormTableItemID'], ['asc']);
          _.forEach(allChild, function (value, key) {
            listChild.push(value);
            if (_.filter(tableItemArr, ['ParentID', value.NormTableItemID]).length) {
              let recursiveArr = self.getAllChildTableItem(value, tableItemArr);
              recursiveArr = _.orderBy(recursiveArr, ['NormTableItemID', 'asc']);
              _.forEach(recursiveArr, function (recursive, key) {
                listChild.push(recursive);
              });
            }

          });
        }
        return listChild;
      },
      handleCopyItem() {
        this.$router.push({name: CreateRouter, params: {itemCopy: this.defaultModel}});
      },
      onEditClicked() {
        this.$router.push({
          name: 'listing-normtable-edit',
          params: {id: this.idParams, req: this.reqParams}
        });
      },
      onCreateClicked() {
        this.$router.push({name: 'listing-normtable-create'});
      },
      onBackToList(message = '') {
        if (_.isString(message)) {
          this.$router.push({name: 'listing-normtable', params: {message: message}});
        } else {
          this.$router.push({name: 'listing-normtable'});
        }
      },
      handleDeleteItem() {
        let self = this;
        let title = 'Bạn có muốn xóa bản ghi?';
        Swal.fire({
          title: title,
          text: 'Bạn sẽ không thể khôi phục thông tin bản ghi!',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Đồng ý',
          cancelButtonText: 'Hủy bỏ'
        }).then((result) => {
          if (result.value) {
            let requestData = {
              method: 'post',
              url: DeleteApi + '/' + self.idParams,
              data: {
                array_id: [self.idParams],
              },
            };

            ApiService.setHeader();
            ApiService.customRequest(requestData).then((response) => {
              let responseData = response.data;
              if (responseData.status === 1) {
                self.onBackToList('Bản ghi đã được xóa');
              } else {
                Swal.fire(
                  'Có lỗi',
                  '',
                  'error'
                );
              }
            }, (error) => {
              console.log(error);

            });

          }
        });
      },
      handleExportReport(){
        let request = {};
        request.id = this.idParams;
        request.exportData = true;
        this.$router.push({
          name: 'listing-normtable-report',
          query: request
        });
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
      updateModel() {
        if (this.stage.updatedData) {
          this.$forceUpdate();
        }
      },
      onNavigationItem(type) {
        let currentIndex = this.reqParams.idsArray.indexOf(this.idParams);
        let newIndex = (type === 'prev') ? currentIndex - 1 : currentIndex + 1;

        if (newIndex === (this.reqParams.idsArray.length) && (this.reqParams.currentPage != this.reqParams.lastPage)) {
          this.reqParams.currentPage = this.reqParams.currentPage + 1;
          this.getItemIds(type);
        } else if (newIndex < 0 && this.reqParams.currentPage > 1){
          this.reqParams.currentPage = this.reqParams.currentPage - 1;
          this.getItemIds(type);
        }
        else {
          this.idParams = (this.reqParams.idsArray[newIndex]) ? this.reqParams.idsArray[newIndex] : this.idParams;
        }
      },
      getItemIds(type){
        let self = this;
        let requestData = {
          method: 'post',
          url: ListApi,
          data: {
            per_page: this.reqParams.perPage,
            page: this.reqParams.currentPage,
            type: 'only-id'
          }
        };

        if (this.reqParams.search.NormTableNo) {
          requestData.data.NormTableNo = this.reqParams.search.NormTableNo;
        }
        if (this.reqParams.search.NormTableName) {
          requestData.data.NormTableName = this.reqParams.search.NormTableName;
        }
        if (this.reqParams.search.officePhone) {
          requestData.data.OfficePhone = this.reqParams.search.officePhone;
        }
        if (this.reqParams.search.fax !== '') {
          requestData.data.Fax = this.reqParams.search.fax;
        }
        if (this.reqParams.search.email !== '') {
          requestData.data.Email = this.reqParams.search.email;
        }


        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let dataResponse = response.data;
          if (dataResponse.status === 1) {
            self.reqParams.total = dataResponse.data.total;
            self.reqParams.perPage = String(dataResponse.data.per_page);
            self.reqParams.currentPage = dataResponse.data.current_page;

            this.reqParams.idsArray = [];
            _.forEach(dataResponse.data.data, function (value, key) {
              self.reqParams.idsArray.push(value.NormTableID);
            });

            (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });

      },
    },
    watch: {
      idParams() {
        this.fetchData();
      }
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
    border-left: 1px dotted #858585;
    z-index: 1;
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
</style>


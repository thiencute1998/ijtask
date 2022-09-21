<template>
    <div>
        <div class="main-entry">
            <div class="main-header">
                <div class="main-header-padding">
                    <b-row class="mb-2">
                        <b-col class="col-md-12">
                            <div class="main-header-item main-header-name">
                                <span class="text-capitalize"><i class="fa fa-list mr-2"></i> TempitemID </span>
                            </div>
                        </b-col>
                        <b-col class="col-md-12"></b-col>
                    </b-row>
                    <b-row class="mb-2">
                      <b-col class="col-lg-12 col-md-24 col-sm-24 col-24 mb-2 mb-lg-0 d-lg-flex justify-content-start align-items-center">
                        <div class="main-header-item main-header-actions">
                          <b-button class="main-header-action mr-2" variant="primary" @click="onToggleModal()" size="md">
                            <i class="fa fa-plus"></i> Thêm
                          </b-button>

                          <b-dropdown variant="primary" id="dropdown-actions" @toggle="$_lists_onToggleActionMajor" class="main-header-action mr-2" text="Thao tác">
                            <b-dropdown-item @click="$_lists_handleDeleteItem" :disabled="stage.disableActions">Xóa</b-dropdown-item>
                            <b-dropdown-item>In</b-dropdown-item>
                            <li class="dropdown b-dropdown dropright" >
                              <a class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" data-toggle="dropdown" @click.stop="$_lists_onToggleDropdownSubMenu($event)" href="#">Nhập</a>
                              <ul role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-menu m-0">
                                <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Excel</a></li>
                                <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Cvs</a></li>
                                <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Xml</a></li>
                                <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Json</a></li>
                              </ul>
                            </li>
                            <li class="dropdown b-dropdown dropright">
                              <a class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" :class="[stage.disableActions ? 'disabled' : '']" data-toggle="dropdown" @click.stop="onToggleDropdownSubMenu($event)" href="#">Xuất</a>
                              <ul role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-menu m-0">
                                <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Excel</a></li>
                                <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Cvs</a></li>
                                <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Xml</a></li>
                                <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item">Json</a></li>
                              </ul>
                            </li>
                            <b-dropdown-item :disabled="stage.disableActions">Chia sẻ</b-dropdown-item>
                            <b-dropdown-item :disabled="stage.disableActions">Chat</b-dropdown-item>
                            <b-dropdown-item :disabled="stage.disableActions">Zalo</b-dropdown-item>
                            <b-dropdown-item :disabled="stage.disableActions">Phân quyền</b-dropdown-item>

                          </b-dropdown>
                        </div>
                          <div class="main-header-item main-header-search" style="flex: 1 1 auto">
                              <div class="search-input">
                                  <input v-model="fullTextSearch" @keyup.enter="$_lists_handleFullTextSearch" type="text" placeholder="Tìm kiếm..."/>
                              </div>
                              <span class="search-icon" @click="$_lists_handleFullTextSearch"><i class="fa fa-search"></i></span>
                          </div>
                      </b-col>
                      <b-col class="col-lg-12 col-md-24 col-sm-24 col-24">
                        <div class="main-header-item main-header-icons">
                          <b-button-group id="main-header-views" class="main-header-views">
                            <b-dropdown id="dropdown-view-type" title="Loại hiển thị" menu-class="p-0" :class="[(stage.viewType === 'list' || stage.viewType === 'tree' || stage.viewType === 'select') ? 'view-active' : '']" class="app-dropdown-center" toggle-class="main-header-view">
                              <template v-slot:button-content>
                                <i class="fa fa-tree" v-if="stage.viewType === 'tree'"></i>
                                <i class="fa fa-list" v-else></i>
                              </template>
                              <b-dropdown-item id="tooltip-view-list" title="Danh sách" @click="stage.viewType = 'list'"><i class="fa fa-list m-0"></i></b-dropdown-item>
                              <b-dropdown-item id="tooltip-view-tree" title="Cây" @click="stage.viewType = 'tree'"><i class="fa fa-tree m-0"></i></b-dropdown-item>
                            </b-dropdown>
                            <b-button id="tooltip-view-kanban" title="Thẻ tin" @click="stage.viewType = 'kanban'" :class="[(stage.viewType === 'kanban') ? 'view-active' : '']" class="main-header-view"><i class="fa fa-th"></i></b-button>
                          </b-button-group>
                          <b-dropdown id="dropdown-per-page" title="Số bản ghi/trang" menu-class="p-0" :text="perPage" class="app-dropdown-center main-header-icon">
                            <b-dropdown-item @click="$_lists_handleChangePerPage(10)">10</b-dropdown-item>
                            <b-dropdown-item @click="$_lists_handleChangePerPage(15)">15</b-dropdown-item>
                            <b-dropdown-item @click="$_lists_handleChangePerPage(20)">20</b-dropdown-item>
                            <b-dropdown-item @click="$_lists_handleChangePerPage(30)">30</b-dropdown-item>
                            <b-dropdown-item @click="$_lists_handleChangePerPage(40)">40</b-dropdown-item>
                            <b-dropdown-item @click="$_lists_handleChangePerPage(50)">50</b-dropdown-item>
                          </b-dropdown>
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
            <div class="main-body" :class="(stage.viewType === 'list') ? 'main-body-view-list' : 'main-body-view-kanban'">
                <b-card class="m-0 border-0" body-class="py-0 px-0">
                    <div class="content-body">
                      <div class="content-table content-body-list" v-if="stage.viewType === 'list'">
                        <b-table ref="selectableTable"
                                   :hover="settings.propsTable.hover"
                                   :small="settings.propsTable.small"
                                   :sticky-header="settings.propsTable.stickyHeader"
                                   head-variant="light"
                                   :fields="captions"
                                   responsive
                                   selectable
                                   select-mode="multi"
                                   @row-selected="$_lists_onRowSelected"
                                   @row-clicked="onToggleModalview"
                                   selected-variant="active"
                                   primary-key="index"
                                   :items="itemsArray">
<!--                          @row-clicked="$_lists_onRowClicked"-->
<!--                               :striped="propsTable.striped"-->
                              <!-- A custom formatted header cell for field 'name' -->
                              <template v-slot:head(selected)="data">
                                  <b-form-checkbox class="text-left" @input="$_lists_onToggleSelectAllRows($event)" :checked="false"></b-form-checkbox>
                              </template>
                              <template slot="top-row" slot-scope="data">
                                  <td :class="[(field.key == 'selected') ? 'pl-3' : '', (field.key == 'ItemName') ? 'pr-3' : '']" role="cell" v-for="(field, key) in data.fields">
                                      <!-- type input-->
                                      <b-form-input
                                        type="text" :placeholder="field.searchOnTopRow.placeholder"
                                        v-if=" field.searchOnTopRow && field.searchOnTopRow.type == 'text' && key !== 0"
                                        :name="field.field"
                                        v-model="modelSearch[field.field]"
                                        @keyup.enter="$_lists_handleSubmitSearch"
                                        :autocomplete="field.field">
                                      </b-form-input>

                                      <!-- type slect -->
                                      <b-form-select
                                        v-if="field.searchOnTopRow && field.searchOnTopRow.type == 'select' && key !== 0"
                                        :plain="true"
                                        :name="field.field"
                                        v-model="modelSearch[field.field]"
                                        :options="field.searchOnTopRow.option"
                                        @change="$_lists_handleSubmitSearch">
                                      </b-form-select>
                                  </td>
                              </template>
                              <!-- Example scoped slot for select state illustrative purposes -->
                              <template v-slot:cell(selected)="data">
                                  <b-form-checkbox class="checkbox-selected" @change="$_lists_onToggleRowSelected(data)" :checked="data.rowSelected"></b-form-checkbox>
                              </template>
                          </b-table>
                      </div>
                      <div class="content-body-kanban" v-if="stage.viewType === 'kanban'">
                        <div class="kanban-items row">
                          <div class="kanban-item col-lg-6 col-md-8 col-sm-12 col-24" v-for="(item, key) in itemsArray">
                            <div class="kanban-item-inner" @click="$_lists_onFieldClicked(item)">
                              <div class="kanban-record d-flex justify-content-between">
                                <span class="kanban-title">{{item.IndicatorName}}</span>
                                <span class="kanban-no">{{item.IndicatorNo}}</span>
                              </div>
<!--                              <div class="kanban-record">-->
<!--                                <span class="kanban-text" v-if="item.Tel && item.Tel !== ''">{{item.Tel}}</span>-->
<!--                              </div>-->
<!--                              <div class="kanban-record">-->
<!--                                <span class="kanban-text" v-if="item.Fax && item.Fax !== ''">{{item.Fax}}</span>-->
<!--                              </div>-->
<!--                              <div class="kanban-record">-->
<!--                                <span class="kanban-text" v-if="item.Email && item.Email !== ''">{{item.Email}}</span>-->
<!--                              </div>-->
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                </b-card>
            </div>
            <div class="main-footer">
              <div class="d-flex flex-wrap justify-content-between align-items-center m-0">
                    <div class="main-footer-pagination">
                        <div class="overflow-auto">
                            <b-pagination
                                    v-model="currentPage"
                                    :total-rows="rows"
                                    :per-page="perPage"
                                    aria-controls="my-table"
                                    size="md"
                            ></b-pagination>
                        </div>
                    </div>
                    <div class="total-item">
                        <span style="font-weight: 500">Tổng số: {{totalRows}}</span>
                    </div>
                </div>
            </div>
        </div>
      <!-- Popup Add -->
      <b-modal ref="modal" id="modal-form-input-task-general" size="xl">
          <div class="main-body main-body-view-action">
            <FormAdd v-model="model" v-if="isForm"> </FormAdd>
            <FormAdd  v-model="model" :isForm="true" v-if="!isForm" ></FormAdd>
          </div>
          <template v-slot:modal-footer>
            <div class="w-100 left">
<!--              <b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()"  v-if="isForm">-->
<!--                Sửa-->
<!--              </b-button>-->
              <b-button variant="primary" size="md" class="float-left mr-2" @click="onSubmitForm()" v-if="!isForm">
                Lưu
              </b-button>
              <b-button variant="primary" size="md" class="float-left mr-2" @click="" v-if="isForm">
                Hủy
              </b-button>
              <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideModal()">
                Đóng
              </b-button>
            </div>
          </template>
      </b-modal>
      <!-- Popup View -->
      <b-modal ref="modalview" id="modal-form-input-task-general1" size="xl">
        <div class="main-body main-body-view-action">
            <FormView :id-param="model.idSelected"> </FormView>
        </div>
        <template v-slot:modal-footer>
          <div class="w-100 left">
            <b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()" >
              Sửa
            </b-button>
<!--            <b-button variant="primary" size="md" class="float-left mr-2" @click="onSubmitForm()" v-if="!isForm">-->
<!--              Lưu-->
<!--            </b-button>-->
<!--            <b-button variant="primary" size="md" class="float-left mr-2" @click="" v-if="isForm">-->
<!--              Hủy-->
<!--            </b-button>-->
            <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideModalview()">
              Đóng
            </b-button>
          </div>
        </template>
      </b-modal>
    </div>
</template>


<script>
    import ApiService from '@/services/api.service';
    import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
    import 'v-calendar/lib/v-calendar.min.css';
    import Swal from 'sweetalert2';
    import 'sweetalert2/src/sweetalert2.scss';
    import mixinLists from '@/mixins/lists';
    import Select2 from 'v-select2-component';
    import FormAdd from "./partials/FormAdd";
    import FormView from "./partials/FormView";

    const ListRouter = 'task-indicator-tempitem';
    const EditRouter = 'task-indicator-tempitem-edit';
    const ViewRouter = 'task-indicator-tempitem-view';
    const CreateRouter = 'task-indicator-tempitem-create';
    const DetailApi = 'task/api/indicator-tempitem/view';
    const EditApi = 'task/api/indicator-tempitem/edit';
    const CreateApi = 'task/api/indicator-tempitem/create';
    const StoreApi = 'task/api/indicator-tempitem/store';
    const UpdateApi = 'task/api/indicator-tempitem/update';
    const ListApi = 'task/api/indicator-tempitem';

    export default {
        name: 'task-items',
        mixins: [mixinLists],
        data() {
            return {
                isForm: false,
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                  TempitemID: null,
                  TemplateID: null,
                  IndicatorNo: '',
                  IndicatorName: '',
                  ParentID: null,
                  UomID: null,
                  UomName: '',
                  FrequencyType: null,
                  GradingType: null,
                  Rate: '',
                  Description: '',
                  Uom: null,
                  uomOption: [],
                  idSelected: null
                },
                stage: {
                  updatedData: false
                }

            }
        },

        computed: {

            rows() {
                return this.totalRows
            },
            inActiveOption() {
                return inActiveOption;
            },
            captions: function() {
                let fieldsTable = [
                    {key: 'selected', label: '', thStyle: 'width: 5%',tdClass: 'pl-3', thClass: 'pl-3'},
                    {key: 'IndicatorNo', label: 'Mã chỉ tiêu', field: 'IndicatorNo', thStyle: 'width: 12%; min-width: 100px', searchOnTopRow: {type: 'text'}},
                    {key: 'IndicatorName', label: 'Tên chỉ tiêu', thStyle: 'min-width: 300px', field: 'IndicatorName',searchOnTopRow: {type: 'text'}},
                ];

                return fieldsTable;
            }
        },
        created() {

          this.settings.FieldID = 'TempitemID';
          this.settings.Table = 'task_indicator_temp_item';
          this.settings.ListApi = 'task/api/indicator-tempitem';
          this.settings.DeleteApi = 'task/api/indicator-tempitem/delete';
          this.settings.CreateRouter = 'task-indicator-tempitem-create';
          this.settings.EditRouter = 'task-indicator-tempitem-edit';
          this.settings.ViewRouter = 'task-indicator-tempitem-view';

          this.modelSearch = {
            IndicatorNo: '',
            IndicatorName: '',
          };
          this.init();
        },
        updated() {
            this.stage.updatedData = true;
        },
        mounted() {
          this.$_lists_showMessage();
        },
        components: {
          FormView,
          FormAdd,
          Select2
        },
        methods: {
            init(){
                this.fetchData();
            },

            handleDebugger(data){
                console.log(data);
            },

            fetchData(){
                let self = this;
                let urlApi = this.settings.ListApi;
                let requestData = {
                    method: 'post',
                    url: urlApi,
                    data: {
                        per_page: this.perPage,
                        page: this.currentPage,
                    },

                };

                if (this.modelSearch.IndicatorNo.trim() !== '') {
                    requestData.data.IndicatorNo = this.modelSearch.IndicatorNo;
                }
                if (this.modelSearch.IndicatorName !== '') {
                    requestData.data.IndicatorName = this.modelSearch.IndicatorName;
                }

                if (this.fullTextSearch !== '') {
                    requestData.data.fullTextSearch = this.fullTextSearch;
                }

                this.$store.commit('isLoading', true);
                ApiService.customRequest(requestData).then((response) => {
                    let dataResponse = response.data;
//console.log(dataResponse);
                    if (dataResponse.status === 1) {
                        self.totalRows = dataResponse.data.total;
                        self.perPage = String(dataResponse.data.per_page);
                        self.currentPage = dataResponse.data.current_page;
                        // converse object to array
                        self.itemsArray = _.toArray(dataResponse.data.data);

                        // set params request
                        self.paramsReqRouter.lastPage = dataResponse.data.last_page;
                        self.paramsReqRouter.from = dataResponse.data.from;
                        self.paramsReqRouter.to = dataResponse.data.to;
                        self.$_lists_setParamsReqRouter();
                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                  Swal.fire({
                    title: 'Thông báo',
                    text: 'Không kết nối được với máy chủ',
                    confirmButtonText: 'Đóng'
                  });
                });

                // scroll to top perfect scroll
                const container = document.querySelector('.b-table-sticky-header');
                if (container) container.scrollTop = 0;

            },
          onSubmitForm(){
            let self = this;
            this.$store.commit('isLoading', true);
            let UpdateApi = 'task/api/indicator-tempitem/update';
            const requestData = {
              method: 'post',
              url: StoreApi,
              data: {
                TemplateID: this.$route.params.id,
                IndicatorNo: this.model.IndicatorNo,
                IndicatorName: this.model.IndicatorName,
                ParentID: this.model.ParentID,
                Description: this.model.Description,
                Level: this.model.Level,
                Detail: this.model.Detail,
                UomID: this.model.UomID,
                UomName: this.model.UomName,
                GradingType: this.model.GradingType,
                FrequencyType: this.model.FrequencyType,
                Rate: this.model.Rate
              }
            };
            //console.log('hhhhhhhhhhh');
            // edit user
            if (this.idParams) {
              requestData.data.TemplateID = this.idParams;
              requestData.url = UpdateApi + '/' + this.idParams;
            }
            console.log(requestData);
            ApiService.setHeader();
            ApiService.customRequest(requestData).then((responses) => {
              let responsesData = responses.data;
              console.log(responsesData)
              if (responsesData.status === 1) {
                // if (!self.idParams && responsesData.data) self.idParams = responsesData.data.TempitemID;
                this.isForm = false;
                if (responsesData.data) self.itemsArray.push(responsesData.data);
                self.model.IndicatorNo = '';
                self.model.IndicatorName = '';
                self.model.ParentID = '';
                self.model.Description = '';
                self.model.UomID = null;
                self.model.GradingType = null;
                self.model.FrequencyType = null;
                self.model.Rate = '';

                self.$forceUpdate();

              } else {
                let htmlErrors = __.renderErrorApiHtml(responsesData.data);
                Swal.fire(
                  'Thông báo',
                  htmlErrors,
                  'error'
                )
              }
              self.$store.commit('isLoading', false);
              this.$refs['modal'].hide();
            }, (error) => {
              self.$store.commit('isLoading', false);
              Swal.fire(
                'Thông báo',
                'Không kết nối được với máy chủ',
                'error'
              )
            });
          },
            onToggleModal(){
              this.$refs['modal'].show();
              let self = this;
              let urlApi = CreateApi;
              let requestData = {
                method: 'get',
                data: {}
              };
              // console.log('asdfdfdsfdsf');
              // console.log(this.idParams);
                // Api edit user
                if(this.idParams){
                  urlApi = EditApi + '/' + this.idParams;
                  requestData.data.id = this.idParams;
                }
                requestData.url = urlApi;
                this.$store.commit('isLoading', true);

                ApiService.setHeader();
                ApiService.customRequest(requestData).then((responses) => { //console.log(responses);
                  let responsesData = responses.data;
                  //console.log(responsesData);
                  // copy item
                  if (!self.idParams && !_.isEmpty(self.itemCopy)) {
                    responsesData.data.data = self.itemCopy.data.data;
                  }
                  if (responsesData.status === 1) {

                    if (self.idParams || !_.isEmpty(self.itemCopy)) {
                      if (_.isObject(responsesData.data.data)) {
                        //self.model.TempitemID = responsesData.data.data.TempitemID;
                        self.model.TemplateID = responsesData.data.data.TemplateID;
                        self.model.ParentID = responsesData.data.data.ParentID;
                        self.model.IndicatorName = responsesData.data.data.IndicatorName;
                        self.model.IndicatorNo = responsesData.data.data.IndicatorNo;
                        self.model.UomID = responsesData.data.data.UomID;
                        self.model.uomName = responsesData.data.data.uomName;
                        self.model.FrequencyType = responsesData.data.data.FrequencyType;
                        self.model.GradingType = responsesData.data.data.GradingType;
                        self.model.FrequencyType = responsesData.data.data.FrequencyType;
                        self.model.Rate = responsesData.data.data.Rate;
                        self.model.Description = responsesData.data.data.Description;
                      }

                      if (!_.isEmpty(self.itemCopy)) {
                        self.model.IndicatorNo = responsesData.data.auto;
                      }
                    }else {
                      self.model.IndicatorNo = responsesData.data.auto;
                    }

                    if (_.isArray(responsesData.data.IndicatorTempItem)) {
                      self.model.indicatorOption = [];
                      _.forEach(responsesData.data.IndicatorTempItem, function (value, key) {
                        let tmpObj = {};
                        tmpObj.id = value.TempitemID;
                        tmpObj.text = value.IndicatorName;
                        self.model.indicatorOption.push(tmpObj);
                      });
                    }
                    if (_.isArray(responsesData.data.Uom)) {

                      self.model.uomOption = [
                        {value: null, text: 'Chọn đơn vị tính'}
                      ];
                      _.forEach(responsesData.data.Uom, function (value, key) {
                        let tmpObj = {};
                        tmpObj.value = value.UomID;
                        tmpObj.text = value.UomName;
                        self.model.uomOption.push(tmpObj);
                      });
                      console.log(self.model.uomOption);
                    }
                    if(!this.idParams) {
                      self.model.IndicatorName = '';
                      self.model.ParentID = '';
                      self.model.Description = '';
                      self.model.UomID = null;
                      self.model.GradingType = null;
                      self.model.FrequencyType = null;
                      self.model.Rate = '';

                    }


                  }
                  self.$store.commit('isLoading', false);
                }, (error) => {
                  console.log(error);
                  self.$store.commit('isLoading', false);
                  Swal.fire({
                    title: 'Thông báo',
                    text: 'Không kết nối được với máy chủ',
                    confirmButtonText: 'Đóng'
                  });
                });

            },
            onToggleModalview(item, index, event) {
              this.model.idSelected = item.TempitemID;
              this.$refs['modal'].hide();
              this.$refs['modalview'].show();

            },
            onEdit(){
              this.isForm = false;
              this.idParams = this.model.idSelected;
              this.$refs['modalview'].hide();
              this.onToggleModal();
            },
            onHideModal(){
              this.isForm = false;
              this.$refs['modal'].hide();
            },
            onHideModalview(){
              this.$refs['modalview'].hide();
            },
            handleExportExcel() {
                // Todo: handle export excel
                alert('excel');
            },
            handleExportPrint() {
                // Todo: handle export print
                alert('print');
            },
            changePR() {
              let self = this;
              let urlApi = this.api;
              let requestData = {
                method: 'post',
                url: '/listing/api/common/auto-childtable',
                data: {
                  table: 'task_indicator_temp_item',
                  ParentID: this.model.ParentID,
                  TableNo: 'IndicatorNo',
                  TableID: 'TempitemID',
                },

              };

              this.$store.commit('isLoading', true);
              ApiService.customRequest(requestData).then((response) => {//console.log(response);
                let dataResponse = response.data;
                this.model.IndicatorNo = dataResponse.data;
                self.$store.commit('isLoading', false);
              }, (error) => {
                self.$store.commit('isLoading', false);
                Swal.fire({
                  title: 'Thông báo',
                  text: 'Không kết nối được với máy chủ',
                  confirmButtonText: 'Đóng'
                });
              });
            },
            autoCorrectedDatePipe: () => { return createAutoCorrectedDatePipe('dd/mm/yyyy') },
            scrollHandle(evt){},
        },
        watch: {
            currentPage() {
                this.fetchData();
            },
          model: {
            handler(newOptionValue) {
              // alert('acsdf');
            },
            deep: true
          },
        }
    }
</script>

<style lang="css">
    .main-footer-pagination ul {
        margin-bottom: 0;
    }
    #modal-form-input-task-general .modal-lg .modal-content{
        width: 1024px;
        margin: auto;
    }
    @media (max-width: 1024px){
      #modal-form-input-task-general .modal-lg {
         max-width: 100%;
      }
      #modal-form-input-task-general .modal-lg .modal-content{
          width: 90%;
          margin: auto;
      }
    }
    @media (min-width: 992px){
      #modal-form-input-task-general .modal-lg {
          max-width: 100%;
      }
    }
    #modal-form-input-task-general1 .modal-lg .modal-content{
      width: 1024px;
      margin: auto;
    }
    @media (max-width: 1024px){
      #modal-form-input-task-general1 .modal-lg {
        max-width: 100%;
      }
      #modal-form-input-task-general1 .modal-lg .modal-content{
        width: 90%;
        margin: auto;
      }
    }
    @media (min-width: 992px){
      #modal-form-input-task-general1 .modal-lg {
        max-width: 100%;
      }
    }
</style>


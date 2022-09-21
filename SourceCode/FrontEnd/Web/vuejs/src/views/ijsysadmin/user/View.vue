<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span><i class="icon-eye icon mr-2"></i> Người dùng: {{model.information.fullName}}</span>
                        </div>
                    </b-col>
                    <b-col class="col-md-6"></b-col>
                </b-row>
                <b-row>
                  <b-col class="col-md-12 col-sm-12 col-24 mb-2 mb-sm-0 mb-md-0">
                        <div class="main-header-item main-header-actions">
                          <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onCreateClicked"><i class="fa fa-plus"></i> Thêm</b-button>
                          <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="onEditClicked"><i class="fa fa-edit"></i> Sửa</b-button>
                            <b-dropdown variant="primary" id="dropdown-actions" class="main-header-action mr-2" text="Thao tác">
                                <b-dropdown-item @click="handleDeleteItem">Xóa</b-dropdown-item>
                                <b-dropdown-item @click="handleCopyItem">Nhân bản</b-dropdown-item>
                                <b-dropdown-item>Chia sẻ</b-dropdown-item>
                                <b-dropdown-item>Chat</b-dropdown-item>
                                <b-dropdown-item>Zalo</b-dropdown-item>
                                <b-dropdown-item>Phân quyền</b-dropdown-item>
                            </b-dropdown>
                        </div>
                    </b-col>
                  <b-col class="col-md-12 col-sm-12 col-24">
                        <div class="main-header-item main-header-icons">
                            <div class="main-header-item-counter ml-auto mr-3" v-if="reqParams">
                                <span>{{itemNo}} - {{reqParams.idsArray.length + this.reqParams.perPage * (this.reqParams.currentPage - 1)}}</span>
                                /
                                <span>{{reqParams.total}}</span>
                            </div>
                            <b-button-group id="main-header-views" class="main-header-views">
                                <b-button id="tooltip-view-prev" @click="onNavigationItem('prev')" v-if="reqParams" class="main-header-view"><i class="fa fa-angle-left"></i></b-button>
                                <b-button id="tooltip-view-next" @click="onNavigationItem('next')" v-if="reqParams" class="main-header-view"><i class="fa fa-angle-right"></i></b-button>
                                <b-button id="tooltip-view-back-to-list" @click="onBackToList" class="main-header-view"><i class="fa fa-list"></i></b-button>
                                <b-tooltip container="main-header-views" target="tooltip-view-back-to-list">Danh sách</b-tooltip>
                                <b-tooltip container="main-header-views" target="tooltip-view-prev">Trước</b-tooltip>
                                <b-tooltip container="main-header-views" target="tooltip-view-next">Sau</b-tooltip>
                            </b-button-group>
                            <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
                                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen=true />
                            </div>
                        </div>
                    </b-col>
                </b-row>
            </div>

        </div>
        <div class="main-body main-body-view-action">
            <vue-perfect-scrollbar class="scroll-area" :settings="$store.state.psSettings">
                <div class="container-fluid">
                    <b-card>
                        <b-card no-body class="content-body">
                            <b-tabs card>
                                <b-tab title="Thông tin chung" active>

                                    <b-row class="mb-3">
                                        <b-col class="col-md-4 col-sm-8 mb-sm-0 mb-md-0 col-12 mb-2"><span>Tài khoản đăng nhập</span></b-col>
                                        <b-col class="col-md-20 col-sm-16 col-12"><span>{{model.information.username}}</span></b-col>
                                    </b-row>

                                    <b-row class=" mb-3">
                                        <b-col class="col-md-4 col-sm-8 mb-sm-0 mb-md-0 col-12 mb-2"><span>Họ và tên</span></b-col>
                                        <b-col class="col-md-20 col-sm-16 col-12"><span>{{model.information.fullName}}</span></b-col>
                                    </b-row>

                                    <b-row class=" mb-3">
                                        <b-col class="col-md-4 col-sm-8 mb-sm-0 mb-md-0 col-12 mb-2"><span>Ghi chú</span></b-col>
                                        <b-col class="col-md-20 col-sm-16 col-12"><span>{{model.information.note}}</span></b-col>
                                    </b-row>
                                </b-tab>
                                <b-tab title="Phân quyền tính năng">
                                    <b-card-text>
                                      <div class="table-responsive">
<!--                                        <table class="table b-table table-striped table-hover table-bordered table-sm b-table-fixed table-permission-features">-->
                                        <table class="table b-table table-sm table-bordered table-permission-features">
                                          <thead>
                                          <tr class="text-center">
                                            <th scope="col" style="width: 68%; min-width: 300px">Tên</th>
                                            <th scope="col" style="width: 8%; min-width: 80px">Truy cập</th>
                                            <th scope="col" style="width: 8%; min-width: 80px">Thêm</th>
                                            <th scope="col" style="width: 8%; min-width: 80px">Sửa</th>
                                            <th scope="col" style="width: 8%; min-width: 80px">Xóa</th>
                                          </tr>
                                          </thead>
                                          <tbody>

                                          <tr role="row" class="bg-tree-tr show-child" :data-feature-id="item.FeatureID" :data-parent-id="item.ParentID" :class="[item.ParentID ? 'feature-child' : 'feature-parent']" v-for="(item, key) in model.features.items">
                                            <td role="cell" :class="[item.ParentID ? 'bg-tree-td' : 'bg-tree-td-parent']">
                                              <i class="pl-2 pr-2 fa fa-minus-square" @click="onToggleChildNodes(item.FeatureID)" v-if="!item.ParentID" style="cursor: pointer"></i>
                                              <span>{{item.FeatureName}}</span>
                                            </td>
                                            <td role="cell" class="text-center">
                                              <b-form-checkbox
                                                v-model="model.features.items[key].Access"
                                                disabled></b-form-checkbox>
                                            </td>
                                            <td role="cell" class="text-center">
                                              <b-form-checkbox
                                                v-model="model.features.items[key].Addnew"
                                                disabled></b-form-checkbox>
                                            </td>
                                            <td role="cell" class="text-center">
                                              <b-form-checkbox
                                                v-model="model.features.items[key].Edit"
                                                disabled></b-form-checkbox>
                                            </td>
                                            <td role="cell" class="text-center">
                                              <b-form-checkbox
                                                v-model="model.features.items[key].Delete"
                                                disabled>
                                              </b-form-checkbox>
                                            </td>
                                          </tr>

                                          </tbody>
                                        </table>
                                      </div>
                                    </b-card-text>
                                </b-tab>
                                <b-tab title="Phân quyền báo cáo">
                                    <b-card-text>

                                    </b-card-text>
                                </b-tab>
                            </b-tabs>
                        </b-card>
                    </b-card>
                </div>
            </vue-perfect-scrollbar>
        </div>
    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import Swal from 'sweetalert2';
    import 'sweetalert2/src/sweetalert2.scss';

    const ListRouter = 'sysadmin-user-list';
    const EditRouter = 'sysadmin-user-edit';
    const DeleteApi = 'sysadmin/api/users/delete';
    const CreateRouter = 'sysadmin-user-create';
    const ViewApi = 'sysadmin/api/users/view';

    String.prototype.capitalize = function () {
        return this.charAt(0).toUpperCase() + this.slice(1);
    };

    export default {
        name: 'FormUser',
        data () {
            return {
                idParams: this.$route.params.id,
                reqParams: this.$route.params.req,
                model: {
                    information: {
                        username: '',
                        password: '',
                        confirmPassword: '',
                        fullName: '',
                        groupUsers: [],
                        note: '',
                        numberOrder: '',
                        inactive: false
                    },
                    features: {
                        selectAll: {
                            checked: false,
                            access: false,
                            add: false,
                            edit: false,
                            delete: false
                        },
                        items: []
                    },
                    reports: {}
                },
                defaultModel: {},
                groupUsers: [],
                stage: {
                    updatedData: false,
                  message: (this.$route.params.message) ? this.$route.params.message : ''
                }
            }

        },

        components: {
        },
        beforeCreate(){
            if (!this.$route.params.id) {
                this.$router.push({name: 'sysadmin-users'});
            }
        },
        mounted() {
            this.fetchData();
          // hiển thị thông báo
          if (this.stage.message !== '') {
            this.$bvToast.toast(this.stage.message, {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });
          }
        },
        updated() {
        },
        computed: {
            itemNo(){
                let index = 0;
                index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
                return index;
            }
        },
        methods: {
            fetchData() {
                if (this.idParams === 0 || _.isUndefined(this.idParams)) {
                    return false;
                }
                let self = this;
                let urlApi = '';
                let requestData = {
                    method: 'get',
                };
                // Api edit user
                if(this.idParams){
                    urlApi = ViewApi + '/' + this.idParams;
                    let data = {
                        id: this.idParams
                    };
                    requestData.data = data;
                }
                requestData.url = urlApi;
                this.$store.commit('isLoading', true);
                ApiService.setHeader();
                ApiService.customRequest(requestData).then((responses) => {
                    let responsesData = responses.data;
                    if (responsesData.status === 1) {
                        self.defaultModel = responsesData.data;
                        self.setDefaultInformation();
                        self.setDefaultFeatures();
                        self.setGroupUsers();
                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                });
            },
            setDefaultInformation() {

                if (!_.isUndefined(this.defaultModel.data)) {
                    this.model.information.username = this.defaultModel.data.username;
                    this.model.information.password = this.defaultModel.data.password;
                    this.model.information.confirmPassword = this.defaultModel.data.password;
                    this.model.information.fullName = this.defaultModel.data.FullName;
                    this.model.information.numberOrder = this.defaultModel.data.NOrder;
                    this.model.information.inactive = (this.defaultModel.data.Inactive == 1) ? true : false;
                    this.model.information.note = this.defaultModel.data.Note;
                }

                return;
            },
            setDefaultFeatures(){
                this.model.features.items = [];
                let self = this;
                _.forEach(this.defaultModel.feature, function (item, key) {
                    let tmpObj = item;
                    tmpObj.Access = (item.Access == 1) ? true : false;
                    tmpObj.Addnew = (item.Addnew == 1) ? true : false;
                    tmpObj.Edit = (item.Edit == 1) ? true : false;
                    tmpObj.Delete = (item.Delete == 1) ? true : false;
                    self.model.features.items.push(tmpObj);

                });
            },
            setGroupUsers() {
                let self = this;
                _.forEach(this.defaultModel.group, function (groupUser, key) {
                    if (groupUser.UserGroupID && groupUser.UserGroupName) {
                        let tmpObj = {};
                        tmpObj.value = groupUser.UserGroupID;
                        tmpObj.label = groupUser.UserGroupName;
                        if (groupUser.groupSelect) {
                            self.model.information.groupUsers.push(tmpObj);
                        }
                        self.groupUsers.push(tmpObj);
                    }
                });

            },
            onToggleChildNodes(parentID) {
                let childEl = document.querySelectorAll('[data-parent-id="' + parentID + '"]'),
                    parentEl = document.querySelectorAll('[data-feature-id="' + parentID + '"]'),
                    iconToggle = document.querySelectorAll('[data-feature-id="' + parentID + '"] i');

                if (!childEl.length) return;

                if (parentEl[0].classList.contains('show-child')) {
                    _.forEach(childEl, function (child, key) {
                        childEl[key].classList.add('d-none');
                    });
                    parentEl[0].classList.remove('show-child');
                    iconToggle[0].classList.add('fa-plus-square');
                    iconToggle[0].classList.remove('fa-minus-square');
                }else {
                    _.forEach(childEl, function (child, key) {
                        childEl[key].classList.remove('d-none');
                    });

                        parentEl[0].classList.add('show-child');
                        iconToggle[0].classList.remove('fa-plus-square');
                        iconToggle[0].classList.add('fa-minus-square');
                }
            },
            onEditClicked(){
                // let router = EditRouter;
                // this.$router.push({path: router});
                this.$router.push({
                    name: EditRouter,
                    params: {id: this.idParams, req: this.reqParams}});
            },
            onCreateClicked(){
                this.$router.push({name: CreateRouter});
            },
            onBackToList(message = '') {
              if (_.isString(message)) {
                  this.$router.push({name: ListRouter, params: {message: message}});
              } else {
                  this.$router.push({name: ListRouter});
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
                    url: 'sysadmin/api/users',
                    data: {
                        per_page: this.reqParams.perPage,
                        page: this.reqParams.currentPage,
                        type: 'only-id'
                    }
                };

                if (this.reqParams.search.FullName !== '') {
                    requestData.data.FullName = this.reqParams.search.FullName;
                }
                if (this.reqParams.search.username !== '') {
                    requestData.data.username = this.reqParams.search.username;
                }
                if (this.reqParams.search.Inactive !== -1) {
                    requestData.data.Inactive = this.reqParams.search.Inactive;
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
                            self.reqParams.idsArray.push(value.UserID);
                        });

                        (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                });

            },
            onToggleDropdownSubMenu(event){
                __.onToggleDropdownSubMenu(event);
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
                                console.log(response);
                            }
                        }, (error) => {
                            console.log(error);

                        });

                    }
                });
            },
            handleCopyItem(){
                this.$router.push({name: CreateRouter, params: {itemCopy: this.defaultModel}});
            },
            updateModel() {
                if (this.stage.updatedData) {
                    this.$forceUpdate();
                }
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
    .table-permission-features .form-group{
        margin: 0;
    }

    .feature-child td:first-child{
        padding-left: 35px;
    }
    .feature-parent td:first-child{
        /*padding-left: 11px;*/
    }

    .custom-control-label::before, .custom-control-label::after, .custom-control-label {
        cursor: pointer;
    }

    .bg-tree-tr{
        background-image: url("http://pabmis.vn/demo2/style/treeview/images/treeview-default-line.gif");
        background-position: 5px;
        background-repeat: no-repeat;
    }
    .bg-tree-td:before{
        display: inline-block;
        content: "";
        position: relative;
        top: -4px;
        left: -7px;
        width: 16px;
        height: 0;
        border-top: 1px dotted #858585;
        z-index: 1;
    }
    .bg-tree-td-parent:before{
        display: inline-block;
        content: "";
        position: relative;
        top: -4px;
        left: 8px;
        width: 6px;
        height: 0;
        border-top: 1px dotted #858585;
        z-index: 1;
    }
    .bg-tree-td{
        background-image: url("http://pabmis.vn/demo2/style/treeview/images/treeview-default-line.gif");
        background-position: 20px;
        background-repeat: no-repeat;
    }
</style>

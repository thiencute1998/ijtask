<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Nhóm người dùng<span v-if="model.information.groupUserName">:</span> {{model.information.groupUserName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Nhóm người dùng<span v-if="model.information.groupUserName">:</span> {{model.information.groupUserName}}</span>
                        </div>
                    </b-col>
                    <b-col class="col-md-12"></b-col>
                </b-row>
                <b-row>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-actions">
                            <b-button type="submit" variant="primary" class="mr-2" @click="handleSubmitForm"><i class="fa fa-check-circle"></i> Lưu</b-button>
                            <b-button type="reset" variant="primary" @click="onBackToList"><i class="fa fa-ban"></i> Hủy</b-button>
                        </div>
                    </b-col>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-icons">
                            <div class="main-header-item main-header-icons">
<!--                                <div class="main-header-item-counter ml-auto mr-3" v-if="idParams">-->
<!--                                    <span>{{itemNo}} - {{reqParams.idsArray.length + this.reqParams.perPage * (this.reqParams.currentPage - 1)}}</span>-->
<!--                                    /-->
<!--                                    <span>{{reqParams.total}}</span>-->
<!--                                </div>-->
<!--                                <b-button-group id="main-header-views" class="main-header-views mr-2" v-if="idParams">-->
<!--                                    <b-button id="tooltip-view-prev" @click="onNavigationItem('prev')" class="main-header-view"><i class="fa fa-angle-left"></i></b-button>-->
<!--                                    <b-button id="tooltip-view-next" @click="onNavigationItem('next')" class="main-header-view"><i class="fa fa-angle-right"></i></b-button>-->
<!--                                    <b-button id="tooltip-view-back-to-list" @click="onBackToList" class="main-header-view"><i class="fa fa-list"></i></b-button>-->
<!--                                    <b-tooltip container="main-header-views" target="tooltip-view-back-to-list">Danh sách</b-tooltip>-->
<!--                                    <b-tooltip container="main-header-views" target="tooltip-view-prev">Trước</b-tooltip>-->
<!--                                    <b-tooltip container="main-header-views" target="tooltip-view-next">Sau</b-tooltip>-->
<!--                                </b-button-group>-->

<!--                                <div v-if="!idParams">-->
<!--                                    <b-button id="tooltip-view-back-to-list" @click="onBackToList" class="main-header-view mr-2"><i class="fa fa-list"></i></b-button>-->
<!--                                    <b-tooltip container="tooltip-view-back-to-list" target="tooltip-view-back-to-list">Danh sách</b-tooltip>-->
<!--                                </div>-->

                                <div class="main-header-collapse">
                                    <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen=true />
                                </div>
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
                        <b-card no-body class="ij-main-body">
                            <b-tabs card>
                                <b-tab title="Thông tin chung" active>
                                    <b-row>
                                        <b-col class="col-sm-12">
                                            <b-form-group label="Tên nhóm" label-for="groupUserName" :label-cols="4">
                                                <b-form-input
                                                        id="groupUserName"
                                                        v-model.trim="$v.model.information.groupUserName.$model"
                                                        :state="chkState('groupUserName')"
                                                        type="text"
                                                        placeholder="Tên nhóm"
                                                        aria-describedby="inputLiveFeedbackGroupUserName"
                                                        autocomplete="username email"></b-form-input>
                                                <b-form-invalid-feedback id="inputLiveFeedbackGroupUserName">
                                                    Tài khoản đăng nhập tối thiều 3 kí tự
                                                </b-form-invalid-feedback>
                                            </b-form-group>

                                            <b-form-group label="Loại người dùng" label-for="groupUserType" :label-cols="4">
                                                <b-form-select
                                                        v-model="model.information.groupUserType"
                                                        :options="groupOptions"
                                                        @input="onChangeGroupOptions"
                                                        placeholder="Chọn loại nhóm người dùng">
                                                </b-form-select>
                                            </b-form-group>

                                            <b-form-group label="Ghi chú" label-for="note" :label-cols="4">
                                                <b-form-textarea
                                                        id="note"
                                                        v-model="model.information.note"
                                                        placeholder="Ghi chú"
                                                ></b-form-textarea>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                </b-tab>
                                <b-tab title="Phân quyền tính năng">
                                    <b-card-text>
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
                                            <!-- chọn tất-->
                                                <tr role="row" class=first-row>
                                                  <td role="cell">
                                                    <b-form-group label-for="featuresSelectAll" class="d-inline-flex">
                                                        <b-form-checkbox
                                                          v-model="model.features.selectAll.checked"
                                                          @input="toggleCheckedChildNodesPermission($event, 'features', 'selectAll')"
                                                          id="featuresSelectAll">Chọn tất</b-form-checkbox>
                                                    </b-form-group>
                                                  </td>
                                                  <td role="cell" class="text-center">
                                                    <b-form-checkbox
                                                      v-model="model.features.selectAll.access"
                                                      @input="toggleCheckedChildNodesPermission($event, 'features', 'selectAllAccess')"
                                                      id="featuresSelectAllAccess"></b-form-checkbox>
                                                  </td>
                                                  <td role="cell" class="text-center">
                                                    <b-form-checkbox
                                                      v-model="model.features.selectAll.add"
                                                      :disabled="!model.features.selectAll.access"
                                                      @input="toggleCheckedChildNodesPermission($event, 'features', 'selectAllAdd')"
                                                      id="featuresSelectAllAdd"></b-form-checkbox>
                                                  </td>
                                                  <td role="cell" class="text-center">
                                                    <b-form-checkbox
                                                      v-model="model.features.selectAll.edit"
                                                      :disabled="!model.features.selectAll.access"
                                                      @input="toggleCheckedChildNodesPermission($event, 'features', 'selectAllEdit')"
                                                      id="featuresSelectAllEdit"></b-form-checkbox>
                                                  </td>
                                                  <td role="cell" class="text-center">
                                                    <b-form-checkbox
                                                      v-model="model.features.selectAll.delete"
                                                      :disabled="!model.features.selectAll.access"
                                                      @input="toggleCheckedChildNodesPermission($event, 'features', 'selectAllDelete')"
                                                      id="featuresSelectAllDelete"></b-form-checkbox>
                                                  </td>
                                                </tr>

                                            <!--                                            foreach-->
                                              <tr role="row" class="bg-tree-tr show-child" :data-feature-id="item.FeatureID" :data-parent-id="item.ParentID" :class="[item.ParentID ? 'feature-child' : 'feature-parent']" v-for="(item, key) in model.features.items">
                                                <td role="cell" :class="[item.ParentID ? 'bg-tree-td' : 'bg-tree-td-parent']">
                                                  <div class="d-inline-flex align-items-center">
                                                    <i class="pl-2 pr-2 fa fa-minus-square" @click="onToggleChildNodes(item.FeatureID)" v-if="!item.ParentID" style="cursor: pointer"></i>
                                                    <!--                                                    <span>{{item.FeatureName}}</span>-->
                                                    <b-form-group label="" class="d-inline-flex">
                                                      <b-form-checkbox v-model="model.features.items[key].SelectAll" @input="toggleCheckedChildNodesPermission($event, 'features', 'selectItem', item)">{{item.FeatureName}}</b-form-checkbox>
                                                    </b-form-group>
                                                  </div>
                                                </td>
                                                <td role="cell" class="text-center">
                                                  <b-form-checkbox
                                                    v-model="model.features.items[key].Access"
                                                    :disabled="model.features.items[key].isDisableAccess"
                                                    @input="toggleCheckedChildNodesPermission($event, 'features', 'selectItemAccess', item)"></b-form-checkbox>
                                                </td>
                                                <td role="cell" class="text-center">
                                                  <b-form-checkbox
                                                    v-model="model.features.items[key].Addnew"
                                                    :disabled="!model.features.items[key].Access || model.features.items[key].isDisableAddnew"
                                                     @input="toggleCheckedChildNodesPermission($event, 'features', 'selectItemAddnew', item)"></b-form-checkbox>
                                                </td>
                                                <td role="cell" class="text-center">
                                                  <b-form-checkbox
                                                    v-model="model.features.items[key].Edit"
                                                    :disabled="!model.features.items[key].Access || model.features.items[key].isDisableEdit"
                                                     @input="toggleCheckedChildNodesPermission($event, 'features', 'selectItemEdit', item)"></b-form-checkbox>
                                                </td>
                                                <td role="cell" class="text-center">
                                                  <b-form-checkbox
                                                    v-model="model.features.items[key].Delete"
                                                    :disabled="!model.features.items[key].Access || model.features.items[key].isDisableDelete"
                                                    @input="toggleCheckedChildNodesPermission($event, 'features', 'selectItemDelete', item)"></b-form-checkbox>
                                                </td>
                                              </tr>

                                            </tbody>
                                        </table>

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
<!--    <div>-->
<!--    </div>-->
</template>

<script>
    import ApiService from '@/services/api.service';
    import vSelect from 'vue-select';
    import { validationMixin } from "vuelidate";
    import { required, minLength, email, sameAs, helpers } from "vuelidate/lib/validators";
    import Swal from 'sweetalert2';
    import 'sweetalert2/src/sweetalert2.scss';

    String.prototype.capitalize = function () {
        return this.charAt(0).toUpperCase() + this.slice(1);
    };

    // Required password containing at least: number, uppercase and lowercase letter, 8 characters
    // const strongPass = helpers.regex('strongPass', /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/);

    // STRONG PASSWORD
    // Must have capital letter, numbers and lowercase letters
    const strongPass = new RegExp("^(?=.{8,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\\W).*$", "g");

    // MEDIUM PASSWORD
    // Must have either capitals and lowercase letters or lowercase and numbers
    const mediumPass = new RegExp("^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");

    // WEAK PASSWORD
    // Must be at least 6 characters long
    const weakPass =  new RegExp("(?=.{6,}).*", "g");

    const CreateApi = 'sysadmin/api/group-user/create';
    const EditApi = 'sysadmin/api/group-user/edit';
    const StoreApi = 'sysadmin/api/group-user/store';
    const UpdateApi = 'sysadmin/api/group-user/update';
    const ListRouter = 'sysadmin-group-user';
    const ViewRouter = 'sysadmin-group-user-view';

    const groupOptions = [
        {value: null, text: 'Chọn loại nhóm người dùng'},
        {value: 1, text: 'Người quản trị'},
        {value: 2, text: 'Người tác nghiệp'},
        {value: 3, text: 'Người khai thác'},
        {value: 4, text: 'Người Kiểm tra'},
        {value: 5, text: 'Khách'},
    ];

    export default {
        name: 'FormGroupUser',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    information: {
                        groupUserName: '',
                        groupUserType: null,
                        note: ''
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
                stage: {
                    updatedData: false,
                    disabledFeature: false
                }
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
            itemCopy: {
                type: Object,
                default: function () {
                    return {}
                }
            }
        },
        mixins: [validationMixin],
        validations: {
            model: {
                information: {
                    groupUserName: {
                        required,
                        minLength: minLength(3)
                    }
                }
            }
        },
        components: {
            // Multiselect,
            vSelect,
        },
        beforeCreate() {},
        mounted() {
            this.fetchData();
        },
        updated() {
            this.stage.updatedData = true;
        },
        computed: {
            formStr() { return JSON.stringify(this.model.information, null, 4) },
            isValid() { return !this.$v.model.information.$anyError },
            isDirty() { return this.$v.model.information.$anyDirty },
            invCheck() { return 'You must accept before submitting' },
            groupOptions() {
                return groupOptions;
            },
            itemNo(){
                let index = 0;
                index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
                return index;
            }
        },
        methods: {
            fetchData() {
                let self = this;
                let urlApi = CreateApi;
                let requestData = {
                    method: 'get',
                };

                // Api edit user
                if(this.idParams){
                    urlApi = EditApi + '/' + this.idParams;
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
                        // copy item
                        if (!self.idParams && !_.isEmpty(self.itemCopy)) {
                            self.defaultModel.data = self.itemCopy.data;
                            self.defaultModel.feature = self.itemCopy.feature;
                        }

                        self.setDefaultInformation();
                        self.setDefaultFeatures();
                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                });
            },
            setDefaultInformation() {

                if (!_.isUndefined(this.defaultModel.data)) {
                    this.model.information.groupUserName = this.defaultModel.data.UserGroupName;
                    this.model.information.groupUserType = this.defaultModel.data.UserGroupType;
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
                    tmpObj.SelectAll = false;
                    tmpObj.isDisableAccess = false;
                    tmpObj.isDisableAddnew = false;
                    tmpObj.isDisableEdit = false;
                    tmpObj.isDisableDelete = false;
                    self.model.features.items.push(tmpObj);
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
            toggleCheckedChildNodesPermission(value, typePermission, typeChecked, itemFeature = null) {
                let self = this;
                if (this.stage.updatedData) {
                    if (typePermission === 'features') {
                        // type check all
                        if (typeChecked === 'selectAll') {
                            _.forEach(this.model.features.items, function (item, key) {
                                if(!self.model.features.items[key].isDisableAccess) self.model.features.items[key].Access = value;
                                if(!self.model.features.items[key].isDisableAddnew) self.model.features.items[key].Addnew = value;
                                if(!self.model.features.items[key].isDisableEdit) self.model.features.items[key].Edit = value;
                                if(!self.model.features.items[key].isDisableDelete) self.model.features.items[key].Delete = value;
                                self.model.features.items[key].SelectAll = value;
                            });

                            this.model.features.selectAll.access = value;
                            this.model.features.selectAll.edit = value;
                            this.model.features.selectAll.add = value;
                            this.model.features.selectAll.delete = value;
                        }
                        if (typeChecked === 'selectAllAccess') {
                            _.forEach(this.model.features.items, function (item, key) {
                                if(!self.model.features.items[key].isDisableAccess) self.model.features.items[key].Access = value;
                            });
                            if (!value) {
                                this.model.features.selectAll.add = false;
                                this.model.features.selectAll.edit = false;
                                this.model.features.selectAll.delete = false;
                            }
                        }
                        if (typeChecked === 'selectAllAdd') {
                            _.forEach(this.model.features.items, function (item, key) {
                                if(!self.model.features.items[key].isDisableAddnew) self.model.features.items[key].Addnew = value;
                            });
                        }
                        if (typeChecked === 'selectAllEdit') {
                            _.forEach(this.model.features.items, function (item, key) {
                                if(!self.model.features.items[key].isDisableEdit) self.model.features.items[key].Edit = value;
                            });
                        }
                        if (typeChecked === 'selectAllDelete') {
                            _.forEach(this.model.features.items, function (item, key) {
                                if(!self.model.features.items[key].isDisableDelete) self.model.features.items[key].Delete = value;
                            });
                        }

                        if (typeChecked === 'selectItem') {
                            if (itemFeature.ParentID) {
                                let _index = _.findIndex(this.model.features.items, ['FeatureID', itemFeature.FeatureID]);
                                if(!self.model.features.items[_index].isDisableAccess) self.model.features.items[_index].Access = value;
                                if(!self.model.features.items[_index].isDisableAddnew) self.model.features.items[_index].Addnew = value;
                                if(!self.model.features.items[_index].isDisableEdit) self.model.features.items[_index].Edit = value;
                                if(!self.model.features.items[_index].isDisableDelete) self.model.features.items[_index].Delete = value;
                            } else {
                                let itemsChild = _.filter(this.model.features.items, ['ParentID', itemFeature.FeatureID]);
                                _.forEach(itemsChild, function (itemChild, key) {
                                    let _index = _.findIndex(self.model.features.items, ['FeatureID', itemChild.FeatureID]);

                                    if(!self.model.features.items[_index].isDisableAccess) self.model.features.items[_index].Access = value;
                                    if(!self.model.features.items[_index].isDisableAddnew) self.model.features.items[_index].Addnew = value;
                                    if(!self.model.features.items[_index].isDisableEdit) self.model.features.items[_index].Edit = value;
                                    if(!self.model.features.items[_index].isDisableDelete) self.model.features.items[_index].Delete = value;

                                    self.model.features.items[_index].SelectAll = value;
                                });
                                let _index = _.findIndex(this.model.features.items, ['FeatureID', itemFeature.FeatureID]);

                                if(!self.model.features.items[_index].isDisableAccess) self.model.features.items[_index].Access = value;
                                if(!self.model.features.items[_index].isDisableAddnew) self.model.features.items[_index].Addnew = value;
                                if(!self.model.features.items[_index].isDisableEdit) self.model.features.items[_index].Edit = value;
                                if(!self.model.features.items[_index].isDisableDelete) self.model.features.items[_index].Delete = value;

                                this.model.features.items[_index].SelectAll = value;

                            }
                        }
                        if (typeChecked === 'selectItemAccess') {
                            if (!itemFeature.ParentID) {
                                let itemsChild = _.filter(this.model.features.items, ['ParentID', itemFeature.FeatureID]);
                                _.forEach(itemsChild, function (itemChild, key) {
                                    let _index = _.findIndex(self.model.features.items, ['FeatureID', itemChild.FeatureID]);
                                    if (!self.model.features.items[_index].isDisableAccess) self.model.features.items[_index].Access = value;
                                });
                            }
                            if (!value) {
                                let _index = _.findIndex(this.model.features.items, ['FeatureID', itemFeature.FeatureID]);
                                if (!self.model.features.items[_index].isDisableAddnew) this.model.features.items[_index].Addnew = false;
                                if (!self.model.features.items[_index].isDisableEdit) this.model.features.items[_index].Edit = false;
                                if (!self.model.features.items[_index].isDisableDelete) this.model.features.items[_index].Delete = false;
                            }
                        }
                        if (typeChecked === 'selectItemAddnew') {
                            if (!itemFeature.ParentID) {
                                let itemsChild = _.filter(this.model.features.items, ['ParentID', itemFeature.FeatureID]);
                                _.forEach(itemsChild, function (itemChild, key) {
                                    let _index = _.findIndex(self.model.features.items, ['FeatureID', itemChild.FeatureID]);
                                    if (!self.model.features.items[_index].isDisableAddnew) self.model.features.items[_index].Addnew = value;
                                });
                            }
                        }
                        if (typeChecked === 'selectItemEdit') {
                            if (!itemFeature.ParentID) {
                                let itemsChild = _.filter(this.model.features.items, ['ParentID', itemFeature.FeatureID]);
                                _.forEach(itemsChild, function (itemChild, key) {
                                    let _index = _.findIndex(self.model.features.items, ['FeatureID', itemChild.FeatureID]);
                                    if (!self.model.features.items[_index].isDisableEdit) self.model.features.items[_index].Edit = value;
                                });
                            }
                        }
                        if (typeChecked === 'selectItemDelete') {
                            if (!itemFeature.ParentID) {
                                let itemsChild = _.filter(this.model.features.items, ['ParentID', itemFeature.FeatureID]);
                                _.forEach(itemsChild, function (itemChild, key) {
                                    let _index = _.findIndex(self.model.features.items, ['FeatureID', itemChild.FeatureID]);
                                    if (!self.model.features.items[_index].isDisableDelete) self.model.features.items[_index].Delete = value;
                                });
                            }
                        }
                    }
                }
            },
            handleCheckedChildNodesAccess(type, parentNode, node) {
                if (type === 'features') {
                    if (_.isObject(this.model.features[parentNode].childNodes[node])) {
                        if (!this.model.features[parentNode].childNodes[node].access) {
                            if (!this.model.features[parentNode].childNodes[node].isDisableAdd) this.model.features[parentNode].childNodes[node].add = false;
                            if (!this.model.features[parentNode].childNodes[node].isDisableEdit) this.model.features[parentNode].childNodes[node].edit = false;
                            if (!this.model.features[parentNode].childNodes[node].isDisableDelete) this.model.features[parentNode].childNodes[node].delete = false;
                        }
                    }
                }
            },
            handleSubmitForm(){
                let self = this;
                let features = [];
                let information = {
                    UserGroupName: this.model.information.groupUserName,
                    UserGroupType: this.model.information.groupUserType,
                    Note: this.model.information.note
                };
                _.forEach(this.model.features.items, function (item, key) {
                    let tmpObj = {};
                    tmpObj.FeatureID = item.FeatureID;
                    tmpObj.FeatureKey = item.FeatureKey;
                    tmpObj.FeatureName = item.FeatureName;
                    tmpObj.ParentID = item.ParentID;
                    tmpObj.Access = (item.Access) ? 1 : 0;
                    tmpObj.Addnew = (item.Addnew) ? 1 : 0;
                    tmpObj.Edit = (item.Edit) ? 1 : 0;
                    tmpObj.Delete = (item.Delete) ? 1 : 0;
                    features.push(tmpObj);
                });

                const requestData = {
                    method: 'post',
                    url: StoreApi,
                    data: {
                        information: information,
                        features: features,
                        reports: this.model.reports
                    }
                };

                // edit user
                if (this.idParams) {
                    requestData.data.UserGroupID = this.idParams;
                    requestData.url = UpdateApi + '/' + this.idParams;
                }

                ApiService.setHeader();
                ApiService.customRequest(requestData).then((responses) => {
                    let responsesData = responses.data;
                    if (responsesData.status === 1) {
                      if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
                      self.$router.push({
                        name: ViewRouter,
                        params: {id: self.idParams, message: 'Bản ghi đã được cập nhật!'}
                      });
                    } else {
                        let htmlErrors = __.renderErrorApiHtml(responsesData.data);
                        Swal.fire(
                            'Thông báo',
                            htmlErrors,
                            'error'
                        )
                    }
                }, (error) => {
                    console.log(error);
                    Swal.fire(
                        'Thông báo',
                        'Không kết nối được với máy chủ',
                        'error'
                    )
                });
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
                    url: 'sysadmin/api/group-user',
                    data: {
                        per_page: this.reqParams.perPage,
                        page: this.reqParams.currentPage,
                        type: 'only-id'
                    }
                };

                if (this.reqParams.search.UserGroupName !== '') {
                    requestData.data.UserGroupName = this.reqParams.search.UserGroupName;
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
                            self.reqParams.idsArray.push(value.UserGroupID);
                        });

                        (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                });

            },

            onBackToList() {
                this.$router.push({name: ListRouter});
            },

            // validate
            chkState(val) {
                const field = this.$v.model.information[val];
                // return !field.$dirty || !field.$invalid;
                return (!field.$dirty) ? null : !field.$invalid;
            },
            validate() {
                this.$v.$touch();
                this.$nextTick(() => this.findFirstError());
                return this.isValid;
            },
            updateModel() {
                if (this.stage.updatedData) {
                    this.$forceUpdate();
                }
            },

            onChangeGroupOptions() {
                let self = this;
                console.log(this.model.features.items);
                let _index = null;
                let _categoryID = null;
                let _sysadminID = null;
                let itemsChild = [];
                switch (this.model.information.groupUserType) {
                    case 1:
                        // Nhóm người quản trị

                        _index = _.findIndex(self.model.features.items, ['FeatureKey', 'SYSADMIN']);
                        _sysadminID = this.model.features.items[_index].FeatureID;
                        if (this.model.features.items[_index]) {
                            this.model.features.items[_index].Access = true;
                            this.model.features.items[_index].Addnew = true;
                            this.model.features.items[_index].Edit = true;
                            this.model.features.items[_index].Delete = true;
                            this.model.features.items[_index].isDisableAccess = true;
                            this.model.features.items[_index].isDisableAddnew = true;
                            this.model.features.items[_index].isDisableEdit = true;
                            this.model.features.items[_index].isDisableDelete = true;
                        }

                        _index = _.findIndex(self.model.features.items, ['FeatureKey', 'CATEGORY']);
                        _categoryID = this.model.features.items[_index].FeatureID;
                        if (this.model.features.items[_index]) {
                            this.model.features.items[_index].Access = true;
                            this.model.features.items[_index].Addnew = true;
                            this.model.features.items[_index].Edit = true;
                            this.model.features.items[_index].Delete = true;
                            this.model.features.items[_index].isDisableAccess = true;
                            this.model.features.items[_index].isDisableAddnew = true;
                            this.model.features.items[_index].isDisableEdit = true;
                            this.model.features.items[_index].isDisableDelete = true;
                        }

                        itemsChild = _.filter(this.model.features.items, ['ParentID', _sysadminID]);
                        _.forEach(itemsChild, function (itemChild, key) {
                            let _indexChild = _.findIndex(self.model.features.items, ['FeatureID', itemChild.FeatureID]);
                            self.model.features.items[_indexChild].Access = true;
                            self.model.features.items[_indexChild].Addnew = true;
                            self.model.features.items[_indexChild].Edit = true;
                            self.model.features.items[_indexChild].Delete = true;
                            self.model.features.items[_indexChild].isDisableAccess = true;
                            self.model.features.items[_indexChild].isDisableAddnew = true;
                            self.model.features.items[_indexChild].isDisableEdit = true;
                            self.model.features.items[_indexChild].isDisableDelete = true;
                        });

                        itemsChild = _.filter(this.model.features.items, ['ParentID', _categoryID]);
                        _.forEach(itemsChild, function (itemChild, key) {
                            let _indexChild = _.findIndex(self.model.features.items, ['FeatureID', itemChild.FeatureID]);
                            self.model.features.items[_indexChild].Access = true;
                            self.model.features.items[_indexChild].Addnew = true;
                            self.model.features.items[_indexChild].Edit = true;
                            self.model.features.items[_indexChild].Delete = true;
                            self.model.features.items[_indexChild].isDisableAccess = true;
                            self.model.features.items[_indexChild].isDisableAddnew = true;
                            self.model.features.items[_indexChild].isDisableEdit = true;
                            self.model.features.items[_indexChild].isDisableDelete = true;
                        });

                        _index = _.findIndex(self.model.features.items, ['FeatureKey', 'CALENDAR']);
                        if (this.model.features.items[_index]) this.model.features.items[_index].Access = true;

                        _index = _.findIndex(self.model.features.items, ['FeatureKey', 'TASK']);
                        if (this.model.features.items[_index]) this.model.features.items[_index].Access = true;

                        break;

                    case 2:
                        // Nhóm người tác nghiệp
                        _index = _.findIndex(self.model.features.items, ['FeatureKey', 'CALENDAR']);
                        if (this.model.features.items[_index]) {
                            this.model.features.items[_index].Access = true;
                            this.model.features.items[_index].isDisableAccess = true;
                        }

                        _index = _.findIndex(self.model.features.items, ['FeatureKey', 'TASK']);
                        if (this.model.features.items[_index]) {
                            this.model.features.items[_index].Access = true;
                            this.model.features.items[_index].isDisableAccess = true;
                        }

                        _index = _.findIndex(self.model.features.items, ['FeatureKey', 'CATEGORY']);
                        _categoryID = self.model.features.items[_index].FeatureID;
                        if (this.model.features.items[_index]) {
                            this.model.features.items[_index].Access = true;
                            this.model.features.items[_index].isDisableAccess = true;
                        }
                        itemsChild = _.filter(this.model.features.items, ['ParentID', _categoryID]);
                        _.forEach(itemsChild, function (itemChild, key) {
                            let _indexChild = _.findIndex(self.model.features.items, ['FeatureID', itemChild.FeatureID]);
                            if (self.model.features.items[_indexChild]) {
                                self.model.features.items[_indexChild].Access = true;
                                self.model.features.items[_indexChild].isDisableAccess = true;
                            }
                        });
                        break;

                    case 3:
                        // Nhóm người khai thác
                        _index = _.findIndex(self.model.features.items, ['FeatureKey', 'CALENDAR']);
                        if (this.model.features.items[_index]) {
                            this.model.features.items[_index].Access = true;
                            this.model.features.items[_index].isDisableAccess = true;
                        }

                        _index = _.findIndex(self.model.features.items, ['FeatureKey', 'TASK']);
                        if (this.model.features.items[_index]) {
                            this.model.features.items[_index].Access = true;
                            this.model.features.items[_index].isDisableAccess = true;
                        }

                        _index = _.findIndex(self.model.features.items, ['FeatureKey', 'CATEGORY']);
                        _categoryID = self.model.features.items[_index].FeatureID;
                        if (this.model.features.items[_index]) {
                            this.model.features.items[_index].Access = true;
                            this.model.features.items[_index].isDisableAccess = true;
                        }
                        itemsChild = _.filter(this.model.features.items, ['ParentID', _categoryID]);
                        _.forEach(itemsChild, function (itemChild, key) {
                            let _indexChild = _.findIndex(self.model.features.items, ['FeatureID', itemChild.FeatureID]);
                            if (self.model.features.items[_indexChild]) {
                                self.model.features.items[_indexChild].Access = true;
                                self.model.features.items[_indexChild].isDisableAccess = true;
                            }
                        });
                        break;

                    case 4:
                        // Nhóm người kiểm tra
                        _index = _.findIndex(self.model.features.items, ['FeatureKey', 'CALENDAR']);
                        if (this.model.features.items[_index]) {
                            this.model.features.items[_index].Access = true;
                            this.model.features.items[_index].isDisableAccess = true;
                        }

                        _index = _.findIndex(self.model.features.items, ['FeatureKey', 'TASK']);
                        if (this.model.features.items[_index]) {
                            this.model.features.items[_index].Access = true;
                            this.model.features.items[_index].isDisableAccess = true;
                        }

                        _index = _.findIndex(self.model.features.items, ['FeatureKey', 'CATEGORY']);
                        _categoryID = self.model.features.items[_index].FeatureID;
                        if (this.model.features.items[_index]) {
                            this.model.features.items[_index].Access = true;
                            this.model.features.items[_index].isDisableAccess = true;
                        }
                        itemsChild = _.filter(this.model.features.items, ['ParentID', _categoryID]);
                        _.forEach(itemsChild, function (itemChild, key) {
                            let _indexChild = _.findIndex(self.model.features.items, ['FeatureID', itemChild.FeatureID]);
                            if (self.model.features.items[_indexChild]) {
                                self.model.features.items[_indexChild].Access = true;
                                self.model.features.items[_indexChild].isDisableAccess = true;
                            }
                        });
                        break;

                    case 5:
                        // remove disable feature
                        this.stage.disabledFeature = false;
                        _.forEach(this.model.features.items, function (item, key) {
                            self.model.features.items[key].isDisableAccess = false;
                            self.model.features.items[key].isDisableAddnew = false;
                            self.model.features.items[key].isDisableEdit = false;
                            self.model.features.items[key].isDisableDelete = false;
                        });
                        break;
                    default:
                        // remove disable feature
                        this.stage.disabledFeature = false;
                        _.forEach(this.model.features.items, function (item, key) {
                            self.model.features.items[key].isDisableAccess = false;
                            self.model.features.items[key].isDisableAddnew = false;
                            self.model.features.items[key].isDisableEdit = false;
                            self.model.features.items[key].isDisableDelete = false;
                        });
                        break;
                }
                this.updateModel();
            }
        },
        watch: {
            idParams() {
                this.fetchData();
            }
        }
    }
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="css">
    .ij-main-body .tab-content {
        border: none;
        border-top: 1px solid #c8ced3;
    }

    .v-select .dropdown-menu {
        max-height: 170px !important;
    }

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

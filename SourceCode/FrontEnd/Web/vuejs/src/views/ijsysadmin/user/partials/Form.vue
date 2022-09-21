<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Người dùng : {{model.information.fullName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Người dùng : {{model.information.fullName}}</span>
                        </div>
                    </b-col>
                    <b-col class="col-md-6"></b-col>
                </b-row>
                <b-row>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-actions">
                            <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="handleSubmitForm"><i class="fa fa-check-circle"></i> Lưu</b-button>
                            <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onBackToList"><i class="fa fa-ban"></i> Hủy</b-button>
                        </div>
                    </b-col>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-icons">
                            <div class="main-header-collapse">
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
                                    <b-row>
                                        <b-col class="col-sm-12">
                                            <b-form-group label="Tài khoản đăng nhập" label-for="username" :label-cols="4">
                                                <b-form-input
                                                        id="username"
                                                        v-model.trim="$v.model.information.username.$model"
                                                        :state="chkState('username')"
                                                        type="text"
                                                        placeholder="Tài khoản đăng nhập"
                                                        aria-describedby="inputLiveFeedbackUsername"
                                                        autocomplete="username email"></b-form-input>
                                                <b-form-invalid-feedback id="inputLiveFeedbackUsername">
                                                    Tài khoản đăng nhập tối thiều 3 kí tự
                                                </b-form-invalid-feedback>
                                            </b-form-group>

                                            <b-form-group label="Mật khẩu" label-for="password" :label-cols="4">
                                                <b-form-input
                                                        id="password"
                                                        v-model.trim="$v.model.information.password.$model"
                                                        type="password"
                                                        :state="chkState('password')"
                                                        aria-describedby="inputLiveFeedbackPassword"
                                                        placeholder="Mật khẩu"
                                                        autocomplete="new-password"></b-form-input>
                                                <b-form-invalid-feedback id="inputLiveFeedbackPassword">
                                                    Mật khẩu yêu cầu tối thiểu 6 kí tự
                                                </b-form-invalid-feedback>
                                            </b-form-group>
                                            <b-form-group label="Nhập lại mật khẩu" label-for="confirmPassword" :label-cols="4">
                                                <b-form-input
                                                        id="confirmPassword"
                                                        v-model.trim="$v.model.information.confirmPassword.$model"
                                                        :state="chkState('confirmPassword')"
                                                        aria-describedby="inputLiveFeedbackConfirmPassword"
                                                        type="password"
                                                        placeholder="Xác nhận mật khẩu"
                                                        autocomplete="new-password"></b-form-input>
                                                <b-form-invalid-feedback id="inputLiveFeedbackConfirmPassword">
                                                    Mật khẩu không khớp
                                                </b-form-invalid-feedback>
                                            </b-form-group>

                                            <b-form-group label="Họ và tên" label-for="fullName" :label-cols="4">
                                                <b-form-input
                                                        id="fullName"
                                                        v-model="model.information.fullName"
                                                        type="text"
                                                        placeholder="Họ và tên"
                                                        autocomplete="fullName"></b-form-input>
                                            </b-form-group>

                                            <b-form-group label="Nhóm người dùng" label-for="groupUsers" :label-cols="4">
                                              <Select2 v-model="model.information.groupUsers" :settings="{multiple: true}" :options="groupUsers" @change="onChangeGroupUsers"></Select2>
                                            </b-form-group>

                                            <b-form-group label="Ghi chú" label-for="note" :label-cols="4">
                                                <b-form-textarea
                                                        id="note"
                                                        v-model="model.information.note"
                                                        placeholder="Ghi chú"
                                                ></b-form-textarea>
                                            </b-form-group>
<!--                                            <b-form-group label="Số thứ tự" label-for="numberOrder" :label-cols="4">-->
<!--                                                <b-form-input-->
<!--                                                        id="numberOrder"-->
<!--                                                        v-model="model.information.numberOrder"-->
<!--                                                        type="number"-->
<!--                                                        autocomplete="numberOrder"-->
<!--                                                        style="max-width: 100px"></b-form-input>-->
<!--                                            </b-form-group>-->
<!--                                            <b-form-group label="Ngừng hoạt động" class="form-group-checkbox-center" label-for="inactive" :label-cols="4">-->
<!--                                                <b-form-checkbox v-model="model.information.inactive" id="inactive" style="cursor: pointer"></b-form-checkbox>-->
<!--                                            </b-form-group>-->
                                        </b-col>
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
                                              <b-form-checkbox v-model="model.features.items[key].Addnew"
                                                               :disabled="!model.features.items[key].Access || model.features.items[key].isDisableAddnew"
                                                               @input="toggleCheckedChildNodesPermission($event, 'features', 'selectItemAddnew', item)"></b-form-checkbox>
                                            </td>
                                            <td role="cell" class="text-center">
                                              <b-form-checkbox v-model="model.features.items[key].Edit"
                                                               :disabled="!model.features.items[key].Access || model.features.items[key].isDisableEdit"
                                                               @input="toggleCheckedChildNodesPermission($event, 'features', 'selectItemEdit', item)"></b-form-checkbox>
                                            </td>
                                            <td role="cell" class="text-center">
                                              <b-form-checkbox v-model="model.features.items[key].Delete"
                                                               :disabled="!model.features.items[key].Access || model.features.items[key].isDisableDelete"
                                                               @input="toggleCheckedChildNodesPermission($event, 'features', 'selectItemDelete', item)"></b-form-checkbox>
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
    // import Multiselect from 'vue-multiselect';
    import ApiService from '@/services/api.service';
    import Select2 from 'v-select2-component';
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

    const CreateApi = 'sysadmin/api/users/create';
    const EditApi = 'sysadmin/api/users/edit';
    const ListRouter = 'sysadmin-user-list';
    const EditRouter = 'sysadmin-user-edit';
    const CreateRouter = 'sysadmin-user-create';
    const ViewRouter = 'sysadmin-user-view';
    const ViewApi = 'sysadmin/api/users/view';

    export default {
        name: 'FormUser',
        data () {
            return {
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
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
                    username: {
                        required,
                        minLength: minLength(3)
                    },
                    password: {
                        required,
                        minLength: minLength(6),
                        weakPass
                    },
                    confirmPassword: {
                        required,
                        sameAsPassword: sameAs("password")
                    },
                    fullName: {
                        required,
                        minLength: minLength(2)
                    },
                }
            }
        },
        components: {
          Select2
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
                    tmpObj.SelectAll = false;
                    tmpObj.isDisableAccess = false;
                    tmpObj.isDisableAddnew = false;
                    tmpObj.isDisableEdit = false;
                    tmpObj.isDisableDelete = false;
                    self.model.features.items.push(tmpObj);
                });


            },
            setGroupUsers() {
                let self = this;
                _.forEach(this.defaultModel.group, function (groupUser, key) {
                    if (groupUser.UserGroupID && groupUser.UserGroupName) {
                        let tmpObj = {};
                        tmpObj.id = groupUser.UserGroupID;
                        tmpObj.text = groupUser.UserGroupName;
                        if (groupUser.groupSelect) {
                            self.model.information.groupUsers.push(tmpObj.id);
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
            toggleCheckedChildNodesPermission(value, typePermission, typeChecked, itemFeature = null) {
              let self = this;
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
              setTimeout(() => {
                this.$forceUpdate();
              });
            },
            onNavigationItem(type) {
                let currentIndex = this.reqParams.idsArray.indexOf(this.idParams);
                let newIndex = (type === 'prev') ? currentIndex - 1 : currentIndex + 1;

                if (newIndex === (this.reqParams.idsArray.length) && (this.reqParams.currentPage != this.reqParams.lastPage)) {
                    this.reqParams.currentPage = this.reqParams.currentPage + 1;
                    this.getItemIds(type);
                } else if (newIndex < 0 && this.reqParams.currentPage > 1) {
                    this.reqParams.currentPage = this.reqParams.currentPage - 1;
                    this.getItemIds(type);
                } else{
                    this.idParams = (this.reqParams.idsArray[newIndex]) ? this.reqParams.idsArray[newIndex] : this.idParams;
                }
            },
            handleSubmitForm(){
                let self = this;
                let features = [];
                let information = {
                    username: this.model.information.username,
                    password: this.model.information.password,
                    confirm_password: this.model.information.confirmPassword,
                    FullName: this.model.information.fullName,
                    GroupUsers: this.model.information.groupUsers,
                    Note: this.model.information.note,
                    NumberOrder: this.model.information.numberOrder,
                    Inactive: (this.model.information.inactive) ? 1 : 0

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

                // _.forEach(this.model.information.groupUsers, function (group, key) {
                //     information.GroupUsers.push(group);
                // });
                const requestData = {
                    method: 'post',
                    url: "sysadmin/api/users/store",
                    data: {
                        information: information,
                        features: features,
                        reports: this.model.reports
                    }
                };

                // edit user
                if (this.idParams) {
                    requestData.data.UserID = this.idParams;
                    requestData.url = 'sysadmin/api/users/update' + '/' + this.idParams;
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

                if (this.reqParams.search.fullName !== '') {
                    requestData.data.FullName = this.reqParams.search.fullName;
                }
                if (this.reqParams.search.username !== '') {
                    requestData.data.username = this.reqParams.search.username;
                }
                if (this.reqParams.search.inActive !== -1) {
                    requestData.data.Inactive = this.reqParams.search.inActive;
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
            updateModel() {
                if (this.stage.updatedData) {
                    this.$forceUpdate();
                }
            },
            onChangeGroupUsers() {
                let self = this;
                if (self.model.information.groupUsers.length) {
                    let requestData = {
                        method: 'post',
                        url: 'sysadmin/api/users/set-group',
                        data: {
                            groups: self.model.information.groupUsers
                        }
                    };
                    ApiService.setHeader();
                    ApiService.customRequest(requestData).then(function (responses) {
                        let responsesData = responses.data;
                        if (responsesData.status === 1) {
                            _.forEach(responsesData.data, function (value, key) {
                                let _index = _.findIndex(self.model.features.items, ['FeatureID', value.FeatureID]);
                                if (self.model.features.items[_index]) {
                                    if (value.accessC === 1) {
                                        self.model.features.items[_index].Access = true;
                                        self.model.features.items[_index].isDisableAccess = false;
                                    }
                                    if (value.addnewC === 1) {
                                        self.model.features.items[_index].Addnew = true;
                                        self.model.features.items[_index].isDisableAddnew = false;
                                    }
                                    if (value.editC === 1) {
                                        self.model.features.items[_index].Edit = true;
                                        self.model.features.items[_index].isDisableEdit = false;
                                    }
                                    if (value.deleteC === 1) {
                                        self.model.features.items[_index].Delete = true;
                                        self.model.features.items[_index].isDisableDelete = false;
                                    }
                                }
                            });
                        }
                        self.stage.disabledFeature = false;
                        self.updateModel();
                    }).catch(function (error) {
                        console.log(error);
                    });
                } else {
                    // remove disable feature
                    this.stage.disabledFeature = false;
                    _.forEach(this.model.features.items, function (item, key) {
                        self.model.features.items[key].isDisableAccess = false;
                        self.model.features.items[key].isDisableAddnew = false;
                        self.model.features.items[key].isDisableEdit = false;
                        self.model.features.items[key].isDisableDelete = false;
                    });

                    this.updateModel();
                }
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
    .content-body .tab-content {
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

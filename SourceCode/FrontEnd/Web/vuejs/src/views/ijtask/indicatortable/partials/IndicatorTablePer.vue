<template>
  <b-button @click="onToggleModal()" type="reset" variant="primary" class="main-header-action mr-2" title="Chi tiết">
    <i class="fa fa-shield ij-icon" aria-hidden="true"></i> Phân quyền
    <b-modal ref="modal" scrollable size="xl">
      <template slot="modal-title">
        <i class="icon-eye icon" v-if="!isForm"></i><i class="fa fa-edit" v-if="isForm"></i> Phân quyền
      </template>
      <div class="table-responsive">
        <table class="not-border" v-if="!isForm">
          <thead>
          <tr class="text-left">
            <th class="pr-3">Nhân viên</th>
            <th class="pr-3">Nhóm đánh giá</th>
            <th class="text-center th-per">Xem</th>
            <th class="text-center th-per">Trường xem</th>
            <th class="text-center th-per">Sửa</th>
            <th class="text-center th-per">Trường sửa</th>
            <th class="text-center th-per">Xóa</th>
          </tr>
          </thead>
          <tbody>
            <tr v-for="(item, key) in listtable">
              <td class="pr-3">{{item['EmployeeName']}}</td>
              <td class="pr-3">{{EvaluatorGroup[item['EvaluatorGroup']]}}</td>
              <td class="text-center">
                <b-form-checkbox :disabled="!isForm" v-model="item['Access']" @change="changeAccessAll(key)">
                </b-form-checkbox>
              </td>
              <td class="text-center">
                <IndicatorTableDetailPer v-model="listtable[key].AccessField" :keyIndex="key" :field-model="'AccessField'" @updateFromChild="updateParent" :disable="!item['Access']==1" :isForm="isForm" :title="'Phân quyên xem bảng chỉ tiêu'"></IndicatorTableDetailPer>
              </td>
              <td class="text-center">
                <b-form-checkbox :disabled="!isForm" v-model="item['Edit']"  @change="changeEditAll(key)">
                </b-form-checkbox>
              </td>
              <td class="text-center">
                <IndicatorTableDetailPer v-model="listtable[key].EditField" :keyIndex="key" :field-model="'EditField'" @updateFromChild="updateParent" :disable="!item['Edit']==1" :isForm="isForm" :title="'Phân quyên sửa bảng chỉ tiêu'"></IndicatorTableDetailPer>
              </td>
              <td class="text-center">
                <b-form-checkbox :disabled="!isForm" v-model="item['Delete']">
                </b-form-checkbox>
              </td>
            </tr>
            <IjcoreModalMultiListing v-model="value" @changed="addLine" v-if="isForm" :title="'nhân viên'"
                                     :api="'/listing/api/common/list'" :table="'employee'"></IjcoreModalMultiListing>
          </tbody>
        </table>
        <table class="table b-table table-sm table-bordered table-editable" v-else>
          <thead>
          <tr class="text-left">
            <th class="has-padding text-center th-per">Nhân viên</th>
            <th class="has-padding text-center th-per">Nhóm đánh giá</th>
            <th class="has-padding text-center th-per">Xem</th>
            <th class="has-padding text-center th-per" style="width: 50px">Trường xem</th>
            <th class="has-padding text-center th-per">Sửa</th>
            <th class="has-padding text-center th-per" style="width: 50px">Trường sửa</th>
            <th class="has-padding text-center th-per">Xóa</th>
            <th style="width: 50px;"></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(item, key) in listtable">
            <td class="has-padding">{{item['EmployeeName']}}</td>
            <td>
              <b-form-select
                v-model="listtable[key].EvaluatorGroup"
                :options="[
                                        {value: 1, text: 'Nhân viên tự đánh giá'},
                                        {value: 2, text: 'Người quản lý trực tiếp'},
                                        {value: 3, text: 'Người quản lý cấp trên'},
                                        {value: 4, text: 'Người kiểm tra'},
                                        {value: 5, text: 'Người quyết định'},
                                        ]"
              >
              </b-form-select>
            </td>
            <td class="has-padding text-center">
              <b-form-checkbox :disabled="!isForm" v-model="item['Access']" @change="changeAccessAll(key)">
              </b-form-checkbox>
            </td>
            <td class="has-padding text-center">
              <IndicatorTableDetailPer v-model="listtable[key].AccessField" :keyIndex="key" :field-model="'AccessField'" @updateFromChild="updateParent" :disable="!item['Access']==1" :isForm="isForm" :title="'Phân quyên xem bảng chỉ tiêu'"></IndicatorTableDetailPer>
            </td>
            <td class="has-padding text-center">
              <b-form-checkbox :disabled="!isForm" v-model="item['Edit']"  @change="changeEditAll(key)">
              </b-form-checkbox>
            </td>
            <td class="has-padding text-center">
              <IndicatorTableDetailPer v-model="listtable[key].EditField" :keyIndex="key" :field-model="'EditField'" @updateFromChild="updateParent" :disable="!item['Edit']==1" :isForm="isForm" :title="'Phân quyên sửa bảng chỉ tiêu'"></IndicatorTableDetailPer>
            </td>
            <td class="has-padding text-center">
              <b-form-checkbox :disabled="!isForm" v-model="item['Delete']">
              </b-form-checkbox>
            </td>
            <td class="text-center">
              <i @click="onDeleteLine(key)" class="fa fa-trash-o" title="Xóa"
                 style="font-size: 18px; cursor: pointer"></i>
            </td>
          </tr>
          <IjcoreModalMultiListing v-model="value" @changed="addLine" v-if="isForm" :title="'nhân viên'"
                                   :api="'/listing/api/common/list'" :table="'employee'"></IjcoreModalMultiListing>
          </tbody>
        </table>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()" v-if="!isForm"
          >
            Sửa
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdate()" v-if="isForm">
            Lưu
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="" v-if="isForm">
            Hủy
          </b-button>
          <b-button
            variant="primary"
            size="md"
            class="float-left mr-2"
            @click="onHideModal()"
          >
            Đóng
          </b-button>
        </div>
      </template>

    </b-modal>
  </b-button>
</template>

<script>
  import ApiService from '@/services/api.service';
  import IjcoreModalMultiListing from "../../../../components/IjcoreModalMultiListing";
  import IndicatorTableDetailPer from "./IndicatorTableDetailPer";
  export default {
    name: 'IndicatorTablePer',
    components: {IndicatorTableDetailPer, IjcoreModalMultiListing},
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {
        TableEdit: '',
        Pr3: 'pr-3',
        isForm: false,
        listtable: [],
        EvaluatorGroup : {
          1: 'Nhân viên tự đánh giá',
          2: 'Người quản lý trực tiếp',
          3: 'Người quản lý cấp trên',
          4: 'Người kiểm tra',
          5: 'Người quyết định',

        }
      }
    },
    created() {
    },
    mounted() {
    },
    methods: {
      onDeleteLine(Key){
        this.listtable.splice(Key, 1)
      },
      changeAccessAll(key){
        if(!this.listtable[key].Access){
          this.listtable[key].AccessField = 'all';
        }else{
          this.listtable[key].AccessField = '';
          this.listtable[key].EditField = '';
          this.listtable[key].Delete = false;
          this.listtable[key].Edit = false;
        }
      },
      changeEditAll(key){
        if(!this.listtable[key].Edit){
          this.listtable[key].EditField = 'all';
        }else{
          this.listtable[key].EditField = '';
        }
      },
      updateParent(key, data, FieldData){
        this.listtable[key][FieldData] = data;
      },
      fetchData() {
        let self = this;
        let urlApi = '/task/api/indicator-table/table-per';
        let requestData = {
          method: 'post',
          url: urlApi,
          data: {
            TableID: self.TableID
          },
        };

        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let dataResponse = response.data;
          if (dataResponse.status === 1) {
            self.listtable = dataResponse.data
            _.forEach(self.listtable, function (field, key) {
              self.listtable[key].Access = field.Access == 1? true: false;
              self.listtable[key].Edit = field.Edit == 1? true: false;
              self.listtable[key].Delete = field.Delete == 1? true: false;
            });
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });

        // scroll to top perfect scroll
        const container = document.querySelector('.b-table-sticky-header');
        if (container) container.scrollTop = 0;
      },
      onSaveModal() {
        this.$bvToast.toast('Đã lưu ràng buộc', {
          variant: 'success',
          solid: true
        });
      },
      onCancelModal(e) {
        this.onResetModal();
        e.preventDefault();
      },
      onCancelModal(e) {
        this.onResetModal();
        e.preventDefault();
      },
      onHideModal() {
        this.$refs['modal'].hide();
        this.isForm = false;
      },
      onToggleModal() {
        let self = this;
        self.FieldGenEdit = '';
        self.FieldGenAccess = '';
        this.currentPage = 1;
        this.$refs['modal'].show();
        this.fetchData();
      },
      onResetModal() {
      },
      onEdit() {
        this.isForm = true;
        this.$forceUpdate()
      },

      addLine(datalist) {
        let self = this;
        datalist.map(function (item, key) {
          self.listtable.push({
            TableID: self.TableID,
            EmployeeID: item.EmployeeID,
            EmployeeName: item.EmployeeName,
            EvaluatorGroup: 1,
            Access: true,
            AccessField: 'all',
            Edit: false,
            EditField: '',
            Delete: false
          });
        });

      },
      onUpdate() {
        this.$store.commit('isLoading', true);
        let self = this;
        const requestData = {
          method: 'post',
          url: 'task/api/indicator-table/update-per',
          data: {
            TableID: self.TableID,
            Detail: self.listtable
          }
        };
        // edit user
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.LineIDTaskPer = responsesData.data;
            if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
            this.$bvToast.toast('Cập nhật thành công!', {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });
            this.isForm = false;
            self.$store.commit('isLoading', false);
          } else {
            let htmlErrors = __.renderErrorApiHtml(responsesData.data);
            Swal.fire(
              'Thông báo',
              htmlErrors,
              'error'
            )
            self.$store.commit('isLoading', false);
          }

        }, (error) => {
          console.log(error);
          Swal.fire(
            'Thông báo',
            'Không kết nối được với máy chủ',
            'error'
          )
          self.$store.commit('isLoading', false);
        });
      }
    },
    watch: {
      isForm(){
        if(this.isForm == true){
          this.TableEdit = 'table b-table table-sm table-bordered table-editable';
          this.Pr3 = 'has-padding';
        }else{
          this.TableEdit = 'not-border';
          this.Pr3 = 'pr-3';
        }
      }
    },
    props: {
      value: {},
      title: {},
      name: {},
      TableID: {}
    },
  }
</script>
<style>
  .readonly {
    background-color: #fff !important;
  }

  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }

  .modal-footer ol, .modal-footer ul, .modal-footer dl {
    margin-bottom: 0px;
  }

  #modal-form-input-assign .modal-lg .modal-content {
    width: 1024px;
    margin: auto;
  }

  @media (max-width: 1024px) {
    #modal-form-input-assign .modal-lg {
      max-width: 100%;
    }

    #modal-form-input-assign .modal-lg .modal-content {
      width: 90%;
      margin: auto;
    }
  }

  @media (min-width: 992px) {
    #modal-form-input-assign .modal-lg{
      max-width: 100%;
    }
  }
  .text-center{
    text-align: center;
  }
  .th-per{
    margin-left: 0.5rem; font-style: italic;font-weight: normal;
    padding-right: 0.5rem;
  }
</style>

<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Loại chương trình mục tiêu<span v-if="model.CateName">:</span> {{model.CateName}}</span>
              <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Loại chương trình mục tiêu<span v-if="model.CateName">:</span> {{model.CateName}}</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="handleSubmitForm"><i
                class="fa fa-check-square-o"></i> Lưu
              </b-button>
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onBackToList"><i
                class="fa fa-ban"></i> Hủy
              </b-button>
            </div>
          </b-col>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-icons">
              <div class="main-header-collapse">
                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen="true"/>
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
            <div class="form-group row align-items-center">
              <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
              <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                <input v-model="model.CateName" type="text" id="RevenuName" class="form-control" placeholder="Tên chương trình mục tiêu" name="RevenuName"/>
              </div>
              <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                <input type="text" v-model="model.CateNo" class="form-control" placeholder="Mã số"/>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Là mục con của</label>
              <div class="col-md-17">
                <IjcoreModalSystemParentCate
                  v-model="model"
                  :title="'loại chương trình mục tiêu'"
                  :api="'/listing/api/common/listCate'"
                  :table="'program'"
                  :fieldName="'CateName'"
                  :fieldNo="'CateNo'"
                  :fieldID="'CateID'"
                  :placeholderInput="'Chọn loại chương trình mục tiêu cha'"
                  :placeholderSearch="'Nhập loại chương trình mục tiêu'"
                  :columnID="'CateID'"
                  :columnNo="'CateNo'"
                  :columnName="'CateName'"
                >
                </IjcoreModalSystemParentCate>
              </div>
              <div v-if="model.ParentID" class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số: </span>
                <span>{{model.ParentNo}}</span>
              </div>
            </div>
            <label>Giá trị :</label>
            <table class="table b-table table-sm table-bordered table-editable">
              <thead>
              <tr>
                <th style="width: 3%; min-width: 30px; background: #fff">STT</th>
                <th scope="col" style="width: 30%" class="text-center">Tên</th>
                <th scope="col" style="width: 10%" class="text-center">Kiểu giá trị</th>
                <th scope="col" style="width: 10%" class="text-center">Giá trị</th>
                <th scope="col" style="width: 3%" class="text-center"></th>
              </tr>
              </thead>
              <draggable v-model="model.programCateValue" tag="tbody" draggable=".draggable" handle=".my-handle" @change="changeProgramTableItem">
                <tr class="draggable" v-for="(field, key) in model.programCateValue">
                  <td class="text-center my-handle" style=" cursor: move">
                    <span>{{key+1}}</span>
                  </td>
                  <td>
                    <b-form-input
                      type="text"
                      v-model="model.programCateValue[key].Description"
                      autocomplete="program cate list description">
                    </b-form-input>
                  </td>
                  <td>
                    <b-form-select :options="dataTypeOption" @change="onChangeDataType($event, field)"
                                   v-model="model.programCateValue[key].DataType"></b-form-select>
                  </td>
                  <td>
                    <b-form-input
                      v-if="field.DataType === 1"
                      type="number"
                      v-model="model.programCateValue[key].CateValue"
                      autocomplete="program cate list description">
                    </b-form-input>
                    <b-form-input
                      v-if="field.DataType === 2"
                      type="text"
                      v-model="model.programCateValue[key].CateValue"
                      autocomplete="program cate list description">
                    </b-form-input>
                    <masked-input
                      v-if="field.DataType === 3"
                      type="text"
                      name="date"
                      v-model="model.programCateValue[key].CateValue + ''"
                      class="form-control"
                      :mask="[/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/]"
                      :guide="true"
                      placeholderChar="_"
                      :showMask="true"
                      :keepCharPositions="true"
                      :pipe="autoCorrectedDatePipe()">
                    </masked-input>
                    <masked-input
                      v-if="field.DataType === 4"
                      type="text"
                      name="date-time"
                      v-model="model.programCateValue[key].CateValue + ''"
                      class="form-control"
                      :mask="[/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/, ' ', /\d/, /\d/, ':', /\d/, /\d/]"
                      :guide="true"
                      placeholderChar="_"
                      :showMask="true"
                      :keepCharPositions="true"
                      :pipe="autoCorrectedDateTimePipe()">
                    </masked-input>


                    <b-form-select v-if="field.DataType === 5" v-model="model.programCateValue[key].CateValue"
                                   :options="[{value: 1, text: 'Có'},{value: 2, text: 'Không'}]"></b-form-select>
                    <b-form-select v-if="field.DataType === 6" v-model="model.programCateValue[key].CateValue"
                                   :options="[{value: 1, text: 'Đúng'},{value: 2, text: 'Sai'}]"></b-form-select>

                  </td>
                  <td class="text-center">
                    <i @click="onDeleteFieldOnTable(field)" class="fa fa-trash-o" title="Xóa"
                       style="font-size: 18px; cursor: pointer"></i>
                  </td>
                </tr>
              </draggable>

            </table>
            <a @click="onAddFieldOnTable" class="new-row">
              <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
            </a>
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
import vSelect from 'vue-select';
import MaskedInput from 'vue-text-mask';
import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
import Select2 from 'v-select2-component';
import moment from 'moment';
import IjcoreModalSystemParentCate from "../../../../components/IjcoreModalSystemParentCate";
import draggable from 'vuedraggable';
import ColumnResizer from "column-resizer";


moment.locale('vi');


const ListRouter = 'listing-programcatelist';
const EditRouter = 'listing-programcatelist-edit';
const CreateRouter = 'listing-programcatelist-edit';
const ViewRouter = 'listing-programcatelist-view';
const ViewApi = 'listing/api/program-cate-list/view';
const EditApi = 'listing/api/program-cate-list/edit';
const CreateApi = 'listing/api/program-cate-list/create';
const StoreApi = 'listing/api/program-cate-list/store';
const UpdateApi = 'listing/api/program-cate-list/update';
const ListApi = 'listing/api/program-cate-list';

const dataTypeOption = {
  1: 'Số',
  2: 'Kí tự',
  3: 'Ngày',
  4: 'Ngày giờ',
  5: 'Có/Không',
  6: 'Đúng/Sai'
};

const DataTypeOption = [
  {value: 1, text: 'Số'},
  {value: 2, text: 'Kí tự'},
  {value: 3, text: 'Ngày'},
  {value: 4, text: 'Ngày giờ'},
  {value: 5, text: 'Có/Không'},
  {value: 6, text: 'Đúng/Sai'}
];

export default {
  name: 'listing-tcatelist-view',
  data() {
    return {
      idParams: this.idParamsProps,
      reqParams: this.reqParamsProps,
      model: {
        CateName: '',
        CateNo: '',
        ParentName: '',
        ParentNo: '',
        programCateValue: [],
        ParentID: null,
        maxLineID: 0,
      },
      stage: {
        updatedData: false
      },

      // for field operator time
      formats: {
        title: 'MMMM YYYY',
        weekdays: 'W',
        navMonths: 'MMM',
        input: ['L', 'YYYY-MM-DD', 'YYYY/MM/DD'], // Only for `v-date-picker`
        dayPopover: 'L', // Only for `v-date-picker`
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

  components: {
    Select2,
    MaskedInput,
    IjcoreModalSystemParentCate,
    draggable,
  },
  beforeCreate() {
  },
  mounted() {
    this.fetchData();
    if (document.querySelector('.table-column-resizable')) {
      new ColumnResizer(
        document.querySelector('.table-column-resizable'),{resizeMode: 'overflow'}
      );
    }
  },
  updated() {
    this.stage.updatedData = true;
  },
  computed: {
    itemNo() {
      let index = 0;
      index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
      return index;
    },
    dataTypeOption() {
      return DataTypeOption;
    }
  },
  methods: {
    handleDebugger(value) {
      alert('aaa');
    },
    fetchData() {
      let self = this;
      let urlApi = CreateApi;
      let requestData = {
        method: 'get',
        data: {}
      };
      // Api edit user
      if (this.idParams) {
        urlApi = EditApi + '/' + this.idParams;
        requestData.data.id = this.idParams;
      }
      requestData.url = urlApi;
      this.$store.commit('isLoading', true);

      ApiService.setHeader();
      ApiService.customRequest(requestData).then((responses) => {
        let responsesData = responses.data;
        // copy item
        if (!self.idParams && !_.isEmpty(self.itemCopy)) {
          responsesData = self.itemCopy;
        }
        if (responsesData.status === 1) {
          if (self.idParams || !_.isEmpty(self.itemCopy)) {
            if (_.isObject(responsesData.data.data)) {
              if( responsesData.data.data.ParentID){

                let Parent = _.filter(responsesData.data.programCateList, ["CateID", responsesData.data.data.ParentID])
                self.model.ParentName = Parent[0].CateName;
                self.model.ParentNo = Parent[0].CateNo;
              }
              self.model.CateName = responsesData.data.data.CateName;
              self.model.ParentID = responsesData.data.data.ParentID;
              self.model.CateNo = responsesData.data.data.CateNo;
              self.model.inactive = (responsesData.data.data.Inactive) ? true : false;
              self.model.programCateValue = responsesData.data.programCateValue;

              // set max lineID
              _.forEach(self.model.programCateValue, function (field, key) {
                if (Number(field.LineID) > self.model.maxLineID) self.model.maxLineID = Number(field.LineID);
                // set type of value
                if (field.DataType == 1) self.model.programCateValue[key].CateValue = Number(self.model.programCateValue[key].CateValue);
                if (field.DataType == 2) self.model.programCateValue[key].CateValue = String(self.model.programCateValue[key].CateValue);
                if (field.DataType == 3) self.model.programCateValue[key].CateValue = moment(self.model.programCateValue[key].CateValue).format('DD/MM/YYYY');
                if (field.DataType == 4) self.model.programCateValue[key].CateValue = moment(self.model.programCateValue[key].CateValue).format('DD/MM/YYYY hh:mm');
              });

            }
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
    onNavigationItem(type) {
      let currentIndex = this.reqParams.idsArray.indexOf(this.idParams);
      let newIndex = (type === 'prev') ? currentIndex - 1 : currentIndex + 1;

      if (newIndex === (this.reqParams.idsArray.length) && (this.reqParams.currentPage != this.reqParams.lastPage)) {
        this.reqParams.currentPage = this.reqParams.currentPage + 1;
        this.getItemIds(type);
      } else if (newIndex < 0 && this.reqParams.currentPage > 1) {
        this.reqParams.currentPage = this.reqParams.currentPage - 1;
        this.getItemIds(type);
      } else {
        this.idParams = (this.reqParams.idsArray[newIndex]) ? this.reqParams.idsArray[newIndex] : this.idParams;
      }
    },
    getItemIds(type) {
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

      if (this.reqParams.search.CateName !== '') {
        requestData.data.CateName = this.reqParams.search.CateName;
      }
      if (this.reqParams.search.CateNo !== '') {
        requestData.data.CateNo = this.reqParams.search.CateNo;
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
            self.reqParams.idsArray.push(value.CateID);
          });

          (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
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
    onChangeDataType(value, field) {
      if (value === 1) field.CateValue = 0;
      if (value === 2) field.CateValue = '';
      if (value === 3) field.CateValue = moment().format('DD/MM/YYYY');
      if (value === 4) field.CateValue = moment().format('DD/MM/YYYY hh:mm');
      if (value === 5 || value === 6) field.CateValue = 1;
      this.$forceUpdate();
    },
    onAddFieldOnTable() {
      let fieldObj = {};
      this.model.maxLineID += 1;
      fieldObj.LineID = this.model.maxLineID;
      fieldObj.DataType = 1;
      fieldObj.CateValue = null;
      fieldObj.CateName = '';
      fieldObj.CateNo = '';
      fieldObj.NOrder = null;
      this.model.programCateValue.push(fieldObj);
      this.$forceUpdate();
    },
    onDeleteFieldOnTable(field) {

      // remove field in fieldOnTableReq
      let fieldExist = _.find(this.model.programCateValue, ['LineID', field.LineID]);
      if (_.isObject(fieldExist)) {
        _.remove(this.model.programCateValue, ['LineID', field.LineID]);
      }
      this.$forceUpdate();
    },
    handleSubmitForm() {
      let self = this;
      const requestData = {
        method: 'post',
        url: StoreApi,
        data: {
          master: {
            CateName: this.model.CateName,
            CateNo: this.model.CateNo,
            ParentNo: this.model.ParentNo,
            ParentName: this.model.ParentName,
            ParentID: this.model.ParentID,
            Inactive: (this.model.inactive) ? 1 : 0,
          },
          detail: this.model.programCateValue
        }
      };

      // edit user
      if (this.idParams) {
        requestData.data.master.CateID = this.idParams;
        requestData.url = UpdateApi + '/' + this.idParams;
      }

      this.$store.commit('isLoading', true);
      ApiService.setHeader();
      ApiService.customRequest(requestData).then((responses) => {
        let responsesData = responses.data;
        if (responsesData.status === 1) {
          // ver 2.0
          if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
          let item = {
            CateID : responsesData.data,
            CateName: self.model.CateName,
            CateNo: self.model.CateNo,
            ParentNo: self.model.ParentNo,
            ParentName: self.model.ParentName,
            ParentID: self.model.ParentID,
            Inactive: (self.model.inactive) ? 1 : 0,
            Level: null,
            Detail: null,
            Class: '',
            hidden: false,
          };
          let indexold =  _.findIndex(self.$route.params.req.itemsArray, {'CateID': item.CateID});
          let indexParent = null;
          if(indexold >= 0){
            // update

            item.Detail = 1;
            // set for new Parent
            let ParentOldID =  self.$route.params.req.itemsArray[indexold].ParentID;
            self.$route.params.req.itemsArray.splice(indexold, 1);
            if(item.ParentID) {
              indexParent = _.findIndex(self.$route.params.req.itemsArray, {'CateID': self.model.ParentID});
              if(indexParent >= 0 ){
                self.$set(self.$route.params.req.itemsArray[indexParent], 'Detail', 0);
                self.$set(self.$route.params.req.itemsArray[indexParent], 'Class', 'fa fa-minus-square-o');
                item.Level = self.$route.params.req.itemsArray[indexParent].Level + 1;
                self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, indexParent + 1, item);
              }
            }else {
              item.Level = 1;
              self.$route.params.req.itemsArray.push(item);
            }

            // set for ParentOld
            let indexParentOld = _.findIndex(self.$route.params.req.itemsArray, {'CateID':  ParentOldID});
            if(indexParentOld >= 0){
              let ParentOld = self.$route.params.req.itemsArray[indexParentOld];
              let child = _.filter(self.$route.params.req.itemsArray, ['ParentID', ParentOld.CateID]);
              if(child.length > 0){
                self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Detail', 0);
                self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Class', 'fa fa-minus-square-o');
              } else {
                self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Detail', 1);
                self.$set(self.$route.params.req.itemsArray[indexParentOld], 'Class', '');
              }
            }


          } else {
            // store
            if(self.model.ParentID){
              indexParent = _.findIndex(self.$route.params.req.itemsArray, {'CateID': self.model.ParentID});
              if(indexParent >= 0){
                let level = self.$route.params.req.itemsArray[indexParent].Level;
                if(self.$route.params.req.itemsArray[indexParent].Detail) {
                  self.$set(self.$route.params.req.itemsArray[indexParent], 'Detail', 0);
                  self.$set(self.$route.params.req.itemsArray[indexParent], 'Class', 'fa fa-minus-square-o');
                }
                item.Level = level + 1;
                item.Detail = 1;
                self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, indexParent + 1, item);
              }
            } else {
              item.Level = 1;
              item.Detail = 1;
              self.$route.params.req.itemsArray.push(item);
              _.orderBy(self.$route.params.req.itemsArray, ['CateID'], 'asc');
            }
          }
          self.onBackToList('Bản ghi đã được cập nhật');
        } else if (responsesData.status === 4){
          Swal.fire(
            'Lỗi',
            'Không được sửa bản ghi Tổng hợp',
            'error'
          )
        } else {
          let htmlErrors = __.renderErrorApiHtml(responsesData.data);
          Swal.fire(
            'Thông báo',
            htmlErrors,
            'error'
          )
        }

        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        Swal.fire(
          'Thông báo',
          'Không kết nối được với máy chủ',
          'error'
        );
        self.$store.commit('isLoading', false);
      });
    },
    onEditClicked() {
      this.$router.push({
        name: EditRouter,
        params: {id: this.idParams, req: this.reqParams}
      });
    },
    onCreateClicked() {
      this.$router.push({name: CreateRouter});
    },
    onBackToList(message = '') {
      debugger
      let self = this;
      let params = (this.$route.params.req) ? this.$route.params.req:{};
      let query = this.$route.query;
      query.isBackToList = true;
      if (_.isString(message)) {
        params.message = message;
        this.$router.push({
          name: ViewRouter,
          query: query,
          params: {id: self.idParams, req: params, message: 'Bản ghi đã được cập nhật!'}
        });
      } else {
        this.$router.push({name: ListRouter, query: query, params: params});
      }
    },
    updateModel() {
      if (this.stage.updatedData) {
        this.$forceUpdate();
      }
    },
    autoCorrectedDatePipe: () => {
      return createAutoCorrectedDatePipe('dd/mm/yyyy')
    },
    autoCorrectedDateTimePipe: () => {
      return createAutoCorrectedDatePipe('dd/mm/yyyy hh:mm')
    },
    changeProgramTableItem(moved) {},
  },
  watch: {
    idParams() {
      this.fetchData();
    },
  }
}
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="css">
.v-select .dropdown-menu {
  max-height: 170px !important;
}

.custom-align {
  flex: 0 0 12.3%;
}
</style>

<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Định mức phân bổ dự toán: {{model.NormAllotLevelName}}</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
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
          <b-col class="col-md-12">
            <div class="main-header-item main-header-icons">
              <div class="main-header-item-counter ml-auto mr-3" v-if="reqParams">
                <span>{{itemNo}} - {{reqParams.total}}</span>
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
            <div class="form-group row align-items-center">
              <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" title="Mã chỉ tiêu dự toán" style="white-space: nowrap">Mã CTDT: </div>
              <div class="col-md-3 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3">
                {{model.NormNo}}
              </div>
              <div class="col-md-3 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span title="Tên chỉ tiêu dự toán">Tên CTDT:</span>
              </div>
              <div class="col-md-15">{{model.NormName}}</div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" title="Tiêu chí phân bổ dự toán">TCPBDT: </label>
              <div class="col-md-21">
                <norm-allot-map :norm="model" v-model="model.NormAllot" :is-view="true"></norm-allot-map>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0" title="Mã số định mức phân bổ dự toán">Mã số ĐMPBDT: </label>
              <div class="col-md-3">
                {{model.NormAllotLevelNo}}
              </div>
              <label class="col-md-3 m-0" title="Tên định mức phân bổ dự toán">Tên ĐMPBDT: </label>
              <div class="col-md-15">
                {{model.NormAllotLevelName}}
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Là mục con của: </label>
              <div class="col-md-21">
                {{model.ParentName}}
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Ngày hiệu lực: </label>
              <div class="col-md-6">
                {{model.EffectiveDate | convertServerDateToClientDate}}
              </div>
              <label class="col-md-3 m-0">Ngày hết hiệu lực: </label>
              <div class="col-md-6">
                {{model.ExpirationDate | convertServerDateToClientDate}}
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label class="col-md-3 m-0">Đơn vị tính: </label>
              <div class="col-md-4 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3">
                {{model.UomName}}
              </div>
              <label class="col-md-2 m-0">Định mức: </label>
              <div class="col-md-4">
                {{model.LCUnitPrice | formatCurrency}}
              </div>
            </div>

            <label>Đơn giá :</label>
            <table class="table b-table table-sm table-bordered">
              <thead>
              <tr>
                <th scope="col" style="width: 20%" class="text-center">Ngày hiệu lực</th>
                <th scope="col" style="width: 20%" class="text-center">Ngày hết hiệu lực</th>
                <th scope="col" style="width: 20%" class="text-center">Đơn vị tính</th>
                <th scope="col" style="width: 20%" class="text-center">Định mức tối thiểu</th>
                <th scope="col" style="width: 20%" class="text-center">Định mức tối đa</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(field, key) in NormAllotLevelItem">
                <td class="text-center">{{NormAllotLevelItem[key].EffectiveDate | convertServerDateToClientDate}}</td>
                <td class="text-center">{{NormAllotLevelItem[key].ExpirationDate | convertServerDateToClientDate}}</td>
                <td>{{NormAllotLevelItem[key].UomName}}</td>
                <td class="text-right">{{NormAllotLevelItem[key].FCMinUnitPrice | formatCurrency}}</td>
                <td class="text-right">{{NormAllotLevelItem[key].FCMaxUnitPrice | formatCurrency}}</td>
              </tr>
              </tbody>
            </table>
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
import NormAllotMap from "./partials/NormAllotMap";

const ListRouter = 'listing-normallotlevel';
const ViewRouter = 'listing-normallotlevel-view';
const EditRouter = 'listing-normallotlevel-edit';
const CreateRouter = 'listing-normallotlevel-create';
const ViewApi = 'listing/api/norm-allot-level/view';
const ListApi = 'listing/api/norm-allot-level';
const DeleteApi = 'listing/api/norm-allot-level/delete';

export default {
  name: 'listing-view-vendor',
  data () {
    return {
      idParams: this.$route.params.id,
      reqParams: this.$route.params.req,
      model: {
        NormAllotLevelID: null,
        NormAllotLevelNo: '',
        NormAllotLevelName: '',
        NormAllotLevelType: 1,
        NormAllot: [],
        ParentID: null,
        ParentNo: '',
        ParentName: '',
        Level: 1,
        Detail: 1,
        NormID: null,
        NormNo: '',
        NormName: '',
        NormAllotID: null,
        NormAllotNo: '',
        NormAllotName: '',
        EffectiveDate: '',
        ExpirationDate: '',
        UomID: '',
        UomNo: '',
        UomName: '',
        FCUnitPrice: null,
        LCUnitPrice: null,
      },
      NormAllotLevelItem: [],
      defaultModel: {},
      stage: {
        updatedData: false,
        message: (this.$route.params.message) ? this.$route.params.message : ''
      }
    }

  },

  components: {
    NormAllotMap
  },
  beforeCreate() {
    if (!this.$route.params.id) {
      this.$router.push({name: ListRouter});
    }
  },
  mounted() {
    this.fetchData();
    // hiển thị thông báo
    if (this.stage.message && this.stage.message !== '') {
      this.$bvToast.toast(this.stage.message, {
        title: 'Thông báo',
        variant: 'success',
        solid: true
      });
    }
  },
  updated() {
    this.stage.updatedData = true;
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
      if (this.idParams == 0 || _.isUndefined(this.idParams)) {
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
        self.defaultModel = responsesData;
        if (responsesData.status === 1) {
          self.model.NormAllotLevelID = responsesData.data.data.NormAllotLevelID;
          self.model.NormAllotLevelNo = responsesData.data.data.NormAllotLevelNo;
          self.model.NormAllotLevelName = responsesData.data.data.NormAllotLevelName;
          self.model.NormAllotLevelType = responsesData.data.data.NormAllotLevelType;
          self.model.ParentID = responsesData.data.data.ParentID;
          self.model.ParentNo = responsesData.data.data.ParentNo;
          self.model.ParentName = responsesData.data.data.ParentName;
          self.model.NormID = responsesData.data.data.NormID;
          self.model.NormNo = responsesData.data.data.NormNo;
          self.model.NormName = responsesData.data.data.NormName;
          self.model.EffectiveDate = responsesData.data.data.EffectiveDate;
          self.model.ExpirationDate = responsesData.data.data.ExpirationDate;
          self.model.UomID = responsesData.data.data.UomID;
          self.model.UomNo = responsesData.data.data.UomNo;
          self.model.UomName = responsesData.data.data.UomName;
          self.model.FCUnitPrice = responsesData.data.data.FCUnitPrice;
          self.model.LCUnitPrice = responsesData.data.data.LCUnitPrice;

          self.model.NormAllot = responsesData.data.NormAllotLevelMap;
          self.NormAllotLevelItem = responsesData.data.NormAllotLevelItem;

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

      if (this.reqParams.search.NormAllotLevelName !== '') {
        requestData.data.NormAllotLevelName = this.reqParams.search.NormAllotLevelName;
      }
      if (this.reqParams.search.CateNo !== '') {
        requestData.data.CateNo = this.reqParams.search.NormAllotLevelName;
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
            self.reqParams.idsArray.push(value.NormAllotLevelID);
          });

          (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        self.$store.commit('isLoading', false);
        console.log(error);
        Swal.fire({
          title: 'Thông báo',
          text: 'Không kết nối được với máy chủ',
          confirmButtonText: 'Đóng'
        });
      });

    },

    onEditClicked(){
      this.$router.push({
        name: EditRouter,
        params: {id: this.idParams, req: this.reqParams}
      });
    },
    onCreateClicked(){
      let self = this;
      let params = (this.$route.params.req) ? this.$route.params.req:{};
      let query = this.$route.query;
      query.isBackToList = true;
      this.$router.push({
        name: CreateRouter,
        query: query,
        params: {id: self.idParams, req: params}
      });
    },
    onBackToList(message = '') {
      let params = (this.$route.params.req) ? this.$route.params.req:{};
      let query = this.$route.query;
      query.isBackToList = true;
      if (_.isString(message)) {
        params.message = message;
        this.$router.push({name: ListRouter, query: query, params: params});
      } else {
        this.$router.push({name: ListRouter, query: query, params: params});
      }
    },
    handleCopyItem(){
      this.$router.push({name: CreateRouter, params: {itemCopy: this.defaultModel}});
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
              let index = _.findIndex(self.$route.params.req.itemsArray, {'NormAllotLevelID' : self.idParams});
              self.$route.params.req.itemsArray.splice(index, 1);
              let indexParent = _.findIndex(self.$route.params.req.itemsArray, {'NormAllotLevelID':self.model.parentID});
              if(indexParent >= 0){
                let child = _.filter(self.$route.params.req.itemsArray, {'ParentID': self.model.parentID});
                if(child.length == 0){
                  self.$set(self.$route.params.req.itemsArray[indexParent], 'Class', '');
                  self.$set(self.$route.params.req.itemsArray[indexParent], 'Detail', 1);
                }
              }
              self.onBackToList('Bản ghi đã được xóa');
            } else  if (responseData.status === 4) {
              Swal.fire(
                'Lỗi',
                'Không được xóa bản ghi Tổng hợp',
                'error'
              );
            } else {
              Swal.fire(
                'Có lỗi',
                '',
                'error'
              );

            }
          }, (error) => {
            console.log(error);
            Swal.fire({
              title: 'Thông báo',
              text: 'Không kết nối được với máy chủ',
              confirmButtonText: 'Đóng'
            });

          });

        }
      });
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

<style lang="css"></style>


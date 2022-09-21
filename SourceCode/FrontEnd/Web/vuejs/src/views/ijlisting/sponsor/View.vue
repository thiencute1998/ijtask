<template>
  <div class="main-entry">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="icon-eye icon mr-2"></i> Nhà tài trợ: {{model.SponsorName}}</span>
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
              <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
              <div class="col-md-17 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                {{ model.SponsorName }}
              </div>
              <div class="col-md-4 col-sm-8 col-24 d-flex align-items-center app-object-code">
                <span>Mã số</span>
                {{ model.SponsorNo }}
              </div>
            </div>
            <div class="form-group row align-items-center">
              <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Địa chỉ</div>
              <div class="col-md-21 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                {{ model.SponsorAddress }}
              </div>
            </div>
            <div class="form-group row align-items-center">
              <div class="col-md-3 col-sm-8 col-24 d-flex align-items-center app-object-code"><span>Loại</span></div>
              <div class="col-md-5 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
                <span v-if="model.SponsorType  == 1">Tổ chức trong nước</span>
                <span v-if="model.SponsorType  == 2">Tổ chức ngoài nước</span>
              </div>
              <label class="col-md-3 m-0">Tỉnh</label>
              <div class="col-md-5 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
                {{ model.ProvinceName }}
              </div>
              <label class="col-md-3 m-0">Số điện thoại</label>
              <div class="col-md-5 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
                {{model.OfficePhone}}
              </div>
            </div>
            <div class="form-group row">
              <label class="col-md-3">Ghi chú</label>
              <div class="col-md-21">
                {{ model.Note }}
              </div>
            </div>

            <label>Liên kết nhà tài trợ</label>
            <table class="table b-table table-sm table-bordered table-editable">
              <thead>
              <tr>
                <th scope="col" style="width: 5%" class="text-center">Mã số</th>
                <th scope="col" style="width: 30%" class="text-center">Tên</th>
                <th scope="col" style="width: 20%" class="text-center">LinkTable</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(field, key) in model.SponsorLink">
                <td class="p-1"><span>{{ field.LinkNo }}</span></td>
                <td class="p-1"><span>{{ field.LinkName }}</span></td>
                <td class="p-1"><span>{{ field.LinkTable }}</span></td>
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

const ListRouter = 'listing-sponsor';
const EditRouter = 'listing-sponsor-edit';
const CreateRouter = 'listing-sponsor-create';
const ViewApi = 'listing/api/sponsor/view';
const ListApi = 'listing/api/sponsor';
const DeleteApi = 'listing/api/sponsor/delete';

export default {
  name: 'listing-view-sponsor',
  data () {
    return {
      idParams: this.$route.params.id,
      reqParams: this.$route.params.req,
      model: {
        SponsorNo: '',
        SponsorType : 1,
        SponsorLink: [],
        SponsorName: '',
        SponsorAddress: '',
        ProvinceID: null,
        ProvinceNo: '',
        ProvinceName: '',
        OfficePhone: '',
        Note: '',
      },
      defaultModel: {},
      stage: {
        updatedData: false,
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

          self.model.SponsorNo = responsesData.data.data.SponsorNo;
          self.model.SponsorType  = responsesData.data.data.SponsorType ;
          self.model.SponsorName = responsesData.data.data.SponsorName;
          self.model.SponsorAddress = responsesData.data.data.SponsorAddress;
          self.model.Province = responsesData.data.data.ProvinceName;
          self.model.Note = responsesData.data.data.Note;
          self.model.SponsorLink = responsesData.data.SponsorLink;
          self.model.ProvinceID = responsesData.data.data.ProvinceID;
          self.model.ProvinceNo = responsesData.data.data.ProvinceNo;
          self.model.ProvinceName = responsesData.data.data.ProvinceName;
          self.model.OfficePhone = responsesData.data.data.OfficePhone;

          let parentObj = _.find(self.model.vendorCateList, ['CateID', self.model.parentID]);
          if (parentObj) {
            self.model.parentName = parentObj.CateName;
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

      if (this.reqParams.search.CateName !== '') {
        requestData.data.CateName = this.reqParams.search.CateName;
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
      this.$router.push({name: CreateRouter});
    },
    onBackToList(message = '') {
      if (_.isString(message)) {
        this.$router.push({name: ListRouter, params: {message: message}});
      } else {
        this.$router.push({name: ListRouter});
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


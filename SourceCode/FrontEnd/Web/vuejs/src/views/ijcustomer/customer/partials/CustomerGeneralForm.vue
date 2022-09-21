<template>
  <div class="main-entry">
    <vue-perfect-scrollbar class="scroll-area" :settings="$store.state.psSettings">
      <div class="container-fluid">
<!--        <b-card>-->
          <div class="form-group row align-items-center">
            <div class="col-lg-2 col-md-2 mb-md-0 col-sm-4 mb-sm-0" style="white-space: nowrap">Tên</div>
            <div class="col-lg-18 col-md-22 col-sm-20 mb-sm-2 mb-md-3 mb-lg-0 mb-3 app-object-name">
              <input v-model="value.CustomerName" type="text" class="form-control" placeholder="Tên khách hàng"/>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-8 app-object-code d-md-none d-sm-none d-none d-lg-flex align-items-center">
              <span>Mã số</span>
              <input type="text" v-model="value.CustomerNo" class="form-control" placeholder="Mã số"/>
            </div>

            <div class="d-lg-none col-md-2 col-sm-2">Mã số</div>
            <div class="d-lg-none col-md-8 col-sm-8">
              <input type="text" v-model="value.CustomerNo" class="form-control" placeholder="Mã số"/>
            </div>

          </div>

          <div class="form-group row align-items-center">
            <label class="col-md-2 col-sm-4 m-0" for="Address">ĐCGD</label>
            <div class="col-md-22 col-sm-20">
              <input class="form-control" v-model="value.Address" id="Address" placeholder="Địa chỉ giao dịch" name="Address"/>
            </div>
          </div>
          <div class="form-group row align-items-center">
            <label class="col-md-2 col-sm-4 m-0" for="BillTo">ĐCNHĐ</label>
            <div class="col-md-22 col-sm-20">
              <input class="form-control" v-model="value.BillTo" id="BillTo" placeholder="Địa chỉ nhận hóa đơn" name="BillTo"/>
            </div>
          </div>
          <div class="form-group row align-items-center">
            <label class="col-md-2 col-sm-4 m-0" for="ShipTo">ĐCNH </label>
            <div class="col-md-22 col-sm-20">
              <input class="form-control" v-model="value.ShipTo" id="ShipTo" placeholder="Địa chỉ nhận hàng" name="ShipTo"/>
            </div>
          </div>

          <div class="form-group row align-items-center">
            <label class="col-md-2 col-sm-4 m-0" for="TaxCode">MST</label>
            <div class="col-md-6 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
              <input type="number" v-model="value.TaxCode" id="TaxCode" class="form-control" placeholder="Mã số thuế" name="TaxCode" />
            </div>

            <label class="col-md-2 col-sm-4 m-0" for="Fax">TKNH</label>
            <div class="col-md-4 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
              <input v-model="value.BankAccount" type="number" id="BankAccount" class="form-control" placeholder="Tài khoản ngân hàng" name="BankAccount" />
            </div>

            <label class="col-md-2 col-sm-4 m-0">ĐCNH</label>
            <div class="col-md-8 col-sm-20">
              <input v-model="value.BankName" id="BankName" class="form-control" placeholder="Địa chỉ ngân hàng" name="BankName" />
            </div>
          </div>

          <div class="form-group row align-items-center">
            <label class="col-md-2 col-sm-4 m-0" for="OfficePhone">ĐTCQ</label>
            <div class="col-md-6 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
              <input type="number" v-model="value.OfficePhone" id="OfficePhone" class="form-control" placeholder="Số điện thoại" name="OfficePhone" />
            </div>

            <label class="col-md-2 col-sm-4 m-0" for="Fax">Số Fax</label>
            <div class="col-md-4 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
              <input v-model="value.Fax" type="number" id="Fax" class="form-control" placeholder="Số Fax" name="Fax" />
            </div>

            <label class="col-md-2 col-sm-4 m-0">Email</label>
            <div class="col-md-8 col-sm-20">
              <input v-model="value.Email" type="email" id="Email" class="form-control" placeholder="Email" name="Email" />
            </div>
          </div>

          <div class="form-group row align-items-center">
            <label class="col-md-2 col-sm-4 m-0" for="Website">Website</label>
            <div class="col-md-6 col-sm-20">
              <input v-model="value.Website" type="text" id="Website" class="form-control" placeholder="Website" name="Website" />
            </div>
          </div>
          <div class="form-group row align-items-center">
            <label class="col-md-2 col-sm-4 m-0" for="OfficePhone">Tỉnh</label>
            <div class="col-md-6 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
              <IjcoreModalListing
                v-model="value" :title="'Tỉnh'" :api="'/listing/api/common/list'"
                :table="'province'" :FieldID="'ProvinceID'" :FieldName="'ProvinceName'">
              </IjcoreModalListing>
            </div>

            <label class="col-md-2 col-sm-4 m-0" for="Fax">Huyện</label>
            <div class="col-md-6 col-sm-20 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
              <IjcoreModalListing
                v-model="value" :title="'Huyện'" :api="'/listing/api/common/list'"
                :table="'district'" :FieldID="'DistrictID'" :FieldName="'DistrictName'"
                :FieldWhere="{ProvinceID : value.ProvinceID}">
              </IjcoreModalListing>
            </div>

            <label class="col-md-2 col-sm-4 m-0">Xã</label>
            <div class="col-md-6 col-sm-20">
              <IjcoreModalListing
                v-model="value" :title="'Xã'" :api="'/listing/api/common/list'"
                :table="'commune'" :FieldID="'CommuneID'" :FieldName="'CommuneName'"
                :FieldWhere="{DistrictID : value.DistrictID}">
              </IjcoreModalListing>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-2 col-sm-4 m-0" for="Note">Ghi chú</label>
            <div class="col-md-22 col-sm-20">
              <textarea v-model="value.Note" class="form-control" id="Note" rows="3" placeholder="Ghi chú" name="Note"></textarea>
            </div>
          </div>
          <div class="form-group row align-items-center">
            <label class="col-md-2 col-sm-4 m-0" for="Website">Là NCC</label>
            <div class="col-md-6 col-sm-20">
              <b-form-checkbox
                type="check" v-model="value.IsVendor">
              </b-form-checkbox>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-2 col-sm-4 m-0" for="Note">Loại KH</label>
            <div class="col-md-2 col-sm-20">
              <i @click="AddCustomerCate()"
                 class="fa fa-external-link" title="Loại khách hàng"
                 style="font-size: 18px; cursor: pointer; padding-right: 5px;"></i>
            </div>
            <div class="col-md-20">
              <span v-for="(field, key) in value.CustomerCate">{{value.arrCustomerCateList[field.CateID]}}: {{field.Description}}, </span>
            </div>
          </div>
<!--        </b-card>-->
      </div>
    </vue-perfect-scrollbar>

    <!-- Popup Add CustomerCate -->
    <b-modal ref="CustomerCate" id="modal-form-input-task-general1" size="lg"
             title="Loại khách hàng">
      <div class="main-body main-body-view-action">
        <table class="table b-table table-sm table-bordered table-editable">
          <thead>
          <tr>
            <th scope="col" style="width: 20%; border-bottom: none;" class="text-center">Loại khách hàng </th>
            <th scope="col" style="width: 10%; border-bottom: none;" class="text-center">Giá trị</th>
            <th scope="col" style="width: 3%; border-bottom: none;" class="text-center">
              <!--<i @click="onAddFieldOnTable" class="fa fa-plus" style="cursor: pointer; font-size: 16px; padding: 0 5px"></i>--></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(field, key) in value.CustomerCate" v-bind:RowItem="field.CateID">
            <td>
              <IjcoreModalListing v-model="value.CustomerCate[key]" :title="'loại khách hàng'" :api="'/listing/api/common/list'"
                                  :table="'customer_cate_list'" :FieldID="'CateID'" :FieldName="'CateName'"
                                  :FieldNo="'CateNo'" >
              </IjcoreModalListing>
            </td>
            <td>
              <IjcoreModalListing v-model="value.CustomerCate[key]" :title="'giá trị'" :api="'/listing/api/common/list'"
                                  :table="'customer_cate_value'" :FieldName="'Description'" :FieldNo="'CateValue'"
                                  :FieldWhere="{CateID : value.CustomerCate[key]}">
              </IjcoreModalListing>
            </td>

            <td class="text-center">
              <i @click="onDeleteFieldCustomerCate(key)" class="fa fa-trash-o" title="Xóa"
                 style="font-size: 18px; cursor: pointer"></i>
            </td>
          </tr>
          </tbody>
        </table>
        <a @click="onAddFieldCustomerCate(RowItem)" class="new-row">
          <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới
        </a>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="SaveCustomerCate()">
            Lưu
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="HideCustomerCate()">
            Đóng
          </b-button>
        </div>
      </template>
    </b-modal>

  </div>
</template>

<script>
  import ApiService from '@/services/api.service';
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";

  const ListRouter = 'customer-customer';
  const EditRouter = 'customer-customer-edit';
  const CreateRouter = 'customer-customer-create';
  const DetailApi = 'customer/api/customer/view';
  const ListApi = 'customer/api/customer';
  const EditApi = 'customer/api/customer/edit';

  export default {
    name: 'CustomerGeneralForm',
    components: {
      IjcoreModalListing,
    },
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {
        idParams: this.$route.params.id,
        listtable: [],
        tableName: '',
        search: '',
        lenghNo: 0,
        RowItem: [],
        CateID: '',
        CustomerCate: {},
      }
    },
    created() {
    },
    mounted() {
      this.fetchData();
    },
    methods: {
      fetchData() {
        let self = this;
        let urlApi = '';
        let requestData = {
          method: 'get',
          data: {}
        };
        // Api edit user
        if(this.idParams){
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
            let auto = responsesData.data.auto;
            responsesData = self.itemCopy;
            responsesData.data.auto = auto;
          }
          if (responsesData.status === 1) {

            if (self.idParams || !_.isEmpty(self.itemCopy)) {
              if (_.isObject(responsesData.data)) {
                //console.log(self.value)
                self.value.CustomerNo = responsesData.data.CustomerNo;
                self.value.CustomerName = responsesData.data.CustomerName;
                self.value.Address = responsesData.data.Address;
                self.value.BillTo = responsesData.data.BillTo;
                self.value.ShipTo = responsesData.data.ShipTo;
                self.value.TaxCode = responsesData.data.TaxCode;
                self.value.BankAccount = responsesData.data.BankAccount;
                self.value.BankName = responsesData.data.BankName;
                self.value.OfficePhone = responsesData.data.OfficePhone;
                self.value.Fax = responsesData.data.Fax;
                self.value.Email = responsesData.data.Email;
                self.value.Website = responsesData.data.Website;
                self.value.ProvinceID = responsesData.data.ProvinceID;
                self.value.ProvinceName = responsesData.data.ProvinceName;
                self.value.DistrictID = responsesData.data.DistrictID;
                self.value.DistrictName = responsesData.data.DistrictName;
                self.value.CommuneID = responsesData.data.CommuneID;
                self.value.CommuneName = responsesData.data.CommuneName;
                self.value.Note = responsesData.data.Note;
                self.value.IsVendor = responsesData.data.IsVendor;
                self.value.inactive = (responsesData.data.Inactive) ? true : false;

                self.value.CustomerCate = responsesData.data.CustomerCate;
                self.value.arrCustomerCateList = responsesData.data.arrCustomerCateList;

                _.forEach(self.value.CustomerCate, function (field, key) {
                  // set name of value
                  self.value.CustomerCate[key].CateName = self.value.arrCustomerCateList[field.CateID];
                });

              }
              if (!_.isEmpty(self.itemCopy)) {
                self.value.CustomerNo = responsesData.data.auto;
              }
            } else {
              self.value.CustomerNo = responsesData.data.auto;
            }

          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      handleSubmitForm(){
        let self = this;
        const requestData = {
          method: 'post',
          url: StoreApi,
          data: {
            CustomerNo: this.value.CustomerNo,
            CustomerName: this.value.CustomerName,
            Address: this.value.Address,
            BillTo: this.value.BillTo,
            ShipTo: this.value.ShipTo,
            TaxCode: this.value.TaxCode,
            BankAccount: this.value.BankAccount,
            BankName: this.value.BankName,
            OfficePhone: this.value.OfficePhone,
            Fax: this.value.Fax,
            Email: this.value.Email,
            Website: this.value.Website,
            ProvinceID: this.value.ProvinceID,
            ProvinceName: this.value.ProvinceName,
            DistrictID: this.value.DistrictID,
            DistrictName: this.value.DistrictName,
            CommuneID: this.value.CommuneID,
            CommuneName: this.value.CommuneName,
            Note: this.value.Note,
            IsVendor: (this.value.IsVendor) ? 1 : 0,
            Inactive: (this.value.inactive) ? 1 : 0,
            CateID: this.value.CateID,
            detail: this.value.CustomerCate,
          }
        };

        // edit user
        if (this.idParams) {
          requestData.data.CustomerID = this.idParams;
          requestData.url = UpdateApi + '/' + this.idParams;
        }

        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.$router.push({
              name: ViewRouter,
              params: {id: self.idParams, req: self.reqParams, message: 'Bản ghi đã được cập nhật!'}
            });
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
      onCreateClicked() {
        this.$router.push({name: CreateRouter});
      },
      onBackToList() {
        this.$router.push({name: ListRouter});
      },
      onAddFieldCustomerCate(RowItem) {
        let fieldObj = {};
        fieldObj.CustomerID = RowItem;
        fieldObj.CateID = '';
        fieldObj.CateValue = null;
        this.RowItem = RowItem + 1;
        this.value.CustomerCate.push(fieldObj);
        this.$forceUpdate();
      },
      onDeleteFieldCustomerCate(key) {
        this.value.CustomerCate.splice(key, 1);
        //
        //this.setStyleAction();
        this.$forceUpdate();
      },
      AddCustomerCate() {
        this.$forceUpdate();
        this.$refs['CustomerCate'].show();
      },
      HideCustomerCate() {
        this.isForm = false;
        this.$refs['CustomerCate'].hide();
      },
      SaveCustomerCate() {
        this.$bvToast.toast('Đã lưu loại khách hàng\n', {
          title: 'Thông báo',
          variant: 'success',
          solid: true
        });
        this.$refs['CustomerCate'].hide();
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
      },
      'value.CalendarTypeID'() {
        this.getHour();
      },
      'value.StartDate'() {
        if (__.convertDate(this.value.StartDate) > __.convertDate(this.value.DueDate)) {
          this.value.StartDate = this.value.DueDate;
        }
        this.getHour();
        this.value.statusHour = 1;
      },
      'value.DueDate'() {
        if (__.convertDate(this.value.StartDate) > __.convertDate(this.value.DueDate)) {
          this.value.DueDate = this.value.StartDate;
        }
        if (this.value.statusHour == 0) {
          this.getHour();
        }
      },
    },
    props: ['value'],
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

  #modal-form-input-task-general-form .modal-lg .modal-content {
    width: 1024px;
    margin: auto;
  }

  @media (max-width: 1024px) {
    #modal-form-input-task-general-form .modal-lg {
      max-width: 100%;
    }

    #modal-form-input-task-general-form .modal-lg .modal-content {
      width: 90%;
      margin: auto;
    }
  }

  @media (min-width: 992px) {
    #modal-form-input-task-general-form .modal-lg {
      max-width: 100%;
    }
  }
</style>

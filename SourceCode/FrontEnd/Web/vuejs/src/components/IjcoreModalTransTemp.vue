<template>
  <b-button type="submit" variant="primary" class="main-header-action mr-2" :title="'Chọn ' + titlenew"
            @click="onToggleModal" style="background-color: #00a2e8 !important;">
    <i class='fa fa-external-link'></i> Thêm từ
    <input type="hidden" :readonly="true" v-model="value['TemplateID']" class="readonly form-control"></input>
    <b-modal ref="modal" class="editcss" id="modal" size="lg">
      <template slot="modal-title">
          Chọn chứng từ {{this.titlenew}}
      </template>
      <div class="form-group row" style="margin-bottom: 0px">
        <div class="col-md-9 col-xl-9 m-0 ml-0">
          <IjcoreModalListing
            v-model="model" :title="'đơn vị'"  :api="'/listing/api/common/list3'"
            :FieldName="'CompanyName'" :FieldNo="'CompanyNo'" :FieldID="'CompanyID'" :table="'company'" :tabletrans="'act_gvouc_trans'" @changed="fetchData"

          ></IjcoreModalListing>
<!--          :FieldWhere="{'CompanyID' : ''}"-->
        </div>
        <div class="col-md-5 col-xl-5 m-0 ml-0">
          <b-form-select style=""
                         v-model="TransType"
                         :options="OptionsTransType"  @change="fetchData">
          </b-form-select>
        </div>
        <div class="col-md-10 col-xl-10 m-0 ml-0" style="margin: 5px 0 !important;">
          <b-form-radio-group
            v-model="RequestRepID"
            :options="options"
            class="mb-3"
            value-field="item"
            text-field="name"
            disabled-field="notEnabled"
            @change="fetchData"
            style="margin-bottom: 0px !important"
          ></b-form-radio-group>
        </div>
      </div>
      <b-input-group class="pt-10">
        <b-form-input v-model="search" :placeholder="'Chọn chứng từ ' + this.title" class="readonly form-control"
                      @change="fetchData"></b-form-input>
        <div class="input-group-prepend" @click="fetchData">
                                <span class="input-group-text">
                                  <i class='fa fa-search'></i>
                                </span>
        </div>
      </b-input-group>
      <div class="table-responsive pb-10">
        <table class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
          <thead role="rowgroup" class="thead-light">
            <tr role="row" class="">
              <th role="columnheader" scope="col" style="width: 10%; min-width: 120px" class="pl-3 text-center">Ngày CTG</th>
              <th role="columnheader" scope="col" style="width: 10%; min-width: 120px" class="pl-3 text-center">Số CTG</th>
              <th role="columnheader" scope="col" style="width: 68%; min-width: 120px">Diễn giải</th>
              <th role="columnheader" scope="col" style="width: 12%; min-width: 120px">Tổng số tiền(đ)</th>
            </tr>
          </thead>
          <tbody>
          <tr v-for="item in listtable" @click="updateForParent(item['TransID'], item['Comment'])">
            <td role="cell" class="pl-3 text-center">{{item['TransDate'] | convertServerDateToClientDate}}</td>
            <td role="cell" class="pl-3 text-center">{{item['TransNo']}}</td>
            <td class="pr-3" :title="item['Comment']">{{item['Comment']}}</td>
            <td role="cell" class="pl-3 text-right">{{item['LCTotalAmount'] | convertNumberToText}}</td>
          </tr>
          </tbody>
        </table>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button
            variant="primary"
            size="md"
            class="float-left"
            @click="onHideModal()"
          >
            Đóng
          </b-button>
          <div class="d-flex flex-wrap justify-content-between align-items-center m-0 float-right">
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
          </div>
        </div>
      </template>

    </b-modal>
  </b-button>
</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';
  import IjcoreModalListing from "@/components/IjcoreModalListing";

  export default {
    name: 'IjcoreModalTransTemp',
    mixins: [mixinLists],
    components: { IjcoreModalListing,},
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {
        model:{
          CompanyID: '',
          CompanyNo: '',
          CompanyName: '',
        },
        listtable: [],
        tableName: '',
        search: '',
        RequestRepID: '0',
        TransType: this.TransTypeID,
        titlenew: '',
      }
    },
    created() {
      //this.settings.CreateRouter = 'statebudgetplanning-sbpreviewplan-create';
    },
    mounted() {
    },
    methods: {
      fetchData() {
        let self = this;
        let urlApi =  this.api;
        let requestData = {
          method: 'post',
          url: urlApi,
          data: {
            per_page: 10,
            page: this.currentPage,
            table: this.tableName,
            search: this.search,
            RequestRepID: this.RequestRepID,
            TransTypeID: this.TransType,
            CompanyID: this.model.CompanyID,
          },

        };
        //if(this.TransTypeID ==2){ this.title= 'xem xét dự toán';}else if(this.TransTypeID ==3){ this.title= 'lập dự toán';}else{this.title= this.title;}
        let Check_TType = this.TransType;
        if(Check_TType){
          switch(Check_TType){
            case '2':
              self.titlenew = 'lập dự toán';
              break;
            case '3':
              self.titlenew = 'xem xét dự toán';
              break;
            case '4':
              self.titlenew = 'phê duyệt dự toán';
              break;
            case '6':
              self.titlenew = 'giao dự toán';
              break;
            case '7':
              self.titlenew  = 'cấp dự toán';
              break;
            case '8':
              self.titlenew  = 'ước thực hiện dự toán';
              break;
            default:
              self.titlenew  = self.title;
              break;

          }
        }
        // if (this.modelSearch.CompanyNo.trim() !== '') {
        //     requestData.data.CompanyNo = this.modelSearch.CompanyNo;
        // }

        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let dataResponse = response.data; //console.log(response.data);
          if (dataResponse.status == '1') {
            self.totalRows = dataResponse.data.total;
            self.perPage = String(dataResponse.data.per_page);
            self.currentPage = dataResponse.data.current_page;
            // converse object to array
            self.listtable = _.toArray(dataResponse.data.data);

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
        });

        // scroll to top perfect scroll
        const container = document.querySelector('.b-table-sticky-header');
        if (container) container.scrollTop = 0;

      },
      updateForParent: function (ParentID, ParentName) {
        this.value['TransID'] = ParentID;
        this.value['Comment'] = ParentName;

        this.$emit('changed', true);
        this.$refs['modal'].hide();

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
      },
      onToggleModal() {
        this.currentPage = 1;
        let self = this;
        this.currentPage = 1;
        this.$refs['modal'].show();
        this.fetchData();
      },
      onResetModal() {
      },
    },
    watch: {
      currentPage() {
        this.fetchData();
      }
    },
    props: {
      value: {
        type: Object,
        default() {
          return {
            ParentID: '',
          };
        }
      },
      title: {},
      api: {},
      name: {},
      table: {},
      FieldNo: {},
      FieldName: {},
      FieldID: {},
      TransTypeID: {},
      options: [Array, Object],
      OptionsTransType: [Array, Object],
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
  .editcss .custom-control-inline {
    display: -webkit-inline-box;
    display: -ms-inline-flexbox;
    display: inline-flex;
    margin-right: 0.5rem !important;
  }
</style>

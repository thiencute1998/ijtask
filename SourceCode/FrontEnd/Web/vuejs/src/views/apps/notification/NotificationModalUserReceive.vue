<template>
    <div class="d-inline-flex align-items-center justify-content-center component-notification-user-receive">
      <span class="badge badge-primary" style="cursor: pointer" @click="init">Chi tiết</span>
      <b-modal id="modal-user-receive" title="Người nhận"
               content-class="modal-user-receive"
               ref="modal-user-receive"
               size="md" ok-title="Đóng" ok-only>
        <div class="notification-modal-search pt-10">
          <b-input-group>
            <b-form-input
              v-model="model.UserName"
              type="text"
              @keyup.enter="onSubmitSearch">
            </b-form-input>

            <!-- Attach Right button -->
            <b-button variant="primary" @click="onSubmitSearch">
              <i class="fa fa-search"></i>
            </b-button>
          </b-input-group>
        </div>


        <div class="notification-modal-data">
          <b-table :hover="propsTable.hover" :striped="propsTable.striped"
                   :bordered="propsTable.bordered"
                   :small="propsTable.small"
                   :fields="captions"
                   class="mb-0 pb-10"
                   :foot-clone="propsTable.footClone"
                   fixed="fixed" responsive="sm" :items="itemsArray">

            <template v-slot:cell(Status)="data">
              <span v-if="data.item.Status === 0">Chưa đọc</span>
              <span v-if="data.item.Status === 1">Đã đọc</span>
              <span v-if="data.item.Status === 2">Không đọc</span>
            </template>

            <template v-slot:cell(ReadDate)="data">
              <span>{{data.item.ReadDate | convertTimeToHMTime}}</span>
            </template>

          </b-table>
        </div>

        <template v-slot:modal-footer="{ ok, cancel, hide }">
          <b-button class="ml-0" variant="primary" @click="hide()">
            Đóng
          </b-button>
          <div class="notification-modal-pagination mr-0">
            <div class="overflow-auto">
              <b-pagination
                v-model="currentPage"
                :total-rows="rows"
                :per-page="perPage"
                aria-controls="my-table"
              ></b-pagination>
            </div>
          </div>
        </template>

      </b-modal>
    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import _ from 'lodash';
    export default {
      name: 'advanced-forms',
      components: {},
      data () {
        return {
          perPage: 8,
          currentPage: 1,
          itemsArray: [],
          totalRows: 0,

          model: {
            UserName: ''
          }
        }
      },
      mounted(){

      },
      props:{
        value: {
            type: Object,
            default () {
                return {}
            }
        },
        noFadeModal: {
            type: Boolean,
            default: false
        },
        sizeModal: {
            type: String,
            default: 'md' // sm|md|lg|xl
        },
        propsTable: {
            type: Object,
            default() {
                return {
                    id: '',
                    primaryKey: '',
                    striped: false,
                    bordered: false,
                    borderless: false,
                    outlined: false,
                    dark: false,
                    hover: true,
                    small: true,
                    fixed: true,
                    responsive: true,
                    stickyHeader: false,
                    captionTop: false,
                    tableVariant: '',
                    tableClass: '',
                    stacked: '',
                    headVariant: '',
                    threadClass: '',
                    threadTrClass: {},
                    footClone: false,
                    footVariant: '',
                    tfootClass: {},
                    tfootTrClass: {},
                    tbodyTrClass: {},
                    tbodyClass: {},
                    tbodyTransitionProps: {},
                    tbodyTransitionHandlers: {}
                    // Todo:: add more props for table
                };
            }
        },

        notificationID: [Number, String]
      },
      computed: {
        rows() {
            return this.totalRows
        },
        captions: function() {
          let fieldsTable = [
            {key: 'UserName', label: 'Người nhận', field: 'UserName'},
            {key: 'Status', label: 'Trạng thái', field: 'Status'},
            {key: 'ReadDate', label: 'Thời gian đọc', field: 'ReadDate', tdClass: 'text-center'}
          ];

          return fieldsTable;
        },
      },
      methods: {
        init(e){
          e.preventDefault();
          e.stopPropagation();
          if (!this.itemsArray.length) {
            this.fetchData();
          }
          this.$refs['modal-user-receive'].show();
        },
        fetchData(){
          let self = this;
          let requestData = {
            method: 'post',
            url: 'extensions/api/notice/get-user-receive',
            data: {
              per_page: this.perPage,
              page: this.currentPage,
              NotificationID: this.notificationID
            },
          };

          if (this.model.UserName) {
            requestData.data.UserName = this.model.UserName;
          }


          self.$store.commit('isLoading', true);
          ApiService.customRequest(requestData).then((response) => {
            self.$store.commit('isLoading', false);
            let responseData = response.data;
            if (responseData.status === 1) {
              self.itemsArray = _.toArray(responseData.data.data);
              self.perPage = responseData.data.per_page;
              self.currentPage = responseData.data.current_page;
              self.totalRows = responseData.data.total;
            }
          }, (error) => {
            console.log(error);
            self.$store.commit('isLoading', false);
          });
        },
        onSubmitSearch(){
            this.fetchData();
        },
      },
      watch: {
        currentPage() {
          this.fetchData();
        },
        'value': {
          handler(val){
            // do stuff

          },
          deep: true
        }
      }
    }
</script>
<style lang="css">
  #modal-user-receive table thead {
    display: none;
  }

  #modal-user-receive .modal-footer {
    justify-content: space-between;
  }
  #modal-user-receive .input-group .btn {
    border-bottom-right-radius: 0;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
  }

</style>

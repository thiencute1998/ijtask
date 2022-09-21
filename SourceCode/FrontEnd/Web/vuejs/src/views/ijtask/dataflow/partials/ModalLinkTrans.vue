<template>
  <div class="ijcore ijcore-modal ijcore-modal-link-trans component-modal-link-trans">
    <b-modal ref="modal" id="modal">
      <template slot="modal-title">
        {{titleModal}}
      </template>
      <b-input-group class="pt-10">
        <b-form-input v-model="search" placeholder="Tìm kiếm..." class="readonly form-control" @change="fetchData"></b-form-input>
        <div class="input-group-prepend" @click="fetchData">
          <span class="input-group-text">
            <i class='fa fa-search'></i>
          </span>
        </div>
      </b-input-group>
      <div class="table-responsive pb-10">
        <table class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
          <tbody>
          <tr v-for="(item, key) in itemsArray" @click="selectTrans($event, key)">
            <td style="width: 5%" class="no-overflow">
              <b-form-checkbox v-model="itemsArray[key].Checked"></b-form-checkbox>
            </td>
            <td style="width: 20%">{{item.TransNo}}</td>
            <td class="pr-3" :title="item.Comment">{{item.Comment}}</td>
          </tr>
          </tbody>
        </table>
      </div>
      <template v-slot:modal-footer="{ ok, cancel, hide}">
        <div class="w-100 left">
          <b-button
            variant="primary"
            size="md"
            class="float-left"
            @click="onSelectModal()">
            Chọn
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
  </div>
</template>

<script>
  import ApiService from '@/services/api.service';

  export default {
    name: 'modal-link-trans',
    props: {
      value: [Boolean],
      dataflowArray: [Array, Object],
      dataflowTrans: [Object, Array],
      toggle: false
    },
    data() {
      return {
        titleModal: 'Chứng từ',
        TransTypeID: null,
        itemsArray: [],
        search: '',
        perPage: (this.$store.state.optionBehavior.perPage) ? this.$store.state.optionBehavior.perPage : null,
        currentPage: 1,
        totalRows: null,
      }
    },
    components: {},
    computed: {
      rows() {
        return this.totalRows
      },
    },
    created() {
    },
    mounted() {
    },
    methods: {
      fetchData() {
        if (!this.dataflowTrans || !this.TransTypeID) {
          return;
        }
        let self = this;
        let requestData = {
          method: 'post',
          url: 'task/api/dataflow/get-trans',
          data: {
            TransTypeID: this.TransTypeID,
            per_page: 10,
            page: this.currentPage,
            search: this.search,
          },
        };

        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let responseData = response.data;
          if (responseData.status === 1) {
            _.forEach(responseData.data.data, function (value, key) {
              responseData.data.data[key].Checked = false;
            });
            self.itemsArray = responseData.data.data;
            self.totalRows = responseData.data.total;
            self.perPage = String(responseData.data.per_page);
            self.currentPage = responseData.data.current_page;
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      selectTrans(e, key){
        e.preventDefault();
        e.stopPropagation();
        let self = this;
        _.forEach(this.itemsArray, function (value, keyItem) {
          self.itemsArray[keyItem].Checked = false;
        });
        self.itemsArray[key].Checked = true;
      },
      onSaveModal(){},
      onCancelModal(e) {
        this.onResetModal();
        e.preventDefault();
      },
      onHideModal() {
        this.$refs['modal'].hide();
      },
      onShowModal() {
        this.currentPage = 1;
        this.fetchData();
        this.$refs['modal'].show();
      },
      onSelectModal(){
        let tranSelected = _.find(this.itemsArray, ['Checked', true]);
        if (tranSelected && this.dataflowTrans) {
          let self = this;
          let requestData = {
            method: 'post',
            url: 'task/api/dataflow/update-trans',
            data: {
              TransIDOld: this.dataflowTrans.TransID,
              TransID: tranSelected.TransID,
              WFID: this.dataflowTrans.WFID,
              WFNo: this.dataflowTrans.WFNo,
              WFName: this.dataflowTrans.WFName,
              WFItemID: this.dataflowTrans.WFItemID,
              WFItemName: this.dataflowTrans.WFItemName,
              DFID: this.dataflowTrans.DFID,
              DFKey: this.dataflowTrans.DFKey
            },
          };

          this.$store.commit('isLoading', true);
          ApiService.customRequest(requestData).then((response) => {
            let responseData = response.data;
            if (responseData.status === 1) {
              // update dataflowArray
              let index = _.findIndex(self.dataflowArray, ['TaskID', self.dataflowTrans.TaskID]);
              if (index > -1) {
                self.dataflowArray[index].TransID = tranSelected.TransID;
                self.dataflowArray[index].TransNo = tranSelected.TransNo;
                self.dataflowArray[index].TransComment = tranSelected.Comment;
              }

              self.onHideModal();
              self.$bvToast.toast('Cập nhật thành công', {
                title: 'Thông báo',
                variant: 'success',
                solid: true
              });
            }else {
              this.$bvToast.toast('Cập nhật thất bại', {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
            }
            self.$store.commit('isLoading', false);
          }, (error) => {
            console.log(error);
            self.$store.commit('isLoading', false);
          });
        }
      },
      onResetModal() {},
      hide() {
        this.onHideModal();
      },
      ok() {
        this.onHideModal();
      },
      cancel() {
        this.onCancelModal();
      }
    },
    watch: {
      currentPage() {
        this.fetchData();
      },
      toggle() {
        switch (this.dataflowTrans.FeatureKey){
          // ước
          case 'SBPESTIMATEPLAN':
            this.titleModal = 'Ước thực hiện';
            this.TransTypeID = 8;
            break;
          // Lập
          case 'SBPMAKEPLAN':
            this.titleModal = 'Lập dự toán';
            this.TransTypeID = 2;
            break;
          // Xem xét
          case 'SBPREVIEWPLAN':
            this.titleModal = 'Xem xet dự toán';
            this.TransTypeID = 3;
            break;
          // Phê duyệt
          case 'SBPAPPROVALPLAN':
            this.titleModal = 'Phê duyệt dự toán';
            this.TransTypeID = 4;
            break;
          // Giao dự toán
          case 'SBPASSIGNPLAN':
            this.titleModal = 'Giao dự toán';
            this.TransTypeID = 6;
            break;

          // Cấp dự toán
          case 'SBPGIVEPLAN':
            this.titleModal = 'Cấp dự toán';
            this.TransTypeID = 7;
            break;
          // Phân bổ
          case 'SBPREGUPLAN':
            this.titleModal = 'Phân bổ điều tiết thu ngân sách';
            break;
          default:
            break;
        }
        this.onShowModal();
      }
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
    margin-bottom: 0;
  }

  .ijcore-modal-link-trans .input-group-append {
    position: absolute;
    right: 0;
    z-index: 9;
  }
  .ijcore-modal-link-trans .ijcore-element-clear {
    display: none !important;
  }
  .ijcore-modal-link-trans:hover .ijcore-element-clear{
    display: inline-block !important;
  }
  .ijcore-modal-link-trans input {
    padding-right: 35px;
    background: #fff !important;
    border-bottom-right-radius: 0.25rem !important;
    border-top-right-radius: 0.25rem !important;
  }
  .ijcore-modal-link-trans button{
    background: transparent;
    border: none;
    padding: 0.275rem 0.25rem 0.275rem 0;
  }
  .ijcore-modal-link-trans button:hover{
    background: transparent !important;
  }
  .ijcore-modal-link-trans .input-group {
    align-items: center;
  }

</style>

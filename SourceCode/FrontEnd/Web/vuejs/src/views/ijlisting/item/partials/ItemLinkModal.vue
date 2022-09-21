<template>
  <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
    <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
    <b-modal ref="modal" scrollable id="modal-form-input-item-link" size="xl" @hide="onHideModalDataflow($event)">
      <template slot="modal-title">
        <i class="icon-eye icon" v-if="!isForm"></i><i class="fa fa-edit" v-if="isForm"></i> {{title}}
      </template>
      <item-link-content v-model="value" v-if="!isForm" :Item="Item"></item-link-content>
      <item-link-content v-model="value" :isForm="true" v-if="isForm" :Item="Item" :SysTable="SysTable"></item-link-content>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()" v-if="!isForm"
          >
            Sửa
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdate()" v-if="isForm">
            Lưu
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideModal" v-if="isForm">
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
  </a>
</template>

<script>
  import ApiService from '@/services/api.service';
  import ItemLinkContent from "./ItemLinkContent";

  export default {
    name: 'item-link-modal',
    components: {
      ItemLinkContent
    },
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {
        isForm: false,
        listtable: [],
        tableName: '',
        search: '',
        lenghNo: 0,
        SysTable: [],
      }
    },
    created() {
    },
    mounted() {
      if (this.isDataflow) {
        this.onToggleModal();
      }
    },
    methods: {
      formatDate(data) {
        data = data.split(' ');
        data = data[0];
        data = data.split('-');
        let dd = data[2];
        let mm = data[1];
        let yyyy = data[0];
        data = dd + '/' + mm + '/' + yyyy;
        return data;
      },
      fetchData() {
        let self = this;
        let requestData = {
          method: 'get',
          data: {}
        };
        requestData.url = '/listing/api/common/get-table';
        this.$store.commit('isLoading', true);


        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          self.SysTable = [];
          _.forEach(responsesData.data, function (value, key) {
            let tmpObj = {};
            if (value.TableName !== 'item') {
              tmpObj.value = value.TableName;
              tmpObj.text = value.TableDescription;
              self.SysTable.push(tmpObj);
            }
          });

          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });

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
      onHideModal() {
        this.$refs['modal'].hide();
        this.isForm = false;
      },
      onToggleModal() {
        this.$emit('on:get-data');
        // this.$parent.onToggleItemLink(false);
        this.currentPage = 1;
        this.$refs['modal'].show();
        if (!this.$parent.showItemLink) this.$parent.showItemLink = true;
      },
      onHideModalDataflow() {
        if (this.isDataflow) {
          this.$emit('onHideModalItem');
        }
      },
      onResetModal() {
      },
      onEdit() {
        this.isForm = true;
        this.fetchData();
      },
      onUpdate() {
        this.$store.commit('isLoading', true);
        let self = this;
        const requestData = {
          method: 'post',
          url: 'listing/api/item/item-data-list',
          data: {
            ItemLink: self.value,
            ItemID: this.Item.ItemID
          }
        };
        // edit user
        requestData.data.ItemID = this.Item.ItemID;

        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
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
            );
            self.$store.commit('isLoading', false);
          }
          self.onHideModal();
        }, (error) => {
          console.log(error);
          Swal.fire(
            'Thông báo',
            'Không kết nối được với máy chủ',
            'error'
          );
          self.$store.commit('isLoading', false);
        });
      }
    },
    watch: {},
    props: {
      value: [Array, Object],
      title: {},
      name: {},
      api: {},
      Item: [Array, Object],
      table: {},
      isDataflow: false,
      per: {}
    },
  }
</script>
<style></style>

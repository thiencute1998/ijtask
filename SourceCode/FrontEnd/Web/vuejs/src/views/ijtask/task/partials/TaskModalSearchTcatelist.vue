<template>
    <div class="ijcore ijcore-modal component-modal-search-tcatelist">
      <b-modal :id="idModal" :title="titleModal"
               :content-class="'sb-modal-content' + classModal"
               :ref="refModal"
               :no-fade="noFadeModal"
               :size="sizeModal" @hide="onHideModal($event)" @show="onShowModal">
        <template v-slot:modal-footer="{ ok, cancel, hide }">
          <b-button class="mr-2 ml-0" variant="primary" @click="onSubmitSearch">
            Tìm
          </b-button>
          <b-button variant="primary" @click="hide()">
            Đóng
          </b-button>
        </template>
        <div class="ijcore-search-task-tcatelist ijcore-modal-search">
          <table class="table b-table table-sm table-bordered table-editable">
            <thead>
            <tr class="text-center">
              <th class="pr-3">Loại công việc</th>
              <th class="pr-3">Giá trị</th>
              <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, key) in value">
              <td>
                <Select2 v-model="value[key].CateID" :settings="{allowClear: true, placeholder: {id: null, text: 'Chọn loại công việc'}}" :options="TaskCateListOptions" @select="onSelectTaskCateList($event, key)"></Select2>
              </td>
              <td>
                <Select2 v-model="value[key].LineID" :settings="{allowClear: true, placeholder: {id: null, text: 'Chọn giá trị'}}" :options="TaskCateValueOptions | filterTaskCateValue(value[key].CateID)" @select="onSelectTaskCateValue($event, key)"></Select2>
              </td>
              <td style="width: 50px; vertical-align: center">
                <div class="d-flex align-content-center justify-content-center">
                  <i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o" style="font-size: 18px; cursor: pointer;"></i>
                </div>
              </td>
            </tr>
            </tbody>
          </table>
          <a @click="addLine()" class="new-row"><i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới</a>
        </div>
      </b-modal>
    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import Select2 from 'v-select2-component';
    import IjcoreSelect2Server from "@/components/IjcoreSelect2Server";

    export default {
      name: 'modal-search-tcatelist',
      components: {
        Select2,
        IjcoreSelect2Server
      },
      data () {
        return {
          TaskCateList: [],
          TaskCateListOptions: [],
          TaskCateValue: [],
          TaskCateValueOptions: [],
        }
      },
      props:{
        value: [Array, Object],
        titleModal: {
            type: String,
            default: ''
        },
        classModal: {
            type: String,
            default: ''
        },
        refModal: {
            type: String,
            default: 'appModal'
        },
        idModal: {
            type: String,
            default: 'appModal'
        },
        noFadeModal: {
            type: Boolean,
            default: false
        },
        sizeModal: {
            type: String,
            default: 'md' // sm|md|lg|xl
        },
      },
      computed: {
          rows() {
              return this.totalRows
          },
      },
      mounted() {
        // this.fetchData();
      },
      filters: {
        filterTaskCateValue(value, CateID){
          if (CateID) {
            let taskCateValue = _.filter(value, ['CateID', Number(CateID)]);
            return taskCateValue;
          }
          return [];
        },
      },
      methods: {
        fetchData(){
          let self = this;
          let requestData = {
            method: 'get',
            data: {}
          };
          requestData.url = '/task/api/common/get-task-cate-list';
          this.$store.commit('isLoading', true);

          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            let responsesData = responses.data;
            if (responsesData.status === 1) {
              self.TaskCateList = responsesData.data.TaskCateList;
              self.TaskCateListOptions = [];
              _.forEach(self.TaskCateList, function (taskCateList, key) {
                let tmpObj = {};
                tmpObj.id = taskCateList.CateID;
                tmpObj.text = taskCateList.CateName;
                self.TaskCateListOptions.push(tmpObj);
              });

              self.TaskCateValue = responsesData.data.TaskCateValue;
              self.TaskCateValueOptions = [];
              _.forEach(self.TaskCateValue, function (taskCateValue, key) {
                let tmpObj = {};
                tmpObj.id = taskCateValue.LineID;
                tmpObj.text = taskCateValue.Description;
                tmpObj.CateID = taskCateValue.CateID;
                tmpObj.CateValue = taskCateValue.CateValue;
                self.TaskCateValueOptions.push(tmpObj);
              });
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
        onSelectTaskCateList(selected, key){
          this.value[key].CateName = selected.text;
        },
        onSelectTaskCateValue(selected, key){
          this.value[key].CateValue = selected.CateValue;
          this.value[key].Description = selected.text;
        },
        addLine(){
          let tmpObj = {};
          tmpObj.CateID = null;
          tmpObj.CateName = '';
          tmpObj.LineID = null;
          tmpObj.CateValue = null;
          tmpObj.Description = '';
          this.value.push(tmpObj);
        },
        deleteLine(key){
          this.value.splice(key, 1);
        },
        onSubmitSearch(){
          this.$emit('onSubmitSearch');
          this.$bvModal.hide(this.idModal);
        },
        onShowModal(){
          if (!this.TaskCateList.length) {
            this.fetchData();
          }
        },
        onHideModal(event){}
      },
      watch: {
        'value': {
          handler(val){
            // do stuff
          },
          deep: true
        },
      }
    }
</script>
<style lang="css">
  .ijcore-modal-search-input .input-group-append {
      position: absolute;
      right: 0;
      z-index: 9;
  }
  .ijcore-modal-search-input .ijcore-element-clear {
      display: none !important;
  }
  .ijcore-modal-search-input:hover .ijcore-element-clear{
      display: inline-block !important;
  }
  .ijcore-modal-search-input input {
      padding-right: 56px;
      background: #fff !important;
      border-bottom-right-radius: 0.25rem !important;
      border-top-right-radius: 0.25rem !important;
  }
  .ijcore-modal-search-input button{
      background: transparent;
      border: none;
      padding: 0.275rem 0.5rem;
  }
  .ijcore-modal-search-input button:hover{
      background: transparent !important;
  }
  .ijcore-modal-search-input .input-group {
      align-items: center;
  }
  .ijcore-modal-data tr {
    cursor: pointer;
  }
  .ijcore-search-task-tcatelist .select2.select2-container {
    width: 100% !important;
  }
  .ijcore-modal-search .select2-container--default .select2-selection--single {
    border: none;
  }
</style>

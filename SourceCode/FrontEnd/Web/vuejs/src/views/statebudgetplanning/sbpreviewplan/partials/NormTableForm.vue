<template>
  <div>
    <a @click="onToggleModal()" class="new-row mb-2">
      <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> ĐMDTCT
    </a>

    <b-modal ref="modal-norm-table-form" id="modal-norm-table-form" size="lg"
             @shown="onShownModal"
             title="Chỉ tiêu định mức dự toán chi tiết">
      <div class="form-group row">
        <div class="col-md-24">
          <ijcore-select2-server
            v-model="NormTableID"
            :url="$store.state.appRootApi + '/listing/api/common/get-norm-table'"
            id-name="NormTableID"
            text-name="NormTableName"
            placeholder="Chọn chỉ tiêu"
            :allowClear="true"
            :delay="500">
          </ijcore-select2-server>
        </div>
      </div>
      <div class="table-responsive" v-if="LoadData">
        <table class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
          <thead>
          <tr>
            <th class="pr-3 td-ijcheckbox">
              <b-form-checkbox v-model="CheckAll" @change="checkAllItem($event)">Chọn tất cả chỉ tiêu</b-form-checkbox>
            </th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(item, key) in listtable" :style="listtable[key].Style" style="border-bottom: 1px solid #c8ced3;position: relative;"
          >
            <td class="pr-3 td-ijcheckbox" style="border: none;width: 95%;">
              <b-form-checkbox v-model="listtable[key].IsCheck">
                {{listtable[key].Description}}
              </b-form-checkbox>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button variant="primary" size="md" @click="updateForParent()" class="float-left mr-2">Chọn</b-button>
          <b-button variant="primary" size="md" @click="onHideModal()" class="float-left mr-2">Đóng</b-button>
        </div>
      </template>
    </b-modal>

<!--    <div v-show="showModal">-->
<!--      <transition name="modal">-->
<!--        <div class="modal-mask">-->
<!--          <div class="modal-wrapper">-->
<!--            <div class="modal-dialog modal-lg" role="document">-->
<!--              <div class="modal-content">-->
<!--                <div class="modal-header">-->
<!--                  <h5 class="modal-title">Chỉ tiêu định mức dự toán chi tiết</h5>-->
<!--                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
<!--                    <span aria-hidden="true" @click="showModal = false">&times;</span>-->
<!--                  </button>-->
<!--                </div>-->
<!--                <div class="modal-body fix-width">-->
<!--                  <div class="form-group row">-->
<!--                    <div class="col-md-24">-->
<!--                      &lt;!&ndash;          <b-form-select v-model="NormTableID" :options="value.NormTableOption" @change="getListNormTable($event)"></b-form-select>&ndash;&gt;-->

<!--                      <ijcore-select2-server-->
<!--                        v-model="NormTableID"-->
<!--                        :url="$store.state.appRootApi + '/listing/api/common/get-norm-table'"-->
<!--                        id-name="NormTableID"-->
<!--                        text-name="NormTableName"-->
<!--                        placeholder="Chọn chỉ tiêu"-->
<!--                        :allowClear="true"-->
<!--                        :delay="500">-->
<!--                      </ijcore-select2-server>-->
<!--                    </div>-->
<!--                  </div>-->
<!--                  <div class="table-responsive" v-if="LoadData">-->
<!--                    <table class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">-->
<!--                      <thead>-->
<!--                      <tr>-->
<!--                        <th class="pr-3 td-ijcheckbox">-->
<!--                          <b-form-checkbox v-model="CheckAll" @change="checkAllItem($event)">Chọn tất cả chỉ tiêu</b-form-checkbox>-->
<!--                        </th>-->
<!--                      </tr>-->
<!--                      </thead>-->
<!--                      <tbody>-->
<!--                      <tr v-for="(item, key) in listtable" :style="listtable[key].Style" style="border-bottom: 1px solid #c8ced3;position: relative;"-->
<!--                      >-->
<!--                        <td class="pr-3 td-ijcheckbox" style="border: none;width: 95%;">-->
<!--                          <b-form-checkbox v-model="listtable[key].IsCheck">-->
<!--                            {{listtable[key].Description}}-->
<!--                          </b-form-checkbox>-->
<!--                        </td>-->
<!--                      </tr>-->
<!--                      </tbody>-->
<!--                    </table>-->
<!--                  </div>-->
<!--                </div>-->
<!--                <div class="modal-footer">-->
<!--                  <div class="w-100 left">-->
<!--                    <b-button-->
<!--                      variant="primary"-->
<!--                      size="md"-->
<!--                      class="float-left mr-2"-->
<!--                      @click="updateForParent()"-->
<!--                    >-->
<!--                      Chọn-->
<!--                    </b-button>-->
<!--                    <b-button-->
<!--                      variant="primary"-->
<!--                      size="md"-->
<!--                      class="float-left"-->
<!--                      @click="onHideModal()"-->
<!--                    >-->
<!--                      Đóng-->
<!--                    </b-button>-->
<!--                  </div>-->
<!--                </div>-->
<!--              </div>-->
<!--            </div>-->
<!--          </div>-->
<!--        </div>-->
<!--      </transition>-->
<!--    </div>-->
  </div>
</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';
  import Select2 from 'v-select2-component';
  import IjcoreSelect2Server from '@/components/IjcoreSelect2Server';

  export default {
    name: 'NormTableForm',
    mixins: [mixinLists],
    components: {Select2, IjcoreSelect2Server},
    computed: {
      rows() {
        return this.totalRows
      },
      getItemSelected(){
        return _.filter(this.listtable, function(value, key){
          return value.IsCheck === true;
        })
      }
    },
    data() {
      return {
        listtable: [],
        search: '',
        NormTableID: null,
        CheckAll: false,
        LoadData: false,
        showModal: false
      }
    },
    created() {
    },
    mounted() {},
    directives:{
      focus: {
        // directive definition
        inserted: function (el) {
          el.focus()
        }
      }
    },
    methods: {
      clickCheckbox(){
        return false;
      },
      fetchData() {

        // scroll to top perfect scroll
        const container = document.querySelector('.b-table-sticky-header');
        if (container) container.scrollTop = 0;

      },
      updateForParent() {
        this.$emit('changed', this.getItemSelected);
        this.onResetModal();
        this.onHideModal();
      },
      onShownModal(){
        $('.modal-content').removeAttr('tabindex');
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
        this.$refs['modal-norm-table-form'].hide();
      },
      onToggleModal() {
        this.showModal = true;
        this.$refs['modal-norm-table-form'].show();
      },
      onResetModal() {
        this.listtable = [];
        this.search = '';
        this.NormTableID = null;
        this.CheckAll = false;
      },
      getListNormTable($event){

      },
      checkAllItem($event){
        _.map(this.listtable, (value)=>{
          return value.IsCheck = $event;
        })
      }
    },
    watch: {
      NormTableID(newVal){
        this.listtable = [];
        this.CheckAll = false;
        let self = this;
        let urlApi = '/listing/api/norm/get-table-item';
        let requestData = {
          method: 'post',
          url: urlApi,
          data: {
            NormTableID: newVal,
            // detail: 1,
          },
        };
        if(newVal !== null){
          this.$store.commit('isLoading', true);
          ApiService.customRequest(requestData).then(response => {
            let dataResponse = response.data;
            if(dataResponse.status === 1){
              this.LoadData = true;
              _.forEach(dataResponse.data, function (val, key) {
                val.LineID = key;
                val.Description = val.NormTableItemName;
                val.IsCheck = false;
                self.listtable.push(val);
              });
            }
            self.$store.commit('isLoading', false);
          }).catch(error=>{
            console.log(error);
            self.$store.commit('isLoading', false);
          })
        }
        else{
          this.LoadData = false;
        }
      }
    },
    props: {
      value: {
        type: Object,
        default() {
          return {
          };
        }
      },
    },
  }
</script>
<style>
  .modal-dialog{
    overflow-y: initial !important
  }
  .modal-body{
    max-height: 70vh;
    overflow-y: auto;
  }
  .readonly {
    background-color: #fff !important;
  }

  .table th, .table td {
    border-bottom: 1px solid #c8ced3;
  }

  .modal-footer ol, .modal-footer ul, .modal-footer dl {
    margin-bottom: 0px;
  }
  .td-ijcheckbox{
    padding-top: 0px !important;
    padding-bottom: 0px !important;
  }
  .td-ijcheckbox div{
    height: 30px;
    padding-top: 5px;
  }
  .td-ijcheckbox div label{
    width: 100%;
    cursor: pointer;
  }
  a.new-row{
    width: 200px;
  }
  .ij-loader{
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background-color: #000;
    opacity: 0.4;
  }
  .ij-loader img{
    position: absolute;
    left: 50%;
    top: 50%;
  }
  .modal-mask {
    position: fixed;
    z-index: 1021;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, .5);
    display: table;
    transition: opacity .3s ease;
  }

  .modal-wrapper {
    display: table-cell;
    vertical-align: top;
  }

</style>

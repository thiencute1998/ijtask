<template>
  <div class="component-modal-search-norm-level ijcore-modal-search-input ijcore ijcore-modal">
    <b-input-group>
      <b-form-input placeholder="" :title="value.NormAllotLevelNo" :readonly="true" v-model="value.NormAllotLevelNo"
                    class="readonly form-control" @click="onShowModal"></b-form-input>
      <b-input-group-append>
        <b-button variant="light" @click="onShowModal" class="ijcore-element-search"><i class="fa fa-search"></i></b-button>
      </b-input-group-append>
    </b-input-group>
    <b-modal :id="idModal" :title="titleModal"
             :content-class="'sb-modal-content' + classModal"
             :ref="refModal"
             :no-fade="noFadeModal"
             :size="sizeModal" @hide="onHideModal($event)">
      <template slot="modal-title">
        ĐMDTPB
      </template>
      <ijcore-modal-listing
        v-model="normAllotLevel"
        class="mb-3"
        @changed="getNormAllotLevel"
        title="Chỉ tiêu ĐMPBDT" :api="'/listing/api/common/list'"
        :table="'norm_allot_level'" :FieldID="'NormAllotLevelID'" :FieldName="'NormAllotLevelName'"
        :FieldNo="'NormAllotLevelNo'">
      </ijcore-modal-listing>

      <div class="table-responsive pb-10">
        <table class="table b-table table-hover table-sm b-table-selectable b-table-select-multi">
          <thead role="rowgroup" class="thead-light">
          <tr role="row" class="">
            <th aria-label="Selected" style="width: 5%;"></th>
            <th scope="col" style="width: 60%;">Chỉ tiêu ĐMPBDT</th>
            <th scope="col" style="width: 10%;">Ngày hiệu lực</th>
            <th scope="col" style="width: 10%;">Ngày hết hiệu lực</th>
            <th scope="col" style="width: 15%;">Định mức</th>
          </tr>
          </thead>
          <tbody>
          <tr :class="[(item.Checked) ? 'b-table-row-selected table-active' : '']" v-for="(item, key) in normAllotLevelItem" style="border-bottom: 1px solid #c8ced3;position: relative;">
            <td class="pr-3 td-ijcheckbox no-overflow" style="border: none; width: 5%;">
              <b-form-checkbox class="no-overflow" v-model="item.Checked" @change="selectNormAllotLevel(key)"></b-form-checkbox>
            </td>
            <td>{{item.NormAllotLevelName}}</td>
            <td class="text-center">{{item.EffectiveDate | convertServerDateToClientDate}}</td>
            <td class="text-center">{{item.ExpirationDate | convertServerDateToClientDate}}</td>
            <td class="text-right">{{item.LCUnitPrice}}</td>
          </tr>
          </tbody>
        </table>
      </div>
      <template v-slot:modal-footer>
        <div class="w-100 left">
          <b-button @click="onSaveModal" variant="primary" size="md" class="float-left">Chọn</b-button>
        </div>
      </template>

    </b-modal>
  </div>
</template>

<script>
  import ApiService from '@/services/api.service';
  import IjcoreModalListing from "../../../../components/IjcoreModalListing";
  export default {
    name: 'modal-listing-norm-allot-level',
    components: {
      IjcoreModalListing
    },
    computed: {},
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
        default: ''
      },
      noFadeModal: {
        type: Boolean,
        default: false
      },
      sizeModal: {
        type: String,
        default: 'md' // sm|md|lg|xl
      },
      idModal: [String],
    },
    data() {
      return {
        normAllotLevel: {
          NormAllotLevelID: null,
          NormAllotLevelNo: '',
          NormAllotLevelName: ''
        },
        normAllotLevelItem: [],
        CheckAll: false
      }
    },
    created() {},
    mounted() {},
    methods: {
      fetchData() {},
      getNormAllotLevel(normAllotLevel) {
        let self = this;
        let requestData = {
          method: 'get',
          url: 'listing/api/norm-table/get-norm-allot-level/' + normAllotLevel.NormAllotLevelID,
          data: {},
        };

        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let responseData = response.data;
          if (responseData.status === 1) {
            _.forEach(responseData.data, function (normAllotLevel, key) {
              if (key === 0) {
                responseData.data[key].Checked = true;
              } else {
                responseData.data[key].Checked = false;
              }
            });
            self.normAllotLevelItem = responseData.data;
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      onShowModal() {
        this.normAllotLevel.NormAllotLevelID = null;
        this.normAllotLevel.NormAllotLevelNo = '';
        this.normAllotLevel.NormAllotLevelName = '';
        this.$bvModal.show(this.idModal);
      },
      onHideModal(event){},
      onSaveModal(){
        let normAllotLevelSelected = _.find(this.normAllotLevelItem, ['Checked', true]);
        if (normAllotLevelSelected) {
          this.value.NormID = normAllotLevelSelected.NormID;
          this.value.NormNo = normAllotLevelSelected.NormNo;
          this.value.NormName = normAllotLevelSelected.NormName;
          this.value.NormAllotLevelID = normAllotLevelSelected.NormAllotLevelID;
          this.value.NormAllotLevelNo = normAllotLevelSelected.NormAllotLevelNo;
          this.value.NormAllotLevelName = normAllotLevelSelected.NormAllotLevelName;
          this.value.UomID = normAllotLevelSelected.UomID;
          this.value.UomNo = normAllotLevelSelected.UomNo;
          this.value.UomName = normAllotLevelSelected.UomName;
          this.value.CcyID = normAllotLevelSelected.CcyID;
          this.value.CcyNo = normAllotLevelSelected.CcyNo;
          this.value.CcyName = normAllotLevelSelected.CcyName;
          this.value.ExchangeRate = normAllotLevelSelected.ExchangeRate;
          this.value.UnitPriceType = normAllotLevelSelected.UnitPriceType;
          this.value.FCMinUnitPrice = normAllotLevelSelected.FCMinUnitPrice;
          this.value.FCMaxUnitPrice = normAllotLevelSelected.FCMaxUnitPrice;
          this.value.LCMinUnitPrice = normAllotLevelSelected.LCMinUnitPrice;
          this.value.LCMaxUnitPrice = normAllotLevelSelected.LCMaxUnitPrice;
          this.value.FCUnitPrice = normAllotLevelSelected.FCUnitPrice;
          this.value.LCUnitPrice = normAllotLevelSelected.LCUnitPrice;
          this.value.NormTableItemName = normAllotLevelSelected.NormAllotLevelName;
        }
        this.$emit('saved', normAllotLevelSelected);
        this.$bvModal.hide(this.idModal);
      },
      selectNormAllotLevel(key) {
        let self = this;
        _.forEach(this.normAllotLevelItem, function (normAllotLevel, keyNorm) {
          self.normAllotLevelItem[keyNorm].Checked = false;
        });
        this.normAllotLevelItem[key].Checked = true;
      }
    },
    watch: {},
  }
</script>
<style></style>

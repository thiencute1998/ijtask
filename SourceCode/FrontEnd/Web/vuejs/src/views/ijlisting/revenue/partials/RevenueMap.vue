<template>
  <div>
    <span @click="init($event)">
<!--      <i class="fa fa-sitemap" style="transform: rotate(-90deg);" aria-hidden="true"></i>-->
      <i title="Tỷ lệ điều tiết thu ngân sách" class="fa fa-external-link" style="font-size: 18px; cursor: pointer;"></i>
    </span>
    <b-modal :ref="'modal-revenue-map-' + revenue.RevenueID" :id="'modal-revenue-map-' + revenue.RevenueID" size="lg">
      <template slot="modal-title">Tỷ lệ điều tiết thu ngân sách</template>
      <template>
        <div class="table-responsive table-responsive-assign" ref="DivContainerTable">
          <table class="table b-table table-sm table-bordered" >
            <thead>
            <tr class="text-left">
              <th class="pr-3 text-center" style="width: 15%;">Ngày hiệu lực</th>
              <th class="pr-3 text-center" style="width: 15%">Ngày hết hiệu lực</th>
              <th class="pr-3 text-center" style="width: 67%">Chỉ tiêu phân bổ</th>
              <th class="pr-3 text-center" style="width: 3%">%</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, key) in RevenueReguItem">
              <td >{{RevenueReguItem[key].EffectiveDate}}</td>
              <td>{{RevenueReguItem[key].ExpirationDate}}</td>
              <td class="text-left">{{ReguRateOptions[RevenueReguItem[key].BudgetLevel].text}}</td>
              <td class="text-right">{{RevenueReguItem[key].ReguRate}}</td>
            </tr>
            </tbody>
          </table>
        </div>
      </template>
      <template v-slot:modal-footer="{ ok, cancel, hide}">
        <div class="w-100 left">
          <b-button variant="primary" size="md" class="float-left" @click="hide">Đóng</b-button>
        </div>
      </template>
    </b-modal>
  </div>
</template>

<script>
import ApiService from '@/services/api.service';
import Select2 from 'v-select2-component';


export default {
  name: 'revenue-map',
  props: {
    revenue: [Object, Array]
  },
  computed: {
    rows() {
      return this.totalRows
    },
  },
  data() {
    return {
      RevenueReguItem: [],
      ReguRateOptions : [
        {value: 0, text : 'Để lại đơn vị'},
        {value: 1, text : 'Ngân sách trung ương'},
        {value: 2, text : 'Ngân sách tỉnh'},
        {value: 3, text : 'Ngân sách huyện'},
        {value: 4, text : 'Ngân sách xã'},
      ],
    }
  },
  created() {

  },
  mounted() {
  },
  methods: {
    init(e) {
      e.preventDefault();
      e.stopPropagation();
      this.$bvModal.show('modal-revenue-map-' + this.revenue.RevenueID);
      this.fetchData();
    },
    fetchData(){
      let self = this;
      let requestData = {
        method: 'post',
        url: 'listing/api/revenue/get-revenue-regu-item' ,
        data: {
          RevenueID: self.revenue.RevenueID
        },

      };
      this.$store.commit('isLoading', true);
      ApiService.customRequest(requestData).then((response) => {
        let responseData = response.data;
        if (responseData.status === 1) {
          debugger
          self.RevenueReguItem = _.toArray(responseData.data);

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

  },
  watch: {
  }
}
</script>
<style></style>

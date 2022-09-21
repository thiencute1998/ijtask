<template>
  <div style="position: relative">
    <div class="sort-area" style="float: left;position: absolute; margin-top: 10px;">
      <b-form-select v-model="selected" :options="options" @change="changeChart"></b-form-select>
    </div>
    <canvas id="pie-chart-revenues" width="100px" height="100px"></canvas>
  </div>
</template>
<script>
  import ApiService from '@/services/api.service';
  import Chart from 'chart.js';
  import 'chartjs-plugin-labels';
  import 'chartjs-plugin-zoom';

  let PieChartRevenues = null;
  export default {
    name: 'dashboard-module-state-budget-estimate-pie-chart',
    props: {
      filter: [Array, Object],
    },
    data() {
      return {
        selected: null,
        isLoadingData: false,
        options: [
          {value: null, text: '-- Chọn cơ cấu thu --'},
          {value: 1, text: 'Thu nội địa'},
          {value: 2, text: 'Thu XNK'},
          {value: 3, text: 'Thu viện trợ'},
          {value: 4, text: 'Thu khác'},
        ],
        optionDataPie: {
          labels: ['Thu nội địa', 'Thu XNK', 'Thu viện trợ', 'Thu khác'],
          datasets: [{
            label: '',
            data: [21, 18, 15, 10],
            backgroundColor: [
              '#2eadd3',
              '#ed7d31',
              '#a5aeb7',
              '#635734'
            ],
            borderColor: [
              '#2eadd3',
              '#ed7d31',
              '#a5aeb7',
              '#635734'
            ],
            borderWidth: null
          }]
        },
      }
    },
    components: {},
    mounted () {
      let self = this;
      let ctxPie = document.getElementById('pie-chart-revenues');
      ctxPie.height = 60;
      PieChartRevenues = new Chart(ctxPie, {
        type: 'pie',
        data: {
          labels: self.optionDataPie.labels,
          datasets: [{
            label: '# of Votes',
            data: self.optionDataPie.datasets[0].data,
            backgroundColor: self.optionDataPie.datasets[0].backgroundColor,
            borderColor: self.optionDataPie.datasets[0].borderColor,
            borderWidth: 1
          }]
        },
        options: {
          scales: {
            // yAxes: [{
            //   ticks: {
            //     beginAtZero: true
            //   }
            // }]
          },

          title: {
            display: true,
            fontSize: 16,
            text: 'Cơ cấu thu ',
            position: "top"
          },
          legend: {
            display: true,
            position: 'bottom'
          },
          // 'onClick' : function (point, event) {
          //     self.$router.push({
          //         name: 'report_STD_S02-DH-CH'
          //     })
          // },
          plugins: {
            labels: {
              render: 'percentage',
              precision: 2,
              fontColor: '#000'
            }
          }
        }
      });
      this.changeChart();
    },
    methods: {
      updateData(chart, labels = [], datasets, title = '') {
        if (labels.length) {
          chart.data.labels = labels;
        }
        chart.data.datasets.forEach((item, key) => {
          if (datasets && datasets[key] && datasets[key].data) {
            item.data = datasets[key].data;
          }
          if (datasets && datasets[key] && datasets[key].backgroundColor) {
            item.backgroundColor = datasets[key].backgroundColor;
          }
          if (datasets && datasets[key] && datasets[key].borderColor) {
            item.borderColor = datasets[key].borderColor;
          }
        });
        if (title) {
          chart.options.title.title = title;
          chart.config.options.title.text = title;
          chart.config.options.title.title = title;
        }
        chart.update();
      },
      changeChart() {
        let self = this;
        let requestData = {
          method: 'post',
          url: 'dashboard/api/state-budget-estimate/revenue-pie-chart',
          data: {
            filter: this.filter,
            CateID: this.selected
          },

        };

        if (!this.isLoadingData) {
          this.isLoadingData = true;
          this.$store.commit('isLoading', true);
          ApiService.setHeader();
          ApiService.customRequest(requestData).then((response) => {
            let responseData = response.data;
            if (responseData.data && responseData.data.length) {
              self.options = [{value: null, text: '-- Chọn cơ cấu thu --'}];
              self.optionDataPie.labels = [];
              self.optionDataPie.datasets[0].data = [];
              _.forEach(responseData.data, function (value, key) {
                self.options.push({
                  value: value.RevenueID,
                  text: value.RevenueName
                });
                self.optionDataPie.labels.push(value.RevenueName);
                self.optionDataPie.datasets[0].data.push(value.FCDebitAmount);
              });
              self.updateData(PieChartRevenues, self.optionDataPie.labels, self.optionDataPie.datasets, '');
            }

            self.isLoadingData = false;
            self.$store.commit('isLoading', false);
          }, (error) => {
            console.log(error);
            self.$store.commit('isLoading', false);
          });
        }
      },
    },
    watch: {
      'filter.UserType'(){
        this.changeChart();
      },
      'filter.ProvinceID'(){
        this.changeChart();
      },
      'filter.DistrictID'(){
        this.changeChart();
      },
      'filter.CommuneID'(){
        this.changeChart();
      },
      'filter.CompanyID'(){
        this.changeChart();
      },
      'filter.FromDate'(){
        this.changeChart();
      },
      'filter.ToDate'(){
        this.changeChart();
      },
      'filter.SectorID'(){
        this.changeChart();
      },
      'filter.PeriodID'(){
        this.changeChart();
      }
    }
  }
</script>

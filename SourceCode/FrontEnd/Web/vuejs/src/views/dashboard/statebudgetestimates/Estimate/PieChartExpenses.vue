<template>
  <div style="position: relative">
    <div class="sort-area-2" style="float: left; position: absolute; margin-top: 10px;">
      <b-form-select v-model="selected" :options="options" @change="changeChart"></b-form-select>
    </div>
    <canvas id="pie-chart-expense" width="100px" height="100px"></canvas>
  </div>
</template>
<script>
  import ApiService from '@/services/api.service';
  import Chart from 'chart.js';
  import 'chartjs-plugin-labels';
  import 'chartjs-plugin-zoom';

  let PieChartExpenses = null;
  export default {
    name: 'dashboard-module-state-budget-estimate-pie-chart',
    props: {
      filter: [Array, Object],
    },
    data: function () {
      return {
        selected: null,
        isLoadingData: false,
        options: [
          {value: null, text: '-- Chọn cơ cấu chi --'},
          {value: 1, text: 'Chi thường xuyên'},
          {value: 2, text: 'Chi đầu tư phát triển'},
          {value: 3, text: 'Chi cải cách tiền lương'},
          {value: 4, text: 'Chi viện trơ'},
          {value: 5, text: 'Chi trả nợ'},
          {value: 6, text: 'Chi khác'},
        ],
        optionDataPie: {
          labels: ['Chi thường xuyên', 'Chi đầu tư phát triển', 'Chi cải cách tiền lương', 'Chi viện trợ', 'Chi trả nợ', 'Chi khác'],
          datasets: [{
            label: '',
            data: [21, 18, 15, 10, 20, 30],
            backgroundColor: [
              '#2eadd3',
              '#ed7d31',
              '#a5aeb7',
              '#635734',
              '#454501',
              '#0b6aad'

            ],
            borderColor: [
              '#2eadd3',
              '#ed7d31',
              '#a5aeb7',
              '#635734',
              '#454501',
              '#0b6aad'
            ],
            borderWidth: null
          }]
        },
      }
    },
    components: {},
    mounted () {
      let self = this;
      let ctxPie = document.getElementById('pie-chart-expense');
      ctxPie.height = 60;
      PieChartExpenses = new Chart(ctxPie, {
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
            text: 'Cơ cấu chi',
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
          url: 'dashboard/api/state-budget-estimate',
          data: {
            filter: this.filter,
            CateID: this.selected
          }
        };
        // Api edit user
        if (!this.isLoadingData) {
          this.isLoadingData = true;
          this.$store.commit('isLoading', true);
          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            let responsesData = responses.data;
            if (responsesData.data && responsesData.data.length) {
              self.options = [{value: null, text: '-- Chọn cơ cấu chi --'}];
              self.optionDataPie.labels = [];
              self.optionDataPie.datasets[0].data = [];
              _.forEach(responsesData.data, function (value, key) {
                self.options.push({
                  value: value.ExpenseID,
                  text: value.ExpenseName
                });
                self.optionDataPie.labels.push(value.ExpenseName);
                self.optionDataPie.datasets[0].data.push(value.FCDebitAmount);
              });
              self.updateData(PieChartExpenses, self.optionDataPie.labels, self.optionDataPie.datasets, '');
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

<template>
  <div style="position: relative">
    <canvas id="pie-chart-total-revenue" width="100px" height="100px"></canvas>
    <span class="icon-chart-full-width"><i class="fa fa-expand" @click="chartFullWidth"></i></span>
    <b-modal id="modal-pie-chart-total-revenue-fullwidth" size="xl" ref="modal-pie-chart-total-revenue-fullwidth" title="Tổng thu" ok-only ok-title="Đóng">
      <canvas id="pie-chart-total-revenue-fullwidth" height="150px"></canvas>
    </b-modal>
  </div>
</template>
<script>
  import ApiService from '@/services/api.service';
  import Chart from 'chart.js';
  import 'chartjs-plugin-labels';
  import 'chartjs-plugin-zoom';

  let PieChart = null;
  export default {
    name: 'dashboard-module-state-budget-revenues-pie-chart-total-revenue',
    props: {
      filter: [Array, Object]
    },
    data: function () {
      return {
        optionDataPie: {
          labels: ['TW', 'Tỉnh', 'Huyện'],
          datasets: [{
            label: '',
            data: [21, 18, 15],
            backgroundColor: [
              '#2eadd3',
              '#ed7d31',
              '#969696',
              '#6b6b0d'
            ],
            borderColor: [
              '#2eadd3',
              '#ed7d31',
              '#969696',
              '#6b6b0d'
            ],
            borderWidth: null
          }]
        },
      }
    },
    components: {},
    mounted () {
      let self = this;
      let ctxPie = document.getElementById('pie-chart-total-revenue');
      ctxPie.height = 75;
      PieChart = new Chart(ctxPie, {
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
            text: 'Thu NSNN theo cấp NS (tỷ đồng)',
            position: "top"
          },
          legend: {
            display: true,
            position: 'top'
          },
          // 'onClick' : function (point, event) {
          //   self.$router.push({
          //     name: 'report_STD_S02-DH-CH'
          //   })
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
        self.optionDataPie.datasets[0].data = []
        switch (this.filter.UserType) {
          case 1:
            this.optionDataPie.labels = ['TW', 'Tỉnh'];
            _.forEach(this.optionDataPie.labels, function (label, key) {
              let randomNumber = _.random(1, 100);
              self.optionDataPie.datasets[0].data.push(randomNumber);
            });
            break;
          case 2:
            this.optionDataPie.labels = ['TW', 'Tỉnh'];
            _.forEach(this.optionDataPie.labels, function (label, key) {
              let randomNumber = _.random(1, 100);
              self.optionDataPie.datasets[0].data.push(randomNumber);
            });
            break;
          case 3:
            this.optionDataPie.labels = ['TW', 'Tỉnh', 'Huyện'];
            _.forEach(this.optionDataPie.labels, function (label, key) {
              let randomNumber = _.random(1, 100);
              self.optionDataPie.datasets[0].data.push(randomNumber);
            });
            break;
          case 4:
            this.optionDataPie.labels = ['TW', 'Tỉnh', 'Huyện', 'Xã'];
            _.forEach(this.optionDataPie.labels, function (label, key) {
              let randomNumber = _.random(1, 100);
              self.optionDataPie.datasets[0].data.push(randomNumber);
            });
            break;
          case 5:
            this.optionDataPie.labels = ['TW', 'Tỉnh', 'Huyện', 'Xã'];
            _.forEach(this.optionDataPie.labels, function (label, key) {
              let randomNumber = _.random(1, 100);
              self.optionDataPie.datasets[0].data.push(randomNumber);
            });
            break;
          default:
            this.optionDataPie.labels = ['TW', 'Tỉnh', 'Huyện'];
            _.forEach(this.optionDataPie.labels, function (label, key) {
              let randomNumber = _.random(1, 100);
              self.optionDataPie.datasets[0].data.push(randomNumber);
            });
            break;
        }
        this.updateData(PieChart, this.optionDataPie.labels, this.optionDataPie.datasets, '');
      },
      chartFullWidth() {
        this.$bvModal.show('modal-pie-chart-total-revenue-fullwidth');
        let self = this;
        this.$nextTick(() => {
          let ctxChartFullwidth = document.getElementById('pie-chart-total-revenue-fullwidth').getContext('2d');
          let ChartFullwidth = new Chart(ctxChartFullwidth, {
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
                text: 'Thu NSNN theo cấp NS (tỷ đồng)',
                position: "top"
              },
              legend: {
                display: true,
                position: 'bottom'
              },
              // 'onClick' : function (point, event) {
              //   self.$router.push({
              //     name: 'report_STD_S02-DH-CH'
              //   })
              // },
              plugins: {
                labels: {
                  render: 'percentage',
                  precision: 2,
                  fontColor: '#fff'
                }
              }
            }
          });
        });
      }
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

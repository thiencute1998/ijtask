<template>
  <div style="position: relative">
    <canvas id="pie-chart-total-rxpense" width="100px" height="100px"></canvas>
    <span class="icon-chart-full-width"><i class="fa fa-expand" @click="chartFullWidth"></i></span>
    <b-modal id="modal-pie-chart-total-rxpense-fullwidth" size="xl" ref="modal-pie-chart-total-rxpense-fullwidth" title="Tổng chi" ok-only ok-title="Đóng">
      <canvas id="pie-chart-total-rxpense-fullwidth" height="150px"></canvas>
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
    name: 'dashboard-module-state-budget-rxpenses-pie-chart-total-rxpense',
    props: {
      filter: [Array, Object],
      value: [Array, Object],
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
              '#92D050',
              '#969696',
              'green'
            ],
            borderColor: [
              '#2eadd3',
              '#92D050',
              '#969696',
              'green'
            ],
            borderWidth: null
          }]
        },
      }
    },
    components: {},
    mounted () {
      let self = this;
      let ctxPie = document.getElementById('pie-chart-total-rxpense');
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
            text: 'Chi NSNN theo cấp NS (tỷ đồng)',
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
        if(self.value.length === 1 && !self.value[0].Stt) {
          self.optionDataPie.labels = ['Không có số liệu'];
          self.optionDataPie.datasets[0].data = [];
          self.optionDataPie.datasets[0].borderColor =['#cccccc'];
          self.optionDataPie.datasets[0].backgroundColor = ['#cccccc'];
        };
        if(self.value.length >= 1 && self.value[0].Stt){
          _.forEach(self.value, function (val, key){
            if(val.data){
              self.optionDataPie.datasets[0].data[val.Stt - 1] = val.data
            }
          });
        };
        this.updateData(PieChart, this.optionDataPie.labels, this.optionDataPie.datasets, '');
      },
      chartFullWidth() {
        this.$bvModal.show('modal-pie-chart-total-rxpense-fullwidth');
        let self = this;
        this.$nextTick(() => {
          let ctxChartFullwidth = document.getElementById('pie-chart-total-rxpense-fullwidth').getContext('2d');
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
                text: 'Chi NSNN theo cấp NS (tỷ đồng)',
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
     'value'(){
       this.changeChart();
     }
    }
  }
</script>

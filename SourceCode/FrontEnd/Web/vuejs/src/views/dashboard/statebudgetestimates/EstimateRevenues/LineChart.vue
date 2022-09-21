<template>
  <div style="position: relative">
    <canvas id="line-chart-total-revenue" height="180px"></canvas>
    <span class="icon-chart-full-width"><i class="fa fa-expand" @click="chartFullWidth"></i></span>
    <b-modal id="modal-line-chart-total-revenue-fullwidth" size="xl" ref="modal-line-chart-total-revenue-fullwidth" title="Tổng thu" ok-only ok-title="Đóng">
      <canvas id="line-chart-total-revenue-fullwidth" height="150px"></canvas>
    </b-modal>
  </div>
</template>
<script>
  import ApiService from '@/services/api.service';
  import Chart from 'chart.js';
  import 'chartjs-plugin-labels';
  import 'chartjs-plugin-zoom';

  let LineChart = null;
  export default {
    name: 'dashboard-module-state-budget-revenues-line-chart-total-revenue',
    props: {
      filter: [Array, Object],
      overviewNumber: [Array, Object],
      value: [Array, Object],
      labelsType: [String],
    },
    data: function () {
      return {
        optionDataLineChar: {
          labels: ['T01', 'T02', 'T03', 'T04', 'T05', 'T06', 'T07','T08', 'T09', 'T10', 'T11', 'T12'],
          datasets: [
            {
              label: 'Dự toán thu',
              data: [1246, 1342, 1498, 1689, 1645, 1765, 1538, 1476, 1565, 1776, 1740, 1803],
              borderColor: '#2eadd3',
              backgroundColor: '#2eadd3',
              fill: false,
              // borderColor: 'rgb(75, 192, 192)',
              tension: 0.1
            },
            {
              label: 'Uớc thực hiện thu',
              data: [1146, 1442, 1398, 1589, 1745, 1665, 1638, 1576, 1465, 1676, 1640, 1903],
              backgroundColor: '#92D050',
              fill: false,
              borderColor: '#92D050',
              tension: 0.1
            }
          ]
        }
      }
    },
    components: {},
    mounted () {
      let self = this;
      let ctxLineChart = document.getElementById('line-chart-total-revenue');
      ctxLineChart.height = 180;
      LineChart = new Chart(ctxLineChart, {
        type: 'line',
        data: {
          labels: this.optionDataLineChar.labels,
          datasets: this.optionDataLineChar.datasets
        },
        options: {
          title: {
            display: true,
            text: 'Dự toán Thu NSNN theo kỳ ngân sách (tỷ đồng)',
            position: "top",
            fontSize: 16
          },
          // 'onClick' : function (point, event) {
          //   self.$router.push({
          //     name: 'report_STD_S02-DH-CH'
          //   })
          // },
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            },
            title: {
              display: true,
              text: 'Chart.js Line Chart'
            },
            // zoom: {
            //   pan: {
            //     enabled: true,
            //     mode: 'x',
            //     sensitivity: 0.25,
            //   },
            //   zoom: {
            //     enabled: true,
            //     mode: 'x',
            //     sensitivity: 0.25,
            //   }
            // }
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
      changeChart(){
        let self = this;
        switch (this.filter.PeriodID) {
          case 1:
            this.optionDataLineChar.labels = ['T01', 'T02', 'T03', 'T04', 'T05', 'T06', 'T07', 'T08', 'T09', 'T10', 'T11', 'T12'];
            break;
          case 2:
            if (this.filter.PeriodValue === null) {
              this.optionDataLineChar.labels = ['Qúy 1', 'Quý 2', 'Quý 3', 'Quý 4'];
            } else if (this.filter.PeriodValue === 1) {
              this.optionDataLineChar.labels = ['T01', 'T02', 'T03'];
            }else if (this.filter.PeriodValue === 2) {
              this.optionDataLineChar.labels = ['T04', 'T05', 'T06'];
            }else if (this.filter.PeriodValue === 3) {
              this.optionDataLineChar.labels = ['T07', 'T08', 'T09'];
            }else if (this.filter.PeriodValue === 4) {
              this.optionDataLineChar.labels = ['T10', 'T11', 'T12'];
            }
            break;
          case 3:
            if (this.filter.PeriodValue === null) {
              this.optionDataLineChar.labels = ['T01', 'T02', 'T03', 'T04', 'T05', 'T06', 'T07', 'T08', 'T09', 'T10', 'T11', 'T12'];
            } else if(this.filter.PeriodValue === 1 || this.filter.PeriodValue === 3 || this.filter.PeriodValue === 5 || this.filter.PeriodValue === 7 || this.filter.PeriodValue === 8 || this.filter.PeriodValue === 10 || this.filter.PeriodValue === 12 ) {
              this.optionDataLineChar.labels = [
                '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12',
                '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24',
                '25', '26', '27', '28', '29', '30', '31'
              ];
            } else if(this.filter.PeriodValue === 4 || this.filter.PeriodValue === 6 || this.filter.PeriodValue === 9 || this.filter.PeriodValue === 11 ){
              this.optionDataLineChar.labels = [
                '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12',
                '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24',
                '25', '26', '27', '28', '29', '30'
              ];
            } else {
              this.optionDataLineChar.labels = [
                '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12',
                '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24',
                '25', '26', '27', '28'
              ];
            }
            break;
          case 4:
            if (this.filter.PeriodValue === null) {
              this.optionDataLineChar.labels = ['T01', 'T02', 'T03', 'T04', 'T05', 'T06', 'T07', 'T08', 'T09', 'T10', 'T11', 'T12',
                'T13', 'T14', 'T15', 'T16', 'T17', 'T18', 'T19', 'T20', 'T21', 'T22', 'T23', 'T24', 'T25', 'T26', 'T27', 'T28', 'T29', 'T30', 'T31', 'T32', 'T33', 'T34',
                'T35', 'T36', 'T37', 'T38', 'T39', 'T40', 'T41', 'T42', 'T43', 'T44', 'T45', 'T46',
                'T47', 'T48', 'T49', 'T50', 'T51', 'T52', 'T53', 'T54'
              ];
            } else {
              this.optionDataLineChar.labels = ['01', '02', '03', '04', '05', '06', '07'];
            }

            break;
          case 5:
            this.optionDataLineChar.labels = ['T01', 'T02', 'T03', 'T04', 'T05', 'T06', 'T07', 'T08', 'T09', 'T10', 'T11', 'T12'];
            break;
          case 6:
            if (this.filter.PeriodValue === 1) {
              this.optionDataLineChar.labels = ['T01', 'T02', 'T03', 'T04', 'T05', 'T06'];
            }else {
              this.optionDataLineChar.labels = ['T07', 'T08', 'T09', 'T10', 'T11', 'T12'];
            }
            break;
          case 7:
            this.optionDataLineChar.labels = ['T01', 'T02', 'T03', 'T04', 'T05', 'T06', 'T07', 'T08', 'T09'];
            break;
          case 8:
            if (this.filter.PeriodValue === 1) {
              this.optionDataLineChar.labels = ['2018', '2019', '2020'];
            }else if (this.filter.PeriodValue === 2) {
              this.optionDataLineChar.labels = ['2021', '2022', '2023'];
            } else {
              this.optionDataLineChar.labels = ['2024', '2025', '2026'];
            }

            break;
          case 9:
            if (this.filter.PeriodValue === 1) {
              this.optionDataLineChar.labels = ['2016', '2017', '2018', '2019', '2020'];
            }else if (this.filter.PeriodValue === 2) {
              this.optionDataLineChar.labels = ['2021', '2022', '2023', '2024', '2025'];
            } else {
              this.optionDataLineChar.labels = ['2026', '2027', '2028', '2029', '2030'];
            }

            break;
          case 10:
            if (this.filter.PeriodValue === 1) {
              this.optionDataLineChar.labels = ['2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020'];
            }else if (this.filter.PeriodValue === 2) {
              this.optionDataLineChar.labels = ['2021', '2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029', '2030'];
            } else {
              this.optionDataLineChar.labels = ['2031', '2032', '2033', '2034', '2035', '2036', '2037', '2038', '2039', '2040'];
            }
            break;
          case 99:
            this.optionDataLineChar.labels = ['T01', 'T02', 'T03', 'T04', 'T05', 'T06', 'T07', 'T08', 'T09', 'T10', 'T11', 'T12'];
            break;
          default:
            this.optionDataLineChar.labels = ['T01', 'T02', 'T03', 'T04', 'T05', 'T06', 'T07', 'T08', 'T09', 'T10', 'T11', 'T12'];
            break;
        }
        self.optionDataLineChar.datasets[0].data = [];
        self.optionDataLineChar.datasets[1].data = [];
        _.forEach(self.optionDataLineChar.labels, function (val, key){
          self.optionDataLineChar.datasets[0].data.push(0);
          self.optionDataLineChar.datasets[1].data.push(0);
        });
        _.forEach(self.value , function (val, key){
          if(val.DT){
            self.optionDataLineChar.datasets[0].data[val.STT -1] = val.DT;
          }
          if(val.UTT){
            self.optionDataLineChar.datasets[1].data[val.STT- 1] = val.UTT;
          }
        });
        this.updateData(LineChart, this.optionDataLineChar.labels, this.optionDataLineChar.datasets, '');
      },
      chartFullWidth() {
        this.$bvModal.show('modal-line-chart-total-revenue-fullwidth');
        this.$nextTick(() => {
          let ctxLineChartFullwidth = document.getElementById('line-chart-total-revenue-fullwidth').getContext('2d');
          let LineChartFullwidth = new Chart(ctxLineChartFullwidth, {
            type: 'line',
            data: {
              labels: this.optionDataLineChar.labels,
              datasets: this.optionDataLineChar.datasets
            },
            options: {
              title: {
                display: true,
                text: 'Thu NSNN theo kỳ ngân sách (tỷ đồng)',
                position: "top",
                fontSize: 16
              },
              // 'onClick' : function (point, event) {
              //   self.$router.push({
              //     name: 'report_STD_S02-DH-CH'
              //   })
              // },
              responsive: true,
              plugins: {
                legend: {
                  position: 'top',
                },
                title: {
                  display: true,
                  text: 'Chart.js Line Chart'
                },
              }
            }
          });
        });
      }
    },
    watch: {
      value(){
        this.changeChart();
      }
    }
  }
</script>

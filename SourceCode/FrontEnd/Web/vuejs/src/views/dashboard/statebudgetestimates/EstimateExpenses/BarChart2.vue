<template>
  <div>
    <div class="sort-area" style="position: absolute;">
      <select class="dropdown" style="background-color: rgb(255 255 255)" id="sort-cocau-2" v-model="model1.SortValue">
        <option value="" selected="true" disabled="disabled">Sắp xếp</option>
        <option value="1">Tăng dần</option>
        <option value="2">Giảm dần</option>
        <option value="3">Cơ cấu chi</option>
      </select>
    </div>
    <canvas id="bar-chart-2" height="180px"></canvas>
    <span class="icon-bar-chart-2-full-width"><i class="fa fa-expand" @click="chartFullWidth"></i></span>
    <b-modal id="modal-bar-chart-2-fullwidth" size="xl" ref="modal-bar-chart-2-fullwidth" title="Tổng chi" ok-only ok-title="Đóng">
      <canvas id="bar-chart-2-fullwidth" height="150px"></canvas>
    </b-modal>
  </div>
</template>
<script>
import ApiService from '@/services/api.service';
import Chart from 'chart.js';
import 'chartjs-plugin-labels';
import 'chartjs-plugin-zoom';
import moment from "moment";

let BarChart2 = null;
let ChartFullwidth = null;
export default {
  name: 'dashboard-module-state-budget-expenses-bar-chart-2',
  props: {
    filter: [Array, Object],
    overviewNumber: [Array, Object],
    model: [Object],
    value: [Array, Object],
  },
  data: function () {
    return {
      optionDataBar2: {
        labels: ['GDĐT và dạy nghề', 'Khoa học và công nghệ', 'Quốc phòng, an ninh',
          'Y tế, dân số và gia đình', 'VHTT, TDTH, truyền hình', 'Công nghiệp, XD, KM và KK',
          'Nông nghiệp, lâm, ngư nghiệp, thủy lợi', 'Kinh tế khác', 'Hoạt động QLNN, Đảng, đoàn thể', 'Các khoản chi khác'
        ],
        norder: [1,2,3,4,5,6,7,8,9,10],
        datasets: [
          {
            label: "Dự toán chi",
            backgroundColor: "#2eadd3",
            fill: false,
            data: [1333,658,327,1936,166,821,854,1609,518,7045],
          },
          {
            label: "Ước thực hiện chi ",
            backgroundColor: "#92D050",
            fill: false,
            data: [1339,131,224,4393,172,1049,1091,2057,526,7507],
          },
        ]
      },
      model1: {
        SortValue: "",
      }
    }
  },
  components: {},
  mounted () {
    let self = this;
    let ctxBar2 = document.getElementById('bar-chart-2');
    ctxBar2.height = 150;
    BarChart2 = new Chart(ctxBar2, {
      type: 'horizontalBar',
      data: {
        labels: self.optionDataBar2.labels,
        datasets: self.optionDataBar2.datasets,
        norder: self.optionDataBar2.norder,
      },
      options: {
        responsive: true,
        title: {
          display: true,
          text: 'Dự toán chi NSNN theo lĩnh vực (tỷ đồng)',
          position: "top",
          fontSize: 16
        },
        legend: {
          display: true, position: 'top'
        },
        // 'onClick' : function (point, event) {
        //   self.$router.push({
        //     name: 'report_BCDH_B02-DH-CH'
        //   })
        // },
        barValueSpacing: 10,
        scales: {
          xAxes: [{
            stacked: false,
            ticks: {
              min: 0,
            },
            gridLines: {
              offsetGridLines: true
            }
          }],
          yAxes: [{
            stacked: false,
            beginAtZero: true,
            barPercentage: 0.6,
            gridLines: {
              display:false
            }
          }]
        },
        plugins: {
          labels: {
            render: function (args) {
              let max = 17; //This is the default 100% that will be used if no Max value is found
              try {
                //Try to get the actual 100% and overwrite the old max value
                max = Object.values(args.dataset.data).map((num) => {
                  return +num; //Convert num to integer
                });
                max = Math.max.apply(null, max);
              } catch (e) {}
              return (args.value);
            }
          },
        },
        hover: {
          animationDuration: 0
        },
        animation: {
          duration: 1,
          onComplete: function () {
            let chartInstance = this.chart,
              ctx = chartInstance.ctx;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';
            ctx.font = 'bold 9px "Helvetica Neue", Helvetica, Arial, sans-serif'
            this.data.datasets.forEach(function (dataset, i) {
              let meta = chartInstance.controller.getDatasetMeta(i);
              meta.data.forEach(function (bar, index) {
                let data = dataset.data[index];
                ctx.fillText(data, bar._model.x + 10, bar._model.y + 5);
              });
            });
          }
        }
      }
    });
  },
  methods: {
    updateData(chart, labels = [], norder = [], datasets, title = '') {
      if (labels.length) {
        chart.data.labels = labels;
      }
      if (norder.length) {
        chart.data.norder = norder;
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

      // chart.data.labels.push(labels);
      // chart.data.datasets.forEach((datasets) => {
      //   dataset.data.push(data);
      // });
    },
    sortChart(){
      let self = this;
      let arrayLabel = self.optionDataBar2.labels;
      let arrayNorder = self.optionDataBar2.norder;
      let arrayThucHien = self.optionDataBar2.datasets[0].data;
      let arrayDuToan = self.optionDataBar2.datasets[1].data;
      let arrayOfObj = arrayLabel.map(function(d, i) {
        return {
          label: d,
          norder: arrayNorder[i],
          data: arrayThucHien[i] || 0,
          data1: arrayDuToan[i],
        };
      });
      let sortedArrayOfObj = [];
      if(this.model1.SortValue != ''){
        if(this.model1.SortValue == 1){
          sortedArrayOfObj = _.orderBy(arrayOfObj, ['data'], ['asc']);
        }
        else if(this.model1.SortValue == 2){
          sortedArrayOfObj = _.orderBy(arrayOfObj, ['data'], ['desc']);
        }
        else{
          sortedArrayOfObj = _.orderBy(arrayOfObj, ['norder'], ['asc']);
        }
        let newArrayLabel = [];
        let newArrayNorder = [];
        let newArrayThucHien = [];
        let newArrayDuToan = [];
        sortedArrayOfObj.forEach(function(d){
          newArrayLabel.push(d.label);
          newArrayNorder.push(d.norder);
          newArrayThucHien.push(d.data);
          newArrayDuToan.push(d.data1)
        });
        self.optionDataBar2.labels = newArrayLabel;
        self.optionDataBar2.norder = newArrayNorder;
        self.optionDataBar2.datasets[0].data = newArrayThucHien;
        self.optionDataBar2.datasets[1].data = newArrayDuToan;
        this.updateData(BarChart2, self.optionDataBar2.labels, self.optionDataBar2.norder, self.optionDataBar2.datasets, '');
      }

    },
    chartFullWidth() {
      this.$bvModal.show('modal-bar-chart-2-fullwidth');
      let self = this;
      this.$nextTick(() => {
        let ctxChartFullwidth = document.getElementById('bar-chart-2-fullwidth');
        ChartFullwidth = new Chart(ctxChartFullwidth, {
          type: 'horizontalBar',
          data: {
            labels: self.optionDataBar2.labels,
            datasets: self.optionDataBar2.datasets,
            norder: self.optionDataBar2.norder
          },
          options: {
            responsive: true,
            title: {
              display: true,
              text: 'Chi NSNN theo lĩnh vực (tỷ đồng)',
              position: "top",
              fontSize: 16
            },
            legend: {
              display: true, position: 'top'
            },
            // 'onClick' : function (point, event) {
            //   self.$router.push({
            //     name: 'report_BCDH_B02-DH-CH'
            //   })
            // },
            barValueSpacing: 10,
            scales: {
              xAxes: [{
                stacked: false,
                ticks: {
                  min: 0
                },
                gridLines: {
                  offsetGridLines: true
                }
              }],
              yAxes: [{
                stacked: false,
                beginAtZero: true,
                barPercentage: 0.6,
                gridLines: {
                  display:false
                }
              }]
            },
            plugins: {
              labels: {
                render: function (args) {
                  let max = 17; //This is the default 100% that will be used if no Max value is found
                  try {
                    //Try to get the actual 100% and overwrite the old max value
                    max = Object.values(args.dataset.data).map((num) => {
                      return +num; //Convert num to integer
                    });
                    max = Math.max.apply(null, max);
                  } catch (e) {}
                  return (args.value);
                }
              },
            },
            hover: {
              animationDuration: 0
            },
            animation: {
              duration: 1,
              onComplete: function () {
                let chartInstance = this.chart,
                  ctx = chartInstance.ctx;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                ctx.font = 'bold 12px "Helvetica Neue", Helvetica, Arial, sans-serif'
                this.data.datasets.forEach(function (dataset, i) {
                  let meta = chartInstance.controller.getDatasetMeta(i);
                  meta.data.forEach(function (bar, index) {
                    let data = dataset.data[index];
                    ctx.fillText(data, bar._model.x + 15, bar._model.y + 7);
                  });
                });
              }
            }
          }
        });
      });
    },
    getRandomExpense(totalEstimate, totalExpense, keyDataset){
      let self = this;
      if(self.optionDataBar2.labels.length){
        let total = 0;
        for (let i = self.optionDataBar2.labels.length - 1; i >= 0; i--) {
          let randomNumber = 0;
          if(keyDataset == 1){
            if(i != 0){
              randomNumber = _.random(Number(totalEstimate)/Number(self.optionDataBar2.labels.length * 2), Number(totalEstimate)/Number(self.optionDataBar2.labels.length));
              total += randomNumber;
            }
            else{
              randomNumber = Number(totalEstimate) - total;
            }
          }
          else{
            if(i != 0){
              randomNumber = _.random(Number(totalExpense)/Number(self.optionDataBar2.labels.length * 2), Number(totalExpense)/Number(self.optionDataBar2.labels.length));
              total += randomNumber;
            }
            else{
              randomNumber = Number(totalExpense) - total;
            }
          }
          randomNumber = Math.round(randomNumber);
          self.optionDataBar2.datasets[keyDataset].data.push(randomNumber);
          self.optionDataBar2.datasets[keyDataset].data = self.optionDataBar2.datasets[keyDataset].data.reverse();
        }
      }
    },
    changeChartExpense(){
      let self = this;
      self.optionDataBar2.labels = [];
      self.optionDataBar2.datasets[0].data = [],
      self.optionDataBar2.datasets[1].data = []
      if(self.value){
        _.forEach(self.value, function (val, key){
          if(val.CateName){
            self.optionDataBar2.labels.push(val.CateName);
          }
          if(val.DT){
            self.optionDataBar2.datasets[0].push(val.DT);
          } else self.optionDataBar2.datasets[0].push(0);
          if(val.DT){
            self.optionDataBar2.datasets[1].push(val.UTT);
          } else self.optionDataBar2.datasets[1].push(0);
        });
      }
      this.updateData(BarChart2, self.optionDataBar2.labels, self.optionDataBar2.norder, self.optionDataBar2.datasets, '');
      this.sortChart();
    },
  },
  watch: {
    'value'(){
      this.changeChartExpense();
    }
  }
}
</script>
<style lang="css">
.icon-bar-chart-2-full-width{
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  border-radius: 4px;
  cursor: pointer;
  position: absolute;
  top: 30px !important;
  right: 40px !important;
}
</style>

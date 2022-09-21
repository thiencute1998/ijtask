<template>
  <div>
    <div class="sort-area" style="position: absolute;">
      <select class="dropdown" style="background-color: rgb(255 255 255)" id="sort-cocau-2" v-model="SortValue">
        <option value="" selected="true" disabled="disabled">Sắp xếp</option>
        <option value="1">Tăng dần</option>
        <option value="2">Giảm dần</option>
        <option value="3">Cơ cấu chi</option>
      </select>
    </div>
    <canvas id="bar-chart-3" height="180px"></canvas>
    <div class="chart-icons-action">
      <span class="icon-item"><i class="fa fa-expand" @click="chartFullWidth"></i></span>
    </div>
    <b-modal id="modal-bar-3-chart-fullwidth" size="xl" ref="modal-bar-3-chart-fullwidth" title="Tổng chi" ok-only ok-title="Đóng">
      <canvas id="bar-3-chart-fullwidth" height="150px"></canvas>
      <template #modal-footer="{ ok, cancel, hide }">
        <div class="left">
          <b-button
            variant="primary"
            size="md"
            @click="ok"
            class="float-left mr-2">
            Đóng
          </b-button>
        </div>
        <div class="right" style="position: absolute; right: 8px;">
          <b-button variant="primary" size="md" class="float-left mr-2">
            Excel
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2">
            PDF
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2">
            Email
          </b-button>
          <b-button variant="primary" size="md" class="float-left mr-2">
            In
          </b-button>

          <b-button-group id="main-header-views" class="main-header-views">
            <b-button id="tooltip-view-prev" class="main-header-view"><i class="fa fa-angle-left"></i></b-button>
            <b-button id="tooltip-view-next" class="main-header-view"><i class="fa fa-angle-right"></i></b-button>
          </b-button-group>
        </div>
      </template>
    </b-modal>

    <div class="text-to-voice">
      <audio controls>
        <!--        <source src="horse.ogg" type="audio/ogg">-->
        <source :src="$store.state.appRootApi + '/audio/dashboard.mp3'" type="audio/mpeg">
        Your browser does not support the audio element.
      </audio>
      <div id="close-audio" style="position: absolute;top: 50%;right: -15px;transform: translateY(-50%);cursor: pointer;">
        <i class="fa fa-close" style="font-size: 14px; color: #9A9FB0"></i>
      </div>
    </div>

  </div>
</template>
<script>
import ApiService from '@/services/api.service';
import Chart from 'chart.js';
import 'chartjs-plugin-labels';
import 'chartjs-plugin-zoom';
import moment from "moment";

let BarChart3 = null;
export default {
  name: 'dashboard-module-state-budget-expenses-bar-chart-3',
  props: {
    filter: [Array, Object],
    overviewNumber: [Array, Object],
    model: [Array, Object],
    value: [Array, Object],
  },
  data: function () {
    return {
      optionDataChart: {
        labels: [
          'Chi đầu tư và phát triển',
          'Chi thường xuyên',
          'Chi các CTMT',
          'Chi trả nợ lãi do CQĐP vay',
          'Chi cải cách tiền lương',
          'Các khoản chi còn lại',
        ],
        norder: [
          100,200,300,400,500,600
        ],
        datasets: [
          {
            label: "Dự toán chi ",
            backgroundColor: "#2eadd3",
            fill: true,
            data: [6185, 3925, 111, 9, 214, 4824]
          },
          {
            label: "Ước thực hiện chi",
            backgroundColor: "#92D050",
            fill: true,
            data: [9137, 4788, 111, 9, 215, 4228]
          },


        ]
      },
      SortValue: "",
      isLoadingData: false
    }
  },
  components: {},
  mounted () {
    let self = this;
    let ctxBar3 = document.getElementById('bar-chart-3');
    ctxBar3.height = 120;
    BarChart3 = new Chart(ctxBar3, {
      type: 'horizontalBar',
      data: {
        labels: self.optionDataChart.labels,
        datasets: self.optionDataChart.datasets,
        norder: self.optionDataChart.norder,
      },
      options: {
        responsive: true,
        title: {
          display: true,
          text: 'Dự toán chi NSNN theo cơ cấu chi (tỷ đồng)',
          position: "top",
          fontSize: 16
        },
        legend: {
          display: true
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
            ctx.font = 'bold 11px "Helvetica Neue", Helvetica, Arial, sans-serif'
            this.data.datasets.forEach(function (dataset, i) {
              let meta = chartInstance.controller.getDatasetMeta(i);
              meta.data.forEach(function (bar, index) {
                let data = dataset.data[index];
                ctx.fillText(data, bar._model.x + 17, bar._model.y + 7);
              });
            });
          }
        }
      }
    });

    this.changeChart();
  },
  methods: {
    changeChart(){
      let self =this;
      this.$store.commit('isLoading', true);
      self.optionDataChart.labels = [];
      self.optionDataChart.datasets[0].data = [];
      self.optionDataChart.datasets[1].data = [];
      if(self.value.length){
        _.forEach(self.value, function (val, key){
          self.optionDataChart.labels.push(val.CateName);
          self.optionDataChart.datasets[0].data.push(val.DT);
          self.optionDataChart.datasets[1].data.push(val.UTT);
        });
      }
      this.updateData(BarChart3, self.optionDataChart.labels, self.optionDataChart.norder, self.optionDataChart.datasets, '');
      this.$store.commit('isLoading', false);
    },
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
      let arrayLabel = self.optionDataChart.labels;
      let arrayNorder = self.optionDataChart.norder;
      let arrayThucHien = self.optionDataChart.datasets[0].data;
      let arrayDuToan = self.optionDataChart.datasets[1].data;
      let arrayOfObj = arrayLabel.map(function(d, i) {
        return {
          label: d,
          norder: arrayNorder[i],
          data: arrayThucHien[i] || 0,
          data1: arrayDuToan[i],
        };
      });
      let sortedArrayOfObj = [];
      if(this.SortValue != ''){
        if(this.SortValue == 1){
          sortedArrayOfObj = _.orderBy(arrayOfObj, ['data'], ['asc']);
        }
        else if(this.SortValue == 2){
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
        self.optionDataChart.labels = newArrayLabel;
        self.optionDataChart.norder = newArrayNorder;
        self.optionDataChart.datasets[0].data = newArrayThucHien;
        self.optionDataChart.datasets[1].data = newArrayDuToan;
        this.updateData(BarChart3, self.optionDataChart.labels, self.optionDataChart.norder, self.optionDataChart.datasets, '');
      }

    },
    chartFullWidth() {
      this.$bvModal.show('modal-bar-3-chart-fullwidth');
      let self = this;
      this.$nextTick(() => {
        let ctxChartFullwidth = document.getElementById('bar-3-chart-fullwidth');
        let ChartFullwidth = new Chart(ctxChartFullwidth, {
          type: 'horizontalBar',
          data: {
            labels: self.optionDataChart.labels,
            datasets: self.optionDataChart.datasets,
            norder: self.optionDataChart.norder,
          },
          options: {
            responsive: true,
            title: {
              display: true,
              text: 'Chi NSNN theo cơ cấu chi (tỷ đồng)',
              position: "top",
              fontSize: 16
            },
            legend: {
              display: true
            },
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
                ctx.font = 'bold 13px "Helvetica Neue", Helvetica, Arial, sans-serif'
                this.data.datasets.forEach(function (dataset, i) {
                  let meta = chartInstance.controller.getDatasetMeta(i);
                  meta.data.forEach(function (bar, index) {
                    let data = dataset.data[index];
                    ctx.fillText(data, bar._model.x + 20, bar._model.y + 7);
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
      if(self.optionDataChart.labels.length){
        let total = 0;
        for (let i = self.optionDataChart.labels.length -1; i >= 0; i--) {
          let randomNumber = 0;
          if(keyDataset == 1){
            if(i != 0){
              randomNumber = Math.round(_.random(Number(totalEstimate)/Number(self.optionDataChart.labels.length * 2), Number(totalEstimate)/Number(self.optionDataChart.labels.length)));
              total += randomNumber;
            }
            else{
              randomNumber = Number(totalEstimate) - total;
            }
          }
          else{
            if(i != 0){
              randomNumber = Math.round(_.random(Number(totalExpense)/Number(self.optionDataChart.labels.length * 2), Number(totalExpense)/Number(self.optionDataChart.labels.length)));
              total += randomNumber;
            }
            else{
              randomNumber = Number(totalExpense) - total;
            }
          }
          randomNumber = Math.round(randomNumber);
          self.optionDataChart.datasets[keyDataset].data.push(randomNumber);
          self.optionDataChart.datasets[keyDataset].data = self.optionDataChart.datasets[keyDataset].data.reverse();
        }
      }
    },
  },
  watch: {
    'value'(){
      this.changeChart();
    }
  }
}
</script>
<style lang="css">
.chart-icons-action {
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
.chart-icons-action .icon-item:first-child {
  border-right: 1px solid #ebebeb;
}
.chart-icons-action .icon-item {
  padding: 0 10px;
}
</style>

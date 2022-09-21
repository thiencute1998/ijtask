<template>
  <div>
    <div class="sort-area" id="select-revenue-list" style="position: absolute;">
      <select class="dropdown" style="background-color: rgb(255 255 255)" id="sort-cocau-2" v-model="SortValue">
        <option value="" selected="true" disabled="disabled">Sắp xếp</option>
        <option value="1">Tăng dần</option>
        <option value="2">Giảm dần</option>
        <option value="3">Cơ cấu thu</option>
      </select>
    </div>
    <canvas id="bar-chart-3" height="180px"></canvas>
    <div class="chart-icons-action">
      <span class="icon-item" @click="openTextToVoice"><i class="fa fa-headphones" style="cursor: pointer"></i></span>
      <span class="icon-item"><i class="fa fa-expand" @click="chartFullWidth"></i></span>
    </div>
    <b-modal id="modal-bar-3-chart-fullwidth" size="xl" ref="modal-bar-3-chart-fullwidth" title="Tổng thu" ok-only ok-title="Đóng">
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
          <b-button @click="openTextToVoice" variant="primary" size="md" class="float-left mr-2">
            <i class="fa fa-headphones"></i>
          </b-button>
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
            <b-button id="tooltip-view-prev" variant="primary" class="main-header-view"><i class="fa fa-angle-left"></i></b-button>
            <b-button id="tooltip-view-play" variant="primary" class="main-header-view"><i class="fa fa-play-circle"></i></b-button>
<!--            <b-button id="tooltip-view-pause" variant="primary" class="main-header-view"><i class="fa fa-angle-left"></i></b-button>-->
            <b-button id="tooltip-view-next" variant="primary" class="main-header-view"><i class="fa fa-angle-right"></i></b-button>
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
      <div id="close-audio" @click="closeTextToVoice" style="position: absolute;top: 50%;right: -15px;transform: translateY(-50%);cursor: pointer;">
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
  name: 'dashboard-module-state-budget-revenues-bar-chart-3',
  props: {
    filter: [Array, Object],
    overviewNumber: [Array, Object],
    model: [Array, Object],
  },
  data: function () {
    return {
      optionDataBar3: {
        labels: [
          'Thu nội địa',
          'Hoạt động XNK',
          'Thu viện trợ, vay nợ',
          'Thu khác',
        ],
        norder: [
          100,200,300,400
        ],
        datasets: [
          {
            label: "Đã thu ",
            backgroundColor: "#ed7d31",
            fill: true,
            data: [13336, 2237, 1021,28]
          },
          {
            label: "Dự toán",
            backgroundColor: "#2eadd3",
            fill: true,
            data: [18495, 3365,521, 28],
          },

        ]
      },
      SortValue: "",
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
        labels: self.optionDataBar3.labels,
        datasets: self.optionDataBar3.datasets,
        norder: self.optionDataBar3.norder,
      },
      options: {
        responsive: true,
        title: {
          display: true,
          text: 'Thu NSNN theo cơ cấu thu (tỷ đồng)',
          position: "top",
          fontSize: 16
        },
        legend: {
          display: true
        },
        'onClick' : function (point, event) {
          self.$router.push({
            name: 'report_BCDH_B02-DH-CH'
          })
        },
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
            ctx.font = 'bold 10px "Helvetica Neue", Helvetica, Arial, sans-serif'
            this.data.datasets.forEach(function (dataset, i) {
              let meta = chartInstance.controller.getDatasetMeta(i);
              meta.data.forEach(function (bar, index) {
                let data = dataset.data[index];
                ctx.fillText(data, bar._model.x + 17, bar._model.y + 6);
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
      let arrayLabel = self.optionDataBar3.labels;
      let arrayNorder = self.optionDataBar3.norder;
      let arrayThucHien = self.optionDataBar3.datasets[0].data;
      let arrayDuToan = self.optionDataBar3.datasets[1].data;
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
        self.optionDataBar3.labels = newArrayLabel;
        self.optionDataBar3.norder = newArrayNorder;
        self.optionDataBar3.datasets[0].data = newArrayThucHien;
        self.optionDataBar3.datasets[1].data = newArrayDuToan;
        this.updateData(BarChart3, self.optionDataBar3.labels, self.optionDataBar3.norder, self.optionDataBar3.datasets, '');
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
            labels: self.optionDataBar3.labels,
            datasets: self.optionDataBar3.datasets,
            norder: self.optionDataBar3.norder,
          },
          options: {
            responsive: true,
            title: {
              display: true,
              text: 'Thu NSNN theo cơ cấu thu (tỷ đồng)',
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
    getRandomRevenue(totalEstimate, totalRevenue, keyDataset){
      let self = this;
      if(self.optionDataBar3.labels.length){
        let total = 0;
        for (let i = self.optionDataBar3.labels.length -1; i >= 0; i--) {
          let randomNumber = 0;
          if(keyDataset == 1){
            if(i != 0){
              randomNumber = Math.round(_.random(Number(totalEstimate)/Number(self.optionDataBar3.labels.length * 2), Number(totalEstimate)/Number(self.optionDataBar3.labels.length)));
              total += randomNumber;
            }
            else{
              randomNumber = Number(totalEstimate) - total;
            }
          }
          else{
            if(i != 0){
              randomNumber = Math.round(_.random(Number(totalRevenue)/Number(self.optionDataBar3.labels.length * 2), Number(totalRevenue)/Number(self.optionDataBar3.labels.length)));
              total += randomNumber;
            }
            else{
              randomNumber = Number(totalRevenue) - total;
            }
          }
          randomNumber = Math.round(randomNumber);
          self.optionDataBar3.datasets[keyDataset].data.push(randomNumber);
          self.optionDataBar3.datasets[keyDataset].data = self.optionDataBar3.datasets[keyDataset].data.reverse();
        }
      }
    },
    changeByRevenue(){
      let arrayLabels = [];
      let arrayNorder = [];
      let arrayDuToan = [];
      let arrayThucHien = [];
      if(this.filter.RevenueCateID == null){
        arrayLabels = [
          'Thu nội địa',
          'Hoạt động XNK',
          'Thu viện trợ, vay nợ',
          'Thu khác',
        ];
        arrayNorder = [100,200,300,400];
        this.optionDataBar3.labels = arrayLabels;
        this.optionDataBar3.norder = arrayNorder;
        if(this.filter.ProvinceID == 36 && this.filter.DistrictID == null && this.filter.CommuneID == null && ((this.filter.PeriodID == 1 && this.filter.PeriodValue == 2020) || this.filter.PeriodID == null)){
          arrayDuToan = [18495, 3365,521, 28];
          arrayThucHien = [13336, 2237, 1021,28];
          this.optionDataBar3.datasets[0].data = arrayThucHien;
          this.optionDataBar3.datasets[1].data = arrayDuToan;
        }
        else{
          let self = this;
          _.forEach(self.optionDataBar3.datasets, function (dataset, keyDataset) {
            self.optionDataBar3.datasets[keyDataset].data = [];
            self.getRandomRevenue(self.overviewNumber.totalEstimate, self.overviewNumber.totalRevenue,keyDataset)
          });
        }
      }
      else{
         if(this.filter.RevenueCateID == 22){
            arrayLabels = ['Thu thuế','Thu phí, lệ phí','Khác'];
            arrayNorder = [100,200,300,400];
          }

         else if(this.filter.RevenueCateID == 33){
           arrayLabels = ['Thuế giá trị gia tăng','Thuế tiêu thụ đặc biệt','Thuế thu nhập DN',
             'Thuế tài nguyên','Thuế BVMT','Thuế thu nhập CN','Thuế SD đất',
             'Các thuế khác'];
           arrayNorder = [100,200,300,400,500,600,700,800];
         }
         else if(this.filter.RevenueCateID == 27){
           arrayLabels = ['Thuế xuất khẩu','Thuế nhập khẩu','Thuế tiêu thụ đặc biệt',
             'Thuế BVMT','Thuế giá trị gia tăng','Thuế BS đối với HHNK vào VN',
             'Thu khác từ HĐXNK'];
           arrayNorder = [100,200,300,400,500,600,700];
         }
        this.optionDataBar3.labels = arrayLabels;
        this.optionDataBar3.norder = arrayNorder;

        let self = this;
        _.forEach(self.optionDataBar3.datasets, function (dataset, keyDataset) {
          self.optionDataBar3.datasets[keyDataset].data = [];
          self.getRandomRevenue(self.overviewNumber.totalEstimate, self.overviewNumber.totalRevenue,keyDataset)
        });
      }

      this.updateData(BarChart3, this.optionDataBar3.labels, this.optionDataBar3.norder, this.optionDataBar3.datasets, '');
      this.sortChart();
    },
    openTextToVoice() {
      $('.text-to-voice-1 audio')[0].pause();
      $('.text-to-voice-1').hide();
      $('.text-to-voice').show();
      $('.text-to-voice audio')[0].play();
    },
    closeTextToVoice() {
      $('.text-to-voice-1 audio')[0].pause();
      $('.text-to-voice audio')[0].pause();
      $('.text-to-voice').hide();
      // $('.text-to-voice audio')[0].pause();
    }
  },
  watch: {
    'overviewNumber.totalRevenue'(){
      this.changeByRevenue();
    },
    SortValue(){
      this.sortChart();
    },
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

  .text-to-voice {
    position: fixed;
    z-index: 9999 !important;
    right: 40px;
    bottom: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    border-radius: 20px;
    display: none;
  }
  .text-to-voice audio{
    height: 48px;
  }
</style>

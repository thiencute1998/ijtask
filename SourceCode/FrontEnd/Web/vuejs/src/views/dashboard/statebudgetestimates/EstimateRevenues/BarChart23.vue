<template>
  <div>
    <div class="sort-area" style="position: absolute;">
      <select class="dropdown" style="background-color: rgb(255 255 255)" id="sort-cocau-2" v-model="model1.SortValue">
        <option value="" selected="true" disabled="disabled">Sắp xếp</option>
        <option value="1">Tăng dần</option>
        <option value="2">Giảm dần</option>
        <option value="3">Đơn vị thu</option>
      </select>
    </div>
    <canvas id="bar-chart-23" height="180px"></canvas>
    <span class="icon-bar-chart-23-full-width"><i class="fa fa-expand" @click="chartFullWidth"></i></span>
    <b-modal id="modal-bar-chart-23-fullwidth" size="xl" ref="modal-bar-chart-23-fullwidth" title="Tổng thu" ok-only ok-title="Đóng">
      <canvas id="bar-chart-23-fullwidth" height="150px"></canvas>
    </b-modal>
  </div>
</template>
<script>
import ApiService from '@/services/api.service';
import Chart from 'chart.js';
import 'chartjs-plugin-labels';
import 'chartjs-plugin-zoom';
import moment from "moment";

let BarChart23 = null;
let ChartFullwidth = null;
export default {
  name: 'dashboard-module-state-budget-revenues-bar-chart-23',
  props: {
    filter: [Array, Object],
    overviewNumber: [Array, Object],
    model: [Object],
  },
  data: function () {
    return {
      optionDataBar23: {
        labels: [
          'Cục thuế tỉnh',
          'Cục hải quan tỉnh',
          'Kho bạc nhà nước',
          'Các đơn vị khác'
        ],
        norder: [1,2,3,4,5,6,7,8,9,10],
        datasets: [
          {
            label: "Dự toán thu",
            backgroundColor: "#2eadd3",
            fill: false,
            data: [1739,1255,3365,895],
          },
          {
            label: "Ước thực hiện thu",
            backgroundColor: "#92D050",
            fill: false,
            data: [2196,1962,3237,1417],
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
    let ctxBar23 = document.getElementById('bar-chart-23');
    ctxBar23.height = 80;
    BarChart23 = new Chart(ctxBar23, {
      type: 'bar',
      data: {
        labels: self.optionDataBar23.labels,
        datasets: self.optionDataBar23.datasets,
        norder: self.optionDataBar23.norder,
      },
      options: {
        responsive: true,
        title: {
          display: true,
          text: 'Dự toán thu NSNN theo đơn vị (tỷ đồng)',
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
      let arrayLabel = self.optionDataBar23.labels;
      let arrayNorder = self.optionDataBar23.norder;
      let arrayThucHien = self.optionDataBar23.datasets[0].data;
      let arrayDuToan = self.optionDataBar23.datasets[1].data;
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
        self.optionDataBar23.labels = newArrayLabel;
        self.optionDataBar23.norder = newArrayNorder;
        self.optionDataBar23.datasets[0].data = newArrayThucHien;
        self.optionDataBar23.datasets[1].data = newArrayDuToan;
        this.updateData(BarChart23, self.optionDataBar23.labels, self.optionDataBar23.norder, self.optionDataBar23.datasets, '');
      }

    },
    chartFullWidth() {
      this.$bvModal.show('modal-bar-chart-23-fullwidth');
      let self = this;
      this.$nextTick(() => {
        let ctxChartFullwidth = document.getElementById('bar-chart-23-fullwidth');
        ChartFullwidth = new Chart(ctxChartFullwidth, {
          type: 'bar',
          data: {
            labels: self.optionDataBar23.labels,
            datasets: self.optionDataBar23.datasets,
            norder: self.optionDataBar23.norder
          },
          options: {
            responsive: true,
            title: {
              display: true,
              text: 'Dự toán thu NSNN theo lĩnh vực (tỷ đồng)',
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
            }
          }
        });
      });
    },
    getRandomRevenue(totalEstimate, totalRevenue, keyDataset){
      let self = this;
      if(self.optionDataBar23.labels.length){
        let total = 0;
        for (let i = self.optionDataBar23.labels.length - 1; i >= 0; i--) {
          let randomNumber = 0;
          if(keyDataset == 1){
            if(i != 0){
              randomNumber = _.random(Number(totalEstimate)/Number(self.optionDataBar23.labels.length * 2), Number(totalEstimate)/Number(self.optionDataBar23.labels.length));
              total += randomNumber;
            }
            else{
              randomNumber = Number(totalEstimate) - total;
            }
          }
          else{
            if(i != 0){
              randomNumber = _.random(Number(totalRevenue)/Number(self.optionDataBar23.labels.length * 2), Number(totalRevenue)/Number(self.optionDataBar23.labels.length));
              total += randomNumber;
            }
            else{
              randomNumber = Number(totalRevenue) - total;
            }
          }
          randomNumber = Math.round(randomNumber);
          self.optionDataBar23.datasets[keyDataset].data.push(randomNumber);
          self.optionDataBar23.datasets[keyDataset].data = self.optionDataBar23.datasets[keyDataset].data.reverse();
        }
      }
    },
    changeChartRevenue(){
      let self = this;
      let labels = [];
      let norder = [];
      if(this.filter.RevenueCateID == 23){
        labels = ['Khu vực DNNN do TWQL', 'Khu vực DNNN do ĐPQL',
          'Khu vực DN có vốn ĐT nước ngoài ', 'Khu vực kinh tế ngoài quốc doanh',
          'Thuế TNCN, BVMT','Hoạt động xổ số kiến thiết, điện toán',
          'Các khoản thu từ nhà và đất', 'Các khoản phí, lệ phí',
          'Các khoản thu khác'];
        norder = [1,2,3,4,5,6,7,8,9];
      }
      else if(this.filter.RevenueCateID == 33){
        labels = ['Khu vực DNNN do TWQL', 'Khu vực DNNN do ĐPQL',
          'Khu vực DN có vốn ĐT nước ngoài ', 'Khu vực kinh tế ngoài quốc doanh',
          'Thuế TNCN, BVMT','Hoạt động xổ số kiến thiết, điện toán',
          'Các khoản thu từ nhà và đất'];
        norder = [1,2,3,4,5,6,7];
      }
      else{
        labels = ['Khu vực DNNN do TWQL', 'Khu vực DNNN do ĐPQL',
          'Khu vực DN có vốn ĐT nước ngoài ', 'Khu vực kinh tế ngoài quốc doanh',
          'Thuế TNCN, BVMT','Hoạt động xổ số kiến thiết, điện toán',
          'Các khoản thu từ nhà và đất', 'Các khoản phí, lệ phí',
          'Hoạt động xuất, nhập khẩu', 'Các khoản thu khác'
        ]
        norder = [1,2,3,4,5,6,7,8,9,10];
      }
      this.optionDataBar23.labels = labels;
      this.optionDataBar23.norder = norder;
      if(self.filter.ProvinceID == 36 && self.filter.RevenueCateID == null && this.filter.DistrictID == null && this.filter.CommuneID == null && ((this.filter.PeriodID == 1 && this.filter.PeriodValue == 2020) || this.filter.PeriodID == null)){
        let arrayDuToan = [1639,1730,2249,5230,2530,220,2196,1962,3237,1417];
        let arrayThucHien = [1093,1153,1500,4375,1094,184,1739,1255,3365,895];
        self.optionDataBar23.datasets[0].data = arrayThucHien;
        self.optionDataBar23.datasets[1].data = arrayDuToan;
      }
      else{
        _.forEach(self.optionDataBar23.datasets, function (dataset, keyDataset) {
          self.optionDataBar23.datasets[keyDataset].data = [];
          if(self.optionDataBar23.labels.length){
            let total = 0;
            for (let i = self.optionDataBar23.labels.length - 1; i >= 0; i--) {
              let randomNumber = 0;
              if(keyDataset == 1){
                if(i != 0){
                  randomNumber = Math.round(_.random(Number(self.overviewNumber.totalEstimate)/Number(self.optionDataBar23.labels.length * 2), Number(self.overviewNumber.totalEstimate)/Number(self.optionDataBar23.labels.length)));
                  total += randomNumber;
                }
                else{
                  randomNumber = Number(self.overviewNumber.totalEstimate) - total;
                }
              }
              else{
                if(i != 0){
                  randomNumber = Math.round(_.random(Number(self.overviewNumber.totalRevenue)/Number(self.optionDataBar23.labels.length * 2), Number(self.overviewNumber.totalRevenue)/Number(self.optionDataBar23.labels.length)));
                  total += randomNumber;
                }
                else{
                  randomNumber = Number(self.overviewNumber.totalRevenue) - total;
                }
              }
              randomNumber = Math.round(randomNumber);
              self.optionDataBar23.datasets[keyDataset].data.push(randomNumber);
              self.optionDataBar23.datasets[keyDataset].data = self.optionDataBar23.datasets[keyDataset].data.reverse();
            }
          }
        });
      }
      this.updateData(BarChart23, self.optionDataBar23.labels, self.optionDataBar23.norder, self.optionDataBar23.datasets, '');
      this.sortChart();
    },
  },
  watch: {
    'model1.SortValue'(){
      this.sortChart();
    },
    'overviewNumber.totalRevenue'(){
      this.changeChartRevenue();
    },
  }
}
</script>
<style lang="css">
.icon-bar-chart-23-full-width{
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

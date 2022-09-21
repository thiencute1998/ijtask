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
    <canvas id="bar-chart" height="180px"></canvas>
    <div class="chart-icons-action-1">
<!--      <span class="icon-item"><i @click="openTextToVoice" class="fa fa-headphones" style="cursor: pointer"></i></span>-->
      <span class="icon-item"><i class="fa fa-expand" @click="chartFullWidth"></i></span>
    </div>
    <!--    <span class="icon-bar-chart-full-width"><i class="fa fa-expand" @click="chartFullWidth"></i></span>-->
    <b-modal id="modal-bar-chart-fullwidth" size="xl" ref="modal-bar-chart-fullwidth" title="Tổng chi" ok-only ok-title="Đóng">
      <canvas id="bar-chart-fullwidth" height="150px"></canvas>
    </b-modal>

    <div class="text-to-voice-1">
      <audio controls>
        <!--        <source src="horse.ogg" type="audio/ogg">-->
        <source :src="$store.state.appRootApi + '/audio/dashboard-1.mp3'" type="audio/mpeg">
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

let BarChart = null;
let ChartFullwidth = null;
export default {
  name: 'dashboard-module-state-budget-expenses-bar-chart',
  props: {
    filter: [Array, Object],
    overviewNumber: [Array, Object],
    model: [Object],
    value: [Array, Object],
  },
  data: function () {
    return {
      optionDataBar: {
        labels: ['Thành phố Nam Định', 'Huyện Mỹ Lộc', 'Huyện Vụ Bản',
          'Huyện ý Yên', 'Huyện Nghĩa Hưng', 'Huyện Nam Trực', 'Huyện Trực Ninh',
          'Huyện Xuân Trường', 'Huyện Giao Thủy', 'Huyện Hải Hậu'
        ],
        districtID: [356,358,359,360,361,362,363,364,365,366],
        districtNo: ['07100','07200','07250','07300','07400','07500','07600','07700','07800','07900'],
        norder: [1,2,3,4,5,6,7,8,9,10],
        datasets: [
          {
            label: "Dự toán thu",
            backgroundColor: "green",
            fill: false,
            data: [6305,2493,441,1496,631,575,1219,388,251,1470]
          },
          {
            label: "Dự toán chi ",
            backgroundColor: "#2eadd3",
            fill: false,
            data: [6865,2714,480,1220,687,626,1327,422,273,2008]
          },
          {
            label: "Ước thực hiện chi",
            backgroundColor: "#92D050",
            fill: false,
            data: [7635,3019,534,1491,764,696,1476,470,2100],
          },
        ]
      },
      model1: {
        SortValue: "",
        showFullWidth: false,
        ProvinceID: this.filter.ProvinceID,
        DistrictID: this.filter.DistrictID,
        CommuneID: this.filter.CommuneID,
      }
    }
  },
  components: {},
  mounted () {
    let self = this;
    let ctxBar = document.getElementById('bar-chart');
    // ctxBar.height = 80;
    BarChart = new Chart(ctxBar, {
      type: 'bar',
      data: {
        labels: self.optionDataBar.labels,
        datasets: self.optionDataBar.datasets,
        districtID: self.optionDataBar.districtID,
        districtNo: self.optionDataBar.districtNo,
        norder: self.optionDataBar.norder,
      },
      options: {
        elements: {
          rectangle: {

          }
        },
        responsive: true,
        title: {
          display: true,
          text: 'Dự toán chi NSNN theo địa bàn (tỷ đồng)',
          position: "top",
          fontSize: 16
        },
        legend: {
          display: true,
          position: 'top'
        },
        'onClick' : function (evt) {
          // self.$router.push({
          //   name: 'report_BCDH_B02-DH-CH'
          // })
          let activePoints = BarChart.getElementsAtEventForMode(evt, 'point', BarChart.options);
          let firstPoint = activePoints[0];
          if(firstPoint && self.filter.DistrictID === null){
            let districtID = BarChart.data.districtID[firstPoint._index];
            let districtNo = BarChart.data.districtNo[firstPoint._index];
            let districtName = BarChart.data.labels[firstPoint._index];
            self.filter.DistrictID = districtID;
            self.filter.DistrictNo = districtNo;
            self.filter.DistrictName = districtName;
          }
        },
        barValueSpacing: 10,
        scales: {
          xAxes: [{
            stacked: false,
            barPercentage: 0.8,
            // categoryPercentage: 1.0,
            gridLines: {
              offsetGridLines: true
            }
          }],
          yAxes: [{
            stacked: false,
            // ticks:{
            //   min: 0,
            // },
            ticks: {
              beginAtZero: true
            },
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
    updateData(chart, labels = [], districtID = [], districtNo = [], norder = [], datasets, title = '') {
      if (labels.length) {
        chart.data.labels = labels;
      }
      if (districtID.length) {
        chart.data.districtID = districtID;
      }
      if (districtNo.length) {
        chart.data.districtNo = districtNo;
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
    changeChart(){
      let self = this;
      self.optionDataBar.labels = [];
      self.optionDataBar.datasets[0].data = [];
      self.optionDataBar.datasets[1].data = [];
      self.optionDataBar.datasets[2].data = [];
      self.optionDataBar.AdressID =[];
      self.optionDataBar.AdressNo = [];
      _.forEach(self.value.Adress , function (val, key){
        self.optionDataBar.labels.push(val.Name);
        self.optionDataBar.AdressID.push(val.ID);
        self.optionDataBar.AdressNo.push(val.No);
        self.optionDataBar.datasets[0].data.push(0);
        self.optionDataBar.datasets[1].data.push(0);
        self.optionDataBar.datasets[2].data.push(0);
        _.forEach(self.value.data, function (v, k){
          if(v.ID === val.ID){
            if(v.DT){
              self.optionDataBar.datasets[1].data[key] = v.DT;
            }
            if(v.UTT){
              self.optionDataBar.datasets[2].data[key] = v.UTT;
            }
          }
        });
        _.forEach(self.value.Dbt, function (v, k){
          if(v.ID === val.ID){
            self.optionDataBar.datasets[0].data[key] = v.DTT;
          }
        })
      });
      this.updateData(BarChart, self.optionDataBar.labels, self.optionDataBar.AdressID , self.optionDataBar.AdressNo, self.optionDataBar.norder, self.optionDataBar.datasets, '');
      this.sortChart();
    },

    sortChart(){
      let self = this;
      let arrayLabel = self.optionDataBar.labels;
      let arrayDistrictID = self.optionDataBar.districtID;
      let arrayDistrictNo = self.optionDataBar.districtNo;
      let arrayNorder = self.optionDataBar.norder;
      let arrayThucHien = self.optionDataBar.datasets[0].data;
      let arrayDaThu = self.optionDataBar.datasets[1].data;
      let arrayDuToan = self.optionDataBar.datasets[2].data;
      let arrayOfObj = arrayLabel.map(function(d, i) {
        return {
          label: d,
          districtID: arrayDistrictID[i],
          districtNo: arrayDistrictNo[i],
          norder: arrayNorder[i],
          data: arrayThucHien[i] || 0,
          data1: arrayDaThu[i],
          data2: arrayDuToan[i],
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
        let newArrayDistrictID = [];
        let newArrayDistrictNo = [];
        let newArrayNorder = [];
        let newArrayThucHien = [];
        let newArrayDaThu = [];
        let newArrayDuToan = [];
        sortedArrayOfObj.forEach(function(d){
          newArrayLabel.push(d.label);
          newArrayDistrictID.push(d.districtID);
          newArrayDistrictNo.push(d.districtNo);
          newArrayNorder.push(d.norder);
          newArrayThucHien.push(d.data);
          newArrayDaThu.push(d.data1);
          newArrayDuToan.push(d.data2);
        });
        self.optionDataBar.labels = newArrayLabel;
        self.optionDataBar.districtID = newArrayDistrictID;
        self.optionDataBar.districtNo = newArrayDistrictNo;
        self.optionDataBar.norder = newArrayNorder;
        self.optionDataBar.datasets[0].data = newArrayThucHien;
        self.optionDataBar.datasets[1].data = newArrayDaThu;
        self.optionDataBar.datasets[2].data = newArrayDuToan;
        this.updateData(BarChart, self.optionDataBar.labels, self.optionDataBar.districtID, self.optionDataBar.districtNo, self.optionDataBar.norder, self.optionDataBar.datasets, '');
        if(this.model1.showFullWidth){
          this.updateData(ChartFullwidth, self.optionDataBar.labels, self.optionDataBar.districtID, self.optionDataBar.districtNo, self.optionDataBar.norder, self.optionDataBar.datasets, '');
        }
      }

    },
    getDistrict(){
      let self = this;
      let requestData = {
        url: 'dashboard/api/dashboard/get-district',
        method: 'post',
        data: {
          ProvinceID: this.filter.ProvinceID
        }
      };
      this.$store.commit('isLoading', true);
      ApiService.setHeader();
      ApiService.customRequest(requestData).then((responses) => {

        let responsesData = responses.data;
        if (responsesData.status === 1) {
          self.model1.ProvinceID = self.filter.ProvinceID;
          self.model1.DistrictID = self.filter.DistrictID;
          self.optionDataBar.labels = [];
          self.optionDataBar.norder = [];
          self.optionDataBar.districtID = [];
          self.optionDataBar.districtNo = [];
          _.forEach(responsesData.data, function (district, key) {
            self.optionDataBar.labels.push(district.DistrictName);
            self.optionDataBar.norder.push(Number(key)+1);
            self.optionDataBar.districtID.push(district.DistrictID);
            self.optionDataBar.districtNo.push(district.DistrictNo);
          });
          _.forEach(self.optionDataBar.datasets, function (dataset, keyDataset) {
            self.optionDataBar.datasets[keyDataset].data = [];
            if(self.filter.ProvinceID == 36 && self.filter.ExpenseCateID == null && self.filter.DistrictID == null && self.filter.CommuneID == null && ((self.filter.PeriodID == 1 && self.filter.PeriodValue == 2020) || self.filter.PeriodID == null)){
              let valueTotal = [];
              if(keyDataset == 0){
                valueTotal = [6305,2493,441,1496,631,575,1219,388,251,1470];
              }
              else if(keyDataset == 1){
                valueTotal = [6865,2714,480,1220,687,626,1327,422,273,2008];
              }
              else{
                valueTotal = [7635,3019,534,1491,764,696,1476,470,2100];
              }
              self.optionDataBar.datasets[keyDataset].data = valueTotal;
            }
            else{
              self.getRandomExpense(self.overviewNumber.totalEstimate, self.overviewNumber.totalRevenue, self.overviewNumber.totalExpense, keyDataset)
            }
          });
          this.updateData(BarChart, self.optionDataBar.labels, self.optionDataBar.districtID, self.optionDataBar.districtNo, self.optionDataBar.norder, self.optionDataBar.datasets, '');
          this.sortChart();
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });
    },
    getCommune(){
      let self = this;
      let requestData = {
        url: 'dashboard/api/dashboard/get-commune',
        method: 'post',
        data: {
          DistrictID: this.filter.DistrictID
        }
      };
      this.$store.commit('isLoading', true);
      ApiService.setHeader();
      ApiService.customRequest(requestData).then((responses) => {

        let responsesData = responses.data;
        if (responsesData.status === 1) {
          self.model1.ProvinceID = self.filter.ProvinceID;
          self.model1.DistrictID = self.filter.DistrictID;
          self.optionDataBar.labels = [];
          self.optionDataBar.norder = [];
          _.forEach(responsesData.data, function (commune, key) {
            self.optionDataBar.labels.push(commune.CommuneName);
            self.optionDataBar.norder.push(Number(key)+1);
          });
          _.forEach(self.optionDataBar.datasets, function (dataset, keyDataset) {
            self.optionDataBar.datasets[keyDataset].data = [];
            for (let i = 0; i < responsesData.data.length; i++) {
              self.getRandomExpense(self.overviewNumber.totalEstimate, self.overviewNumber.totalRevenue, self.overviewNumber.totalExpense, keyDataset)
            }
          });
          this.updateData(BarChart, self.optionDataBar.labels, self.optionDataBar.districtID, self.optionDataBar.districtNo, self.optionDataBar.norder, self.optionDataBar.datasets, '');
          if(self.model1.showFullWidth){
            this.updateData(ChartFullwidth, self.optionDataBar.labels, self.optionDataBar.districtID, self.optionDataBar.districtNo, self.optionDataBar.norder, self.optionDataBar.datasets, '');
          }
          this.sortChart();
        }
        self.$store.commit('isLoading', false);
      }, (error) => {
        console.log(error);
        self.$store.commit('isLoading', false);
      });
    },
    getCommuneSelected(){
      let label = [this.filter.CommuneName];
      let norder = [1];
      let dataDuToan = [];
      let dataThucHien = [];
      switch (this.filter.CommuneID){
        case 8722:
          dataDuToan = [1394];
          dataThucHien = [1339];
          break;
        case 8716:
          dataDuToan = [738];
          dataThucHien = [709];
          break;
        case 8713:
          dataDuToan = [844];
          dataThucHien = [811];
          break;
        case 8710:
          dataDuToan = [434];
          dataThucHien = [417];
          break;
        case 8719:
          dataDuToan = [852];
          dataThucHien = [819];
          break;
        case 8707:
          dataDuToan = [754];
          dataThucHien = [725];
          break;
        case 8725:
          dataDuToan = [1016];
          dataThucHien = [977];
          break;
        case 8728:
          dataDuToan = [410];
          dataThucHien = [394];
          break;
        case 8731:
          dataDuToan = [1755];
          dataThucHien = [1686];
          break;

        case 8935:
          dataDuToan = [389];
          dataThucHien = [373];
          break;
        case 8962:
          dataDuToan = [291];
          dataThucHien = [280];
          break;
        case 8944:
          dataDuToan = [334];
          dataThucHien = [320];
          break;
        case 8936:
          dataDuToan = [171];
          dataThucHien = [165];
          break;
        case 8950:
          dataDuToan = [337];
          dataThucHien = [324];
          break;
        case 8971:
          dataDuToan = [298];
          dataThucHien = [286];
          break;
        case 8956:
          dataDuToan = [175];
          dataThucHien = [168];
          break;
        case 8959:
          dataDuToan = [162];
          dataThucHien = [155];
          break;
        case 8965:
          dataDuToan = [194];
          dataThucHien = [186];
          break;
        case 8953:
          dataDuToan = [291];
          dataThucHien = [280];
          break;
        case 8968:
          dataDuToan = [227];
          dataThucHien = [218];
          break;
        case 8947:
          dataDuToan = [171];
          dataThucHien = [165];
          break;
        case 8938:
          dataDuToan = [197];
          dataThucHien = [190];
          break;
        default :
          dataDuToan = [_.random(500,1000)];
          dataThucHien = [_.random(500,1000)];
          break;
      }
      this.optionDataBar.labels = label;
      this.optionDataBar.norder = norder;
      this.optionDataBar.datasets[0].data = dataDuToan;
      this.optionDataBar.datasets[1].data = dataThucHien;
      this.updateData(BarChart, this.optionDataBar.labels, this.optionDataBar.districtID, this.optionDataBar.districtNo, this.optionDataBar.norder, this.optionDataBar.datasets, '');

    },
    chartFullWidth() {
      this.model1.showFullWidth = true;
      this.$bvModal.show('modal-bar-chart-fullwidth');
      let self = this;
      this.$nextTick(() => {
        let ctxChartFullwidth = document.getElementById('bar-chart-fullwidth');
        ChartFullwidth = new Chart(ctxChartFullwidth, {
          type: 'bar',
          data: {
            labels: self.optionDataBar.labels,
            datasets: self.optionDataBar.datasets,
            districtID: self.optionDataBar.districtID,
            districtNo: self.optionDataBar.districtNo,
            norder: self.optionDataBar.norder,
          },
          options: {
            elements: {
              rectangle: {

              }
            },
            responsive: true,
            title: {
              display: true,
              text: 'Chi NSNN theo địa bàn (tỷ đồng)',
              position: "top",
              fontSize: 16
            },
            legend: {
              display: true,
              position: 'bottom'
            },
            'onClick' : function (evt) {
              // self.$router.push({
              //   name: 'report_BCDH_B02-DH-CH'
              // })
              let activePoints = ChartFullwidth.getElementsAtEventForMode(evt, 'point', ChartFullwidth.options);
              let firstPoint = activePoints[0];
              if(firstPoint && self.filter.DistrictID === null){
                let districtID = ChartFullwidth.data.districtID[firstPoint._index];
                let districtNo = ChartFullwidth.data.districtNo[firstPoint._index];
                let districtName = ChartFullwidth.data.labels[firstPoint._index];
                self.filter.DistrictID = districtID;
                self.filter.DistrictNo = districtNo;
                self.filter.DistrictName = districtName;
              }
            },
            scales: {
              xAxes: [{
                stacked: false,
                barPercentage: 0.8,
                // categoryPercentage: 1.0,
                gridLines: {
                  offsetGridLines: true
                }
              }],
              yAxes: [{
                stacked: false,
                // ticks:{
                //   min: 0,
                // },
                ticks: {
                  beginAtZero: true
                },
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
              }
            }
          }
        });
      });
    },
    getRandomExpense(totalEstimate, totalRevenue, totalExpense, keyDataset){
      let self = this;
      if(self.optionDataBar.labels.length){
        let total = 0;
        for (let i = self.optionDataBar.labels.length - 1; i >= 0; i--) {
          let randomNumber = 0;
          if(keyDataset == 2){
            if(i != 0){
              randomNumber = _.random(Number(totalEstimate)/Number(self.optionDataBar.labels.length * 2), Number(totalEstimate)/Number(self.optionDataBar.labels.length));
              total += randomNumber;
            }
            else{
              randomNumber = Number(totalEstimate) - total;
            }
          }
          else if(keyDataset == 1){
            if(i != 0){
              randomNumber = _.random(Number(totalRevenue)/Number(self.optionDataBar.labels.length * 2), Number(totalRevenue)/Number(self.optionDataBar.labels.length));
              total += randomNumber;
            }
            else{
              randomNumber = Number(totalRevenue) - total;
            }
          }
          else{
            if(i != 0){
              randomNumber = _.random(Number(totalExpense)/Number(self.optionDataBar.labels.length * 2), Number(totalExpense)/Number(self.optionDataBar.labels.length));
              total += randomNumber;
            }
            else{
              randomNumber = Number(totalExpense) - total;
            }
          }
          randomNumber = Math.round(randomNumber);
          self.optionDataBar.datasets[keyDataset].data.push(randomNumber);
          self.optionDataBar.datasets[keyDataset].data = self.optionDataBar.datasets[keyDataset].data.reverse();
        }
      }
    }
  },
  watch: {
    'value'(){
      this.changeChart();
    }

  }
}
</script>
<style lang="css">
.chart-icons-action-1 {
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
.chart-icons-action-1 .icon-item:first-child {
  border-right: 1px solid #ebebeb;
}
.chart-icons-action-1 .icon-item {
  padding: 0 10px;
}
</style>

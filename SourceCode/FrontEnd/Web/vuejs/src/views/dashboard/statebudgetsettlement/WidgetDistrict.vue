<template>
  <div>
    <div class="row" style="overflow-x:scroll">
      <div class="col-8 mb-2">
        <b-card-group deck style="min-height: 90px;">
          <b-card class="text-center small-box bg-info" >
            <b-card-text style="cursor: default !important;" >
              <h4 style="color: white">114.000 tỷ</h4>
              <div style="color: white">Tổng dự toán thu</div>
            </b-card-text>
          </b-card>
        </b-card-group>
      </div>
      <div class="col-8 mb-2">
        <b-card-group deck style="min-height: 90px;">
          <b-card class="text-center bg-secondary" style="background-color: #ed7d31 !important;border-color: #ed7d31;" >
            <b-card-text style="cursor: default !important;" >
              <h4 style="color: white">124.000 tỷ</h4>
              <div style="color: white">Tỏng thu</div>
            </b-card-text>
          </b-card>
        </b-card-group>
      </div>
      <div class="col-8 mb-2">
        <b-card-group deck style="min-height: 90px;">
          <b-card class="text-center bg-secondary" >
            <b-card-text style="cursor: default !important;" >
              <h4 style="color: black">94.000 tỷ</h4>
              <div>Tổng quyết toán thu</div>
            </b-card-text>
          </b-card>
        </b-card-group>
      </div>


    </div>
    <div class="row">
      <div class="col-23" style="padding-left: 20px">
        <div class="sort-area-5" style="float: right">
          <select class="dropdown" style="background-color:rgb(255 255 255) " id="sort-diaban-5">
            <option selected="true" disabled="disabled">Sắp xếp</option>
            <option value="1">Địa bàn thu</option>
            <option value="2">Giá trị giảm dần</option>
            <option value="3">Giá trị tăng dần</option>
          </select>
        </div>
        <canvas id="bar-chart-5" height="180px"></canvas>
      </div>
    </div>
    <div class="row">
      <div class="col-23" style="padding-left: 20px">
        <div class="sort-area-6" style="float: right">
          <select class="dropdown" style="background-color:rgb(255 255 255) " id="sort-diaban-6">
            <option selected="true" disabled="disabled">Sắp xếp</option>
            <option value="1">Địa bàn chi</option>
            <option value="2">Giá trị giảm dần</option>
            <option value="3">Giá trị tăng dần</option>
          </select>
        </div>
        <canvas id="bar-chart-6" height="180px"></canvas>
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

  let BarChart5 = null;
  let BarChart6 = null;
  export default {
    name: 'dashboard-module-state-budget-district',
    props: {
      filter: [Array, Object]
    },
    data: function () {
      return {
        chartType: 1,
        optionDataBar5: {
          labels: [
            'TP. Biên Hòa',
            'An Bình',
            'An Hòa',
            'Bình Đa',
            'Bửu Hòa',
            'Bửu Long',
            'Hiệp Hòa',
            'Hóa An',
            'Hòa Bình',
            'Hố Nai',
            'Long Bình',
            'Long Bình Tân'
          ],
          datasets: [
            {
              label: "Dự toán chi",
              backgroundColor: "#2eadd3",
              fill: true,
              data: [ 9, 8, 8, 4, 6, 7, 8, 6, 8,7, 9, 9],
            },
            {
              label: "Đã chi",
              backgroundColor: "#ed7d31",
              fill: true,
              data: [ 9, 6, 7, 8, 5, 5, 5, 5, 6, 6, 7, 8]
            },
            {
              label: "Đã thu",
              backgroundColor: "#a5aeb7",
              fill: true,
              data: [ 9, 7, 6, 5, 7, 6, 5, 7, 6, 8, 8, 9]
            },
          ]
        },
        optionDataBar6: {
          labels: [
            'TP. Biên Hòa',
            'An Bình',
            'An Hòa',
            'Bình Đa',
            'Bửu Hòa',
            'Bửu Long',
            'Hiệp Hòa',
            'Hóa An',
            'Hòa Bình',
            'Hố Nai',
            'Long Bình',
            'Long Bình Tân'
          ],
          datasets: [
            {
              label: "Dự toán chi",
              backgroundColor: "#2eadd3",
              fill: true,
              data: [9, 8, 8, 4, 6, 7, 8, 6, 8,7, 9, 9],
            },
            {
              label: "Đã chi",
              backgroundColor: "#ed7d31",
              fill: true,
              data: [9, 6, 7, 8, 5, 5, 5, 5, 6, 6, 7, 8]
            },
            {
              label: "Đã thu",
              backgroundColor: "#a5aeb7",
              fill: true,
              data: [9, 7, 6, 5, 7, 6, 5, 7, 6, 8, 8, 9]
            },
          ]
        },
      }
    },
    components: {},
    mounted () {
      let self = this;
      let ctxBar5 = document.getElementById('bar-chart-5');
      let ctxBar6 = document.getElementById('bar-chart-6');
      ctxBar5.height = 70;
      ctxBar6.height = 70;
      BarChart5 = new Chart(ctxBar5, {
        type: 'bar',
        data: {
          labels: self.optionDataBar5.labels,
          datasets: self.optionDataBar5.datasets
        },
        options: {
          elements: {
            rectangle: {

            }
          },
          responsive: true,
          title: {
            display: true,
            text: 'Thu NSNN theo địa bàn',
            position: "top",
            fontSize: 16
          },
          legend: {
            display: true,
            position: 'bottom'
          },
          'onClick' : function (point, event) {
            // self.$router.push({
            //   name: 'report_BCDH_B02-DH-CH'
            // })
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
            zoom: {
              pan: {
                enabled: true,
                mode: 'x',
                sensitivity: 0.25,
              },
              zoom: {
                enabled: true,
                mode: 'x',
                sensitivity: 0.25,
              }
            }
          }
        }
      });
      BarChart6 = new Chart(ctxBar6, {
        type: 'bar',
        data: {
          labels: self.optionDataBar6.labels,
          datasets: self.optionDataBar6.datasets
        },
        options: {
          responsive: true,
          title: {
            display: true,
            text: 'Chi NSNN theo địa bàn',
            position: "top",
            fontSize: 16
          },
          legend: {
            display: true,
            position: 'bottom'
          },
          'onClick' : function (point, event) {
            // self.$router.push({
            //   name: 'report_BCDH_B02-DH-CH'
            // })
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
            zoom: {
              pan: {
                enabled: true,
                mode: 'x',
                sensitivity: 0.25,
              },
              zoom: {
                enabled: true,
                mode: 'x',
                sensitivity: 0.25,
              }
            }
          }
        }
      });
    },
    methods: {
      changeChart(type){
        let datasetsPie = [];
        let labelsPie = [];
        let titlePie = '';
        let currentDataBar = this.optionDataBar.datasets[0].data;
        this.optionDataBar.datasets[0].data = [];
        let self = this;
        switch (type) {
          case 1:
            datasetsPie = [12, 30, 3, 5];
            labelsPie = ['Chi nội địa', 'Chi XNK', 'Chi viện trợ', 'Chi khác'];
            this.chartType = 1;
            titlePie = 'Dự toán chi';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          case 2:
            datasetsPie = [8, 23, 6, 9, 19, 25];
            labelsPie = ['Chi thường xuyên', 'Chi đầu tư phát triển', 'Chi cải cách tiền lương, tinh giảm biên chế', 'Chi viện trợ', 'Chi trả nợ', 'Chi khác'];
            this.chartType = 2;
            titlePie = 'Dự toán chi';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          case 3:
            datasetsPie = [11, 55, 32, 5];
            labelsPie = ['Chi nội địa', 'Chi XNK', 'Chi viện trợ', 'Chi khác'];
            this.chartType = 3;
            titlePie = 'Tổng chi';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          case 4:
            datasetsPie = [88, 32, 56, 78, 64];
            labelsPie = ['Chi thường xuyên', 'Chi đầu tư phát triển', 'Chi cải cách tiền lương, tinh giảm biên chế', 'Chi trả nợ', 'Chi khác'];
            this.chartType = 4;
            titlePie = 'Tổng chi';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          case 5:
            datasetsPie = [9, 45, 54, 6];
            labelsPie = ['Chi nội địa', 'Chi XNK', 'Chi viện trợ', 'Chi khác'];
            this.chartType = 5;
            titlePie = 'Quyết toán Chi';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          case 6:
            datasetsPie = [66, 33, 99, 50, 60];
            labelsPie = ['Chi thường xuyên', 'Chi đầu tư phát triển', 'Chi cải cách tiền lương, tinh giảm biên chế', 'Chi trả nợ', 'Chi khác'];
            this.chartType = 6;
            titlePie = 'Quyết toán chi';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          default:
            datasetsPie = [78, 45, 67, 54];
            labelsPie = ['Chi nội địa', 'Chi XNK', 'Chi viện trợ', 'Chi khác'];
            titlePie = 'Dự toán chi';
            this.optionDataBar.datasets[0].data = [];
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            this.chartType = 1;
        }

        this.updateData(PieChart, labelsPie, [{data: datasetsPie}], titlePie);
        this.updateData(BarChart, [], [{data: self.optionDataBar.datasets[0].data}], '');
        this.updateData(BarChart2, [], [{data: self.optionDataBar2.datasets[0].data}], '');
        this.updateData(BarChart3, [], [{data: self.optionDataBar3.datasets[0].data}], '');
      },
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

        // chart.data.labels.push(labels);
        // chart.data.datasets.forEach((datasets) => {
        //   dataset.data.push(data);
        // });
      },
      removeData(chart) {
        chart.data.labels.pop();
        chart.data.datasets.forEach((dataset) => {
          dataset.data.pop();
        });
        chart.update();
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
            self.optionDataBar.labels = [];
            _.forEach(responsesData.data, function (district, key) {
              self.optionDataBar.labels.push(district.DistrictName);
            });
            _.forEach(self.optionDataBar.datasets, function (dataset, keyDataset) {
              self.optionDataBar.datasets[keyDataset].data = [];
              for (let i = 0; i < responsesData.data.length; i++) {
                let randomNumber = _.random(0, 100);
                self.optionDataBar.datasets[keyDataset].data.push(randomNumber);
              }
            });
            this.updateData(BarChart, self.optionDataBar.labels, self.optionDataBar.datasets, '');
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
            self.optionDataBar.labels = [];
            _.forEach(responsesData.data, function (commune, key) {
              self.optionDataBar.labels.push(commune.CommuneName);
            });
            _.forEach(self.optionDataBar.datasets, function (dataset, keyDataset) {
              self.optionDataBar.datasets[keyDataset].data = [];
              for (let i = 0; i < responsesData.data.length; i++) {
                let randomNumber = _.random(0, 100);
                self.optionDataBar.datasets[keyDataset].data.push(randomNumber);
              }
            });
            this.updateData(BarChart, self.optionDataBar.labels, self.optionDataBar.datasets, '');
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      }
    },
    watch: {
      'filter.ProvinceID'(){
        if (this.filter.ProvinceID) {
          this.getDistrict();
        }
      },
      'filter.DistrictID'(){
        if (this.filter.DistrictID) {
          this.getCommune();
        }
      }
    }
  }
</script>

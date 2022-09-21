<template>
  <div class="animated fadeIn">
    <div class="row">
      <div class="col-24 mb-2" v-if="query.muc && !query.theloai">
        <b-card-group deck>
          <b-card :bg-variant="(query.theloai === 'tong-thu') ? 'primary' : 'secondary'" class="text-center small-box bg-info" @click="changeChart('tong-thu')">
            <b-card-text>
              <h4 style="color: white;">100.000 tỷ</h4>
              <h5 style="color: white;">15% GDP</h5>
              <div style="color: white;">Dự toán thu</div>
            </b-card-text>
          </b-card>
          <b-card :bg-variant="(query.theloai === 'tong-thu') ? 'primary' : 'secondary'" class="text-center small-box" style="background-color: #CC6600 !important;border-color: #CC6600;" @click="changeChart('tong-thu')">
            <b-card-text>
              <h4 style="color: white;">110.000 tỷ</h4>
              <h4 style="color: green;">tong-thulse" data-prefix="fas" data-icon="caret-up" class="svg-inline--fa fa-caret-up fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M288.662 352H31.338c-17.818 0-26.741-21.543-14.142-34.142l128.662-128.662c7.81-7.81 20.474-7.81 28.284 0l128.662 128.662c12.6 12.599 3.676 34.142-14.142 34.142z"></path></svg>
                110%</h4>
              <div style="color: white;">Tổng thu</div>
            </b-card-text>
          </b-card>
          <b-card :bg-variant="(query.theloai === 'tong-thu') ? 'primary' : 'secondary'" class="text-center small-box bg-secondary" @click="changeChart('tong-thu')">
            <b-card-text>
              <h4 style="color:black"> 109.000 tỷ </h4>
              <h4 style="color: red">
                <svg height="20" width="20" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="caret-down" class="svg-inline--fa fa-caret-down fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z"></path></svg>
                1 %</h4>
              <div>Quyết toán chi</div>
            </b-card-text>rimary\n'+
'          </b-card>\n'+
'          <b-card :bg-variant=" (query.theloai==='tong-thu') ? 'primary' : 'secondary'" class="text-center small-box bg-info" @click="changeChart('tong-thu')">
            <b-card-text>
              <h4 style="color: white;">105.000 tỷ</h4>
              <h5 style="color: white;">5% </h5>
              <div style="color: white;">Dự toán chi</div>
            </b-card-text>
          </b-card>
          <b-card :bg-variant="(query.theloai === 'tong-chi') ? 'primary' : 'secondary'" class="text-center small-box" style="background-color: #CC6600 !important;border-color: #CC6600;" @click="changeChart('tong-chi')">
            <b-card-text>
              <h4 style="color:white">115.000 tỷ</h4>
              <h5 style="color: green;"> <svg height="20" width="20" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="caret-up" class="svg-inline--fa fa-caret-up fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M288.662 352H31.338c-17.818 0-26.741-21.543-14.142-34.142l128.662-128.662c7.81-7.81 20.474-7.81 28.284 0l128.662 128.662c12.6 12.599 3.676 34.142-14.142 34.142z"></path></svg>
                9,52%   4,55%</h5>
              <div style="color:white">Tổng chi</div>
            </b-card-text>
          </b-card>
          <b-card :bg-variant="(query.theloai === 'tong-chi') ? 'primary' : 'secondary'" class="text-center small-box bg-active" @click="changeChart('tong-chi')">
            <b-card-text>

              <h4 style="color:black">113.000 tỷ</h4>
              <h5 style="color: red">
                <svg height="20" width="20" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="caret-down" class="svg-inline--fa fa-caret-down fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z"></path></svg>
                1,74%   3,67%</h5>
              <div >Quyết toán chi</div>
            </b-card-text>
          </b-card>
        </b-card-group>
      </div>
    </div>
    <div class="row" v-if="query.muc === 'quyettoan-nsnn'">
        <div class="col-23" style="padding-left: 20px">
          <canvas id="bar-chart" height="180px"></canvas>
        </div>
      <div class="col-23" style="padding-left: 20px">
        <canvas id="bar-chart-2" height="180px"></canvas>
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

  export default {
    name: 'dashboard-module-state-budget-quyettoan',
    props: {
      filter: [Array, Object],
      query: [Array, Object]
    },
    data: function () {
      return {
        chartType: 1,
        optionDataBar: {
          labels: ['Thành phố Biên Hòa', 'Thành phố Long Khánh', 'Huyện Long Thành', 'Huyện Nhơn Trạch', 'Huyện Vĩnh Cửu', 'Huyện Trảng Bom', 'Huyện Thống Nhất', '\t\n' +
          'Huyện Cẩm Mỹ', 'Huyện Xuân Lộc', 'Huyện Định quán', 'Huyện Tân Phú'],
          datasets: [
            {
              label: "Dự toán ",
              backgroundColor: "#2eadd3",
              fill: true,
              data: [ 8, 8, 4, 6, 7, 8, 6, 8,7, 9, 9],
            },
            {
              label: "Thực hiện",
              backgroundColor: "#ed7d31",
              fill: true,
              data: [6, 7, 8, 5, 5, 5, 5, 6, 6, 7, 8]
            },
            {
              label: "Quyết toán",
              backgroundColor: "#a5aeb7",
              fill: true,
              data: [7, 6, 5, 7, 6, 5, 7, 6, 8, 8, 9]
            },
          ]
        },
        optionDataBar2: {
          labels: ['Thành phố Biên Hòa', 'Thành phố Long Khánh', 'Huyện Long Thành', 'Huyện Nhơn Trạch', 'Huyện Vĩnh Cửu', 'Huyện Trảng Bom', 'Huyện Thống Nhất', '\t\n' +
          'Huyện Cẩm Mỹ', 'Huyện Xuân Lộc', 'Huyện Định quán', 'Huyện Tân Phú'],
            huyen_code :['AAA', 'BBB', 'CCC','AAA', 'BBB', 'CCC','AAA', 'BBB', 'CCC','AAA', 'BBB'],
          datasets: [
            {
              label: "Dự toán ",
              backgroundColor: "#2eadd3",
              fill: true,
              data: [8, 8, 4, 6, 7, 8, 6, 8, 7, 9, 9],
            },
            {
              label: "Thực hiện",
              backgroundColor: "#ed7d31",
              fill: true,
              data: [8, 6, 8, 7, 6, 6, 5, 5, 6, 6, 7, 8]
            },
            {
              label: "Quyết toán",
              backgroundColor: "#a5aeb7",
              fill: true,
              data: [8, 4, 6, 7, 8, 6, 8, 7, 8, 8, 9]
            },
          ]
        },
      }
    },
    components: {
    },
    mounted () {
      let self = this;
      let ctxBar = document.getElementById('bar-chart').getContext('2d');
      BarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
          labels: self.optionDataBar.labels,
          datasets: self.optionDataBar.datasets
        },
        options: {
          elements: {
            rectangle: {

            }
          },
          responsive: true,
          title: {
            display: true,
            text: 'Quyết toán thu NSNN theo địa bàn',
            position: "top",
            fontSize: 16
          },
          legend: {
            display: true
          },
          'onClick' : function (point, event) {
              let p = new URLSearchParams(window.location.search);
              p.set("name", "Jack & Jill?");
              alert(p.toString());
            // console.log(event[0]);
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
      let ctxBar2 = document.getElementById('bar-chart-2').getContext('2d');
      BarChart2 = new Chart(ctxBar2, {
        type: 'bar',
        data: {
          labels: self.optionDataBar2.labels,
          datasets: self.optionDataBar2.datasets
        },
        options: {
          elements: {
            rectangle: {

            }
          },
          responsive: true,
          title: {
            display: true,
            text: 'Quyết toán chi NSNN theo địa bàn',
            position: "top",
            fontSize: 16
          },
          legend: {
            display: true
          },
          'onClick' : function (point, event) {
              alert(3);
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
    },

    methods: {
      changeChart(type) {
        this.optionDataBar.datasets[0].data = [];
        this.optionDataBar2.datasets[0].data = [];
        let self = this;
        this.updateData(BarChart, [], [{data: self.optionDataBar.datasets[0].data}], '');
        this.updateData(BarChart2, [], [{data: self.optionDataBar2.datasets[0].data}], '');
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
        // if (title) {
        //   chart.options.title.title = title;
        //   chart.config.options.title.text = title;
        //   chart.config.options.title.title = title;
        // }
        chart.update();

        // chart.data.labels.push(labels);
        // chart.data.datasets.forEach((datasets) => {
        //   dataset.data.push(data);
        // });
      },
      changeBreadcrumb(e, url){
        e.preventDefault();
        e.stopPropagation();
        this.$router.push({
          path: url,
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


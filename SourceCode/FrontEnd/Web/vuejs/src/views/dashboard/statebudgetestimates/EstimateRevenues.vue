<template>
  <div>
    <b-card-group deck class="mb-3">
      <b-card class="text-center small-box" body-class="p-1" style="background-color: #2eadd3; border-color: #2eadd3; cursor: default !important;">
        <b-card-text>
          <h6>Dự toán thu
            <span style="font-size:20px" v-if="overviewNumber.totalRevenue">{{ overviewNumber.totalRevenue | convertNumberToText}}</span>
            <span style="font-size:20px" v-else>0</span>
            tỷ</h6>
          <div class="row">
            <div class="col-24 col-sm-24 col-md-24 col-lg-24 col-xl-12">
              <b style="color: green" v-if="overviewNumber.totalRevenue >= overviewNumber.totalRevenuePrePeriod">
                <svg v-if="" height="16" width="16" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="caret-up" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-caret-up fa-w-10"><path fill="currentColor" d="M288.662 352H31.338c-17.818 0-26.741-21.543-14.142-34.142l128.662-128.662c7.81-7.81 20.474-7.81 28.284 0l128.662 128.662c12.6 12.599 3.676 34.142-14.142 34.142z"></path></svg>
              </b>
              <b v-else style="color: red;"><svg height="20" width="20" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="caret-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-caret-down fa-w-10"><path fill="currentColor" d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z"></path></svg></b>
              {{( (!overview.totalEstimate) ? 0 :  Math.abs((overviewNumber.totalRevenue - overview.totalEstimate) / overviewNumber.totalEstimate)) | formatPercent }}%<br class="d-sm-none d-md-none d-lg-none d-xl-block">so với ước thực hiện
            </div>
            <div class="col-24 col-sm-24 col-md-24 col-lg-24 col-xl-12">
                <b v-if="overviewNumber.totalRevenue >= overviewNumber.totalRevenuePrePeriod" style="color: green;">
                  <svg height="20" width="20" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="caret-up" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-caret-up fa-w-10"><path fill="currentColor" d="M288.662 352H31.338c-17.818 0-26.741-21.543-14.142-34.142l128.662-128.662c7.81-7.81 20.474-7.81 28.284 0l128.662 128.662c12.6 12.599 3.676 34.142-14.142 34.142z"></path>
                  </svg>
                </b>
                <b v-else style="color: red;"><svg height="20" width="20" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="caret-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-caret-down fa-w-10"><path fill="currentColor" d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z"></path></svg></b>
                {{((! overviewNumber.totalRevenuePrePeriod) ? 0 : Math.abs((overviewNumber.totalRevenue - overviewNumber.totalRevenuePrePeriod) / overviewNumber.totalRevenuePrePeriod)) | formatPercent}}% <br class="d-sm-none d-md-none d-lg-none d-xl-block">so với cùng kỳ
            </div>
          </div>
        </b-card-text>
      </b-card>
      <b-card class="text-center small-box" body-class="p-1" style="max-width: 260px; background-color: rgb(57 208 251) !important;border-color: rgb(38 136 165); cursor: default !important;">
        <b-card-text>
          <h6>
            <span style="font-size:20px" v-if="overviewNumber.taxAgency">{{overviewNumber.taxAgency | convertNumberToText}}</span>
            <span style="font-size:20px" v-else>0</span>
            tỷ</h6>
          <div class="row">
            <div class="col-24 mb-2">
              <b style="color: rgb(197, 90, 17);">≈</b>{{ (!overviewNumber.totalRevenue) ? 0 : (overviewNumber.taxAgency / overviewNumber.totalRevenue) | formatPercent}}%
              <br>Cơ quan thuế thu
            </div>
          </div>
        </b-card-text>
      </b-card>
      <b-card class="text-center small-box" body-class="p-1" style="max-width: 260px;background-color: rgb(142 229 255) !important;border-color: rgb(38 136 165); cursor: default !important;">
        <b-card-text>
          <h6>
            <span style="font-size:20px" v-if="overviewNumber.customsAgency">{{overviewNumber.customsAgency | convertNumberToText}}</span>
            <span style="font-size:20px" v-else>0</span>
            tỷ</h6>
          <div class="row">
            <div class="col-24 mb-2">
              <b style="color: rgb(197, 90, 17);">≈</b>{{ (!overviewNumber.totalRevenue) ? 0 : (overviewNumber.customsAgency / overviewNumber.totalRevenue) | formatPercent}}%
              <br>Hải quan thu
            </div>
          </div>
        </b-card-text>
      </b-card>
      <b-card class="text-center small-box" body-class="p-1" style="max-width: 260px;background-color: rgb(198 242 255) !important;border-color: rgb(38 136 165); cursor: default !important;">
        <b-card-text>
          <h6>
            <span style="font-size:20px" v-if="overviewNumber.otherAgency">{{overviewNumber.otherAgency | convertNumberToText}}</span>
            <span style="font-size:20px" v-else>0</span>
            tỷ</h6>
          <div class="row">
            <div class="col-24 mb-2">
              <b style="color: rgb(197, 90, 17);">≈</b>{{ (!overviewNumber.totalRevenue) ? 0 : (overviewNumber.otherAgency / overviewNumber.totalRevenue) | formatPercent}}%
              <br>Cơ quan khác thu
            </div>
          </div>
        </b-card-text>
      </b-card>
    </b-card-group>

    <div class="row">
      <div class="col-24 col-sm-24 col-md-24 col-lg-24 col-xl-14" style="padding-top: 30px">
        <BarChart3 :filter="filter" :overviewNumber="overviewNumber" v-model="model.Cct" :labels-type="labelsType" ></BarChart3>
      </div>

      <div class="col-24 col-sm-24 col-md-24 col-lg-24 col-xl-9" style="padding-top: 30px">
        <line-chart :filter="filter" :overview-number="overviewNumber" v-model="model.Knst" :labels-type="labelsType" ></line-chart>
      </div>
    </div>
    <div class="row">
      <div class="col-24 col-sm-24 col-md-24 col-lg-24 col-xl-14" style="padding-top: 30px" v-show="filter.UserType !== 2">
        <BarChart2 :filter="filter" :overviewNumber="overviewNumber" v-model="model.Lvt"></BarChart2>
      </div>
      <div class="col-24 col-sm-24 col-md-24 col-lg-24 col-xl-24" style="padding-top: 30px" v-show="filter.UserType === 2">
        <BarChart22 :filter="filter" :overviewNumber="overviewNumber" ></BarChart22>
      </div>
      <div class="col-24 col-sm-24 col-md-18 m-md-auto m-lg-auto col-lg-18 col-xl-9" style="padding-top: 30px" v-if="filter.UserType !== 2">
        <pie-chart :filter="filter" v-model="model.Cnst"></pie-chart>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-24" style="padding-left: 20px; padding-top: 30px;">
        <BarChart :filter="filter" :overviewNumber="overviewNumber" v-model="model.Dbt"></BarChart>
      </div>
    </div>
  </div>
</template>
<style>
  .text-to-voice {
    position: fixed;
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
<script>
  import ApiService from '@/services/api.service';
  import Chart from 'chart.js';
  import 'chartjs-plugin-labels';
  import 'chartjs-plugin-zoom';
  import LineChart from "./EstimateRevenues/LineChart";
  import PieChart from "./EstimateRevenues/PieChart";
  import BarChart3 from './EstimateRevenues/BarChart3';
  import BarChart from './EstimateRevenues/BarChart';
  import BarChart2 from './EstimateRevenues/BarChart2';
  import BarChart22 from './EstimateRevenues/BarChart22';


  export default {
    name: 'dashboard-module-state-budget-revenues',
    props: {
      filter: [Array, Object],
      overview: [Array, Object]
    },
    data: function () {
      return {
        model:{
          Tht: [],
          Cct: [],
          Knst: [],
          Lvt: [],
          Dbt: [],
          Cnst: [],
        },
        isFetchData: false,
        labelsType: "thu",
        chartType: 1,
        optionDataBar: {
          labels: ['Thành phố Biên Hòa', 'Thành phố Long Khánh', 'Huyện Long Thành',
            'Huyện Nhơn Trạch', 'Huyện Vĩnh Cửu', 'Huyện Trảng Bom', 'Huyện Thống Nhất',
            'Huyện Cẩm Mỹ', 'Huyện Xuân Lộc', 'Huyện Định quán', 'Huyện Tân Phú'
          ],
          datasets: [
            {
              label: "Dự toán",
              backgroundColor: "#2eadd3",
              fill: false,
              data: [9, 9, 3, 8, 5, 6, 5, 6, 7, 8, 8],
            },
            {
              label: "Đã thu",
              backgroundColor: "#ed7d31",
              fill: false,
              data: [8, 8, 4, 6, 7, 8, 8, 8, 5, 6, 9]
            },
          ]
        },
        optionDataBar2: {
          labels: [
            'Thu nội địa',
            'Thu dầu thô và khí thiên nhiên',
            'Thu từ xuất nhập khẩu',
            'Thu bổ sung từ ngân sách cấp trên',
            'Thu từ quỹ dự trữ tài chính',
            'Thu kết dư',
            'Thu chuyển nguồn từ năm trước',
            'Thu huy động đóng góp',
            'Thu viện trợ',
            'Thu vay nợ ',
            'Thu từ ngân sách cấp dưới nộp lên',
            'Tạm thu ngân sách',
          ],
          datasets: [
            {
              label: "Dự toán",
              backgroundColor: "#2eadd3",
              data: [29, 29, 23, 28, 26, 25, 26, 23, 28, 26, 25, 26],
            },
            {
              label: "Đã thu",
              backgroundColor: "#ed7d31",
              data: [28, 28, 24, 26, 28, 25, 28, 28, 28, 24, 26, 28]
            },
          ]
        },
        optionDataBar3: {
          labels: [
            'Thu thuế',
            'Thu phí, lệ phí',
            'Thu dầu thô và khí thiên nhiên',
            'Vốn góp & các khoản đầu tư của NN',
            'Thu từ viện trợ không hoàn lại',
            'Thu vay nợ ',
            'Thu huy động đóng góp',
            'Thu khác'
          ],
          datasets: [
            {
              label: "Dự toán",
              backgroundColor: "#2eadd3",
              fill: true,
              data: [29, 29, 23, 28, 26, 25, 26, 28],
            },
            {
              label: "Đã thu ",
              backgroundColor: "#ed7d31",
              fill: true,
              data: [28, 28, 24, 26, 28, 25, 28, 23]
            },
          ]
        },
        optionDataLineChar: {
          labels: ['Thu NSNN theo cơ cấu thu (Tỷ dồng)'],
          datasets: [{
            label: '',
            data: [],
            backgroundColor: [],
            borderColor: [],
            borderWidth: null
          }]
        },
        overviewNumber : {
          totalRevenue: 16622,
          totalEstimate: 22409,
          compareEstimate: 110,
          compareSamePeriod: 21.97,
          totalRevenuePrePeriod: 21302,
          taxAgency: 9382,
          customsAgency: 3365,
          otherAgency: 3875
        }
      }
    },
    components: {
      LineChart,
      PieChart,
      BarChart3,
      BarChart,
      BarChart2,
      BarChart22,

    },
    mounted () {
      let self = this;
      this.fetchData();
    },
    created() {
      this.fetchData();
    },
    methods: {
      fetchData(){
        if (!this.isFetchData) {
          let self = this;
          let requestData = {
            method: 'post',
            url: 'dashboard/api/state-budget-estimate/estimate-revenue'+'?XDEBUG_SESSION_START=PHPSTORM',
            data: {
              filter: this.filter
            }
          };
          // Api edit user
          this.$store.commit('isLoading', true);
          this.isFetchData = true;
          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            let responsesData = responses.data;
            if (responsesData.data) {
              self.model.Tht = responses.data.data.Tht;
              self.model.Cct = responses.data.data.Cct;
              self.model.Knst = responses.data.data.Knst;
              self.model.Lvt = responses.data.data.Lvt;
              self.model.Dbt = responses.data.data.Dbt;
              self.model.Cnst = responses.data.data.Cnst;


              self.overviewNumber.totalRevenue = 0;
              self.overviewNumber.totalRevenuePrePeriod = 0;
              self.overviewNumber.totalEstimate = 0;
              self.overviewNumber.taxAgency = 0;
              self.overviewNumber.customsAgency = 0;
              self.overviewNumber.otherAgency = 0;
              if(self.model.Tht){
                _.forEach(self.model.Tht, function (val, key){
                  if(val.DT1){
                    self.overviewNumber.totalRevenuePrePeriod += val.DT1;
                  }
                  if(val.DT2){
                    self.overviewNumber.totalRevenue += val.DT2;
                    if(val.STT === 1){
                      self.overviewNumber.taxAgency = val.DT2;
                    }
                    if(val.STT === 2){
                      self.overviewNumber.customsAgency = val.DT2;
                    }
                    if(val.STT === 3){
                      self.overviewNumber.otherAgency = val.DT2;
                    }
                  }
                  if(val.UTT){
                    self.overviewNumber.totalEstimate += val.UTT;
                  }

                });
              }
            }
            self.isFetchData = false;
            self.$store.commit('isLoading', false);
          }, (error) => {
            console.log(error);
            self.isFetchData = false;
            self.$store.commit('isLoading', false);
          });
        }

      },

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
            labelsPie = ['Thu nội địa', 'Thu XNK', 'Thu viện trợ', 'Thu khác'];
            this.chartType = 1;
            titlePie = 'Dự toán thu';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          case 2:
            datasetsPie = [8, 23, 6, 9, 19, 25];
            labelsPie = ['Thu thường xuyên', 'Thu đầu tư phát triển', 'Thu cải cách tiền lương, tinh giảm biên chế', 'Thu viện trợ', 'Thu trả nợ', 'Thu khác'];
            this.chartType = 2;
            titlePie = 'Dự toán thu';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          case 3:
            datasetsPie = [11, 55, 32, 5];
            labelsPie = ['Thu nội địa', 'Thu XNK', 'Thu viện trợ', 'Thu khác'];
            this.chartType = 3;
            titlePie = 'Tổng thu';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          case 4:
            datasetsPie = [88, 32, 56, 78, 64];
            labelsPie = ['Thu thường xuyên', 'Thu đầu tư phát triển', 'Thu cải cách tiền lương, tinh giảm biên chế', 'Thu trả nợ', 'Thu khác'];
            this.chartType = 4;
            titlePie = 'Tổng thu';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          case 5:
            datasetsPie = [9, 45, 54, 6];
            labelsPie = ['Thu nội địa', 'Thu XNK', 'Thu viện trợ', 'Thu khác'];
            this.chartType = 5;
            titlePie = 'Quyết toán thu';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          case 6:
            datasetsPie = [66, 33, 99, 50, 60];
            labelsPie = ['Thu thường xuyên', 'Thu đầu tư phát triển', 'Thu cải cách tiền lương, tinh giảm biên chế', 'Thu trả nợ', 'Thu khác'];
            this.chartType = 6;
            titlePie = 'Quyết toán thu';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          default:
            datasetsPie = [78, 45, 67, 54];
            labelsPie = ['Thu nội địa', 'Thu XNK', 'Thu viện trợ', 'Thu khác'];
            titlePie = 'Dự toán thu';
            this.optionDataBar.datasets[0].data = [];
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            this.chartType = 1;
        }
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
            // this.updateData(BarChart, self.optionDataBar.labels, self.optionDataBar.datasets, '');
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
            // this.updateData(BarChart, self.optionDataBar.labels, self.optionDataBar.datasets, '');
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      changeFilter() {
        let self = this;
        let ratePeriod = 1;
        if (this.filter.PeriodID === 6) {
          ratePeriod = _.random(0.3, 0.7);
        }else if (this.filter.PeriodID === 7) {
          ratePeriod = _.random(0.5, 0.9);
        }else if (this.filter.PeriodID === 8) {
          ratePeriod = _.random(2.5, 3.5);
        }else if (this.filter.PeriodID === 9) {
          ratePeriod = _.random(3.5, 8.5);
        }else if (this.filter.PeriodID === 10) {
          ratePeriod = _.random(7.5, 13.5);
        }

        let rateDistrict = 1;
        let rateCommune = 1;

        let rateRevenueCate = _.random(0.2, 0.8);

        if (this.filter.PeriodID === 1 && Number(this.filter.PeriodValue) > 2020){
          this.overviewNumber.totalRevenue = 0;
          this.overviewNumber.compareSamePeriod = 0;
          this.overviewNumber.taxAgency = 0;
          this.overviewNumber.customsAgency = 0;
          this.overviewNumber.otherAgency = 0;
          return;
        }

        if (!this.overviewNumber.totalRevenue) {
          this.overviewNumber.totalRevenue = _.random(10000, 33650);
        }else if (this.filter.ProvinceID === 36 && !this.filter.DistrictID && !this.filter.CommuneID) {
          if (!this.filter.RevenueCateID) {
            return;
          }
        }

        if (this.filter.ProvinceID && !this.filter.DistrictID && !this.filter.CommuneID) {
          this.overviewNumber.totalRevenue = this.overviewNumber.totalRevenue * ratePeriod;
        }else if (this.filter.ProvinceID && this.filter.DistrictID && !this.filter.CommuneID) {
          rateDistrict = _.random(1 / 15, 1 / 35);
          this.overviewNumber.totalRevenue = this.overviewNumber.totalRevenue * rateDistrict * ratePeriod;

        }else if (this.filter.CommuneID) {
          rateDistrict = _.random(1 / 15, 1 / 35);
          rateCommune = _.random(1 / 60, 1 / 120);
          this.overviewNumber.totalRevenue = this.overviewNumber.totalRevenue * rateDistrict * rateCommune * ratePeriod;
        }

        if (this.filter.RevenueCateID) {
          this.overviewNumber.totalRevenue *= rateRevenueCate;
        }

        this.overviewNumber.totalRevenue = Math.floor(this.overviewNumber.totalRevenue);
        this.overviewNumber.totalEstimate = _.random(this.overviewNumber.totalRevenue + 1000 * ratePeriod * rateDistrict * rateCommune, this.overviewNumber.totalRevenue + 2000 * ratePeriod * rateDistrict * rateCommune);
        this.overviewNumber.totalEstimate = Math.floor(this.overviewNumber.totalEstimate);
        this.overviewNumber.totalRevenuePrePeriod = _.random(this.overviewNumber.totalRevenue - 1000 * ratePeriod * rateDistrict * rateCommune, this.overviewNumber.totalRevenue + 1000 * ratePeriod * rateDistrict * rateCommune);
        this.overviewNumber.totalRevenuePrePeriod = Math.floor(this.overviewNumber.totalRevenuePrePeriod);
        this.overviewNumber.compareSamePeriod = _.random(0.1, 5.8);
        this.overviewNumber.compareSamePeriod = this.overviewNumber.compareSamePeriod.toFixed(2);
        this.overviewNumber.taxAgency = _.random((this.overviewNumber.totalRevenue / 3) - 1000 * ratePeriod * rateDistrict * rateCommune, (this.overviewNumber.totalRevenue / 3) + 1000 * ratePeriod * rateDistrict * rateCommune);
        this.overviewNumber.taxAgency = Math.floor(this.overviewNumber.taxAgency);
        this.overviewNumber.customsAgency = _.random((this.overviewNumber.totalRevenue / 4) - 1000 * ratePeriod * rateDistrict * rateCommune, (this.overviewNumber.totalRevenue / 4) + 1000 * ratePeriod * rateDistrict * rateCommune);
        this.overviewNumber.customsAgency = Math.floor(this.overviewNumber.customsAgency);
        this.overviewNumber.otherAgency = this.overviewNumber.totalRevenue - this.overviewNumber.taxAgency - this.overviewNumber.customsAgency;

      },
      resetOverviewNumber() {
        if (this.filter.ProvinceID === 36) {
          if (!this.filter.DistrictID && !this.filter.CommuneID) {
            let ratePeriod = 1;
            if (this.filter.PeriodID === 6) {
              ratePeriod = _.random(0.3, 0.7);
            }else if (this.filter.PeriodID === 7) {
              ratePeriod = _.random(0.5, 0.9);
            }else if (this.filter.PeriodID === 8) {
              ratePeriod = _.random(2.5, 3.5);
            }else if (this.filter.PeriodID === 9) {
              ratePeriod = _.random(3.5, 8.5);
            }else if (this.filter.PeriodID === 10) {
              ratePeriod = _.random(7.5, 13.5);
            }

            this.overviewNumber.totalRevenue = 16622 * ratePeriod;
            this.overviewNumber.totalRevenue = Math.floor(this.overviewNumber.totalRevenue);
            if (ratePeriod === 1) {
              this.overviewNumber.totalEstimate = 22409 * ratePeriod;
              this.overviewNumber.totalRevenuePrePeriod = 21409 * ratePeriod;
            } else {
              this.overviewNumber.totalEstimate = _.random(16622 + 500 * ratePeriod, 16622 + 1000 * ratePeriod);
              this.overviewNumber.totalEstimate = this.overviewNumber.totalEstimate * ratePeriod;

              this.overviewNumber.totalRevenuePrePeriod = _.random(16622 - 500 * ratePeriod, 16622 + 500 * ratePeriod);
              this.overviewNumber.totalRevenuePrePeriod = this.overviewNumber.totalRevenuePrePeriod * ratePeriod;
            }
            this.overviewNumber.totalEstimate = Math.floor(this.overviewNumber.totalEstimate);
            this.overviewNumber.totalRevenuePrePeriod = Math.floor(this.overviewNumber.totalRevenuePrePeriod);
            this.overviewNumber.compareSamePeriod = 2;
            this.overviewNumber.taxAgency = 6550 * ratePeriod;
            this.overviewNumber.taxAgency = Math.floor(this.overviewNumber.taxAgency);
            this.overviewNumber.customsAgency = 5951 * ratePeriod;
            this.overviewNumber.customsAgency = Math.floor(this.overviewNumber.customsAgency);
            this.overviewNumber.otherAgency = this.overviewNumber.totalRevenue - this.overviewNumber.taxAgency - this.overviewNumber.customsAgency;
          }else {
            this.overviewNumber.totalRevenue = 16622;
          }
        }else {
          this.overviewNumber.totalRevenue = 0;
        }

        if (this.filter.PeriodID === 1 && this.filter.PeriodValue != 2020) {
          this.overviewNumber.totalRevenue = 0;
        }else if (this.filter.PeriodID === 1 && this.filter.PeriodValue == 2020){
          this.overviewNumber.totalRevenue = 16622;
          this.overviewNumber.totalEstimate = 22409;
          this.overviewNumber.totalRevenuePrePeriod = 21302;
          this.overviewNumber.compareSamePeriod = 2;
          this.overviewNumber.taxAgency = 6550;
          this.overviewNumber.customsAgency = 5951;
          this.overviewNumber.otherAgency = this.overviewNumber.totalRevenue - this.overviewNumber.taxAgency - this.overviewNumber.customsAgency;
        }
      }
    },
    watch: {
      'filter.UserType'(){
        this.filter.ProvinceName = 'Tỉnh Nam Định';
        this.filter.ProvinceNo = '420000';
        this.filter.ProvinceID = 36;
        this.filter.DistrictName = '';
        this.filter.DistrictNo = '';
        this.filter.DistrictID = null;
        this.filter.CommuneName = '';
        this.filter.CommuneID = null;
        this.filter.CommuneNo = '';
        this.filter.CompanyName = '';
        this.filter.CompanyNo = '';
        this.filter.CompanyID = null;
        this.filter.SectorID = null;
        this.filter.SectorNo = '';
        this.filter.SectorName = '';
        this.filter.SectorNo = '';
        this.filter.SectorID = null;
        this.filter.PeriodID = null;
        this.filter.PeriodNo = '';
        this.filter.PeriodName = '';
        this.filter.PeriodValue = '';
        this.filter.PeriodValueName = '';
        this.filter.FromDate = null;
        this.filter.ToDate = null;

        this.overviewNumber.totalRevenue = 16622;
        this.overviewNumber.totalEstimate = 22409;
        this.overviewNumber.totalRevenuePrePeriod = 21302;
        this.overviewNumber.compareSamePeriod = 2;
        this.overviewNumber.taxAgency = 6550;
        this.overviewNumber.customsAgency = 5951;
        this.overviewNumber.otherAgency = 6582;

        this.resetOverviewNumber();

      },
      'filter.RevenueCateID'(){
        this.resetOverviewNumber();

      },
      'filter.ProvinceID'(){
       this.fetchData();
      },
      'filter.DistrictID'(){
       this.fetchData();
      },
      'filter.CommuneID'(){
       this.fetchData();
      },
      'filter.CompanyID'(){
        this.fetchData();
      },
      'filter.SectorID'(){
        this.fetchData();
      },
      'filter.PeriodValue'(){
        this.fetchData();
      },
      'filter.PeriodID'(){
        this.filter.PeriodValue = null;
        this.fetchData()
      },
    }
  }
</script>

import {TokenService} from '@/services/storage.service';
import moment from "moment";
export default {
  data(){
    return {
      PeriodValueOption: [],
      PeriodValue: null,
      FromDate: '',
      ToDate: '',
      PeriodID: null // 1 - Năm, 2 - Quý, 3 - Tháng, 4 - Tuần, 5 - Ngày, 6 - 6 tháng, 7 - 9 tháng, 8 - 3 năm, 9 - 5 năm, 10 - 10 năm
    }
  },
  methods:{
    $_period_changePeriodType(){
      let self = this;
      let workDate = TokenService.getWorkdate();
      if (!workDate) {
        workDate = moment().format('L');
      }
      let momentWorkDate = moment(workDate, 'L');
      let yearWorkDate = momentWorkDate.get("year");
      let monthWorkDate = momentWorkDate.get('month');
      this.PeriodValueOption = [];
      switch (this.PeriodID) {
        case 1:
          for (let i = 8; i >= 1; i--) {
            let year = Number(yearWorkDate) - i;
            let tmpObj = {};
            tmpObj.value = year;
            tmpObj.text = year;
            tmpObj.fromDate = moment([year]).startOf("year").format('L');
            tmpObj.toDate = moment([year]).endOf("year").format('L');
            self.PeriodValueOption.push(tmpObj);
          }
          this.PeriodValueOption.push({
            value: yearWorkDate,
            text: yearWorkDate,
            fromDate: moment([yearWorkDate]).startOf("year").format('L'),
            toDate: moment([yearWorkDate]).endOf("year").format('L')
          });
          for (let i = 1; i <= 8; i++) {
            let year = Number(yearWorkDate) + i;
            let tmpObj = {};
            tmpObj.value = Number(yearWorkDate);
            tmpObj.text = year;
            tmpObj.fromDate = moment([year]).startOf("year").format('L');
            tmpObj.toDate = moment([year]).endOf("year").format('L');
            self.PeriodValueOption.push(tmpObj);
          }
          this.PeriodValue = Number(yearWorkDate);
          break;
        case 2:
          this.PeriodValueOption.push({
            value: 1,
            text: 'Quý 1/' + yearWorkDate,
            fromDate: moment([yearWorkDate, 0]).startOf("months").format('L'),
            toDate: moment([yearWorkDate, 2]).endOf("months").format('L')
          });
          this.PeriodValueOption.push({
            value: 2,
            text: 'Quý 2/' + yearWorkDate,
            fromDate: moment([yearWorkDate, 3]).startOf("months").format('L'),
            toDate: moment([yearWorkDate, 5]).endOf("months").format('L')
          });
          this.PeriodValueOption.push({
            value: 3,
            text: 'Quý 3/' + yearWorkDate,
            fromDate: moment([yearWorkDate, 6]).startOf("months").format('L'),
            toDate: moment([yearWorkDate, 8]).endOf("months").format('L')
          });
          this.PeriodValueOption.push({
            value: 4,
            text: 'Quý 4/' + yearWorkDate,
            fromDate: moment([yearWorkDate, 9]).startOf("months").format('L'),
            toDate: moment([yearWorkDate, 11]).endOf("months").format('L')
          });
          this.PeriodValue = 1;
          break;
        case 3:
          for (let i = 1; i <= 12; i++) {
            self.PeriodValueOption.push({
              value: i,
              text: 'Tháng ' + i + '/' + yearWorkDate,
              fromDate: moment([yearWorkDate, i - 1]).startOf("months").format('L'),
              toDate: moment([yearWorkDate, i - 1]).endOf("months").format('L')
            });
          }
          this.PeriodValue = 1;
          break;
        case 4:
          for (let i = 1; i <= 52; i++) {
            self.PeriodValueOption.push({
              value: i,
              text: 'Tuần ' + i + '/' + yearWorkDate,
              fromDate: moment(workDate).week(i - 1).startOf('week').format('L'),
              toDate: moment(workDate).week(i-1).endOf('week').format('L')
            });
          }
          this.PeriodValue = 1;
          break;
        case 5:
          this.FromDate = workDate;
          this.ToDate = this.FromDate;
          break;
        case 6:
          self.PeriodValueOption.push({
            value: 1,
            text: yearWorkDate + '/6th đầu',
            fromDate: moment([yearWorkDate, 0]).startOf("months").format('L'),
            toDate: moment([yearWorkDate, 5]).endOf("months").format('L')
          });
          self.PeriodValueOption.push({
            value: 2,
            text: yearWorkDate + '/6th cuối',
            fromDate: moment([yearWorkDate, 6]).startOf("months").format('L'),
            toDate: moment([yearWorkDate, 11]).endOf("months").format('L')
          });
          this.PeriodValue = 1;
          // this.FromDate = workDate;
          // this.ToDate = workDate;
          break;
        case 7:
          this.PeriodValueOption.push({
            value: 1,
            text: (Number(yearWorkDate) - 1) + '/9 tháng',
            fromDate: moment([(Number(yearWorkDate) - 1), 0]).startOf("months").format('L'),
            toDate: moment([(Number(yearWorkDate) - 1), 8]).endOf("months").format('L')
          });
          this.PeriodValueOption.push({
            value: 2,
            text: (Number(yearWorkDate)) + '/9 tháng',
            fromDate: moment([yearWorkDate, 0]).startOf("months").format('L'),
            toDate: moment([yearWorkDate, 8]).endOf("months").format('L')
          });
          this.PeriodValueOption.push({
            value: 3,
            text: (Number(yearWorkDate) + 1) + '/9 tháng',
            fromDate: moment([(Number(yearWorkDate) + 1), 0]).startOf("months").format('L'),
            toDate: moment([(Number(yearWorkDate) + 1), 8]).endOf("months").format('L')
          });
          this.PeriodValue = 2;
          break;
        case 8:
          this.PeriodValueOption.push({
            value: 1,
            text: (Number(yearWorkDate) - 3) + ' - ' + (Number(yearWorkDate) - 1),
            fromDate: moment([Number(yearWorkDate) - 3]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) - 1]).endOf("year").format('L')
          });
          this.PeriodValueOption.push({
            value: 2,
            text: (Number(yearWorkDate)) + ' - ' + (Number(yearWorkDate) + 2),
            fromDate: moment([Number(yearWorkDate)]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) + 1]).endOf("year").format('L')
          });
          this.PeriodValueOption.push({
            value: 3,
            text: (Number(yearWorkDate) + 3) + ' - ' + (Number(yearWorkDate) + 5),
            fromDate: moment([Number(yearWorkDate) + 3]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) + 5]).endOf("year").format('L')
          });
          this.PeriodValue = 2;
          break;
        case 9:
          this.PeriodValueOption.push({
            value: 1,
            text: (Number(yearWorkDate) - 5) + ' - ' + (Number(yearWorkDate) - 1),
            fromDate: moment([Number(yearWorkDate) - 5]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) - 1]).endOf("year").format('L')
          });
          this.PeriodValueOption.push({
            value: 2,
            text: (Number(yearWorkDate)) + ' - ' + (Number(yearWorkDate) + 4),
            fromDate: moment([Number(yearWorkDate)]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) + 4]).endOf("year").format('L')
          });
          this.PeriodValueOption.push({
            value: 3,
            text: (Number(yearWorkDate) + 5) + ' - ' + (Number(yearWorkDate) + 9),
            fromDate: moment([Number(yearWorkDate) + 5]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) + 9]).endOf("year").format('L')
          });
          this.PeriodValue = 2;
          break;
        case 10:
          this.PeriodValueOption.push({
            value: 1,
            text: (Number(yearWorkDate) - 10) + ' - ' + (Number(yearWorkDate) - 1),
            fromDate: moment([Number(yearWorkDate) - 10]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) - 1]).endOf("year").format('L')
          });
          this.PeriodValueOption.push({
            value: 2,
            text: (Number(yearWorkDate)) + ' - ' + (Number(yearWorkDate) + 9),
            fromDate: moment([Number(yearWorkDate)]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) + 9]).endOf("year").format('L')
          });
          this.PeriodValueOption.push({
            value: 3,
            text: (Number(yearWorkDate) + 10) + ' - ' + (Number(yearWorkDate) + 19),
            fromDate: moment([Number(yearWorkDate) + 10]).startOf("year").format('L'),
            toDate: moment([Number(yearWorkDate) + 19]).endOf("year").format('L')
          });
          this.PeriodValue = 2;
          break;
        case 99:
          this.FromDate = '';
          this.ToDate = '';
          break;
        default:
          break;
      }
      this.$_period_changePeriodValue();
    },
    $_period_changePeriodValue(){
      let dateRange = _.find(this.PeriodValueOption, ['value', Number(this.PeriodValue)]);
      if (dateRange) {
        this.FromDate = dateRange.fromDate;
        this.ToDate = dateRange.toDate;
      }
    }
  },
  watch:{}
}

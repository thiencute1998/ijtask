<template>
    <date-picker
      valueType="format"
      ref="datePicker"
      :lang="lang"
      :open="openPanel"
      :format="momentFormat"
       :disabled="disabled"
      :input-class="classDatePicker"
      :disabled-date="disabledBeforeDayAndAfterDay"
       v-bind:value="parentValue"
       v-on:change="updateValue(parentValue)"
       v-model="parentValue"
       @clear="eventHandle('clear-date-picker')"
       @input="eventHandle('input-date-picker')"
       @open="eventHandle('openPanel-date-picker')"
       @close="eventHandle('close-date-picker')"
       @confirm="eventHandle('confirm-date-picker')"
       @input-error="eventHandle('input-error-date-picker')"
       @focus="eventHandle('focus-date-picker')"
       @blur="eventHandle('blur-date-picker')"
       @pick="eventHandle('pick-date-picker')"
       @calendar-change="eventHandle('calendar-change-date-picker')"
       v-on:click="updateStatusHour">
      <template v-slot:input="{ emit }">
        <masked-input
          :disabled="disabled"
          type="text"
          class="form-control date-picker-masked-input"
          :id="'date-picker-masked-input-' + _uid"
          :mask="[/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/]"
          :guide="true"
          v-click-outside="clickOutsideDatePicker"
          placeholderChar="_"
          @focus="onTogglePanelDatePicker(true)"
          @keypress.enter="onTogglePanelDatePicker(false)"
          :showMask="true"
          :keepCharPositions="true"
          :pipe="autoCorrectedDatePipe()"
          v-on:change="updateValue(parentValue)"
          v-model="parentValue"
          v-on:click="updateStatusHour"
        >
        </masked-input>

      </template>
    </date-picker>
</template>

<script>
    import MaskedInput from 'vue-text-mask';
    import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
    import ClickOutside from 'vue-click-outside';
    import moment from 'moment';
    //datepicker
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';
    import 'vue2-datepicker/locale/vi';
    let today = '';
    moment.locale('vi');
    export default {
      name: 'IjcoreDatePicker',
      components: {
        MaskedInput,
        DatePicker,
      },
      data () {
        return {
          parentValue: this.value,
          classDatePicker: 'mx-input demo',
          momentFormat: 'DD/MM/YYYY',
          lang: {
            formatLocale: {
              firstDayOfWeek: 1,
            },
            monthBeforeYear: false,
          },
          openPanel: false
        }
      },
      created() {
        let checkFormat = moment(this.value, 'DD/MM/YYYY', true).isValid();
        if (!checkFormat && this.value) {
          this.parentValue = moment(this.value).format('L');
        }
      },
      mounted() {},
      methods: {
        updateStatusHour(){
        },
        autoCorrectedDatePipe: () => {
          return createAutoCorrectedDatePipe('dd/mm/yyyy');
        },
        updateValue: function (value) {
          this.$emit('input', value);
        },
        onTogglePanelDatePicker(value){
          this.openPanel = value;
        },
        clickOutsideDatePicker(e){
            if (typeof e === 'undefined') {
                this.onTogglePanelDatePicker(false);
            }
            if (e && !$(e.target).closest('.mx-datepicker-main').length && !$(e.target).hasClass('mx-btn')) {
                this.onTogglePanelDatePicker(false);
            }
        },
        eventHandle(type) {
          if (type === 'clear-date-picker') {
            this.openPanel = false;
          }
          if (type === 'pick-date-picker') {
            this.onTogglePanelDatePicker(false);
          }
          this.$emit(type);
        },
        disabledBeforeDayAndAfterDay(date) {
          if (!this.disableBeforeDay && !this.disableAfterDay) {
            return false;
          }
          let disableBeforeDay = null;
          let disableAfterDay = null;
          if (this.disableFormatDate) {
            disableBeforeDay = moment(this.disableBeforeDay, this.disableFormatDate);
            disableAfterDay = moment(this.disableAfterDay, this.disableFormatDate);
          } else {
            disableBeforeDay = moment(this.disableBeforeDay);
            disableAfterDay = moment(this.disableAfterDay);
          }
          return date.getTime() < disableBeforeDay.format('x') || date.getTime() > disableAfterDay.format('x');
        },
      },
      watch:{
        value(){
          this.parentValue = this.value;
        }
      },
      // props: ['today', 'value'],
      props: {
        today: [String, Object, Array],
        value: [String, Object, Array],
        disabled: false,
        disableBeforeDay: {
          type: String,
          default: ''
        },
        disableAfterDay: {
          type: String,
          default: ''
        },
        disableFormatDate: {
          type: String,
          default: ''
        }
      },
      directives: {
        ClickOutside
      }
    }
</script>

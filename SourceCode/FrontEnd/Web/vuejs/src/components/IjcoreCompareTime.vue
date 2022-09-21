<!--============== How to use =======================================

    <ijcore-compare-time v-model="object"></ijcore-compare-time>

==================================================================-->

<template>
    <div class="component-compare-time">
      <b-form-group>
        <b-input-group :title="title">
          <template slot="prepend">
            <b-dropdown :text="model.operator" ref="dropdownCompareTime" variant="secondary" @toggle="onToggleDropdown" :no-caret="true" class="app-dropdown-center ij-operator-time">
              <b-dropdown-item
                class="text-center"
                @click="onChangeOperator(operator)"
                v-for="operator in operatorArray" :key="operator.id"> {{operator}} </b-dropdown-item>
            </b-dropdown>
          </template>
          <ijcore-date-picker
            ref="datePicker"
            @clear-date-picker="eventHandle('clear-date-picker')"
            @input="eventHandle('input-date-picker')"
            @open="eventHandle('openPanel-date-picker')"
            @close="eventHandle('close-date-picker')"
            @confirm="eventHandle('confirm-date-picker')"
            @input-error="eventHandle('input-error-date-picker')"
            @focus="eventHandle('focus-date-picker')"
            @blur="eventHandle('blur-date-picker')"
            @pick="eventHandle('pick-date-picker')"
            @calendar-change="eventHandle('calendar-change-date-picker')"
            v-model="model.dateTime">
          </ijcore-date-picker>
        </b-input-group>
      </b-form-group>
    </div>
</template>

<style lang="css">
  .component-compare-time .input-group {
    flex-wrap: nowrap;
  }
  .component-compare-time input {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
  }
</style>
<script>
  import IjcoreDatePicker from "@/components/IjcoreDatePicker";
  export default {
    name: 'compare-time',
    props:{
      value: {
        type: Object,
        default () {
          return {}
        }
      },
      title: {
        type: String,
        default: ''
      }
    },
    data () {
      return {
        model: {
          operator: (this.value && this.value.operator) ? this.value.operator : '=',
          dateTime: (this.value && this.value.dateTime) ? this.value.dateTime : ''
        },
        operatorArray: ['=', '>=', '<=', '>', '<'],
      }
    },
    created() {},
    mounted() {},
    components: {
      IjcoreDatePicker
    },
    methods: {
      onChangeOperator(operator){
        this.model.operator = operator;
      },
      onToggleDropdown(){
        this.$refs.datePicker.clickOutsideDatePicker();
      },
      eventHandle(type) {
        if (type === 'clear-date-picker') {
          this.$refs.dropdownCompareTime.hide(true);
        }
        this.$emit(type);
      },
    },
    watch:{
      model: {
        handler(newOptionValue) {
          this.$emit('input', this.model);
        },
        deep: true
      },
    }
  }
</script>

<!--============== How to use =======================================

    <ijcore-compare-number v-model="object"></ijcore-compare-number>

==================================================================-->

<template>
    <div class="component-compare-number">
      <b-form-group>
        <b-input-group :title="title">
          <template slot="prepend">
            <b-dropdown :text="model.operator" variant="secondary" :no-caret="true" class="app-dropdown-center ij-operator-number">
              <b-dropdown-item
                class="text-center"
                @click="onChangeOperator(operator)"
                v-for="operator in operatorArray" :key="operator.id"> {{operator}} </b-dropdown-item>
            </b-dropdown>
          </template>
          <input
            v-model="model.number"
            @blur="eventHandle($event)"
            @keyup.enter="eventHandle($event)"
            type="number" class="form-control"/>
        </b-input-group>
      </b-form-group>
    </div>
</template>

<style lang="css">
  .component-compare-number .input-group {
    flex-wrap: nowrap;
  }
</style>
<script>
  export default {
    name: 'compare-number',
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
          operator: (this.value && this.value.operator) ? this.value.operator : '>=',
          number: (this.value && this.value.number) ? this.value.number : ''
        },
        operatorArray: ['=', '>=', '<=', '>', '<'],
      }
    },
    created() {},
    mounted() {},
    components: {},
    methods: {
      onChangeOperator(operator){
        this.model.operator = operator;
      },
      eventHandle(evt) {
        this.$emit(evt.type + '-compare-input', evt);
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

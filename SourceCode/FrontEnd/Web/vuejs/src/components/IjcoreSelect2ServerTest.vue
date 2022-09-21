<template>

  <!--  ============================= how to use ==================================-->

  <!--  <ijcore-select2-server-->
  <!--          v-model="roleId"-->
  <!--          url="http://dev.ijtask.com:7777/task/api/dataflow/test"-->
  <!--          id-name="UserID"-->
  <!--          :delay="1000"-->
  <!--          text-name="FullName"></ijcore-select2-server>-->

  <!--  ============================= end to use ==================================-->

  <div :title="title" class="component-select2-server">
    <select2 v-model="value[FieldID]" :settings="select2Settings" @select="onSelected($event)"/>
  </div>
</template>

<style lang="css">
.select2-results__option span {
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.select2-selection__choice {
  max-width: 125px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.select2-results__option.loading-results {
  display: none;
}
</style>

<script>
import Select2 from 'v-select2-component';
import {TokenService} from "@/services/storage.service";
export default {
  name: 'ijcore-select2-server',
  props: {
    value: [String, Array, Object, Number],
    FieldNo: [String, Number],
    FieldName: [String],
    FieldID: [String, Number],
    FieldUpdate: [Object, String, Array],
    FieldWhere: [Object, String, Array],
    FieldSelect: [Object, String, Array],
    extraParams: [String, Array, Object, Number],
    FieldType: {
      type: Number,
      default: 1
    },

    placeholder: {
      type: String,
      default: ''
    },
    url: {
      type: String,
      default: ''
    },
    multiple: {
      type: Boolean,
      default: false
    },
    allowClear: {
      type: Boolean,
      default: false
    },
    delay: {
      type: Number,
      default: 600
    },
    method: {
      type: String,
      default: 'post'
    },
    title: {
      type: String,
      default: ''
    }
  },
  components:{
    Select2
  },
  created(){
    this.select2Settings = this.getSelect2Settings({
      url: this.url,
      placeholder: this.placeholder,
      multiple: this.multiple,
      allowClear: this.allowClear,
      delay: this.delay
    });
  },
  mounted(){},
  data () {
    return {
      select2Settings: null,
      itemsSelectedArray: []
    }
  },
  methods: {
    getSelect2Settings(options = {}) {
      let self = this;
      return {
        ajax: {
          headers: {
            Authorization: 'Bearer ' + TokenService.getToken()
          },
          method: this.method,
          dataType: 'json',
          cache: true,
          delay: options.delay,
          url: options.url,
          data: function (params) {
            params.page = params.page || 1;
            return {
              term: params.term, // search term
              extraParams: this.extraParams,
              page: params.page,
              per_page: 10
            }
          },
          processResults: function processResults(data, params) {
            params.page = params.page || 1;
            let options = [];
            _.forEach(data.data.data, function (value, key) {
              let tmpObj = {};
              tmpObj.id = value[self.FieldID];
              tmpObj.text = value[self.FieldName];
              if (self.FieldType === 2) {
                tmpObj.text = value[self.FieldNo] + ' - ' + value[self.FieldName];
              }
              if (value[self.FieldID]) {}tmpObj[self.FieldID] = value[self.FieldID];
              if (value[self.FieldNo]) tmpObj[self.FieldNo] = value[self.FieldNo];
              if (value[self.FieldName]) tmpObj[self.FieldName] = value[self.FieldName];
              if (self.FieldUpdate && self.FieldUpdate.length) {
                _.forEach(self.FieldUpdate, function (field, key) {
                  tmpObj[field] = value[field];
                });
              }
              options.push(tmpObj);
            });
            return {
              results: options,
              pagination: {
                more: params.page * data.data.per_page < data.data.total
              }
            };
          },
        },
        allowClear: options.allowClear,
        multiple: options.multiple || false,
        placeholder: options.placeholder,
        cache: true,
        templateResult: function (state) {
          if (!state.id) {
            return state.text;
          }
          let $state = $('<span title="' + state.text + '">' + state.text + '</span>');
          return $state;
        }
      };
    },
    onSelected(params){
      let self = this;
      if (this.title !== '') {
        $('.select2-selection__rendered').hover(function () {
          $(this).removeAttr('title');
        });
      }
      if (this.FieldID) this.value[this.FieldID] = params[this.FieldID];
      if (this.FieldNo) this.value[this.FieldNo] = params[this.FieldNo];
      if (this.FieldName) this.value[this.FieldName] = params[this.FieldName];
      if (this.FieldUpdate) {
        _.forEach(this.FieldUpdate, function (field, key) {
          self.value[field] = params[field];
        });
      }
    },
  },
  watch: {
    value(){}
  },
  destroyed: function () {}
}
</script>

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
    <select2 v-model="myValue" :settings="select2Settings" @select="onSelected($event)"/>
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
      extraParams: [String, Array, Object, Number],
      idName: {
        type: String,
        default: ''
      },
      textName: {
        type: String,
        default: ''
      },
      fieldsName: {
        type: Array,
        default(){
          return [];
        }
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
        myValue: this.value,
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
                tmpObj.id = value[self.idName];
                tmpObj.text = value[self.textName];
                if (self.fieldsName.length) {
                  _.forEach(self.fieldsName, function (field, key) {
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

        let itemSelected = _.find(this.itemsSelectedArray, [this.idName, Number(params.id)]);
        if (!itemSelected) {
          let tmpObj = {};
          tmpObj[this.idName] = params.id;
          tmpObj[this.textName] = params.text;
          if (self.fieldsName.length) {
            _.forEach(self.fieldsName, function (field, key) {
              tmpObj[field] = value[field];
            });
          }
          this.itemsSelectedArray.push(tmpObj);
        }
      },
    },
    watch: {
      myValue(){
        this.$emit('input', this.myValue);
        this.$emit('update:item-selected', this.itemsSelectedArray);
      },
      value(){
        if (!this.value) {
          this.myValue = null;
          this.title = '';
          this.$forceUpdate();
        }
      }
    },
    destroyed: function () {}
  }
</script>

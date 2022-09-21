<template>
  <select class="form-control"></select>
</template>

<style lang="css"></style>
<script>
  export default {
    props: ['options', 'value', 'settings', 'FieldID', 'FieldNo', 'FieldName' ,'FieldUpdate'],
    data () {
      return {}
    },
    mounted: function () {
      let self = this;
      let vm = this;
      $(this.$el)
      // init select2
        .select2(this.setConfig())
        .val(this.value)
        .trigger('change')
        // emit event on change.
        .on('change', function () {
          let value = this.value;
          let objSelected = _.find(self.options, function (o) {
            return o.id == value;
          });
          if (objSelected) {
            if (self.FieldUpdate) {
              _.forEach(self.FieldUpdate, function (field, key) {
                if (self.value[field]) self.value[field] = objSelected[field];
              });
            }
            if (self.FieldID) self.value[self.FieldID] = objSelected.id;
            if (self.FieldNo && objSelected.no) self.value[self.FieldNo] = objSelected.no;
            if (self.FieldNo && objSelected[self.FieldNo]) self.value[self.FieldNo] = objSelected[self.FieldNo];
            if (self.FieldName) self.value[self.FieldName] = objSelected.text;
          }
          vm.$emit('input', self.value);
        })
        .on('change change.select2 select2:closing select2:close select2:opening select2:open select2:selecting select2:select select2:unselecting select2:unselect select2:clearing select2:clear', function (e) {
          vm.$emit(e.type, e);
        });
    },
    methods: {
      setConfig(){
        let config = {
          data: this.options
        };
        _.forEach(this.settings, function (value, key) {
          config[key] = value;
        });
        return config;
      }
    },
    watch: {
      value: function (value) {
        // update value
        $(this.$el).val(value).trigger('change');
      },
      options: function (options) {
        // update options
        let config = this.setConfig();
        $(this.$el).select2(config)
      }
    },
    destroyed: function () {
      $(this.$el).off().select2('destroy')
    }
  }
</script>

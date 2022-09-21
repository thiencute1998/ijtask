<!--============== How to use =======================================
    v-model: Number
    options: [{id: null, text: ''}]
    <ijcore-select-dropdown v-model="myModel" :options="options" placeholder="Quy trình"></ijcore-select-dropdown>

==================================================================-->

<template>
  <div>
    <b-dropdown id="dropdownSelect" variant="primary" :text="placeholder" menu-class="m-0 p-0">
      <b-dropdown-header>
        <input type="text" size="sm" v-model="search" @keyup="onSearchItems" class="form-control" style="min-width: 120px"/>
      </b-dropdown-header>
      <vue-perfect-scrollbar class="ijcore-perfect-scrollbar" :settings="$store.state.psSettings">
        <b-dropdown-item
                v-for="item in itemsArray"
                @click="handleSelectedItem(item)"
                :key="item.id">
          {{item.text}}
        </b-dropdown-item>
      </vue-perfect-scrollbar>
    </b-dropdown>
  </div>
</template>

<style lang="css"></style>
<script>
  export default {
    props: ['options', 'value', 'placeholder'],
    data () {
      return {
        search: '',
        itemsArray: this.options
      }
    },
    components: {},
    mounted: function () {
      let self = this;
      _.forEach(this.itemsArray, function (item, key) {
        let label = __.cleanAccents(item.text);
        let slug = __.stringToSlug(label);
        self.itemsArray[key].label = label;
        self.itemsArray[key].slug = slug;
      });
    },
    methods: {
      onSearchItems() {
        if (this.search) {
          this.itemsArray = _.filter(this.options, _.flow(
                  _.identity,
                  _.values,
                  _.join,
                  _.toLower,
                  _.partialRight(_.includes, this.search)
          ));
        } else {
          this.itemsArray = this.options;
        }
      },
      handleSelectedItem(item){
        this.$emit('input', item.id);
        this.$emit('selected', item);
      }
    },
    watch: {
      'options': {
        handler: function (after, before) {
          // Changes detected. Do work...
          this.itemsArray = this.options;
        },
        deep: true
      }

    },
    destroyed: function () {}
  }
</script>

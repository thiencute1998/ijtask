<template>
    <div class="ijtask ijtask-button-dropdown-menu component-button-dropdown-menu" :class="[(dropdownHeaderText || dropdownHeaderSearch) ? 'show-dropdown-header' : '']">
      <b-dropdown :right="right" :split="split" :text="text" :variant="variant" @click="handleClickButton">
        <template v-slot:button-content>
          <div class="d-inline-block">
            <i :class="icon + ' mr-1'" v-if="icon"></i>
            <span>{{text}}</span>
          </div>
        </template>
        <b-dropdown-header v-if="dropdownHeaderText || dropdownHeaderSearch">
          <span v-if="dropdownHeaderText">{{dropdownHeaderText}}</span>
          <input v-if="dropdownHeaderSearch" type="text" size="sm" @keyup="onSearchItems" v-model="search" class="form-control"/>
        </b-dropdown-header>
        <b-dropdown-item
          v-for="(item, key) in itemsArray"
          @click="handleSelectedItem(item)"
          :active="selectedID === item.id"
          :key="item.id">{{item.text}}</b-dropdown-item>
      </b-dropdown>
    </div>
</template>

<script>
  export default {
    name: 'ijtask-button-dropdown-menu',
    components: {},
    data () {
        return {
          itemsArray: this.dropdownMenu,
          search: '',
          selectedID: this.value
        }
    },
    props:{
      value: [Array, Object, String, Number],
      text: [String],
      variant: [String],
      icon: [String],
      dropdownMenu: [Array, Object],
      dropdownHeaderText: [String],
      dropdownHeaderSearch: {
        type: Boolean,
        default: false
      },
      split: {
        type: Boolean,
        default: false
      },
      right: {
        type: Boolean,
        default: false
      }
    },
    mounted() {
      let self = this;
      _.forEach(this.itemsArray, function (item, key) {
        let label = __.cleanAccents(item.text);
        let slug = __.stringToSlug(label);
        self.itemsArray[key].label = label;
        self.itemsArray[key].slug = slug;
      });
    },
    methods:{
      onSearchItems() {
        if (this.search) {
          this.itemsArray = _.filter(this.dropdownMenu, _.flow(
            _.identity,
            _.values,
            _.join,
            _.toLower,
            _.partialRight(_.includes, this.search)
          ));
        } else {
          this.itemsArray = this.dropdownMenu;
        }
      },
      handleSelectedItem(item){
        this.selectedID = item.id;
        this.$emit('on:selected', item);
      },
      handleClickButton(){
        let itemSelected = _.find(this.dropdownMenu, ['id', this.selectedID]);
        this.$emit('on:click-button', itemSelected);
      }
    },
    watch: {
      'dropdownMenu': {
        handler: function (after, before) {
          // Changes detected. Do work...
          this.itemsArray = this.dropdownMenu;
        },
        deep: true
      },
      selectedID(){
        this.$emit('input', this.selectedID);
      }
    }
  }
</script>
<style type="text/css">
  .component-button-dropdown-menu .dropdown-menu {
    min-width: 6rem;
  }
  .component-button-dropdown-menu.show-dropdown-header .dropdown-menu {
    padding-top: 0;
  }
</style>

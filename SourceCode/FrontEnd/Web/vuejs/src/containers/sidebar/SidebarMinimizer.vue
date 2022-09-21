<template>
  <div class="sidebar-minimizer" @click="onClick()">
    <input type="text" v-model="search" @click="onClickSearch" @input="onSearch">
<!--    <button class="sidebar-minimizer" type="button"></button>-->
  </div>
</template>
<style scoped>
  .sidebar-minimizer {
    display: flex;
    align-items: center;
  }
  .sidebar-minimizer input {
    margin-left: 5px;
    border: none;
    border-radius: 2px;
    padding-left: 5px
  }
  .brand-minimized .sidebar-minimizer input{
    display: none;
  }
</style>
<script>
// import { togglePs } from '../../mixins/togglePs'
import { togglePs } from '../mixins/togglePs'

export default {
  name: 'sidebar-minimizer',
  mixins: [ togglePs ],
  data() {
    return {
      search: ''
    }
  },
  mounted: function () {
    const isMinimized = document.body.classList.contains('sidebar-minimized')
    this.togglePs(!isMinimized)
  },
  methods: {
    onClick () {
      this.sidebarMinimize();
      this.brandMinimize();
    },
    onClickSearch(e){
      e.preventDefault();
      e.stopPropagation();
    },
    sidebarMinimize () {
      const isMinimized = document.body.classList.toggle('sidebar-minimized')
      this.$emit('cui-sidebar-minimize', isMinimized)
      this.togglePs(!isMinimized)
    },
    onSearch(){
      this.$emit('on-search-nav', this.search);
    },
    brandMinimize () {
      document.body.classList.toggle('brand-minimized')
    }
  }
}
</script>

<template>
  <li :class="classList" @click="hideMobile">
    <slot></slot>
  </li>
</template>

<script>
// import { hideMobile } from '../mixins/hideMobile'

export default {
  name: 'sidebar-nav-item',
  // mixins: [ hideMobile ],
  props: {
    classes: {
      type: [String, Array, Object],
      default: ''
    },
    moduleNavTop: {
      type: String,
      default: 'DEFAULT'
    }
  },
  computed: {
    classList () {
      return [
        'nav-item',
        ...this.itemClasses
      ]
    },
    itemClasses () {
      const classes = this.classes;
      return !classes ? [] : typeof classes === 'string' || classes instanceof String ? classes.split(' ') : Array.isArray(classes) ? classes : Object.keys(classes).filter(i => classes[i]);
    }
  },
  methods: {
    hideMobile(){
      if (document.body.classList.contains('sidebar-show')) {
        document.body.classList.toggle('sidebar-show')
      }
      // =======================
      this.$store.commit('moduleNavTop', this.moduleNavTop);
    }
  }
}
</script>

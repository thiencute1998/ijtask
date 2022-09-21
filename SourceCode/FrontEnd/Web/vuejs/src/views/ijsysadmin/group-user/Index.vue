<template>
  <div class="animated fadeIn">
    <vue-perfect-scrollbar class="scroll-area" :settings="$store.state.psSettings">
      <ijcore-flowchart :diagram-config="diagramConfig"></ijcore-flowchart>
    </vue-perfect-scrollbar>
  </div>
</template>

<script>
  // import flowchart from 'flowchart.js';
  import IjcoreFlowchart from '@/components/IjcoreFlowchart';
  export default {
    name: 'sysadmin-group-user-index',
    data() {
      return {
        diagramConfig: {

        }
      }
    },
    components: {
      IjcoreFlowchart
    },
    beforeCreate(){},
    mounted(){},
    methods: {
    },
    watch: {

    }
  }
</script>

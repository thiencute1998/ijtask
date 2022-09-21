<template>
  <div class="animated fadeIn">
    <div class="main-entry">
      <div class="main-header">
        <div class="main-header-padding">
          <b-row>
            <b-col class="col-md-12">
              <div class="main-header-item main-header-name">
                <span class="text-capitalize"><i class="fa fa-line-chart mr-2"></i> Người dùng</span>
              </div>
            </b-col>
            <b-col class="col-md-12">
              <div class="main-header-item">
                <div class="main-header-item main-header-icons">
                  <b-button-group id="main-header-views" class="main-header-views">
                    <b-button id="tooltip-view-list" title="Danh sách người dùng" @click="$router.push({name: 'sysadmin-user-list'})" class="main-header-view"><i class="fa fa-list"></i></b-button>
                  </b-button-group>
                  <div class="main-header-collapse ml-2" id="main-header-collapse">
                    <sidebar-toggle class="d-md-down-none btn" display="lg" :defaultOpen=true />
                  </div>
                </div>
              </div>
            </b-col>
          </b-row>
        </div>
      </div>
      <div class="main-body main-body-view-list">
        <ijcore-flowchart-admin></ijcore-flowchart-admin>
      </div>
      <div class="main-footer"></div>
    </div>

  </div>
</template>

<script>

  import IjcoreFlowchartAdmin from '@/components/IjcoreFlowchartAdmin';
  export default {
    name: 'sysadmin-user-index',
    data() {
      return {
        diagramConfig: {}
      }
    },
    components: {
      IjcoreFlowchartAdmin
    },
    beforeCreate(){},
    mounted(){
    },
    methods: {
    },
    watch: {

    }
  }
</script>

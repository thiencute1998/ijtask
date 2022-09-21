<template>
  <div class="animated">
    <div class="row mt-5">
      <div class="col-4 m-auto">
        <ijcore-select2-server2
          v-model="model"
          :url="$store.state.appRootApi + '/listing/api/common/list2'"
          table="employee"
          field-name="EmployeeName"
          field-i-d="EmployeeID"
          field-no="EmployeeNo"
          :field-type="2"
          :dropdown-with="400"
          :get-all="true"
          placeholder="Chọn nhân viên"
          :allowClear="false"
          :settings="{maximumSelectionSize: 1}"
          :multiple="true">
        </ijcore-select2-server2>
      </div>

      <div class="col-4 m-auto">
        <ijcore-select2-server2
          v-model="model2"
          :url="$store.state.appRootApi + '/listing/api/common/list2'"
          table="employee"
          field-name="EmployeeName"
          field-i-d="EmployeeID"
          field-no="EmployeeNo"
          :field-type="1"
          :get-all="false"
          :client-d-b="true"
          placeholder="Chọn nhân viên"
          :allowClear="false"
          :settings="{maximumSelectionSize: 1}"
          :multiple="true">
        </ijcore-select2-server2>
      </div>

      <div class="col-4 m-auto">
        <ijcore-select2-server2
          v-model="model3"
          :url="$store.state.appRootApi + '/listing/api/common/list2'"
          table="employee"
          field-name="EmployeeName"
          field-i-d="EmployeeID"
          field-no="EmployeeNo"
          :field-type="1"
          :get-all="false"
          placeholder="Chọn nhân viên"
          :allowClear="true"
          :settings="{maximumSelectionSize: 1}"
          :multiple="false"
          :delay="200">
        </ijcore-select2-server2>
      </div>

    </div>
  </div>
</template>
<style type="text/css">
</style>
<script>
  import IjcoreSelect2Server2 from "../../components/IjcoreSelect2Server2";
  import IjcoreSelect2ServerTest from "../../components/IjcoreSelect2ServerTest";
  export default {
    name: 'Test',
    created(){},
    data() {
      return {
        model: {
          EmployeeID: 33,
          EmployeeNo: '01',
          EmployeeName: 'Hà Thị lanh'
        },
        model2: {
          EmployeeID: null
        },
        model3: {
          EmployeeID: 79,
          EmployeeNo: '79',
          EmployeeName: 'Nguyễn Thị Khánh',
        },
      }
    },
    components:{
      IjcoreSelect2Server2,
      IjcoreSelect2ServerTest
    },
    mounted() {
    },

    methods: {},
    watch:{
      model: {
        handler(val){
            // do stuff
          console.log(this.model);
        },
        deep: true
      },
      model3: {
        handler(val){
          // do stuff
          console.log(this.model3);
        },
        deep: true
      }
    }
  }
</script>

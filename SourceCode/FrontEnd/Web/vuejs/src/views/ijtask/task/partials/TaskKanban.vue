<template>
  <div class="kanban-items">
    <div class="row form-group" style="position: absolute; width: 150%; height: 98%" >
      <div class="col-md-4 kanban-item" >
        <div class="form-group text-align-center header-kanban">
          <h5 class="text-center">{{statusTask[1]}}</h5>
        </div>
        <draggable class="list-group kanban-item-inner body-kanban" :list="list1" group="task-list" >
          <div class="list-group-item mb-1"  v-for="(item, key) in list1">
            <div class="kanban-record d-flex justify-content-between">
              <!--                                <span class="kanban-title">{{item.TaskName}}</span>-->
              <span class="kanban-title">{{getTaskName(item)}}</span>
            </div>
            <div class="kanban-record">
              <span>Mã số: </span>
              <span>{{item.TaskNo}}</span>
            </div>
            <div class="kanban-footer d-flex justify-content-between align-items-center">
              <div class="kanban-assign d-inline-flex"><ijcore-users-icon :all-users="taskAssign" filter-name="TaskID" :filter-value="item.TaskID" :number="4"></ijcore-users-icon></div>
              <span class="kanban-percent-completed"><b-badge :variant="(item.PercentCompleted <= 0) ? 'warning' : (item.PercentCompleted > 0 && item.PercentCompleted < 100) ? 'primary' : 'success'">{{item.PercentCompleted}} %</b-badge></span>
            </div>
          </div>
        </draggable>
      </div>
      <div class="col-md-4 kanban-item">
        <div class="form-group text-align-center header-kanban">
          <h5 class="text-center">{{statusTask[2]}}</h5>
        </div>
        <draggable class="list-group kanban-item-inner body-kanban" :list="list2" group="task-list" >
          <div class="list-group-item mb-1 "  v-for="(item, key) in list2">
            <div class="kanban-record d-flex justify-content-between">
              <!--                                <span class="kanban-title">{{item.TaskName}}</span>-->
              <span class="kanban-title">{{getTaskName(item)}}</span>
            </div>
            <div class="kanban-record">
              <span>Mã số: </span>
              <span>{{item.TaskNo}}</span>
            </div>
            <div class="kanban-footer d-flex justify-content-between align-items-center">
              <div class="kanban-assign d-inline-flex"><ijcore-users-icon :all-users="taskAssign" filter-name="TaskID" :filter-value="item.TaskID" :number="4"></ijcore-users-icon></div>
              <span class="kanban-percent-completed"><b-badge :variant="(item.PercentCompleted <= 0) ? 'warning' : (item.PercentCompleted > 0 && item.PercentCompleted < 100) ? 'primary' : 'success'">{{item.PercentCompleted}} %</b-badge></span>
            </div>
          </div>
        </draggable>
      </div>
      <div class="col-md-4 kanban-item">
        <div class="form-group text-align-center header-kanban">
          <h5 class="text-center">{{statusTask[3]}}</h5>
        </div>
        <draggable class="list-group kanban-item-inner body-kanban" :list="list3" group="task-list" >
          <div class="list-group-item mb-1 "  v-for="(item, key) in list3">
            <div class="kanban-record d-flex justify-content-between">
              <!--                                <span class="kanban-title">{{item.TaskName}}</span>-->
              <span class="kanban-title">{{getTaskName(item)}}</span>
            </div>
            <div class="kanban-record">
              <span>Mã số: </span>
              <span>{{item.TaskNo}}</span>
            </div>
            <div class="kanban-footer d-flex justify-content-between align-items-center">
              <div class="kanban-assign d-inline-flex"><ijcore-users-icon :all-users="taskAssign" filter-name="TaskID" :filter-value="item.TaskID" :number="4"></ijcore-users-icon></div>
              <span class="kanban-percent-completed"><b-badge :variant="(item.PercentCompleted <= 0) ? 'warning' : (item.PercentCompleted > 0 && item.PercentCompleted < 100) ? 'primary' : 'success'">{{item.PercentCompleted}} %</b-badge></span>
            </div>
          </div>
        </draggable>
      </div>
      <div class="col-md-4 kanban-item">
        <div class="form-group text-align-center header-kanban">
          <h5 class="text-center">{{statusTask[4]}}</h5>
        </div>
        <draggable class="list-group kanban-item-inner body-kanban" :list="list4" group="task-list" >
          <div class="list-group-item mb-1 "  v-for="(item, key) in list4">
            <div class="kanban-record d-flex justify-content-between">
              <!--                                <span class="kanban-title">{{item.TaskName}}</span>-->
              <span class="kanban-title">{{getTaskName(item)}}</span>
            </div>
            <div class="kanban-record">
              <span>Mã số: </span>
              <span>{{item.TaskNo}}</span>
            </div>
            <div class="kanban-footer d-flex justify-content-between align-items-center">
              <div class="kanban-assign d-inline-flex"><ijcore-users-icon :all-users="taskAssign" filter-name="TaskID" :filter-value="item.TaskID" :number="4"></ijcore-users-icon></div>
              <span class="kanban-percent-completed"><b-badge :variant="(item.PercentCompleted <= 0) ? 'warning' : (item.PercentCompleted > 0 && item.PercentCompleted < 100) ? 'primary' : 'success'">{{item.PercentCompleted}} %</b-badge></span>
            </div>
          </div>
        </draggable>
      </div>
      <div class="col-md-4 kanban-item" >
        <div class="form-group text-align-center header-kanban">
          <h5 class="text-center">{{statusTask[5]}}</h5>
        </div>
        <draggable class="list-group kanban-item-inner body-kanban" :list="list5" group="task-list" >
          <div class="list-group-item mb-1 "  v-for="(item, key) in list5">
            <div class="kanban-record d-flex justify-content-between">
              <!--                                <span class="kanban-title">{{item.TaskName}}</span>-->
              <span class="kanban-title">{{getTaskName(item)}}</span>
            </div>
            <div class="kanban-record">
              <span>Mã số: </span>
              <span>{{item.TaskNo}}</span>
            </div>
            <div class="kanban-footer d-flex justify-content-between align-items-center">
              <div class="kanban-assign d-inline-flex"><ijcore-users-icon :all-users="taskAssign" filter-name="TaskID" :filter-value="item.TaskID" :number="4"></ijcore-users-icon></div>
              <span class="kanban-percent-completed"><b-badge :variant="(item.PercentCompleted <= 0) ? 'warning' : (item.PercentCompleted > 0 && item.PercentCompleted < 100) ? 'primary' : 'success'">{{item.PercentCompleted}} %</b-badge></span>
            </div>
          </div>
        </draggable>
      </div>
      <div class="col-md-4 kanban-item">
        <div class="form-group text-align-center header-kanban">
          <h5 class="text-center">{{statusTask[6]}}</h5>
        </div>
        <draggable class="list-group kanban-item-inner body-kanban" :list="list6" group="task-list" >
          <div class="list-group-item mb-1 "  v-for="(item, key) in list6">
            <div class="kanban-record d-flex justify-content-between">
              <!--                                <span class="kanban-title">{{item.TaskName}}</span>-->
              <span class="kanban-title">{{getTaskName(item)}}</span>
            </div>
            <div class="kanban-record">
              <span>Mã số: </span>
              <span>{{item.TaskNo}}</span>
            </div>
            <div class="kanban-footer d-flex justify-content-between align-items-center">
              <div class="kanban-assign d-inline-flex"><ijcore-users-icon :all-users="taskAssign" filter-name="TaskID" :filter-value="item.TaskID" :number="4"></ijcore-users-icon></div>
              <span class="kanban-percent-completed"><b-badge :variant="(item.PercentCompleted <= 0) ? 'warning' : (item.PercentCompleted > 0 && item.PercentCompleted < 100) ? 'primary' : 'success'">{{item.PercentCompleted}} %</b-badge></span>
            </div>
          </div>
        </draggable>
      </div>
    </div>


<!--     ====================================== Old kaban ==============================================================-->
<!--    <div class="kanban-item col-lg-6 col-md-8 col-sm-12 col-24" v-for="(item, key) in value">-->
<!--      <div class="kanban-item-inner" @click="$_lists_onFieldClicked(item)">-->
<!--        <div class="kanban-record d-flex justify-content-between">-->
<!--          &lt;!&ndash;                                <span class="kanban-title">{{item.TaskName}}</span>&ndash;&gt;-->
<!--          <span class="kanban-title">{{getTaskName(item)}}</span>-->
<!--        </div>-->
<!--        <div class="kanban-record">-->
<!--          <span>Mã số</span>-->
<!--          <span>{{item.TaskNo}}</span>-->
<!--        </div>-->
<!--        <div class="kanban-record">-->
<!--          <span class="kanban-priority">Ưu tiên</span>-->
<!--          <span>{{priority[item.priority]}}</span>-->
<!--        </div>-->
<!--        <div class="kanban-record">-->
<!--          <span class="kanban-create-date">Ngày tạo</span>-->
<!--          <span>{{item.CreateDate | convertServerDateToClientDate}}</span>-->
<!--        </div>-->
<!--        <div class="kanban-record">-->
<!--          <span class="kanban-start-date">Ngày bắt đầu</span>-->
<!--          <span>{{item.StartDate | convertServerDateToClientDate}}</span>-->
<!--        </div>-->
<!--        <div class="kanban-record">-->
<!--          <span class="kanban-due-date">Hạn hoàn thành</span>-->
<!--          <span>{{item.DueDate | convertServerDateToClientDate}}</span>-->
<!--        </div>-->
<!--        <div class="kanban-record mb-2">-->
<!--          <span class="kanban-status" v-if="item.StatusDescription">Trạng thái</span>-->
<!--          <span>{{item.StatusDescription}}</span>-->
<!--        </div>-->
<!--        <div class="kanban-footer d-flex justify-content-between align-items-center">-->
<!--          <div class="kanban-assign d-inline-flex"><ijcore-users-icon :all-users="taskAssign" filter-name="TaskID" :filter-value="item.TaskID" :number="4"></ijcore-users-icon></div>-->
<!--          <span class="kanban-percent-completed"><b-badge :variant="(item.PercentCompleted <= 0) ? 'warning' : (item.PercentCompleted > 0 && item.PercentCompleted < 100) ? 'primary' : 'success'">{{item.PercentCompleted}} %</b-badge></span>-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->
  </div>
</template>

<script>
import draggable from "vuedraggable";
import mixinLists from '@/mixins/lists';
import Swal from 'sweetalert2';
import 'sweetalert2/src/sweetalert2.scss';
import moment from 'moment';
import IjcoreUsersIcon from "@/components/IjcoreUsersIcon";
export default {
  name: "TaskKanban",
  mixins: [mixinLists],
  props:{
    value: [Array, Object],
    priority: [Array, Object],
    taskAssign: [Array, Object],
  },
  components: {
    draggable,
    IjcoreUsersIcon,
  },
  mounted() {
    this.sortTask(this.value);
  },
  data() {
    return {
      list1: [],
      list2: [],
      list3: [],
      list4: [],
      list5: [],
      list6: [],
      statusTask: {
        1: 'Chưa thực hiện',
        2: 'Đang chờ',
        3: 'Đang thực hiện',
        4: 'Tạm dừng',
        5: 'Hủy bỏ',
        6: 'Đã hoàn thành'
      }
    };
  },
  methods: {
    getTaskName(task){
      let name = '';
      if (task.Type === 2 && task.ParentName) {
        name = task.TaskName + ' (' + task.ParentName + ')';
      } else {
        name = task.TaskName;
      }
      name = __.stripHtml(name);
      return name;
    },
    sortTask(tasks){
      let self = this;
      _.forEach(tasks, function (val, key){
        if(val.Status === 1){
          self.list1.push(val);
        }
        if(val.Status === 2){
          self.list2.push(val);
        }
        if(val.Status === 3){
          self.list3.push(val);
        }
        if(val.Status === 4){
          self.list4.push(val);
        }
        if(val.Status === 5){
          self.list5.push(val);
        }
        if(val.Status === 6){
          self.list6.push(val);
        }
      });
    },
  },
  watch:{
    'value'(){
      this.list1 = [];
      this.list2 = [];
      this.list3 = [];
      this.list4 = [];
      this.list5 = [];
      this.list6 = [];
      this.sortTask(this.value);
    }
  }
}
</script>

<style scoped>
                                                 /*scroll-Barr*/
/* width */
::-webkit-scrollbar {
  width: 5px;
}

/* Track */
::-webkit-scrollbar-track {
  box-shadow: inset 0 0 5px #f9f9f9;
  border-radius: 10px;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 10px;
}

/* Handle on hover */


.body-kanban {
  overflow-y: auto;
}
.content-body-kanban .kanban-items .kanban-item{
  height: 90%;
}
</style>

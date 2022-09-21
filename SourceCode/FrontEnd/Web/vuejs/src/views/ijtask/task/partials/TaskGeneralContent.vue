<template>
    <div>
      <div class="form-group row mr-bottom-5">
        <label class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên công việc
          <i style="cursor: pointer;" :style="{color: (value.DoneNowType !== 0) ? '#00a2e8' : '#f3d2d2'}"
             class="fa fa-bullhorn" title="Là thông báo"></i>
        </label>
        <div class="col-lg-14 col-md-14 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
          {{value.TaskName | perView(per, 'TaskName')}}
        </div>
        <div class="col-lg-6 col-md-6 col-sm-8 col-24 d-flex app-object-code justify-content-end">
          <span>Mã số</span>
          {{value.TaskNo | perView(per, 'TaskNo')}}
        </div>
      </div>

      <div class="form-group row mr-bottom-5" v-if="isDetail">
        <div class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Là con của</div>
        <div class="col-lg-16 col-md-14 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
          {{value.ParentName | perView(per, 'ParentName')}}
        </div>
        <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex app-object-code">
          <span>Mã số</span>
          {{value.ParentNo | perView(per, 'ParentNo')}}
        </div>
      </div>

      <div class="form-group row mr-bottom-5" v-if="isDetail && value.DoneNowType !== 0">
        <label class="col-md-4 m-0" title="Loại công việc hoàn thành ngay">Loại công việc HT ngay</label>
        <label class="col-md-20 m-0" v-if="value.DoneNowType === 1">Thông báo</label>
        <label class="col-md-20 m-0" v-if="value.DoneNowType === 2">Nhắc nhở</label>
      </div>


      <div class="form-group row mr-bottom-5">
          <label class="col-md-4 m-0">Mức độ ưu tiên</label>
          <label class="col-md-4 m-0">{{value.PriorityName | perView(per, 'PriorityName')}}</label>
          <label class="col-md-4 m-0">Quyền truy cập</label>
          <label class="col-md-4 m-0">{{value.AccessTypeName | perView(per, 'AccessTypeName')}}</label>
          <label class="col-md-8 m-0">{{value.CompanyName | perView(per, 'CompanyName')}}</label>
      </div>

      <div class="form-group row mr-bottom-5">
        <label class="col-md-4 m-0">Mô tả</label>
        <label class="col-md-20 m-0" style="white-space: pre-wrap;">{{value.TaskDescription | perView(per, 'TaskDescription')}}</label>
      </div>
      <div class="form-group row mr-bottom-5" v-if="isDetail">
        <label class="col-md-4 m-0">Đơn vị tính</label>
        <label class="col-md-8 m-0">{{value.UomName | perView(per, 'UomName')}}</label>
        <label class="col-md-4 m-0">Kiểu lịch</label>
        <label class="col-md-8 m-0">{{value.CalendarName | perView(per, 'CalendarName')}}</label>
      </div>
      <div class="form-group row mr-bottom-5">
        <label class="col-md-4 m-0">Ngày bắt đầu</label>
        <label class="col-md-8 m-0">{{value.StartDate | perView(per, 'StartDate')}}</label>
        <label class="col-md-4 m-0">Hạn hoàn thành</label>
        <label class="col-md-8 m-0">{{value.DueDate | perView(per, 'DueDate')}}</label>
      </div>
      <div class="form-group row mr-bottom-5" v-if="isDetail">
        <label class="col-md-4 m-0">Số giờ ước thực hiện</label>
        <label class="col-md-8 m-0">{{value.Duration | perView(per, 'Duration')}}</label>
        <label class="col-md-4 m-0">KL ước thực hiện</label>
        <label class="col-md-8 m-0">{{value.EstimatedQuantity | perView(per, 'EstimatedQuantity') | convertNumberToText}}</label>
      </div>
      <div class="form-group row mr-bottom-5">
        <label class="col-md-4 m-0">Loại trạng thái</label>
        <label class="col-md-8 m-0">{{value.StatusName}}</label>
        <label class="col-md-4 m-0">Trạng thái</label>
        <label class="col-md-8 m-0">{{value.StatusDescription | perView(per, 'StatusName')}}</label>
      </div>
      <div class="form-group row mr-bottom-5" v-if="isDetail">
        <label class="col-md-4 m-0">Loại công việc</label>
        <div class="col-md-20">
          <span v-for="(TaskCate, key) in value.TaskCate">
            {{TaskCate.CateName}}: {{TaskCate.Description}} <span v-if="key < (value.TaskCate.length - 1)">,</span>
          </span>
        </div>
      </div>
      <div class="form-group row mr-bottom-5">
        <label class="col-md-4 m-0">Nhóm công việc</label>
        <label class="col-md-4 m-0" v-if="value.Type === 1">Độc lập</label>
        <label class="col-md-4 m-0" v-if="value.Type === 2">Quy trình</label>
      </div>
    </div>

</template>

<script>
    import ApiService from '@/services/api.service';
    import mixinLists from '@/mixins/lists';
    export default {
        name: 'TaskGeneralContent',
        mixins: [mixinLists],
        components: {
        },
        computed: {
          rows() {
            return this.totalRows
          },
        },
        data () {
          return {
            listtable: [
            ],
            tableName: '',
            search:'',
            lenghNo: 0,
          }
        },
        created() {
        },
        mounted() {
        },
        methods: {
            formatDate(data){
                data = data.split(' ');
                data = data[0];
                data = data.split('-');
                let dd = data[2];
                let mm = data[1];
                let yyyy = data[0];
                data = dd + '/' + mm + '/' + yyyy;
                return data;
            },
            fetchData(){

            },
            onSaveModal(){
                this.$bvToast.toast('Đã lưu ràng buộc', {
                    variant: 'success',
                    solid: true
                });
            },
            onCancelModal(e){
                this.onResetModal();
                e.preventDefault();
            },
            onHideModal(){
                this.$refs['modal'].hide();
            },
            onToggleModal(){
                let self = this;
                this.currentPage = 1;
                this.$refs['modal'].show();
                this.fetchData();
            },
            onResetModal(){
            },
        },
        watch: {
          currentPage() {
              this.fetchData();
          }
        },
        filters: {

        },
        props: ['title', 'value', 'name', 'api', 'table', 'Task', 'isDetail', 'per'],
    }
</script>
<style>
    .mr-bottom-3{
        margin-bottom: 3px !important;
    }
    .readonly{
        background-color: #fff !important;
    }
    .table th, .table td{
        border-bottom: 1px solid #c8ced3;
    }
    .modal-footer ol,.modal-footer ul,.modal-footer dl{
        margin-bottom: 0;
    }
</style>

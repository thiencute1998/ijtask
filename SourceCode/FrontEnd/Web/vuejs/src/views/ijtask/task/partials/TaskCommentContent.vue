<template>
    <div>
        <div class="form-group row">
            <div class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên công việc</div>
            <div class="col-lg-16 col-md-14 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
                {{this.value.TaskName}}
            </div>
            <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex app-object-code">
                <span>Mã số</span>
                {{this.value.TaskNo}}
            </div>
        </div>

        <div class="form-group row">
            <div class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Là con của</div>
            <div class="col-lg-16 col-md-14 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
                {{this.value.ParentName}}
            </div>
            <div class="col-lg-4 col-md-6 col-sm-8 col-24 d-flex app-object-code">
                <span>Mã số</span>
                {{this.value.ParentNo}}
            </div>
        </div>

        <div class="form-group row">
            <label class="col-md-4 m-0">Loại công việc</label>
            <label class="col-md-20 m-0"></label>
        </div>


        <div class="form-group row">
            <label class="col-md-4 m-0">Mức độ ưu tiên</label>
            <label class="col-md-20 m-0">{{this.value.PriorityName}}</label>
        </div>

        <div class="form-group row">
            <label class="col-md-4 m-0">Mô tả</label>
            <label class="col-md-20 m-0">{{this.value.TaskDescription}}</label>
        </div>
        <div class="form-group row">
            <label class="col-md-4 m-0">Đơn vị tính</label>
            <label class="col-md-8 m-0">{{this.value.UomName}}</label>
            <label class="col-md-4 m-0">Kiểu lịch</label>
            <label class="col-md-8 m-0">{{this.value.CalendarName}}</label>
        </div>
        <div class="form-group row">
            <label class="col-md-4 m-0">Ngày bắt đầu</label>
            <label class="col-md-8 m-0">{{this.value.StartDate}}</label>
            <label class="col-md-4 m-0">Ngày kết thúc</label>
            <label class="col-md-8 m-0">{{this.value.DueDate}}</label>
        </div>
        <div class="form-group row">
            <label class="col-md-4 m-0">Số giờ ước thực hiện</label>
            <label class="col-md-8 m-0">{{this.value.Duration}}</label>
            <label class="col-md-4 m-0">KL ước thực hiện</label>
            <label class="col-md-8 m-0">{{this.value.EstimatedQuantity}}</label>
        </div>
    </div>

</template>

<script>
    import ApiService from '@/services/api.service';
    import mixinLists from '@/mixins/lists';
    export default {
        name: 'TaskCommentContent',
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
        props: {
            value: {
                type: Object,
                default() {
                    return {
                        ParentID: '',
                    };
                }
            },
            title:{},
            name:{},
            api: {},
            table: {}
        },
    }
</script>
<style>
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

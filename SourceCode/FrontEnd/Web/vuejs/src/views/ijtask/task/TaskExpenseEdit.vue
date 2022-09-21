<template>
    <a @click="onToggleModal()" class="ij-a-icon">
        <i class="fa fa-plus-square-o ij-icon ij-icon-plus" aria-hidden="true" v-if="isNew" title="Thêm"></i>
        <i class="fa fa-external-link ij-icon" aria-hidden="true" v-if="!isNew" title="Chi tiết"></i>
        <b-modal ref="modal" id="modal-form-input-task-expense-edit" size="xl">
            <template slot="modal-title">
                {{this.title}}
            </template>
            <TaskExpenseContent v-model="value" v-if="!isForm" :Task="Task">
            </TaskExpenseContent>
            <TaskExpenseContent v-model="value" :isForm="true" v-if="isForm && isNew" :Task="Task" :isAddNew="true">
            </TaskExpenseContent>
            <TaskExpenseContent v-model="value" :isForm="true" v-if="isForm && !isNew" :Task="Task" :isAddNew="false">
            </TaskExpenseContent>
            <template v-slot:modal-footer>
                <div class="w-100 left">
                    <b-button variant="primary" size="md" class="float-left mr-2" @click="onEdit()" v-if="!isForm"
                    >
                        Sửa
                    </b-button>
                    <b-button variant="primary" size="md" class="float-left mr-2" @click="onUpdate()" v-if="isForm">
                        Lưu
                    </b-button>
                    <b-button variant="primary" size="md" class="float-left mr-2" @click="onHideModal" v-if="isForm">
                        Hủy
                    </b-button>
                    <b-button
                            variant="primary"
                            size="md"
                            class="float-left mr-2"
                            @click="onHideModal()"
                    >
                        Đóng
                    </b-button>
                </div>
            </template>

        </b-modal>
    </a>
</template>

<script>
    import ApiService from '@/services/api.service';
    import mixinLists from '@/mixins/lists';
    import TaskExpenseContent from "./partials/TaskExpenseContent";
    export default {
        name: 'TaskExpenseEdit',
        mixins: [mixinLists],
        components: {
            TaskExpenseContent
        },
        computed: {
            rows() {
                return this.totalRows
            },
        },
        data () {
            return {
                isForm: false,
                listtable: [
                ],
                tableName: '',
                search:'',
                lenghNo: 0,
            }
        },
        created() {
            if(this.isNew){
                this.isForm = true;
            }else{
                this.isForm = false;
            }
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
                this.isForm = false;
            },
            onToggleModal(){
                let self = this;
                this.currentPage = 1;
                this.$refs['modal'].show();
                this.fetchData();
            },
            onResetModal(){
            },
            onEdit(){
                this.isForm = true;
            },
            onUpdate(){
                let self = this;
                const requestData = {
                    method: 'post',
                    url: 'task/api/task/task-expense/'+this.Task.TaskID,
                    data: {
                        TaskExpense: self.value
                    }
                };
                // edit user
                requestData.data.ItemID = this.Task.TaskID;
                ApiService.setHeader();
                ApiService.customRequest(requestData).then((responses) => {
                    let responsesData = responses.data;
                    if (responsesData.status === 1) {
                        if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
                        this.$bvToast.toast('Cập nhật thành công!', {
                            title: 'Thông báo',
                            variant: 'success',
                            solid: true
                        });
                        this.value.master.TransID = responsesData.data.TransID;
                        this.isForm = false;
                        this.isNew = false;
                        this.$emit("transferExpense", responsesData.data.TaskExpenseTrans, responsesData.data.TaskExpenseTransItem);
                    } else {
                      let htmlErrors = __.renderErrorApiHtml(responsesData.data);
                      Swal.fire(
                        'Thông báo',
                        htmlErrors,
                        'error'
                      );
                    }

                  self.onHideModal();
                }, (error) => {
                    console.log(error);
                    Swal.fire(
                        'Thông báo',
                        'Không kết nối được với máy chủ',
                        'error'
                    )
                });
            }
        },
        watch: {
            currentPage() {
                this.fetchData();
            }
        },
        props: {
            value: {},
            title:{},
            name:{},
            api: {},
            Task: {},
            table: {},
            isNew: {}
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
        margin-bottom: 0px;
    }
    #modal-form-input-task-expense-edit .modal-lg .modal-content{
        width: 1024px;
        margin: auto;
    }
    @media (max-width: 1024px){
        #modal-form-input-task-expense-edit .modal-lg {
            max-width: 100%;
        }
        #modal-form-input-task-expense-edit .modal-lg .modal-content{
            width: 90%;
            margin: auto;
        }
    }
    @media (min-width: 992px){
        #modal-form-input-task-expense-edit .modal-lg {
            max-width: 100%;
        }
    }
</style>

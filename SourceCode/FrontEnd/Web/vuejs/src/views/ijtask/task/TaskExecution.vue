<template>
    <a @click="onToggleModal()" class="ij-a-icon" title="Chi tiết">
        <i class="fa fa-external-link ij-icon" aria-hidden="true"></i>
        <b-modal scrollable ref="modal" id="modal-form-input-task-execution" size="xl">
            <template slot="modal-title">
                {{this.title}}
            </template>
            <TaskExecutionContent v-model="value" v-if="!isForm" :Task="Task">
            </TaskExecutionContent>
            <TaskExecutionContent v-model="value" :isForm="true" v-if="isForm" :Task="Task" :TaskStatus="TaskStatus">
            </TaskExecutionContent>
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
    import TaskExecutionContent from "./partials/TaskExecutionContent";
    export default {
        name: 'TaskExecution',
        mixins: [mixinLists],
        components: {
            TaskExecutionContent
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
                this.$store.commit('isLoading', true);
                let self = this;
                const requestData = {
                    method: 'post',
                    url: 'task/api/task/task-execution/'+this.Task.TaskID,
                    data: {
                        TaskExecution: self.value
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
                        this.isForm = false;
                        self.$store.commit('isLoading', false);
                    } else {
                      let htmlErrors = __.renderErrorApiHtml(responsesData.data);
                      Swal.fire(
                        'Thông báo',
                        htmlErrors,
                        'error'
                      );
                      self.$store.commit('isLoading', false);
                    }
                  self.onHideModal();
                }, (error) => {
                    console.log(error);
                    Swal.fire(
                        'Thông báo',
                        'Không kết nối được với máy chủ',
                        'error'
                    )
                    self.$store.commit('isLoading', false);
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
            TaskStatus: {},
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
    #modal-form-input-task-execution .modal-lg .modal-content{
        width: 1024px;
        margin: auto;
    }
    @media (max-width: 1024px){
        #modal-form-input-task-execution .modal-lg {
            max-width: 100%;
        }
        #modal-form-input-task-execution .modal-lg .modal-content{
            width: 90%;
            margin: auto;
        }
    }
    @media (min-width: 992px){
        #modal-form-input-task-execution .modal-lg {
            max-width: 100%;
        }
    }
</style>

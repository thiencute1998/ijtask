import Swal from "sweetalert2";
<template>
    <div class="table-responsive table-responsive-auto">
        <table class="not-border" v-if="!isForm">
            <tbody>
            <tr v-for="(item, key) in value">
                <td class="pr-3">{{item.EmployeeName}}</td>
                <td class="pr-3">{{item.StartDate}}</td>
                <td class="pr-3">{{item.DueDate}}</td>
                <td class="pr-3">{{item.Duration}}</td>
                <td class="pr-3">{{item.Description}}</td>
            </tr>
            </tbody>
        </table>

        <table v-if="isForm" class="table b-table table-sm table-bordered table-editable">
            <tbody>
            <tr v-for="(item, key) in this.value">
                <td class=" EmployeeName">
                    <IjcoreModalListing v-model="value[key]" :title="'nhân viên'"  :api="'/listing/api/common/list'" :table="'employee'">
                    </IjcoreModalListing>
                </td>
                <td class="DateText">
                    <IjcoreDatePicker v-model="value[key].StartDate" :keyValue="key">
                    </IjcoreDatePicker>
                </td>
                <td class="DateText">
                    <IjcoreDatePickerExpense v-model="value[key].DueDate" :keyValue="key">
                    </IjcoreDatePickerExpense>
                </td>
                <td class="NumberHour"><input v-model="value[key].Duration" class="form-control" type="number"/></td>
                <td><input v-model="value[key].Description" class="form-control"/></td>
                <td style="text-align: center;width: 50px;"><i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o" style="font-size: 18px; cursor: pointer;"></i></td>
            </tr>
            </tbody>
        </table>

        <a @click="addLine()" v-if="isForm" class="new-row"><i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới</a>
    </div>

</template>

<script>
    import ApiService from '@/services/api.service';
    import mixinLists from '@/mixins/lists';
    import moment from 'moment';
    import IjcoreModalListing from "../../../../components/IjcoreModalListing";
    import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
    export default {
        name: 'TaskExpenseForm',
        mixins: [mixinLists],
        components: {
            IjcoreDatePicker,
            IjcoreModalListing
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
            clickText: function (event, key) {
                if(this.isForm){
                    event.target.hidden = true;
                    event.target.nextSibling.hidden = false;
                    this.value[key].addnew = true;
                }
            },
            hideInput: function (event, loop, key) {
                let element = event.target;
                if(event.target.value){
                    for (let i = 1; i <= loop; i++){
                        element = element.parentElement;
                    }
                    element.hidden = true;
                    element.previousSibling.hidden = false;
                    this.value[key].addnew = false;
                }
            },
            addLine(){
                this.value.push({
                    EmployeeID: '',
                    EmployeeName: '',
                    StartDate: this.Task.StartDate,
                    FinishDate: this.Task.DueDate,
                    DueDate: this.Task.DueDate,
                    Duration: this.Task.Duration,
                    Description: '',
                    addnew: true,
                });
            },
            updateHour(key){
                if(this.value[key].StartDate && this.value[key].DueDate) {
                    let self = this;
                    let urlApi = '/task/api/task/get-hour';
                    let requestData = {
                        method: 'post',
                        url: urlApi,
                        data: {
                            StartDate: self.value[key].StartDate,
                            DueDate: self.value[key].DueDate,
                            CalendarTypeID: self.Task.CalendarTypeID,
                        },

                    };
                    this.$store.commit('isLoading', true);
                    ApiService.customRequest(requestData).then((response) => {
                        let dataResponse = response.data;

                        if (dataResponse.status === 1) {
                            self.value[key].Duration = dataResponse.data;
                        }
                        self.$store.commit('isLoading', false);
                    }, (error) => {
                      console.log(error);
                      self.$store.commit('isLoading', false);
                      Swal.fire({
                        title: 'Thông báo',
                        text: 'Không kết nối được với máy chủ',
                        confirmButtonText: 'Đóng'
                      });
                    });

                    // scroll to top perfect scroll
                    const container = document.querySelector('.b-table-sticky-header');
                    if (container) container.scrollTop = 0;
                }
            },
            deleteLine(key){
                this.value.splice(key, 1);
            }
        },
        watch: {
            currentPage() {
                this.fetchData();
            }
        },
        props: {
            value:{},
            title:{},
            name:{},
            api: {},
            table: {},
            Task: {},
            isForm: false,
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
    .mx-datepicker{
        display: block !important;
    }
    .EmployeeName{
        width: 200px;
    }
    .DateText{
        width: 100px;
    }
    .NumberHour{
        width: 80px;
    }
</style>

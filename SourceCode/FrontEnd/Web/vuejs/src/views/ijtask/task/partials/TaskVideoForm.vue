import Swal from "sweetalert2";
<template>
    <div class="table-responsive">
        <table v-if="isForm" class="table b-table table-sm table-bordered table-editable">
            <thead>
            <tr class="text-center">
                <th>Tên</th>
                <th>Tài liệu</th>
                <th>Kiểu tệp</th>
                <th>Kích thước</th>
                <th>Ngày cập nhật</th>
                <th>Người cập nhật</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, key) in this.value">
                <td class="NameObject"><IjcoreUploadVideo v-model="value[key]"></IjcoreUploadVideo></td>
                <!--<td><input v-model="value[key].Description" class="form-control" @change="changeDescription(key)"/></td>-->
                <td><input v-model="value[key].DocID" class="form-control"/></td>
                <td><input v-model="value[key].VideoType" class="form-control"/></td>
                <td><input v-model="value[key].VideoSize" class="form-control"/></td>
                <td><input v-model="value[key].DateModified" class="form-control"/></td>
                <td><input v-model="value[key].UserModified" class="form-control"/></td>
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
    import IjcoreDatePickerAssign from "../../../../components/IjcoreDatePickerAssign";
    import IjcoreDateTimePicker from "../../../../components/IjcoreDateTimePicker";
    import IjcoreUploadVideo from "../../../../components/IjcoreUploadVideo";
    import IjcoreUploadMultipleVideo from "../../../../components/IjcoreUploadMultipleVideo";
    export default {
        name: 'TaskVideoForm',
        mixins: [mixinLists],
        components: {
            IjcoreUploadVideo,
            IjcoreUploadMultipleVideo,
            IjcoreDateTimePicker,
            IjcoreDatePickerAssign,
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
            changeDescription(key){
                this.value[key].changeData = 1;
            },
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
                    RequestDate: __.convertDateTime(new Date()),
                    RequestName: '',
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
    .NameObject{
        width: 300px;
    }
    .DateTimeText{
        width: 173px;
    }
    .NumberHour{
        width: 80px;
    }
    .mx-input-wrapper{
        width: 173px !important;
    }
    .mx-datepicker{
        width: 173px !important;
    }
</style>

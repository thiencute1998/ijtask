<template>
    <div class="el-first-modal">
        <div>

            <div class="form-group row align-items-center">
                <div class="col-lg-2 col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Loại</div>
                <div class="col-lg-2">
                    <b-form-select :options="optionType" v-model="value.master.TransType"></b-form-select>
                </div>
                <div class="col-lg-2 col-md-2 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Ngày</div>
                <div class="col-lg-2">
                    <!--<IjcoreDatePicker v-model="value.master.TransDate">-->
                    <!--</IjcoreDatePicker>-->
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-2" style="white-space: nowrap">Mô tả</label>
                <div class="col-lg-22">
                    <textarea v-model="value.master.Comment" class="form-control" rows="1" placeholder="Nhập diễn giải"></textarea>
                </div>
            </div>
        </div>
        <div class="table-responsive">

            <table v-if="isForm" class="table b-table table-sm table-bordered table-editable">
                <tbody>
                <tr v-for="(item, key) in dataList">
                    <td class="EmployeeName">
                        <IjcoreDescriptionModalListing v-model="value.detail[key]" :title="'khoản chi'"  :api="'/listing/api/common/list'" :table="'expense'">
                        </IjcoreDescriptionModalListing>
                    </td>
                    <td><input v-model="value.detail[key].UomID" class="form-control"/></td>
                    <td><input v-model="value.detail[key].Quantity" class="form-control"/></td>
                    <td><input v-model="value.detail[key].UnitPrice" class="form-control"/></td>
                    <td><input v-model="value.detail[key].Amount" class="form-control"/></td>
                    <td><input v-model="value.detail[key].TaxRate" class="form-control"/></td>
                    <td><input v-model="value.detail[key].TaxAmount" class="form-control"/></td>
                    <td class="NumberHour"><input v-model="value.detail[key].Duration" class="form-control" type="number"/></td>
                    <td><input v-model="value.detail[key].Description" class="form-control"/></td>
                    <td style="text-align: center;width: 50px;"><i title="Xóa" @click="deleteLine(key)" class="fa fa-trash-o" style="font-size: 18px; cursor: pointer;"></i></td>
                </tr>
                </tbody>
            </table>

            <a @click="addLine()" v-if="isForm" class="new-row"><i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm mới</a>
        </div>
    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import mixinLists from '@/mixins/lists';
    import moment from 'moment';
    import IjcoreModalListing from "../../../../components/IjcoreModalListing";
    import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
    import Swal from 'sweetalert2';
    import IjcoreDescriptionModalListing from "../../../../components/IjcoreDescriptionModalListing";
    export default {
        name: 'TaskExpenseContentEdit',
        mixins: [mixinLists],
        components: {
          IjcoreDescriptionModalListing,
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
                optionType: [
                    {value: 1, text: "Kế hoạch"},
                    {value: 2, text: "Thực tế"},
                ],
                dataList: {}
            }
        },
        created() {

        },
        mounted() {
            if (this.idParams == 0 || _.isUndefined(this.idParams)) {
                return false;
            }
            let self = this;
            let urlApi = '';
            let requestData = {
                method: 'get',
            };
            // Api edit user
            if(this.idParams){
                urlApi = 'task/api/task/task-expense-edit' + '/' + this.idParams;
                let data = {
                    id: this.idParams
                };
                requestData.data = data;
            }
            requestData.url = urlApi;
            this.$store.commit('isLoading', true);
            ApiService.setHeader();
            ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                self.defaultModel = responsesData;
                if (responsesData.status === 1) {
                    self.Task = responsesData.data.Task;
                    self.Task.StartDate = __.convertDateToString(self.Task.StartDate);
                    self.Task.DueDate = __.convertDateToString(self.Task.DueDate);
                    self.Task.statusHour = 0;
                    self.Task.PriorityOptions = responsesData.data.PriorityOptions;
                    self.Task.AccessTypeOptions = responsesData.data.AccessTypeOptions;
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
                this.dataList.push({
                    ExpenseNo: '',
                    Description: '',
                    UomID: '',
                    Quantity: '',
                    UnitPrice:'',
                    Amount: '',
                    TaxRate: '',
                    TaxAmount: '',
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
                this.value.detail.splice(key, 1);
            }
        },
        watch: {
            currentPage() {
                this.fetchData();
            },
            dataList(){
                this.value.detail = this.dataList;
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
            isAddNew: false,
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
    .NumberHour{
        width: 80px;
    }
</style>

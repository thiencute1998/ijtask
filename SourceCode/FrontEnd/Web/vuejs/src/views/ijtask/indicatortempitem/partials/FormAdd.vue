<template>
  <vue-perfect-scrollbar class="scroll-area" :settings="$store.state.psSettings">
    <div class="container-fluid">
      <b-card>
                <div class="form-group row align-items-center">
                  <div class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên</div>
                  <div class="col-lg-14 col-md-14 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
                    <input v-model="value.IndicatorName" type="text" class="form-control" placeholder="Tên chỉ tiêu" name="IndicatorName"/>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                    <span>Mã số</span>
                    <input type="text" v-model="value.IndicatorNo" class="form-control" placeholder="Mã số"/>
                  </div>
                </div>
                <div class="form-group row align-items-center">
                  <label class="col-md-4 m-0">Mã gốc</label>
                  <div class="col-md-20">
                    <Select2 v-model="value.ParentID" @change="changePR" :options="value.indicatorOption" :settings="{allowClear: true, placeholder: {id: 0, text: 'Chọn chỉ tiêu cha'}}"></Select2>
                  </div>
                </div>
                <div class="form-group row align-items-center">
                  <label class="col-md-4 m-0" >Đơn vị tính</label>
                  <div class="col-md-8">
                    <b-form-select v-model="value.UomID" :options="value.uomOption" id="UomID"></b-form-select>
                  </div>
                  <label class="col-md-4 m-0">Loại tần suất</label>
                  <div class="col-md-8">
                    <b-form-select
                      v-model="value.FrequencyType"
                      :options="[
                                                  {value: null, text: 'Chọn tần suất'},
                                                  {value: 1, text: 'Năm'},
                                                  {value: 2, text: '6 tháng'},
                                                  {value: 3, text: 'Quý'},
                                                  {value: 4, text: 'Tháng'},
                                                  {value: 5, text: 'Tuần'},
                                                  {value: 6, text: 'Ngày'},
                                                  {value: 7, text: 'Vụ việc'},]">
                    </b-form-select>
                  </div>
                </div>
                <div class="form-group row align-items-center">
                  <label class="col-md-4 m-0" >Loại chấm điểm</label>
                  <div class="col-md-8">
                    <b-form-select
                      v-model="value.GradingType"
                      :options="[
                                                  {value: null, text: 'Chọn loại chấm điểm'},
                                                  {value: 1, text: 'Điểm thường'},
                                                  {value: 2, text: 'Điểm thưởng'},
                                                  {value: 3, text: 'Điểm phạt'},]">
                    </b-form-select>
                  </div>
                  <label class="col-md-4 m-0">Trọng số</label>
                  <div class="col-md-8">
                    <input type="text" v-model="value.Rate" class="form-control" placeholder="Trọng số"/>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-md-2" for="Description">Mô tả</label>
                  <div class="col-md-20">
                    <textarea v-model="value.Description" class="form-control" id="Description" rows="3" placeholder="Ghi chú" name="Description"></textarea>
                  </div>
                </div>
      </b-card>
    </div>
  </vue-perfect-scrollbar>
</template>

<script>
  import ApiService from '@/services/api.service';
  import mixinLists from '@/mixins/lists';
  import Select2 from 'v-select2-component';

  const ListRouter = 'task-indicator-tempitem';
  const EditRouter = 'task-indicator-tempitem-edit';
  const ViewRouter = 'task-indicator-tempitem-view';
  const CreateRouter = 'task-indicator-tempitem-create';
  const DetailApi = 'task/api/indicator-tempitem/view';
  const EditApi = 'task/api/indicator-tempitem/edit';
  const CreateApi = 'task/api/indicator-tempitem/create';
  const StoreApi = 'task/api/indicator-tempitem/store';
  const UpdateApi = 'task/api/indicator-tempitem/update';
  const ListApi = 'task/api/indicator-tempitem';

  export default {
    name: 'FormAdd',
    mixins: [mixinLists],

    components: {
      Select2
    },
    computed: {
      rows() {
        return this.totalRows
      },
    },
    data() {
      return {
        isForm: true,
        idParams: this.idParamsProps,
        reqParams: this.reqParamsProps,
        model: {
          TempitemID: null,
          TemplateID: null,
          IndicatorNo: '',
          IndicatorName: '',
          ParentID: null,
          UomID: null,
          UomName: '',
          FrequencyType: null,
          GradingType: null,
          Rate: '',
          Description: '',
          Uom: null,
          uomOption: [],
          indicatorOption: []
        },
        stage: {
          updatedData: false
        }

      }
    },
    created() {
    },
    mounted() {
        // this.fetchData();
    },
    methods: {
      fetchData() {
        let self = this;
        let urlApi = CreateApi;
        let requestData = {
          method: 'get',
          data: {}
        };
        // Api edit user
        if(this.idParams){
          urlApi = EditApi + '/' + this.idParams;
          requestData.data.id = this.idParams;
        }
        requestData.url = urlApi;
        this.$store.commit('isLoading', true);

        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          // copy item
          if (!self.idParams && !_.isEmpty(self.itemCopy)) {
            responsesData.data.data = self.itemCopy.data.data;
          }

          if (responsesData.status === 1) {

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
      handleSubmitForm(){
        let self = this;
        const requestData = {
          method: 'post',
          url: StoreApi,
          data: {
            IndicatorNo: this.model.indicatorNo,
            IndicatorName: this.model.indicatorName,
            ParentID: this.model.parentID,
            FrequencyType: this.model.FrequencyType,
            Inactive: (this.model.inactive) ? 1 : 0,
            UomID: this.model.UomID,
            UomName: this.model.uomName,
            Description: this.model.description
          }
        };

        // edit user
        if (this.idParams) {
          requestData.data.ItemID = this.idParams;
          requestData.url = UpdateApi + '/' + this.idParams;
        }

        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
            self.$router.push({
              name: ViewRouter,
              params: {id: self.idParams, message: 'Bản ghi đã được cập nhật!'}
            });
          } else {
            let htmlErrors = __.renderErrorApiHtml(responsesData.data);
            Swal.fire(
              'Thông báo',
              htmlErrors,
              'error'
            )
          }

          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          Swal.fire(
            'Thông báo',
            'Không kết nối được với máy chủ',
            'error'
          );
          self.$store.commit('isLoading', false);
        });
      },
      changePR() {
        let self = this;
        let urlApi = this.api;
        let requestData = {
          method: 'post',
          url: '/listing/api/common/auto-childtable',
          data: {
            table: 'task_indicator_temp_item',
            ParentID: this.value.ParentID,
            TableNo: 'IndicatorNo',
            TableID: 'TempitemID',
          },

        };

        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {//console.log(response);
          let dataResponse = response.data;
          this.value.IndicatorNo = dataResponse.data;
          self.$store.commit('isLoading', false);
        }, (error) => {
          self.$store.commit('isLoading', false);
          Swal.fire({
            title: 'Thông báo',
            text: 'Không kết nối được với máy chủ',
            confirmButtonText: 'Đóng'
          });
        });
      },

    },
    watch: {
      idParams() {
        this.fetchData();
      },
    },
    props: {
      value: {}
    },
  }
</script>
<style>
</style>

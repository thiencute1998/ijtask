<template>
  <vue-perfect-scrollbar class="scroll-area" :settings="$store.state.psSettings">
    <div class="container-fluid">
      <b-card>
        <div class="form-group row align-items-center">
          <div class="col-lg-4 col-md-4 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-2" style="white-space: nowrap">Tên</div>
          <div class="col-lg-14 col-md-14 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-2 app-object-name">
            {{model.IndicatorName}}
            <input v-model="model.IndicatorName" type="text" id="IndicatorName" class="form-control" placeholder="Tên chỉ tiêu" name="IndicatorName"/>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
            <span>Mã số</span>
            {{model.IndicatorNo}}
          </div>
        </div>
        <div class="form-group row align-items-center">
          <label class="col-md-4 m-0">Mã gốc</label>
          <div class="col-md-20">
            {{model.parentName}}
          </div>
        </div>

        <div class="form-group row align-items-center">
          <label class="col-md-4 m-0" >Đơn vị tính</label>
          <div class="col-md-8">
            {{model.uomName}}
          </div>
          <label class="col-md-4 m-0">Loại tần suất</label>
          <div class="col-md-8">
            {{model.FrequencyType}}
          </div>
        </div>
        <div class="form-group row align-items-center">
          <label class="col-md-4 m-0" >Loại chấm điểm</label>
          <div class="col-md-8">
            {{model.GradingType}}
          </div>
          <label class="col-md-4 m-0">Trọng số</label>
          <div class="col-md-8">
            {{model.Rate}}
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-2" for="Description">Mô tả</label>
          <div class="col-md-20">
            {{model.Description}}
          </div>
        </div>
      </b-card>
    </div>
  </vue-perfect-scrollbar>
</template>

<script>
  import ApiService from '@/services/api.service';
  const ListRouter = 'task-indicator-tempitem';
  const FrequencyType = {
    1: 'Năm',
    2: '6 tháng',
    3: 'Quý',
    4: 'Tháng',
    5: 'Tuần',
    6: 'Ngày',
    7: 'Vụ việc',
  };
    export default {
        name: "FormView",
      data () {
        return {
          idParams: this.idParam,
          reqParams: this.$route.params.req,
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
          },
          defaultModel: {},
          stage: {
            updatedData: false,
            message: (this.$route.params.message) ? this.$route.params.message : ''
          }
        }

      },

      components: {},
      beforeCreate() {
        // this.$router.onReady(() => {
        //   if (!this.$route.params.id) {
        //     this.$router.push({name: ListRouter});
        //   }
        // });
      },
      created(){console.log(this.$route.params.id);
      },
      beforeMount(){},
      mounted() {
        this.fetchData();
        // hiển thị thông báo
        if (this.stage.message && this.stage.message !== '') {
          this.$bvToast.toast(this.stage.message, {
            title: 'Thông báo',
            variant: 'success',
            solid: true
          });
        }
      },
      updated() {
        this.stage.updatedData = true;
      },

      computed: {
        itemNo(){
          if (!this.idParams) return;
          let index = 0;
          index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
          return index;
        },
        itemTotalPerPage(){
          if (!this.idParams) return;
          return this.reqParams.idsArray.length + this.reqParams.perPage * (this.reqParams.currentPage - 1);
        },
        itemTotal(){
          if (!this.idParams) return;
          return this.reqParams.total;

        }
      },
      methods: {
        fetchData() {
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
            urlApi = 'task/api/indicator-tempitem/view/' + this.idParams;
            let data = {
              id: this.idParams
            };
            requestData.data = data;
          }
          requestData.url = urlApi;
          console.log(requestData);
          this.$store.commit('isLoading', true);
          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            let responsesData = responses.data;
            self.defaultModel = responsesData;

            if (responsesData.status === 1) {
              self.model.TemplateID = responsesData.data.data.TemplateID;
              self.model.IndicatorName = responsesData.data.data.IndicatorName;
              self.model.IndicatorNo = responsesData.data.data.IndicatorNo;
              self.model.FrequencyType = (responsesData.data.data.FrequencyType && FrequencyType[responsesData.data.data.FrequencyType]) ? FrequencyType[responsesData.data.data.FrequencyType] : 'Năm';
              self.model.UomID = responsesData.data.data.UomID;
              self.model.Description = responsesData.data.data.Description;
              self.model.ParentName = responsesData.data.ParentName;
              self.model.UomName = responsesData.data.UomName;
            }
            self.$forceUpdate();
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
        onNavigationItem(type) {
          let currentIndex = this.reqParams.idsArray.indexOf(this.idParams);
          let newIndex = (type === 'prev') ? currentIndex - 1 : currentIndex + 1;

          if (newIndex === (this.reqParams.idsArray.length) && (this.reqParams.currentPage != this.reqParams.lastPage)) {
            this.reqParams.currentPage = this.reqParams.currentPage + 1;
            this.getItemIds(type);
          } else if (newIndex < 0 && this.reqParams.currentPage > 1){
            this.reqParams.currentPage = this.reqParams.currentPage - 1;
            this.getItemIds(type);
          }
          else {
            this.idParams = (this.reqParams.idsArray[newIndex]) ? this.reqParams.idsArray[newIndex] : this.idParams;
          }
        },

        onEditClicked(){
          this.$router.push({
            name: EditRouter,
            params: {id: this.idParams, req: this.reqParams}
          });
        },
        onCreateClicked(){
          this.$router.push({name: CreateRouter});
        },
        onBackToList(message = '') {
          if (_.isString(message)) {
            this.$router.push({name: ListRouter, params: {message: message}});
          } else {
            this.$router.push({name: ListRouter});
          }

        },
        handleCopyItem(){
          this.$router.push({name: CreateRouter, params: {itemCopy: this.defaultModel}});
        },
        onEdit(){
          this.isForm = true;
          this.$refs['modal'].show();
          this.$refs['modalview'].hide();
        },
        handleDeleteItem() {
          let self = this;
          let title = 'Bạn có muốn xóa bản ghi?';
          Swal.fire({
            title: title,
            text: 'Bạn sẽ không thể khôi phục thông tin bản ghi!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy bỏ'
          }).then((result) => {
            if (result.value) {
              let requestData = {
                method: 'post',
                url: DeleteApi + '/' + self.idParams,
                data: {
                  array_id: [self.idParams],
                },
              };

              ApiService.setHeader();
              ApiService.customRequest(requestData).then((response) => {
                let responseData = response.data;
                if (responseData.status === 1) {
                  self.onBackToList('Bản ghi đã được xóa');
                } else {
                  Swal.fire(
                    'Có lỗi',
                    '',
                    'error'
                  );
                  console.log(response);
                }
              }, (error) => {
                console.log(error);
                Swal.fire({
                  title: 'Thông báo',
                  text: 'Không kết nối được với máy chủ',
                  confirmButtonText: 'Đóng'
                });

              });

            }
          });
        },

        updateModel() {
          if (this.stage.updatedData) {
            this.$forceUpdate();
          }
        },
      },
      watch: {
        idParams() {
          this.fetchData();
        }
      },
      props: {
        idParam:{},
      },
    }

</script>

<style scoped>

</style>

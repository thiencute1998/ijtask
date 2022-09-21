<template>
    <div class="component-auto-number-option-setting">
      <b-card no-body class="mb-2" footer-class="px-3 py-2">
        <b-card-header header-tag="header" class="p-1" role="tab">
          <h5 @click="visible = !visible" class="m-0 py-1 px-2">Số tự động tăng</h5>
        </b-card-header>
        <b-collapse v-model="visible" id="accordion-2">
          <b-card-body>
            <div class="sysadmin-autonumber">
              <table class="table b-table table-sm table-bordered table-editable">
                <thead>
                <tr>
                  <th scope="col" style="width: 30%">Tên</th>
                  <th scope="col" style="width: 10%">Giá trị đầu</th>
                  <th scope="col" style="width: 10%">Giá trị tăng</th>
                  <th scope="col" style="width: 10%">Giá trị cuối</th>
                  <th scope="col" style="width: 40%">Định dạng mã số</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(field, key) in autoNumber.itemsArray">
                  <td class="has-padding">{{field.NumberName}}</td>
                  <td>
                    <b-form-input
                      type="text"
                      :value="field.Prefix"
                      @change="handleSetSysadminAutonumber($event, field, 'prefix')"
                      autocomplete="autonumber prefix">
                    </b-form-input>

                  </td>
                  <td>
                    <b-form-input
                      type="text"
                      :value="field.NumberValue"
                      @change="handleSetSysadminAutonumber($event, field, 'number-value')"
                      autocomplete="autonumber number value">
                    </b-form-input>
                  </td>
                  <td>
                    <b-form-input
                      type="text"
                      :value="field.Suffix"
                      @change="handleSetSysadminAutonumber($event, field, 'suffix')"
                      autocomplete="autonumber suffix">
                    </b-form-input>
                  </td>
                  <td>
                    <b-form-input
                      type="text"
                      :value="field.NumberMask"
                      @change="handleSetSysadminAutonumber($event, field, 'number-mask')"
                      autocomplete="autonumber number mask">
                    </b-form-input>
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
          </b-card-body>
        </b-collapse>
        <template #footer v-if="visible">
          <b-button variant="primary" @click="handleSubmitForm">Lưu</b-button>
        </template>
      </b-card>
    </div>
</template>

<script>
    import ApiService from '@/services/api.service';

    export default {
      name: 'component-auto-number-option-setting',
      props: {
        value: [String, Array, Object, Number],
      },
      data () {
          return {
            autoNumber: {
              itemsArray: [],
              reqArray: [],
              isChanged: false,
              allowChange: false
            },
            visible: false
          }
      },
      computed: {},
      components: {},
      beforeCreate() {},
      mounted() {
        this.fetchData();
      },
      updated() {},
      methods: {
        fetchData() {
          let self = this;
          let urlApi = 'sysadmin/api/auto-number';
          let requestData = {
            method: 'post',
            url: urlApi,
          };

          this.$store.commit('isLoading', true);
          ApiService.customRequest(requestData).then((response) => {
            let dataResponse = response.data;
            if (dataResponse.status === 1) {
              // converse object to array
              self.autoNumber.itemsArray = _.toArray(dataResponse.data);
            }
            self.$store.commit('isLoading', false);
          }, (error) => {
            console.log(error);
            self.$store.commit('isLoading', false);

          });
        },
        handleSetSysadminAutonumber(value, field, type) {
          let fieldReq = field;
          this.autoNumber.isChanged = true;

          if (type === 'prefix') fieldReq.Prefix = value;
          if (type === 'suffix') fieldReq.Suffix = value;
          if (type === 'number-mask') fieldReq.NumberMask = value;
          if (type === 'number-value') fieldReq.NumberValue = value;

          let fieldExist = _.find(this.autoNumber.reqArray, ['NumberID', field.NumberID]);
          if (_.isObject(fieldExist)) {
            let indexExist = _.findIndex(this.autoNumber.reqArray, fieldExist);
            this.autoNumber.reqArray[indexExist] = fieldReq;
          } else {
            this.autoNumber.reqArray.push(fieldReq);
          }
        },
        handleSubmitForm(){
          let self = this;
          if (this.autoNumber.reqArray.length) {
            let field = _.first(this.autoNumber.reqArray);
            this.autoNumber.reqArray.shift();

            const requestData = {
              method: 'post',
              url: 'sysadmin/api/auto-number/update' + '/' + field.NumberID,
              data: {
                NumberID: field.NumberID,
                NumberKey: field.NumberKey,
                NumberName: field.NumberName,
                NumberValue: field.NumberValue,
                NumberMask: field.NumberMask,
                Prefix: field.Prefix,
                Suffix: field.Suffix,
              }
            };

            this.$store.commit('isLoading', true);
            ApiService.setHeader();
            ApiService.customRequest(requestData).then((responses) => {
              self.$store.commit('isLoading', false);
              let responsesData = responses.data;
              if (responsesData.status === 1) {
                self.$bvToast.toast('Lưu tùy chọn thành công', {
                  title: 'Thông báo',
                  variant: 'success',
                  solid: true
                });

              } else {
                let htmlErrors = responsesData.msg;
                self.$bvToast.toast(htmlErrors, {
                  title: 'Thông báo',
                  variant: 'warning',
                  solid: true
                });
              }
              self.handleSubmitForm();
            }, (error) => {
              self.$store.commit('isLoading', false);
              console.log(error);
            });
          }
        }
      },
      watch: {

      }
    }
</script>
<style lang="css"></style>

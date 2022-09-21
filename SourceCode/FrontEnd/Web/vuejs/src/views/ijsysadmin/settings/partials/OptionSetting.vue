<!--
- SettingMetaValue
  + Type: 1-InputNumber, 2- InputText, 3-Checkbox, 4-Select, 5-Select2, 6-IjcoreModalListing
-->

<template>
    <div class="component-global-option-setting">
      <b-card no-body class="mb-2" footer-class="px-3 py-2">
        <b-card-header header-tag="header" class="p-1" role="tab">
          <h5 @click="visible = !visible" class="m-0 py-1 px-2">{{title}}</h5>
        </b-card-header>
        <b-collapse v-model="visible" id="accordion-1">
          <b-card-body>
            <div class="row">
              <div class="col-md-12" v-for="(option, key) in value">
                <div class="row align-items-center">
                  <div class="col-md-8 mb-3 d-flex align-items-center">{{value[key].SettingName}}</div>
                  <div class="col-md-8 mb-3">
                    <ijcore-select2-server2
                      v-if="value[key].SettingValueMeta.Type === 5"
                      v-model="value[key].SettingValueMeta"
                      :url="$store.state.appRootApi + '/listing/api/common/list2'"
                      :table="value[key].SettingValueMeta.table"
                      :field-name="value[key].SettingValueMeta.FieldName"
                      :field-i-d="value[key].SettingValueMeta.FieldID"
                      :field-no="value[key].SettingValueMeta.FieldNo"
                      :get-all="true"
                      @selected="changeSelect2($event, key)"
                      placeholder="Chọn tiền tệ">
                    </ijcore-select2-server2>
                    <b-form-input v-model="value[key].SettingValue" v-if="value[key].SettingValueMeta.Type === 2" :placeholder="(value[key].SettingValueMeta.placeholder) ? value[key].SettingValueMeta.placeholder : ''"></b-form-input>
                    <b-form-input type="number" v-model="value[key].SettingValue" v-if="value[key].SettingValueMeta.Type === 1"></b-form-input>
                    <b-form-select v-model="value[key].SettingValue" :options="value[key].SettingValueMeta.options" v-if="value[key].SettingValueMeta.Type === 4"></b-form-select>
                    <ijcore-modal-listing
                      v-if="value[key].SettingValueMeta.Type === 6"
                      v-model="value[key].SettingValueMeta" :title="value[key].SettingValueMeta.title"
                      :placeholder-input="value[key].SettingValueMeta.placeholder"
                      api="/listing/api/common/list"
                      :FieldName="value[key].SettingValueMeta.FieldName"
                      :FieldNo="value[key].SettingValueMeta.FieldNo"
                      :FieldID="value[key].SettingValueMeta.FieldID"
                      :table="value[key].SettingValueMeta.table">
                    </ijcore-modal-listing>

                  </div>
                  <div class="col-md-2 mb-3">
                    <b-button title="Tùy chọn chung" v-if="option.SettingType === 1">
                      <i class="fa fa-cog"></i>
                    </b-button>
                    <b-button title="Tùy chọn đơn vị" v-if="option.SettingType === 2">
                      <i class="fa fa-university"></i>
                    </b-button>
                    <b-button title="Tùy chọn người dùng" v-if="option.SettingType === 3">
                      <i class="fa fa-user"></i>
                    </b-button>
                  </div>
                </div>
              </div>
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
    import IjcoreSelect2Server2 from "../../../../components/IjcoreSelect2Server2";
    import IjcoreModalListing from "../../../../components/IjcoreModalListing";

    export default {
      name: 'component-global-option-setting',
      props: {
        value: [String, Array, Object, Number],
        title: [String],
        open: false
      },
      data () {
          return {
            visible: this.open
          }
      },
      computed: {},
      components: {
        IjcoreSelect2Server2,
        IjcoreModalListing
      },
      beforeCreate() {},
      mounted() {
        this.fetchData();
      },
      updated() {},
      methods: {
        fetchData() {
        },
        changeSelect2(data, key){
          if (this.value[key].SettingValueMeta.FieldID) {
            this.value[key].SettingValue = data[this.value[key].SettingValueMeta.FieldID];
          }
        },
        handleSubmitForm(){
          let self = this;
          const requestData = {
            method: 'post',
            url: 'sysadmin/api/setup/update',
            data: {
              optionSetting: this.value
            }
          };

          this.$store.commit('isLoading', true);
          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            self.$store.commit('isLoading', false);
            let responsesData = responses.data;
            if (responsesData.status === 1) {
              this.$bvToast.toast('Cập nhật thành công', {
                variant: 'success',
                solid: true,
                title: 'Thông báo'
              });
            }
          }, (error) => {
            self.$store.commit('isLoading', false);
            console.log(error);
          });
        }
      },
      watch: {

      }
    }
</script>
<style lang="css"></style>

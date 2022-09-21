<template>
    <div class="main-entry">
        <div class="main-body main-body-view-action">
            <vue-perfect-scrollbar class="scroll-area" :settings="$store.state.psSettings">
                <div class="container-fluid">
                    <div role="tablist">
                      <option-setting v-model="Global" title="Tùy chọn chung" :open="true"></option-setting>
                      <option-setting v-model="SBP" title="Dự toán NSNN" :open="false"></option-setting>
                      <auto-number-option-setting></auto-number-option-setting>
                      <option-setting v-model="Chat" title="Trò chuyện" :open="false"></option-setting>
                    </div>
                </div>
            </vue-perfect-scrollbar>
        </div>
    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import IjcoreSelect2Server from "../../../components/IjcoreSelect2Server";
    import OptionSetting from "./partials/OptionSetting";
    import AutoNumberOptionSetting from "./partials/AutoNumberOptionSetting";

    const UpdateApi = 'sysadmin/api/setup/update';
    const EditApi = 'sysadmin/api/setup/edit';
    const ListApi = 'sysadmin/api/setup';
    const ListRouter = '/sysadmin/sys-setup';

    export default {
        name: 'FormUser',
        data () {
            return {
              optionSettings: {},
              Global: [],
              SBP: [],
              Chat: [],
              stage: {
                  updatedData: false
              },
            }

        },
        computed: {},
        components: {
          OptionSetting,
          IjcoreSelect2Server,
          AutoNumberOptionSetting
        },
        beforeCreate() {},
        mounted() {
            this.fetchData();
        },
        updated() {
            this.stage.updatedData = true;
        },
        methods: {
          fetchData() {
              this.fetchDataOption();
          },
          fetchDataOption() {
              let self = this;
              let requestData = {
                  method: 'post',
                  url: ListApi,
                  data: {
                  },

              };

              this.$store.commit('isLoading', true);
              ApiService.customRequest(requestData).then((response) => {
                  let responseData = response.data;
                  if (responseData.status === 1) {
                    self.optionSettings = responseData.data;
                    _.forEach(self.optionSettings, function (option, key) {
                      if (option.DataType === 'INT') {
                        self.optionSettings[key].SettingValue = Number(self.optionSettings[key].SettingValue);
                      }
                      self.optionSettings[key].SettingValueMeta = JSON.parse(self.optionSettings[key].SettingValueMeta);
                    });
                    self.Global = _.filter(self.optionSettings, ['ModuleID', 99]);
                    self.Chat = _.filter(self.optionSettings, ['ModuleID', 4]);
                    self.SBP = _.filter(self.optionSettings, ['ModuleID', 12]);
                  }
                  self.$store.commit('isLoading', false);
              }, (error) => {
                  console.log(error);
                  self.$store.commit('isLoading', false);

              });
          },
          onBackToList() {
            this.$router.push({path: ListRouter});
          },
        },
        watch: {

        }
    }
</script>
<style lang="css">
    .sysadmin-box {
        margin-bottom: 8px;
        margin-top: 8px;
    }
    .sysadmin-pane {
        margin-left: 30px;
        border-left: 1px solid #bbbbbb;
        padding-left: 10px;
    }
</style>

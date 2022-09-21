import Swal from "sweetalert2";
<template>
  <div class="component-user-change-password main-entry animated fadeIn">
    <div class="main-header">
      <div class="main-header-padding">
        <b-row class="mb-2">
          <b-col class="col-md-18">
            <div class="main-header-item main-header-name">
              <span><i class="fa fa-edit mr-2"></i> Đổi thông tin Người dùng</span>
            </div>
          </b-col>
          <b-col class="col-md-6"></b-col>
        </b-row>
        <b-row>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-actions">
              <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="handleSubmitForm"><i class="fa fa-check-square-o"></i> Lưu</b-button>
              <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onBackToList"><i class="fa fa-ban"></i> Hủy</b-button>
            </div>
          </b-col>
          <b-col class="col-md-12">
            <div class="main-header-item main-header-icons">
              <div class="main-header-collapse">
                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen=true />
              </div>

            </div>
          </b-col>
        </b-row>
      </div>

    </div>
    <div class="main-body main-body-view-action">
      <vue-perfect-scrollbar class="scroll-area" :settings="$store.state.psSettings">
        <div class="container-fluid">
          <b-card>
            <b-row>
              <b-col class="col-sm-12">
                <b-form-group
                  label="Tên đăng nhập"
                  label-for="username"
                  :label-cols="3">
                  <b-form-input v-model="username" id="username" type="text" placeholder="Tên đăng nhập" autocomplete="username email"></b-form-input>
                </b-form-group>
                <b-form-group
                  label="Mật khẩu"
                  label-for="password"
                  :label-cols="3">
                  <b-form-input v-model="password" id="password" type="password" placeholder="Mật khẩu" autocomplete="new-password"></b-form-input>
                </b-form-group>
                <b-form-group
                  label="Nhập lại mật khẩu"
                  label-for="confirmPassword"
                  :label-cols="3">
                  <b-form-input v-model="confirmPassword" id="confirmPassword" type="password" placeholder="Xác nhận mật khẩu" autocomplete="new-password"></b-form-input>
                </b-form-group>

                <b-form-group
                  label="Ảnh đại diện"
                  label-for="fileInput"
                  :label-cols="3">
                  <b-form-file ref="upload-images" id="upload-images" @change="uploadImages" :plain="true" class="mb-2" accept="image/*"></b-form-file>
                  <b-card
                    :img-src="avatar"
                    img-alt="Image"
                    img-top
                    tag="article"
                    style="max-width: 20rem;"
                    class="mb-2"
                    body-class="p-0"
                  >
                  </b-card>
                </b-form-group>
              </b-col>
            </b-row>
          </b-card>
        </div>
      </vue-perfect-scrollbar>
    </div>
  </div>
</template>

<style type="text/css">
  .component-user-change-password #upload-images{
    color: transparent;
    overflow: hidden;
  }
</style>

<script>
  import ApiService from '@/services/api.service';
  import {mapGetters, mapActions} from 'vuex';
  export default {
    name: 'sysadmin-user-changePassword',
    data() {
      return {
        username: '',
        password: '',
        confirmPassword: '',
        avatar: null,
        file: null
      }
    },
    computed: {
      ...mapGetters('auth', [
        'authenticating',
        'authenticationError',
        'authenticationErrorCode'
      ])
    },
    mounted() {
      this.init();
    },
    methods: {
      ...mapActions('auth', [
        'logout'
      ]),
      init() {
        let self = this;
        let requestData = {
          method: 'get',
          url: 'sysadmin/api/users/change-password',
          data: {}
        };
        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.username = responsesData.data.username;
            self.password = responsesData.data.password;
            self.confirmPassword = self.password;
            self.avatar = self.$store.state.appRootApi + responsesData.data.Avata;
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      uploadImages(e){
        let files = e.target.files;
        let self = this;
        if (!files.length) return;

        _.forEach(files, function (file, key) {
          if (file.type.includes('image')) {
            let reader = new FileReader();
            reader.onload = (e) => {
              let tmpObj = file;
              tmpObj.src = e.target.result;
              tmpObj.fileType = 1;
              self.file = tmpObj;
              self.avatar = self.file.src;
            };
            reader.readAsDataURL(file);
          }
        });
      },
      handleSubmitForm() {
        let self = this;
        let formData = new FormData();

        formData.append('username', self.username);
        formData.append('password', self.password);
        formData.append('password_confirmation', self.confirmPassword);
        formData.append('File', self.file);

        let requestData = {
          method: 'post',
          url: 'sysadmin/api/users/update-password',
          data: formData
        };

        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.logout();
          } else {
            let htmlErrors = __.renderErrorApiHtml(responsesData.data);
            Swal.fire(
              'Thông báo',
              htmlErrors,
              'error'
            );
          }
        }, (error) => {
          console.log(error);
        });
      },
      onBackToList(){
        this.$router.push({
          name: 'Home'
        });
      }
    }
  }
</script>

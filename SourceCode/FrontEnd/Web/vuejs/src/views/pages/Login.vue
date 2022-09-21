<template>
  <div class="app flex-row align-items-center">
    <div class="container">
      <b-row class="justify-content-center">
        <b-col class="col-md-12">
          <b-card-group>
            <b-card no-body class="p-4">
              <b-card-body>
                <b-form>
                  <h1>Đăng nhập</h1>
                  <p class="text-muted">Nhập tên và mật khẩu</p>

                  <b-alert :show="showError" variant="danger">{{error}}</b-alert>
                  <b-input-group class="mb-3">
                    <b-input-group-prepend><b-input-group-text><i class="icon-user"></i></b-input-group-text></b-input-group-prepend>
                    <b-form-input
                        type="text"
                        v-model="username"
                        class="form-control"
                        ref="usernameInput"
                        aria-describedby="inputLiveFeedbackUsername"
                        placeholder="Tên đăng nhập"
                        @keyup.enter="handleNextToPassword"
                        autocomplete="username email" />
                    <b-form-invalid-feedback :class="{'d-block': validation.username.show}" id="inputLiveFeedbackUsername">
                      {{validation.username.error}}
                    </b-form-invalid-feedback>
                  </b-input-group>
                  <b-input-group class="mb-4">
                    <b-input-group-prepend><b-input-group-text><i class="icon-lock"></i></b-input-group-text></b-input-group-prepend>
                    <b-form-input
                        type="password"
                        v-model="password"
                        class="form-control"
                        ref="passwordInput"
                        placeholder="Mật khẩu"
                        @keyup.enter="handleSubmit"
                        aria-describedby="inputLiveFeedbackPassword"
                        autocomplete="current-password" />
                      <b-form-invalid-feedback :class="{'d-block': validation.password.show}" id="inputLiveFeedbackPassword">
                          {{validation.password.error}}
                      </b-form-invalid-feedback>
                  </b-input-group>
                  <b-row>
                    <b-col class="col-12">
                      <div class="d-flex justify-content-between align-items-center">
                        <b-button variant="primary" @click="handleSubmit" :disabled="state.isSending" class="px-4">Đăng nhập</b-button>
                        <div v-show="state.isLoading" class="spinner-grow text-primary" role="status">
                          <span class="sr-only">Loading...</span>
                        </div>
                      </div>
                    </b-col>
                    <b-col class="col-12 text-right d-flex justify-content-end align-items-center">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="customChk1" value="1">
                        <label class="custom-control-label" for="customChk1">Duy trì đăng nhập</label>
                      </div>
<!--                      <b-button variant="link" class="px-0">Forgot password?</b-button>-->
                    </b-col>
                  </b-row>
                </b-form>
              </b-card-body>
            </b-card>
          </b-card-group>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
  import { mapGetters, mapActions } from 'vuex';
  import __ from "../../helpers";
export default {
  name: 'Login',
  data() {
    return {
      username: '',
      password: '',
      showError: false,
      error: '',
      state: {
        isSending: false,
        isLoading: false
      },
      validation: {
        username:{
          show: false,
          error: ''
        },
        password: {
          show: false,
          error: ''
        }
      }
    };
  },
  computed: {
    ...mapGetters('auth', [
      'authenticating',
      'authenticationError',
      'authenticationErrorCode'
    ]),

  },

  methods: {
    ...mapActions('auth', [
      'login'
    ]),

    handleNextToPassword(){
      this.$refs.passwordInput.focus();
    },

    handleSubmit() {

      let self = this;

      // change stage
      this.state.isLoading = true;
      this.state.isSending = true;

      // TODO: add validation for login
      // validation input
      if (this.username.trim().length < 4) {
          this.validation.username.show = true;
          this.validation.username.error = 'Tên đăng nhập tối thiểu 4 kí tự';
          return;
      }

      if (this.password.trim().length <4) {
          this.validation.password.show = true;
          this.validation.password.error = 'Mật khẩu chứa tối thiểu 4 kí tự';
          this.password = '';
          return;

      }

      // Perform a simple validation that email and password have been typed in
      if (this.username != '' && this.password != '') {
        this.login({email: this.username, password: this.password}).then(function (response) {
          self.state.isSending = false;
          self.state.isLoading = false;

          // có lỗi xảy ra
          if (response.status !== 1) {
            self.showError = true;
            self.error = response.msg;
            self.password = '';
          }else {
            if (response.data.Employee) socket.emit('add user', response.data.Employee);
            // create schema client database
            __.createSchemaClientDB();
          }
        });

      }

    }

  }
}
</script>

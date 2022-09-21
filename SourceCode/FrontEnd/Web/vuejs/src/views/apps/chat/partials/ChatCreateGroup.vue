<template>
  <div class="chat-create-group component-chat-create-group">
    <span class="icon-chat-comment-plus px-2" style="cursor: pointer; position: relative" title="Tạo nhóm mới" v-b-modal.modal-chat-create-group @click="fetchData">
<!--      <i class="fa fa-commenting-o icon-chat-comment"></i>-->
      <i class="fa fa-users icon-create-group"></i>
      <i class="fa fa-plus icon-chat-plus" style="position: absolute; font-size: 12px; bottom: 1px"></i>
    </span>
    <b-modal
      ref="modal-chat-create-group"
      id="modal-chat-create-group"
      ok-title="Lưu"
      title="Tạo nhóm"
      cancel-title="Đóng" size="lg" @hide="" @ok="">
      <template v-slot:modal-footer="{ ok, cancel, hide }">
        <b-button class="mr-2" variant="primary" @click="handleSubmitForm">
          Lưu
        </b-button>
        <b-button variant="primary" @click="cancel()">
          Đóng
        </b-button>
      </template>

      <div>
        <div role="group" class="form-group form-row mb-2">
          <label class="col-form-label col-sm-2">Tên</label>
          <div class="col-sm-20">
            <b-form-input v-model="model.GroupName" type="text" placeholder="Đặt tên nhóm"></b-form-input>
          </div>
        </div>

        <b-form-group
          label="Thành viên"
          label-for="member-radio"
          :label-cols="2"
          class="mb-2"
        >
          <b-form-radio-group
            id="member-radio"
            v-model="model.TypeMemberSelected"
            :plain="true"
            :options="[
              {text: 'Tất cả người dùng',value: 1},
              {text: 'Nhóm người dùng',value: 2},
              {text: 'Người dùng',value: 3}
            ]"
            :checked="3">
          </b-form-radio-group>
        </b-form-group>

        <div role="group" class="form-group form-row mb-2" v-if="model.TypeMemberSelected !== 1">
          <label class="col-form-label col-sm-2"></label>
          <div class="col-sm-20">
            <Select2 v-if="model.TypeMemberSelected === 2" v-model="model.UserGroup" :settings="{multiple: true, placeholder: '-- Chọn nhóm người dùng --'}" :options="UserGroupOption"></Select2>
            <Select2 v-if="model.TypeMemberSelected === 3" v-model="model.Members" :settings="{multiple: true, placeholder: '-- Chọn thành viên --'}" :options="UsersOption"></Select2>
          </div>
        </div>

        <div role="group" class="form-group form-row mb-2">
          <label class="col-form-label col-sm-2">Nội dung</label>
          <div class="col-sm-20">
            <b-form-textarea v-model="model.GroupDescription" rows="4"></b-form-textarea>
          </div>
        </div>

<!--        <div role="group" class="form-group form-row mb-2">-->
<!--          <label class="col-form-label col-sm-2">Đính kèm</label>-->
<!--          <div class="col-sm-20">-->
<!--            <div class="d-flex align-items-center" style="height: 100%">-->
<!--              <i class="fa fa-cloud-upload" style="font-size: 21px"></i>-->
<!--            </div>-->
<!--          </div>-->
<!--        </div>-->

      </div>
    </b-modal>
  </div>
</template>
<style type="text/css">
  #modal-chat-create-group .modal-body{
    padding-top: 1rem;
    padding-bottom: 1rem;
  }
  #modal-chat-create-group .select2-container {
    width: 100% !important;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice {
    margin-top: 5px !important;
  }
  .icon-chat-comment-plus:focus {
    outline: none;
    box-shadow: none;
  }
  .icon-chat-comment-plus i {
    color: #999;
  }
  .icon-chat-comment-plus:hover i {
    color: #73818f;
  }
  #modal-chat-create-group .bv-no-focus-ring {
    display: flex;
    align-items: center;
  }
  #modal-chat-create-group .form-check-inline {
    margin-right: 1rem;
  }
</style>
<script>
  import ApiService from '@/services/api.service';
  import Select2 from 'v-select2-component'
  export default {
    name: 'chat-create-group',
    data () {
      return {
        model: {
          GroupName: '',
          GroupDescription: '',
          Members: [],
          UserGroup: null,
          TypeMemberSelected: 1
        },
        UsersOption: [],
        UserGroupOption: []
      }
    },
    props: {
      value: [Array, Object]
    },
    components: {
      Select2
    },
    beforeCreate() {},
    mounted() {
    },
    methods: {
      fetchData(){
        // reset model
        this.model.GroupName = '';
        this.model.GroupDescription = '';
        this.model.Members = [];
        this.model.UserGroup = null;
        this.model.TypeMemberSelected = 1;

        if (this.UsersOption.length) return;
        let self = this;
        let requestData = {
          method: 'get',
          url: 'extensions/api/chat/create-group',
          data: {}
        };
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.UsersOption = [];
            _.forEach(responsesData.data.AllUsers, function (user, key) {
              let tmpObj = {};
              tmpObj.id = user.UserID;
              tmpObj.text = user.FullName;
              tmpObj.EmployeeID = user.EmployeeID;
              tmpObj.Avatar = user.Avata;
              self.UsersOption.push(tmpObj);
            });

            // user group
            self.UserGroupOption = [];
            _.forEach(responsesData.data.UserGroup, function (user, key) {
              let tmpObj = {};
              tmpObj.id = user.UserGroupID;
              tmpObj.text = user.UserGroupName;
              self.UserGroupOption.push(tmpObj);
            });
          }
        }, (error) => {
          console.log(error);
        });
      },
      handleSubmitForm(){
        // validate
        if (!this.model.GroupName) {
          this.$bvToast.toast('Tên nhóm không được để trống', {
            title: 'Thông báo',
            variant: 'warning'
          });
          return false;
        }

        if ((this.model.TypeMemberSelected === 3 && !this.model.Members.length) ||
          (this.model.TypeMemberSelected === 2 && (!this.model.UserGroup || (this.model.UserGroup && !this.model.UserGroup.length)))) {
          this.$bvToast.toast('Nhóm chưa có thành viên nào', {
            title: 'Thông báo',
            variant: 'warning'
          });
          return false;
        }

        let self = this;
        let members = [];
        _.forEach(this.model.Members, function (UserID, key) {
          let user = _.find(self.UsersOption, ['id', Number(UserID)]);
          if (user) {
            let tmpObj = {};
            tmpObj.UserID = user.id;
            tmpObj.UserName = user.text;
            tmpObj.EmployeeID = user.EmployeeID;
            members.push(tmpObj);
          }
        });
        let requestData = {
          method: 'post',
          url: 'extensions/api/chat/store-group',
          data: {
            GroupName: this.model.GroupName,
            GroupDescription: this.model.GroupDescription,
            Members: members,
            TypeMemberSelected: this.model.TypeMemberSelected,
            UserGroup: this.model.UserGroup
          }
        };
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
            if (responsesData.status === 1) {
              if (responsesData.data.Group) {
                self.$emit('on:new-group', {
                  group: responsesData.data.Group,
                  members: responsesData.data.Members
                });
                this.$bvToast.toast('Nhóm đã được tạo', {
                  title: 'Thông báo',
                  variant: 'success'
                });
                self.$bvModal.hide('modal-chat-create-group');
              }
          } else {
            if (responsesData.data.GroupName) {
              this.$bvToast.toast(responsesData.data.GroupName[0], {
                title: 'Thông báo',
                variant: 'warning'
              });
            }
            if (responsesData.data.Members) {
              this.$bvToast.toast(responsesData.data.Members[0], {
                title: 'Thông báo',
                variant: 'warning'
              });
            }
          }
        }, (error) => {
          console.log(error);
        });
      }
    },
    watch: {}

  }
</script>

<template>
  <div class="chat-tab mx-1" :class="['chat-tab-' + tabChat.GroupID, (stage.minimize) ? 'tab-minimize' : '']" ref="chat-tab" @drop="onDropFiles">
    <div class="tab-header" ref="tab-header" @click="onToggleMinimize">
      <div class="chat-bar px-2 py-2">
        <div class="d-flex align-items-center justify-content-between chat-bar-wrap">
          <div class="media align-items-center">
            <div class="d-flex mr-2 align-self-start img-block" v-if="tabChat.GroupType === 1">
              <img :src="$store.state.appRootApi + tabChat.Avatar" class="img-avatar"/>
              <span aria-label="Đang hoạt động" v-if="$store.state.chat.online[tabChat.UserID]" class="chat-icon-active"></span>
            </div>

            <div class="img-block img-block-group d-flex mr-3 align-self-center" v-if="tabChat.GroupType !== 1">
              <div class="img-block-item">
                <img :src="$store.state.appRootApi + tabChat.Avatar0" class="img-avatar"/>
              </div>
              <div class="img-block-item">
                <img :src="$store.state.appRootApi + tabChat.Avatar1" class="img-avatar"/>
              </div>
            </div>

            <div class="media-body d-flex align-items-center flex-wrap">
              <span v-if="tabChat.GroupType !== 3" class="tab-header-title" :title="tabChat.GroupName">{{tabChat.GroupName}}</span>
              <a class="tab-header-title" :title="tabChat.GroupName" href="#/" @click="redirectToView($event, tabChat.GroupID)" v-else>{{tabChat.GroupName}}</a>
            </div>
          </div>
          <b-dropdown class="tab-header-icon settings ml-auto" no-caret right title="Tùy chọn">
            <template v-slot:button-content>
              <span class="d-flex align-items-center"><i class="fa fa-cog"></i></span>
            </template>
          </b-dropdown>
          <div class="tab-header-icon video-conference ml-1">
            <span class="d-flex align-items-center" style="cursor: pointer"><i class="fa fa-video-camera"></i></span>
          </div>
          <b-dropdown class="tab-header-icon action-more" no-caret right title="Thao tác">
            <template v-slot:button-content>
              <span class="d-flex align-items-center"><i class="fa fa-ellipsis-v"></i></span>
            </template>
            <b-dropdown-item v-if="!isMessenger" @click="openMessenger">Chi tiết</b-dropdown-item>
            <b-dropdown-item @click="onShowSearchMessage">Tìm kiếm</b-dropdown-item>
            <b-dropdown-item @click="stage.showSearchAdvanced = !stage.showSearchAdvanced">Tìm kiếm nâng cao</b-dropdown-item>
            <b-dropdown-divider v-if="tabChat.GroupType !== 1 && !isMessenger"></b-dropdown-divider>
<!--            <b-dropdown-item v-if="tabChat.GroupType !== 1 && isAdmin && !isMessenger">Chỉnh sửa</b-dropdown-item>-->
            <b-dropdown-item v-if="tabChat.GroupType !== 1 && isAdmin" @click="stage.showEditGroupName = !stage.showEditGroupName">Tên cuộc trò chuyện</b-dropdown-item>
            <b-dropdown-item v-if="tabChat.GroupType !== 1" @click="onShowMember">Thành viên</b-dropdown-item>
            <b-dropdown-item v-if="tabChat.GroupType !== 1 && isAdmin" @click="onShowAddMember">Thêm thành viên</b-dropdown-item>
            <b-dropdown-divider></b-dropdown-divider>
            <li class="dropdown b-dropdown dropright">
              <a class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" data-toggle="dropdown" @click.stop="$_onToggleDropdownSubMenu($event)" href="#">Cỡ chữ</a>
              <ul role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-menu m-0">
                <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item" @click="setFontSizeMessage('sm')">Nhỏ</a></li>
                <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item" @click="setFontSizeMessage('md')">Trung bình</a></li>
                <li role="presentation"><a role="menuitem" target="_self" href="#" class="dropdown-item" @click="setFontSizeMessage('lg')">To</a></li>
              </ul>
            </li>
            <b-dropdown-divider v-if="tabChat.GroupType !== 1"></b-dropdown-divider>
            <b-dropdown-item v-if="tabChat.GroupType !== 1" @click="handleLeaveGroup">Rời nhóm</b-dropdown-item>
            <b-dropdown-item v-if="tabChat.GroupType !== 1 && isAdmin" @click="changeMemberRole(currentUser.UserID, 2)">Gỡ vai trò quản trị viên</b-dropdown-item>
            <b-dropdown-item v-if="tabChat.GroupType !== 1 && isAdmin" @click="handleDeleteGroup">Xóa cuộc trò chuyện</b-dropdown-item>
          </b-dropdown>
          <span class="px-1" style="cursor: pointer" v-if="!isMessenger" @click="openMessenger"><i class="fa fa-expand" style="font-size: 16px" title="Chi tiết"></i></span>
          <span class="px-1" style="cursor: pointer" v-if="!isMessenger" @click="stage.minimize = !stage.minimize">
            <i class="fa fa-minus" style="font-size: 16px" title="Thu gọn" v-if="!stage.minimize"></i>
            <i class="fa fa-window-maximize" style="font-size: 16px" title="Mở rộng" v-if="stage.minimize"></i>
          </span>
          <span class="px-1" @click="onRemoveTabChat" style="cursor: pointer"><i class="fa fa-times" title="Đóng trò chuyện"></i></span>
        </div>
      </div>

      <b-collapse class="collapse-search" id="collapse-search" v-model="stage.showSearch">
        <b-input-group>
          <b-form-input v-model="model.search" @keypress.enter="handleSubmitSearch" ref="collapse-search-input"></b-form-input>
          <b-input-group-append>
            <b-button @click="handleSubmitSearch"><i class="fa fa-search"></i></b-button>
            <b-button @click="onCloseSearch"><i class="fa fa-times"></i></b-button>
          </b-input-group-append>
        </b-input-group>
      </b-collapse>
      <b-collapse class="collapse-search" id="collapse-search-advanced" v-model="stage.showSearchAdvanced">
        <b-input-group>
          <b-form-input v-model="model.searchAdvanced" placeholder="Chọn đối tượng..." :title="model.searchAdvanced" @click="onClickSearchAdvanced" readonly ref="collapse-search-input"></b-form-input>
          <b-input-group-append>
            <b-button @click="onCloseSearchAdvanced"><i class="fa fa-times"></i></b-button>
          </b-input-group-append>
        </b-input-group>
      </b-collapse>
      <b-collapse id="collapse-add-member" v-model="stage.showAddMember">
        <b-input-group>
          <div class="input-group-select2">
            <Select2 v-model="membersAdded" :options="addMembersOption" :settings="{allowClear: true, placeholder: 'Thêm thành viên vào cuộc trò chuyện', multiple: true}">></Select2>
          </div>
          <template v-slot:append>
            <b-input-group-text style="cursor: pointer" @click="handleAddMembers">Thêm</b-input-group-text>
          </template>
        </b-input-group>
      </b-collapse>
      <b-collapse id="collapse-edit-group-name" v-model="stage.showEditGroupName">
        <b-input-group>
          <b-form-input v-model="model.groupName" @keypress.enter="handleUpdateGroupName" ref="collapse-edit-group-name-input"></b-form-input>
          <template v-slot:append>
            <b-input-group-text style="cursor: pointer" @click="handleUpdateGroupName">Lưu</b-input-group-text>
          </template>
        </b-input-group>
      </b-collapse>
      <b-collapse id="collapse-edit-message" class="collapse-edit-message" v-model="stage.showEditMessage">
        <div class="message-edit-container input-group" v-if="messageEdit" :class="(stage.isSendingMessage) ? 'app-disable' : ''">
          <div class="message-edit-input px-2 py-1 area-auto-resize"
               @keyup="onKeyupMessage($event)"
               @keypress.enter="handleSubmitEditMessage($event)"
               :value="messageEdit.Content"
               contenteditable>
            {{messageEdit.Content | stripHtml}}
          </div>
          <div class="input-group-append m-auto">
            <b-button @click="handleSubmitEditMessage" title="Cập nhật"><i class="fa fa-paper-plane-o"></i></b-button>
            <b-button @click="onCloseEditMessage" title="Đóng"><i class="fa fa-times"></i></b-button>
          </div>
        </div>
      </b-collapse>

    </div>
    <div class="tab-body scroll-touch p-2" ref="tab-body" @scroll="onScrollMessage">
      <div class="spinners" style="height: 40px" v-if="stage.loading && messageArray.length && !stage.showReloadMessage">
        <div class="sk-double-bounce">
          <div class="sk-child sk-double-bounce1"></div>
          <div class="sk-child sk-double-bounce2"></div>
        </div>
      </div>
      <div class="chat-content" :class="'message-font-size-' + $store.state.chat.fontSize" v-if="tabChat.GroupID && messageArray.length && !stage.showReloadMessage" ref="chat-content">
        <div class="media mb-2"
             :id="'message-' + message.LineID"
             :class="[(message.Content.includes(':sb-action-member')) ? 'message-action-member' : ((message.UserID === currentUser.UserID) ? 'message-mine' : 'message-not-mine')]"
             v-for="(message, key) in messageArray">
          <div :id="'popover-target-user-name-' + message.LineID" class="d-flex mr-2 align-self-start img-block" :title="message.UserName" v-if="message.UserID !== currentUser.UserID && !message.Content.includes(':sb-action-member')">
            <img class="img-avatar" :src="$store.state.appRootApi + message.Avata" alt="">
            <span aria-label="Đang hoạt động" v-if="tabChat.GroupType !== 1 && $store.state.chat.online[message.UserID]" class="chat-icon-active"></span>
            <b-popover :target="'popover-target-user-name-' + message.LineID" triggers="click" placement="top">
              {{message.UserName}}
            </b-popover>
          </div>
          <div class="media-body" :class="[(message.Content.includes(':sb-action-member')) ? 'text-left' : ((message.UserID === currentUser.UserID) ? 'text-right message-right' : 'text-left message-left')]">
            <div class="message-content-entry" v-if="!message.Content.includes(':sb-action-member')">
              <div class="message-content"
                   @mouseenter="onMouseenterMessage($event, key)"
                   @mouseleave="onMouseleaveMessage($event, key)">
                <div class="message-main">
                  <div class="message-files" v-if="message.ContentFile && message.ContentFile.length">
                    <div class="message-file" :class="[(file.FileType === 1) ? 'message-image' : ((file.FileType === 2) ? 'message-application' : ((file.FileType === 3) ? 'message-audio' : ((file.FileType === 4) ? 'message-video' : '')))]" v-for="(file, key) in message.ContentFile">
                      <a :href="$store.state.appRootApi + file.FieldAttach" target="_blank" class="img-block" v-if="file.FileType === 1">
                        <img :src="$store.state.appRootApi + file.FieldAttach" :alt="file.FileAttachName">
                      </a>
<!--                      <a :href="$store.state.appRootApi + file.FieldAttach" target="_blank" class="img-block" v-if="file.FileType === 2">-->
                      <a href="#" @click="downloadFile($event, file)" class="img-block" v-if="file.FileType === 2">
                        <div class="file-left d-flex align-items-center justify-content-end">
                          <i class="icon-eye icon mr-2" @click="onViewFile($event, file)"></i>
                          <i class="fa fa-file-text-o"></i>
                        </div>
                        <div class="file-right">
                          <span :title="file.FileAttachName">{{file.FileAttachName}}</span>
                        </div>
                      </a>
                      <div class="video-content" v-if="file.FileType === 4">
                        <video controls>
                          <source :src="$store.state.appRootApi + file.FieldAttach" type="video/mp4">
                          <source :src="$store.state.appRootApi + file.FieldAttach" type="video/ogg">
                          Your browser does not support HTML video.
                        </video>
                      </div>
                      <audio controls v-if="file.FileType === 3">
                        <source :src="$store.state.appRootApi + file.FieldAttach" type="audio/ogg">
                        <source :src="$store.state.appRootApi + file.FieldAttach" type="audio/mpeg">
                        Your browser does not support the audio element.
                      </audio>
                    </div>
                  </div>
                  <div class="message-text-wrap" :class="(checkTaskStatus(message)) ? 'has-task-status' : ''">
                    <span v-if="message.ContentParent" class="message-feedback-info">
                      <i class="fa fa-share"></i> &nbsp;
                      <span v-if="message.UserID === currentUser.UserID">Bạn </span>
                      <span v-else>{{getLastName(message.UserID)}}</span>
                      đã trả lời <span v-if="message.UserID !== message.ContentParent.UserID && tabChat.GroupType !== 1">{{getLastName(message.ContentParent.UserID)}}</span>
                    </span>
                    <div class="message-task-status text-right" style="line-height: 1" v-if="checkTaskStatus(message)">
                      <span class="badge text-white"
                            v-if="checkTaskStatus(message) === 1"
                        :id="'popover-target-task-' + message.LineID"
                        :class="[
                          (message.Datalist[0].Status === 1) ? 'badge-warning' : '',
                          (message.Datalist[0].Status === 2) ? 'badge-info' : '',
                          (message.Datalist[0].Status === 3) ? 'badge-primary' : '',
                          (message.Datalist[0].Status === 4) ? 'badge-dark' : '',
                          (message.Datalist[0].Status === 5) ? 'badge-danger' : '',
                          (message.Datalist[0].Status === 6) ? 'badge-success' : '',
                          (!message.Datalist[0].Status) ? 'badge-secondary' : '']"
                        style="cursor: pointer">{{(message.Datalist[0].StatusDescription) ? message.Datalist[0].StatusDescription : 'Chưa có trạng thái'}}</span>
                      <span
                        :id="'popover-target-task-' + message.LineID"
                        v-if="checkTaskStatus(message) === 2"
                        class="badge badge-info text-white">Trạng thái</span>

                      <b-popover custom-class="message-popover" :ref="'popover-target-task-' + message.LineID" :target="'popover-target-task-' + message.LineID" triggers="hover" placement="top">
                        <div v-for="(datalist, key) in messageArray[key].Datalist">
                          <div v-if="datalist.DatalistTable === 'task'">
                            - {{datalist.LinkTableName}}:
                            <span>{{datalist.LinkName}}</span>
                            <span v-if="datalist.Type && datalist.Type === 2 && datalist.ParentName"> ({{datalist.ParentName}})</span> &nbsp;
                            <span
                              class="badge text-white"
                              v-if="checkTaskStatus(message) === 2"
                              :class="[
                                (datalist.Status === 1) ? 'badge-warning' : '',
                                (datalist.Status === 2) ? 'badge-info' : '',
                                (datalist.Status === 3) ? 'badge-primary' : '',
                                (datalist.Status === 4) ? 'badge-dark' : '',
                                (datalist.Status === 5) ? 'badge-danger' : '',
                                (datalist.Status === 6) ? 'badge-success' : '',
                                (!datalist.Status) ? 'badge-secondary' : '']"
                              style="cursor: pointer">{{(datalist.StatusDescription) ? datalist.StatusDescription : 'Chưa có trạng thái'}}</span>
                          </div>
                        </div>
                      </b-popover>
                    </div>
                    <div class="message-text px-2 py-1" :title="message.CreatedDate | convertTimeToHMTime" v-if="message.Content && message.Content !== ':sb-agree'">
                      <div class="message-feedback" v-if="message.ContentParent">
                        <blockquote>
                          <div class="quote-line"></div>
                          <div class="quote-content">
                            <a :href="'#message-' + message.ContentParent.LineID" @click="scrollIntoView($event, message.ContentParent.LineID)">
                              <span class="message-feedback-text" :title="message.ContentParent.Content" v-if="message.ContentParent.Content && !message.ContentParent.Content.includes(':sb-action')" v-html="message.ContentParent.Content"></span>
                              <span class="message-feedback-text" v-if="message.ContentParent.Content && message.ContentParent.Content.includes(':sb-action')">{{getMessageActionMember(message.ContentParent)}}</span>
                              <div class="message-feedback-file" :title="message.ContentParent.ContentFile[0].FileAttachName" v-if="!message.ContentParent.Content && message.ContentParent.ContentFile
                                && message.ContentParent.ContentFile.length && message.ContentParent.ContentFile[0].FileType === 1">
                                <span>Ảnh</span>
                                <i class="fa fa-file-image-o ml-1"></i>
                              </div>
                              <div class="message-feedback-file" :title="message.ContentParent.ContentFile[0].FileAttachName" v-if="!message.ContentParent.Content && message.ContentParent.ContentFile
                                && message.ContentParent.ContentFile.length && message.ContentParent.ContentFile[0].FileType === 2">
                                <span>Tài liệu</span>
                                <i class="fa fa-file-text-o ml-1"></i>
                            </div>
                              <div class="message-feedback-file" :title="message.ContentParent.ContentFile[0].FileAttachName" v-if="!message.ContentParent.Content && message.ContentParent.ContentFile
                                && message.ContentParent.ContentFile.length && message.ContentParent.ContentFile[0].FileType === 3">
                                <span>Âm thanh</span>
                                <i class="fa fa-file-audio-o ml-1"></i>
                            </div>
                              <div class="message-feedback-file" :title="message.ContentParent.ContentFile[0].FileAttachName" v-if="!message.ContentParent.Content && message.ContentParent.ContentFile
                                && message.ContentParent.ContentFile.length && message.ContentParent.ContentFile[0].FileType === 4">
                                <span>Video</span>
                                <i class="fa fa-file-video-o ml-1"></i>
                            </div>
                            </a>
                          </div>
                        </blockquote>
                      </div>
                      <span class="text-left" v-html="filterMessage(message.Content)"></span>
                    </div>
                  </div>
                  <span class="text-left message-icon-agree px-2 py-1" v-if="message.Content === ':sb-agree'">
                  <div style="width: 36px; height: 36px">
                    <svg aria-labelledby="js_43x" role="img" height="100%" width="100%" version="1.1" viewBox="0 0 256 256" x="0px" y="0px"><title id="js_43x">Ký hiệu giơ ngón tay cái</title><g><g><polyline fill="transparent" points="256,0 258,256 2,258 "></polyline><path d="M254,147.1c0-12.7-4.4-16.4-9-20.1c2.6-4.2,5.1-10.2,5.1-18c0-15.8-12.3-25.7-32-25.7h-52c-0.5,0-1-0.5-0.9-1
                      c1.4-8.6,3-24,3-31.7c0-16.7-4-37.5-19.3-45.7c-4.5-2.4-8.3-3.7-14.1-3.7c-8.8,0-14.6,3.6-16.7,5.9c-1.3,1.4-1.9,3.3-1.8,5.2
                      l1.3,34.6c0.2,2.8-0.3,5.4-1.6,7.7l-24,47.8c-1.7,3.5-4.2,6.6-7.6,8.5c-3.5,2-6.5,5.9-6.5,9.5v94.8C78,230,94,238,112.3,238h86.1
                      c13.5,0,22.4-4.5,27.2-13.5c4.4-8.2,3.2-15.8,1.4-21.5c7.4-2.3,14.8-8,16.9-18.3c1.3-6.6-0.7-12.1-2.9-16.2
                      C247.5,165,254,159.8,254,147.1z" fill="#4080ff" stroke="transparent" stroke-linecap="round" stroke-width="5%"></path><path d="M56.2,100H13.8C7.3,100,2,105.3,2,111.8v128.5c0,6.5,5.3,11.8,11.8,11.8h42.4c6.5,0,11.8-5.3,11.8-11.8V111.8
                      C68,105.3,62.7,100,56.2,100z" fill="#4080ff"></path></g></g></svg>
                  </div>
                </span>
                </div>
                <div class="message-action-other">
                  <b-button :id="'popover-target-' + message.LineID">
                    <i class="fa fa-ellipsis-h"></i>
                  </b-button>
                  <b-button :id="'popover-target-object-' + message.LineID" v-show="messageArray[key].Datalist && messageArray[key].Datalist.length">
                    <i class="fa fa-puzzle-piece"></i>
                  </b-button>

                  <b-popover custom-class="message-popover" :ref="'popover-target-' + message.LineID" :target="'popover-target-' + message.LineID" triggers="focus" placement="top">
                    <span class="mx-2" @click="handleAttachDataList(key)" v-if="message.UserID === currentUser.UserID">Gắn</span>
                    <span class="mx-2" @click="onShowEditMessage(key)" v-if="message.UserID === currentUser.UserID">Sửa</span>
                    <span class="mx-2" @click="handleDeleteMessage(key)" v-if="message.UserID === currentUser.UserID">Gỡ</span>
                    <span class="mx-2" @click="handleFeedbackMessage(key)">Phản hồi</span>
<!--                    <span class="mx-2 mt-1 d-inline-block">Đang thực hiện</span>-->
                    <chat-task-status v-model="messageArray[key].Datalist"></chat-task-status>
                  </b-popover>

                  <b-popover custom-class="message-popover" :ref="'popover-target-object-' + message.LineID" :target="'popover-target-object-' + message.LineID" triggers="hover" placement="top">
                    <div v-for="(datalist, key) in messageArray[key].Datalist">
                      <div v-if="datalist.DatalistTable === 'task'">
                        - {{datalist.LinkTableName}}:
                        <span>{{datalist.LinkName}}</span>
                        <span v-if="datalist.Type && datalist.Type === 2 && datalist.ParentName"> ({{datalist.ParentName}})</span>
                      </div>
                      <div v-else>- {{datalist.LinkTableName}}: {{datalist.LinkName}}</div>
                    </div>
                  </b-popover>

                </div>
              </div>
              <div class="message-timer" v-if="(showMessageTime(key) && key !== (messageArray.length - 1) && tabChat.GroupType === 1) || (tabChat.GroupType !== 1 && key !== (messageArray.length - 1))">
                <span v-if="message.UserID !== currentUser.UserID">{{getLastName(message.UserID)}} &nbsp;•&nbsp;</span>
                <span>{{formatMessageTime(key)}}</span>
              </div>
              <div class="status-seen" v-if="key === (messageArray.length - 1) && tabChat.seen && message.UserID === currentUser.UserID && tabChat.GroupType === 1">
                <i class="fa fa-check"></i>
                Đã xem
<!--                <span v-if="tabChat.GroupType === 1">lúc {{formatMessageTime(key)}}</span>-->
              </div>
              <div class="status-seen" v-if="getUserSeen() && key === (messageArray.length - 1) && tabChat.userSeen && tabChat.userSeen.length && tabChat.GroupType !== 1">
                <i class="fa fa-check"></i>
                <span>&nbsp; {{getUserSeen()}} &nbsp;•&nbsp;Đã xem</span>
              </div>
            </div>

            <div class="message-content-entry" v-else>
              <div class="message-content">
                <div class="message-text p-2">
                  <i class="fa fa-user-plus mr-3" v-if="message.Content.includes(':sb-action-member_add_')"></i>
                  <i class="fa fa-eject mr-3" v-if="message.Content.includes(':sb-action-member_remove_')"></i>
                  <div>
                    <div style="max-width: none; font-size: 11px">{{getMessageActionMember(message)}}</div>
                    <div><span style="font-size: 11px; color: #607d8b">{{formatMessageTime(key)}}</span></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
      <div class="chat-content-init text-center" v-if="!messageArray.length && !stage.loading && !stage.showReloadMessage">
        <span v-if="!stage.showSearch && !stage.showSearchAdvanced">Hãy trò chuyện trên Ijtask!</span>
        <span v-if="stage.showSearch || stage.showSearchAdvanced">Không tìm thấy kết quả</span>
      </div>
      <div class="spinners" v-if="stage.loading && !messageArray.length && !stage.showReloadMessage">
        <div class="sk-double-bounce">
          <div class="sk-child sk-double-bounce1"></div>
          <div class="sk-child sk-double-bounce2"></div>
        </div>
      </div>
      <div class="chat-reload-message" v-if="stage.showReloadMessage">
        <b-button variant="primary" @click="loadMessage">Tải lại</b-button>
      </div>

    </div>
    <div class="tab-footer" :class="(stage.isSendingMessage) ? 'app-disable' : ''" ref="tab-footer">
      <div class="message-typing">
        <div class="message-upload-preview" v-if="imagesPreview.length || filesPreview.length">
          <div class="img-preview mr-2 upload-item" v-for="(image, key) in imagesPreview">
            <img :src="image.src" :alt="image.name">
            <i class="fa fa-times icon-remove" @click="onRemoveUpload(key, 'image')"></i>
          </div>
          <div class="file-preview mr-2 upload-item" v-for="(file, key) in filesPreview">
            <div class="file-left">
              <i class="fa fa-file-text-o" v-if="file.fileType === 2"></i>
              <i class="fa fa-file-audio-o" v-if="file.fileType === 3"></i>
              <i class="fa fa-file-video-o" v-if="file.fileType === 4"></i>
            </div>
            <div class="file-right">
              <span>{{file.ext}}</span>
              <span :title="file.name">{{file.name}}</span>
            </div>
            <i class="fa fa-times icon-remove" @click="onRemoveUpload(key, 'file')"></i>
          </div>
        </div>
        <div class="message-feedback-preview" v-if="messageFeedback">
          <div class="feedback-title">
            Đang phản hồi <span>{{getLastName(messageFeedback.UserID)}}</span>
          </div>
          <div class="feedback-text">
            <div v-if="messageFeedback.ContentFile && messageFeedback.ContentFile.length && !messageFeedback.Content">
              <span v-if="messageFeedback.ContentFile[0].FileType === 1">Ảnh <i class="fa fa-file-image-o ml-1" style="font-size: 12px"></i></span>
              <span v-if="messageFeedback.ContentFile[0].FileType === 2">Tài liệu <i class="fa fa-file-text-o ml-1" style="font-size: 12px"></i></span>
              <span v-if="messageFeedback.ContentFile[0].FileType === 3">Âm thanh <i class="fa fa-file-audio-o ml-1" style="font-size: 12px"></i></span>
              <span v-if="messageFeedback.ContentFile[0].FileType === 4">Video <i class="fa fa-file-video-o ml-1" style="font-size: 12px"></i></span>
            </div>
            <span v-if="messageFeedback.Content">{{messageFeedback.Content | stripHtml}}</span>
          </div>
          <i class="fa fa-times remove-feedback" @click="messageFeedback = null"></i>
        </div>
        <div @focus="onReadMessage"
             @keyup="onKeyupMessage($event)"
             @keydown="onKeydownSearchSuggest"
             @keypress.enter="handleSubmitMessage($event)"
             data-text="Nhập tin nhắn..."
             class="message-input px-2 py-1 area-auto-resize" id="message-input" placeholder="Nhập tin nhắn..." :contenteditable="!stage.isSendingMessage">
        </div>
      </div>
      <div class="chat-extension px-2 text-left d-flex align-items-center">
        <span class="mr-3" title="Thêm ảnh" @click="onClickUploadImages"><i class="fa fa-file-image-o"></i></span>
        <span class="mr-3" title="Thêm tệp" @click="onClickUploadFiles('file')"><i class="fa fa-paperclip"></i></span>
        <span class="mr-3" title="Thêm audio/video" @click="onClickUploadFiles('audio-video')"><i class="fa fa-file-video-o"></i></span>
        <span class="mr-3" title="Thêm thành viên" v-if="tabChat.GroupType !== 1 && isAdmin" @click="onShowAddMember"><i class="fa fa-user-plus"></i></span>
        <span class="mr-3" title="Chọn biểu tượng cảm xúc" @click="onToggleEmoji">
          <i class="fa fa-smile-o"></i>
        </span>
        <div class="container-emoji" :id="'container-emoji-' + tabChat.GroupID">
          <input class="emojionearea-input" hidden>
        </div>
        <span class="chat-icon-agree ml-auto" v-if="!stage.showIconSubmitMessage" title="Gửi đi" @click="onClickIconAgree">
            <svg aria-labelledby="js_4ex" preserveAspectRatio="xMinYMax meet" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" height="16" width="16"><title id="js_4ex">Gửi đi</title><g style="fill: rgb(190, 195, 201);"><path d="M16,9.1c0-0.8-0.3-1.1-0.6-1.3c0.2-0.3,0.3-0.7,0.3-1.2c0-1-0.8-1.7-2.1-1.7h-3.1c0.1-0.5,0.2-1.3,0.2-1.8 c0-1.1-0.3-2.4-1.2-3C9.3,0.1,9,0,8.7,0C8.1,0,7.7,0.2,7.6,0.4C7.5,0.5,7.5,0.6,7.5,0.7L7.6,3c0,0.2,0,0.4-0.1,0.5L5.7,6.6 c0,0-0.1,0.1-0.1,0.1l0,0l0,0L5.3,6.8C5.1,7,5,7.2,5,7.4v6.1c0,0.2,0.1,0.4,0.2,0.5c0.1,0.1,1,1,2,1h5.2c0.9,0,1.4-0.3,1.8-0.9 c0.3-0.5,0.2-1,0.1-1.4c0.5-0.2,0.9-0.5,1.1-1.2c0.1-0.4,0-0.8-0.2-1C15.6,10.3,16,9.9,16,9.1z"></path><path d="M3.3,6H0.7C0.3,6,0,6.3,0,6.7v8.5C0,15.7,0.3,16,0.7,16h2.5C3.7,16,4,15.7,4,15.3V6.7C4,6.3,3.7,6,3.3,6z"></path></g></svg>
        </span>
        <span class="ml-auto" v-if="stage.showIconSubmitMessage" @click="handleSubmitMessage($event)"><i class="fa fa-paper-plane-o"></i></span>
      </div>
      <div class="chat-suggestion">
        <chat-suggestion v-if="stage.showSuggestion" v-model="suggested" :suggest-array="suggestArray" @on:suggested="onChatSuggested($event)"></chat-suggestion>
      </div>
      <div class="chat-hidden">
        <input type="file" multiple hidden @change="uploadImages" name="upload-images" ref="upload-images" id="upload-images" accept="image/*">
        <input type="file" multiple hidden @change="uploadFiles('file')" name="upload-files" ref="upload-files" id="upload-files" accept=".xlsx, .xls, .doc, .docx, .ppt, .pptx, .txt, .pdf, .zip, .rar, .cif">
        <input type="file" multiple hidden @change="uploadFiles('audio-video')" name="upload-audios-videos" ref="upload-audios-videos" id="upload-audios-videos" accept="audio/*, video/*">
      </div>

    </div>
    <chat-modal-datalist
      v-model="messageArray[keyMessageAttach]"
      ref="chat-modal-datalist"
      ref-modal="modal-datalist"
      id-modal="modal-datalist"
      title-modal="Gắn đối tượng"
      @on:save-category-key="handleSaveCategoryKey"
      size-modal="xl"></chat-modal-datalist>

    <chat-modal-datalist
      ref="chat-modal-search-datalist"
      ref-modal="modal-search-datalist"
      :is-search="true"
      id-modal="modal-search-datalist"
      title-modal="Tìm kiếm nâng cao"
      @on:save-category-key="handleSearchAdvancedMessage"
      size-modal="lg"></chat-modal-datalist>

    <b-modal
      :ref="'modal-chat-member-' + tabChat.GroupID"
      :id="'modal-chat-member-' + tabChat.GroupID"
      modal-class="modal-chat-member"
      ok-title="Đóng"
      :title="'Thành viên: ' + tabChat.GroupName"
      ok-only
      :hide-footer="true"
      body-class="p-3"
      size="md">
      <div class="content">
        <b-card no-body>
          <b-tabs card>
            <b-tab title="Thành viên" active style="max-height: 350px; overflow-y: auto">
              <ul class="m-0 p-0">
                <li class="d-flex align-items-center py-2" v-for="(member, key) in members">
                  <div class="member-avatar d-flex mr-2 align-self-start img-block" style="width: 40px; height: 40px">
                    <img :src="$store.state.appRootApi + member.Avatar" class="img-avatar">
                  </div>
                  <div class="member-name">{{member.UserName}}</div>

                  <div class="member-action ml-auto">
                    <b-dropdown class="tab-header-icon action-more" no-caret right title="Thao tác" v-if="member.UserID !== currentUser.UserID">
                      <template v-slot:button-content>
                        <span class="d-flex align-items-center"><i class="fa fa-ellipsis-h" style="color: #999"></i></span>
                      </template>
                      <b-dropdown-item v-if="isAdmin && member.Type !== 1" @click="changeMemberRole(member.UserID, 1)">Đặt làm quản trị viên</b-dropdown-item>
                      <b-dropdown-item @click="onShowChatMessage(member)">Nhắn tin</b-dropdown-item>
                      <b-dropdown-item v-if="isAdmin" @click="handleRemoveMember(member)">Xóa</b-dropdown-item>
                    </b-dropdown>
                    <b-button v-if="member.UserID === currentUser.UserID" style="background: #f5f6f7" @click="handleLeaveGroup">Rời khỏi nhóm</b-button>
                  </div>

                </li>
              </ul>
            </b-tab>
            <b-tab title="Quản trị viên" style="max-height: 350px; overflow-y: auto">
              <ul class="m-0 p-0">
                <li class="d-flex align-items-center py-2" v-for="(admin, key) in admins">
                  <div class="member-avatar d-flex mr-2 align-self-start img-block" style="width: 40px; height: 40px">
                    <!--                        <img :src="/img/avatars/1.jpg" class="img-avatar">-->
                    <img :src="$store.state.appRootApi + admin.Avatar" class="img-avatar">
                  </div>
                  <div class="member-name">{{admin.UserName}}</div>
                  <div class="member-action ml-auto">
                    <b-button v-if="admin.UserID === currentUser.UserID" @click="changeMemberRole(admin.UserID, 2)" style="background: #f5f6f7">Gỡ vai trò quản trị viên</b-button>
                    <b-dropdown class="tab-header-icon action-more" no-caret right title="Thao tác" v-if="admin.UserID !== currentUser.UserID">
                      <template v-slot:button-content>
                        <span class="d-flex align-items-center"><i class="fa fa-ellipsis-h" style="color: #999"></i></span>
                      </template>
                      <b-dropdown-item v-if="isAdmin" @click="changeMemberRole(admin.UserID, 2)">Gỡ vai trò quản trị viên</b-dropdown-item>
                      <b-dropdown-item @click="onShowChatMessage(admin)">Nhắn tin</b-dropdown-item>
                    </b-dropdown>
                  </div>

                </li>
              </ul>
            </b-tab>
          </b-tabs>
        </b-card>
      </div>
    </b-modal>

  </div>
</template>
<style type="text/css">
  .chat-tab {
    width: 310px;
    height: 400px;
    background: #fff;
    box-shadow: 0 1px 4px rgba(0, 0, 0, .3);
    display: flex;
    flex-direction: column;
    position: relative;
  }
  .chat-content {
    scroll-behavior: smooth;
  }
  .chat-tab .tab-header {
    z-index: 9;
  }
  .chat-tab .tab-body {
    position: relative;
  }
  .chat-tab .spinners {
    /*position: absolute;*/
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9;
    background: #fff;
  }
  .chat-tab i {
    font-size: 18px;
    color: #999;
  }
  .chat-tab i:hover{
    color: #73818f;
  }
  .chat-tab .img-block {
    width: 28px;
    height: 28px;
  }
  .chat-bar {
    position: relative;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, .10);
  }
  .chat-tabs .tab-body {
    flex-grow: 1;
    overflow-y: auto;
  }
  .chat-tabs .tab-footer {
    border-top: 1px solid rgba(0, 0, 0, .10);
    padding-bottom: 5px;
  }
  .chat-tabs .tab-footer i{
    cursor: pointer;
  }

  /*minimize*/
  .tab-minimize.chat-tab {
    height: 44px;
    align-self: flex-end;
  }

  .status-seen {
    font-size: 11px;
  }
  .status-seen i {
    font-size: 11px;
  }
  .area-auto-resize {
    width: 100%;
    min-height: 29px;
    overflow: hidden;
    padding: .25rem .5rem;
  }
  .img-block {
    position: relative;
  }
  .img-block .chat-icon-active {
    background: rgb(66, 183, 42);
    border-radius: 50%;
    display: inline-block;
    height: 8px;
    margin-left: 4px;
    width: 8px;
    position: absolute;
    bottom: 2px;
    right: 2px;
  }
  .message-typing .message-input{
    border: none;
    /*height: 29px;*/
    height: auto;
    min-height: 29px;
    max-height: 90px;
    overflow-y: auto;
    text-align: left;
    font-size: 1rem;
    white-space: pre-wrap;
  }
  .message-typing .message-input:focus {
    outline: none;
    box-shadow: none;
  }

  [contentEditable=true]:empty:not(:focus):before {
    content: attr(data-text);
    color: #999;
  }
  .message-content-entry span{
    display: inline-block;
    color: #050505;
    word-break: break-word;
  }
  .message-content .message-text {
    white-space: pre-wrap;
  }
  .message-content-entry .message-content .message-text{
    border-radius: 5px;
  }
  .message-content-entry .message-timer {
    font-size: 11px;
  }
  .message-content-entry .message-timer span {
    color: #8a8d91;
  }
  .message-content-entry .status-seen, .message-content-entry .status-seen span {
    color: #8a8d91;
  }
  .message-content-entry .message-content a{
    text-decoration: underline;
  }
  .message-content-entry .message-content {
    display: flex;
    align-items: center;
  }
  .message-right .message-content {
    flex-direction: row-reverse;
  }
  .message-content .message-text-wrap {
    text-align: left;
    display: inline-block;
  }
  .message-content .message-action-other {
    margin: 0 .25rem;
    visibility: hidden;
    opacity: 0;
    z-index: -1;
    flex-grow: 0;
    flex-shrink: 0;
  }
  .message-content .message-action-other.show {
    visibility: visible;
    opacity: 1;
    z-index: 1;
  }

  .message-popover span{
    cursor: pointer;
  }

  .message-content .message-action-other .btn {
    padding: 0 .25rem;
    line-height: normal;
    height: 18px;
    background: transparent;
    border: none;
  }
  .message-content .message-action-other .btn:focus,
  .message-content .message-action-other .btn:active {
    border: none;
    outline: none;
    box-shadow: none;
  }

  .message-content .message-icon-agree {
    background: #fff !important;
  }
  .message-content .message-icon-agree svg path {
    fill: #00a2e8;
  }

  .message-content span{
    max-width: 200px;
  }

  /*.message-mine .message-content span{*/
  /*  background: #cfd8dc;*/
  /*}*/
  .message-mine .message-content .message-text {
    background: #0084ff;
  }
  .message-mine .message-content .message-text span,
  .message-mine .message-content .message-text a{
    color: #fff;
  }
  .message-not-mine .message-content .message-text {
    background: #e4e6eb;
  }
  /*.message-not-mine .message-content span{*/
  /*  background: #f1f1f1;*/
  /*}*/
  .message-upload-preview {
    padding: .25rem .5rem;
    display: flex;
    overflow-y: auto;
    align-items: center;
  }
  .message-upload-preview .img-preview {
    height: 54px;
    width: 54px;
    min-width: 54px;
    position: relative;
  }
  .message-upload-preview .img-preview img {
    height: 100%;
    width: 100%;
    object-fit: cover;
  }
  .message-upload-preview .upload-item i.icon-remove {
    position: absolute;
    right: 5px;
    top: 0;
    cursor: pointer;
    display: none;
  }
  .message-upload-preview .upload-item:hover i.icon-remove{
    display: block;
  }
  .message-upload-preview .file-preview {
    display: flex;
    border-radius: 8px;
    height: 44px;
    width: 180px;
    background: #fff;
    border: 1px solid rgba(0, 0, 0, .15);
    position: relative;
  }
  .message-upload-preview .file-preview .file-left {
    height: 42px;
    flex: 0 0 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, .15);
    border-top-left-radius: 7px;
    border-bottom-left-radius: 7px;
  }
  .message-upload-preview .file-preview .file-right {
    overflow: hidden;
    text-align: left;
    padding: 0 .5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;

  }
  .message-upload-preview .file-preview .file-left i {
    font-size: 24px;
  }
  .message-upload-preview .file-preview span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: block;
    line-height: normal;
  }

  .message-feedback-preview {
    padding: .25rem .5rem;
    text-align: left;
    position: relative;
  }
  .message-feedback-preview .remove-feedback {
    position: absolute;
    top: 5px;
    right: 10px;
  }
  .message-feedback-preview .feedback-title {
    font-size: 11px;
  }
  .message-feedback-preview .feedback-title span {
    font-size: 12px;
    font-weight: 500;
  }
  .message-feedback-preview .feedback-text {
    font-size: 10px;
    color: rgba(0, 0, 0, .5);
  }

  .message-feedback blockquote {
    display: inline-block;
    position: relative;
    white-space: pre-wrap;
    padding-left: 10px;
    margin: 6px 0;
  }
  .message-feedback .message-feedback-text {
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
  }
  .message-feedback .quote-content span {
    color: #828282;
  }
  .message-feedback .quote-content {
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .message-feedback .quote-line {
    background-color: rgba(0, 0, 0, .3);
    border-radius: 1px;
    display: inline-block;
    margin-right: 10px;
    position: absolute;
    width: 2px;
    height: 100%;
    left: 0;
  }
  .message-content .message-feedback-info {
    font-size: 10px;
    color: rgba(0, 0, 0, .5);
  }
  .message-content .message-feedback-info span{
    color: rgba(0, 0, 0, .5);
  }
  .message-content .message-feedback-info i {
    font-size: 8px;
    color: rgba(0, 0, 0, .5);
  }

  .message-right .message-files{
    flex-direction: row-reverse;
  }
  .message-right .message-file {
    margin-left: .25rem;
  }
  .message-left .message-file {
    margin-right: .25rem;
  }
  .message-files .message-file {
    margin-bottom: .25rem;
    cursor: pointer;
  }
  .message-files .message-image {
    border: 1px solid #ccc;
    border-radius: 2px;
    /*height: 76px;*/
    /*width: 76px;*/
    overflow: hidden;
  }

  .message-files {
    display: flex;
    flex-wrap: wrap;
  }
  .message-files .img-block {
    display: block;
    width: 100%;
    height: 100%;
  }
  .message-files .img-block img {
    max-width: 100%;
    height: auto;
    object-fit: cover;
  }
  .message-files .message-application {
    flex: 1 1 100%;
  }
  .message-files .message-application .file-right{
    overflow: hidden;
  }

  .message-files .message-application span {
    background: #fff;
    color: #373737;
    display: inline-block;
    font-weight: bold;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 140px;
  }

  .message-files .message-video {
    height: auto;
    width: 100%;
  }
  .message-files .message-video .video-content {
    height: auto;
    width: 100%;
    max-height: 100%;
    max-width: 100%;
    overflow: hidden;
  }
  .message-files .message-video video {
    width: 100%;
    height: auto;
    border-radius: 10px;
  }
  .message-right .video-content {
    margin-left: auto;
  }
  .message-left .video-content {
    margin-right: auto;
  }

  .message-files .message-audio {
    width: 85%;
    min-width: 175px;
    overflow: hidden;
  }
  .message-files .message-audio audio {
    width: 100%;
    height: 32px;
    max-height: 32px;
  }

  .tab-header-title {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: inline-block;
    max-width: 140px;
    color: #151b1e;
    font-weight: 500;
    cursor: pointer;
  }
  .tab-header-icon .btn{
    background: transparent !important;
    border: none !important;
  }
  .tab-header-icon .btn:focus, .tab-header-icon .btn:active {
    box-shadow: none !important;
    border: none !important;
    background-color: transparent !important;
  }
  .tab-header-icon.settings .btn {
    padding: 0 .25rem !important;
  }
  .tab-header-icon.action-more .btn {
    padding: 0 .5rem !important;
  }

  .chat-icon-agree {
    display: flex;
    align-content: center;
    margin-top: -5px;
  }

  .collapse-search .btn, .collapse-edit-message .btn {
    background: transparent;
    border-radius: 0;
  }
  .collapse-search i {
    font-size: 1rem;
  }

  /* emoji */
  .container-emoji {
    /*width: 21px;*/
    /*position: absolute;*/
    /*top: 0;*/
    /*left: 0;*/
  }
  .container-emoji .emojionearea-button{
    display: none;
  }
  .emojionearea.emojionearea-inline>.emojionearea-button {
    top: -2px;
    right: 0;
  }
  .container-emoji .emojionearea {
     height: 21px;
     border: none;
     outline: none;
     box-shadow: none;
   }
  .emojioneemoji {
    font-size: inherit;
    height: 2ex;
    width: 2.1ex;
    min-height: 20px;
    min-width: 20px;
    display: inline-block;
    margin: -.2ex .15em .2ex;
    line-height: normal;
    vertical-align: middle;
    max-width: 100%;
    top: 0;
  }
  .modal-chat-member .tab-content {
    border: none;
  }

  #collapse-add-member .input-group-select2 {
    position: relative;
    -webkit-box-flex: 1;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    width: 1%;
    margin-bottom: 0;
  }
  #collapse-add-member .select2-selection {
    border-radius: 0;
  }
  #collapse-add-member .select2-selection__choice {
    margin-top: 2px !important;
    margin-bottom: 2px !important;
  }

  #collapse-add-member .select2-selection__rendered {
    display: inline-flex;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow-x: auto;
  }

  .message-action-member .message-content {
    background: #cfd8dc;

  }
  .message-action-member .message-text {
    width: 100%;
    display: flex;
    align-items: center;
  }
  .message-action-member i {
    color: rgba(0, 0, 0, .54);
  }

  .chat-tab .dropdown-divider {
    margin: 0.25rem 0;
  }

  .message-tag {
    font-weight: 600;
    color: #050505;
  }

  span.label.highlight {
    background: #E1ECF4;
    border: 1px dotted #39739d;
  }
  .tag-user {
    background: #E1ECF4;
    padding: 0 4px;
    border-radius: 4px;
  }

  /*font size message*/
  .message-font-size-sm .message-content .message-text span{
    font-size: .75rem;
  }
  .message-font-size-lg .message-content .message-text span{
    font-size: 1rem;
  }

  /*message edit*/
  .message-edit-input {
    border: 1px solid #ebebeb;
  }
  .message-edit-container .input-group-append {
    position: absolute;
    background: #fff;
    bottom: 0;
    left: 50%;
    transform: translate(-50%, 100%);
  }

  .chat-tab .chat-reload-message {
    width: 100%;
    height: 100%;
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .chat-tab .tab-footer {
    position: relative;
  }
  .chat-tab .tab-footer .chat-suggestion {
    position: fixed;
    left: 0;
    top: 0;
    transform: translateY(-100%);
    background: #fff;
    border-radius: 4px;
    box-shadow: 0 4px 8px 0 rgba(0,26,51,.1);
    border: 1px solid #e1e4ea;
  }

  .message-text-wrap.has-task-status .message-text {
    border-top-right-radius: 0;
  }
  .chat-tab .message-task-status {
    /*height: 15px;*/
  }
  .chat-tab .message-task-status .badge {
    cursor: pointer;
    display: inline-block;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
  }

</style>
<script>
  import ApiService from '@/services/api.service';
  import moment from 'moment';
  import ChatModalDatalist from "./partials/ChatModalDatalist";
  import Select2 from 'v-select2-component';
  import emojiJs from 'emojionearea/dist/emojionearea.min';
  import emojiCss from 'emojionearea/dist/emojionearea.min.css';
  import ChatSuggestion from "./partials/ChatSuggestion";
  import { position, offset } from 'caret-pos';
  import ChatTaskStatus from "./partials/ChatTaskStatus";

  moment.locale('vi');
  export default {
    name: 'chat-tab',
    data () {
      return {
        perPage: 15,
        page: 1,
        lastPage: 1,
        model: {
          message: '',
          search: '',
          searchAdvanced: '',
          categoryKey: [],
          groupName: this.tabChat.GroupName
        },
        messageArray: [],
        imagesPreview: [],
        filesPreview: [],
        messageFeedback: null,
        dragAndDropCapable: false,
        keyMessageAttach: null,

        members: [],
        admins: [],
        isAdmin: false,

        addMembersOption: [],
        membersAdded: [],

        messageEdit: null,

        searchSuggestion: '',
        suggested: null,
        suggestArray: [],
        range: null,

        stage: {
          showSearch: false,
          showSearchAdvanced: false,
          showAddMember: false,
          showEditMessage: false,
          loadingMessage: false,
          initialMessage: true,
          showIconSubmitMessage: false,
          showEditGroupName: false,
          loading: false,
          minimize: false,
          isSendingMessage: false,
          showReloadMessage: false,
          showSuggestion: false
        }
      }
    },
    props: {
      value: [Array, Object],
      tabChat: [Array, Object],
      currentUser: [Object, Array],
      allUsers: [Object, Array],
      membersGroups: [Object, Array],
      groups: [Object, Array],
      isMessenger: {
        type: Boolean,
        default: false
      }
    },
    components: {
      ChatModalDatalist,
      Select2,
      ChatSuggestion,
      ChatTaskStatus
    },
    computed:{
      imageExtension() {
        return ['gif', 'jpeg', 'jpg', 'png', 'ico', 'psd', 'ai'];
      }
    },
    filters: {
      filterMessage(value){
      }
    },
    beforeCreate() {},
    mounted() {
      let self = this;
      this.loadMessage();

      /*
        Determine if drag and drop functionality is capable in the browser
      */
      this.dragAndDropCapable = this.determineDragAndDropCapable();

      /*
        If drag and drop capable, then we continue to bind events to our elements.
      */
      if (this.dragAndDropCapable) {
        /*
          Listen to all of the drag events and bind an event listener to each
          for the fileform.
        */
        ['drag', 'dragstart', 'dragend', 'dragover', 'dragenter', 'dragleave', 'drop'].forEach( function( evt ) {
          /*
            For each event add an event listener that prevents the default action
            (opening the file in the browser) and stop the propagation of the event (so
            no other elements open the file in the browser)
          */
          this.$refs['chat-tab'].addEventListener(evt, function(e){
            e.preventDefault();
            e.stopPropagation();
          }.bind(this), false);
        }.bind(this));
      }

      // copy and paste image
      this.$el.querySelector('.message-typing .message-input').addEventListener('paste', function (e) {
        let clipboardData, pastedData;

        // Stop data actually being pasted into div
        e.stopPropagation();
        e.preventDefault();

        // Get pasted data via clipboard API
        clipboardData = e.clipboardData || window.clipboardData;
        pastedData = clipboardData.getData('Text');

        // Do whatever with pasteddata
        self.insertTextAtCaret(pastedData);
        // self.$el.querySelector('.message-typing .message-input').innerHTML += pastedData;
        // self.placeCaretAtEnd(self.$el.querySelector('.message-typing .message-input'));

        self.handlePasteImage(e);
      }, false);

      // set member
      if (!this.members.length) {
        this.members = _.filter(this.membersGroups, ['GroupID', this.tabChat.GroupID]);
        _.forEach(this.members, function (member, key) {
          let user = _.find(self.allUsers, ['UserID', member.UserID]);
          if (user) self.members[key].Avatar = user.Avata;
        });

        let index = _.findIndex(this.members, ['UserID', this.currentUser.UserID]);
        if (index > -1) {
          this.members[index].Avatar = this.currentUser.Avata;
          if (this.members[index].Type === 1) {
            this.isAdmin = true;
          }
        }
        this.members = _.uniqBy(this.members, 'UserID');
        // admin
        this.admins = _.filter(this.members, ['Type', 1]);
      }

      // Socket events
      socket.on('new message', (data) => {
        setTimeout(() => {
          self.$nextTick(() => {
            if (data.GroupID === self.tabChat.GroupID) {
              self.messageArray.push(data);
              self.scrollToEnd();
              self.$forceUpdate();
            }
          });
        });
      });

      socket.on('add members', (data) => {
        _.forEach(data.members, function (member, key) {
          let tmpObj = member;
          let user = _.find(self.allUsers, ['UserID', member.UserID]);
          if (user) {
            tmpObj.Avatar = user.Avata;
            if (!_.isObject(_.find(self.members, ['UserID', member.UserID]))) {
              self.members.push(tmpObj);
            }
          }
        });
        self.$forceUpdate();
      });

      socket.on('remove member', (data) => {
        _.remove(self.members, ['UserID', data.member.UserID]);
        self.$forceUpdate();
      });

      socket.on('edit message', (data) => {
        let indexEditMessage = _.findIndex(self.messageArray, ['LineID', data.LineID]);
        if (indexEditMessage > -1) {
          self.messageArray[indexEditMessage].Content = data.Content;
        }
        self.$forceUpdate();
      });

      socket.on('delete message', (data) => {
        _.remove(self.messageArray, ['LineID', data.LineID]);
        self.$forceUpdate();
      });

      socket.on('user left', (data) => {
        self.$store.commit('userOnline', {UserID: data.UserID, value: false});
        self.$forceUpdate();
      });

      socket.on('user joined', (data) => {
        let usersConnected = data.usersConnected;
        self.$store.commit('userOnline', {});
        _.forEach(usersConnected, function (userOnline, key) {
          self.$store.commit('userOnline', {UserID: userOnline.UserID, value: true});
        });
        self.$forceUpdate();
      });

    },
    methods: {
      loadMessage(){
        if (!this.tabChat.GroupID) return;

        // trigger focus in input chat
        // let messageArea = this.$el.querySelector('.message-typing textarea');
        // messageArea.focus();

        let self = this;
        let tabBody = self.$el.querySelector('.tab-body');
        let olderHeight = tabBody.scrollHeight;
        let requestData = {
          method: 'post',
          url: 'extensions/api/chat/load-message',
          data: {
            GroupID: this.tabChat.GroupID,
            per_page: this.perPage,
            page: this.page,
            search: this.model.search,
            CategoryKey: this.model.categoryKey
          }
        };
        this.stage.loading = true;
        this.stage.showReloadMessage = false;
        // this.$emit('on:user-read');
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.perPage = responsesData.data.data.per_page;
            self.lastPage = responsesData.data.data.last_page;
            self.page = responsesData.data.data.current_page;
            if (responsesData.data.data.data) {

              _.forEach(responsesData.data.data.data, function (message, key) {
                if (responsesData.data.ContentFile.length) {
                  let contentFile = _.filter(responsesData.data.ContentFile, ['ChatContentID', message.LineID]);
                  responsesData.data.data.data[key].ContentFile = contentFile;
                }

                if (!responsesData.data.data.data[key].Content) {
                  responsesData.data.data.data[key].Content = '';
                }

                if (responsesData.data.ContentParent.length) {
                  let contentFileParent = _.filter(responsesData.data.ContentFileParent, ['ChatContentID', message.ParentID]);
                  let contentParent = _.find(responsesData.data.ContentParent, ['LineID', message.ParentID]);
                  if (contentParent) {
                    contentParent.ContentFile = contentFileParent;
                    responsesData.data.data.data[key].ContentParent = contentParent;
                  }
                }

                let datalist = [];
                if (message.CategoryKey) {
                  let categories = message.CategoryKey.split('_');
                  _.forEach(categories, function (category, key) {
                    let pieces = category.split(':');
                    let table = pieces[0];
                    let tableID = pieces[1];
                    let tableName = table.charAt(0).toUpperCase() + table.slice(1);
                    let tmpObj = {};
                    if (table !== 'tag') {
                      let categoryExist = _.find(responsesData.data.Datalist[table], [tableName + 'ID', Number(tableID)]);
                      if (categoryExist) {
                        tmpObj.LinkID = tableID;
                        tmpObj.LinkNo = categoryExist[tableName + 'No'];
                        tmpObj.LinkName = categoryExist[tableName + 'Name'];
                        tmpObj.DatalistTable = table;

                        switch (table) {
                          case 'task':
                            tmpObj.LinkTableName = 'Công việc';
                            tmpObj.Status = categoryExist.Status;
                            tmpObj.StatusID = categoryExist.StatusID;
                            tmpObj.StatusName = categoryExist.StatusName;
                            tmpObj.StatusValue = categoryExist.StatusValue;
                            tmpObj.StatusDescription = categoryExist.StatusDescription;
                            tmpObj.Type = categoryExist.Type;
                            tmpObj.ParentID = categoryExist.ParentID;
                            tmpObj.ParentNo = categoryExist.ParentNo;
                            tmpObj.ParentName = categoryExist.ParentName;
                            break;
                          case 'customer':
                            tmpObj.LinkTableName = 'Khách hàng';
                            break;
                          case 'project':
                            tmpObj.LinkTableName = 'Dự án';
                            break;
                          case 'vendor':
                            tmpObj.LinkTableName = 'Nhà cung cấp';
                            break;
                          case 'item':
                            tmpObj.LinkTableName = 'Hàng hóa';
                            break;
                          case 'expense':
                            tmpObj.LinkTableName = 'Chi phí';
                            break;
                          case 'employee':
                            tmpObj.LinkTableName = 'Nhân viên';
                            break;
                          case 'company':
                            tmpObj.LinkTableName = 'Đơn vị';
                            break;
                          default:
                            break;
                        }
                        datalist.push(tmpObj);
                      }
                    } else {
                      datalist.push({
                        LinkTableName: 'Tag',
                        LinkID: null,
                        LinkNo: null,
                        LinkName: tableID,
                        DatalistTable: 'tag'
                      });
                    }

                  });
                }
                message.Datalist = datalist;
              });

              self.messageArray = _.concat(_.reverse(responsesData.data.data.data), self.messageArray);
            }
            self.$nextTick(() => {
              tabBody.scrollTop = tabBody.scrollHeight - olderHeight;
            });
            if (self.page === 1) {
              self.scrollToEnd();
            }
            self.stage.loadingMessage = false;
            self.stage.loading = false;
          }
        }, (error) => {
          console.log(error);
          self.stage.loadingMessage = false;
          self.stage.loading = false;
          this.stage.showReloadMessage = true;
          Swal.fire({
            title: 'Thông báo',
            text: 'Không kết nối được với máy chủ',
            confirmButtonText: 'Đóng'
          });
        });
      },
      filterMessage(value){
        if (value.includes('#')) {
          let pieces = value.split(' ');
          _.forEach(pieces, function (tag, key) {
            if (tag.charAt(0) === '#') {
              let tagHtml = '<a href="#" class="message-tag message-tag-hashtag">' + tag + '</a>';
              value = value.replace(tag, tagHtml);
            }
          });
        }

        if (value.includes('@')) {
          let pieces = value.split(' ');
          _.forEach(pieces, function (tagUser, key) {
            if (tagUser.charAt(0) === '@') {
              let tagHtml = '<a href="#" class="message-tag message-tag-username">' + tagUser + '</a>';
              value = value.replace(tagUser, tagHtml);
            }
          });
        }

        return value;
      },
      onShowSearchMessage(){
        this.stage.showSearch = !this.stage.showSearch;
        this.$nextTick(() => {
          this.$el.querySelector('#collapse-search input').focus();
        });
      },
      handleSubmitSearch(){
        this.messageArray = [];
        this.page = 1;
        this.loadMessage();
      },
      onCloseSearch(){
        this.stage.showSearch = false;
        if (this.model.search) {
          this.model.search = '';
          this.handleSubmitSearch();
        }
      },
      onScrollMessage(e){
        if (!this.stage.loadingMessage && !this.stage.initialMessage) {
          let tabBody = this.$el.querySelector('.tab-body');
          if (tabBody.scrollTop < 10) {
            this.stage.loadingMessage = true;
            if (this.page < this.lastPage) {
              this.page += 1;
              this.loadMessage();
            }
          }
        }
      },
      scrollToEnd: function() {
        this.$nextTick(() => {
          let container = this.$el.querySelector('.tab-body');
          container.scrollTop = container.scrollHeight;
          this.stage.initialMessage = false;
        });
      },
      onReadMessage(){
        this.$emit('on:user-read');
      },
      handleSubmitMessage(e){

        this.model.message = this.$el.querySelector('.message-typing .message-input').innerHTML;
        this.model.message = this.urlify(this.model.message);
        e.preventDefault();
        if (!this.model.message && !this.imagesPreview.length && !this.filesPreview.length || (e.shiftKey && e.keyCode === 13)) return;

        if (this.stage.isSendingMessage) {
          return false;
        }
        this.stage.isSendingMessage = true;

        let self = this;
        let formData = new FormData();

        // file Word, PowerPoint, PDF, Txt
        // let isLargeFile = false;
        _.forEach(this.filesPreview, function (file, key) {
          formData.append('Files[]', file);
          // if (file.size > 10000000) {
          //   isLargeFile = true;
          // }
        });

        // file images
        _.forEach(this.imagesPreview, function (image, key) {
          formData.append('Files[]', image);
        });

        // feedback
        if (this.messageFeedback) {
          formData.append('ParentID', this.messageFeedback.LineID);
        }

        formData.append('GroupID', this.tabChat.GroupID);
        formData.append('GroupType', this.tabChat.GroupType);
        formData.append('message', this.model.message);
        if (this.tabChat.GroupType === 1) {
          formData.append('UserID', this.tabChat.UserID);
          formData.append('UserName', this.tabChat.GroupName);
        }

        let requestData = {
          method: 'post',
          url: 'extensions/api/chat/store-message',
          data: formData
        };

        // if (isLargeFile) {}
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {

            if (responsesData.data.Group) {
              responsesData.data.Group.GroupType = Number(responsesData.data.Group.GroupType);
              self.$emit('on:update-group', responsesData.data.Group);
            }

            if (responsesData.data.data) {
              let message = responsesData.data.data;
              if (responsesData.data.ContentFile.length) {
                message.ContentFile = responsesData.data.ContentFile;
              }

              if (!message.ParentID) {
                message.LineIDPost = null;
                message.LineIDComment = null;
              }else {
                if (!self.messageFeedback.ParentID) {
                  message.LineIDPost = self.messageFeedback.LineID;
                  message.LineIDComment = null;
                } else {
                  message.LineIDPost = self.messageFeedback.ParentID;
                  message.LineIDComment = self.messageFeedback.LineID;
                }
              }

              if (self.messageFeedback) {
                message.ContentParent = self.messageFeedback;
              }

              self.imagesPreview = [];
              self.$el.querySelector('#upload-images').value = '';
              self.filesPreview = [];
              self.$el.querySelector('#upload-files').value = '';
              self.messageFeedback = null;

              message.GroupType = self.tabChat.GroupType;
              self.messageArray.push(message);
              let messageData = {
                Content: message,
                GroupType: self.tabChat.GroupType,
                GroupID: self.tabChat.GroupID,
                ContentFile: responsesData.data.ContentFile
              };
              if (self.tabChat.GroupType === 1) {
                messageData.UserID = self.tabChat.UserID;
                messageData.UserName = self.tabChat.UserName;
              }

              socket.emit('new message', messageData);
              self.$emit('on:new-message', message);
            }

            self.model.message = '';
            self.stage.showIconSubmitMessage = false;
            self.$el.querySelector('.message-typing .message-input').innerHTML = '';
            self.scrollToEnd();
          }else {
            this.$bvToast.toast(responsesData.msg, {
              title: 'Thông báo',
              variant: 'warning'
            });
          }
          self.stage.isSendingMessage = false;
          self.$nextTick(() => {
            self.$el.querySelector('.message-typing .message-input').focus();
          });
        }, (error) => {
          console.log(error);
          self.stage.isSendingMessage = false;
          Swal.fire({
            title: 'Thông báo',
            text: 'Không kết nối được với máy chủ',
            confirmButtonText: 'Đóng'
          });
        });

      },
      handleFeedbackMessage(key){
        this.$root.$emit('bv::hide::popover');

        if (this.tabChat.GroupType === 3) {
          if (!this.messageArray[key].ParentID) {
            this.messageFeedback = this.messageArray[key];
          } else {
            let messageParent = _.find(this.messageArray, ['LineID', this.messageArray[key].ParentID]);
            if (messageParent) {
              this.messageFeedback = messageParent;
            } else {
              let self = this;
              let requestData = {
                method: 'post',
                url: 'extensions/api/chat/get-message',
                data: {
                  LineID: this.messageArray[key].ParentID
                }
              };

              ApiService.setHeader();
              ApiService.customRequest(requestData).then((responses) => {
                let responsesData = responses.data;
                if (responsesData.status === 1) {
                  self.messageFeedback = responsesData.data;
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
          }
        } else {
          this.messageFeedback = this.messageArray[key];
        }
        this.$el.querySelector('.message-typing .message-input').focus();
      },
      handleDeleteMessage(key){

        Swal.fire({
          title: 'Xác nhận',
          text: 'Xóa bài viết',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Đồng ý',
          cancelButtonText: 'Hủy bỏ'
        }).then((result) => {
          if (result.value) {
            let LineID = this.messageArray[key].LineID;
            if (!LineID) {
              this.$bvToast.toast('Nội dung không tồn tại', {
                title: 'Thông báo',
                variant: 'warning'
              });
              return;
            }

            let self = this;
            let requestData = {
              method: 'post',
              url: 'extensions/api/chat/delete-message',
              data: {
                LineID: LineID
              }
            };

            ApiService.setHeader();
            ApiService.customRequest(requestData).then((responses) => {
              let responsesData = responses.data;
              if (responsesData.status === 1) {
                self.messageArray.splice(key, 1);
                let comment = responsesData.data;
                if (comment.ParentID) {
                  if (comment.GrandParentID) {
                    comment.LineIDPost = comment.GrandParentID;
                    comment.LineIDComment = comment.ParentID;
                    comment.LineIDReply = comment.LineID;
                  } else {
                    comment.LineIDPost = comment.ParentID;
                    comment.LineIDComment = comment.LineID;
                    comment.LineIDReply = null;
                  }
                } else {
                  comment.LineIDPost = comment.LineID;
                  comment.LineIDComment = null;
                  comment.LineIDReply = null;
                }

                let socketData = {
                  Content: comment,
                  GroupID: comment.GroupID,
                  GroupType: comment.GroupType,
                  UserID: self.tabChat.UserID
                };

                socket.emit('delete message', socketData);
                this.$bvToast.toast('Cập nhật thành công', {
                  title: 'Thông báo',
                  variant: 'success'
                });
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
      onShowEditMessage(key){
        let self = this;
        this.stage.showEditMessage = true;
        this.messageEdit = this.messageArray[key];
        this.$nextTick(() => {
          if (self.$el.querySelector('.message-edit-input')) {
            self.$el.querySelector('.message-edit-input').focus();
            self.placeCaretAtEnd(self.$el.querySelector('.message-edit-input'));
          }
        });

      },
      onCloseEditMessage() {
        this.stage.showEditMessage = false;
        this.messageEdit = null;
      },
      handleSubmitEditMessage(e) {
        let self = this;
        let contentEdit = this.$el.querySelector('.message-edit-input').innerHTML;
        contentEdit = this.urlify(contentEdit);
        e.preventDefault();
        if ((e.shiftKey && e.keyCode === 13)) return;
        if (!this.messageEdit) {
          this.stage.showEditMessage = false;
          return;
        }

        if (this.stage.isSendingMessage) {
          return false;
        }
        this.stage.isSendingMessage = true;
        let formData = new FormData();

        formData.append('message', contentEdit);
        formData.append('LineID', this.messageEdit.LineID);

        let requestData = {
          method: 'post',
          url: 'extensions/api/chat/update-message',
          data: formData
        };

        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            let messageEdit = responsesData.data.data;
            messageEdit.GroupType = self.tabChat.GroupType;
            let indexMessageEdit = _.findIndex(self.messageArray, ['LineID', self.messageEdit.LineID]);
            if (indexMessageEdit > -1) {
              self.messageArray[indexMessageEdit].Content = messageEdit.Content;
              this.$bvToast.toast('Cập nhật thành công', {
                title: 'Thông báo',
                variant: 'success'
              });
              self.messageEdit = null;
              self.stage.showEditMessage = false;
            }

            let socketData = {
              Content: messageEdit,
              GroupID: messageEdit.GroupID,
              GroupType: messageEdit.GroupType,
              UserID: self.tabChat.UserID
            };

            socket.emit('edit message', socketData);

          }else {
            this.$bvToast.toast('Cập nhật không thành công', {
              title: 'Thông báo',
              variant: 'warning'
            });
          }
          self.stage.isSendingMessage = false;
        }, (error) => {
          console.log(error);
          Swal.fire({
            title: 'Thông báo',
            text: 'Không kết nối được với máy chủ',
            confirmButtonText: 'Đóng'
          });
          self.stage.isSendingMessage = false;
        });
      },
      placeCaretAtEnd(el) {
        el.focus();
        if (typeof window.getSelection != "undefined"
          && typeof document.createRange != "undefined") {
          let range = document.createRange();
          range.selectNodeContents(el);
          range.collapse(false);
          let sel = window.getSelection();
          sel.removeAllRanges();
          sel.addRange(range);
        } else if (typeof document.body.createTextRange != "undefined") {
          let textRange = document.body.createTextRange();
          textRange.moveToElementText(el);
          textRange.collapse(false);
          textRange.select();
        }
      },
      onKeyupMessage(e) {
        if (e.shiftKey && e.keyCode === 13) {
          e.preventDefault(); //Prevent default browser behavior
          if (window.getSelection) {
            let selection = window.getSelection(),
              range = selection.getRangeAt(0),
              br = document.createElement("br"),
              textNode = document.createTextNode(''); //Passing " " directly will not end up being shown correctly
            range.deleteContents();//required or not?
            range.insertNode(br);
            range.collapse(false);
            range.insertNode(textNode);
            range.selectNodeContents(textNode);

            selection.removeAllRanges();
            selection.addRange(range);
            return false;
          }
        }
        if (e.keyCode !== 13) {
          let message = this.$el.querySelector('.message-typing .message-input').innerHTML;
          if (message) {
            this.stage.showIconSubmitMessage = true;
          } else {
            this.stage.showIconSubmitMessage = false;
          }
        }
        // tag user
        if (this.tabChat.GroupType !== 1) {
          let $messageInput = $(this.$el.querySelector('.message-typing .message-input'));
          let caretPosition = position(this.$el.querySelector('.message-typing .message-input'));
          let textContent = $messageInput.text();
          textContent = textContent.substring(0, caretPosition.pos);
          textContent = textContent.split(' ');
          let lastWord = textContent[textContent.length - 1];

          this.searchSuggestion = lastWord;
          if (this.searchSuggestion && !this.searchSuggestion.includes('@')) {
            this.stage.showSuggestion = false;
            this.searchSuggestion = '';
          }

          if ((this.searchSuggestion && this.searchSuggestion.includes('@')) || e.key === '@' || (e.ctrlKey && e.keyCode === 32)) {
            this.stage.showSuggestion = true;
            this.saveSelection();
            this.setSearchSuggestion(e);
          }else {
            if (e.key !== 'Shift' && e.key !== 'ArrowDown' && e.key !== 'ArrowUp') {
              this.stage.showSuggestion = false;
              this.searchSuggestion = '';
            }
          }
          if (e.key === ' ') {
            this.stage.showSuggestion = false;
            this.searchSuggestion = '';
          }
        }
      },
      getCaretPosition(editableDiv) {
        let caretPos = 0,
          sel, range;
        if (window.getSelection) {
          sel = window.getSelection();
          if (sel.rangeCount) {
            range = sel.getRangeAt(0);
            if (range.commonAncestorContainer.parentNode == editableDiv) {
              caretPos = range.endOffset;
            }
          }
        } else if (document.selection && document.selection.createRange) {
          range = document.selection.createRange();
          if (range.parentElement() == editableDiv) {
            let tempEl = document.createElement("span");
            editableDiv.insertBefore(tempEl, editableDiv.firstChild);
            let tempRange = range.duplicate();
            tempRange.moveToElementText(tempEl);
            tempRange.setEndPoint("EndToEnd", range);
            caretPos = tempRange.text.length;
          }
        }
        return caretPos;
      },
      getCaretPixelPos($node, offsetx, offsety){
        offsetx = offsetx || 0;
        offsety = offsety || 0;

        let nodeLeft = 0,
          nodeTop = 0;
        if ($node){
          nodeLeft = $node.offsetLeft;
          nodeTop = $node.offsetTop;
        }

        let pos = {left: 0, top: 0};

        if (document.selection){
          let range = document.selection.createRange();
          pos.left = range.offsetLeft + offsetx - nodeLeft + 'px';
          pos.top = range.offsetTop + offsety - nodeTop + 'px';
        }else if (window.getSelection){
          let sel = window.getSelection();
          let range = sel.getRangeAt(0).cloneRange();
          try{
            range.setStart(range.startContainer, range.startOffset-1);
          }catch(e){}
          let rect = range.getBoundingClientRect();
          if (range.endOffset == 0 || range.toString() === ''){
            // first char of line
            if (range.startContainer == $node){
              // empty div
              if (range.endOffset == 0){
                pos.top = '0px';
                pos.left = '0px';
              }else{
                // firefox need this
                let range2 = range.cloneRange();
                range2.setStart(range2.startContainer, 0);
                let rect2 = range2.getBoundingClientRect();
                pos.left = rect2.left + offsetx - nodeLeft + 'px';
                pos.top = rect2.top + rect2.height + offsety - nodeTop + 'px';
              }
            }else{
              pos.top = range.startContainer.offsetTop + 'px';
              pos.left = range.startContainer.offsetLeft + 'px';
            }
          }else{
            pos.left = rect.left + rect.width + offsetx - nodeLeft + 'px';
            pos.top = rect.top + offsety - nodeTop + 'px';
          }
        }
        return pos;
      },
      setSearchSuggestion(e) {
        let $messageInput = $(this.$el.querySelector('.message-typing .message-input'));
        if (this.searchSuggestion.includes('@') || e.key === '@' || (e.ctrlKey && e.keyCode === 32)) {
          let posCaret = this.getCaretPixelPos($messageInput[0]);
          this.$nextTick(() => {
            $(this.$el.querySelector('.chat-tab .chat-suggestion')).css({
              left: posCaret.left,
              top: posCaret.top
            });
          });
        }

        if (e.key === 'Backspace') {
          if (this.searchSuggestion) {
            this.searchSuggestion = this.searchSuggestion.slice(0, -1);
          }
        }
        this.getSuggestionArray();
      },
      getSuggestionArray(){
        let self = this;
        let searchSuggestion = this.searchSuggestion.replace('@', '');
        self.suggestArray = [];
        let members = _.filter(this.membersGroups, ['GroupID', this.tabChat.GroupID]);
        _.forEach(members, function (member, key) {
          let tmpMember = {};
          tmpMember.Avatar = (member.Avatar) ? member.Avatar : ((member.Avata) ? member.Avata : '');
          tmpMember.UserName = member.UserName;
          tmpMember.UserID = member.UserID;
          self.suggestArray.push(tmpMember);
        });

        if (searchSuggestion) {
          this.suggestArray = _.filter(this.suggestArray, function (o) {
            let noAccent = __.cleanAccents(o.UserName);
            let groupNameLower = _.toLower(o.UserName);
            let noAccentLower = _.toLower(noAccent);

            return o.UserName.includes(searchSuggestion) || noAccent.includes(searchSuggestion)
              || groupNameLower.includes(searchSuggestion) || noAccentLower.includes(searchSuggestion);
          });
          if (!this.suggestArray.length) {
            this.stage.showSuggestion = false;
          }
        }
      },
      saveSelection() {
        if (window.getSelection) {
          let sel = window.getSelection();
          if (sel.getRangeAt && sel.rangeCount) {
            // return sel.getRangeAt(0);
            this.range = sel.getRangeAt(0);
          }
        } else if (document.selection && document.selection.createRange) {
          // return document.selection.createRange();
          this.range = document.selection.createRange();
        }
        return null;
      },
      restoreSelection() {
        let range = this.range;
        if (range) {
          if (window.getSelection) {
            let sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
          } else if (document.selection && range.select) {
            range.select();
          }
        }
      },
      onKeydownSearchSuggest(e){
        if (this.stage.showSuggestion) {
          let code = e.which;
          if (code === 40) {
            e.preventDefault();
            if (!$('.chat-tab .suggest-item.active').length) {
              $('.chat-tab .suggest-item').first().addClass('active');
            } else {
              let $currentActive = $('.chat-tab .suggest-item').filter('.active');
              $currentActive.removeClass('active');
              $currentActive.next().addClass('active');
            }

          } else if (code === 38) {
            e.preventDefault();
            // up here
            if (!$('.chat-tab .suggest-item.active').length) {
              $('.chat-tab .suggest-item').last().addClass('active');
            } else {
              let $currentActive = $('.chat-tab .suggest-item').filter('.active');
              $currentActive.removeClass('active');
              $currentActive.prev().addClass('active');
            }
          }else if (code === 13) {
            if (this.suggestArray.length) {
              $('.chat-tab .suggest-item.active').trigger('click');
              e.preventDefault();
              e.stopPropagation();
            }
          }

          let $suggestActive = $('.component-chat-suggestion li.active');
          if ($suggestActive.length) {
            $('.component-chat-suggestion .scroll-area').scrollTop(($suggestActive.index() * ($suggestActive.height() / 2)));
          }
        }
      },
      onChatSuggested(suggested) {
        if (this.range) {
          this.restoreSelection();
        }
        let content = $(this.$el.querySelector('.message-typing .message-input')).html();
        if (!this.searchSuggestion) {
          this.searchSuggestion = '@';
        }
        content = this.replacerTextAtCaret(this.searchSuggestion, ' ');
        let tag = '<span class="message-tag message-tag-username">' + suggested.UserName + '</span> ';
        this.insertHtmlAtCaret(tag);

        this.stage.showSuggestion = false;
        this.searchSuggestion = '';
      },
      onMouseenterMessage(e, key){
        $(e.target).find('.message-action-other').addClass('show');
      },
      onMouseleaveMessage(e, key){
        let $relatedTarget = $(e.relatedTarget);
        if (!$relatedTarget.hasClass('b-popover') && !$relatedTarget.closest('.b-popover').length) {
          $(e.target).find('.message-action-other').removeClass('show');
        }
      },
      onRemoveTabChat(){
        if (window.innerWidth < 768) {
          $('.chat-messages-mobile').addClass('show-sidebar');
        } else {
          this.$emit('on:remove-tab-chat');
        }
      },
      getLastName(UserID){
        let user = null;
        if (UserID === this.currentUser.UserID) {
          user = this.currentUser;
        }else {
          user = _.find(this.allUsers, ['UserID', UserID]);
        }
        if (user) {
          let res = user.FullName.split(' ');
          return res[res.length - 1];
        }
        return '';
      },
      getUserSeen() {
        let userSeen = '';
        let self = this;
        _.forEach(this.tabChat.userSeen, function (user, key) {
          if (user.UserID === self.currentUser.UserID) return;
          let lastName = self.getLastName(user.UserID);
          userSeen += lastName;
          if (key !== (self.tabChat.userSeen.length - 1)) {
            userSeen += ', ';
          }
        });
        return userSeen;
      },
      showMessageTime(key){
        if (this.tabChat.GroupType === 1) {
          let dateTime = moment(this.messageArray[key].CreatedDate);
          let dateTimePre = (this.messageArray[key - 1]) ? moment(this.messageArray[key].CreatedDate) : null;

          if (!dateTimePre) {
            return true;
          }else {
            if (dateTime.format('L') !== dateTimePre.format('L')) {
              return true;
            }else {
              let timeStamp = new Date(this.messageArray[key].CreatedDate).getTime();
              let timeStampPre = new Date(this.messageArray[key - 1].CreatedDate).getTime();
              if ((timeStamp - timeStampPre)/60000 >= 30) {
                return true;
              } else {
                return false;
              }
            }
          }
        }
        return true;
      },
      formatMessageTime(key){
        let dateTime = moment(this.messageArray[key].CreatedDate);
        if (dateTime.format('L') === moment().format('L')) {
          return dateTime.format('LT');
        }
        if (dateTime.year() === moment().year()) {
          if ((moment().format('x') - dateTime.format('x')) / (60000 * 24 * 60) < 7) {
            return dateTime.format('HH:mm, dddd');
          } else {
            return dateTime.format('HH:mm, DD/MM');
          }
        } else {
          return dateTime.format('HH:mm, MM/DD/YYYY');
        }
      },
      uploadImages(){
        let files = this.$refs['upload-images'].files;
        let self = this;
        if (!files.length) return;

        // this.imagesPreview = [];
        _.forEach(files, function (file, key) {
          if (file.type.includes('image')) {
            let reader = new FileReader();
            reader.onload = (e) => {
              let tmpObj = file;
              tmpObj.src = e.target.result;
              tmpObj.fileType = 1;
              self.imagesPreview.push(tmpObj);
            };
            reader.readAsDataURL(file);
          }
        });
      },
      uploadFiles(type){
        let files = [];
        if (type === 'file') {
          files = this.$refs['upload-files'].files;
        }

        if (type === 'audio-video') {
          files = this.$refs['upload-audios-videos'].files;
        }
        let self = this;
        if (!files.length) return;
        _.forEach(files, function (file, key) {
          if (file.type.includes('application') || !file.type.includes('image')) {
            let reader = new FileReader();
            reader.onload = (e) => {
              let tmpObj = file;

              let name = file.name;
              let lastDot = name.lastIndexOf('.');
              let fileName = name.substring(0, lastDot);
              let ext = name.substring(lastDot + 1);

              tmpObj.src = e.target.result;
              tmpObj.fileName = fileName;
              tmpObj.ext = ext;

              if (file.type.includes('application') || ['pptx', 'ppt', 'pps', 'xls', 'xlsx', 'csv', 'doc', 'docx', 'pdf', 'txt', 'zip', 'rar', 'cif'].includes(file.ext)) {
                tmpObj.fileType = 2;
              }else if (file.type.includes('audio')) {
                tmpObj.fileType = 3;
              }else if (file.type.includes('video')) {
                tmpObj.fileType = 4;
              }
              if (tmpObj.fileType) {
                self.filesPreview.push(tmpObj);
              } else {
                this.$bvToast.toast('Không hỗ trợ định dạng tệp này', {
                  title: 'Thông báo',
                  variant: 'warning'
                });
              }
            };
            reader.readAsDataURL(file);
          }
        });
      },
      onClickUploadImages(){
        let uploadImages = this.$el.querySelector('#upload-images');
        uploadImages.click();
      },
      onClickUploadFiles(type){
        if (type === 'file') {
          let uploadFiles = this.$el.querySelector('#upload-files');
          uploadFiles.click();
        }
        if (type === 'audio-video') {
          let uploadAudiosVideos = this.$el.querySelector('#upload-audios-videos');
          uploadAudiosVideos.click();
        }
      },
      onRemoveUpload(key, type){
        if (type === 'image') {
          this.imagesPreview.splice(key, 1);
        }
        if (type === 'file') {
          this.filesPreview.splice(key, 1);
        }
        this.$forceUpdate();
      },
      determineDragAndDropCapable(){
        /*
          Create a test element to see if certain events
          are present that let us do drag and drop.
        */
        let div = document.createElement('div');

        /*
          Check to see if the `draggable` event is in the element
          or the `ondragstart` and `ondrop` events are in the element. If
          they are, then we have what we need for dragging and dropping files.

          We also check to see if the window has `FormData` and `FileReader` objects
          present so we can do our AJAX uploading
        */
        return ( ( 'draggable' in div )
          || ( 'ondragstart' in div && 'ondrop' in div ) )
          && 'FormData' in window
          && 'FileReader' in window;
      },
      onDropFiles(e){
        // Capture the files from the drop event and add them to our local files array.
        let self = this;
        for( let i = 0; i < e.dataTransfer.files.length; i++ ){
          let file = e.dataTransfer.files[i];
          // add to image preview
          let reader = new FileReader();
          reader.onload = (e) => {
            let tmpObj = file;
            tmpObj.src = e.target.result;
            if (file.type.includes('image')) {
              tmpObj.fileType = 1;
              self.imagesPreview.push(tmpObj);
            } else {
              let name = file.name;
              let lastDot = name.lastIndexOf('.');
              let fileName = name.substring(0, lastDot);
              let ext = name.substring(lastDot + 1);
              tmpObj.fileName = fileName;
              tmpObj.ext = ext;

              if (file.type.includes('application') || ['pptx', 'ppt', 'pps', 'xls', 'xlsx', 'csv', 'doc', 'docx', 'pdf', 'txt', 'zip', 'rar', 'cif'].includes(file.ext)) {
                tmpObj.fileType = 2;
              }else if (file.type.includes('audio')) {
                tmpObj.fileType = 3;
              }else if (file.type.includes('video')) {
                tmpObj.fileType = 4;
              }
              if (tmpObj.fileType) {
                self.filesPreview.push(tmpObj);
              } else {
                this.$bvToast.toast('Không hỗ trợ định dạng tệp này', {
                  title: 'Thông báo',
                  variant: 'warning'
                });
              }
            }
          };
          reader.readAsDataURL(file);
        }
      },
      handlePasteImage(e){
        let self = this;
        // Handle the event
        this.retrieveImageFromClipboardAsBlob(e, function (imageBlob) {
          // If there's an image, display it in the canvas
          if (imageBlob) {
            // Crossbrowser support for URL
            let URLObj = window.URL || window.webkitURL;

            // Creates a DOMString containing a URL representing the object given in the parameter
            // namely the original Blob
            let file = imageBlob;
            file.src = URLObj.createObjectURL(file);
            self.imagesPreview.push(file);
          }
        });
      },
      retrieveImageFromClipboardAsBlob(pasteEvent, callback) {
        if (pasteEvent.clipboardData == false) {
          if (typeof(callback) == "function") {
            callback(undefined);
          }
        }

        let items = pasteEvent.clipboardData.items;

        if (items == undefined) {
          if (typeof(callback) == "function") {
            callback(undefined);
          }
        }

        for (let i = 0; i < items.length; i++) {
          // Skip content if not image
          if (items[i].type.indexOf("image") == -1) continue;
          // Retrieve image on clipboard as blob
          let blob = items[i].getAsFile();

          if (typeof(callback) == "function") {
            callback(blob);
          }
        }
      },
      onToggleEmoji(){
        let self = this;
        if (!this.$el.querySelector('.emojionearea')) {
          // emoji
          $(this.$el.querySelector('.emojionearea-input')).emojioneArea({
            search: false,
            // container: '#container-emoji-' + self.tabChat.GroupID,
            autocomplete: false,
            inline: true,
            saveEmojisAs: 'image',
            filtersPosition: 'bottom',
            // hidePickerOnBlur: true,
            events: {
              change(editor, event) {
              },
              'picker.click'(picker) {
                let $message = self.$el.querySelector('.message-typing .message-input');
                $message.innerHTML += this.getText();
                self.$el.querySelector('.emojionearea-editor').innerHTML = '';
                // this.hidePicker();
              },
              click(editor, event) {
              },
              ready() {
                this.showPicker();
              }
            }
          });
        } else {
          if ($(this.$el.querySelector('.emojionearea-picker')).hasClass('hidden')) {
            $(this.$el.querySelector('.emojionearea-button-open')).trigger('click');
          } else {
            $(this.$el.querySelector('.emojionearea-button-close')).trigger('click');
          }
        }

      },
      onClickIconAgree(){
        let self = this;
        let formData = new FormData();
        let message = ':sb-agree';

        formData.append('GroupID', this.tabChat.GroupID);
        formData.append('GroupType', this.tabChat.GroupType);
        formData.append('message', message);
        if (this.tabChat.GroupType === 1) {
          formData.append('UserID', this.tabChat.UserID);
          formData.append('UserName', this.tabChat.GroupName);
        }

        let requestData = {
          method: 'post',
          url: 'extensions/api/chat/store-message',
          data: formData
        };

        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            if (responsesData.data.data) {
              let message = responsesData.data.data;
              if (responsesData.data.ContentFile.length) {
                message.ContentFile = responsesData.data.ContentFile;
              }

              message.GroupType = self.tabChat.GroupType;
              self.messageArray.push(message);
              let messageData = {
                Content: message,
                GroupType: self.tabChat.GroupType,
                GroupID: self.tabChat.GroupID,
                ContentFile: responsesData.data.ContentFile
              };
              if (self.tabChat.GroupType === 1) {
                messageData.UserID = self.tabChat.UserID;
                messageData.UserName = self.tabChat.UserName;
              }
              socket.emit('new message', messageData);
              $('.last-content-' + responsesData.data.data.GroupID).html(responsesData.data.data.Content);
              self.$emit('on:new-message', responsesData.data.data.Content);
            }

            if (responsesData.data.Group) {
              self.$emit('on:update-group', responsesData.data.Group);
            }
            self.model.message = '';
            self.$el.querySelector('.message-typing .message-input').innerHTML = '';
            self.scrollToEnd();
          }
        }, (error) => {
          console.log(error);
          Swal.fire({
            title: 'Thông báo',
            text: 'Không kết nối được với máy chủ',
            confirmButtonText: 'Đóng'
          });
        });
      },
      scrollIntoView(e, LineID){
        // e.preventDefault();
        const href = '#message-' + LineID;
        const el = href ? this.$el.querySelector(href) : null;
        if (el) {
          this.$refs['chat-content'].scrollTop = el.offsetTop;
        }
      },
      handleAttachDataList(key){
        this.keyMessageAttach = key;
        this.$refs['chat-modal-datalist'].init();
      },
      handleSaveCategoryKey(datalist){
        if (!this.messageArray[this.keyMessageAttach]) return;
        let categoryKey = '';
        let self = this;
        if (datalist.length) {
          _.forEach(datalist, function (value, key) {
            if (value.DatalistTable !== 'tag') {
              categoryKey += value.DatalistTable + ':' + value.LinkID;
            } else {
              let tagName = value.LinkName;
              if (tagName.includes('#')) {
                categoryKey += value.DatalistTable + ':' + tagName;
              }
            }

            if (key !== (datalist.length - 1)) {
              categoryKey += '_';
            }

          });
        }

        if (categoryKey !== '') {
          let requestData = {
            method: 'post',
            url: 'extensions/api/chat/update-category-key',
            data: {
              LineID: this.messageArray[this.keyMessageAttach].LineID,
              CategoryKey: categoryKey
            }
          };

          ApiService.setHeader();
          ApiService.customRequest(requestData).then((responses) => {
            let responsesData = responses.data;
            if (responsesData.status === 1) {
              self.messageArray[this.keyMessageAttach].CategoryKey = categoryKey;
              self.messageArray[this.keyMessageAttach].Datalist = datalist;
              self.$forceUpdate();
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

      },
      onShowMember(){
        let self = this;
        let requestData = {
          method: 'post',
          url: 'extensions/api/chat/get-members',
          data: {
            GroupID: this.tabChat.GroupID
          }
        };

        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            if (responsesData.data) {
              self.members = responsesData.data;

              _.forEach(self.members, function (member, key) {
                let user = _.find(self.allUsers, ['UserID', member.UserID]);
                if (user) self.members[key].Avatar = user.Avata;
              });

              let index = _.findIndex(self.members, ['UserID', self.currentUser.UserID]);
              if (index > -1) {
                self.members[index].Avatar = self.currentUser.Avata;
                if (self.members[index].Type === 1) {
                  self.isAdmin = true;
                }
              }
              self.members = _.uniqBy(self.members, 'UserID');
              // admin
              self.admins = _.filter(self.members, ['Type', 1]);
            }
          }
        }, (error) => {
          console.log(error);
          Swal.fire({
            title: 'Thông báo',
            text: 'Không kết nối được với máy chủ',
            confirmButtonText: 'Đóng'
          });
        });
        this.$bvModal.show('modal-chat-member-' + this.tabChat.GroupID);
      },
      onShowAddMember(){
        let self = this;
        self.addMembersOption = [];
        _.forEach(this.allUsers, function (user, key) {
          if (_.find(self.members, ['UserID', user.UserID])) return;
          let tmpObj = {};
          tmpObj.id = user.UserID;
          tmpObj.text = user.FullName;
          self.addMembersOption.push(tmpObj);
        });
        this.stage.showAddMember = !this.stage.showAddMember;
      },
      handleAddMembers() {
        if (!this.membersAdded.length) {
          this.$bvToast.toast('Bạn chưa chọn thành viên nào', {
            title: 'Thông báo',
            variant: 'warning'
          });
          this.stage.showAddMember = false;
          return false;
        }

        let self = this;
        let members = [];

        _.forEach(this.membersAdded, function (value, key) {
          let member = _.find(self.addMembersOption, ['id', Number(value)]);
          if (member && !_.isObject(_.find(self.members, ['UserID', value]))) {
            members.push({
              UserID: member.id,
              UserName: member.text
            });
          }
        });

        if (!members.length) {
          this.$bvToast.toast('Bạn chưa chọn thành viên nào hoặc đã có thành viên', {
            title: 'Thông báo',
            variant: 'warning'
          });
          return;
        }
        let requestData = {
          method: 'post',
          url: 'extensions/api/chat/add-members',
          data: {
            GroupID: this.tabChat.GroupID,
            members: members
          }
        };

        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            if (responsesData.data.data) {
              _.forEach(responsesData.data.data, function (member, key) {
                let tmpObj = member;
                let user = _.find(self.allUsers, ['UserID', member.UserID]);
                if (user) {
                  tmpObj.Avatar = user.Avata;
                  if (!_.isObject(_.find(self.members, ['UserID', member.UserID]))) {
                    self.members.push(tmpObj);
                  }
                }
              });
            }

            let group = _.find(self.groups, ['GroupID', self.tabChat.GroupID]);
            socket.emit('add members', {
              GroupID: self.tabChat.GroupID,
              members: responsesData.data.data,
              group: group
            });

            if (responsesData.data.Content) {
              let message = responsesData.data.Content;
              message.GroupType = self.tabChat.GroupType;
              self.messageArray.push(message);
              let messageData = {
                Content: message,
                GroupType: self.tabChat.GroupType,
                GroupID: self.tabChat.GroupID,
              };
              socket.emit('new message', messageData);
              let text = this.getMessageActionMember(message);
              $('.last-content-' + message.GroupID).html(text);
              self.$emit('on:new-message', text);
              self.scrollToEnd();
            }

            self.membersAdded = [];
            this.$bvToast.toast('Thành viên đã được thêm vào nhóm', {
              title: 'Thông báo',
              variant: 'success'
            });

          } else {
            this.$bvToast.toast('Thêm thành viên thất bại', {
              title: 'Thông báo',
              variant: 'warning'
            });
          }
          this.stage.showAddMember = false;
        }, (error) => {
          Swal.fire({
            title: 'Thông báo',
            text: 'Không kết nối được với máy chủ',
            confirmButtonText: 'Đóng'
          });
          console.log(error);
        });

      },
      handleRemoveMember(member){
        let self = this;
        Swal.fire({
          title: 'Xác nhận',
          text: 'Loại thành viên này khỏi nhóm?',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Đồng ý',
          cancelButtonText: 'Hủy bỏ'
        }).then((result) => {
          if (result.value) {
            let requestData = {
              method: 'post',
              url: 'extensions/api/chat/remove-member',
              data: {
                GroupID: this.tabChat.GroupID,
                UserID: member.UserID
              }
            };

            ApiService.setHeader();
            ApiService.customRequest(requestData).then((responses) => {
              let responsesData = responses.data;
              if (responsesData.status === 1) {
                this.$bvToast.toast('Đã loại thành viên khỏi nhóm', {
                  title: 'Thông báo',
                  variant: 'success'
                });

                _.remove(self.members, ['UserID', member.UserID]);

                socket.emit('remove member', {
                  GroupID: self.tabChat.GroupID,
                  member: member
                });

                if (responsesData.data.Content) {
                  let message = responsesData.data.Content;
                  message.GroupType = self.tabChat.GroupType;
                  self.messageArray.push(message);
                  let messageData = {
                    Content: message,
                    GroupType: self.tabChat.GroupType,
                    GroupID: self.tabChat.GroupID,
                  };
                  socket.emit('new message', messageData);
                  let text = this.getMessageActionMember(message);
                  $('.last-content-' + message.GroupID).html(text);
                  self.$emit('on:new-message', text);
                  self.scrollToEnd();

                } else {
                  if (responsesData.status === 2) {
                    this.$bvToast.toast(responsesData.msg, {
                      title: 'Thông báo',
                      variant: 'warning'
                    });
                  } else {
                    this.$bvToast.toast('Có lỗi xảy ra', {
                      title: 'Thông báo',
                      variant: 'warning'
                    });
                  }
                }
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
      getMessageActionMember(message){
        let self = this, pieces = message.Content.split('_'), text = '', listUsersName = '',
          userIDs = pieces[2].split(',');

        _.forEach(userIDs, function (UserID, key) {
          let user = _.find(self.allUsers, ['UserID', Number(UserID)]);
          if (user) {
            listUsersName += user.FullName;
            if (key !== (userIDs.length - 1)) {
              if (userIDs.length === 2) {
                listUsersName += ' và ';
              }else {
                listUsersName += ', ';
              }
            }
          }else if (Number(UserID) === self.currentUser.UserID) {
            listUsersName += 'bạn';
          }
        });
        if (pieces[1] === 'add') {
          text = ((message.UserID === this.currentUser.UserID) ? 'Bạn' : this.getLastName(message.UserID)) + ' đã thêm ' + listUsersName + ' vào nhóm';
        }
        if (pieces[1] === 'remove') {
          text = ((message.UserID === this.currentUser.UserID) ? 'Bạn' : this.getLastName(message.UserID)) + ' đã loại ' + listUsersName + ' khỏi nhóm';
        }

        if (pieces[1] === 'leave') {
          text = ((message.UserID === this.currentUser.UserID) ? 'Bạn' : this.getLastName(message.UserID)) + ' đã rời khỏi nhóm';
        }

        return text;
      },
      handleLeaveGroup(){
        let self = this;

        Swal.fire({
          title: 'Xác nhận',
          text: 'Bạn có chắc rời khỏi nhóm?',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Đồng ý',
          cancelButtonText: 'Hủy bỏ'
        }).then((result) => {
          if (result.value) {
            let requestData = {
              method: 'post',
              url: 'extensions/api/chat/leave-group',
              data: {
                GroupID: this.tabChat.GroupID,
              }
            };

            ApiService.setHeader();
            ApiService.customRequest(requestData).then((responses) => {
              let responsesData = responses.data;
              if (responsesData.status === 1) {
                let member = _.find(self.members, ['UserID', self.currentUser.UserID]);
                socket.emit('remove member', {
                  GroupID: self.tabChat.GroupID,
                  member: member
                });
                _.remove(self.members, ['UserID', self.currentUser.UserID]);

                if (responsesData.data.Content) {
                  let message = responsesData.data.Content;
                  message.GroupType = self.tabChat.GroupType;
                  self.messageArray.push(message);
                  let messageData = {
                    Content: message,
                    GroupType: self.tabChat.GroupType,
                    GroupID: self.tabChat.GroupID,
                  };
                  socket.emit('new message', messageData);
                  let text = this.getMessageActionMember(message);
                  $('.last-content-' + message.GroupID).html(text);
                  self.$emit('on:new-message', text);
                  self.scrollToEnd();

                  this.$bvToast.toast('Bạn đã rời khỏi nhóm', {
                    title: 'Thông báo',
                    variant: 'success'
                  });
                }
              }else if (responsesData.status === 2) {
                this.$bvToast.toast(responsesData.msg, {
                  title: 'Thông báo',
                  variant: 'warning'
                });
              } else {
                this.$bvToast.toast('Cập nhật thất bại', {
                  title: 'Thông báo',
                  variant: 'warning'
                });
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
      changeMemberRole(UserID, role){
        let self = this;
        Swal.fire({
          title: 'Xác nhận',
          text: 'Thay đổi quyền của thành viên',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Đồng ý',
          cancelButtonText: 'Hủy bỏ'
        }).then((result) => {
          if (result.value) {
            let requestData = {
              method: 'post',
              url: 'extensions/api/chat/set-member-role',
              data: {
                GroupID: this.tabChat.GroupID,
                UserID: UserID,
                Type: role
              }
            };

            ApiService.setHeader();
            ApiService.customRequest(requestData).then((responses) => {
              let responsesData = responses.data;
              if (responsesData.status === 1) {
                let indexMember = _.findIndex(self.members, ['UserID', UserID]);
                if (indexMember > -1) {
                  self.members[indexMember].Type = role;
                }

                if (role === 1) {
                  let user = _.find(self.allUsers, ['UserID', UserID]);
                  let member = responsesData.data;
                  if (member) {
                    member.Avatar = user.Avata;
                    self.admins.push(member);
                  }
                }

                if (role === 2) {
                  _.remove(self.admins, ['UserID', UserID]);
                }

                this.$bvToast.toast('Cập nhật thành công', {
                  title: 'Thông báo',
                  variant: 'success'
                });
              }else if (responsesData.status === 2){
                this.$bvToast.toast(responsesData.msg, {
                  title: 'Thông báo',
                  variant: 'warning'
                });
              }else {
                this.$bvToast.toast('Cập nhật thất bại', {
                  title: 'Thông báo',
                  variant: 'warning'
                });
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
      handleUpdateGroupName(){
        let self = this;

        if (!this.model.groupName) {
          this.$bvToast.toast('Tên nhóm không được để trống', {
            title: 'Thông báo',
            variant: 'warning'
          });
          return false;
        }

        let requestData = {
          method: 'post',
          url: 'extensions/api/chat/update-group',
          data: {
            GroupID: this.tabChat.GroupID,
            GroupName: this.model.groupName
          }
        };

        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.$emit('on:update-group-name', responsesData.data);
            self.stage.showEditGroupName = false;
          }
        }, (error) => {
          console.log(error);
          Swal.fire({
            title: 'Thông báo',
            text: 'Không kết nối được với máy chủ',
            confirmButtonText: 'Đóng'
          });
        });
      },
      handleDeleteGroup(){
        if (!this.isAdmin) return false;

        Swal.fire({
          title: 'Xác nhận',
          text: 'Bạn có chắc xóa nhóm',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Đồng ý',
          cancelButtonText: 'Hủy bỏ'
        }).then((result) => {
          if (result.value) {
            let self = this;
            let requestData = {
              method: 'post',
              url: 'extensions/api/chat/delete-group',
              data: {
                GroupID: this.tabChat.GroupID,
              }
            };

            ApiService.setHeader();
            ApiService.customRequest(requestData).then((responses) => {
              let responsesData = responses.data;
              if (responsesData.status === 1) {
                self.$emit('on:delete-group', responsesData.data);
                self.$bvToast.toast('Cập nhật thành công', {
                  title: 'Thông báo',
                  variant: 'success'
                });
              }else if (responsesData.status === 2) {
                self.$bvToast.toast(responsesData.msg, {
                  title: 'Thông báo',
                  variant: 'warning'
                });
              } else {
                self.$bvToast.toast('Cập nhật thất bại', {
                  title: 'Thông báo',
                  variant: 'warning'
                });
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
      onShowChatMessage(user){
        this.$emit('on:show-chat-message', user);
        this.$bvModal.hide('modal-chat-member-' + this.tabChat.GroupID);
      },
      onClickSearchAdvanced(){
        this.$refs['chat-modal-search-datalist'].init();
      },
      handleSearchAdvancedMessage(datalist){
        let self = this;
        let hasCategoryKey = false;
        this.model.categoryKey = [];
        this.model.searchAdvanced = '';
        _.forEach(datalist, function (value, key) {
          let tmp = value.DatalistTable + ':' + value.LinkID;
          if (value.LinkID) {
            hasCategoryKey = true;
          }
          self.model.categoryKey.push(tmp);
          self.model.searchAdvanced += value.LinkTableName + ': ' + value.LinkName;
          if (key !== (datalist.length - 1)) {
            self.model.searchAdvanced += ', ';
          }
        });

        if (hasCategoryKey && this.model.categoryKey.length) {
          this.messageArray = [];
          this.page = 1;
          this.loadMessage();
        }
      },
      onCloseSearchAdvanced(){
        this.stage.showSearchAdvanced = false;
        if (this.model.categoryKey.length) {
          this.model.categoryKey = [];
          this.handleSubmitSearch();
        }
      },
      openMessenger(e) {
        e.preventDefault();
        e.stopPropagation();
        this.$emit('on:open-messenger');
        this.$router.push({
          name: 'apps-chat-message',
          params: {GroupID: this.tabChat.GroupID}
        });
      },
      getAvatar(UserID) {
        let user = _.find(this.allUsers, ['UserID', UserID]);
        if (user) {
          return this.$store.state.appRootApi + user.Avata;
        }
        return ''
      },
      urlify(text) {
        let urlRegex = /(https?:\/\/[^\s]+)/g;
        // let urlRegex = /^(https?|ftp|torrent|image|irc):\/\/(-\.)?([^\s\/?\.#-]+\.?)+(\/[^\s]*)?$/i;
        text = text.replace('<br>', ' <br>');
        return text.replace(urlRegex, function (url) {
          if (!url.includes('cdn.jsdelivr.net/emojione')) {
            return '<a href="' + url + '" target="_blank">' + url + '</a>';
          } else {
            return url;
          }
        });
        // or alternatively
        // return text.replace(urlRegex, '<a href="$1">$1</a>')
      },
      onToggleMinimize(e) {
        if ($(e.target).hasClass('chat-bar-wrap') || $(e.target).hasClass('chat-bar') ||
        $(e.target).hasClass('tab-header-title')) {
          this.stage.minimize = !this.stage.minimize;
        }
      },
      setFontSizeMessage(size){
        this.$store.commit('setChat', {fontSize: size});
      },
      redirectToView(e, GroupID) {
        e.preventDefault();
        e.stopPropagation();
        let group = _.find(this.groups, ['GroupID', GroupID]);
        if (group && group.CategoryKey) {
          let pieces = group.CategoryKey.split('_');
          let piece = pieces[0];
          if (piece) {
            let categoryArr = piece.split(':');

            switch (categoryArr[0]) {
              case 'task':
                this.$router.push({
                  name: 'task-task-view',
                  params: {id: Number(categoryArr[1])}
                });
                break;
              default:
                break;
            }
          }
        }
        return false;
      },
      getLinkCategory(GroupID) {
        let group = _.find(this.groups, ['GroupID', GroupID]);
        let link = '#';
        if (group && group.CategoryKey) {
          let pieces = group.CategoryKey.split('_');
          let piece = pieces[0];
          if (piece) {
            let categoryArr = piece.split(':');
            switch (categoryArr[0]) {
              case 'task':
                link = '/task/task/view/' + categoryArr[1];
                break;
              default:
                break;
            }
          }
        }

        return link;
      },
      downloadFile(e, file) {
        e.preventDefault();
        e.stopPropagation();
        let self = this;
        let urlApi = '/extensions/api/chat/download-file/' + file.FileID;
        let requestData = {
          method: 'get',
          url: urlApi,
          data: {},
          responseType: 'blob',
        };
        this.$store.commit('isLoading', true);
        ApiService.customRequest(requestData).then((response) => {
          let dataResponse = response.data;
          let link = document.createElement('a');
          link.href = window.URL.createObjectURL(dataResponse);
          link.download = file.FileAttachName;
          link.click();
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
      onViewFile(e, file) {
        e.preventDefault();
        e.stopPropagation();
        let href = '';
        if (file.FileAttachName.includes('.pdf')) {
          href = this.$store.state.appRootApi + file.FieldAttach;
        }else {
          href = 'https://view.officeapps.live.com/op/embed.aspx?src=' + this.$store.state.appRootApi + file.FieldAttach;
        }
        window.open(href, '_blank');
      },
      insertTextAtCaret(text) {
        let sel, range;
        if (window.getSelection) {
          sel = window.getSelection();
          if (sel.getRangeAt && sel.rangeCount) {
            range = sel.getRangeAt(0);
            range.deleteContents();
            range.insertNode( document.createTextNode(text) );
          }
        } else if (document.selection && document.selection.createRange) {
          document.selection.createRange().text = text;
        }
      },
      insertHtmlAtCaret(html) {
        let sel, range;
        if (window.getSelection) {
          // IE9 and non-IE
          sel = window.getSelection();
          if (sel.getRangeAt && sel.rangeCount) {
            range = sel.getRangeAt(0);
            range.deleteContents();

            // Range.createContextualFragment() would be useful here but is
            // only relatively recently standardized and is not supported in
            // some browsers (IE9, for one)
            let el = document.createElement("div");
            el.innerHTML = html;
            let frag = document.createDocumentFragment(), node, lastNode;
            while ( (node = el.firstChild) ) {
              lastNode = frag.appendChild(node);
            }
            range.insertNode(frag);

            // Preserve the selection
            if (lastNode) {
              range = range.cloneRange();
              range.setStartAfter(lastNode);
              range.collapse(true);
              sel.removeAllRanges();
              sel.addRange(range);
            }
          }
        } else if (document.selection && document.selection.type != "Control") {
          // IE < 9
          document.selection.createRange().pasteHTML(html);
        }
      },
      replacerTextAtCaret(search, replace) {
        let sel = window.getSelection();
        if (!sel.focusNode) {
          return;
        }

        let startIndex = sel.focusNode.nodeValue.indexOf(search);
        let endIndex = startIndex + search.length;
        if (startIndex === -1) {
          return;
        }
        // console.log("first focus node: ", sel.focusNode.nodeValue);
        let range = document.createRange();
        //Set the range to contain search text
        range.setStart(sel.focusNode, startIndex);
        range.setEnd(sel.focusNode, endIndex);
        //Delete search text
        range.deleteContents();
        // console.log("focus node after delete: ", sel.focusNode.nodeValue);
        //Insert replace text
        range.insertNode(document.createTextNode(replace));
        // console.log("focus node after insert: ", sel.focusNode.nodeValue);
        //Move the caret to end of replace text
        let replacement = document.createTextNode(replace);
        range.insertNode(replacement);
        range.setStartAfter(replacement);
        // Chrome fix
        sel.removeAllRanges();
        sel.addRange(range);
      },

      checkTaskStatus(message){
        if (message.CategoryKey && message.CategoryKey === this.tabChat.CategoryKey) {
          return false;
        }
        if (message.Datalist) {
          let tasks = _.filter(message.Datalist, ['DatalistTable', 'task']);
          if (tasks.length === 1) {
            return 1;
          }else if (tasks.length > 1) {
            return 2;
          } else {
            return 0;
          }
        }
        return false;
      }
  },
    watch: {
      'stage.showSearch'(){
        if (this.stage.showSearch) {
          this.stage.showAddMember = false;
          this.stage.showEditGroupName = false;
          this.stage.showSearchAdvanced = false;
        }
      },
      'stage.showSearchAdvanced'(){
        if (this.stage.showSearchAdvanced) {
          this.stage.showAddMember = false;
          this.stage.showEditGroupName = false;
          this.stage.showSearch = false;
          this.model.categoryKey = [];
          this.model.searchAdvanced = '';
        }
      },
      'stage.showAddMember'(){
        if (this.stage.showAddMember) {
          this.stage.showSearch = false;
          this.stage.showEditGroupName = false;
          this.stage.showSearchAdvanced = false;
        }
      },
      'stage.showEditGroupName'(){
        if (this.stage.showEditGroupName) {
          this.stage.showAddMember = false;
          this.stage.showSearch = false;
          this.stage.showSearchAdvanced = false;
        }
      },
    }

  }
</script>

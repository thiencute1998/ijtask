<template>
    <div class="comments component-category-comments">
        <div class="comment-header d-flex justify-content-between align-items-center mb-2 flex-wrap flex-sm-wrap flex-md-nowrap flex-lg-nowrap">
            <div class="comment-total mb-2 mb-sm-2 mb-md-0 mb-lg-0">Thảo luận ({{totalRows}})</div>
            <div class="comment-filters d-flex align-items-center flex-wrap flex-sm-wrap flex-md-nowrap flex-lg-nowrap">
                <b-form-select class="mr-0 mr-sm-0 mr-md-2 mr-lg-2 mb-2 mb-sm-2 mb-md-0 mb-lg-0" v-model="model.filterDate" :options="[{value: 1, text: 'Mới nhất'}, {value: 2, text: 'Ngày tạo'}]"></b-form-select>
                <ijcore-date-range class="mr-0 mr-sm-2 mr-md-2 mr-lg-2 mb-2 mb-sm-0 mb-md-0 mb-lg-0" v-model="model.filterDateRange"></ijcore-date-range>
              <div class="custom-select2-multiple">
                <ijcore-select2-server
                  v-model="model.filterUsers"
                  :url="appRootApi + '/task/api/common/get-employee'"
                  id-name="UserID"
                  text-name="EmployeeName"
                  placeholder="Chọn nhân viên"
                  :allowClear="true"
                  :multiple="true"
                  :delay="500">
                </ijcore-select2-server>
              </div>
            </div>
        </div>
        <div class="comment-body">
          <div class="post-comments">
            <div class="comment-write d-flex justify-content-between mb-2 px-3"
                 v-if="CategoryComments && (CategoryComments.length < totalRows)"
                 @click="loadMoreComment()"
                 style="font-weight: 500; cursor: pointer">
              <span v-if="CategoryComments.length" style="color: #65676b;">Xem các bình luận trước</span>
              <span v-else>Xem bình luận</span>
              <span v-if="CategoryComments.length">{{CategoryComments.length}}/{{totalRows}}</span>
            </div>
            <div class="comments px-3" v-if="CategoryComments && CategoryComments.length">
              <div class="comment media mb-2" v-for="(comment, keyComment) in CategoryComments">
                <div class="d-flex mr-3 align-self-start img-block">
                  <img :src="$store.state.appRootApi + comment.Avata" alt="placeholder" width="40" height="40" class="img-avatar">
                </div>
                <div class="media-body">
                  <div class="comment-head">
                    <h6 class="comment-user mr-2 mb-0">{{comment.UserName}}</h6>
                    <span class="comment-time mb-0 text-muted">{{formatCommentTime(comment.CreatedDate)}}</span>
                  </div>
                  <div class="comment-content mb-1">
                    <div class="comment-text mb-1">
                      <span v-if="comment.Content && comment.Content !== ':sb-agree' && !comment.Content.includes(':sb-action-')" v-html="comment.Content"></span>
                      <span v-if="comment.Content === ':sb-agree'">
                                <div style="width: 36px; height: 36px">
                                  <svg aria-labelledby="js_43x" role="img" height="100%" width="100%" version="1.1" viewBox="0 0 256 256" x="0px" y="0px"><title id="js_43x">Ký hiệu giơ ngón tay cái</title><g><g><polyline fill="transparent" points="256,0 258,256 2,258 "></polyline><path d="M254,147.1c0-12.7-4.4-16.4-9-20.1c2.6-4.2,5.1-10.2,5.1-18c0-15.8-12.3-25.7-32-25.7h-52c-0.5,0-1-0.5-0.9-1
                                    c1.4-8.6,3-24,3-31.7c0-16.7-4-37.5-19.3-45.7c-4.5-2.4-8.3-3.7-14.1-3.7c-8.8,0-14.6,3.6-16.7,5.9c-1.3,1.4-1.9,3.3-1.8,5.2
                                    l1.3,34.6c0.2,2.8-0.3,5.4-1.6,7.7l-24,47.8c-1.7,3.5-4.2,6.6-7.6,8.5c-3.5,2-6.5,5.9-6.5,9.5v94.8C78,230,94,238,112.3,238h86.1
                                    c13.5,0,22.4-4.5,27.2-13.5c4.4-8.2,3.2-15.8,1.4-21.5c7.4-2.3,14.8-8,16.9-18.3c1.3-6.6-0.7-12.1-2.9-16.2
                                    C247.5,165,254,159.8,254,147.1z" fill="#4080ff" stroke="transparent" stroke-linecap="round" stroke-width="5%"></path><path d="M56.2,100H13.8C7.3,100,2,105.3,2,111.8v128.5c0,6.5,5.3,11.8,11.8,11.8h42.4c6.5,0,11.8-5.3,11.8-11.8V111.8
                                    C68,105.3,62.7,100,56.2,100z" fill="#4080ff"></path></g></g></svg>
                                </div>
                              </span>
                      <span v-if="comment.Content && comment.Content.includes(':sb-action-')"></span>
                    </div>
                    <div class="comment-files" v-if="comment.ContentFile && comment.ContentFile.length">
                      <div class="comment-file mr-1" :class="[(file.FileType === 1) ? 'comment-image' : ((file.FileType === 2) ? 'comment-application' : ((file.FileType === 3) ? 'message-audio' : ((file.FileType === 4) ? 'comment-video' : '')))]" v-for="(file, key) in comment.ContentFile">
                        <a :href="$store.state.appRootApi + file.FieldAttach" target="_blank" class="img-block" v-if="file.FileType === 1">
                          <img :src="$store.state.appRootApi + file.FieldAttach" :alt="file.FileAttachName">
                        </a>
                        <a :href="$store.state.appRootApi + file.FieldAttach" target="_blank" class="img-block" v-if="file.FileType === 2">
                          <div class="file-left">
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
                  </div>
                  <div class="comment-actions mb-1">
                    <span class="comment-action comment-reply mr-2" @click="onShowReply(keyComment)">Trả lời</span>
                    <span class="comment-action comment-edit mr-2" @click="onShowEdit(keyComment)" v-if="comment.UserID === currentUser.UserID">Sửa</span>
                    <span class="comment-action comment-delete mr-3" @click="deleteComment(keyComment)" v-if="comment.UserID === currentUser.UserID">Xóa</span>
                  </div>
                  <chat-comment-form
                    :id="'comment-form-edit-' + comment.LineID"
                    :object="comment"
                    v-if="CategoryComments[keyComment].showEdit"
                    type-store="category"
                    :Category="Category"
                    :CategoryKey="CategoryKey"
                    :CategoryID="CategoryID"
                    @on:updated-comment="updatedComment($event, keyComment)"
                    type-form="edit"></chat-comment-form>
                  <div class="comment-total" v-if="!CategoryComments[keyComment].showContentReply && CategoryComments[keyComment].TotalComment" @click="loadMoreReply(keyComment)">
                          <span style="cursor: pointer;">
                            <i class="fa fa-reply-all" style="font-size: 14px"></i>
                            {{CategoryComments[keyComment].TotalComment}} trả lời
                          </span>
                  </div>
                  <div class="comment-write d-flex justify-content-between"
                       v-if="CategoryComments[keyComment].Replies && CategoryComments[keyComment].showContentReply
                             && (CategoryComments[keyComment].Replies.length < CategoryComments[keyComment].TotalComment)"
                       @click="loadMoreReply(keyComment)"
                       style="font-weight: 500; cursor: pointer; color: #65676b">
                    <span>Xem các trả lời trước</span>
                  </div>
                  <div class="comment-reply mt-2" v-if="CategoryComments[keyComment].Replies && CategoryComments[keyComment].Replies.length && CategoryComments[keyComment].showContentReply">
                    <div class="comment mb-2" v-for="(reply, keyReply) in CategoryComments[keyComment].Replies">
                      <div class="media">
                        <div class="d-flex mr-3 align-self-start img-block">
                          <img :src="$store.state.appRootApi + reply.Avata" alt="placeholder" width="40" height="40" class="img-avatar">
                        </div>
                        <div class="media-body">
                          <div class="comment-head">
                            <h6 class="comment-user mr-2 mb-0">{{reply.UserName}}</h6>
                            <span class="comment-time mb-0 text-muted">{{formatCommentTime(reply.CreatedDate)}}</span>
                          </div>
                          <div class="comment-content mb-1">
                            <div class="comment-text mb-1">
                              <span v-if="reply.Content && reply.Content !== ':sb-agree' && !reply.Content.includes(':sb-action-')" v-html="reply.Content"></span>
                              <span v-if="reply.Content === ':sb-agree'">
                                    <div style="width: 36px; height: 36px">
                                      <svg aria-labelledby="js_43x" role="img" height="100%" width="100%" version="1.1" viewBox="0 0 256 256" x="0px" y="0px"><title id="js_43x">Ký hiệu giơ ngón tay cái</title><g><g><polyline fill="transparent" points="256,0 258,256 2,258 "></polyline><path d="M254,147.1c0-12.7-4.4-16.4-9-20.1c2.6-4.2,5.1-10.2,5.1-18c0-15.8-12.3-25.7-32-25.7h-52c-0.5,0-1-0.5-0.9-1
                                        c1.4-8.6,3-24,3-31.7c0-16.7-4-37.5-19.3-45.7c-4.5-2.4-8.3-3.7-14.1-3.7c-8.8,0-14.6,3.6-16.7,5.9c-1.3,1.4-1.9,3.3-1.8,5.2
                                        l1.3,34.6c0.2,2.8-0.3,5.4-1.6,7.7l-24,47.8c-1.7,3.5-4.2,6.6-7.6,8.5c-3.5,2-6.5,5.9-6.5,9.5v94.8C78,230,94,238,112.3,238h86.1
                                        c13.5,0,22.4-4.5,27.2-13.5c4.4-8.2,3.2-15.8,1.4-21.5c7.4-2.3,14.8-8,16.9-18.3c1.3-6.6-0.7-12.1-2.9-16.2
                                        C247.5,165,254,159.8,254,147.1z" fill="#4080ff" stroke="transparent" stroke-linecap="round" stroke-width="5%"></path><path d="M56.2,100H13.8C7.3,100,2,105.3,2,111.8v128.5c0,6.5,5.3,11.8,11.8,11.8h42.4c6.5,0,11.8-5.3,11.8-11.8V111.8
                                        C68,105.3,62.7,100,56.2,100z" fill="#4080ff"></path></g></g></svg>
                                    </div>
                                  </span>
                              <span v-if="reply.Content && reply.Content.includes(':sb-action-')"></span>
                            </div>
                            <div class="comment-files" v-if="reply.ContentFile && reply.ContentFile.length">
                              <div class="comment-file mr-1" :class="[(file.FileType === 1) ? 'comment-image' : ((file.FileType === 2) ? 'comment-application' : ((file.FileType === 3) ? 'message-audio' : ((file.FileType === 4) ? 'comment-video' : '')))]" v-for="(file, key) in reply.ContentFile">
                                <a :href="$store.state.appRootApi + file.FieldAttach" target="_blank" class="img-block" v-if="file.FileType === 1">
                                  <img :src="$store.state.appRootApi + file.FieldAttach" :alt="file.FileAttachName">
                                </a>
                                <a :href="$store.state.appRootApi + file.FieldAttach" target="_blank" class="img-block" v-if="file.FileType === 2">
                                  <div class="file-left">
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
                            <div class="comment-links text-muted" v-if="reply.Datalist && reply.Datalist.length">
                              <div v-for="(datalist, key) in reply.Datalist">
                                <i class="fa fa-puzzle-piece"></i> {{datalist.LinkTableName}}: {{datalist.LinkName}}
                              </div>
                            </div>
                          </div>
                          <div class="comment-actions mb-1">
                            <span class="comment-action comment-reply mr-2" @click="onShowReply(keyComment, keyReply, reply.UserName)">Trả lời</span>
                            <span class="comment-action comment-edit mr-2" @click="onShowEdit(keyComment, keyReply)" v-if="reply.UserID === currentUser.UserID">Sửa</span>
                            <span class="comment-action comment-delete mr-3" v-if="reply.UserID === currentUser.UserID" @click="deleteComment(keyComment, keyReply)">Xóa</span>
                          </div>
                          <chat-comment-form
                            :id="'comment-form-reply-edit-' + reply.LineID"
                            :object="reply"
                            type-form="edit"
                            type-store="category"
                            :Category="Category"
                            :CategoryKey="CategoryKey"
                            :CategoryID="CategoryID"
                            v-if="CategoryComments[keyComment].Replies[keyReply].showEdit"
                            @on:updated-comment="updatedComment($event, keyComment, keyReply)"
                          ></chat-comment-form>
                        </div>
                      </div>

                    </div>
                  </div>
                  <chat-comment-form
                    :id="'comment-form-reply-' + comment.LineID"
                    :object="comment"
                    type-form="reply"
                    type-store="category"
                    :line-i-d-post="comment.LineID"
                    :line-i-d-comment="CategoryComments[keyComment].LineIDReply"
                    :Category="Category"
                    :CategoryKey="CategoryKey"
                    :CategoryID="CategoryID"
                    :group-name="GroupName"
                    @on:stored-comment="storedComment($event, keyComment)"
                    v-if="CategoryComments[keyComment].showReply"></chat-comment-form>
                </div>
              </div>
            </div>
            <div class="mx-3">
              <chat-comment-form
                id="comment-form-0"
                type-store="category"
                :Category="Category"
                :CategoryKey="CategoryKey"
                :CategoryID="CategoryID"
                :group-name="GroupName"
                @on:stored-comment="storedComment($event)"
                type-form="reply">
              </chat-comment-form>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import IjcoreDateRange from '@/components/IjcoreDateRange';
    import IjcoreSelect2Server from '@/components/IjcoreSelect2Server';
    import ChatCommentForm from "./ChatCommentForm";
    import moment from "moment";

    const CategoryComment = 'extensions/api/chat/category-comment';
    export default {
        name: 'task-comment',
        components: {
          IjcoreDateRange,
          IjcoreSelect2Server,
          ChatCommentForm
        },
        props: {
          task: [Object, Array],
          CategoryKey: [Object, Array],
          Category: [String],
          CategoryID: [String, Number],
          isDataflow: false,
          GroupName: [String]
        },
        data() {
            return {
              perPage: 5,
              currentPage: 1,
              totalRows: 0,
              CategoryComments: [],
              currentUser: null,
              model: {
                currentEmployeeID: null,
                currentUserID: null,

                filterDate: 1,
                filterDateRange: null,
                filterUsers: []
              },
              stage: {
                reLoadReply: 10000
              }
            }
        },
        computed: {
            appRootApi(){
                return process.env.VUE_APP_ROOT_API;
            }
        },
        mounted(){
          let self = this;
          this.currentUser = JSON.parse(localStorage.getItem('Employee'));
          this.fetchData();

          // socket event
          socket.on('new message', (data) => {
            if (data.GroupType === 3 && data.CategoryKey && data.CategoryKey.includes(self.Category + ':' + self.CategoryID)) {
              let keyComment = null;
              let keyReply = null;
              let LineIDComment = data.LineIDPost;
              let LineIDReply = data.LineIDComment;

              if (LineIDReply !== null) {
                keyComment = _.findIndex(self.CategoryComments, ['LineID', Number(LineIDComment)]);
                if (keyComment > -1) {
                  keyReply = _.findIndex(self.CategoryComments[keyComment].Replies, ['LineID', Number(LineIDReply)]);
                } else {
                  return;
                }
              }else if (LineIDComment !== null) {
                keyComment = _.findIndex(self.CategoryComments, ['LineID', Number(LineIDComment)]);
              }
              self.storedComment(data, keyComment, keyReply);
            }
          });

          socket.on('delete message', (data) => {
            let keyComment = null, keyReply = null, keyReplyChild = null;
            let LineIDComment = data.LineIDPost;
            let LineIDReply = data.LineIDComment;
            let LineIDReplyChild = data.LineIDReply;

            if (LineIDReplyChild !== null) {
              keyComment = _.findIndex(self.CategoryComments, ['LineID', LineIDComment]);
              if (keyComment > -1) {
                keyReply = _.findIndex(self.CategoryComments[keyComment].Replies, ['LineID', LineIDReplyChild]);
                if (keyReply > -1) {
                  self.CategoryComments[keyComment].Replies.splice(keyReply, 1);
                  this.totalRows--;
                }
              }
            }else if (LineIDReply !== null) {
              keyComment = _.findIndex(self.CategoryComments, ['LineID', LineIDComment]);
              if (keyComment > -1) {
                keyReply = _.findIndex(self.CategoryComments[keyComment].Replies, ['LineID', LineIDReply]);
                self.CategoryComments[keyComment].Replies.splice(keyReply, 1);
                this.totalRows--;
              }
            }else {
              keyComment = _.findIndex(self.CategoryComments, ['LineID', LineIDComment]);
              if (keyComment > -1) {
                self.CategoryComments.splice(keyComment, 1);
                this.totalRows--;
              }
            }
          });

        },
        methods: {
          fetchData(){
            let self = this;
            let requestData = {
                method: 'post',
                url: CategoryComment,
                data: {
                  per_page: this.perPage,
                  page: this.currentPage,
                  CategoryKey: this.CategoryKey,
                  DateType: this.model.filterDate
                },
            };

            if (this.model.filterUsers && this.model.filterUsers.length) {
              let UserIDs = [];
              _.forEach(this.model.filterUsers, function (UserID, key) {
                UserIDs.push(Number(UserID));
              });
              requestData.data.UserIDs = UserIDs;
            }

            if (this.model.filterDateRange && this.model.filterDateRange.fromDate && this.model.filterDateRange.fromDate !== '' && this.model.filterDateRange.fromDate !== '__/__/____') {
              requestData.data.fromDate = this.model.filterDateRange.fromDate;
            }
            if (this.model.filterDateRange && this.model.filterDateRange.toDate && this.model.filterDateRange.toDate !== '' && this.model.filterDateRange.toDate !== '__/__/____') {
              requestData.data.toDate = this.model.filterDateRange.toDate;
            }

            ApiService.customRequest(requestData).then((response) => {
              self.$store.commit('isLoading', false);
              let responseData = response.data;
              if (responseData.status === 1) {
                let comments = responseData.data.data.data;
                _.forEach(comments, function (comment, key) {
                  let ContentFile = _.filter(responseData.data.CommentFile, ['ChatContentID', comment.LineID]);
                  comments[key].ContentFile = ContentFile;
                  comments[key].showReply = false;
                  comments[key].showEdit = false;
                  comments[key].showContentReply = false;
                  comments[key].Replies = [];
                  comments[key].LineIDReply = null;
                });

                self.perPage = responseData.data.data.per_page;
                self.currentPage = responseData.data.data.current_page;
                self.totalRows = responseData.data.data.total;

                comments = comments.reverse();
                self.CategoryComments = _.concat(comments, self.CategoryComments);
                self.CategoryComments = _.uniqBy(self.CategoryComments, 'LineID');

              } else {
                self.$store.commit('isLoading', false);
              }
            }, (error) => {
                console.log(error);
            });
            },
          loadMoreComment(){
            this.currentPage += 1;
            this.fetchData();
          },
          loadMoreReply(keyComment, keyReply = null){
            let self = this;
            let type = '';
            let page = null, per_page = null;
            let ParentID = null;
            if (keyReply !== null) {
              type = 'reply';
              page = (this.CategoryComments[keyComment].Replies[keyReply].page) ? this.CategoryComments[keyComment].Replies[keyReply].page + 1 : 1;
              ParentID = this.CategoryComments[keyComment].Replies[keyReply].LineID;
              this.CategoryComments[keyComment].Replies[keyReply].showContentReply = true;
            } else {
              page = (this.CategoryComments[keyComment].page) ? this.CategoryComments[keyComment].page + 1 : 1;
              ParentID = this.CategoryComments[keyComment].LineID;
              type = 'comment';
              this.CategoryComments[keyComment].showContentReply = true;
            }

            let requestData = {
              method: 'post',
              url: 'extensions/api/chat/category-reply',
              data: {
                per_page: this.perPage,
                page: page,
                ParentID: ParentID
              },
            };

            ApiService.customRequest(requestData).then((response) => {
              self.$store.commit('isLoading', false);
              let responseData = response.data;
              if (responseData.status === 1) {

                let comments = responseData.data.data.data;
                _.forEach(comments, function (comment, key) {
                  let ContentFile = _.filter(responseData.data.CommentFile, ['ChatContentID', comment.LineID]);
                  comments[key].ContentFile = ContentFile;
                  comments[key].showReply = false;
                  comments[key].showEdit = false;
                  comments[key].showContentReply = false;

                  if (type === 'comment') {
                    comments[key].RepliesChild = [];
                  }
                });

                if (type === 'comment') {
                  if (!self.CategoryComments[keyComment].Replies) {
                    self.CategoryComments[keyComment].Replies = [];
                  }
                  self.CategoryComments[keyComment].current_page = responseData.data.data.current_page;
                  self.CategoryComments[keyComment].per_page = responseData.data.data.per_page;
                  self.CategoryComments[keyComment].last_page = responseData.data.data.last_page;
                  self.CategoryComments[keyComment].page = responseData.data.data.current_page;
                  self.CategoryComments[keyComment].total = responseData.data.data.total;

                  comments = comments.reverse();
                  self.CategoryComments[keyComment].Replies = _.concat(comments, self.CategoryComments[keyComment].Replies);
                  self.CategoryComments[keyComment].Replies = _.uniqBy(self.CategoryComments[keyComment].Replies, 'LineID');
                }

                if (type === 'reply') {

                  if (!self.CategoryComments[keyComment].Replies[keyReply].RepliesChild) {
                    self.CategoryComments[keyComment].Replies[keyReply].RepliesChild = [];
                  }
                  self.CategoryComments[keyComment].Replies[keyReply].current_page = responseData.data.data.current_page;
                  self.CategoryComments[keyComment].Replies[keyReply].per_page = responseData.data.data.per_page;
                  self.CategoryComments[keyComment].Replies[keyReply].last_page = responseData.data.data.last_page;
                  self.CategoryComments[keyComment].Replies[keyReply].page = responseData.data.data.current_page;
                  self.CategoryComments[keyComment].Replies[keyReply].total = responseData.data.data.total;

                  comments = comments.reverse();
                  self.CategoryComments[keyComment].Replies[keyReply].RepliesChild = _.concat(comments, self.CategoryComments[keyComment].Replies[keyReply].RepliesChild);
                  self.CategoryComments[keyComment].Replies[keyReply].RepliesChild = _.uniqBy(self.CategoryComments[keyComment].Replies[keyReply].RepliesChild, 'LineID');
                }
              } else {
                self.$store.commit('isLoading', false);
              }
            }, (error) => {
              console.log(error);
              // self.$store.commit('isLoading', false);
            });

            this.$forceUpdate();
          },
          onShowReply(keyComment, keyReply = null, UserName = '') {
            this.CategoryComments[keyComment].LineIDReply = (keyReply !== null && keyReply > -1) ? this.CategoryComments[keyComment].Replies[keyReply].LineID : null;

            if (this.CategoryComments[keyComment].TotalComment && (!this.CategoryComments[keyComment].Replies || (this.CategoryComments[keyComment].Replies && !this.CategoryComments[keyComment].Replies.length))) {
              this.loadMoreReply(keyComment);
            }
            this.CategoryComments[keyComment].showReply = true;
            this.CategoryComments[keyComment].showEdit = false;

            // TODO: get user name
            let html = '';
            if (UserName) {
              html = '<span class="label tag-user">@' + UserName + '</span> &nbsp;';
            }

            this.$nextTick(() => {
              let form = this.$el.querySelector('#comment-form-reply-' + this.CategoryComments[keyComment].LineID);
              let input = this.$el.querySelector('#comment-form-reply-' + this.CategoryComments[keyComment].LineID + ' .comment-input');

              $(input).html(html);
              $('body').animate({
                scrollTop: $(form).offset().top
              }, 500);
              $(input).focus();
              this.placeCaretAtEnd(input);
            });
            this.$forceUpdate();
          },
          onShowEdit(keyComment = null, keyReply = null, keyReplyChild = null){
            if (keyReplyChild !== null) {
              let replyChild = this.CategoryComments[keyComment].Replies[keyReply].RepliesChild[keyReplyChild];
              this.CategoryComments[keyComment].Replies[keyReply].RepliesChild[keyReplyChild].showEdit = true;
              this.CategoryComments[keyComment].Replies[keyReply].showReply = false;
              this.$nextTick(() => {
                let input = this.$el.querySelector('#comment-form-reply-edit-' + replyChild.LineID + ' .comment-input');
                $(input).html(replyChild.Content);
                $(input).focus();
                this.placeCaretAtEnd(input);
              });
            }else if (keyReply !== null) {
              let reply = this.CategoryComments[keyComment].Replies[keyReply];
              this.CategoryComments[keyComment].Replies[keyReply].showEdit = true;
              this.CategoryComments[keyComment].showReply = false;
              this.$nextTick(() => {
                let input = this.$el.querySelector('#comment-form-reply-edit-' + reply.LineID + ' .comment-input');
                $(input).html(reply.Content);
                $(input).focus();
                this.placeCaretAtEnd(input);
              });
            } else if (keyComment !== null) {
              let comment = this.CategoryComments[keyComment];
              this.CategoryComments[keyComment].showEdit = true;
              this.CategoryComments[keyComment].showReply = false;
              this.$nextTick(() => {
                let input = this.$el.querySelector('#comment-form-edit-' + comment.LineID + ' .comment-input');
                $(input).html(comment.Content);
                $(input).focus();
                this.placeCaretAtEnd(input);
              });
            }

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
          storedComment(comment, keyComment = null, keyReply = null){
            if (keyComment !== null && keyReply !== null && keyComment > -1 && keyReply > -1) {
              comment.showEdit = false;
              comment.showReply = false;
              comment.showContentReply = false;
              if (this.CategoryComments && this.CategoryComments[keyComment]) {
                if (this.CategoryComments && this.CategoryComments[keyComment].Replies && this.CategoryComments[keyComment].Replies.length) {
                  this.CategoryComments[keyComment].Replies.push(comment);
                } else {
                  this.CategoryComments[keyComment].Replies = [];
                  this.CategoryComments[keyComment].Replies.push(comment);
                }
                this.CategoryComments[keyComment].showContentReply = true;
                if (!this.CategoryComments[keyComment].TotalComment) {
                  this.CategoryComments[keyComment].TotalComment = 0;
                }
                this.CategoryComments[keyComment].TotalComment++;
              }
            }

            if (keyComment !== null && keyComment > -1 && keyReply === null) {
              comment.showEdit = false;
              comment.showReply = false;
              comment.showContentReply = false;
              if (this.CategoryComments && this.CategoryComments[keyComment]) {
                if (this.CategoryComments && this.CategoryComments[keyComment].Replies && this.CategoryComments[keyComment].Replies.length) {
                  this.CategoryComments[keyComment].Replies.push(comment);
                } else {
                  this.CategoryComments[keyComment].Replies = [];
                  this.CategoryComments[keyComment].Replies.push(comment);
                }
                this.CategoryComments[keyComment].showContentReply = true;
                if (!this.CategoryComments[keyComment].TotalComment) {
                  this.CategoryComments[keyComment].TotalComment = 0;
                }
                this.CategoryComments[keyComment].TotalComment++;
              }
            }

            if (keyComment === null && keyReply === null) {
              comment.Replies = [];
              comment.showEdit = false;
              comment.showReply = false;
              comment.showContentReply = false;
              comment.LineIDReply = null;
              this.totalRows++;
              this.CategoryComments.push(comment);
            }

            if (this.isDataflow) {
              this.$_storeTaskDataflowNotice(this.CategoryID, 'comment');
            }

            this.$forceUpdate();
          },

          updatedComment(comment, keyComment = null, keyReply = null, keyReplyChild = null){
            if (keyReplyChild !== null){
              this.CategoryComments[keyComment].Replies[keyReply].RepliesChild[keyReplyChild].Content = comment.Content;
              this.CategoryComments[keyComment].Replies[keyReply].RepliesChild[keyReplyChild].ContentFile = comment.ContentFile;
              this.CategoryComments[keyComment].Replies[keyReply].RepliesChild[keyReplyChild].CreatedDate = comment.CreatedDate;
              this.CategoryComments[keyComment].Replies[keyReply].RepliesChild[keyReplyChild].UpdatedDate = comment.UpdatedDate;
              this.CategoryComments[keyComment].Replies[keyReply].RepliesChild[keyReplyChild].showEdit = false;
            } else if (keyReply !== null) {
              this.CategoryComments[keyComment].Replies[keyReply].Content = comment.Content;
              this.CategoryComments[keyComment].Replies[keyReply].ContentFile = comment.ContentFile;
              this.CategoryComments[keyComment].Replies[keyReply].CreatedDate = comment.CreatedDate;
              this.CategoryComments[keyComment].Replies[keyReply].UpdatedDate = comment.UpdatedDate;
              this.CategoryComments[keyComment].Replies[keyReply].showEdit = false;
            } else if (keyComment !== null) {
              this.CategoryComments[keyComment].Content = comment.Content;
              this.CategoryComments[keyComment].ContentFile = comment.ContentFile;
              this.CategoryComments[keyComment].CreatedDate = comment.CreatedDate;
              this.CategoryComments[keyComment].UpdatedDate = comment.UpdatedDate;
              this.CategoryComments[keyComment].showEdit = false;
            }

            this.$bvToast.toast('Cập nhật thành công', {
              title: 'Thông báo',
              variant: 'success'
            });
          },

          deleteComment(keyComment = null, keyReply = null, keyReplyChild = null) {
            Swal.fire({
              title: 'Xác nhận',
              text: 'Xóa bài viết',
              type: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Đồng ý',
              cancelButtonText: 'Hủy bỏ'
            }).then((result) => {
              if (result.value) {
                let LineID = null;
                let LineIDComment = null, LineIDReply = null, LineIDReplyChild = null;

                if (keyReply !== null) {
                  if (this.CategoryComments[keyComment].Replies[keyReply].ParentID !== this.CategoryComments[keyComment].LineID) {
                    LineIDComment = this.CategoryComments[keyComment].LineID;
                    LineIDReply = this.CategoryComments[keyComment].Replies[keyReply].ParentID;
                    LineIDReplyChild = this.CategoryComments[keyComment].Replies[keyReply].LineID;
                  } else {
                    LineIDComment = this.CategoryComments[keyComment].LineID;
                    LineIDReply = this.CategoryComments[keyComment].Replies[keyReply].LineID;
                  }

                  LineID = this.CategoryComments[keyComment].Replies[keyReply].LineID;

                }else if (keyComment !== null) {
                  LineIDComment = this.CategoryComments[keyComment].LineID;

                  LineID = LineIDComment;
                }

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
                    let comment = responsesData.data;
                    comment.LineIDPost = LineIDComment;
                    comment.LineIDComment = LineIDReply;
                    comment.LineIDReply = LineIDReplyChild;

                    if (keyReply !== null) {
                      self.CategoryComments[keyComment].Replies.splice(keyReply, 1);
                    }else if (keyComment !== null) {
                      self.CategoryComments.splice(keyComment, 1);
                    }

                    let socketData = {
                      Content: comment,
                      GroupID: comment.GroupID,
                      GroupType: comment.GroupType
                    };

                    socket.emit('delete message', socketData);
                    this.$bvToast.toast('Cập nhật thành công', {
                      title: 'Thông báo',
                      variant: 'success'
                    });
                  }
                }, (error) => {
                  console.log(error);
                });
              }
            });

          },

          formatCommentTime(time){
            let dateTime = moment(time);
            if (dateTime.format('L') === moment().format('L')) {
              return dateTime.format('LT');
            }
            if (dateTime.year() === moment().year()) {
              if ((moment().format('x') - dateTime.format('x')) / (60000 * 24 * 60) < 7) {
                return dateTime.format('hh:mm, dddd');
              } else {
                return dateTime.format('hh:mm, DD/MM');
              }
            } else {
              return dateTime.format('hh:mm, MM/DD/YYYY');
            }
          },
        },
        watch: {
          'CategoryID'(){
            this.currentPage = 1;
            this.totalRows = 0;
            this.CategoryComments = [];
            this.fetchData();
          },

          'model.filterDate'(){
            this.perPage = 5;
            this.currentPage = 1;
            this.totalRows = 0;
            this.CategoryComments = [];
            this.fetchData();
          },
          'model.filterDateRange': {
            handler(val){
              // do stuff
              this.perPage = 5;
              this.currentPage = 1;
              this.totalRows = 0;
              this.CategoryComments = [];
              this.fetchData();
            },
            deep: true
          },
          'model.filterUsers'(){
            this.perPage = 5;
            this.currentPage = 1;
            this.totalRows = 0;
            this.CategoryComments = [];
            this.fetchData();
          }
        }
    }
</script>
<style type="text/css">
  .comments .comment-header{
      position: relative;
      padding: 0 0 0 10px;
  }
  .comments .comment-filters {}
  .comments .comment-total {
      font-weight: 500;
  }
  .comments .comment-head {
      display: flex;
      align-items: center;
  }
  .comments .comment-reply {}
  .comments .comment-action {
    cursor: pointer;
    font-weight: 500;
    color: #65676b;
    font-size: .75rem;
  }
  .comments .comment-action:hover, .comment-reply-count span:hover,
  .comments .comment-view-more:hover, .comments .comment-write:hover{
      color: #999;
  }
  .comment-reply-count span {
    color: #65676b;
  }
  .comments .comment-area {
      position: relative;
  }
  .comments .comment-area textarea {
      padding-left: calc(1.75rem + 2px);
  }
  .comments .comment-area .comment-attack {
      position: absolute;
      top: 5px;
      left: 5px;
      width: 21px;
      height: 21px;
      display: flex;
      justify-content: center;
      align-items: center;
  }
  .comments .comment-area .comment-attack i {
      font-size: 18px;
      cursor: pointer;
  }
  .comments .comment-time {
      cursor: pointer;
  }
  .component-category-comments .select2.select2-container {
    min-width: 200px;
  }

  .component-category-comments a{
    word-break: break-word;
  }

  .comments .comment-form {
    display: flex;
    justify-content: flex-end;
    flex-wrap: wrap;
    align-items: center;
    /*border-bottom: 1px solid #ccc;*/
    /*border-top: 1px solid #ccc;*/
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  .comments .comment-form .comment-extension {
    align-self: flex-end;
  }

  .comments .comment-form {
    display: flex;
    justify-content: flex-end;
    flex-wrap: wrap;
    align-items: center;
    /*border-bottom: 1px solid #ccc;*/
    /*border-top: 1px solid #ccc;*/
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  .comments .comment-form .comment-extension {
    align-self: flex-end;
  }

  .comment-form .comment-typing {
    flex: 1 1 auto;
  }
  .comment-form .comment-extension {
    display: flex;
    align-items: center;
    align-self: flex-end;
  }
  .comment-form i.fa:hover {
    color: #73818f;
  }
  .comment-form i.fa {
    font-size: 18px;
    color: #999;
    cursor: pointer;
  }
  .comment-form i.fa:hover {
    color: #73818f;
  }

  .comment-form .comment-typing {
    flex: 1 1 auto;
  }
  .comment-form .comment-extension {
    display: flex;
    align-items: center;
    align-self: flex-end;
  }

  .comments .comment-header{
    position: relative;
    padding: 0 0 0 10px;
  }
  .comments .comment-total {
    font-weight: 500;
  }
  .comments .comment-total span, .comments .comment-total i{
    color: #65676b;
  }
  .comments .comment-head {
    display: flex;
    align-items: center;
  }
  .comments .comment-action {
    cursor: pointer;
    font-weight: 500;
    color: #65676b;
    font-size: .75rem;
  }

  .comment-total {
    display: inline-block;
  }
  .comments .comment-action:hover, .comment-total:hover span, .comment-total:hover i {
    color: #999;
  }
  .comments .comment-area {
    position: relative;
  }
  .comments .comment-area .comment-attack {
    position: absolute;
    top: 5px;
    left: 5px;
    width: 21px;
    height: 21px;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .comments .comment-area .comment-attack i {
    font-size: 18px;
    cursor: pointer;
  }
  .comments .comment-time {
    cursor: pointer;
  }
  /*====================== text ===========================*/
  .comment-content .comment-text {
    white-space: pre-wrap;
    text-align: justify;
  }

  /* ==================== file ============================*/
  .comment-files .comment-file {
    margin-bottom: .25rem;
    cursor: pointer;
  }
  .comment-files .comment-image {
    border: 1px solid #ccc;
    border-radius: 2px;
  }

  .comment-files {
    display: flex;
    flex-wrap: wrap;
  }
  .comment-files .img-block {
    display: block;
    width: 100%;
    height: 100%;
    max-height: 500px;
    overflow: hidden;
  }
  .comment-files .img-block img {
    max-width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .comment-files .comment-application {
    flex: 1 1 100%;
  }
  .comment-files .comment-application .file-right{
    overflow: hidden;
  }

  .comment-files .comment-application span {
    background: #fff;
    color: #373737;
    display: inline-block;
    font-weight: bold;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 140px;
  }
  .comment-files.post-files .comment-application span {
    max-width: 100%;
  }

  .comment-files .comment-video {
    height: auto;
    width: 33%;
  }
  .comment-files .comment-video .video-content {
    height: auto;
    width: 100%;
    max-height: 100%;
    max-width: 100%;
    overflow: hidden;
  }
  .comment-files .comment-video video {
    height: auto;
    width: 100%;
    border-radius: 10px;
  }

  .comment-files .comment-audio {
    width: 85%;
    overflow: hidden;
  }
  .comment-files .comment-audio audio {
    width: 100%;
    height: 32px;
    max-height: 32px;
  }
  .component-category-comments .custom-select2-multiple .select2-selection {
    border: 1px solid #e4e7ea !important;
  }
  .custom-select2-multiple {
    max-width: 450px;
  }
  .component-category-comments .ijcore-date-range .mx-datepicker {
    min-width: 120px;
  }
  /* ==================== file ============================*/


</style>

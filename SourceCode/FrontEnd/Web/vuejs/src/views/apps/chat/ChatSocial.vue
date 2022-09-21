<template>
  <div class="component-chat-social">
    <div class="social-entry d-flex">
      <div class="social-main p-3" @scroll="onScrollPost">
        <div class="social-content">
          <div class="social-search mb-2">
            <b-input-group>
              <b-form-input v-model="model.search" @keypress.enter="searchSocial" type="search" placeholder="Tìm kiếm..."></b-form-input>
              <b-input-group-append>
                <b-button variant="primary" @click="searchSocial"><i class="fa fa-search text-white"></i></b-button>
              </b-input-group-append>
            </b-input-group>
          </div>

          <div class="card create-post" v-if="currentObject || stage.objectType === 'home'">
            <div class="card-body p-0">
              <chat-comment-form
                @on:stored-comment="storedComment($event, null, null)"
                :object="currentObject"></chat-comment-form>
            </div>
          </div>
          <div class="card post" v-for="(post, keyPost) in posts">
            <div class="card-body p-0">
              <div class="post-head pt-3 px-3 d-flex justify-content-between">
                <li class="media align-items-center pr-1 py-1" style="cursor: pointer;">
                  <div class="img-block d-flex mr-3 align-self-center">
                    <img :src="getAvatar(post.UserID)" class="img-avatar">
                  </div>
                  <div class="media-body text-left">
                    <h6 class="post-name">
                      {{getUserName(post.UserID)}}
                      <span class="ml-2" v-if="post.GroupName"><i class="fa fa-caret-right"></i> {{post.GroupName}}</span>
                    </h6>
                    <p class="post-time text-muted">{{formatCommentTime(post.CreatedDate)}}</p>
                  </div>
                </li>
                <div v-if="post.UserID === currentUser.UserID">
                  <b-dropdown class="tab-header-icon action-more" no-caret right title="Thao tác">
                    <template v-slot:button-content>
                      <span class="d-flex align-items-center"><i class="fa fa-ellipsis-v"></i></span>
                    </template>
                    <b-dropdown-item @click="handleAttachDataList(post, keyPost)">Gắn</b-dropdown-item>
                    <b-dropdown-item @click="onShowEdit(keyPost)">Sửa</b-dropdown-item>
                    <b-dropdown-item @click="deleteComment(keyPost)">Xóa</b-dropdown-item>
                  </b-dropdown>
                </div>
              </div>

              <div class="mx-3">
                <chat-comment-form
                  :id="'comment-form-post-edit-' + post.LineID"
                  :object="post"
                  type-form="edit"
                  v-if="posts[keyPost].showEdit"
                  @on:updated-comment="updatedComment($event, keyPost)"></chat-comment-form>
              </div>

              <div class="post-content p-3">
                <div class="comment-text mb-1">
                  <span v-if="post.Content && post.Content !== ':sb-agree' && !post.Content.includes(':sb-action-')" v-html="post.Content"></span>
                  <span v-if="post.Content === ':sb-agree'">
                    <div style="width: 36px; height: 36px">
                    <svg aria-labelledby="js_43x" role="img" height="100%" width="100%" version="1.1" viewBox="0 0 256 256" x="0px" y="0px"><title id="js_43x">Ký hiệu giơ ngón tay cái</title><g><g><polyline fill="transparent" points="256,0 258,256 2,258 "></polyline><path d="M254,147.1c0-12.7-4.4-16.4-9-20.1c2.6-4.2,5.1-10.2,5.1-18c0-15.8-12.3-25.7-32-25.7h-52c-0.5,0-1-0.5-0.9-1
                      c1.4-8.6,3-24,3-31.7c0-16.7-4-37.5-19.3-45.7c-4.5-2.4-8.3-3.7-14.1-3.7c-8.8,0-14.6,3.6-16.7,5.9c-1.3,1.4-1.9,3.3-1.8,5.2
                      l1.3,34.6c0.2,2.8-0.3,5.4-1.6,7.7l-24,47.8c-1.7,3.5-4.2,6.6-7.6,8.5c-3.5,2-6.5,5.9-6.5,9.5v94.8C78,230,94,238,112.3,238h86.1
                      c13.5,0,22.4-4.5,27.2-13.5c4.4-8.2,3.2-15.8,1.4-21.5c7.4-2.3,14.8-8,16.9-18.3c1.3-6.6-0.7-12.1-2.9-16.2
                      C247.5,165,254,159.8,254,147.1z" fill="#4080ff" stroke="transparent" stroke-linecap="round" stroke-width="5%"></path><path d="M56.2,100H13.8C7.3,100,2,105.3,2,111.8v128.5c0,6.5,5.3,11.8,11.8,11.8h42.4c6.5,0,11.8-5.3,11.8-11.8V111.8
                      C68,105.3,62.7,100,56.2,100z" fill="#4080ff"></path></g></g></svg>
                  </div>
                  </span>
                  <span v-if="post.Content && post.Content.includes(':sb-action-')">{{getCommentActionMember(post)}}</span>
                </div>
                <div class="comment-files post-files" v-if="post.ContentFile && post.ContentFile.length">
                  <div class="comment-file mr-1" :class="[(file.FileType === 1) ? 'comment-image' : ((file.FileType === 2) ? 'comment-application' : ((file.FileType === 3) ? 'message-audio' : ((file.FileType === 4) ? 'comment-video' : '')))]" v-for="(file, key) in post.ContentFile">
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
                <div class="comment-links text-muted" v-if="post.Datalist && post.Datalist.length">
                  <div v-for="(datalist, key) in post.Datalist">
                    <i class="fa fa-puzzle-piece"></i>&nbsp;
                    <a href="#" @click="onClickDatalist($event, datalist)" class="text-muted">
                      <span>{{datalist.LinkTableName}}: {{datalist.LinkName}}</span>
                    </a>
                  </div>
                </div>
              </div>
              <div class="post-actions mb-2">
                <div class="post-action post-action-comment px-3" @click="writeComment(post)">
                  <i class="fa fa-comment-o mr-1"></i>
                  <span>Bình luận <span v-if="post.TotalComment">({{post.TotalComment}})</span></span>
                </div>
              </div>
              <div class="post-comments">
                <div class="comment-write d-flex justify-content-between mb-2 px-3"
                     v-if="post.Comments && (posts[keyPost].Comments.length < posts[keyPost].TotalComment)"
                     @click="loadMoreComment(keyPost)"
                     style="font-weight: 500; cursor: pointer">
                  <span v-if="posts[keyPost].Comments.length" style="color: #65676b;">Xem các bình luận trước</span>
                  <span v-else>Xem bình luận</span>
                  <span v-if="posts[keyPost].Comments.length">{{posts[keyPost].Comments.length}}/{{posts[keyPost].TotalComment}}</span>
                </div>
                <div class="comments px-3" v-if="post.Comments && post.Comments.length">
                  <div class="comment media mb-2" v-for="(comment, keyComment) in post.Comments">
                      <div class="d-flex mr-3 align-self-start img-block">
                        <img :src="getAvatar(comment.UserID)" alt="placeholder" width="40" height="40" class="img-avatar">
                      </div>
                      <div class="media-body">
                        <div class="comment-head">
                          <h6 class="comment-user mr-2 mb-0">{{getUserName(comment.UserID)}}</h6>
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
                            <span v-if="comment.Content && comment.Content.includes(':sb-action-')">{{getCommentActionMember(comment)}}</span>
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
                          <div class="comment-links text-muted" v-if="comment.Datalist && comment.Datalist.length">
                            <div v-for="(datalist, key) in comment.Datalist">
                              <i class="fa fa-puzzle-piece"></i>&nbsp;
                              <a href="#" @click="onClickDatalist($event, datalist)" class="text-muted">
                                <span>{{datalist.LinkTableName}}: {{datalist.LinkName}}</span>
                              </a>
                            </div>
                          </div>
                        </div>
                        <div class="comment-actions mb-1">
                          <span class="comment-action comment-reply mr-2" @click="onShowReply( keyPost, keyComment)">Trả lời</span>
                          <span class="comment-action comment-edit mr-2" @click="onShowEdit(keyPost, keyComment)" v-if="comment.UserID === currentUser.UserID">Sửa</span>
                          <span class="comment-action comment-delete mr-3" v-if="comment.UserID === currentUser.UserID" @click="deleteComment(keyPost, keyComment)">Xóa</span>
                          <span class="comment-action comment-delete mr-3" @click="handleAttachDataList(comment, keyPost, keyComment)" v-if="comment.UserID === currentUser.UserID">Gắn</span>
                        </div>

                        <chat-comment-form
                          :id="'comment-form-edit-' + comment.LineID"
                          :object="comment"
                          type-form="edit"
                          @on:updated-comment="updatedComment($event, keyPost, keyComment)"
                          v-if="posts[keyPost].Comments[keyComment].showEdit"></chat-comment-form>

                        <div class="comment-total" v-if="!posts[keyPost].Comments[keyComment].showContentReply && posts[keyPost].Comments[keyComment].TotalComment" @click="loadMoreComment(keyPost, keyComment)">
                          <span style="cursor: pointer;">
                            <i class="fa fa-reply-all" style="font-size: 14px"></i>
                            {{posts[keyPost].Comments[keyComment].TotalComment}} trả lời
                          </span>
                        </div>

                        <div class="comment-write d-flex justify-content-between"
                             v-if="posts[keyPost].Comments[keyComment].Replies && posts[keyPost].Comments[keyComment].showContentReply
                             && (posts[keyPost].Comments[keyComment].Replies.length < posts[keyPost].Comments[keyComment].TotalComment)"
                             @click="loadMoreComment(keyPost, keyComment)"
                             style="font-weight: 500; cursor: pointer; color: #65676b">
                          <span>Xem các trả lời trước</span>
                        </div>
                        <div class="comment-reply mt-2" v-if="comment.Replies && comment.Replies.length && posts[keyPost].Comments[keyComment].showContentReply">
                          <div class="comment mb-2" v-for="(reply, keyReply) in comment.Replies">
                            <div class="media">
                              <div class="d-flex mr-3 align-self-start img-block">
                                <img :src="getAvatar(reply.UserID)" alt="placeholder" width="40" height="40" class="img-avatar">
                              </div>
                              <div class="media-body">
                                <div class="comment-head">
                                  <h6 class="comment-user mr-2 mb-0">{{getUserName(reply.UserID)}}</h6>
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
                                    <span v-if="reply.Content && reply.Content.includes(':sb-action-')">{{getCommentActionMember(reply)}}</span>
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
                                      <i class="fa fa-puzzle-piece"></i>&nbsp;
                                      <a href="#" @click="onClickDatalist($event, datalist)" class="text-muted">
                                        <span>{{datalist.LinkTableName}}: {{datalist.LinkName}}</span>
                                      </a>
                                    </div>
                                  </div>
                                </div>
                                <div class="comment-actions mb-1">
                                  <span class="comment-action comment-reply mr-2" @click="onShowReply(keyPost, keyComment, reply.UserID)">Trả lời</span>
                                  <span class="comment-action comment-edit mr-2" @click="onShowEdit(keyPost, keyComment, keyReply)" v-if="reply.UserID === currentUser.UserID">Sửa</span>
                                  <span class="comment-action comment-delete mr-3" v-if="reply.UserID === currentUser.UserID" @click="deleteComment(keyPost, keyComment, keyReply)">Xóa</span>
                                  <span class="comment-action comment-delete mr-3" @click="handleAttachDataList(reply, keyPost, keyComment, keyReply)" v-if="reply.UserID === currentUser.UserID">Gắn</span>
                                </div>

                                <chat-comment-form
                                  :id="'comment-form-reply-edit-' + reply.LineID"
                                  :object="reply"
                                  type-form="edit"
                                  v-if="posts[keyPost].Comments[keyComment].Replies[keyReply].showEdit"
                                  @on:updated-comment="updatedComment($event, keyPost, keyComment, keyReply)"
                                ></chat-comment-form>
                              </div>
                            </div>

                          </div>
                        </div>
                        <chat-comment-form
                          :id="'comment-form-reply-' + comment.LineID"
                          :object="comment"
                          type-form="reply"
                          :line-i-d-post="post.LineID"
                          :line-i-d-comment="comment.LineID"
                          @on:stored-comment="storedComment($event, keyPost, keyComment)"
                          v-if="posts[keyPost].Comments[keyComment].showReply"></chat-comment-form>
                      </div>
                  </div>
                </div>
                <div class="mx-3">
                  <chat-comment-form
                    :id="'comment-form-' + post.LineID"
                    type-form="reply"
                    :line-i-d-post="post.LineID"
                    @on:stored-comment="storedComment($event, keyPost, null)"
                    :object="post">
                  </chat-comment-form>
                </div>
              </div>
<!--              <div class="comment-older px-3 mb-3">-->
<!--                <div style="cursor: pointer;">-->
<!--                  <div class="comment-write" @click="writeComment(post)" style="font-weight: 500">Viết bình luận...</div>-->
<!--                </div>-->
<!--              </div>-->
            </div>
          </div>

          <div class="spinners" style="height: 40px" v-if="stage.loadingPost">
            <div class="sk-double-bounce">
              <div class="sk-child sk-double-bounce1"></div>
              <div class="sk-child sk-double-bounce2"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="social-sidebar bg-light">
        <div class="social-object px-2">
          <b-card no-body>
            <b-tabs pills card>

<!--              <b-tab :active="(stage.objectType === 'home') ? true : false" @click="changeObjectType('home')">-->
<!--                <template v-slot:title>-->
<!--                  <span title="Trang của tôi"><i class="fa fa-home"></i></span>-->
<!--                </template>-->
<!--                  <ul class="objects-list">-->
<!--                    <li class="object-item media align-items-center pl-3 pr-1 py-2"-->
<!--                        @click="onAddTabChat(user)"-->
<!--                        v-for="(user, key) in groupsPrivate">-->
<!--                      <div class="img-block d-flex mr-3 align-self-center">-->
<!--                        <img :src="getAvatar(user.UserID)" class="img-avatar"/>-->
<!--                        <span aria-label="Đang hoạt động" v-if="$store.state.chat.online[user.UserID]" class="chat-icon-active"></span>-->
<!--                      </div>-->
<!--                      <div class="media-body">-->
<!--                        <span class="mb-0" :class="[(user.read) ? '' : 'text-bold']">{{user.UserName}}</span>-->
<!--                      </div>-->
<!--                    </li>-->
<!--                  </ul>-->
<!--              </b-tab>-->

              <b-tab :active="(stage.objectType === 'group') ? true : false" @click="changeObjectType('group')">
                <template v-slot:title>
                  <span title="Nhóm"><i class="fa fa-users"></i></span>
                </template>
                  <ul class="objects-list">
                    <li class="object-item media align-items-center pl-3 pr-1 py-2"
                        :class="['object-item-group-' + group.GroupID, (currentObject && currentObject.GroupID === group.GroupID) ? 'active' : '']"
                        @click="changeGroup(group)" v-for="(group, key) in groups">
                      <div class="media-body">
                        <span class="mb-0"><i class="fa fa-users mr-2" style="color: #999"></i> {{group.GroupName}}</span>
                      </div>
                    </li>
                  </ul>
              </b-tab>
              <b-tab disabled :active="(stage.objectType === 'hashtag') ? true : false" @click="changeObjectType('hashtag')">
                <template v-slot:title>
                  <span title="Hashtag"><i class="fa fa-tags"></i></span>
                </template>
              </b-tab>
              <b-tab disabled :active="(stage.objectType === 'listing') ? true : false" @click="changeObjectType('listing')">
                <template v-slot:title>
                  <span title="Danh mục"><i class="fa fa-file-text-o"></i></span>
                </template>
              </b-tab>
              <b-tab disabled :active="(stage.objectType === 'transaction') ? true : false" @click="changeObjectType('transaction')">
                <template v-slot:title>
                  <span title="Giao dịch"><i class="fa fa-exchange"></i></span>
                </template>
              </b-tab>
            </b-tabs>
            <b-card-footer>
              <div class="d-flex align-content-center justify-content-center">
                <b-form-input v-model="model.searchContact" @input="onSearchContact" @keydown="onKeydownSearchContact" placeholder="Tìm kiếm"></b-form-input>
              </div>
            </b-card-footer>
          </b-card>

        </div>
      </div>
    </div>

    <chat-modal-datalist
      v-model="commentAttach"
      ref="chat-modal-datalist"
      ref-modal="modal-datalist"
      id-modal="modal-datalist"
      title-modal="Gắn đối tượng"
      @on:save-category-key="handleSaveCategoryKey"
      size-modal="xl"></chat-modal-datalist>

    <div class="chat-tabs">
      <chat-tab
        v-model="tabsChat"
        :ref="'chat-tab-' + tabChat.GroupID"
        :key="tabChat.GroupID"
        :tab-chat="tabChat"
        :current-user="currentUser"
        :all-users="allUsers"
        :members-groups="membersInGroup"
        :groups="groups"
        v-show="tabsChat[key].show && !tabsChat[key].delete"
        @on:remove-tab-chat="onRemoveTabChat(key)"
        @on:user-read="onUserReadMessage(key)"
        @on:new-message="onAddNewMessage($event, key)"
        v-for="(tabChat, key) in tabsChat"></chat-tab>
    </div>

  </div>
</template>
<style type="text/css">
  .social-entry , .social-sidebar, .social-object {
    height: 100%;
  }
  .social-entry {
    position: relative;
  }
  .component-chat-social .social-sidebar {
    width: 300px;
    position: absolute;
    right: 1rem;
    overflow-y: auto;
  }
  .social-sidebar .card {
    border-radius: 0;
    border: none;
    border-left: 1px solid #c8ced3;
  }
  .component-chat-social ul.objects-list {
    margin: 0;
    padding: 0;
    height: 100%;
  }
  .social-object .object-item {
    cursor: pointer;
  }
  .social-object .card {
    margin-bottom: 0;
    height: 100%;
  }
  .social-object .tabs {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
  }
  .social-object .tabs .tab-content {
    overflow-y: auto;
  }
  .social-object .tab-content {
    border: none;
  }
  .social-object .tab-content .tab-pane {
    padding: 0;
  }

  .social-object .object-item:hover, .social-object .object-item.active {
    background-color: #dddfe2;
    box-shadow: 1px 0 0 #eaebed inset;
  }
  .object-label span {
    color: #4a4a4a;
    font-size: 11px;
    font-weight: bold;
    overflow: hidden;
    text-overflow: ellipsis;
    text-transform: uppercase;
  }

  .social-main {
    flex: 1 1 auto;
    overflow-y: auto;
  }
  .social-main i.fa {
    font-size: 18px;
    color: #999;
    cursor: pointer;
  }
  .social-main i.fa:hover {
    color: #73818f;
  }

  .social-main .social-content {
    /*max-width: 600px;*/
    margin-right: 300px;
  }
  .post .post-time {
    font-size: 12px;
    margin-bottom: 0;
  }
  .post .post-name {
    margin-bottom: .25rem;
  }
  .post .post-actions {
    display: flex;
    align-items: center;
    border-top: 1px solid #eee;
    background: #f7f8f9;
    /*font-weight: bold;*/
    font-size: .9em;
    white-space: nowrap;
    height: 40px;
  }
  .post .post-action {
    cursor: pointer;
  }

  [contentEditable=true]:empty:not(:focus):before {
    content: attr(data-text);
    color: #999;
  }
  .post .post-actions i, .post .post-actions span {
    color: #666;
  }

  .post .comment-form {
    display: flex;
    justify-content: flex-end;
    flex-wrap: wrap;
    align-items: center;
    /*border-bottom: 1px solid #ccc;*/
    /*border-top: 1px solid #ccc;*/
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  .post .comment-form .comment-extension {
    align-self: flex-end;
  }

  .create-post .comment-input {
    padding: .5rem 0;
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
  .component-chat-social .comment-text {
    white-space: pre-wrap;
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
  }
  .comment-files .img-block img {
    max-width: 100%;
    height: auto;
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
  /* ==================== file ============================*/

  .post-files .comment-image {
    flex: 33%;
    height: auto;
    min-width: 200px;
  }

  .chat-tabs {
    position: fixed;
    bottom: 0;
    right: 285px; display: flex;
    flex-direction: row-reverse
  }


</style>
<script>
  import ApiService from '@/services/api.service';
  import ChatCommentForm from "./partials/ChatCommentForm";
  import ChatModalDatalist from "./partials/ChatModalDatalist";
  import ChatTab from "./ChatTab";
  import moment from "moment";
  moment.locale('vi');
  export default {
    name: 'chat-social',
    data () {
      return {
        perPage: 15,
        page: 1,
        lastPage: 1,

        model: {
          searchContact: '',
          search: ''
        },

        posts: [],
        groups: [],
        groupsPrivate: [],
        allGroupsPrivate: [],
        allGroups: [],

        allUsers: [],
        membersInGroup: [],
        lastMessageGroup: [],
        userRead: [],
        newMessage: (this.$store.state.chat.newMessage) ? this.$store.state.chat.newMessage : 0,

        currentObject: null,
        currentUser: null,

        keyMessageAttach: null,
        commentAttach: null,
        Datalist: [],

        tabsChat: [],

        stage: {
          objectType: 'home',
          loadingPost: false
        }
      }
    },
    components: {
      ChatCommentForm,
      ChatModalDatalist,
      ChatTab
    },
    beforeCreate() {},
    mounted() {
      let self = this;
      this.fetchData();

      $('.navbar-nav .social-icon').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.changeObjectType('home');
      });

      // Socket events
      socket.on('new message', (data) => {
        let keyPost = null;
        let keyComment = null;

        if (data.LineIDComment !== null) {
          keyPost = _.findIndex(self.posts, ['LineID', Number(data.LineIDPost)]);
          if (keyPost > -1) {
            keyComment = _.findIndex(self.posts[keyPost].Comments, ['LineID', Number(data.LineIDComment)]);
          } else {
            return;
          }
        }else if (data.LineIDPost !== null) {
          keyPost = _.findIndex(self.posts, ['LineID', Number(data.LineIDPost)]);
        }
        self.storedComment(data, keyPost, keyComment);
      });
      socket.on('delete message', (data) => {
        let keyPost = null, keyComment = null, keyReply = null;
        if (data.LineIDReply !== null) {
          keyPost = _.findIndex(self.posts, ['LineID', data.LineIDPost]);
          if (keyPost > -1) {
            keyComment = _.findIndex(self.posts[keyPost].Comments, ['LineID', data.LineIDComment]);
            if (keyComment > -1) {
              keyReply = _.findIndex(self.posts[keyPost].Comments[keyComment].Replies, ['LineID', data.LineIDReply]);
              self.posts[keyPost].Comments[keyComment].Replies.splice(keyReply, 1);
            }
          }
        }else if (data.LineIDComment !== null) {
          keyPost = _.findIndex(self.posts, ['LineID', data.LineIDPost]);
          if (keyPost > -1) {
            keyComment = _.findIndex(self.posts[keyPost].Comments, ['LineID', data.keyComment]);
            self.posts[keyPost].Comments.splice(keyComment, 1);
          }
        }else {
          keyPost = _.findIndex(self.posts, ['LineID', data.LineIDPost]);
          if (keyPost > -1) {
            self.posts.splice(keyPost, 1);
          }
        }

      });
      socket.on('seen message', (data) => {
        let tabChatIndex = _.findIndex(self.tabsChat, ['GroupID', data.GroupID]);
        if (tabChatIndex > -1) {
          self.tabsChat[tabChatIndex].seen = true;
          if (data.GroupType !== 1) {
            let userExist = _.find(self.tabsChat[tabChatIndex].userSeen, ['UserID', data.UserIDSeen]);
            if (!userExist) {
              if (!self.tabsChat[tabChatIndex].userSeen) {
                self.tabsChat[tabChatIndex].userSeen = [];
              }
              self.tabsChat[tabChatIndex].userSeen.push({
                UserID: data.UserIDSeen
              });
            }
          }
        }
        this.$forceUpdate();
      });
      socket.on('user joined', (data) => {
        self.usersConnected = data.usersConnected;
        self.$store.commit('userOnline', {});
        _.forEach(self.usersConnected, function (userOnline, key) {
          self.$store.commit('userOnline', {UserID: userOnline.UserID, value: true});
        });
        self.sortGroups();
        self.$forceUpdate();
      });
      socket.on('user left', (data) => {
        _.remove(self.usersConnected, ['SocketID', data.SocketID]);
        self.$store.commit('userOnline', {UserID: data.UserID, value: false});
        self.sortGroups();
        self.$forceUpdate();
      });
    },
    methods: {
      fetchData() {
        let self = this;
        let requestData = {
          method: 'post',
          url: 'extensions/api/social',
          data: {}
        };
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            // self.groups = responsesData.data.data;
            self.groups = _.filter(responsesData.data.AllGroups, function (o) {
              return o.GroupType !== 1;
            });
            self.allGroups = self.groups;
            self.membersInGroup = responsesData.data.MembersInGroup;
            self.allUsers = responsesData.data.AllUsers;
            self.currentUser = responsesData.data.CurrentUser;
            self.lastMessageGroup = responsesData.data.LastMessageGroup;
            self.userRead = responsesData.data.UserRead;

            let groupsPrivate = _.filter(responsesData.data.AllGroups, ['GroupType', 1]);
            self.groupsPrivate = [];
            _.forEach(groupsPrivate, function (groupPrivate, key) {
              let tmpObj = groupPrivate;
              let member = _.find(self.membersInGroup, function (o) {
                return o.GroupID === groupPrivate.GroupID && o.UserID !== self.currentUser.UserID;
              });

              if (member) {
                let lastMessage = _.find(self.lastMessageGroup, ['GroupID', groupPrivate.GroupID]);
                if (lastMessage) {
                  let userRead = _.find(self.userRead, {
                    ChatContentID: lastMessage.LineID,
                    UserID: self.currentUser.UserID
                  });
                  if (userRead || lastMessage.UserID === self.currentUser.UserID) {
                    tmpObj.read = true;
                    tmpObj.seen = true;
                  } else {
                    tmpObj.read = false;
                    tmpObj.seen = false;
                  }
                }else {
                  tmpObj.read = true;
                  tmpObj.seen = false;
                }

                tmpObj.UserID = member.UserID;
                tmpObj.UserName = member.UserName;
                tmpObj.GroupName = member.UserName;
                tmpObj.DateJoin = member.DateJoin;
                self.groupsPrivate.push(tmpObj);

              }
            });

            _.forEach(self.allUsers, function (user, key) {
              if (user.UserID === self.currentUser.UserID) {
                return;
              }
              let groupExist = _.find(self.groupsPrivate, ['UserID', user.UserID]);
              if (groupExist) {
                return;
              } else {
                let tmpObj = {};
                tmpObj.GroupID = null;
                tmpObj.GroupType = 1;
                tmpObj.UserID = user.UserID;
                tmpObj.UserName = user.FullName;
                tmpObj.GroupName = user.FullName;
                tmpObj.read = true;
                tmpObj.seen = false;
                self.groupsPrivate.push(tmpObj);
              }
            });
            self.sortGroups();
            self.allGroupsPrivate = self.groupsPrivate;
            self.loadPost(null, '');
          }
        }, (error) => {
          console.log(error);
        });
      },
      searchSocial() {
        this.posts = [];
        this.page = 1;
        this.loadPost(this.currentObject, '');
      },
      loadPost(object, type = '') {
        let self = this;
        let requestData = {
          method: 'post',
          url: 'extensions/api/social/load-post',
          data: {
            type: 'social',
            objectType: this.stage.objectType,
            per_page: this.perPage,
            page: this.page,
          }
        };
        this.currentObject = object;
        if (object && object.GroupID) {
          requestData.data.GroupID = object.GroupID;
        }
        if (this.model.search) {
          requestData.data.search = this.model.search;
        }
        this.stage.loadingPost = true;
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.perPage = responsesData.data.data.per_page;
            self.lastPage = responsesData.data.data.last_page;
            self.page = responsesData.data.data.current_page;

            let posts = responsesData.data.data.data;
            // self.allUsers = responsesData.data.AllUsers;

            self.Datalist = responsesData.data.Datalist;

            _.forEach(posts, function (post, key) {
              if (responsesData.data.PostFile.length) {
                let contentFile = _.filter(responsesData.data.PostFile, ['ChatContentID', post.LineID]);
                posts[key].ContentFile = contentFile;
              }
              if (responsesData.data.Comments.length) {
                let comments = _.filter(responsesData.data.Comments, ['ParentID', post.LineID]);
                comments = _.orderBy(comments, ['LineID'], ['asc']);
                _.forEach(comments, function (comment, key) {
                  let contentFile = _.filter(responsesData.data.CommentFile, ['ChatContentID', comment.LineID]);
                  comments[key].ContentFile = contentFile;

                  // let commentReplies = _.filter(responsesData.data.CommentReplies, ['ParentID', comment.LineID]);
                  // _.forEach(commentReplies, function (reply, key) {
                  //   let contentFile = _.filter(responsesData.data.CommentReplyFile, ['ChatContentID', reply.LineID]);
                  //   commentReplies[key].ContentFile = contentFile;
                  //   commentReplies[key].showEdit = false;
                  //   commentReplies[key].Datalist = self.getDatalist(reply);
                  // });
                  // comments[key].Replies = commentReplies;
                  comments[key].Replies = [];
                  comments[key].showReply = false;
                  comments[key].showEdit = false;
                  comments[key].Datalist = self.getDatalist(comment);
                  comments[key].showContentReply = false;

                  comments[key].current_page = null;
                  comments[key].from = null;
                  comments[key].per_page = null;
                  comments[key].last_page = null;
                  comments[key].page = null;

                });
                // comments = comments.reverse();
                posts[key].Comments = comments;
              }

              posts[key].current_page = null;
              posts[key].from = null;
              posts[key].per_page = null;
              posts[key].last_page = null;
              posts[key].page = null;

              posts[key].showEdit = false;
              posts[key].Datalist = self.getDatalist(post);
            });
            self.posts = _.concat(self.posts, posts);
          }
          self.stage.loadingPost = false;
        }, (error) => {
          console.log(error);
        });
      },
      loadMoreComment(keyPost = null, keyComment = null){
        let self = this;
        let type = '';
        let page = null, per_page = null;
        let ParentID = null;

        if (keyComment !== null) {
          type = 'comment';
          page = (this.posts[keyPost].Comments[keyComment].page) ? this.posts[keyPost].Comments[keyComment].page + 1 : 1;
          ParentID = this.posts[keyPost].Comments[keyComment].LineID;
          this.posts[keyPost].Comments[keyComment].showContentReply = true;
        } else {
          page = (this.posts[keyPost].page) ? this.posts[keyPost].page + 1 : 1;
          ParentID = this.posts[keyPost].LineID;
          type = 'post';
        }

        let requestData = {
          method: 'post',
          url: 'extensions/api/social/load-more-comment',
          data: {
            type: 'social',
            page: page,
            ParentID: ParentID
          }
        };


        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            let comments = responsesData.data.data.data;
            self.Datalist = responsesData.data.Datalist;

            _.forEach(comments, function (comment, key) {
              let contentFile = _.filter(responsesData.data.CommentFile, ['ChatContentID', comment.LineID]);
              comments[key].ContentFile = contentFile;
              comments[key].Datalist = self.getDatalist(comment);
              comments[key].showReply = false;
              comments[key].showEdit = false;
              comments[key].showContentReply = false;
            });

            comments = comments.reverse();

            if (type === 'post') {
              if (!self.posts[keyPost].Comments) {
                self.posts[keyPost].Comments = [];
              }
              self.posts[keyPost].current_page = responsesData.data.data.current_page;
              self.posts[keyPost].per_page = responsesData.data.data.per_page;
              self.posts[keyPost].last_page = responsesData.data.data.last_page;
              self.posts[keyPost].page = responsesData.data.data.current_page;
              self.posts[keyPost].total = responsesData.data.data.total;

              self.posts[keyPost].Comments = _.concat(comments, self.posts[keyPost].Comments);
              // self.posts[keyPost].Comments = _.orderBy(self.posts[keyPost].Comments, ['LineID'], ['asc']);
              self.posts[keyPost].Comments = _.uniqBy(self.posts[keyPost].Comments, 'LineID');
            }

            if (type === 'comment') {
              if (!self.posts[keyPost].Comments[keyComment].Replies) {
                self.posts[keyPost].Comments[keyComment].Replies = [];
              }
              self.posts[keyPost].Comments[keyComment].current_page = responsesData.data.data.current_page;
              self.posts[keyPost].Comments[keyComment].per_page = responsesData.data.data.per_page;
              self.posts[keyPost].Comments[keyComment].last_page = responsesData.data.data.last_page;
              self.posts[keyPost].Comments[keyComment].page = responsesData.data.data.current_page;
              self.posts[keyPost].Comments[keyComment].total = responsesData.data.data.total;

              self.posts[keyPost].Comments[keyComment].Replies = _.concat(comments, self.posts[keyPost].Comments[keyComment].Replies);
              // self.posts[keyPost].Comments[keyComment].Replies = _.orderBy(self.posts[keyPost].Comments[keyComment].Replies, ['LineID'], ['asc']);
              self.posts[keyPost].Comments[keyComment].Replies = _.uniqBy(self.posts[keyPost].Comments[keyComment].Replies, 'LineID');
            }

            this.$forceUpdate();
          }
        }, (error) => {
          console.log(error);
        });
      },
      getDatalist(comment){
        let datalist = [];
        let self = this;
        if (comment.CategoryKey) {
          let categories = comment.CategoryKey.split('_');
          _.forEach(categories, function (category, key) {
            let pieces = category.split(':');
            let table = pieces[0];
            let tableID = pieces[1];
            let tableName = table.charAt(0).toUpperCase() + table.slice(1);
            let tmpObj = {};
            if (table !== 'tag') {
              let categoryExist = _.find(self.Datalist[table], [tableName + 'ID', Number(tableID)]);
              if (categoryExist) {
                tmpObj.LinkID = tableID;
                tmpObj.LinkNo = categoryExist[tableName + 'No'];
                tmpObj.LinkName = categoryExist[tableName + 'Name'];
                tmpObj.DatalistTable = table;

                switch (table) {
                  case 'task':
                    tmpObj.LinkTableName = 'Công việc';
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
        return datalist;
      },
      onClickDatalist(e, datalist) {
        e.preventDefault();
        e.stopPropagation();
        if (datalist.DatalistTable === 'task') {
          let routeData = this.$router.resolve({name: 'task-task-view', params: {id: datalist.LinkID}});
          window.open(routeData.href, '_blank');
        }
      },
      onScrollPost(e){
        if($(e.target).scrollTop() + $(e.target).innerHeight() >= $(e.target)[0].scrollHeight) {
          if (this.page < this.lastPage) {
            this.page += 1;
            this.loadPost(this.currentObject);
          }
        }
      },
      storedComment(comment, keyPost = null, keyComment = null){
        if (keyPost !== null && keyPost > -1 && keyComment === null) {
          comment.showReply = false;
          comment.showEdit = false;
          if (this.posts[keyPost]) {
            if (this.posts[keyPost].Comments && this.posts[keyPost].Comments.length) {
              this.posts[keyPost].Comments.push(comment);
            } else {
              this.posts[keyPost].Comments = [];
              comment.Replies = [];
              this.posts[keyPost].Comments.push(comment);
            }
            this.posts[keyPost].TotalComment++;
          }
        }

        if (keyPost !== null && keyComment !== null && keyPost > -1 && keyComment > -1) {
          comment.showEdit = false;
          if (this.posts[keyPost] && this.posts[keyPost].Comments[keyComment]) {
            this.posts[keyPost].Comments[keyComment].showContentReply = true;
            if (this.posts[keyPost].Comments && this.posts[keyPost].Comments[keyComment].Replies && this.posts[keyPost].Comments[keyComment].Replies.length) {
              this.posts[keyPost].Comments[keyComment].Replies.push(comment);
            } else {
              this.posts[keyPost].Comments[keyComment].Replies = [];
              this.posts[keyPost].Comments[keyComment].Replies.push(comment);
            }
            this.posts[keyPost].Comments[keyComment].TotalComment++;
          }
        }

        if (keyPost === null && keyComment === null) {
          comment.Comments = [];
          comment.showEdit = false;
          this.posts.unshift(comment);
        }

        this.$forceUpdate();

      },
      updatedComment(comment, keyPost, keyComment = null, keyReply = null){
        if (keyReply !== null) {
          this.posts[keyPost].Comments[keyComment].Replies[keyReply].Content = comment.Content;
          this.posts[keyPost].Comments[keyComment].Replies[keyReply].ContentFile = comment.ContentFile;
          this.posts[keyPost].Comments[keyComment].Replies[keyReply].CreatedDate = comment.CreatedDate;
          this.posts[keyPost].Comments[keyComment].Replies[keyReply].UpdatedDate = comment.UpdatedDate;
          this.posts[keyPost].Comments[keyComment].Replies[keyReply].showEdit = false;
        } else if (keyComment !== null) {
          this.posts[keyPost].Comments[keyComment].Content = comment.Content;
          this.posts[keyPost].Comments[keyComment].ContentFile = comment.ContentFile;
          this.posts[keyPost].Comments[keyComment].CreatedDate = comment.CreatedDate;
          this.posts[keyPost].Comments[keyComment].UpdatedDate = comment.UpdatedDate;
          this.posts[keyPost].Comments[keyComment].showEdit = false;
        } else {
          this.posts[keyPost].Content = comment.Content;
          this.posts[keyPost].ContentFile = comment.ContentFile;
          this.posts[keyPost].CreatedDate = comment.CreatedDate;
          this.posts[keyPost].UpdatedDate = comment.UpdatedDate;
          this.posts[keyPost].showEdit = false;
        }

        this.$bvToast.toast('Cập nhật thành công', {
          title: 'Thông báo',
          variant: 'success'
        });
      },
      deleteComment(keyPost = null, keyComment = null, keyReply = null) {
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
            let LineIDPost = null, LineIDComment = null, LineIDReply = null;
            if (keyReply !== null) {
              LineIDPost = this.posts[keyPost].LineID;
              LineIDComment = this.posts[keyPost].Comments[keyComment].LineID;
              LineIDReply = this.posts[keyPost].Comments[keyComment].Replies[keyReply].LineID;

              LineID = LineIDReply;

            }else if (keyComment !== null) {
              LineIDPost = this.posts[keyPost].LineID;
              LineIDComment = this.posts[keyPost].Comments[keyComment].LineID;

              LineID = LineIDComment;
            }else {
              LineIDPost = this.posts[keyPost].LineID;
              LineID = LineIDPost;
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
                comment.LineIDPost = LineIDPost;
                comment.LineIDComment = LineIDComment;
                comment.LineIDReply = LineIDReply;

                if (keyReply !== null) {
                  self.posts[keyPost].Comments[keyComment].Replies.splice(keyReply, 1);
                  self.posts[keyPost].Comments[keyComment].TotalComment--;
                }else if (keyComment !== null) {
                  self.posts[keyPost].Comments.splice(keyComment, 1);
                  self.posts[keyPost].TotalComment--;
                }else {
                  self.posts.splice(keyPost, 1);
                }

                let socketData = {
                  Content: comment,
                  GroupID: comment.GroupID,
                  GroupType: comment.GroupType
                };

                if (comment.GroupType === 1) {
                  let member = _.find(self.membersInGroup, function (o) {
                    return o.GroupID === comment.GroupID && o.UserID !== self.currentUser.UserID;
                  });
                  if (member) {
                    socketData.UserID = member.UserID;
                  }
                }

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
      onShowReply(keyPost, keyComment = null, UserID = null) {
        if (this.posts[keyPost].Comments[keyComment].TotalComment && (!this.posts[keyPost].Comments[keyComment].Replies || (this.posts[keyPost].Comments[keyComment].Replies && !this.posts[keyPost].Comments[keyComment].Replies.length))) {
          this.loadMoreComment(keyPost, keyComment);
        }
        this.posts[keyPost].Comments[keyComment].showReply = true;
        this.posts[keyPost].Comments[keyComment].showEdit = false;
        let html = '';
        if (UserID) {
          let userName = this.getUserName(UserID);
          html = '<span class="label tag-user">@' + userName + '</span> &nbsp;';
        }
        this.$nextTick(() => {
          let form = this.$el.querySelector('#comment-form-reply-' + this.posts[keyPost].Comments[keyComment].LineID);
          let input = this.$el.querySelector('#comment-form-reply-' + this.posts[keyPost].Comments[keyComment].LineID + ' .comment-input');
          $(input).html(html);

          $('.main-body').animate({
            scrollTop: $(form).offset().top
          }, 500);
          $(input).focus();
          this.placeCaretAtEnd(input);
        });
      },
      onShowEdit(keyPost, keyComment = null, keyReply = null){
        if (keyReply !== null) {
          let reply = this.posts[keyPost].Comments[keyComment].Replies[keyReply];
          this.posts[keyPost].Comments[keyComment].Replies[keyReply].showEdit = true;
          this.posts[keyPost].Comments[keyComment].showReply = false;
          this.$nextTick(() => {
            let input = this.$el.querySelector('#comment-form-reply-edit-' + reply.LineID + ' .comment-input');
            $(input).html(reply.Content);
            $(input).focus();
            this.placeCaretAtEnd(input);
          });
        } else if (keyComment !== null) {
          let comment = this.posts[keyPost].Comments[keyComment];
          this.posts[keyPost].Comments[keyComment].showEdit = true;
          this.posts[keyPost].Comments[keyComment].showReply = false;
          this.$nextTick(() => {
            let input = this.$el.querySelector('#comment-form-edit-' + comment.LineID + ' .comment-input');
            $(input).html(comment.Content);
            $(input).focus();
            this.placeCaretAtEnd(input);
          });
        } else {
          let post = this.posts[keyPost];
          this.posts[keyPost].showEdit = true;
          this.$nextTick(() => {
            let input = this.$el.querySelector('#comment-form-post-edit-' + post.LineID + ' .comment-input');
            $(input).html(post.Content);
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
      changeGroup(group){
        // $('.object-item').removeClass('active');
        // $('.object-item-group-' + group.GroupID).addClass('active');
        this.posts = [];
        this.page = 1;
        this.loadPost(group);
      },
      getAvatar(UserID) {
        let user = _.find(this.allUsers, ['UserID', UserID]);
        if (user) {
          return this.$store.state.appRootApi + user.Avata;
        }
        return ''
      },
      getUserName(UserID){
        let user = _.find(this.allUsers, ['UserID', UserID]);
        if (user) {
          return user.FullName;
        }
        return ''
      },
      writeComment(post) {
        let $input = $(this.$el.querySelector('#comment-form-' + post.LineID + ' .comment-input'));
        $input.focus();
        $('.main-body').animate({
          scrollTop: $(this.$el.querySelector('#comment-form-' + post.LineID)).offset().top
        }, 500);
      },
      handleAttachDataList(comment, keyPost = null, keyComment = null, keyReply = null){
        comment.keyPost = keyPost;
        comment.keyComment = keyComment;
        comment.keyReply = keyReply;
        this.commentAttach = comment;
        this.$refs['chat-modal-datalist'].init();
      },
      handleSaveCategoryKey(datalist){
        if (!this.commentAttach) return;
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

        // if (categoryKey !== '') {
        // }

        let requestData = {
          method: 'post',
          url: 'extensions/api/chat/update-category-key',
          data: {
            LineID: this.commentAttach.LineID,
            CategoryKey: categoryKey
          }
        };

        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            // self.messageArray[this.keyMessageAttach].CategoryKey = categoryKey;
            // self.messageArray[this.keyMessageAttach].Datalist = datalist;

            if (self.commentAttach.keyReply !== null) {
              self.posts[self.commentAttach.keyPost].Comments[self.commentAttach.keyComment].Replies[self.commentAttach.keyReply].Datalist = datalist;
              self.posts[self.commentAttach.keyPost].Comments[self.commentAttach.keyComment].Replies[self.commentAttach.keyReply].CategoryKey = categoryKey;
            }else if (self.commentAttach.keyComment !== null) {
              self.posts[self.commentAttach.keyPost].Comments[self.commentAttach.keyComment].Datalist = datalist;
              self.posts[self.commentAttach.keyPost].Comments[self.commentAttach.keyComment].CategoryKey = categoryKey;
            } else {
              self.posts[self.commentAttach.keyPost].Datalist = datalist;
              self.posts[self.commentAttach.keyPost].CategoryKey = categoryKey;
            }

            self.$bvToast.toast('Cập nhật thành công', {
              title: 'Thông báo',
              variant: 'success'
            });

            self.$forceUpdate();
          }
        }, (error) => {
          console.log(error);
        });

      },
      onUserReadMessage(key){
        if (this.tabsChat[key].GroupID) {
          let lastMessage = document.querySelector('.last-content-' + this.tabsChat[key].GroupID);
          if (lastMessage && lastMessage.classList.contains('unread')) {

            let self = this;
            let requestData = {
              method: 'post',
              url: 'extensions/api/chat/read-message',
              data: {
                GroupID: this.tabsChat[key].GroupID,
              }
            };

            ApiService.customRequest(requestData).then((responses) => {
              let responsesData = responses.data;
              if (responsesData.status === 1) {
                $(lastMessage).removeClass('unread');
                $(lastMessage).closest('.user-unread').removeClass('user-unread');
                self.newMessage--;
                socket.emit('read message', {
                  GroupID: self.tabsChat[key].GroupID,
                  GroupType: self.tabsChat[key].GroupType,
                  UserID: self.CurrentUser.UserID
                });

                // let dataSeen = self.tabsChat[key];
                let dataSeen = responsesData.data;
                dataSeen.GroupID = self.tabsChat[key].GroupID;
                dataSeen.GroupType = self.tabsChat[key].GroupType;
                dataSeen.UserIDSeen = self.CurrentUser.UserID;
                dataSeen.UserIDSocket = self.tabsChat[key].UserID;
                socket.emit('seen message', dataSeen);

                let index = _.findIndex(self.AllUsers, ['GroupID', self.tabsChat[key].GroupID]);
                if (index > -1) {
                  self.AllUsers[index].read = true
                }

                let indexInterested = _.findIndex(self.UsersInterested, ['GroupID', self.tabsChat[key].GroupID]);
                if (indexInterested > -1) {
                  self.UsersInterested[indexInterested].read = true;
                }

                let indexOther = _.findIndex(self.UsersOther, ['GroupID', self.tabsChat[key].GroupID]);
                if (indexOther > -1) {
                  self.UsersOther[indexOther].read = true;
                }

                let indexGroup = _.findIndex(self.Groups, ['GroupID', self.tabsChat[key].GroupID]);
                if (indexGroup > -1) {
                  self.Groups[indexGroup].read = true;
                }

                self.$forceUpdate();
              }
            }, (error) => {
              console.log(error);
            });
          }

        }
      },
      onAddNewMessage(content, key){
        this.tabsChat[key].seen = false;
        this.tabsChat[key].userSeen = [];
        this.$forceUpdate();
      },
      changeObjectType(objectType){
        this.stage.objectType = objectType;
        this.posts = [];
        this.page = 1;
        this.loadPost(null, '');
      },
      onAddTabChat(user){
        let self = this;
        let tabIndex = _.findIndex(this.tabsChat, ['UserID', user.UserID]);
        let userSys = _.find(this.allUsers, ['UserID', user.UserID]);
        let tmpObj = {};

        if (!user.read) {
          this.newMessage--;
        }

        if (tabIndex > -1) {
          this.tabsChat[tabIndex].show = true;
          this.tabsChat[tabIndex].delete = false;
        } else {
          tmpObj.GroupName = user.UserName;
          tmpObj.Avatar = userSys.Avata;
          tmpObj.GroupID = user.GroupID;
          tmpObj.UserID = user.UserID;
          tmpObj.GroupType = 1;
          tmpObj.LineID = this.tabsChat.length;
          tmpObj.show = true;
          tmpObj.delete = false;
          tmpObj.seen = user.seen;
          tabIndex = this.tabsChat.length;
          this.tabsChat.push(tmpObj);
        }

        let tabsShow = _.filter(this.tabsChat, {
          show: true,
          delete: false
        });
        if (this.tabsChat.length > 3 && tabsShow.length > 3) {
          let indexHide = _.findIndex(this.tabsChat, function (o) {
            return o.show === true && o.delete === false && o.GroupID !== self.tabsChat[tabIndex].GroupID;
          });
          if (indexHide > -1) {
            this.tabsChat[indexHide].show = false;
          }
        }
      },
      onRemoveTabChat(key){
        this.tabsChat[key].show = false;
        this.tabsChat[key].delete = true;

        // show next chat
        let indexShow = _.findIndex(this.tabsChat, {
          show: false,
          delete: false
        });
        if (indexShow > -1) {
          this.tabsChat[indexShow].show = true;
        }

        let tabsDelete = _.filter(this.tabsChat, ['delete', true]);
        if (tabsDelete.length === this.tabsChat.length) {
          this.tabsChat = [];
        }
        this.$forceUpdate();
      },
      onSearchContact() {
        let self = this;
        if (this.stage.objectType === 'home') {
          if (this.model.searchContact) {
            this.groupsPrivate = _.filter(this.allGroupsPrivate, function (o) {
              let noAccent = __.cleanAccents(o.GroupName);
              let groupNameLower = _.toLower(o.GroupName);
              let noAccentLower = _.toLower(noAccent);

              return o.GroupName.includes(self.model.searchContact) || noAccent.includes(self.model.searchContact)
                || groupNameLower.includes(self.model.searchContact) || noAccentLower.includes(self.model.searchContact);
            });
          } else {
            this.groupsPrivate = this.allGroupsPrivate;
          }
        }

        if (this.stage.objectType === 'group') {
          if (this.model.searchContact) {
            this.groups = _.filter(this.allGroups, function (o) {
              let noAccent = __.cleanAccents(o.GroupName);
              let groupNameLower = _.toLower(o.GroupName);
              let noAccentLower = _.toLower(noAccent);

              return o.GroupName.includes(self.model.searchContact) || noAccent.includes(self.model.searchContact)
                || groupNameLower.includes(self.model.searchContact) || noAccentLower.includes(self.model.searchContact);
            });
          } else {
            this.groups = this.allGroups;
          }
        }

      },
      onKeydownSearchContact(e){
        let code = e.which;

        if (code === 40) {
          e.preventDefault();
          if (!$('.objects-list .object-item.active').length) {
            $('.objects-list .object-item').first().addClass('active');
          } else {
            let $currentActive = $('.objects-list .object-item').filter('.active');
            $currentActive.removeClass('active');
            $currentActive.next().addClass('active');
          }

        } else if (code === 38) {
          e.preventDefault();
          // up here
          if (!$('.objects-list .object-item.active').length) {
            $('.objects-list .object-item').last().addClass('active');
          } else {
            let $currentActive = $('.objects-list .object-item').filter('.active');
            $currentActive.removeClass('active');
            $currentActive.prev().addClass('active');
          }
        }else if (code === 13) {
          $('.objects-list .object-item.active').trigger('click');
        }

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
      getCommentActionMember(comment){
        let self = this, pieces = comment.Content.split('_'), text = '', listUsersName = '',
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
          text = ((comment.UserID === this.currentUser.UserID) ? 'Bạn' : this.getLastName(comment.UserID)) + ' đã thêm ' + listUsersName + ' vào nhóm';
        }
        if (pieces[1] === 'remove') {
          text = ((comment.UserID === this.currentUser.UserID) ? 'Bạn' : this.getLastName(comment.UserID)) + ' đã loại ' + listUsersName + ' khỏi nhóm';
        }

        if (pieces[1] === 'leave') {
          text = ((comment.UserID === this.currentUser.UserID) ? 'Bạn' : this.getLastName(comment.UserID)) + ' đã rời khỏi nhóm';
        }

        return text;
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
      sortGroups(){
        let self = this;
        let onlineObj = this.$store.state.chat.online;
        _.forEach(this.groupsPrivate, function (group, key) {
          if (onlineObj[group.UserID]) {
            self.groupsPrivate[key].online = onlineObj[group.UserID];
          } else {
            self.groupsPrivate[key].online = false;
          }
        });
        self.groupsPrivate = _.orderBy(self.groupsPrivate, ['online', 'UserName'], ['desc', 'asc']);
        this.$forceUpdate();
      }
    },
    watch: {
      newMessage() {
        if (this.newMessage > 0) {
          this.$store.commit('newMessage', this.newMessage);
        } else {
          this.$store.commit('newMessage', 0);
        }
      }
    },
    destroyed() {
      $('.navbar-nav .social-icon').unbind('click');
    }
  }
</script>

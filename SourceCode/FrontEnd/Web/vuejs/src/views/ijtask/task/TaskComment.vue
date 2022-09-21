import Swal from "sweetalert2";
import Swal from "sweetalert2";
import Swal from "sweetalert2";
import Swal from "sweetalert2";
<template>
    <div class="comments component-comments">
        <div class="comment-header d-flex justify-content-between align-items-center mb-2 flex-wrap flex-sm-wrap flex-md-nowrap flex-lg-nowrap">
            <div class="comment-total mb-2 mb-sm-2 mb-md-0 mb-lg-0">Thảo luận ({{totalRows}})</div>
            <div class="comment-filters d-flex align-items-center flex-wrap flex-sm-wrap flex-md-nowrap flex-lg-nowrap">
                <b-form-select class="mr-0 mr-sm-0 mr-md-2 mr-lg-2 mb-2 mb-sm-2 mb-md-0 mb-lg-0" v-model="model.filterDate" :options="[{value: 1, text: 'Mới nhất'}, {value: 2, text: 'Ngày tạo'}]"></b-form-select>
                <ijcore-date-range class="mr-0 mr-sm-2 mr-md-2 mr-lg-2 mb-2 mb-sm-0 mb-md-0 mb-lg-0" v-model="model.filterDateRange"></ijcore-date-range>
                <ijcore-select2-server
                        v-model="model.filterUsers"
                        :url="appRootApi + '/task/api/common/get-employee'"
                        id-name="EmployeeID"
                        text-name="EmployeeName"
                        placeholder="Chọn nhân viên"
                        :allowClear="true"
                        :multiple="true"
                        :delay="500">
                </ijcore-select2-server>
            </div>
        </div>
        <div class="comment-body">
            <div class="comment-area comment-area-reply comment-area-reply-0 mb-2" id="comment-area-write">
            <textarea
                    @keyup.enter="handleSubmitStoreComment($event, null)"
                    style="height: 50px"
                    placeholder="Nhập nội dung trao đổi" rows="2" wrap="soft" class="form-control"></textarea>
                <div class="comment-attack" @click="onClickFileAttach(0, 'reply')">
                    <i class="fa fa-paperclip"></i>
                </div>
                <input type="file" hidden class="input-file-attach-0" @change="handleFileAttack($event, 0)"/>
            </div>
            <div class="card comment-area-file-attach container-area-file-attach-0"
                 style="border: 1px solid #BABFC7;padding-left: 10px; padding-right: 10px; display: none">
                <div class="card-content">
                    <div class="card-body" style="padding: 5px;">
                        <img class="img-attach img-attach-0"/>
                        <span class="doc-file-attach doc-file-attach-0"></span>
                    </div>
                </div>
            </div>
            <div class="comments-media mb-4">
                <div class="media mb-2" :class="[(comment.ParentID) ? 'comment-reply' : '']" v-for="(comment, key) in model.taskComments" :key="key">
                    <div class="d-flex mr-3 align-self-start">
                        <img class="img-avatar" :src="$store.state.appRootApi + comment.Avata"
                             alt="placeholder" width="40" height="40"/>
                    </div>
                    <div class="media-body">
                        <div class="comment-head">
                            <h6 class="comment-user mr-2 mb-0">{{comment.EmployeeName}}</h6>
                            <h6 class="comment-time mb-0 text-muted" v-if="model.filterDate === 2">{{comment.CommentDate | convertTimeToHMTime}} - {{comment.CommentDate | convertTimeToTimeAgo}}</h6>
                            <h6 class="comment-time mb-0 text-muted" v-if="model.filterDate ===1">{{comment.LastCommentDate | convertTimeToHMTime}} - {{comment.LastCommentDate | convertTimeToTimeAgo}}</h6>
                        </div>
                        <div class="comment-content mb-1">
                            <span v-html="filterCommentContent(comment.CommentContent)"></span>
                        </div>
                        <div class="card comment-file-attach mb-1" v-if="comment.FileAttach">
                            <div class="card-content">
                                <div class="card-body" style="padding: 5px">
                                    <a v-if="comment.IsImg" :href="appRootApi + comment.FileAttach" target="_blank" :class="'href-file-' + comment.LineID">
                                        <img :class="'img-file-' + comment.LineID" :src="appRootApi + comment.FileAttach" width="150"/>
                                    </a>
                                    <a v-if="!comment.IsImg" :href="appRootApi + comment.FileAttach" :class="'href-file-' + comment.LineID">
                                        <span class="fa fa-paperclip">{{comment.FileAttachName}}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="comment-actions mb-1">
                            <span class="comment-action comment-reply mr-2" @click="onReplyComment(comment.LineID, key)">Trả lời</span>
                            <span class="comment-action comment-edit mr-2" v-if="comment.EmployeeID === model.currentEmployeeID" @click="onEditComment(comment.LineID, 'comment')">Sửa</span>
                            <span class="comment-action comment-delete mr-3" v-if="comment.EmployeeID === model.currentEmployeeID" @click="handleSubmitDeleteComment(comment.LineID, 'comment')">Xóa</span>
                        </div>

                        <div class="comment-reply-count" v-if="getTotalReplyComment(comment.LineID)">
                        <span style="cursor: pointer" v-if="!model.taskComments[key].showReply" @click="onShowReplyComment(comment.LineID)">
                            <i class="fa fa-reply-all"></i>
                            {{getTotalReplyComment(comment.LineID)}} trả lời
                        </span>
                        </div>
                        <div v-if="model.taskComments[key].showReply" class="media mb-2 comment-reply" v-for="(reply, keyReply) in filterRepliesComment(comment.LineID)" :key="keyReply">
                            <div class="d-flex mr-3 align-self-start">
                                <img class="img-avatar" :src="$store.state.appRootApi + reply.Avata"
                                     alt="placeholder" width="40" height="40"/>
                            </div>
                            <div class="media-body">
                                <div class="comment-head">
                                    <h6 class="comment-user mr-2 mb-0">{{reply.EmployeeName}}</h6>
                                    <h6 class="comment-time mb-0 text-muted" v-if="model.filterDate === 2">{{reply.CommentDate | convertTimeToHMTime}} - {{reply.CommentDate | convertTimeToTimeAgo}}</h6>
                                    <h6 class="comment-time mb-0 text-muted" v-if="model.filterDate === 1">{{reply.LastCommentDate | convertTimeToHMTime}} - {{reply.LastCommentDate | convertTimeToTimeAgo}}</h6>
                                </div>
                                <div class="comment-content mb-1">
                                    <span v-html="filterCommentContent(reply.CommentContent)"></span>
                                </div>
                                <div class="card comment-file-attach mb-1" v-if="reply.FileAttach">
                                    <div class="card-content">
                                        <div class="card-body" style="padding: 5px">
                                            <a v-if="reply.IsImg" :href="appRootApi + reply.FileAttach" target="_blank" :class="'href-file-' + reply.LineID">
                                                <img :class="'img-file-' + reply.LineID" :src="appRootApi + reply.FileAttach" width="150"/>
                                            </a>
                                            <a v-if="!reply.IsImg" :href="appRootApi + reply.FileAttach" :class="'href-file-' + reply.LineID">
                                                <span class="fa fa-paperclip">{{reply.FileAttachName}}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="comment-actions mb-1">
                                    <span class="comment-action comment-edit mr-2" v-if="reply.EmployeeID === model.currentEmployeeID" @click="onEditComment(reply.LineID, 'reply')">Sửa</span>
                                    <span class="comment-action comment-delete mr-3" v-if="reply.EmployeeID === model.currentEmployeeID" @click="handleSubmitDeleteComment(reply.LineID, 'reply')">Xóa</span>
                                </div>
                                <div class="comment-area comment-area-edit" :class="'comment-area-edit-' + reply.LineID" style="display: none">
                                <textarea
                                        placeholder="Nhập nội dung trao đổi" rows="2" wrap="soft" class="form-control"
                                        @keyup.enter="handleSubmitEditComment($event, reply.LineID, 'reply')"
                                        style="height: 50px"
                                        v-html="reply.CommentContent"></textarea>
                                    <div class="comment-attack" @click="onClickFileAttach(reply.LineID)">
                                        <i class="fa fa-paperclip"></i>
                                    </div>
                                    <input type="file" hidden :class="'input-file-attach-' + reply.LineID" @change="handleFileAttack($event, reply.LineID)"/>
                                </div>
                                <div class="card comment-area-file-attach"
                                     :class="'container-area-file-attach-' + reply.LineID"
                                     style="border: 1px solid #BABFC7;padding-left: 10px; padding-right: 10px; display: none">
                                    <div class="card-content">
                                        <div class="card-body" style="padding: 5px;">
                                            <img class="img-attach" :class="'img-attach-' + reply.LineID"/>
                                            <span class="doc-file-attach" :class="'doc-file-attach-' + reply.LineID"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="comment-area comment-area-reply" :class="'comment-area-reply-' + comment.LineID" style="display: none;">
                        <textarea placeholder="Nhập nội dung trao đổi" rows="2" wrap="soft" class="form-control"
                                  style="height: 50px"
                                  @keyup.enter="handleSubmitStoreComment($event, comment)"></textarea>
                            <div class="comment-attack" @click="onClickFileAttach(comment.LineID, 'reply')">
                                <i class="fa fa-paperclip"></i>
                            </div>
                            <input type="file" hidden :class="'input-file-attach-' + comment.LineID" @change="handleFileAttack($event, comment.LineID)"/>
                        </div>
                        <div class="comment-area comment-area-edit" :class="'comment-area-edit-' + comment.LineID" style="display: none">
                        <textarea
                                placeholder="Nhập nội dung trao đổi" rows="2" wrap="soft" class="form-control"
                                v-html="comment.CommentContent"
                                style="height: 50px"
                                @keyup.enter="handleSubmitEditComment($event, comment.LineID, 'comment')"></textarea>
                            <div class="comment-attack" @click="onClickFileAttach(comment.LineID, 'edit')">
                                <i class="fa fa-paperclip"></i>
                            </div>
                            <input type="file" hidden :class="'input-file-attach-' + comment.LineID" @change="handleFileAttack($event, comment.LineID)">
                        </div>
                        <div class="card comment-area-file-attach" :class="'container-area-file-attach-' + comment.LineID"
                             style="border: 1px solid #BABFC7;padding-left: 10px; padding-right: 10px; display: none">
                            <div class="card-content">
                                <div class="card-body" style="padding: 5px">
                                    <img class="img-attach" :class="'img-attach-' + comment.LineID"/>
                                    <span class="doc-file-attach" :class="'doc-file-attach-' + comment.LineID"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="comment-older">
                <div style="cursor: pointer;" @click="handleShowOlderComment" v-if="model.taskComments.length < totalRows" class="mb-2">
                    <span class="comment-view-more mr-2" style="font-weight: 500; color: #65676b">Xem thêm</span>
                    <span class="text-muted">({{model.taskComments.length}}/{{totalRows}})</span>
                </div>
                <div style="cursor: pointer;" @click="onScrollWriteComment">
                    <span class="comment-write" style="font-weight: 500">Viết trao đổi...</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import IjcoreDateRange from '@/components/IjcoreDateRange';
    import IjcoreSelect2Server from '@/components/IjcoreSelect2Server';

    const TaskComment = 'task/api/task/comment';
    const StoreComment = 'task/api/task/storeComment';
    const EditComment = 'task/api/task/editComment';
    const DeleteComment = 'task/api/task/deleteComment';
    export default {
        name: 'task-comment',
        components: {
            IjcoreDateRange,
            IjcoreSelect2Server
        },
        props: ['task'],
        data() {
            return {
                perPage: 5,
                currentPage: 1,
                totalRows: 0,
                model: {
                    taskComments: [],
                    taskRepliesComment: [],
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
          this.init();
        },
        methods: {
            init(){
                let self = this;
                let requestData = {
                    method: 'post',
                    url: TaskComment,
                    data: {
                        TaskID: this.task.TaskID,
                        per_page: this.perPage,
                        page: this.currentPage,
                        filterDate: this.model.filterDate,
                        filterDateRange: (this.model.filterDateRange) ? this.model.filterDateRange : null,
                        filterUsers: this.model.filterUsers
                    },
                };

                // this.$store.commit('isLoading', true);
                ApiService.customRequest(requestData).then((response) => {
                    self.$store.commit('isLoading', false);
                    let responseData = response.data;
                    if (responseData.status === 1) {
                        if (responseData.data.taskComments.data) {
                            self.model.taskComments = _.concat(self.model.taskComments, responseData.data.taskComments.data);
                        }
                        self.perPage = responseData.data.taskComments.per_page;
                        self.currentPage = responseData.data.taskComments.current_page;
                        self.totalRows = responseData.data.taskComments.total;
                        if (responseData.data.taskRepliesComment) {
                            self.model.taskRepliesComment = _.concat(self.model.taskRepliesComment, responseData.data.taskRepliesComment);
                        }
                        self.model.currentEmployeeID = responseData.data.EmployeeID;
                        self.model.currentUserID = responseData.data.UserID;

                    } else {
                        self.$store.commit('isLoading', false);
                    }
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
            filterCommentContent(value){
                return value.replace(/\r\n|\r|\n/g,"<br/>");
            },
            filterRepliesComment(commentID){
                // let replies = _.filter(this.model.taskRepliesComment, ['ParentID', commentID]);
                let replies = _.filter(this.model.taskRepliesComment, function (o) {
                    return o.ParentID == commentID;
                });
                return replies;
            },
            onReplyComment(LineID, key){
                $('.comment-area-reply-' + LineID).show();
                $('.comment-area-reply-' + LineID + ' textarea').focus();
                $('.comment-area-edit-' + LineID).hide();
            },
            onEditComment(LineID, type = ''){
                let comment = null;
                if (type === 'comment') {
                    comment = _.find(this.model.taskComments, ['LineID', LineID]);
                }else {
                    comment = _.find(this.model.taskRepliesComment, ['LineID', LineID]);
                }
                if (this.model.currentEmployeeID === comment.EmployeeID) {
                    $('.comment-area-edit-' + LineID).show();
                    $('.comment-area-edit-' + LineID + ' textarea').focus();
                    $('.comment-area-reply-' + LineID).hide();
                } else {
                    this.$bvToast.toast('Bạn không có quyền sửa', {
                        title: 'Thông báo',
                        variant: 'danger',
                        solid: true
                    });
                }

            },
            onClickFileAttach(LineID = 0, type = 'edit'){
                if (type === 'reply') {
                    $('.comment-area-reply .input-file-attach-' + LineID).trigger('click');
                } else {
                    $('.comment-area-edit .input-file-attach-' + LineID).trigger('click');
                }
            },
            onScrollWriteComment(){
                $('.main-body').animate({
                    scrollTop: $("#comment-area-write").offset().top
                }, 500);
                $('.comment-area-reply-0 textarea').focus();
            },
            onShowReplyComment(LineID){
              let index = _.findIndex(this.model.taskComments, ['LineID', LineID]);
              this.$set(this.model.taskComments[index], 'showReply', true);
            },
            getTotalReplyComment(LineID){
                let repliesComment = _.filter(this.model.taskRepliesComment, function (o) {
                    return o.ParentID == LineID;
                });
                return repliesComment.length;
            },
            handleFileAttack(e, LineID){
                let element = e.srcElement;
                let ext = element.files[0].name.split('.').pop().toLowerCase();
                let ValidImageTypes = ["gif", "jpeg", "jpg", "png", "ico", "psd", "ai"];
                let ValidDocTypes = ["pptx", "ppt", "pps", "xls", "xlsx", "csv", "doc", "docx", "pdf", "txt", "cif", "zip", "rar"];
                if ($.inArray(ext, ValidImageTypes) < 0 && $.inArray(ext, ValidDocTypes) < 0) {
                    this.$bvToast.toast('Tệp này không được phép upload', {
                        title: 'Thông báo',
                        variant: 'danger',
                        solid: true
                    });
                    $(this).empty();
                }else{
                    if($.inArray(ext, ValidImageTypes) >= 0){
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $('.img-attach-' + LineID)
                                .attr('src', e.target.result)
                                .width(150);
                        };
                        reader.readAsDataURL(element.files[0]);
                        $('.container-area-file-attach-' + LineID).show();
                        $('.img-attach-' + LineID).show();
                        $('.doc-file-attach-' + LineID).hide();
                    }

                    if($.inArray(ext, ValidDocTypes) >= 0){
                        $('.doc-file-attach-' + LineID).text(" " + element.files[0].name);
                        $('.container-area-file-attach-' + LineID).show();
                        $('.img-attach-' + LineID).hide();
                        $('.doc-file-attach-' + LineID).show();
                    }
                }
            },
            handleSubmitStoreComment(e, comment = null){
                e.preventDefault();
                if (e.shiftKey) return;

                let self = this;
                let LineID = (comment) ? comment.LineID : 0;
                let index = _.findIndex(this.model.taskComments, ['LineID', LineID]);
                let fileAttach = $('.input-file-attach-' + LineID).prop('files')[0];

                let formData = new FormData();
                let commentContent = $('.comment-area-reply-' + LineID + ' textarea').val();
                formData.append('Comment', commentContent);
                formData.append('TaskID', this.task.TaskID);
                formData.append('ParentID', (comment) ? comment.LineID : 0);
                formData.append('FileAttach', (fileAttach) ? fileAttach : null);

                let requestData = {
                    method: 'post',
                    url: StoreComment,
                    data: formData,
                };

                ApiService.customRequest(requestData).then((response) => {
                    let responseData = response.data;
                    if (responseData.status === 1) {
                        let newComment = responseData.data;
                        if (comment) {
                            self.model.taskRepliesComment.push(newComment);
                            self.refreshComment(index);
                        } else {
                            self.model.taskComments.unshift(newComment);
                        }
                        $('.comment-area-reply-' + LineID + ' textarea').val('');
                        $('.container-area-file-attach-' + LineID).hide();
                      if (LineID) {
                        $('.comment-area-reply-' + LineID).hide();
                      }

                      // notification
                      this.$_storeTaskNotice(this.task.TaskID, 'comment');
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
            handleSubmitEditComment(e, LineID, type = ''){
                let comment = null;
                let key = null;
                if (type === 'comment') {
                    comment = _.find(this.model.taskComments, ['LineID', LineID]);
                    key = _.findIndex(this.model.taskComments, ['LineID', LineID]);
                }else {
                    comment = _.find(this.model.taskRepliesComment, ['LineID', LineID]);
                    key = _.findIndex(this.model.taskRepliesComment, ['LineID', LineID]);
                }

                if (e.shiftKey) return;
                if (this.model.currentEmployeeID !== comment.EmployeeID) {
                    this.$bvToast.toast('Bạn không có quyền sửa', {
                        title: 'Thông báo',
                        variant: 'danger',
                        solid: true
                    });
                    return;
                }
                let self = this;
                let fileAttach = $('.input-file-attach-' + LineID).prop('files')[0];
                let commentContent = $('.comment-area-edit-' + LineID + ' textarea').val();

                let formData = new FormData();
                formData.append('Comment', commentContent);
                formData.append('CommentID', LineID);
                formData.append('ParentID', comment.ParentID);
                formData.append('FileAttach', (fileAttach) ? fileAttach : null);

                let requestData = {
                    method: 'post',
                    url: EditComment,
                    data: formData
                };

                ApiService.customRequest(requestData).then((response) => {
                    let responseData = response.data;
                    if (responseData.status === 1) {
                        if (type === 'comment') {
                            self.$set(self.model.taskComments[key], 'CommentContent', responseData.data.CommentContent);
                            self.$set(self.model.taskComments[key], 'FileAttach', responseData.data.FileAttach);
                            self.$set(self.model.taskComments[key], 'IsImg', responseData.data.IsImg);
                            self.$set(self.model.taskComments[key], 'FileAttachName', responseData.data.FileAttachName);
                            self.$set(self.model.taskComments[key], 'CommentDate', responseData.data.CommentDate);
                            self.$set(self.model.taskComments[key], 'LastCommentDate', responseData.data.LastCommentDate);
                        } else {
                            self.model.taskRepliesComment[key].CommentContent = responseData.data.CommentContent;
                            self.model.taskRepliesComment[key].FileAttach = responseData.data.FileAttach;
                            self.model.taskRepliesComment[key].IsImg = responseData.data.IsImg;
                            self.model.taskRepliesComment[key].FileAttachName = responseData.data.FileAttachName;
                            self.model.taskRepliesComment[key].CommentDate = responseData.data.CommentDate;
                            self.model.taskRepliesComment[key].LastCommentDate = responseData.data.LastCommentDate;
                            let indexParent = _.findIndex(self.model.taskComments, function (o) {
                                return o.LineID == comment.ParentID;
                            });
                            self.refreshComment(indexParent);
                        }

                        $('.comment-area-edit-' + LineID).hide();
                        $('.container-area-file-attach-' + LineID).hide();
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
            handleSubmitDeleteComment(LineID, type = ''){
                let comment = null;
                let key = null;
                if (type === 'comment') {
                    comment = _.find(this.model.taskComments, ['LineID', LineID]);
                    key = _.findIndex(this.model.taskComments, ['LineID', LineID]);
                }else {
                    comment = _.find(this.model.taskRepliesComment, ['LineID', LineID]);
                    key = _.findIndex(this.model.taskRepliesComment, ['LineID', LineID]);
                }

                if (this.model.currentEmployeeID !== comment.EmployeeID) {
                    this.$bvToast.toast('Bạn không có quyền xóa', {
                        title: 'Thông báo',
                        variant: 'danger',
                        solid: true
                    });
                    return;
                }

                // kiểm tra nếu comment quá 1 ngày thì không được quyền xóa nữa.
                let commentDate = new Date(comment.CommentDate);
                let currentDate = new Date();
                if ((currentDate.getTime() - commentDate.getTime()) / 86400000 > 1) {
                    this.$bvToast.toast('Trao đổi đã được khóa', {
                        title: 'Thông báo',
                        variant: 'warning',
                        solid: true
                    });
                    return;
                }

                // kiểm tra nếu commnet có con thì không cho phép xóa
                if (type === 'comment') {
                    let repliesComment = _.filter(this.model.taskRepliesComment, function (o) {
                        return o.ParentID == LineID;
                    });
                    if (repliesComment.length) {
                        this.$bvToast.toast('Không thể xóa trao đổi này', {
                            title: 'Thông báo',
                            variant: 'warning',
                            solid: true
                        });
                        return;
                    }
                }

                let self = this;
                Swal.fire({
                    title: '',
                    text: 'Bạn có muốn xóa trao đổi?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Đóng'
                }).then((result) => {
                    if (result.value) {
                        let requestData = {
                            method: 'post',
                            url: DeleteComment,
                            data: {
                                CommentID: comment.LineID,
                            },
                        };

                        ApiService.customRequest(requestData).then((response) => {
                            let responseData = response.data;
                            if (responseData.status === 1) {
                                if (type === 'comment') {
                                    self.model.taskComments.splice(key, 1);
                                } else {
                                    self.model.taskRepliesComment.splice(key, 1);
                                    let indexParent = _.findIndex(self.model.taskComments, function (o) {
                                        return o.LineID == comment.ParentID;
                                    });
                                    self.refreshComment(indexParent);
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
            handleShowOlderComment(){
                this.currentPage += 1;
                this.init();
            },
            refreshComment(index){
                if (typeof this.model.taskComments[index].LineID === 'number') {
                    this.model.taskComments[index].LineID = String(this.model.taskComments[index].LineID);
                } else {
                    this.model.taskComments[index].LineID = Number(this.model.taskComments[index].LineID);
                }
            },
        },
        watch: {
            'task': {
                handler(val){
                    // do stuff
                    this.perPage = 5;
                    this.currentPage = 1;
                    this.totalRows = 0;
                    this.model.taskComments = [];
                    this.model.taskRepliesComment = [];

                    this.init();

                },
                deep: true
            },
            'model.filterDate'(){
                this.perPage = 5;
                this.currentPage = 1;
                this.totalRows = 0;
                this.model.taskComments = [];
                this.model.taskRepliesComment = [];
                this.init();
            },
            'model.filterDateRange': {
                handler(val){
                    // do stuff
                    this.perPage = 5;
                    this.currentPage = 1;
                    this.totalRows = 0;
                    this.model.taskComments = [];
                    this.model.taskRepliesComment = [];
                    this.init();
                },
                deep: true
            },
            'model.filterUsers'(){
                this.perPage = 5;
                this.currentPage = 1;
                this.totalRows = 0;
                this.model.taskComments = [];
                this.model.taskRepliesComment = [];
                this.init();
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
    .component-comments .select2.select2-container {
      min-width: 200px;
    }
</style>

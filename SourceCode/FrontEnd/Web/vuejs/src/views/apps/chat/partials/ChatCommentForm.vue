<template>
  <div class="comment-form px-3 py-2 mb-3" :class="(isSendingMessage) ? 'app-disable' : ''" ref="comment-form" @drop="onDropFiles">
    <div class="comment-typing">
      <div class="comment-upload-preview" v-if="imagesPreview.length || filesPreview.length">
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
      <div data-text="Nhập bình luận..."
           class="comment-input area-auto-resize" placeholder="Nhập bình luận..."
           @keyup="onKeyupComment($event)"
           @keydown="onKeydownSearchSuggest"
           @keypress.enter="handleSubmitComment($event)"
           :contenteditable="!isSendingMessage">
      </div>
    </div>
    <div class="comment-extension ml-2">
      <span class="mr-3" title="Gọi điện thoại"><i class="fa fa-phone" aria-hidden="true"></i></span>
      <span class="mr-3" title="Bắt đầu cuộc họp" @click="onClickVideoConference"><i class="fa fa-video-camera" aria-hidden="true"></i></span>
      <span class="mr-3" title="Thêm ảnh" @click="onClickUploadImages"><i class="fa fa-file-image-o" aria-hidden="true"></i></span>
      <span class="mr-3" title="Thêm tệp" @click="onClickUploadFiles('file')"><i class="fa fa-paperclip" aria-hidden="true"></i></span>
      <span class="mr-3" title="Thêm audio/video" @click="onClickUploadFiles('audio-video')"><i class="fa fa-file-video-o" aria-hidden="true"></i></span>
      <span class="mr-3" title="Chọn biểu tượng cảm xúc" @click="onToggleEmoji"><i class="fa fa-smile-o" aria-hidden="true"></i></span>
      <span class="mr-3" title="Bình luận" @click="handleSubmitComment($event)"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></span>
      <div class="container-emoji">
        <input class="emojionearea-input" hidden>
      </div>
    </div>
    <div class="comment-suggestion">
      <chat-suggestion v-if="stage.showSuggestion" v-model="suggested" :suggest-array="suggestArray" @on:suggested="onChatSuggested($event)" @on:close="onCloseSuggested"></chat-suggestion>
    </div>

    <div class="comment-hidden">
      <input type="file" multiple hidden @change="uploadImages" name="upload-images" ref="upload-images" id="upload-images" accept="image/*">
      <input type="file" multiple hidden @change="uploadFiles('file')" name="upload-files" ref="upload-files" id="upload-files" accept=".xlsx, .xls, .doc, .docx, .ppt, .pptx, .txt, .pdf, .zip, .rar, .cif">
      <input type="file" multiple hidden @change="uploadFiles('audio-video')" name="upload-audios-videos" ref="upload-audios-videos" id="upload-audios-videos" accept="audio/*, video/*">
    </div>

  </div>
</template>
<style type="text/css">

  /*.message-typing textarea {*/
  .comment-typing .comment-input {
    border: none;
    height: auto;
    min-height: 21px;
    overflow: auto;
    text-align: left;
    word-break: break-word;
    padding: 0;
    white-space: pre-wrap;
  }
  /*.message-typing textarea:focus {*/
  .comment-typing .comment-input:focus {
    outline: none;
    box-shadow: none;
  }

  .comment-upload-preview {
    padding: .25rem .5rem;
    display: flex;
    overflow-y: auto;
    align-items: center;
  }
  .comment-upload-preview .img-preview {
    height: 54px;
    width: 54px;
    min-width: 54px;
    position: relative;
  }
  .comment-upload-preview .img-preview img {
    height: 100%;
    width: 100%;
    object-fit: cover;
  }
  .comment-upload-preview .upload-item i.icon-remove {
    position: absolute;
    right: 5px;
    top: 0;
    cursor: pointer;
    display: none;
  }
  .comment-upload-preview .upload-item:hover i.icon-remove{
    display: block;
  }
  .comment-upload-preview .file-preview {
    display: flex;
    border-radius: 8px;
    height: 44px;
    width: 180px;
    background: #fff;
    border: 1px solid rgba(0, 0, 0, .15);
    position: relative;
  }
  .comment-upload-preview .file-preview .file-left {
    height: 42px;
    flex: 0 0 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, .15);
    border-top-left-radius: 7px;
    border-bottom-left-radius: 7px;
  }
  .comment-upload-preview .file-preview .file-right {
    overflow: hidden;
    text-align: left;
    padding: 0 .5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;

  }
  .comment-upload-preview .file-preview .file-left i {
    font-size: 24px;
  }
  .comment-upload-preview .file-preview span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: block;
    line-height: normal;
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

  .comment-form .comment-suggestion {
    position: fixed;
    left: 0;
    top: 0;
    transform: translateY(-100%);
    background: #fff;
    border-radius: 4px;
    box-shadow: 0 4px 8px 0 rgba(0,26,51,.1);
    border: 1px solid #e1e4ea;
  }
</style>

<script>
  import ApiService from '@/services/api.service';
  import ChatSuggestion from "./ChatSuggestion";
  import emojiJs from 'emojionearea/dist/emojionearea.min';
  import emojiCss from 'emojionearea/dist/emojionearea.min.css';
  import {position} from "caret-pos";
  export default {
    name: 'chat-comment-form',
    data () {
      return {
        model: {
          comment: ''
        },
        imagesPreview: [],
        filesPreview: [],
        dragAndDropCapable: false,
        isSendingMessage: false,
        searchSuggestion: '',
        suggested: null,
        suggestArray: [],
        membersGroups: [],
        stage: {
          showSuggestion: false
        }
      }
    },
    props: {
      value: [Array, Object],
      object: [Array, Object],
      ParentID: {
        type: Number,
        default: 0
      },
      typeForm: {
        type: String,
        default: 'reply'
      },
      LineIDPost: {
        type: [Number, String, Object],
        default: null
      },
      LineIDComment: {
        type: [Number, String, Object],
        default: null
      },
      typeStore: {
        type: String,
        default: 'social'
      },
      CategoryKey: [Object, Array],
      Category: [String],
      CategoryID: [String, Number],
      GroupName: [String]
    },
    components: {
      ChatSuggestion
    },
    beforeCreate() {},
    mounted() {
      let self = this;

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
          this.$refs['comment-form'].addEventListener(evt, function(e){
            e.preventDefault();
            e.stopPropagation();
          }.bind(this), false);
        }.bind(this));
      }

      // copy and paste image
      this.$el.querySelector('.comment-typing .comment-input').addEventListener('paste', function (e) {
        let clipboardData, pastedData;

        // Stop data actually being pasted into div
        e.stopPropagation();
        e.preventDefault();

        // Get pasted data via clipboard API
        clipboardData = e.clipboardData || window.clipboardData;
        pastedData = clipboardData.getData('Text');

        // Do whatever with pasteddata
        self.insertTextAtCaret(pastedData);

        self.handlePasteImage(e);
      }, false);

    },
    methods: {
      handleSubmitComment(e) {
        // if (this.$refs['comment-form'].classList.contains('app-disable')) {
        //   return false;
        // }

        this.model.comment = this.$el.querySelector('.comment-typing .comment-input').innerHTML;
        this.model.comment = this.urlify(this.model.comment);
        e.preventDefault();
        if (!this.model.comment && !this.imagesPreview.length && !this.filesPreview.length || (e.shiftKey && e.keyCode === 13)) return false;

        if (this.isSendingMessage) {
          return false;
        }
        this.isSendingMessage = true;

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
        if (this.LineIDPost !== null || this.LineIDComment !== null) {
          formData.append('ParentID', this.object.LineID);
        }

        formData.append('GroupID', (this.object && this.object.GroupID) ? this.object.GroupID : null);
        formData.append('GroupType', (this.object && this.object.GroupType) ? this.object.GroupType : null);
        formData.append('message', this.model.comment);
        if (this.object && this.object.GroupType === 1) {
          formData.append('UserID', this.object.UserID);
          formData.append('UserName', this.object.GroupName);
        }

        let requestData = {
          method: 'post',
          url: 'extensions/api/chat/store-message',
          data: formData
        };

        if (this.object && this.typeForm === 'edit') {
          formData.append('LineID', this.object.LineID);
          requestData.url = 'extensions/api/chat/update-message';
        }

        if (this.typeForm === 'reply' && (!this.object || (this.object && !this.object.GroupID)) && this.typeStore === 'social') {
          // formData.append('Social', true);
          formData.append('TypeStore', 'social');
        }

        if (this.typeStore === 'category') {
          let CategoryKeyString = JSON.stringify(this.CategoryKey);
          formData.append('TypeStore', 'category');
          formData.append('CategoryKey', CategoryKeyString);
          formData.append('Category', this.Category);
          formData.append('CategoryID', this.CategoryID);
        }

        if (this.GroupName) {
          formData.append('GroupName', this.GroupName);
        }
        // if (isLargeFile) {}
        this.$refs['comment-form'].classList.add('app-disable');
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            if (responsesData.data.data) {
              let comment = responsesData.data.data;
              if (responsesData.data.ContentFile.length) {
                comment.ContentFile = responsesData.data.ContentFile;
              }

              if (responsesData.data.ContentParent) {
                comment.ContentParent = responsesData.data.ContentParent;
              }

              self.imagesPreview = [];
              self.$el.querySelector('#upload-images').value = '';
              self.filesPreview = [];
              self.$el.querySelector('#upload-files').value = '';

              if (self.typeForm === 'reply') {
                comment.LineIDPost = this.LineIDPost;
                comment.LineIDComment = this.LineIDComment;
                if (this.typeStore === 'social') {
                  comment.GroupType = (self.object && self.object.GroupType) ? self.object.GroupType : 0;
                  let commentData = {
                    Content: comment,
                    GroupType: (self.object && self.object.GroupType) ? self.object.GroupType : 0,
                    GroupID: (self.object && self.object.GroupID) ? self.object.GroupID : 0,
                    ContentFile: responsesData.data.ContentFile,
                    Social: ((!self.object || (self.object && !self.object.GroupID))) ? true : false
                  };
                  socket.emit('new message', commentData);
                }

                if (this.typeStore === 'category') {
                  comment.GroupType = (comment.GroupType) ? comment.GroupType : 3;
                  let commentData = {
                    Content: comment,
                    GroupType: comment.GroupType,
                    GroupID: comment.GroupID,
                    ContentFile: responsesData.data.ContentFile,
                  };

                  // new group
                  if (responsesData.data.Group) {
                    let group = responsesData.data.Group;
                    let members = _.uniqBy(responsesData.data.Members, 'UserID');

                    if (group.Deleted !== 1 && !group.HideGroupChat) {
                      group.seen = false;
                      group.read = false;
                      group.userSeen = [];
                      group.Avatar0 = (members[0]) ? members[0].Avata : '';
                      group.Avatar1 = (members[1]) ? members[1].Avata : '';

                      socket.emit('new group', {group: group, members: members});
                    }
                  }

                  if (responsesData.data.HideGroupChat) {
                    commentData.Members = (responsesData.data.Members) ? responsesData.data.Members : [];
                    commentData.HideGroupChat = 1;
                  }
                  // if (responsesData.data.IsDataflow) {
                  //   let members = _.uniqBy(responsesData.data.Members, 'UserID');
                  //   commentData.members = members;
                  //   commentData.isDataflow = true;
                  //   commentData.Content.isDataflow = true;
                  // }

                  socket.emit('new message', commentData);

                }

                $('.last-content-' + comment.GroupID).html(comment.Content);
                self.$emit('on:stored-comment', comment);
              }

              if (self.typeForm === 'edit') {
                self.$emit('on:updated-comment', comment);
              }
            }

            self.model.comment = '';
            self.$el.querySelector('.comment-typing .comment-input').innerHTML = '';
          }else {
            this.$bvToast.toast(responsesData.msg, {
              title: 'Thông báo',
              variant: 'warning'
            });
          }
          self.isSendingMessage = false;
        }, (error) => {
          console.log(error);
          self.isSendingMessage = false;
          Swal.fire({
            title: 'Thông báo',
            text: 'Không kết nối được với máy chủ',
            confirmButtonText: 'Đóng'
          });
        });

      },

      onKeyupComment(e){
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

        // Check for a backspace
        if (e.which === 8) {
          let s = window.getSelection();
          let r = s.getRangeAt(0);
          let el = r.startContainer.parentElement
          // Check if the current element is the .label
          if (el.classList.contains('label')) {
            // Check if we are exactly at the end of the .label element
            if (r.startOffset == r.endOffset && r.endOffset == el.textContent.length) {
              // prevent the default delete behavior
              e.preventDefault();
              if (el.classList.contains('highlight')) {
                // remove the element
                el.remove();
              } else {
                el.classList.add('highlight');
              }
              return;
            }
          }
        }
        e.target.querySelectorAll('span.label.highlight').forEach(function(el) { el.classList.remove('highlight');})

        // Check for a space
        if (e.which === 32) {
          let s = window.getSelection();
          let r = s.getRangeAt(0);
          let el = r.startContainer.parentElement;
          if (el.classList.contains('label')) {
            $(this.$el.querySelector('.comment-input')).append('&nbsp;');
          }
        }

        // tag user
        let $messageInput = $(this.$el.querySelector('.comment-typing .comment-input'));
        let caretPosition = position(this.$el.querySelector('.comment-typing .comment-input'));
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
      onClickVideoConference(){
        window.open('https://meet.google.com/new', '_blank', 'toolbar=0,location=0,menubar=0');
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
                let $comment = self.$el.querySelector('.comment-typing .comment-input');
                $comment.innerHTML += this.getText();
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
      urlify(text) {
        let urlRegex = /(https?:\/\/[^\s]+)/g;
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

      onChatSuggested(suggested) {
        if (this.range) {
          this.restoreSelection();
        }
        let content = $(this.$el.querySelector('.comment-typing .comment-input')).html();
        if (!this.searchSuggestion) {
          this.searchSuggestion = '@';
        }
        content = this.replacerTextAtCaret(this.searchSuggestion, ' ');
        let tag = '<span class="message-tag message-tag-username">' + suggested.UserName + '</span> ';
        this.insertHtmlAtCaret(tag);

        this.stage.showSuggestion = false;
        this.searchSuggestion = '';
      },
      onCloseSuggested() {
        this.stage.showSuggestion = false;
        this.searchSuggestion = '';
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
      async setSearchSuggestion(e) {
        let $messageInput = $(this.$el.querySelector('.comment-typing .comment-input'));
        if (this.searchSuggestion.includes('@') || e.key === '@' || (e.ctrlKey && e.keyCode === 32)) {
          let posCaret = this.getCaretPixelPos($messageInput[0], $messageInput[0].offsetLeft, $messageInput[0].offsetTop);
          this.$nextTick(() => {
            $(this.$el.querySelector('.comment-form .comment-suggestion')).css({
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

        if (!this.membersGroups.length) {
          this.membersGroups = await this.getAllEmployee();
          this.getSuggestionArray();
        } else {
          this.getSuggestionArray();
        }
      },
      async getAllEmployee() {
        const requestData = {
          method: 'get',
          url: "extensions/api/chat/get-all-employee"
        };
        try {
          const response = await ApiService.customRequest(requestData);
          if (response.data.status) {
            return response.data.data;
          }
        }catch (e) {
          console.log('error load report template');
          console.log(e);
        }

      },
      getSuggestionArray(){
        let self = this;
        let searchSuggestion = this.searchSuggestion.replace('@', '');
        self.suggestArray = [];
        let members = this.membersGroups;
        _.forEach(members, function (member, key) {
          let tmpMember = {};
          member.UserName = member.FullName;
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
      onKeydownSearchSuggest(e){
        if (this.stage.showSuggestion) {
          let code = e.which;
          if (code === 40) {
            e.preventDefault();
            if (!$('.comment-form .suggest-item.active').length) {
              $('.comment-form .suggest-item').first().addClass('active');
            } else {
              let $currentActive = $('.comment-form .suggest-item').filter('.active');
              $currentActive.removeClass('active');
              $currentActive.next().addClass('active');
            }

          } else if (code === 38) {
            e.preventDefault();
            // up here
            if (!$('.comment-form .suggest-item.active').length) {
              $('.comment-form .suggest-item').last().addClass('active');
            } else {
              let $currentActive = $('.comment-form .suggest-item').filter('.active');
              $currentActive.removeClass('active');
              $currentActive.prev().addClass('active');
            }
          }else if (code === 13) {
            if (this.suggestArray.length) {
              $('.comment-form .suggest-item.active').trigger('click');
              e.preventDefault();
              e.stopPropagation();
            }
          }

          let $suggestActive = $('.component-chat-suggestion li.active');
          if ($suggestActive.length) {
            $('.component-chat-suggestion .scroll-area').scrollTop(($suggestActive.index() * ($suggestActive.height() / 2)));
          }
        }
      }

    },
    watch: {}

  }
</script>

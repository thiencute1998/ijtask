<template>
  <div class="component-chat-suggestion">
    <vue-perfect-scrollbar class="scroll-area" style="max-height: 200px" :settings="$store.state.psSettings">
      <ul class="chat-group-list suggest-items p-0 m-0">
        <li class="media suggest-item align-items-center px-3 py-1" @click="onSuggested($event, suggest)" style="cursor: pointer" v-if="auth.UserID !== suggest.UserID" v-for="(suggest, key) in suggestArray">
          <div class="img-block d-flex mr-3 align-self-center">
            <img :src="$store.state.appRootApi + suggest.Avatar" class="img-avatar"/>
          </div>
          <div class="media-body text-left">
            <h6 class="chat-user-name mt-0 mb-0">{{suggest.UserName}}</h6>
            <p class="chat-user-last-content mb-0"></p>
          </div>
        </li>
      </ul>
    </vue-perfect-scrollbar>
    <div class="close-suggestion">
      <span @click="onCloseSuggested"><i class="fa fa-times"></i></span>
    </div>
  </div>
</template>
<style>
  .component-chat-suggestion {
    background: #fff;
    position: relative;
  }
  .component-chat-suggestion .close-suggestion {
    position: absolute;
    right: 10px;
    top: 5px;
  }
  .component-chat-suggestion li:hover,
  .component-chat-suggestion li.active{
    background-color: #dddfe2;
  }
</style>
<script>
export default {
  name: 'chat-suggestion',
  props: {
    value: [Array, Object],
    suggestArray: [Array, Object]
  },
  data () {
    return {
      auth: JSON.parse(localStorage.getItem('Employee'))
    }
  },
  methods: {
    onSuggested(e, suggested) {
      this.$emit('on:suggested', suggested);
    },
    onCloseSuggested() {
      this.$emit('on:close');
    }
  }
}
</script>

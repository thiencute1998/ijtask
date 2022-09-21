<template>
    <div class="ij-loading-page" v-show="$store.state.isLoading">
        <div class="sk-cube-grid">
            <div class="sk-cube sk-cube1"></div>
            <div class="sk-cube sk-cube2"></div>
            <div class="sk-cube sk-cube3"></div>
            <div class="sk-cube sk-cube4"></div>
            <div class="sk-cube sk-cube5"></div>
            <div class="sk-cube sk-cube6"></div>
            <div class="sk-cube sk-cube7"></div>
            <div class="sk-cube sk-cube8"></div>
            <div class="sk-cube sk-cube9"></div>
        </div>
    </div>

</template>

<script>
    export default {
        name: 'spinners',
    }
</script>

<style src="spinkit/scss/spinkit.scss" lang="scss" />

<style scoped>
    .sk-fading-circle .sk-circle {
        margin: 0 auto;
    }
    .sk-three-bounce {
        height: 40px;
    }
    .ij-loading-page {
        width: 100%;
        height: 100%;
        position: fixed;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999999;
        background: rgba(0,0,0,.2);
    }


</style>

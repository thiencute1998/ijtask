<template>
    <div class="animated fadeIn">
        <partials-form :id-params-props="idParams" :req-params-props="reqParams" :tab-active="tabActive"></partials-form>
    </div>
</template>

<script>
    import PartialsForm from './partials/Form';
    export default {
        name: 'sysadmin-workflow-edit',
        components: {
            PartialsForm,
        },

        data() {
            return {
                idParams: Number(this.$route.params.id),
                reqParams: this.$route.params.req,
                tabActive: (this.$route.params.tabActive) ? this.$route.params.tabActive : 1
            }
        },
        beforeCreate(){
            if (!this.$route.params.id) {
                this.$router.push({name: 'sysadmin-workflow'});
            }
        },
        watch: {
        }
    }
</script>

<template>
    <div class="animated fadeIn">
        <partials-form :id-params-props="idParams" :req-params-props="reqParams"></partials-form>
    </div>
</template>

<script>
    import PartialsForm from './partials/Form';
    export default {
        name: 'edit-coa-eas',
        components: {
            PartialsForm,
        },

        data() {
            return {
                idParams: Number(this.$route.params.id),
                reqParams: this.$route.params.req
            }
        },
        beforeCreate(){
            if (!this.$route.params.id) {
                this.$router.push({name: 'listing-coa-eas'});
            }
        },
        watch: {
        }
    }
</script>

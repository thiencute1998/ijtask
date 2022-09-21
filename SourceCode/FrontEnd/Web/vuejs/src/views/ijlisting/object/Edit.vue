<template>
    <div class="animated fadeIn">
        <partials-form :id-params-props="idParams" :req-params-props="reqParams"></partials-form>
    </div>
</template>

<script>
    import PartialsForm from './partials/Form';
    export default {
        name: 'object-edit',
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
                this.$router.push({name: 'listing-object'});
            }
        },
        watch: {
        }
    }
</script>

<template>
    <div class="animated fadeIn">
        <partials-form :item-copy="itemCopy"></partials-form>
    </div>
</template>

<script>
    import PartialsForm from './partials/Form';
    export default {
        name: 'doc-doc-create',
        components: {
            PartialsForm
        },

        data() {
            return {
                itemCopy: (this.$route.params.itemCopy) ? this.$route.params.itemCopy : {}
            }
        },
        watch: {

        }
    }
</script>

<template>
    <div class="animated fadeIn">
        <partials-form :cloneTransID="cloneTransID" :item-copy="itemCopy"></partials-form>
    </div>
</template>

<script>
    import PartialsForm from './partials/Form';
    export default {
        name: 'CreateItemListing',
        components: {
            PartialsForm
        },

        data() {
            return {
                itemCopy: (this.$route.params.itemCopy) ? this.$route.params.itemCopy : {},
                cloneTransID: (this.$route.params.cloneTransID) ? this.$route.params.cloneTransID : null,
            }
        },
        watch: {

        }
    }
</script>

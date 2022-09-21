<template>
    <div class="ijcore ijcore-selected ijcore-selected-dropdown">
        <b-dropdown id="dropdownSelected" text="selected">
            <b-dropdown-header>
                <input type="text" size="sm" v-model="search" @keyup="onSearchItems" class="form-control" style="min-width: 120px"/>
            </b-dropdown-header>
            <div class="ijcore-perfect-scrollbar">
                <b-dropdown-item
                    v-for="item in itemsArray"
                    @click="handleSelectedDate(item)"
                    :key="item.id">
                    {{item.name}}
                </b-dropdown-item>
            </div>
        </b-dropdown>
    </div>
</template>

<style lang="css">
    .ijcore-perfect-scrollbar {
        position: relative;
        width: auto;
        max-height: 200px;
    }
    .ps__rail-y {
        z-index: 999;
    }

    .ijcore-selected-dropdown .dropdown-menu {
        min-width: auto;
    }

</style>

<script>
    import PerfectScrollbar from 'perfect-scrollbar';

    export default {
        name: 'SelectedDropdown',
        data () {
            return {
                search: '',
                itemsArray: []
            }
        },
        created() {

        },
        mounted() {
            this.init();
        },
        components: {
        },
        props:{
            value: {
                type: Object,
                default () {
                    return {}
                }
            },
            items: {
                type: Array,
                default () {
                    return []
                }
            }
        },
        methods: {
            init(){
                this.itemsArray = this.items;
                const container = document.querySelector('.ijcore-selected-dropdown .ijcore-perfect-scrollbar');
                const ps = new PerfectScrollbar(container);
                ps.update();
            },

            onSearchItems() {
                this.itemsArray = _.filter(this.items, _.flow(
                    _.identity,
                    _.values,
                    _.join,
                    _.toLower,
                    _.partialRight(_.includes, this.search)
                ));
            },
            handleSelectedDate(item) {

            },
        }
    }
</script>
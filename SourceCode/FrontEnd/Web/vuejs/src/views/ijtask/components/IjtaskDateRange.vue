<template>
    <div class="ijcore ijcore-selected ijcore-selected-dropdown d-lg-flex">
        <b-dropdown id="dropdownDateRange" :text="buttonText">
            <b-dropdown-header>
                <input type="text" size="sm" v-model="search" @keyup="onSearchItems" class="form-control" style="min-width: 120px"/>
            </b-dropdown-header>
            <div class="ijcore-perfect-scrollbar">
                <b-dropdown-item
                    v-for="item in items"
                    @click="handleSelectedDate(item)"
                    :key="item.id">
                    {{item.name}}
                </b-dropdown-item>
            </div>
        </b-dropdown>

        <masked-input
                type="text"
                name="from_date"
                v-model="itemSelected.fromDate"
                class="form-control mx-2"
                :mask="[/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/]"
                :guide="true"
                placeholderChar="_"
                :showMask="true"
                :keepCharPositions="true"
                @blur="handleCheckDateFormat"
                :pipe="autoCorrectedDatePipe()">
        </masked-input>

        <masked-input
                type="text"
                name="to_date"
                v-model="itemSelected.toDate"
                class="form-control mx-2"
                :mask="[/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/]"
                :guide="true"
                placeholderChar="_"
                :showMask="true"
                :keepCharPositions="true"
                @blur="handleCheckDateFormat"
                :pipe="autoCorrectedDatePipe()">
        </masked-input>
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
    import moment from 'moment';

    import MaskedInput from 'vue-text-mask';
    import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';

    moment.locale('vi');

    const itemsArray = [
        {value: -1, name: 'Tùy chọn', slug: 'tuy-chon', label: 'Tuy chon'},
        {value: 0, name: 'Hôm nay', slug: 'hom-nay', label: 'Hom nay'},
        {value: 1, name: 'Hôm qua', slug: 'hom-qua', label: 'Hom qua'},
        {value: 2, name: 'Ngày mai', slug: 'ngay-mai', label: 'Ngay mai'},
        {value: 3, name: 'Tuần trước', slug: 'tuan-truoc', label: 'Tuan truoc'},
        {value: 4, name: 'Tuần này', slug: 'tuan-nay', label: 'Tuan nay'},
        {value: 5, name: 'Tuần sau', slug: 'tuan-sau', label: 'Tuan sau'},
        {value: 6, name: 'Tháng 1', slug: 'thang-1', label: 'Thang 1'},
        {value: 7, name: 'Tháng 2', slug: 'thang-2', label: 'Thang 2'},
        {value: 8, name: 'Tháng 3', slug: 'thang-3', label: 'Thang 3'},
        {value: 9, name: 'Tháng 4', slug: 'thang-4', label: 'Thang 4'},
        {value: 10, name: 'Tháng 5', slug: 'thang-5', label: 'Thang 5'},
        {value: 11, name: 'Tháng 6', slug: 'thang-6', label: 'Thang 6'},
        {value: 12, name: 'Tháng 7', slug: 'thang-7', label: 'Thang 7'},
        {value: 13, name: 'Tháng 8', slug: 'thang-8', label: 'Thang 8'},
        {value: 14, name: 'Tháng 9', slug: 'thang-9', label: 'Thang 9'},
        {value: 15, name: 'Tháng 10', slug: 'thang-10', label: 'Thang 10'},
        {value: 16, name: 'Tháng 11', slug: 'thang-11', label: 'Thang 11'},
        {value: 17, name: 'Tháng 12',slug: 'thang-12', label: 'Thang 12'},
        {value: 18, name: 'Quý 1', slug: 'quy-1', label: 'Quy 1'},
        {value: 19, name: 'Quý 2', slug: 'quy-2', label: 'Quy 2'},
        {value: 20, name: 'Quý 3', slug: 'quy-3', label: 'Quy 3'},
        {value: 21, name: 'Quý 4', slug: 'quy-4', label: 'Quy 4'},
        {value: 22, name: 'Năm trước', slug: 'nam-truoc', label: 'Nam truoc'},
        {value: 23, name: 'Năm nay', slug: 'nam-nay', label: 'Nam nay'},
        {value: 24, name: 'Năm sau', slug: 'nam-sau', label: 'Nam sau'},
    ];
    export default {
        name: 'SelectedDropdown',
        data () {
            return {
                search: '',
                items: [],
                buttonText: 'Chọn kỳ',
                itemSelected: {
                    fromDate: '',
                    toDate: ''
                }
            }
        },
        created() {

        },
        mounted() {
            this.init();
        },
        components: {
            MaskedInput,
        },
        methods: {
            init(){
                this.items = itemsArray;
                const container = document.querySelector('.ijcore-selected-dropdown .ijcore-perfect-scrollbar');
                const ps = new PerfectScrollbar(container);
                ps.update();
            },

            onSearchItems() {
                this.items = _.filter(itemsArray, _.flow(
                    _.identity,
                    _.values,
                    _.join,
                    _.toLower,
                    _.partialRight(_.includes, this.search)
                ));
            },
            handleSelectedDate(item) {
                this.buttonText = item.name;
                let currentDate = moment().format('L'), fromDate = null, toDate = null, year = moment().get("year");

                switch (item.value) {
                    case 0:
                        fromDate = currentDate;
                        toDate = currentDate;
                        break;
                    case 1:
                        fromDate = moment().subtract(1, "days").format('L');
                        toDate = currentDate;
                        break;
                    case 2:
                        fromDate = currentDate;
                        toDate = moment().add(1, "days").format('L');
                        break;
                    case 3:
                        fromDate = moment().subtract(1, "weeks").startOf("isoWeek").format('L');
                        toDate = moment().subtract(1, "weeks").endOf("isoWeek").format('L');
                        break;
                    case 4:
                        fromDate = moment().startOf("isoWeek").format('L');
                        toDate = currentDate;
                        break;
                    case 5:
                        fromDate = moment().add(1, "weeks").startOf("isoWeeks").format('L');
                        toDate = moment().add(1, "weeks").endOf("isoWeeks").format('L');
                        break;
                    case 6:
                        fromDate = moment([year, 0]).startOf("months").format('L');
                        toDate = moment([year, 0]).endOf("months").format('L');
                        break;
                    case 7:
                        fromDate = moment([year, 1]).startOf("months").format('L');
                        toDate = moment([year, 1]).endOf("months").format('L');
                        break;
                    case 8:
                        fromDate = moment([year, 2]).startOf("months").format('L');
                        toDate = moment([year, 2]).endOf("months").format('L');
                        break;
                    case 9:
                        fromDate = moment([year, 3]).startOf("months").format('L');
                        toDate = moment([year, 3]).endOf("months").format('L');
                        break;
                    case 10:
                        fromDate = moment([year, 4]).startOf("months").format('L');
                        toDate = moment([year, 4]).endOf("months").format('L');
                        break;
                    case 11:
                        fromDate = moment([year, 5]).startOf("months").format('L');
                        toDate = moment([year, 5]).endOf("months").format('L');
                        break;
                    case 12:
                        fromDate = moment([year, 6]).startOf("months").format('L');
                        toDate = moment([year, 6]).endOf("months").format('L');
                        break;
                    case 13:
                        fromDate = moment([year, 7]).startOf("months").format('L');
                        toDate = moment([year, 7]).endOf("months").format('L');
                        break;
                    case 14:
                        fromDate = moment([year, 8]).startOf("months").format('L');
                        toDate = moment([year, 8]).endOf("months").format('L');
                        break;
                    case 15:
                        fromDate = moment([year, 9]).startOf("months").format('L');
                        toDate = moment([year, 9]).endOf("months").format('L');
                        break;
                    case 16:
                        fromDate = moment([year, 10]).startOf("months").format('L');
                        toDate = moment([year, 10]).endOf("months").format('L');
                        break;
                    case 17:
                        fromDate = moment([year, 11]).startOf("months").format('L');
                        toDate = moment([year, 11]).endOf("months").format('L');
                        break;
                    case 18:
                        fromDate = moment([year, 0]).startOf("months").format('L');
                        toDate = moment([year, 2]).endOf("months").format('L');
                        break;
                    case 19:
                        fromDate = moment([year, 3]).startOf("months").format('L');
                        toDate = moment([year, 5]).endOf("months").format('L');
                        break;
                    case 20:
                        fromDate = moment([year, 6]).startOf("months").format('L');
                        toDate = moment([year, 8]).endOf("months").format('L');
                        break;
                    case 21:
                        fromDate = moment([year, 9]).startOf("months").format('L');
                        toDate = moment([year, 11]).endOf("months").format('L');

                        break;
                    case 22:
                        fromDate = moment([year, 0]).subtract(1, "years").startOf("months").format('L');
                        toDate = moment([year, 11]).subtract(1, "years").endOf("months").format('L');
                        break;

                    case 23:
                        fromDate = moment([year, 0]).startOf("months").format('L');
                        toDate = moment([year, 11]).endOf("months").format('L');
                        break;
                    case 24:
                        fromDate = moment([year, 0]).add(1,"years").startOf("months").format('L');
                        toDate = moment([year, 11]).add(1, "years").endOf("months").format('L');
                        break;
                }
                item.fromDate = fromDate;
                item.toDate = toDate;

                this.itemSelected.fromDate = item.fromDate;
                this.itemSelected.toDate = item.toDate;
                this.$emit('input', this.itemSelected);
            },
            handleCheckDateFormat() {

                if (!moment(this.itemSelected.fromDate, 'DD-MM-YYYY').isValid()) {
                    this.itemSelected.fromDate = moment().format('L');
                    this.$emit('input', this.itemSelected);
                }

                if (!moment(this.itemSelected.toDate, 'DD-MM-YYYY').isValid()) {
                    this.itemSelected.toDate = moment().format('L');
                    this.$emit('input', this.itemSelected);
                }
            },
            autoCorrectedDatePipe: () => {
                return createAutoCorrectedDatePipe('dd/mm/yyyy')
            },
        },
        props:{
            value: {
                type: Object,
                default () {
                    return {}
                }
            },
        },
    }
</script>
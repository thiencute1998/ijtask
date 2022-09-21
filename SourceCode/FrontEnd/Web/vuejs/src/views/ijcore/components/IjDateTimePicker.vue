<template>
    <date-picker valueType="format"
            :lang="lang"
            :format="momentFormat"
            :input-class="classDatePicker"
             v-bind:value="parentValue"
             v-on:change="updateValue(parentValue)"
             v-model="parentValue"
    >
        <template v-slot:input="{ emit }">
            <masked-input
                    type="text"
                    class="form-control"
                    :mask="[/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/]"
                    :guide="true"
                    placeholderChar="_"
                    :showMask="true"
                    :keepCharPositions="true"
                    :pipe="autoCorrectedDatePipe()"
                    v-on:change="updateValue(parentValue)"
                    v-model="parentValue"
            >
            </masked-input>

        </template>
    </date-picker>
</template>

<script>
    import MaskedInput from 'vue-text-mask';
    import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
    //datepicker
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';
    import 'vue2-datepicker/locale/vi';
    let today = '';
    export default {
        name: 'IjDateTimePicker',
        components: {
            MaskedInput,
            DatePicker,
        },
        data () {
            return {
                parentValue: today,
                classDatePicker: 'mx-input',
                momentFormat: 'DD/MM/YYYY HH:mm:ss',
                lang: {
                    formatLocale: {
                        firstDayOfWeek: 1,
                    },
                    monthBeforeYear: false,
                },
            }
        },
        created() {
            this.parentValue = this.today;
        },
        mounted() {
        },
        methods: {
            autoCorrectedDatePipe: () => {
                return createAutoCorrectedDateTimePipe('dd/mm/yyyy hh:ii:ss')
            },
            updateValue: function (value) {
                this.$emit('input', value);
            }
        },
        props: ['today', 'value'],
    }
</script>
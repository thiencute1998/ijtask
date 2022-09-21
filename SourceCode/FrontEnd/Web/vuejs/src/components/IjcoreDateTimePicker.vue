<!--============== How to use =======================================

    <ijcore-date-time-picker v-model="dateTime" date-time="23/12/1994 05:06:08" :allow-empty-clear="false"></ijcore-date-time-picker>

==================================================================-->

<template>
    <date-picker valueType="format"
            :lang="lang"
            :format="momentFormat"
            :input-class="classDatePicker"
             type="datetime"
             @clear="onClearDatetime()"
             v-model="parentValue">
        <template v-slot:input="{ emit }">
            <masked-input
                    type="text"
                    class="form-control"
                    :mask="[/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/, ' ', /\d/, /\d/, ':', /\d/, /\d/, ':', /\d/, /\d/]"
                    :guide="true"
                    placeholderChar="_"
                    :showMask="true"
                    :keepCharPositions="true"
                    :pipe="autoCorrectedDatePipe()"
                    v-model="parentValue">
            </masked-input>

        </template>
    </date-picker>
</template>

<script>
    import MaskedInput from 'vue-text-mask';
    import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';
    import moment from 'moment';
    //datepicker
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';
    import 'vue2-datepicker/locale/vi';

    export default {
        name: 'IjcoreDateTimePicker',
        components: {
            MaskedInput,
            DatePicker,
        },
        data() {
            let today = moment().format('DD/MM/YYYY HH:mm:ss');
            if (this.value) today = this.value;
            this.$emit('input', today);
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
            };
        },
        mounted() {},
        methods: {
            onClearDatetime(){
                if (!this.allowEmptyClear) this.parentValue = moment().format('DD/MM/YYYY HH:mm:ss');
            },
            autoCorrectedDatePipe: () => {
                return createAutoCorrectedDatePipe('dd/mm/yyyy HH:MM:SS')
            }
        },
        watch: {
            parentValue(){
                this.$emit('input', this.parentValue);
            }
        },
        props: ['value', 'dateTime', 'allowEmptyClear'],
    }
</script>
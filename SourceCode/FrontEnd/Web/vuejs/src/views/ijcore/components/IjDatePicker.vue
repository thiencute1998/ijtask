<template>
    <date-picker valueType="format"
            :lang="lang"
            :format="momentFormat"
            :input-class="classDatePicker"
             v-bind:value="parentValue"
             v-on:change="updateValue(parentValue)"
             v-model="parentValue"
             v-on:click="updateStatusHour"
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
                    v-on:click="updateStatusHour"
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
        name: 'IjDatePicker',
        components: {
            MaskedInput,
            DatePicker,
        },
        data () {
            return {
                parentValue: this.value,
                classDatePicker: 'mx-input demo',
                momentFormat: 'DD/MM/YYYY',
                lang: {
                    formatLocale: {
                        firstDayOfWeek: 1,
                    },
                    monthBeforeYear: false,
                },
            }
        },
        created() {
        },
        mounted() {
        },
        methods: {
            updateStatusHour(){
            },
            autoCorrectedDatePipe: () => {
                return createAutoCorrectedDatePipe('dd/mm/yyyy')
            },
            updateValue: function (value) {
                this.$emit('input', value);
            }
        },
        watch:{
            value(){
                this.parentValue = this.value;
            }
        },
        props: ['today', 'value'],
    }
</script>
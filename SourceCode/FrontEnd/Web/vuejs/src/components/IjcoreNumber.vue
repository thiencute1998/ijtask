<template>
    <div>
        <input type="text" v-model="price" value="price" class="form-control" v-on:keydown="updateKeydown($event)" v-on:keyup="updateKeyup($event)" v-if="readonly" readonly v-on:change="updateChange($event)" style="text-align: right;" :placeholder="placeholder"/>
        <input type="text" v-model="price" class="form-control" v-on:keydown="updateKeydown($event)" v-on:keyup="updateKeyup($event)" v-if="!readonly" v-on:change="updateChange($event)" style="text-align: right;" :placeholder="placeholder"/>
        <input type="hidden" v-model="value" class="form-control"/>
    </div>
</template>

<script>
    export default {
        name: 'IjcoreNumber',
        components: {
        },
        data () {
            return {
                oldValue: '',
                newValue: '',
                price: __.convertNumberToText(this.value),
                priceTemp: __.convertNumberToText(this.value),
                money: {
                    decimal: this.decimal,
                    thousands: this.thousands,
                    prefix: this.prefix,
                    suffix: this.suffix,
                    precision: this.precision,
                    masked: this.masked
                },
                oldPositionTemp: 0,
            }
        },
        props:{
            placeholder:{
              default:''
            },
            value:{
                default: ""
            },

            decimal:{
              type: String,
              default: ','
            },
            thousands:{
                type: String,
                default: '.'
            },
            prefix:{
                type: String,
                default: ''
            },
            suffix:{
                type: String,
                default: ''
            },
            precision:{
                type: Number,
                default: 0
            },
            masked:{
                type: Boolean,
                default: false
            },
            keyarray:{
                type: Number,
                default: -1
            },
            callf:{
                type: String,
                default: ''
            },
            name:{
                type: String,
                default: ''
            },
            readonly: {
                type: Boolean,
                default: false,
            },
            length: {
                type: Number,
                default: 20
            },
            number_decimal:{
                type: Number,
                default: 4
            }
        },
        methods: {
            onFocus(event){

            },
            updateKeyup(event){
                this.$emit('keyup', event);
                var codesAllow = [8, 37, 38, 39, 40, 44, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 188, 9, 17, 67, 86, 46, 45];
                if(!codesAllow.includes(event.keyCode)){
                    event.preventDefault();
                    return false;
                }
                let oldPosition = event.target.selectionStart;
                let numberRight = this.price.length - oldPosition;
                if(event.keyCode == 9){
                    numberRight = 0;
                }
                var replace = '[^\\d'+this.decimal+''+this.thousands+']';
                var re = new RegExp(replace,"g");
                let data = this.price.replace(re, '');
                let arrTemp = data.split(this.decimal);
                let numberDecimal = arrTemp[0];

                for(var i = 0; i < arrTemp.length; i++){
                    var val = arrTemp[i];
                    var replace = '[^\\d]';
                    var re = new RegExp(replace,"g");
                    val = val.replace(re, '');
                    var length = val.length;
                    var textNumber = '';
                    var number = 3;
                    if(i == 0){
                        for (var j = 0; j < length; j++) {
                            if(j == number){
                                textNumber = val[length - j - 1] + this.thousands +textNumber;
                                number = number + 3;
                            }else{
                                textNumber = val[length - j - 1] + '' +textNumber;
                            }
                        }
                    }else{
                        textNumber = val;
                    }

                    arrTemp[i] = textNumber;
                }
                event.target.value = arrTemp.join(',');
                event.target.selectionStart = event.target.value.length - numberRight;
                this.price = event.target.value;
                this.$emit('input', __.convertTextToNumber(this.price));
                this.oldPositionTemp = event.target.selectionStart;
                if(event.keyCode != 8 && event.keyCode != 37 && event.keyCode != 38 && event.keyCode != 39 && event.keyCode != 40){
                    event.target.focus();
                    event.target.setSelectionRange(event.target.selectionStart, event.target.selectionStart);
                    event.preventDefault();
                    return false;
                }
            },
            updateKeydown(event){
                var codesAllow = [8, 37, 38, 39, 40, 44, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 188, 9, 17, 67, 86, 46, 45];
                if(!codesAllow.includes(event.keyCode)){
                    event.preventDefault();
                    return false;
                }
            },
            updateChange(event){
                this.$emit('changed', true)
            }
        },
        watch: {
            price: function (newValue, oldValue) {
                this.oldValue = oldValue;
                this.newValue = newValue;
            },
            value(){
                this.price = __.convertNumberToText(this.value);
            }
        }
    }
</script>

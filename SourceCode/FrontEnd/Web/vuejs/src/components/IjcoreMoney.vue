<template>
    <div class="ijcore ijcore-money form-group">
        <money class="ijcore-money-input form-control" v-model="price" v-bind="money"></money>
    </div>
</template>

<script>
    import {Money} from 'v-money';
    export default {
        name: 'ijcore-money',
        components: {
            'money': Money
        },
        data () {
            return {
                price: this.value,
                money: {
                    decimal: this.decimal,
                    thousands: this.thousands,
                    prefix: this.prefix,
                    suffix: this.suffix,
                    precision: this.precision,
                    masked: this.masked
                }
            }
        },
        props:{

            value:{
                type: Number,
                default: 0
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
            }
        },
        watch: {
            price(){
                this.$emit('input', this.price);
            },
            value(){
                this.price = this.value;
            }
        }
    }
</script>
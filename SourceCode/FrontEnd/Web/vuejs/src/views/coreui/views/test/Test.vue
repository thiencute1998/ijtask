<template>
    <div class="animated fadeIn">
        <b-row>
            <b-col class="col-sm-12">
                <b-card>
                    <div slot="header">
                        <strong>Masked Input</strong> <small class="ml-1">vue-text-mask</small>
                        <a href="https://coreui.io/pro/vue/" rel="noreferrer noopener" target="_blank" class="badge badge-danger ml-1">CoreUI Pro</a>
                        <div class="card-header-actions">
                            <a href="https://github.com/text-mask/text-mask/tree/master/vue#readme" rel="noreferrer noopener" target="_blank" class="card-header-action">
                                <small class="text-muted">docs</small>
                            </a>
                        </div>
                    </div>
                    <b-form-group label="Date input" description="ex. 99/99/9999">
                        <b-input-group>
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class='fa fa-calendar'></i>
                                </span>
                            </div>
                            <masked-input
                                type="text"
                                name="date"
                                class="form-control"
                                v-model="date"
                                :mask="['(', /[1-9]/, /\d/, /\d/, ')', ' ', /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/]"
                                :guide="true"
                                placeholderChar="_"
                                :showMask="true"
                                :keepCharPositions="true"
                                :pipe="autoCorrectedDatePipe()">
                            </masked-input>
                        </b-input-group>
                    </b-form-group>

                    <ijcore-modal v-model="myModal.modelContract" table="contract" ref="myModal" id-modal="myModal" title-modal="Contract Modal" type-element="input"></ijcore-modal>

                </b-card>
            </b-col>

            <b-col class="col-sm-12">

            </b-col>
        </b-row>


    </div>
</template>

<script>
    import MaskedInput from 'vue-text-mask';
    import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';

    export default {
        name: 'advanced-forms',
        components: {
            MaskedInput
        },
        data () {
            return {
                date: '',
                price: 12,
                smartBooks: 'contract',
                myModal: {
                    title: '',
                    table: '',
                    modelContract: {}
                },
            }
        },
        computed: {
        },
        methods: {
            autoCorrectedDatePipe: () => { return createAutoCorrectedDatePipe('mm/dd/yyyy') },
            showModal(type) {
                if (type == 'user') {
                    this.myModal.table = 'User';
                    this.myModal.title = 'Modal user';
                }

                if (type == 'contract') {
                    this.myModal.table = 'Contract';
                    this.myModal.title = 'Modal contract';
                }

                this.$bvModal.show('myModal');
            }
        },

        watch: {
            'myModal.modelContract': function () {

            }
        }
    }
</script>

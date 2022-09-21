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
                    <hr/>
                    <b-button @click="onShowModal('myModalSearch')">Modal Search</b-button>
                    <ijcore-modal-search
                        v-model="modelSearch"
                        :item-per-page="7"
                        :select-fields-api="[
                            {field: 'capitalid', label: 'Mã', key: 'capitalid', thStyle: 'width: 60px', sortable: true},
                            {field: 'capitalname', label: 'Tên nguồn vốn', key: 'capitalname', sortable: true}
                        ]"
                        :search-fields-api="[
                            {field: 'capitalid', placeholder: 'Nhập mã', name: 'capitalid', class: ''},
                        ]"
                        ref-modal="myModalSearch"
                        title-modal="Bao cao"
                        url-api="http://localhost/pabmis/api/index.php"
                        size-modal="lg" ref="myModalSearch">
                    </ijcore-modal-search>
                    <hr/>

                    <ijcore-modal-search-input
                        v-model="myModal.modelContract"
                        :select-fields-api="[
                            {field: 'capitalid', fieldForSelected: 'id', showInTable: false, thClass: 'd-none', tdClass: 'd-none', label: 'Mã', key: 'capitalid', thStyle: 'width: 60px', sortable: true},
                            {field: 'capitalname', fieldForSelected: 'name', showInTable: true, label: 'Tên nguồn vốn', key: 'capitalname', sortable: true}
                        ]"
                        :search-fields-api="[{field: 'capitalid', placeholder: 'Nhập mã', name: 'capitalid', class: '', style: ''},]"
                        tableApi="contract"
                        ref="myModalSearchInput"
                        id-modal="myModalSearchInput"
                        url-api="http://localhost/pabmis/api/index.php"
                        title-modal="Contract Modal" size-modal="lg">
                    </ijcore-modal-search-input>
                    <hr/>
                    <ijcore-money decimal="," thousands="." prefix="$" suffix="" :precision="2"></ijcore-money>

                    <hr/>
                    <!--                    <ijcore-selected-dropdown></ijcore-selected-dropdown>                   -->
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
            MaskedInput,
            // PabmisCore
        },
        data () {
            return {
                date: '',
                price: 12,
                modelSearch: {},
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
            onShowModal(type) {
                // this.$bvModal.show(type);
                if (type == 'myModalSearch') {
                    this.$refs.myModalSearch.init();
                }
            }
        },

        watch: {
            'myModal.modelContract': function () {

            }
        }
    }
</script>

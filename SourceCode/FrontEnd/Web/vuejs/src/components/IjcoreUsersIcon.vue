<!--============== How to use =======================================

    <ijcore-users-icon :all-users="model.WorkflowEmployee" filter-name="WFItemID" :filter-value="field.WFItemID" :number="3"></ijcore-users-icon>

==================================================================-->
<template>
    <div class="user-icons table-padding-x">
        <div class="user-icon" :class="[(user.IsMainResponsiblePerson) ? 'user-main-response' : '']" v-if="key < numberShow" :title="getTitleUser(user)" v-for="(user, key) in usersArray">
            <img :src="$store.state.appRootApi + user.Avata"/>
        </div>
        <div class="user-plus" :id="'popover-user-plus-' + filterValue" v-if="usersArray.length > numberShow">+{{usersArray.length - numberShow}}</div>
        <b-popover :target="'popover-user-plus-' + filterValue" triggers="hover" placement="top">
            <div class="d-flex">
                <div class="user-icon" :class="[(user.IsMainResponsiblePerson) ? 'user-main-response' : '']" :title="getTitleUser(user)" v-for="(user, key) in usersPlus">
                    <img :src="$store.state.appRootApi + user.Avata"/>
                </div>
            </div>
        </b-popover>
    </div>
</template>

<script>


    export default {
        name: 'ijcore-users-icon',
        data () {
            return {
                numberShow: (this.number) ? this.number : 3
            }
        },
        created() {
        },
        mounted() {
        },
        computed: {
            usersArray(){
                let self = this;
                let users = [];
                if (!_.isArray(this.filterValue)) {
                    users = _.filter(this.allUsers, [this.filterName, this.filterValue]);
                } else {
                    _.forEach(this.filterValue, function (value, key) {
                        self.filterValue[key] = Number(value);
                    });
                    users = _.filter(this.allUsers, (user) => _.includes(self.filterValue, Number(user.EmployeeID)));

                    _.forEach(users, function (user, key) {
                        if (Number(user.EmployeeID) === Number(self.mainResponsiblePerson)) {
                            users[key].IsMainResponsiblePerson = true;
                        } else {
                            users[key].IsMainResponsiblePerson = false;
                        }
                    });
                }

                return users;
            },
            usersPlus(){
                let usersPlus = this.usersArray.slice(this.numberShow, this.usersArray.length);
                return usersPlus;
            }
        },
        components: {
        },
        methods: {
            getTitleUser(user){
                let title = user.EmployeeName;
                if (user.IsMainResponsiblePerson) {
                    title += ' - Là người chịu trách nhiệm chính';
                }
                return title;
            },
        },
        props: ['allUsers', 'filterName', 'filterValue', 'number', 'mainResponsiblePerson'],
        watch: {}
    }
</script>

<style lang="css">

</style>

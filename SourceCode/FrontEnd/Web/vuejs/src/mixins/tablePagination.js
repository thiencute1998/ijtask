import ApiService from '@/services/api.service';
export default {
  data(){
    return {
      perPage: '10',
      currentPage: 1,
      itemsArray: [],
      totalRows: null
    }
  },
  methods:{},
  watch:{
    currentPage(){

    }
  }
}

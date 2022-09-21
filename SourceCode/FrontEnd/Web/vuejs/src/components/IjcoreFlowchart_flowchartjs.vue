<template>
  <div id="diagram"></div>
</template>

<script>
import flowchart from 'flowchart.js';
export default {
  name: "Flowchart",
  data: function() {
    return {};
  },
  props: {
    value: {
      type: Object,
      default (){
        return {};
      }
    },
  },
  mounted() {
    // let diagram = flowchart.parse(
    //   'op1=>operation: Phát sinh nghiệp vụ:>/#/sysadmin/users\n' +
    //   'op2=>operation: Kiểm tra xe đã có mã đối tượng trong hệ thống chưa:>/#/listing/items\n' +
    //   'cond=>condition: Chưa có/Đã có|approved\n' +
    //   'op3=>operation: Tạo mã đỗi tượng\n' +
    //   'op4=>operation: Nhập giao dịch trả trước vào hệ thống\n' +
    //   'op5=>operation: Duyệt giao dịch\n' +
    //   'op6=>operation: Thanh toán cho giao dịch\n' +
    //   'op7=>operation: Quản lý công nợ\n' +
    //   '\n' +
    //   'op1(right)->op2(bottom)->cond\n' +
    //   'cond(yes, right)->op3\n' +
    //   'cond(no)->op4\n' +
    //   'op4->op5->op6->op7\n' +
    //   'op7(right)'
    // );
    let diagram = flowchart.parse(this.value.parseStr);

    diagram.drawSVG('diagram', {
      'x': 100,
      'y': 100,
      'line-width': 1,
      'line-length': 30,
      'text-margin': 10,
      'font-size': 14,
      'font-color': '#151b1e',
      'line-color': '#00a2e8',
      'element-color': '#00a2e8',
      'fill': 'white',
      'yes-text': 'yes',
      'no-text': 'no',
      'arrow-end': 'block',
      'scale': 1,
      // even flowstate support ;-)
      'flowstate' : {
        'approved' : {'font-size' : 12, 'yes-text' : 'Chưa có', 'no-text' : 'Đã có'}
      }
    });
  },
  computed: {

  },
  methods: {

  },
  watch: {
    value() {}
  }
};
</script>

<style>
</style>

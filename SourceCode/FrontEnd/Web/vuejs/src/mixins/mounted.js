import colResizeable from '@/libraries/colResizable-1.6.min.js';
function mounted() {
    $('table').colResizable({resizeMode: 'overflow'});
}
export default mounted();
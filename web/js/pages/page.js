/**
 * Created by zhangjiali on 2018/11/14.
 */
document.addEventListener('DOMContentLoaded',function(){
    console.log('page')
    new Vue({
        el:'#page',
        methods:{
            jump:function(v){
                console.log(v);
                get_list();
            }
        }
    })
});

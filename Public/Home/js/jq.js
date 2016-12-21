/* 
* @Author: 会飞的猫
* @Date:   2016-12-06 11:32:39
* @Last Modified by:   会飞的猫
* @Last Modified time: 2016-12-10 17:54:02
*/

$(document).ready(function(){
    // 弹出
    $('.q_4_a_a').click(function(){
        $('.q-zz').fadeIn();
        $('.q-tc1').fadeIn();        
    })
    $('.tc-ch1').click(function(){
        $('.q-tc1').hide();
        $('.q-zz').hide();
    })

    

     $('.q16-at').click(function(){
        $('.q-zz').fadeIn();
        $('.q-tc-fh').fadeIn();        
    })
    $('.tc-ch1').click(function(){
        $('.q-tc-fh').hide();
        $('.q-zz').hide();
    })

     $('.q21-a').click(function(){
        $('.q-zz').fadeIn();
        $('.q-tx').fadeIn();        
    })
    $('.tc-ch1').click(function(){
        $('.q-tx').hide();
        $('.q-zz').hide();
    })

    // 收货地址
    $('.q22-a3').click(function(){
        $('.q-zz').fadeIn();
        $('.q-tsh').fadeIn();        
    })
    $('.tc-ch1').click(function(){
        $('.q-tsh').hide();
        $('.q-zz').hide();
    })


    // 售后
    $('.q21-d3-a').click(function(){
        $('.q-zz').fadeIn();
        $('.q-taddress').fadeIn();        
    })
    $('.tc-ch1').click(function(){
        $('.q-taddress').hide();
        $('.q-zz').hide();
    })

    $('.q_ra').click(function(){
        $('.q-zz').fadeIn();
        $('.q-tsh').fadeIn();        
    })
    $('.tc-ch1').click(function(){
        $('.q-tsh').hide();
        $('.q-zz').hide();
    })



    $('.q21-tx').click(function(){
        $('.q-zz').fadeIn();
        $('.q-tc-fh').fadeIn();        
    })
    $('.tc-ch1').click(function(){
        $('.q-tc-fh').hide();
        $('.q-zz').hide();
    })

    
    $('.q21-d3-a2').click(function(){
        $('.q-zz').fadeIn();
        $('.t-bty').fadeIn();        
    })
    $('.tc-ch1').click(function(){
        $('.t-bty').hide();
        $('.q-zz').hide();
    })


    $('.q_on13').click(function(){
        $('.q-zz').fadeIn();
        $('.t_dpi').fadeIn();        
    })
    $('.tc-ch1').click(function(){
        $('.t_dpi').hide();
        $('.q-zz').hide();
    })

    $('.q_3_a1_tc').click(function(){
        $('.q-zz').fadeIn();
        $('.q45').fadeIn();        
    })
    $('.tc-ch1').click(function(){
        $('.q45').hide();
        $('.q-zz').hide();
    })


    //历史数据 偶数加背景
    $('.y_zjgl1 ul li:even').addClass('even');
});
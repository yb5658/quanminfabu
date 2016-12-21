$(function(){
    //导航下拉
    $('.y_anniu').click(function(){
        $('.y_nav').stop(true,false).slideToggle();
    })

    //弹窗1
    $('.tcbg').height($(window).height());
    $('.tanchuang1').click(function(){
        $('.tcbg').fadeIn();
        $('.tcdiv1').fadeIn();
        $('.tcdiv2').fadeOut();
        // $('.tcdiv1').css('margin-top',($(window).height()-$('.tcdiv1').height())/2);

    })
    $('.tanchuang2').click(function(){
        $('.tcbg').fadeIn();
        $('.tcdiv2').fadeIn();
        $('.tcdiv1').fadeOut();
        // $('.tcdiv1').css('margin-top',($(window).height()-$('.tcdiv1').height())/2);

    })
    $('.tcbg').click(function(){
        $('.tcdiv1,.tcdiv2').hide();
        $('.tcbg').hide();
    })

    //弹窗3
    $('.tcbg2').height($(window).height()-98);
    $('.tanchuang3').click(function(){
        $('.tcbg2').fadeIn();
        $('.tcdiv3').fadeIn();
        // $('.tcdiv1').css('margin-top',($(window).height()-$('.tcdiv1').height())/2);

    })
    $('.tcbg2,.gb2').click(function(){
        $('.tcdiv3').hide();
        $('.tcbg2').hide();
    })

    //弹窗4
    $('.tcdiv4').css('margin-top',($(window).height()-$('.tcdiv4').height())/2);
    $('.tcbg3').height($(window).height());
    $('.tanchuang4').click(function(){
        $('.tcbg3').fadeIn();
        $('.tcdiv4').fadeIn();
        $('.tcdiv4').css('margin-top',($(window).height()-$('.tcdiv4').height())/2);

    })
    $('.tcbg3,.gb3').click(function(){
        $('.tcdiv4').hide();
        $('.tcbg3').hide();
    })

    //弹窗5
    $('.tcdiv5').css('margin-top',($(window).height()-$('.tcdiv5').height())/2);
    $('.tcbg3').height($(window).height());
    $('.tanchuang5').click(function(){
        $('.tcbg3').fadeIn();
        $('.tcdiv5').fadeIn();
        $('.tcdiv5').css('margin-top',($(window).height()-$('.tcdiv5').height())/2);

    })
    $('.tcbg3,.gb3').click(function(){
        $('.tcdiv5').hide();
        $('.tcbg3').hide();
    })

    //弹窗5
    $('.tcdiv6').css('margin-top',($(window).height()-$('.tcdiv6').height())/2);
    $('.tcbg3').height($(window).height());
    $('.tanchuang6').click(function(){
        $('.tcbg3').fadeIn();
        $('.tcdiv6').fadeIn();
        $('.tcdiv6').css('margin-top',($(window).height()-$('.tcdiv6').height())/2);

    })
    $('.tcbg3,.gb3').click(function(){
        $('.tcdiv6').hide();
        $('.tcbg3').hide();
    })

    //TAB切换
    $('.title').each(function(){
         $(this).children('.title1').eq(0).addClass('xz');
    })

    $('.title_nr').each(function(){
         $(this).children('.title_nr1').eq(0).show();
    })

    $('.title1').click(function(){
        $(this).addClass('xz').siblings().removeClass('xz');
        $(this).parent().siblings('.title_nr').children('.title_nr1').eq($(this).index()).show().siblings().hide();
    })

    //关闭广告条
    $('.gb4').click(function(){
        $('.ggt').slideUp();
    })

    //分享出去的链接页面 第一个元素去掉边框
    $('.y_fxcq2_nr').eq(0).addClass('first');

    //购买 微信支付选择
    $('.y_gm4 a').click(function(){
        $(this).stop(true,true).toggleClass('xz');
    })

    

})
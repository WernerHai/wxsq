var canshake=true;
$(function() {
    var m = $(window).width() < $(window).height() ? $(window).width() * 1: $(window).height() * 1;
    var o = 0;//控制不要重复提交请求
    var n = (SHAKE_INFO && $.isArray(SHAKE_INFO.slogan_list) && SHAKE_INFO.slogan_list.length > 0) ? SHAKE_INFO.slogan_list: ["再大力！", "再大力,再大力！", "再大力,再大力,再大力！", "摇，大力摇", "快点摇啊，别停！", "摇啊，摇啊，摇啊", "小心手机，别飞出去伤到花花草草", "看灰机～～～"];
    var l = $(".shake-icon");
    var t = $(".shake-icon").offset().top + $(".shake-icon").height() + 20;
    var a = $(".memo").css({
        top: t + "px"
    });
    $(window).on("resize", 
    function() {
        var u = $(".shake-icon").offset().top + $(".shake-icon").height() + 20;
        $(".memo").css({
            top: u + "px"
        })
    });
    var p = /(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent);
    a.html("点一下图标开启音乐更刺激");
    l.one("click", 
    function() {
        f();
        a.html("听从现场指挥摇动手机")
    });
    if (window.DeviceMotionEvent) {
        window.addEventListener("devicemotion", g, false)
    } else {
        a.html("您的手机不支持摇一摇，您可以通过点击图标参与。");
        l.on("click", 
        function() {
            var u = Math.random() * 10;
            if (u < 5) {
                f();
                $("body").addClass("shakeing");
                q()
            } else {
                $("body").removeClass("shakeing")
				 q();
            }
        })
    }
    function h() {
        return n[(Math.floor(Math.random() * n.length))]
    }
    var c = document.getElementById("ShakeAudio");
    //播放摇晃声
    function f() {
        if (c) {
            if (c.paused) {
                c.play()
            }
        }
    }
    var r = 500;
	var ksl = false;
    if (!p) {
        r = 800
    }
    var s = 0;
    var k,
    j,
    i,
    e,
    d,
    b;
    function g(w) {
        var v = w.accelerationIncludingGravity;
        //时间
        var y = (new Date()).getTime();
        if ((y - s) > 300) {
            var u = y - s;
            s = y;
            k = v.x;
            j = v.y;
            i = v.z;
            var x = Math.abs(k + j + i - e - d - b) / u * 10000;
            if (x > r) {
                f();
                $("body").addClass("shakeing");
                	q();
            } else {
                $("body").removeClass("shakeing")
            }
            e = k;
            d = j;
            b = i
        }
    }
    
    function q() {
    	var openid=$('#openid').val();
    	if(!canshake)return false;
        if (!(o%10)) {
        	o=1;
			_meepoajax._ajax({
				do_it:'shake_count',
				type: "POST",                        
				dataType: 'json',      
				cache: false,                 
				formPata:{'openid':openid},
				success:function(u) {
					o=0;
					//人满
					if(u.status==6){
						canshake=false;
						var currenttime=Math.round(new Date().getTime()/1000);
						var yanchi=currenttime-u.started_at;
						setTimeout(function(){canshake=true},(u.duration-yanchi)*1000); 
					}
					if(u.status==4){
						a.html("你还没有参与摇一摇呢。");
						return false;
					}
					if(u.status==5){
						a.html("活动不存在。");
						return false;
					}
					if(u.status==1){
						
					}
					if(u.status==2){
						a.html(h());
					}
					if(u.status==3){
						a.html("活动已经结束或者活动还没开始。");
					}
					
					if(u.status<0){
						a.html(u.message);
					}
					/*
                    if (u.errno == -1) {//no start
                        a.html("淡定，淡定，游戏还没开始！");
                    } else {
                        if (u.errno == 0) {//doing
                            a.html(h())
                        }
						if (u.errno == 1) {//end
                            a.html(u.message)
                        }
                    }
                    */
				}
			});
        }else{
        	o++;
        }
}
});
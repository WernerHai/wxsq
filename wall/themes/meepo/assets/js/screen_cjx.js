var $ResultSeed;
var Players;
var Winers;
var audio_Running,
audio_GetOne;
var resizePart = window.WBActivity.resize = function() {};
var start = window.WBActivity.start = function() {
	bindhotkey();
    window.WBActivity.hideLoading();
    var b = document.getElementById("Audio_Running");
    if (b.play) {
        audio_Running = b
    }
    var a = document.getElementById("Audio_Result");
    if (a.play) {
        audio_GetOne = a
    }
    $(".usercount-label").html("加载数据中...");
    $(".control").hide();
    
    getReady();
    
    $(".Panel.Top").css({
        top: 0
    });
    $(".Panel.Bottom").css({
        bottom: 0
    });
    $(".Panel.Lottery").css({
        display: "block",
        opacity: 1
    });
    $ResultSeed = $(".lottery-right .result-line");
    $(".control.button-run").on("click", 
    function() {
        start_game()
    });
    $(".control.button-stop").on("click", 
    function() {
        stop_game()
    });
    $(".control.button-nextround").on("click", 
    function() {
        window.location.reload()
    });
    $(".button-reload").on("click", 
    function() {
        window.location.reload()
    });
    $(".select-button").on("click", 
    function(g) {
        var f = $(this),
        c = $(".select-value"),
        d = c.text();
        if (f.hasClass("minus")) {
            if (d > 1) {
                d--;
                c.text(d)
            }
        } else {
            if (f.hasClass("plus")) {
                if (d < Players.length) {
                    d++
                } else {
                    c = Players.length
                }
                c.text(d)
            }
        }
        g.preventDefault();
        return false
    })
};


function changeLuck(obj){//改变奖项
	var option = obj;
	if(option==-1){
		 return;
	}
//	$("#loading").show();
//	$(".lottery-right .had_luck_user").remove();
	getReady();
}
var getReady=function(){
	var awardid=$('#tagid').val();
	if(awardid==-1){
		return false;
	}
	$(".lottery-right .result-line").remove();
    $.getJSON(PATH_ACTIVITY + Path_url('ajax_act_lottory.php?action=ready'), {
    	'from': 'cjx',
        'awardid':awardid
    },
    function(json) {
    	console.log(json);
        if(json.ret==0 && json.data.length>0){
            Players = json.data;
            var c = Players.length;
            $(".usercount-label").html(json.count + "人");
            $('#leftperson').val(json.count);
            $(".control.button-run").fadeIn();
            if(json.luckuser!=null){
            	$.each(json.luckuser,function(i,val){
	            	var list_num = i +1;
					var luck_user = '<div class="result-line had_luck_user" style="display: block;">';
						luck_user += '<div class="result-num">'+list_num+'</div>';
				
							luck_user += '<div class="user" style="background-image: url('+val.avatar+');">';
							luck_user += '<span class="nick-name">'+val.nick_name+'</span></div></div>';
						$(".lottery-right").prepend(luck_user);
            	});
            }
        } else {
            alert("检测抽奖池没有用户或是用户被其他抽奖功能全部抽完了")
        }
    }).fail(function() {
    	
        alert("您断网了，请检查网络连接是否正常")
    });
}
var getUser = function(f) {
    if (audio_GetOne) {
        audio_GetOne.play()
    }
    $(".lottery-right").scrollTop(0);
    var b = $(".lottery-right").scroll(0).children(".result-line").length;// - 1;
    var a = $ResultSeed.clone();
    a.find(".result-num").html((b + 1));
    a.prependTo(".lottery-right").slideDown();
    var e = a.offset();
    
	 $(".lottery-run").addClass('moving');
	 $(".lottery-run").removeClass('box-moving');
	  window.setTimeout(function() { 
	   window.setTimeout(function() { 
	  $(".lottery-run").removeClass('moving');
	  },
        1000);
	  
	var c = $(".lottery-run .user");
    var d = c.clone().appendTo("body").css({
        position: "absolute",
        top: c.offset().top,
        left: c.offset().left,
        width: c.width(),
        height: c.height()
    }).addClass('').animate({
        width: 60,
        height: 60,
        top: e.top + 5,
        left: e.left + 50
    },
    500, 
    function() {
        var g = d.css("background-image");
        d.appendTo(a).removeAttr("style").css({
            "background-image": g
        });
        if ($.isFunction(f)) {
            f.call(this)
        }
    })
	 },
        3000)
};
var start_game = function() {
    //console.log(Players);
	
//    winer_count = $(".select-value").text() * 1;
    winer_count = $("#num").val() * 1;
    if (winer_count <= Players.length) {
    	$(".lottery-run").removeClass('moving');
    	$(".lottery-run").addClass('box-moving');
    	
        $(".control.button-run").hide();
        flgPlaying = true;
        playanimate();
        if (audio_Running) {
            audio_Running.play()
        }
        window.setTimeout(function() {
            $(".control.button-stop").fadeIn()
        },
        500)
    } else {
        alert("计划选" + winer_count + "人，但是只剩" + Players.length + "人可选，请减少选取数！")
    }
};
var stop_game = function() {
    $(".control.button-stop").hide();
    if (audio_Running) {
        audio_Running.pause();
    }
    if ($.isArray(Players)) {
        winer_count = $("#num").val() * 1;
        if (winer_count <= Players.length) {
            getWiner()
        } else {
            alert("计划选" + winer_count + "人，但是只剩" + Players.length + "人可选，请减少选取数！");
            //$(".control.button-stop").fadeIn();
        }
    } else {
        alert("无法获得游戏数据，与游戏服务器断开，请刷新重试！")
    }
};
var winer_count = 0;

var getWiner = function() {
    flgPlaying = false;
    window.clearTimeout(tmr_playanimate);
    var b=false;
    var a;
    var len=Players.length;
    for(var i=0;i<len;i++){
    	if(Players[i].designated==2){
    		b=i;
    		a=Players.splice(b, 1)[0];
    		break;
    	}
    }
    while(b===false){
    	b = Math.floor(Math.random() * Players.length);
    	if(Players[b].designated==3){
    		b=false;
    		continue;
    	}else{
    		a=Players.splice(b, 1)[0];
    	}
    }
    
	//提交中奖信息
	$.ajax({
    	url:"ajax_act_lottory.php?action=ok",//PATH_ACTIVITY + Path_url('lottory_save_user'),
    	data:{"openid":a.openid,"awardid":$('select[name=luckTag]').val(),'from':'cjx'},
    	type:"post",
		dataType:'json',
    	async:true,
    	success:function(d){
    		if(d.ret>0){
                var leftperson=$('#leftperson').val();
                leftperson=leftperson-1;
                $('#leftperson').val(leftperson);
    			$(".usercount-label").html(leftperson + "人");
                //$(".usercount-label").html(Players.length + "人");
    		    $(".lottery-run .user").css({
    		        "background-image": "url(" + d.data.avatar + ")"
    		    });
    		    $(".lottery-run .user .nick-name").html(d.data.nick_name);
    			$(".lottery-run .user .mobile").html(d.data.mobile);
    		}
    	},
    	error:function(){
			alert('您断网了，请检查网络连接是否正常');
		}
    });
	
    window.setTimeout(function() {
        getUser(function() {
            winer_count--;
            if (winer_count > 0) {
                flgPlaying = true;
               
                window.setTimeout(function() {
					 playanimate();
                    getWiner();
                },
                1000)
            } else {
                $(".control.button-run").fadeIn()
            }
        })
    },
    500)
};
var curr_index = 0;
var flgPlaying = false;
var tmr_playanimate;
var playanimate = function() {
    if (Players[curr_index]) {
        var a = Players[curr_index];
        $(".lottery-run .user").css({
            "background-image": "url(" + a.avatar + ")"
        });
        $(".lottery-run .user .nick-name").html(a.nick_name);
        curr_index++;
        if (curr_index >= Players.length) {
            curr_index = 0
        }
        if (flgPlaying) {
            tmr_playanimate = window.setTimeout(playanimate, 100)
        }
    }
};
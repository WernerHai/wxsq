var rankTopTen = [];
var RanksPosition = [];
var Players = {};
var audio_CutdownPlayer,
audio_NewPlayer,
audio_Outride,
audio_Gameover;
function findUserByID(c, a) {
    if ($.isArray(a)) {
        var b = a.length;
        while (b--) {
            if (a[b]["mid"] == c) {
                return b
            }
        }
        return - 1
    } else {
        return - 1
    }
}
function mess(b) {
    var c = Math.floor,
    h = Math.random,
    a = b.length,
    f,
    e,
    d,
    g = c(a / 2) + 1;
    while (g--) {
        f = c(h() * a);
        e = c(h() * a);
        if (f !== e) {
            d = b[f];
            b[f] = b[e];
            b[e] = d
        }
    }
    return b
}
function RndRank() {
    mess(rankTopTen)
} (function(b) {
    var c = 0;
    var d = ["ms", "moz", "webkit", "o"];
    for (var a = 0; a < d.length && !b.requestAnimationFrame; ++a) {
        b.requestAnimationFrame = b[d[a] + "RequestAnimationFrame"];
        b.cancelAnimationFrame = b[d[a] + "CancelAnimationFrame"] || b[d[a] + "CancelRequestAnimationFrame"]
    }
    if (!b.requestAnimationFrame) {
        b.requestAnimationFrame = function(i, f) {
            var e = new Date().getTime();
            var g = Math.max(0, 16 - (e - c));
            var h = b.setTimeout(function() {
                i(e + g)
            },
            g);
            c = e + g;
            return h
        }
    }
    if (!b.cancelAnimationFrame) {
        b.cancelAnimationFrame = function(e) {
            clearTimeout(e)
        }
    }
})(window); (function(a) {
    a.GameTimer = function(b, c) {
        this.__fn = b;
        this.__timeout = c;
        this.__running = false;
        this.__lastTime = Date.now();
        this.__stopcallback = null
    };
    a.GameTimer.prototype.__runer = function() {
        if (Date.now() - this.__lastTime >= this.__timeout) {
            this.__lastTime = Date.now();
            this.__fn.call(this)
        }
        if (this.__running) {
            a.requestAnimationFrame(this.__runer.bind(this))
        } else {
            if (typeof this.__stopcallback === "function") {
                a.setTimeout(this.__stopcallback, 100)
            }
        }
    };
    a.GameTimer.prototype.start = function() {
        this.__running = true;
        this.__runer()
    };
    a.GameTimer.prototype.stop = function(b) {
        this.__running = false;
        this.__stopcallback = b
    }
})(window);
var tick = 1000;
var LineLength = $(window).width();
var PlayStep = 16;
var flgGameStop = false;
var mainTick = new GameTimer(function() {
    $.each(RanksPosition, 
    function(c, d) {
        var e = d + PlayStep;
        if (c == 0 || e < RanksPosition[c - 1] - size * 3 / 4) {
            RanksPosition[c] = e
        }
    });
    setTopLeft();
    if (flgGameStop) {
        window.clearTimeout(tmr_GameDataLoad);
        mainTick.stop();
        var b = $(".tracklist").width() - size + diff;
        for (var a = 0; a < RanksPosition.length; a++) {
            RanksPosition[a] = b
        }
        setTopLeft()
    }
},
tick);
function gameTick() {
    $.each(RanksPosition, 
    function(a, b) {
        var c = b + PlayStep;
        if (a == 0 || c < RanksPosition[a - 1] - size * 3 / 4) {
            RanksPosition[a] = c
        }
    });
    setTopLeft()
}
function setTopLeft() {
    $(".tracklist .player").each(function() {
        var b = $(this),
        a = b.attr("uid");
        if (findUserByID(a, rankTopTen) < 0) {
            b.remove().removeClass("rotateout")
        }
    });
    $.each(rankTopTen, 
    function(b, d) {
        var c = d.mid;
        if ($(".player.player" + c).attr("uid") != c) {
            if (Players[c]) {
                if (!Players[c].$elm) {
                    var a = Players[c].$elm = $PlayeSeed.clone().addClass("player" + c).attr("uid", c);
                    a.find(".head").css({
                        "background-image": "url(" + rankTopTen[b]["avatar"] + ")"
                    }).addClass("shake");
                    a.find(".nickname").html(rankTopTen[b]["nick_name"]);
                    if (audio_NewPlayer) {
                        audio_NewPlayer.play()
                    }
                } else {
                    if (audio_Outride) {
                        audio_Outride.play()
                    }
                }
                var e = RanksPosition[b] - size * 2;
                if (e < 0) {
                    e = 0
                }
                Players[c].$elm.css({
                    left: e,
                    top: lineHeight * b + diff
                }).appendTo(".tracklist")
            } else {}
        } else {
            $(".player.player" + c).css({
                left: RanksPosition[b],
                top: lineHeight * b + diff
            })
        }
    })
}
var tmr_GameDataLoad;
var gameTimeRun = function() {
  $.getJSON(PATH_ACTIVITY + Path_url('ajax_act_shake.php?action=working'), {
	  roundno:CURR_ROUND,
    },
    function(d) {
        if (d.errno == 0 && d.data) {
            if ($.isArray(d.data)) {
                rankTopTen = d.data.slice(0, SHAKE_LINE);
                var a = rankTopTen.length;
				var s;
				for(i=0;i<a;i++){
					if(i<3&&rankTopTen[i]["progress"]>=100){
						$("#tx"+i).attr("src",WEB_URL+"images/shake/p"+i+".jpg");
					}else{
						$("#tx"+i).attr("src",rankTopTen[i]["avatar"]);
					}
					$("#nc"+i).html(rankTopTen[i]["nick_name"]);
					//s =parseInt(rankTopTen[i]["mid"])%10;
					$("#ph"+i).animate({height:rankTopTen[i]["progress"]+'%'});
					if(window.paomatype==0){
						s = i+1;
						if(s==11){
							s = 1;
						}
						$("#ph"+i).css("background","url("+WEB_URL+"images/shake/bo"+s+".png) no-repeat center top");
					}
					$("#pxh"+i).animate({height:rankTopTen[i]["progress"]+'%'});;
				}
            }
            if (d.status  == 4) {
					window.clearInterval(tmr_GameDataLoad);
                    var e = $(".tracklist").height() - size + diff;
                    for (var b = 0; b < RanksPosition.length; b++) {
                        RanksPosition[b] = e
                    }
                    window.setTimeout(function() {
                        showGameResult();
                        hideSlogan()
                    },
                    660)
            } 
        }
    });
  
 };
function showGameResult() {
    var b = $(".result-layer").show();
    var d = $(".result-label", b).show().addClass("pulse");
    var a = $(".result-cup", b).hide();
    var c = ROUNDS_LIST[CURR_ROUND]["pnum"];
    if (audio_Gameover) {
        audio_Gameover.play()
    }
    if (c <= 3 && !rankTopTen[3]) {
        $(".button.allresult", a).hide()
    } else {
        $(".button.allresult", a).show()
    }
    if (CURR_ROUND < (ROUND_COUNT - 1)) {
        $(".button.nexttound").show()
    } else {
        $(".button.nexttound").hide()
    }
    window.setTimeout(function() {
        d.fadeOut(function() {
            a.show(function() {
                if (c >= 1 && rankTopTen[0]) {
                    window.setTimeout(function() {
                        var e = $PlayeSeed.clone().addClass("result").css({
                            left: "50%",
                            "margin-left": "-65px",
                            width: "160px",
                            height: "160px",
                            bottom: "150px"
                        });
                        e.find(".head").css({
                            "background-image": "url(" + rankTopTen[0]["avatar"] + ")"
                        }).addClass("shake");
                        e.find(".nickname").html(rankTopTen[0]["nick_name"]);
                        e.appendTo(a).addClass("bounce")
                    },
                    800)
                }
                if (c >= 2 && rankTopTen[1]) {
                    window.setTimeout(function() {
                        var e = $PlayeSeed.clone().addClass("result").css({
                            left: "40px",
                            width: "100px",
                            height: "100px",
                            bottom: "120px"
                        });
                        e.find(".head").css({
                            "background-image": "url(" + rankTopTen[1]["avatar"] + ")"
                        }).addClass("shake");
                        e.find(".nickname").html(rankTopTen[1]["nick_name"]);
                        e.appendTo(a).addClass("bounce")
                    },
                    1800)
                }
                if (c >= 3 && rankTopTen[2]) {
                    window.setTimeout(function() {
                        var e = $PlayeSeed.clone().addClass("result").css({
                            right: "30px",
                            width: "70px",
                            height: "70px",
                            bottom: "100px"
                        });
                        e.find(".head").css({
                            "background-image": "url(" + rankTopTen[2]["avatar"] + ")"
                        }).addClass("shake");
                        e.find(".nickname").html(rankTopTen[2]["nick_name"]);
                        e.appendTo(a).addClass("bounce")
                    },
                    2800)
                }
            })
        }).removeClass("pulse")
    },
    1000)
}
var $PlayeSeed,
lineHeight,
diff = 10;
var size;
var resizePart = window.WBActivity.resize = function() {
    var b = $(".Panel.Track"),
    a = b.find(".tracklist").children();
    size = lineHeight = b.height() / SHAKE_LINE;
    roundLength = $(".Panel.Track .tracklist").width() - size;
    a.each(function() {
        $(this).css({
            height: size,
            "line-height": size + "px",
            "font-size": size * 3 / 5 + "px"
        }).find(".track-start,.track-end").css({
            width: size + "px",
            height: size + "px"
        })
    });
    $PlayeSeed = $('<div class="player"><div class="head"></div><div class="nickname"></div></div>').css({
        width: size - diff * 2,
        height: size - diff * 2
    })
};
var start = window.WBActivity.start = function() {
	bindhotkey();
    window.WBActivity.hideLoading();
    var d = document.getElementById("Audio_CutdownPlayer");
    if (d.play) {
        audio_CutdownPlayer = d
    }
    var c = document.getElementById("Audio_NewPlayer");
    if (c.play) {
        audio_NewPlayer = c
    }
    var b = document.getElementById("Audio_Outride");
    if (b.play) {
        audio_Outride = b
    }
    var a = document.getElementById("Audio_Gameover");
    if (a.play) {
        audio_Gameover = a
    }
    $(".Panel.Top").css({
        top: 0
    });
    $(".Panel.Bottom").css({
        bottom: 0
    });
    $(".Panel.Track").css({
        display: "block",
        opacity: 1
    });
    createTrack();
    resizePart();
    window.setTimeout(function() {
        nextRound()
    },
    2000);
    $(".round-welcome .button-start").on("click", 
    function() {
        $(".round-welcome").slideUp(function() {
            cutdown_start()
        })
    });
    $(".button.nexttound").on("click", 
    function() {
        CURR_ROUND++;
        CURR_ROUND_ID = ROUNDS_LIST[CURR_ROUND]["id"];
        nextRound()
    });
    $(".button.allresult").on("click", 
    function() {
        showScore(CURR_ROUND_ID)
    });
    $(".button.reset").on("click", 
    function() {
        if (confirm("重玩本轮会导致本轮成绩作废并清空，您确定吗？")) {
            $.getJSON('ajax_act_shake.php?action=reset', {
//                rotate_id: CURR_ROUND_ID,
                roundno:CURR_ROUND,
//                type: "reset"
            },
            function(c) {
			 if(c.errno>0){
                nextRound();
			 }else if(c.errno==-2){
				alert("当前轮数无人参与、无法开始!");
				window.location.reload()
			 }else{
				alert("重置失败啦");
                window.location.reload()
			 }
            })
        }
    })
};
var tmr_cutdown_start;
var cutdown_start = function() {
	console.log('cutdown_start');
    var a = $(".cutdown-start"),
    b = SHAKE_INFO.ready_time * 1 + 1;
    a.html("").show().css({
        "margin-left": -a.width() / 2 + "px",
        "margin-top": -a.height() / 2 + "px",
        "font-size": a.height() * 0.7 + "px",
        "line-height": a.height() + "px"
    }).addClass("cutdownan-imation");
    tmr_cutdown_start = window.setInterval(function() {
        b--;
        if (b == 0) {
        	console.log('cutdown_start1');
        	//开始活动
            $.getJSON('ajax_act_shake.php?action=start', {
                roundno: CURR_ROUND,//0开始的
            },
            function(c) {
            	if (c.errno >= 0) {
                    a.html("GO!")
                }else if(c.errno==-3){
                	setTimeout('gameTimeRun()',2000) ;
                	return;
                }else{
                	a.hide();
                	alert(c.message);
                    window.location.reload();
                }
            	
//                if (c.errno >= 0) {
//                    a.html("GO!")
//                }else if(c.errno==-2){
//				 a.hide();
//				 alert("当前轮数无人参与、无法开始!");
//				 window.location.reload()
//				} else {
//                    alert("游戏初始参数错误，请刷新重新开始");
//                    window.location.reload()
//                }
            }).fail(function() {
                alert("无法连接游戏服务器，请刷新重新开始");
                window.location.reload()
            })
        } else {
            if (b < 0) {
                window.clearInterval(tmr_cutdown_start);
                a.hide();
              tmr_GameDataLoad=window.setInterval('gameTimeRun()',1500);
                showSlogan()
            } else {
                audio_CutdownPlayer.play();
                a.html(b)
            }
        }
    },
    1000)
};

var cutdown_startb = function() {
	console.log('cutdown_startb');
    var a = $(".cutdown-start"),
    bb = 1;
    a.html("").show().css({
        "margin-left": -a.width() / 2 + "px",
        "margin-top": -a.height() / 2 + "px",
        "font-size": a.height() * 0.7 + "px",
        "line-height": a.height() + "px"
    }).addClass("cutdownan-imation");
    tmr_cutdown_start = window.setInterval(function() {
        bb--;
        if (bb == 0) {
//        	console.log('cutdown_startb');
            $.getJSON('ajax_act_shake.php?action=start', {
            	roundno:CURR_ROUND
//                rid: scene_id,
//                rotate_id: CURR_ROUND_ID,
//                type: "start"
            },
            function(c) {
            	if (c.errno >= 0) {
                    a.html("GO!")
                }else if(c.errno==-3){
//                	gameTimeRun();
                	setTimeout('gameTimeRun()',2000) ;
                	return;
                }else{
                	a.hide();
                	alert(c.message);
                    window.location.reload();
                }
//                 if (c.errno == 0) {
//                    a.html("GO!")
//                }else if(c.errno==-2){
//				 a.hide();
//				 alert("当前轮数无人参与、无法开始!");
//				 window.location.reload()
//				} else {
//
//                    alert("游戏初始参数错误，请刷新重新开始");
//                    window.location.reload()
//                }
            }).fail(function() {
                alert("无法连接游戏服务器，请刷新重新开始");
                window.location.reload()
            })
        } else {
            if (bb< 0) {
                window.clearInterval(tmr_cutdown_start);
                a.hide();
				 tmr_GameDataLoad=window.setInterval('gameTimeRun()',1500);
                //gameTimeRun();
                showSlogan()
            } else {
                audio_CutdownPlayer.play();
                a.html(bb)
            }
        }
    },
    1000)
};
var roundTime,
roundLength;
var nextRound = function() {
    $(".result-layer").hide();
	resethd();
    if ((CURR_ROUND == -1 && ROUND_COUNT > 0) || (CURR_ROUND >= ROUND_COUNT)) {
        alert("轮次全部完成，直接显示中奖结果。");
        $(".Panel.Track").hide();
        showScore();
        $(".frame-dialog .closebutton").hide()
    } else {
        if (ROUND_COUNT == 0) {
            alert("没有设置轮次")
        } else {
            flgGameStop = false;
            Players = {};
            RanksPosition = [];
            for (var a = 0; a < SHAKE_LINE; a++) {
                RanksPosition[a] = 0
            }
            roundTime = ROUNDS_LIST[CURR_ROUND]["countdown"] * 1000;
            PlayStep = roundLength / ((roundTime / tick) >> 0);
            $(".Panel.Track .tracklist").find(".player").remove();
            $(".result-layer").hide().find(".player").remove();
            $(".round-welcome").slideDown().find(".round-label").html("ROUND " + (CURR_ROUND + 1));
			//轮次已经开始的
			if(ROUNDS_LIST[CURR_ROUND].status==2){
			$(".round-welcome").slideUp(function() {
				cutdown_startb()
        	})
			}
        }
    }
};
function createTrack() {
    var b = "";
    for (var a = 0; a < SHAKE_LINE; a++) {
        b += '<div class="trackline"><div class="track-start">' + (a + 1) + '</div><div class="track-end"></div></div>'
    }
    $(b).appendTo(".Track .tracklist").hide().each(function(c) {
        var d = $(this);
        window.setTimeout(function() {
            d.show().addClass("leftfadein")
        },
        100 * c)
    })
}
function showScore(b) {
    var a = PATH_ACTIVITY + Path_url('shake_result.php')+"?roundno=" + CURR_ROUND;
//    if (b != undefined) {
//        a += "&rotate_id=" + b
//    }
    $.showPage(a)
}
var tmr_slogan;
function showSlogan() {
    $(".Panel.Top").css({
        top: "-" + $(".Panel.Top").height() + "px"
    });
    $(".Panel.Bottom").css({
        bottom: "-" + $(".Panel.Bottom").height() + "px"
    });
    var c = ($.isArray(SHAKE_INFO.slogan_list) && SHAKE_INFO.slogan_list.length > 0) ? SHAKE_INFO.slogan_list: ["再大力！", "再大力,再大力！", "再大力,再大力,再大力！", "摇，大力摇", "快点摇啊，别停！", "摇啊，摇啊，摇啊", "小心手机，别飞出去伤到花花草草", "看灰机～～～"];
    var a = c.length;
    var b = $(".Panel.SloganList").css({
        top: "-15%"
    }).show();
    b.css({
        top: 0,
        "line-height": b.height() + "px"
    });
    tmr_slogan = window.setInterval(function() {
        b.html(c[Math.floor(Math.random() * a)])
    },
    1000)
}
function hideSlogan() {
    window.clearInterval(tmr_slogan);
    $(".Panel.SloganList").hide();
    $(".Panel.Top").css({
        top: 0
    });
    $(".Panel.Bottom").css({
        bottom: 0
    })
};

function resethd(){
	for(i=0;i<10;i++){
		$("#tx"+i).attr("src",WEB_URL+'images/shake/touxiang.jpg');
		$("#nc"+i).html('');
		//s =parseInt(rankTopTen[i]["mid"])%10;
		$("#ph"+i).animate({height:'0%'});
		//$("#ph"+i).css("background","url(index/wxc/paoche/ma"+s+".png) no-repeat right center");
		$("#pxh"+i).animate({height:'0%'});;					
	}
}
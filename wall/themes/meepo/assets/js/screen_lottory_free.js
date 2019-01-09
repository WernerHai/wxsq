var $ResultSeed;
var Players;
var Winers;
var audio_Running,
audio_GetOne;
var tagList ="" ;
var numList ="" ;
var buttonurl;
var hasnd;
var cj_per = []; 
//一次抽取num人
var num;

var resizePart = window.WBActivity.resize = function() {};
var start = window.WBActivity.start = function() {
	bindhotkey();
	num=$('#num').val();
	num=parseInt(num);
	var base = $(".usercount-label").html();
	base = base.replace("人",""); 
	base = jQuery.trim( base )
	base =parseInt(base);
	num=base>num?num:base;
	console.log('test');
	console.log(num);
    window.WBActivity.hideLoading();
    /*var b = document.getElementById("Audio_Running");
    if (b.play) {
        audio_Running = b
    }
    var a = document.getElementById("Audio_Result");
    if (a.play) {
        audio_GetOne = a
    }*/
    $(".usercount-label").html("加载数据中...");
    $(".control").hide();
	cj_ready();
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
    
    //避免重复绑定事件
    $(".control.button-run").unbind("click");
    $(".control.button-stop").unbind("click");
    $(".control.button-run").on("click", manual_start_lottory);
    $(".control.button-stop").on("click",manual_stop_lottory);
};


var cj_ready = function () {	
   cj_per = [];
   var awardid=$('#tagid').val();
   $.ajax({
		url:PATH_ACTIVITY + Path_url('ajax_act_lottory.php'),
    	data:{action: 'ready','awardid':awardid,'from':'cj'},
    	type:"get",
		dataType:'json', 
		async:false,
    	success:function(json){
    		if(json.ret==-1){
    			return false;
    		}
    		if(json.ret<0 ){
    			alert(json.message);
    			return false;
    		}
    		if(json.data){
    			cj_per = json.data;
    		}
			$(".usercount-label").html(json.count + "人");
			$(".control.button-run").fadeIn();
    		

			if(!json.luckuser){
				$("#loading").hide();
				return false;
			}
			$.each(json.luckuser,function(i,val){
				var list_num = i +1;
				var luck_user = '<div class="result-line had_luck_user" style="display: block;">';
					luck_user += '<div class="result-num">'+list_num+'</div>';
					luck_user += '<i class="delLottery" onclick="delLuckUser("'+val.openid+'")"></i>';
						luck_user += '<div class="user" style="background-image: url('+val.avatar+');">';
						luck_user += '<span class="nick-name">'+val.nick_name+'</span></div></div>';
					$(".lottery-right").prepend(luck_user);
			});
			$("#loading").hide();
			
    	}
    });
}

function tip(msg,autoClose){
	var div = $("#poptip");
	var content =$("#poptip_content");
	if(div.length<=0){
		div = $("<div id='poptip'></div>").appendTo(document.body);
		content =$("<div id='poptip_content'>" + msg + "</div>").appendTo(document.body);
	}else{
		content.html(msg);
		content.show(); div.show();
	}
	if(autoClose) {
		setTimeout(function(){
			content.fadeOut(500);
			div.fadeOut(500);
		},1000);
	}
}
function tip_close(){
	$("#poptip").fadeOut(500);
	$("#poptip_content").fadeOut(500);
}

function changeLuck(obj){//改变奖项
	var option=$(obj).val();
	var imagepath= $(obj).find("option:selected").attr('data-image');
	var awardname=$(obj).find("option:selected").text();
	imagepath=imagepath==''?'themes/meepo/assets/images/defaultaward.jpg':imagepath;
	$('.lottery-award').find('img').attr('src',imagepath);
	$('.lottery-award').find('p').text(awardname);
	//var option = obj;
	if(option==-1){
		 return;
	}
	$("#loading").show();
	$(".lottery-right .had_luck_user").remove();
	cj_ready();
}


function showLayer(i){
	$("#layer"+i).fadeIn();
	$("body").append("<div class=\"layerBlank\"></div>");
};
function closeLayer(o){
	$(o).parents(".layerStyle").hide();
	$("div").remove(".layerBlank");
};

function confirmLayer(openid,luckid){
	$("#layer2").fadeIn();
	$("body").append("<div class=\"layerBlank\"></div>");
	$("#layer2 :button:eq(0)").off().on("click",function(){
		delLuckUser(openid,luckid);
    })

};

function delLuckUser(list_id){
	var option = $("#tagid option:selected").val();
    $.ajax({
		url:PATH_ACTIVITY + Path_url('lottory_remove_user'),
    	data:{"rid":scene_id,"award_id":option,"list_id":list_id},
    	type:"post",
    	async:true,
    	success:function(data){
    		var base = $(".usercount-label").html();
			base = base.replace("人","");
			var person_now = parseInt(base)+1>0?parseInt(base)+1:0;
			$(".usercount-label").html(person_now+"人");
   			$(".lottery-right .had_luck_user").remove();
			cj_ready();
    	}
    });
    
}


function reset(){
	var awardid = $("#tagid option:selected").val();
	if($(".lottery-right .had_luck_user").length==0){
		return;
	}
	if(awardid>0){
		if(confirm("重新抽奖、数据将无法恢复，确定吗？")){
			$.ajax({
				url:'ajax_act_lottory.php?action=reset',
				data:{"awardid":awardid,"from":'cj'},
				type:"post",
				async:true,
				success:function(data){
					$(".lottery-right .had_luck_user").remove();
					cj_ready();
					//$(".usercount-label").html(cj_per.length+"人");
				}
			});
	    }
   }
}

var isChange=true;

var timer;
var numPrizeName;
var luck_num;
function manual_start_lottory(){
	num=$('#num').val();
	num=parseInt(num);
	var base = $(".usercount-label").html();
	base = base.replace("人",""); 
	base = jQuery.trim( base );
	base=parseInt(base);
	num=base>num?num:base;
	if(num<=0){
		return false;
	}
	$(".control.button-run").fadeOut(1)
	$(".control.button-stop").fadeIn(1)
	start_lottory();
}
function start_lottory(i){
	if(num<=0){
		return false;
	}
	var option = $("#tagid option:selected").val();
	if(option==-1){
		alert('请选择奖项');
		return;
	}

	var base = $(".usercount-label").html();
	base = parseInt(base.replace("人","")); 
	if(base ==0){
		alert('参与抽奖人数太少没法抽了！！');
		return;
			
	}
	timer=setInterval(function(){
		changeNum();
	},120)

	//开始抽奖时静止修改奖项和抽取的人数
	$("#tagid").attr("disabled",'true');
	$("#num").attr("disabled",'true');
}

//点击停止抽奖按钮
function stop_lottory(){
	var option = $("#tagid option:selected").val();
	$.ajax({
    	url:"ajax_act_lottory.php?action=ok",//PATH_ACTIVITY + Path_url('lottory_save_user'),
    	data:{"awardid":option,'from':'cj'},
    	type:"post",
		dataType:'json',
    	async:true,
    	success:function(d){
    		if(d.ret>0){
    			var luck_user = '<div class="result-line had_luck_user" style="display: block;">';
				luck_user += '<div class="result-num">'+$('.result-line').length+'</div>';
				luck_user += '<i class="delLottery" onclick="delLuckUser(\"'+d.data.openid+'\")"></i>';
				luck_user += '<div class="user" style="background-image: url('+d.data.avatar+');">';
				luck_user += '<span class="nick-name">'+d.data.nick_name+'</span></div></div>';
				$(".lottery-right").prepend(luck_user);
				
//				$(".control.button-run").fadeIn()
//				$(".control.button-stop").fadeOut()
				
				var base = $(".usercount-label").html();
				base = base.replace("人",""); 
				base = jQuery.trim( base )
				base =parseInt(base);
				
				base=base-1;
				
				$(".usercount-label").html(base+'人');
				
				$(".lottery-run").find(".user").css({"background-image":"url("+d.data.avatar+")"});
				$(".lottery-run").find('.nick-name').text(d.data.nick_name);
				
				clearInterval(timer);//清除定时器 不在滚动
					num--;
					if(num>0){//
						setTimeout(function(){
							
							start_lottory(num);
						},1000);
						setTimeout(function(){
							stop_lottory();
						},2000);
						$(".control.button-stop").fadeOut(1);
				    	//$(".control.button-run").html("开始抽奖("+num+")");
				    	$(".control.button-run").fadeOut(1);
					}else{
						$("#tagid").removeAttr("disabled");
						$("#num").removeAttr("disabled");
						$(".control.button-stop").fadeOut(1);
						$(".control.button-run").fadeIn(1);
//						$(".control.button-run").html("开始抽奖");
					}
    		}else{
    			alert(d.message);
    			return false;
    		}
    	},
    	error:function(d){
    		alert('您的网络断了，请检查网络情况，后刷新此页面！');
    		clearInterval(timer);
    		return false;
    	}
    });
}
function manual_stop_lottory(){
	leftnum=$('#num').val();
	console.log(num);
	stop_lottory();
}

//选人
function changeNum(){
	var p_num = cj_per.length - 1;
	var randomVal = Math.round(Math.random() * p_num);
    numPrizeName = cj_per[randomVal];
	luck_num = randomVal;
	$(".lottery-run .user").css({
        "background-image": "url(" + numPrizeName.avatar + ")"
    });
    $(".lottery-run .user .nick-name").html(numPrizeName.nick_name);
}

var resizePart = window.WBActivity.resize = function() {};
var start = window.WBActivity.start = function() {
	bindhotkey();
    window.WBActivity.hideLoading();
    $(".Panel.Top").css({
        top: 0
    });
    $(".Panel.Bottom").css({
        bottom: 0
    });
    $(".Panel.bimu").css({
        display: "block",
        opacity: 1
    });
};
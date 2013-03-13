$(function() {
    $("input[name=inheritViews]").change(function() {
    if(this.checked) {
        $("#viewsSetting").find("input").attr("disabled","disabled");
    }else{
        $("#viewsSetting").find("input").removeAttr("disabled");
    }
    
});
    

});
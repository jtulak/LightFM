
lightFM.sidebar = new function (){
    /*******************
     * private variables
     */
    


    /******************
     * public variables
     */
    
    /****************
     * public methods
     */
    this.toggle = function(){
	var sidebarContent = $("#sidebar-content");
	if(sidebarContent.width() == 0){
	    sidebarContent.animate({'width':$(".sidebar-content-width").width()},200,function(){
		lightFM.resized();
	    
	    });
	}else{
	    sidebarContent.animate({'width':0},200,function(){
		lightFM.resized();
	    
	    });
	}
	// simulate the resize event
	lightFM.resized();
    };
    
};




/******************************************************************************/



lightFM.addOnLoadCallback(function(){
    $(document).on("click", "#sidebar-control ", function() {
	lightFM.sidebar.toggle();
    });
});

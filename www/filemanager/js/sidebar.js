
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
	    sidebarContent.animate({'width':$(".sidebar-content-width").width()},200);
	}else{
	    sidebarContent.animate({'width':0},200);
	}
	
    };
    
};




/******************************************************************************/



lightFM.addOnLoadCallback(function(){
    $(document).on("click", "#sidebar-control ", function() {
	lightFM.sidebar.toggle();
    });
});

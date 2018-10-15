/*
 ### Zeitguys Item Ratings Zoom Plugin v1.0 ###
*/
jQuery(document).ready(function( $ ){
	
	// Get all the thumbnail
	$('div.zg-zoom-thumbnail-item').mouseenter(function(e) {
		
		var imageContainer = null;
		
		imageContainer = $(this).find('div.zg-zoom-tooltip');
		
		//Load zoom image for this post
		if( imageContainer.find('img').length < 1 ) {
			zgItemRatingZoomGetImage( $(this) );
		}
		
		// Calculate the position of the image tooltip
		x = e.pageX - $(this).offset().left;
		y = e.pageY - $(this).offset().top;
		
		// Set the z-index of the current item,
		$(this).css('z-index','15');
		
		$(this).children("div.zg-zoom-tooltip").css({'display':'block'});
		
		imgHeight = $(this).children("div.zg-zoom-tooltip").children("img").height();
		
		y = y - (imgHeight/2);
		
		// make sure it's greater than the rest of thumbnail items
		// Set the position and display the image tooltip
		$(this).children("div.zg-zoom-tooltip").css({'top': y + 10,'left': x });
		
	}).mousemove(function(e) {
	
		// Calculate the position of the image tooltip  
		x = e.pageX - $(this).offset().left;
		y = e.pageY - $(this).offset().top;
		
		imgHeight = $(this).children("div.zg-zoom-tooltip").children("img").height();
		
		y = y - (imgHeight/2);
		
		// This line causes the tooltip will follow the mouse pointer
		//$(this).children("div.zg-zoom-tooltip").css({'top': y + 10,'left': x + 20});
		
	}).mouseleave(function() {
	
		// Reset the z-index and hide the image tooltip
		$(this).css('z-index','1')
		.children("div.zg-zoom-tooltip")
		.animate({"opacity": "hide"}, "fast");
	
	});
	
	function zgItemRatingZoomGetImage( zoomObject ) {
		
		//Init vars
		var ajaxUrl = zgItemRatingZoomVars.ajax_url;
		var action	= zgItemRatingZoomVars.action;
		var nonce	= zgItemRatingZoomVars.nonce;
		var postID	= null;
		var imageContainer = null;
		
		imageContainer = zoomObject.find('div.zg-zoom-tooltip');
		
		imageLoadingDiv = imageContainer.find('div.zg-zoom-loading');
		
		//Cache post id for current zoom item
		postID = zoomObject.data('post-id');
		
		//Make ajax request to get zoom image html
		$.ajax({
			url: ajaxUrl,
			data: { 
				action: action,
				zgItemRatingZoomNonce: nonce,
				zgItemRatingZooPostID: postID
			},
			type: 'POST',
			success: function (result) {
				//Set container image html
				imageLoadingDiv.fadeOut( 500, function(){
					imageContainer.prepend(result.data.imageHtml);
					imageContainer.find('img').fadeIn( 500 );
				});
			},
			error: function (jxhr, msg, err) {
				//Do nothing
				imageLoadingDiv.fadeOut( 500 );
			}
		});
		
	}
	
});
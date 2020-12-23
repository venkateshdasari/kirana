
(function( $ ) {

	$.fn.inspireQuickview = function( initvar ) {

		/**
		 * Create List Layers By JSON Data.
		 */
		this.price = '';
		this.ischanged = false;

		this.work = function(){
			var _this = this;

			$( "body" ).delegate( "#common-home , #product-product #related , #product-category , #product-search , #product-manufacturer", "mouseover", function(){
				if( $(this).find(".inspire-quickview").length > 0 ){

				} else {
					var html = $("<div class=\"inspire-ownstyle inspire-quickview\"><a href=\"#\"><svg width=\"18px\" height=\"18px\"><use xlink:href=\"#proquick\"></use></svg></a></div>");
					if ( $( this ).find( '.product-thumb .image,.product-thumb1 .image' ).length == 0 ) {
						$( this ).append( html );
					} else {
						$( this ).find( '.product-thumb .bquickv' ).append( html );
					}
				}
			} );

			$("body").delegate( ".inspire-quickview" , "click", function() {
				var tmp = $(this).parents( '.product-thumb,.product-thumb1' ).find("[onclick]").attr("onclick");
				var id = /\d+/.exec(tmp);
				if( !isNaN(id) ){
				 	_this.open( id );
				}
				return false;
			} );
 		},

 		this.open = function( id ){
 			var url = 'index.php?route=extension/module/inspirequickview/show&product_id='+id;
 			$.magnificPopup.open({
			  	items: {
			    	src: url, // can be a HTML string, jQuery object, or CSS selector
			    	type: 'iframe',
			    	width:"80%"
			  	},
		      	callbacks: {
			        afterClose: function() {
			          	setTimeout( function() {
				            $('#cart > ul').load('index.php?route=common/cart/info ul li');
			          	}, 0 );
			        }
		      	}
			});
 		}
		//THIS IS VERY IMPORTANT TO KEEP AT THE END
		return this;
	};

 	$(document).ready( function(){
 		$(document).inspireQuickview().work();
 	} );
})( jQuery );
/***/
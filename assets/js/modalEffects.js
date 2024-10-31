
var ModalEffects = (function() {

	function init() {

		var overlay = document.querySelector( '.md-overlay' );

		var popup_time = jQuery('.popup__timeout').val();
		var popup__display = jQuery('.popup__display').val();

		if(popup_time == ''){
				popup_time = '3000';
			}
		var popup_set = getCookie('wpp_popup');

		[].slice.call( document.querySelectorAll( '.md-modal' ) ).forEach( function( el, i ) {

			var modal = document.querySelector( '#modal-1' );
			
			window.addEventListener( 'load', function( ev ) {
				if(popup__display == ''){
					setTimeout(function(){
						classie.add( modal, 'md-show' );
						delete_cookie('wpp_popup');
					},popup_time);	
					
				}
				else{
					if(popup_set != 'yes')
					{
						setTimeout(function(){
							classie.add( modal, 'md-show' );
							document.cookie = "wpp_popup=yes"
						},popup_time);
					}	
				}
			});
			jQuery('.modal_close').click(function(){
				jQuery('.md-modal').removeClass('md-show');
			});

			jQuery('body').click(function(e) {
				if (!jQuery(e.target).closest('.md-modal').length){
					jQuery('.md-modal').removeClass('md-show');
				}
			});

		} );

	}
	function getCookie(name) {
	  var value = "; " + document.cookie;
	  var parts = value.split("; " + name + "=");
	  if (parts.length == 2) return parts.pop().split(";").shift();
	}

	function delete_cookie(name) {
		document.cookie = name +'=; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
	}

	init();

})();
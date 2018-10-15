( function ( $ ) {

	$( document ).ready( function () {
		
		// user formula option
		$( "#pr_bayesian_formula input[type='radio']" ).change( function () {
			if ( $( this ).attr( 'id' ) == 'user_formula' ) {
				$( '#pr_user_formula' ).slideDown( 'fast' );
			} else {
				$( '#pr_user_formula' ).slideUp( 'fast' );
			}
		} );
		
		// custom hook option
		$( '#pr_link_location #location-custom_hook' ).change( function () {
			if ( $( this ).is( ':checked' ) ) {
				$( '#pr_custom_filter' ).slideDown( 'fast' );
			} else {
				$( '#pr_custom_filter' ).slideUp( 'fast' );
			}
		} );
		
	} );

} )( jQuery );
jQuery( function( $ ){

	//Little dirty hack. I cannot think of a better way.
	var all_posts_on_page = $( 'a.row-title' );
	if( $( '#delete_all' ).length > 0 ){
		var all_posts_on_page = $( 'td.title>strong' );
	}
	$.each( all_posts_on_page, function( index, value) {
		var element = $( value );
		var original_text = element.text();
		var span_obj = $( original_text );
		var clean_text = original_text.replace(/<span(.*)span>/, '');
		element.text( clean_text );
		if( span_obj.hasClass( 'left' ) ){
			span_obj.prependTo( element );
		}
		else{
			span_obj.appendTo( element );
		}
		
	});


	var post_selector = $( '#pi_post_selector' );
	var icon_selector = $( '#post_icon_dashicons_picker' );
	var form = post_selector.parents( 'form' );
	var dashicon_preview = $( '.dashicon-preview' );

	$( '.dashicons-picker' ).on( 'click', function(){
		$( '.dashicon-picker-container a' ).on( 'click', set_dashicon_preview );
	} );
	



	function fill_selectors(){
		var post = post_selector.val();
		$.ajax({
			url: 		ajaxurl,
			data: 		{ 'action' : 'pi_get_post_meta', 'post_id': post},
			type: 		'get',
			dataType: 	'json',
			success: 	function( response ){
				console.log(response);
				icon_selector.val( response.post_icon );
				$( 'input[name=pi_left_right][value=' + Number( response.post_icon_position ) + ']' ).prop( 'checked', true );
				set_dashicon_preview();
			}
		});
	}

	function set_dashicon_preview(){
		dashicon_preview.removeClass( function( index, class_name ){
			var found_classes = class_name.match(/dashicons-.*/);
			if( found_classes != null ){
				return found_classes.join();
			}

		} );
		dashicon_preview.addClass( icon_selector.val() );
	}

	if( post_selector.length > 0 ){
		fill_selectors();
		post_selector.change( fill_selectors );
	}
	

	form.submit( function( ){
		var selected_icon = icon_selector.val();
		var selected_post = post_selector.val();
		var icon_position = $( 'input[name=pi_left_right]:checked' ).val();
		if( selected_icon.length > 0 && selected_post.length > 0 && typeof icon_position != 'undefined'){
			$.ajax({
				url: 		ajaxurl,
				data: 		{'action' : 'add_icon', 'icon' : selected_icon, 'selected_post': selected_post, 'icon_position' : icon_position },
				type: 		'post',
				success: 	function( answer ){
					console.log( answer );
				}
			});
		}
	} );
}); 

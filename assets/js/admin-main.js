jQuery(function($){
 
	// on upload button click
	$('body').on( 'click', '.misha-upl', function(e){
 
		e.preventDefault();
 
		var button = $(this),
		custom_uploader = wp.media({
			title: 'Insert image',
			library : {
				// uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
				type : 'image'
			},
			button: {
				text: 'Use this image' // button label text
			},
			multiple: false
		}).on('select', function() { // it also has "open" and "close" events
			var attachment = custom_uploader.state().get('selection').first().toJSON();
            button.html('<img style="width:100px;" src="' + attachment.url + '">').next().val(attachment.id).next().show();
            $("input[name=naims_pro_pic]").val(attachment.id).hide();
		}).open();
 
	});
 
	// on remove button click
	$('body').on('click', '.misha-rmv', function(e){
 
		e.preventDefault();
 
		var button = $(this);
		button.next().val(''); // emptying the hidden field
		button.hide().prev().html(`<a href="#" class="misha-upl" style="padding: 10px;background: #0e0487;box-shadow: 1px 1px 22px 2px #0e035d78;text-decoration: none;">Upload image</a>`);
		button.hide().append(`<input style="display:none" type="text" name="naims_pro_pic" value="">`);
	});
 
});
jQuery(function() {
	
	app_links = [];
	
	app_links.init = function() {
		jQuery('#add_app_link').on('click', function(e) {
			var content = helpers.get_content_app_link();
			helpers.append_content(jQuery('#app_links'), content);
			e.preventDefault();
		});
		jQuery('#app_links').on('click', '.delete_app_link',function(e) {
			//console.log('click');
			$this = jQuery(this);
			//var index = jQuery('#app_links .row').index($this.parent());
			//console.log(index);
			$this.parent().remove();
			helpers.fix_even_row_classes('#app_links');
			e.preventDefault();
		});
		jQuery('#app_links').on('click','.app_links_show_custom a',function(e) {
			$this = jQuery(this);
			var row = $this.parents('.row');
			if ($this.parent().hasClass('is_custom')) {
				$this.text('Custom property');
				$this.parent().removeClass('is_custom');
				row.find('.app_links_custom').removeClass('show');
				row.find('.app_links_custom input').val('');
				row.find('.app_links_select').removeClass('hide');
			} else {
				$this.text('Default property');
				$this.parent().addClass('is_custom');	
				row.find('.app_links_custom').addClass('show');
				row.find('.app_links_select').addClass('hide');
			}
			e.preventDefault();			
		});
	}
	app_links.add_link = function() {
		
		
		
	}
	app_links.remove_link = function() {
		
		
	}
	
	/* HELPERS */
	helpers = [];
	helpers.get_content_app_link = function() {
		//count number of app_links now
		//+1 and use that index
		var index = jQuery('#app_links .row').size();
		var new_index = index + 1;
		
		var even = '';
		if(new_index % 2 == 0){
			even = ' even';	
		}
		
		var element_select = helpers.form.element_select('al_settings[app_links]['+new_index+'][select]',helpers.get_property_options());
		
		return '<div class="row'+even+'"><div class="app_links_property"><label class="app_links_property_label">Property:</label><div class="app_links_select">'+element_select+'</div><div class="app_links_input app_links_custom"><input class="textinput" type="text" id="app_links_'+new_index+'_property" name="al_settings[app_links]['+new_index+'][property]" value=""></div><div class="app_links_show_custom"><a href="#">Custom property</a></div></div><div class="app_links_content"><label class="app_links_content_label">Content:</label><div class="app_links_input"><input class="textinput" type="text" id="app_links_'+new_index+'_content" name="al_settings[app_links]['+new_index+'][content]" value="" /></div></div><a href="#" class="delete_app_link">Delete</a></div>';	
	}
	
	helpers.append_content = function (element, content) {
		element.append(content);
	}
	helpers.fix_even_row_classes = function(container) {
		jQuery(container).find('.row').removeClass(	'even');
		jQuery(container).find('.row:odd').addClass('even');
	}
	helpers.form = [];
	helpers.form.element_select = function(name, options) {
		var output = '';
		output = output + '<select name="'+name+'">';
		jQuery.each( options, function( index, value ){
			output = output + '<option value="'+index+'">'+value+'</option>';
		});
		output = output + '</select>';
		
		return output;
	}
	helpers.get_property_options = function() {
		return {
			'iOS URL':'al:ios:url','iOS App Store ID':'al:ios:app_store_id','iOS App Name':'al:ios:app_name',
			'iPhone URL':'al:iphone:url','iPhone App Store ID':'al:iphone:app_store_id','iPhone App Name':'al:iphone:app_name',
			'iPad URL':'al:ipad:url','iPad App Store ID':'al:ipad:app_store_id','iPad App Name':'al:ipad:app_name',
			'Android URL':'al:android:url','Android Package':'al:android:package','Android Class':'al:android:class','Android App Name':'al:android:app_name',
			'Windows Phone URL':'al:windows_phone:url','Windows Phone App ID':'al:windows_phone:app_id','Windows Phone App Name':'al:windows_phone:app_name',
			'Fallback Web URL':'al:web:url','Web URL Should Fallback':'al:web:should_fallback',
		};	
	}
	
	app_links.init();
	
});
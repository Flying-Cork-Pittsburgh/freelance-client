

jQuery( document ).ready(function($) {
	if ( $('.wp-admin.users-php') ){
		$('table.users tbody tr').each(function(){
			$row = $(this);
			var role = $(this).children('.column-role').text();
			if ('Administrator' == role){
				$row.hide();
			}
		});
	}
});



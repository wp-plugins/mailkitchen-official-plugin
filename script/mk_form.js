/*JS pour le formulaires*/

jQuery('.mk-form').on( "click", ".mk_form_validate", function(event){
	event.preventDefault(); 
	event.stopPropagation();
	var ident = jQuery(this).attr("data-id"),
	email 	  = jQuery(".mk_insert_mail[data-id='"+ident+"']").val(),
	numform   = jQuery(".mk_num_form[data-id='"+ident+"']").val();
	
	
	
	mkAjaxFormRequest(email,numform,ident);
});

function mkAjaxFormRequest(email,numform,ident){
	 jQuery(".mk-loader-form[data-id='"+ident+"']").parent().css("position","relative");
	 jQuery(".mk-loader-form[data-id='"+ident+"']").css("background-color","rgba(0, 0, 0, 0.7)").css("width","100%").css("height","100%").css("position","absolute").css("top","0");
     jQuery.ajax({
          url: './wp-admin/admin-ajax.php',
		  method:"post",
          data:{
               'action':'mk_ajax_form',
			   'numform':numform,
			   'email':email
               },
          dataType: 'JSON',
          success:function(data){
		  jQuery(".mk-loader-form").hide();
			/*'fn':'get_latest_posts',*/
			jQuery(".mk-form[data-id='"+ident+"']").hide();
			jQuery(".mk-form-valide[data-id='"+ident+"']").show();
          },
          error: function(errorThrown){
			jQuery(".mk-loader-form[data-id='"+ident+"']").hide();
			jQuery(".mk-form-erreur[data-id='"+ident+"']").show();
          }
 
     });
 
}

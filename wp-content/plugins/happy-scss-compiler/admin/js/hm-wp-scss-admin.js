if(typeof jQuery == 'undefined'){
	var oScriptElem = document.createElement("script");
	oScriptElem.type = "text/javascript";
	oScriptElem.src = "https://code.jquery.com/jquery-3.6.0.min.js";
	document.head.insertBefore(oScriptElem, document.head.getElementsByTagName("script")[0]);
}

(function( $ )
{
	$(function()
	{
		if( $('#hm_wp_scss__compilation_mode').val() !== undefined )
		{
			hljs.highlightAll();

			showMinification();
			$('#hm_wp_scss__compilation_mode').change(function(){
				showMinification();
			});

			function showMinification()
			{
				$('.minification_example').hide();
				$('#minification-' + $('#hm_wp_scss__compilation_mode').val().toLowerCase()).show();
			}
		}
	});
	
})( jQuery );

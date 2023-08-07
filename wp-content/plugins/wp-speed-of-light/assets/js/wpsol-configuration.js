jQuery(document).ready(function($){
   $("#get-api-button").click(function(){
       var url = $(this).data('url');
       window.open(url, '_blank');
   }); 
});
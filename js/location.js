
jQuery(document).ready(function(){
 	var lat1 = jQuery('#latitude').val();
  	var lng1 = jQuery('#longitude').val();
  	if(lat1 !='' && lng1 !='')
  	{
		  initialize(lat1,lng1);
  	}
	
 	jQuery('#address').on('change',function(){ 
		var geocoder = new google.maps.Geocoder();
		var address = jQuery('#address').val();
		geocoder.geocode( { 'address': address}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
    		var latitude = results[0].geometry.location.lat();
    		var longitude = results[0].geometry.location.lng();
			 jQuery('#longitude').val(longitude);
         	jQuery('#latitude').val(latitude);
    	 	initialize(latitude,longitude);
  		}else{
 			alert("Geocode was not successful for the following reason: " + status);
  		}
}); 
/*
	address  = jQuery('#address').val();
	jQuery.ajax({ 
   		url :"https://maps.googleapis.com/maps/api/geocode/json?address="+address+"&key="+lmkey,
      	type: "POST",
      	success:function(res){
			var lng1 = res.results[0].geometry.location.lng;
			var lat1 = res.results[0].geometry.location.lat;
            jQuery('#longitude').val(lng1);
            jQuery('#latitude').val(lat1);
		    initialize(lat1,lng1);
		}
});*/
	});
	
});
function initialize(v1,v2) {
    var myLatLng = new google.maps.LatLng( v1, v2 ),
        myOptions = {
            zoom:  parseInt(map_size),
            center: myLatLng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
            },
        map = new google.maps.Map( document.getElementById( 'dvMap' ), myOptions ),
		marker = new google.maps.Marker({position: myLatLng,draggable: true,map: map });
		var kmRadius = parseInt(map_radius);
        //marker = new google.maps.Marker( {position: myLatLng, map: map} );
		//  moveBus( map, marker );
		google.maps.event.addListener(marker, 'click', function (event) {
		 
   				 var clickCircle = new google.maps.Circle({    
     	 				strokeColor: '#FF0000',    
      					strokeOpacity: 0.5,    
      					strokeWeight: 2,    
      					fillColor: '#FF0000',    
      					fillOpacity: 0.25,    
      					map: map,    
      					center: myLatLng,    
      					radius: kmRadius * 1000,  
      					draggable:false  
    			});  
		});
google.maps.event.addListener(marker, 'dragend', function (event) {	 
   	 document.getElementById("latitude").value = event.latLng.lat();
    document.getElementById("longitude").value = event.latLng.lng();
	});
}   

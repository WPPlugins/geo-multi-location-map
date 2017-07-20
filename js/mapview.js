var blnOver = false
function initialize() {
		var geocoder;
		var map;
		var kmRadius = parseInt(map_radius);
		var labels = '123456789';
      	var labelIndex = 0;
		var clickCircle;
		var latlng=new google.maps.LatLng(markers[0].latitude, markers[0].longitude);
		  
		geocoder = new google.maps.Geocoder();
        var mapOptions = {
			center: latlng,
            zoom: parseInt(map_size),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
	
		function addCircle(location) {    
			var clickCircle = new google.maps.Circle({    
     	 	strokeColor: '#FF0000',    
      		strokeOpacity: 0.5,    
      		strokeWeight: 2,    
      		fillColor: '#FF0000',    
      		fillOpacity: 0.25,    
      		map: map,    
      		center: location,    
      		radius: kmRadius * 1000,  
      		draggable:false  
    	});  
		}
        var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
		
		var infoWindow = new google.maps.InfoWindow();
		
 		for (var i = 0; i < markers.length; i++) {
            var data = markers[i];
			 var icon = {
    			 url: data.marker_img, // url
   			     scaledSize: new google.maps.Size(50, 50), // scaled size
   			};
            var myLatlng = new google.maps.LatLng(data.latitude, data.longitude);
			if( data.marker_img != '' )
			{
            var marker = new google.maps.Marker({
                position: myLatlng,
                map: map,
				label: labels[labelIndex++ % labels.length],
				title: data.title,
				icon : icon
         	});
			} else
			{
				var marker = new google.maps.Marker({
                position: myLatlng,
                map: map,
				label: labels[labelIndex++ % labels.length],
				title: data.title,
				
         	});
		}
			
			
			(function (marker, data) {
					   document.getElementById(data.id).onmouseover = function() {
						   this.style.border = "2px solid #90bade";
                if(data.image != '')
					{
                    infoWindow.setContent("<p id='hook'><div id='map_tooltip'><div class='tooltip_img'><img src='"+ data.image +"' style='height:50px; width:50px'></img></div><div class='tootltip_text'><a href='#'><b>" + data.title +"</b></a><br><p>"+ data.address +"</p></div></div></p>");
					}else
					{
						 infoWindow.setContent("<p id='hook'><div id='map_tooltip'><div class='tooltip_img'><img src='"+ imgascpath +"' style='height:50px; width:50px'></img></div><div class='tootltip_text'><a href='#'><b>" + data.title +" :</b></a><br><p>"+ data.address +"</p></div></div></p>");
					}
                    			infoWindow.open(map, marker);
            }
            document.getElementById(data.id).onmouseout = function() {
				 this.style.border = "none";
                infoWindow.close();
            }
				 	google.maps.event.addListener(marker, "click", function (e) {
					center1 = new google.maps.LatLng(data.latitude, data.longitude),
					addCircle(center1); 
                 	document.getElementById(data.id).style.border = "2px solid #90bade"; 
					if(data.image != '')
					{
                    infoWindow.setContent("<p id='hook'><div id='map_tooltip'><div class='tooltip_img'><img src='"+ data.image +"' style='height:50px; width:50px'></img></div><div class='tootltip_text'><a href='#'><b>" + data.title +"</b></a><br><p>"+ data.address +"</p></div></div></p>");
					}else
					{
						infoWindow.setContent("<p id='hook'><div id='map_tooltip'><div class='tooltip_img'><img src='"+ imgascpath +"' style='height:50px; width:50px'></img></div><div class='tootltip_text'><a href='#'><b>" + data.title +" :</b></a><br><p>"+ data.address +"</p></div></div></p>");
					}
					
                    infoWindow.open(map, marker);
                });
				
				
				
            })(marker, data);
        }
}
google.maps.event.addDomListener(window, 'load', initialize);


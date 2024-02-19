var start = "<?php echo WC()->session->get( 'from', 1 ) ?>";
	var eind = "<?php echo WC()->session->get( 'to', 1 ) ?>";
        // Set options
//    var options = {
//        offset: 0,
//        offsetSide: 'top',
//        classes: {
//            clone:   'banner--clone',
//            stick:   'banner--stick',
//            unstick: 'banner--unstick'
//        }
//    };
//        // Initialise with options
//    var banner = new Headhesive('.banner', options);
        // Headhesive destroy
        // banner.destroy();
    var startDate,
        endDate,
        updateStartDate = function() {
            dateObject.setStartRange(startDate);
            dateObject2.setStartRange(startDate);
            dateObject2.setMinDate(startDate);

        },
         updateEndDate = function() {
            dateObject.setEndRange(endDate);
            
            dateObject2.setEndRange(endDate);

		};
    // datepicker
    var dsplit = start.split("-");
	var d = new Date(dsplit[2],dsplit[1]-1,dsplit[0]);
	var dsplit2 = eind.split("-");
	var d2 = new Date(dsplit2[2],dsplit2[1]-1,dsplit2[0]);

	function check_availability(startDate,endDate){
		console.log("startDate: "+startDate+"\nEnd date: "+endDate);
		jQuery.ajax({
			url: ajax_object.ajax_url,
			type: 'POST',
			dataType: 'json',
			data: JSON.stringify({
				action: 'check_availability',
				/* 'startDate': startDate,
				'endDate': endDate */
			}),
			success: function (response) {
				// Aquí puedes hacer algo con la respuesta que devuelve la función PHP
				console.log(response);
			},
			error: function (xhr, ajaxOptions, thrownError) {
				// Aquí puedes manejar el error si la llamada AJAX falla
			}
		});
	}
    
    var dateObject = new Pikaday({
	    field: document.getElementById("start_datePicked"),
	    minDate: new Date(),
	    defaultDate: d,
	    setDefaultDate: false,
	    bound: true,
	    format: "DD-MM-YYYY",
	    onSelect: function() {
                startDate = this.getDate();
                updateStartDate(); 
                jQuery('.update_cart-btn').delay( 300 ).trigger("click"); 
				console.log("start: "+startDate);
				console.log("end: "+endDate);
				check_availability(startDate,endDate);
       }

	});
	
	
    var dateObject2 = new Pikaday({
	    field: document.getElementById("end_datePicked"),
	    format: "DD-MM-YYYY",
	    minDate: d,
	    defaultDate: d2,
	    setDefaultDate: false,	    
	    bound: true,
	    onSelect: function() {
                endDate = this.getDate();
                updateEndDate();
                jQuery('.update_cart-btn').delay( 300 ).trigger("click");
				console.log("start: "+startDate);
				console.log("end: "+endDate);  
				check_availability(startDate,endDate);
       }

	});
	
	jQuery( document ).ajaxSuccess(function() {
		

		
		
		dateObject.destroy();
        dateObject = new Pikaday({
		    field: document.getElementById("start_datePicked"),
		    minDate: new Date(),
		    defaultDate: startDate,
		    setDefaultDate: false,
		    bound: true,
		    format: "DD-MM-YYYY",
		    onSelect: function() {
	                startDate = this.getDate();
	                updateStartDate(); 
	                jQuery('.update_cart-btn').delay( 300 ).trigger("click");  
	       }
		});
		dateObject2.destroy();
        dateObject2 = new Pikaday({
		    field: document.getElementById("end_datePicked"),
		    minDate: new Date(),
		    defaultDate: endDate,
		    setDefaultDate: false,
		    bound: true,
		    format: "DD-MM-YYYY",
		    onSelect: function() {
	                endDate = this.getDate();
	                updateEndDate(); 
	                jQuery('.update_cart-btn').delay( 300 ).trigger("click");  
	       }
		});
	

	});	
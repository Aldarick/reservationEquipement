function changeMateriaux2(value , formObj, selected) {
	$.post(
		'getMateriaux.php',
		{room_id : value},
		function(data) {
			$("#materiaux").html("");
			materiaux = data.split('/');
            to_select = 0;
            

            if (materiaux.length > 0){
			for (i = 0; i < materiaux.length; i++) {

				t = materiaux[i].split("*");
                
                if (undefined != t[1]){
				$("#materiaux").append($("<option>"+t[1]+"</option>").attr({value : t[0]}));
                    if(t[0] == selected) to_select = i;
                }
			}
			t = materiaux[0].split("*");
            
			// select the first entry by default to ensure
            // that one materiau is selected to begin with
            if (materiaux.length > 0 )  // but only do this if there is a materiau
            {
              materiauxObj = eval( "formObj.elements['materiaux']" );
              materiauxObj.options[to_select].selected = true;
            }
            }
		},
		'text'
	);
}


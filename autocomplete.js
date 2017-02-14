function refresh(val) {
	$.post(
		'autocomplete.php',
		{value : val},
		function(data) {
			$("#projet").html("");
			res = data.split("_");
			if (res.length >= 1 && res[0] != "") {
				for (i = 0; i < res.length; i++) {
					t = res[i].split("*");
					$("#projet").html($("#projet").html() + "<li value=\""+t[0]+"\">"+t[1]+"</li>");
				}
			}
		},
		'text'
	);
}
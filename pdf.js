function genererPDF(idEleve) {
	$.post(
		'pdf.php',
		{code : idEleve, 
		 maths : $('#maths').val(), 
		 physique : $('#physique').val(), 
		 francais : $('#francais').val(), 
		 svt : $('#svt').val(), 
		 anglais : $('#anglais').val(), 
		 sport : $('#sport').val()},
		
		function(data) {
			
		},
		'text'
	)
}
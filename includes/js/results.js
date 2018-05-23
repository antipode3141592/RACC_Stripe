jQuery(document).ready(function(){
	jQuery('#printpreviewbutton').click(function(){
		console.log("printpreviewbutton script");
		var printableContent = jQuery('#resultscontent').html();
		var windowFeatures = "menubar=yes,scrollbars=yes"
		var printWindow = window.open('','',windowFeatures);
		printWindow.document.write('<html><head><title>Work for Art Donation - Print Preview</title>');
		printWindow.document.write('</head><body>');
		printWindow.document.write(printableContent);
		printWindow.document.write('<a href="javascript:window.print()">Print!</a>');
		printWindow.document.write('</body></html>');
		printWindow.document.close();
	});
});

jQuery(document).ready(function(){
	jQuery('#errorbackbutton').click(function(){
		window.history.back();		
	});
});
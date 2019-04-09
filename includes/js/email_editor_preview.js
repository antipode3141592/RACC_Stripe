//Open new window with html preview of email template
jQuery(document).ready(function(){
	jQuery('#emailpreviewbutton').click(function(){
		var printableContent = jQuery('#email_body_input').val();
		var windowFeatures = "menubar=yes,scrollbars=yes";
		var printWindow = window.open('','',windowFeatures);
		printWindow.document.write('<html><head><title>Email Preview</title></head><body>');
		// printWindow.document.createElement(printableContent);
		printWindow.document.write(printableContent);
		printWindow.document.write('</body></html>');
		printWindow.document.close();
	});
});
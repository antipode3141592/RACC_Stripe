function sortJson(a,b){
	return a.organization.toLowerCase() > b.organization.toLowerCase() ? 1 : -1;
};

jQuery(document).ready(function(){
	let dropdown = jQuery('#orginput');
	dropdown.empty();

	jQuery.getJSON(jsonlocation.base_url + "/data/orglist.json", function(data) {
		var data = jQuery(data).sort(sortJson);
		jQuery.each(data, function(key,entry) {
			dropdown.append(jQuery('<option></option>').attr('value',entry.address).text(entry.organization).attr('label',entry.organization));
		});
	});
});

jQuery(document).ready(function($) {
	$("#form-selector").submit(function(event){
		var x =  document.getElementById("orginput");
		console.log(x.value);
		document.location.assign(x.value);
		return false;
	});
});
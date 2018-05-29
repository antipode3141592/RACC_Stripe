function sortJson(a,b){
	return a.organization.toLowerCase() > b.organization.toLowerCase() ? 1 : -1;
};

jQuery(document).ready(function(){
	let dropdown = jQuery('#orginput');
	dropdown.empty();

	jQuery.getJSON(urls.base_url + "/data/orglist.json", function(data) {
		var data = jQuery(data).sort(sortJson);
		jQuery.each(data, function(key,entry) {
			dropdown.append(jQuery('<option></option>').attr('value',entry.organization).text(entry.organization).attr('label',entry.organization));
		});
	});
});

jQuery(document).ready(function($) {
	$('#form-selector').submit(function(event){
		jQuery.getJSON(urls.base_url + "/data/orglist.json", function(data) {
			var select_input = jQuery('#orginput').prop('selected');
			jQuery.each(data, function(key,entry) {
				// console.log(select_input);
				if (select_input == entry.organization){
					
					var org = encodeURIComponent(entry.organization);
					// org = org.replace('&','%26');
					// org = org.replace(' ', '+');
					var payperiods = entry.payperiods;
					var optionalperiods = entry.optionalperiods;
					var payroll = entry.payroll;
					var dg = entry.dg;

					var address = urls.site_url + "/springcampaign2018/?org=" + org + "&pp=" + payperiods
						+ "&op=" + optionalperiods + "&pr=" + payroll + "&dg=" + dg;
					// console.log(address);
					// console.log(address);
					document.location.assign(address);
					return false;
				}	
				// } else { 
				// 	console.log("org: " + entry.organization);
				// }
			});
		});
		// var x =  document.getElementById("orginput");
		// console.log(x.value);

	});
});
<?php

global $racc_db_version;
$racc_db_version = '2.4';	//updated 3/7/2019

function racc_db_install() {
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;
	global $racc_db_version;
	
	$charset_collate = $wpdb->get_charset_collate();	

	$sql = "CREATE TABLE racc_donors(
		id bigint(20) NOT NULL AUTO_INCREMENT,
		first_name varchar(50) NOT NULL,
		middle_name varchar(50) DEFAULT NULL,
		last_name varchar(50) NOT NULL,
		donor_id varchar(50) DEFAULT NULL,
		timestamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		anon varchar(10) NOT NULL,
		artscard int(1) DEFAULT NULL,
		giftartscard int(1) DEFAULT NULL,
		organization varchar(50) DEFAULT NULL,
		browser varchar(50) DEFAULT NULL,
		platform varchar(50) DEFAULT NULL,
		org_page varchar(50) DEFAULT NULL,
		gift_to_donor_id varchar(50) DEFAULT NULL,
  		PRIMARY KEY  (id)
		) $charset_collate;";
	$results = dbDelta($sql);
	// if ($results){
	// 	foreach ($results as $value) {
	// 		// error_log($value);
	// 	}
	// }

	$sql = "CREATE TABLE racc_addresses(
		type varchar(20) NOT NULL,
		donor_id varchar(50) NOT NULL,
		address1 varchar(50) NOT NULL,
		address2 varchar(50) DEFAULT NULL,
		city varchar(50) NOT NULL,
		state varchar(2) NOT NULL,
		zipcode int(9) NOT NULL,
		id bigint(20) NOT NULL AUTO_INCREMENT,
		timestamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		name varchar(100),
		PRIMARY KEY  (id)
		) $charset_collate;";
	$results = dbDelta($sql);
	// if ($results){
	// 	foreach ($results as $value) {
	// 		// error_log($value);
	// 	}
	// }

	//// pattern for adding new tables
	//// note that there MUST be two spaces between PRIMARY KEY and (id)
	////   NOT "PRIMARY KEY (id)" but "PRIMARY KEY  (id)", this is a quirk of dbDeltas parser
	// $sql = "[query here]";
	// $results = dbDelta($sql);
	// if ($results){
	// 	foreach ($results as $value) {
	// 		error_log($value);
	// 	}
	// }

	//table for storing email templates
	$sql = "CREATE TABLE racc_email_templates (
		id int(11) NOT NULL AUTO_INCREMENT,
		template_type varchar(50) NOT NULL,
		email_subject varchar(100) NOT NULL,
		email_body_id int(11) NOT NULL,
		response_type varchar(50) NOT NULL,
		PRIMARY KEY  (id)
		) $charset_collate;";
	$results = dbDelta($sql);

	$sql ="CREATE TABLE racc_email_body (
		id int(11) NOT NULL AUTO_INCREMENT,
		email_body text NOT NULL,
		PRIMARY KEY  (id)
		) $charset_collate";
	$results = dbDelta($sql);
	// if ($results){
	// 	foreach ($results as $value) {
	// 		error_log($value);
	// 	}
	// }


	$sql = "CREATE TABLE racc_donor_comments (
  		id int(11) NOT NULL AUTO_INCREMENT,
  		donor_id varchar(50) NOT NULL,
  		comment varchar(500) DEFAULT NULL,
  		PRIMARY KEY  (id)
		) $charset_collate;";
	$results = dbDelta($sql);
	// if ($results){
	// 	foreach ($results as $value) {
	// 		error_log($value);
	// 	}
	// }


	$sql = "CREATE TABLE racc_donor_allocations(
		id bigint(20) NOT NULL AUTO_INCREMENT,
		fund_name varchar(100) NOT NULL,
		fund_amount decimal(10,2) NOT NULL,
		donor_id varchar(50) NOT NULL,
		timestamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		pledgetotal decimal(10,2) NOT NULL,
		paytype varchar(10) NOT NULL,
		periodtotal decimal(10,2) NOT NULL,
		period_count int(4) NOT NULL,
		fund_type varchar(10) DEFAULT NULL,
  		PRIMARY KEY  (id)
		) $charset_collate;";
	$results = dbDelta($sql);
	// if ($results){
	// 	foreach ($results as $value) {
	// 		error_log($value);
	// 	}
	// }

	$sql = "CREATE TABLE racc_contact(
		timestamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		id bigint(20) NOT NULL AUTO_INCREMENT,
		donor_id varchar(50) NOT NULL,
		type varchar(50) NOT NULL,
		address varchar(50) NOT NULL,
  		PRIMARY KEY  (id)
		) $charset_collate;";
	$results = dbDelta($sql);
	// if ($results){
	// 	foreach ($results as $value) {
	// 		error_log($value);
	// 	}
	// }

	$rows_inserted = $wpdb->query("CREATE PROCEDURE sp_getdonordata(IN StartDate DATETIME, IN EndDate DATETIME) select a.timestamp, a.donor_id, a.first_name, a.last_name, a.anon, a.artscard, a.giftartscard, a.organization, b.address as 'email', j.address as 'phone', d.fund_amount as 'community_fund', e.fund_amount as 'education_fund', g.fund_amount as 'designated_fund', g.fund_name as 'designated_name',d.pledgetotal, d.paytype, d.periodtotal, d.period_count, c.address1, c.address2, c.city, c.state, c.zipcode, f.name as 'artscard_name', f.address1 as 'artscard_add1', f.address2 as 'artscard_add2', f.city as 'artscard_city', f.state as 'artscard_state', f.zipcode as 'artscard_zip', k.address as 'artscard_email', h.comment, a.browser, a.platform from racc_donors as a left join racc_contact as b on (a.donor_id = b.donor_id AND b.type = 'email') left join racc_contact as j on (a.donor_id = j.donor_id AND j.type = 'phone') left join racc_contact as k on (k.donor_id = a.donor_id and k.type = 'email-artscard') left join racc_addresses as c on a.donor_id = c.donor_id left join racc_donor_allocations as d on (a.donor_id = d.donor_id AND d.fund_type = 'community') left join racc_donor_allocations as e on (a.donor_id = e.donor_id AND e.fund_type = 'education') left join racc_donor_allocations as g on (a.donor_id = g.donor_id AND g.fund_type = 'designated') left join racc_addresses as f on (a.donor_id = f.donor_id AND f.type='Arts Card') left join racc_donor_comments as h on a.donor_id = h.donor_id WHERE a.timestamp BETWEEN StartDate AND EndDate GROUP by a.donor_id");
	// error_log("create stored procedure sp_getdonordata(): " + $rows_inserted);

	$rows_inserted = $wpdb->query("CREATE PROCEDURE sp_getemaildata(IN DonorID VARCHAR(50)) select a.first_name, a.last_name, a.anon, a.artscard, a.giftartscard, b.address as 'email', d.fund_amount as 'community', e.fund_amount as 'education', d.pledgetotal, d.paytype, d.periodtotal, d.period_count from racc_donors as a left join racc_contact as b on a.donor_id = b.donor_id left join racc_donor_allocations as d on a.donor_id = d.donor_id left join racc_donor_allocations as e on a.donor_id = e.donor_id where a.donor_id = DonorID AND b.type ='email' AND d.fund_name ='community' AND e.fund_name='education'");
	// error_log("create stored procedure sp_getemaildata(): " + $rows_inserted);

	$rows_inserted = $wpdb->query("CREATE PROCEDURE sp_getsingledonordata(IN DonorID VARCHAR(50)) select a.timestamp, a.donor_id, a.first_name, a.last_name, a.anon, a.artscard, a.giftartscard, a.organization, b.address as 'email', d.fund_amount as 'community_fund', e.fund_amount as 'education_fund', g.fund_amount as 'designated_fund', g.fund_name as 'designated_name',d.pledgetotal, d.paytype, d.periodtotal, d.period_count, c.address1, c.address2, c.city, c.state, c.zipcode, f.name as 'artscard_name', f.address1 as 'artscard_add1', f.address2 as 'artscard_add2', f.city as 'artscard_city', f.state as 'artscard_state', f.zipcode as 'artscard_zip', a.browser, a.platform from racc_donors as a left join racc_contact as b on (a.donor_id = b.donor_id AND b.type = 'email') left join racc_addresses as c on a.donor_id = c.donor_id left join racc_donor_allocations as d on (a.donor_id = d.donor_id AND d.fund_type = 'community') left join racc_donor_allocations as e on (a.donor_id = e.donor_id AND e.fund_type = 'education') left join racc_donor_allocations as g on (a.donor_id = g.donor_id AND g.fund_type = 'designated') left join racc_addresses as f on (a.donor_id = f.donor_id AND f.type='Arts Card') WHERE a.donor_id = DonorID");
	// error_log("create stored procedure sp_getsingledonordata(): " + $rows_inserted);

	$rows_inserted = $wpdb->query("CREATE PROCEDURE sp_getcomment(IN DonorID VARCHAR(50)) select donor_id, comment from racc_donor_comments where donor_id = DonorID");
	// error_log("create stored procedure sp_getcomment(): " + $rows_inserted);

	$rows_inserted = $wpdb->query("CREATE PROCEDURE sp_getartscardaddress(IN DonorID VARCHAR(50)) select a.type, a.donor_id, a.address1, a.address2, a.city, a.state, a.zipcode, a.name from racc_addresses as a where a.donor_id = DonorID AND a.type='Arts Card'");
	// error_log("create stored procedure sp_getartscardaddress(): " + $rows_inserted);

	$rows_inserted = $wpdb->query("CREATE PROCEDURE sp_adddonor(IN FirstName VARCHAR(50), IN MiddleInitial VARCHAR(50), IN LastName VARCHAR(50), IN DonorID VARCHAR(50), IN Anon VARCHAR(10), IN ArtsCard INT(1), IN GiftArtsCard INT(1), IN Org VARCHAR(50), IN browser VARCHAR(50), IN platform VARCHAR(50), IN UserOrg VARCHAR(50), IN gift_to_id VARCHAR(50)) INSERT into racc_donors (id,first_name, middle_name, last_name, donor_id,anon,artscard,giftartscard,organization,browser,platform, org_page,gift_to_donor_id) values (null,FirstName, MiddleInitial, LastName, DonorID,Anon,ArtsCard,GiftArtsCard,Org,browser,platform,UserOrg,gift_to_id)");
	// error_log("create stored procedure sp_adddonor(): " + $rows_inserted);

	$rows_inserted = $wpdb->query("CREATE PROCEDURE sp_addcontactinfo(IN Type VARCHAR(50), IN Address VARCHAR(50), IN DonorID VARCHAR(50)) INSERT INTO racc_contact(id,address, donor_id, type) values (null,Address, DonorID, Type)");
	// error_log("create stored procedure sp_addcontactinfo(): " + $rows_inserted);

	$rows_inserted = $wpdb->query("CREATE PROCEDURE sp_addcomment(IN DonorID VARCHAR(50), IN Comment VARCHAR(500)) INSERT INTO racc_donor_comments(id, donor_id, comment) VALUES (null, DonorID, Comment)");
	// error_log("create stored procedure sp_addcomment(): " + $rows_inserted);
// 
	$rows_inserted = $wpdb->query("CREATE PROCEDURE sp_addallocation(IN DonorID VARCHAR(50), IN FundName VARCHAR(100), IN FundAmount DECIMAL(10,2) UNSIGNED, IN PledgeTotal DECIMAL(10,2) UNSIGNED, IN PayType VARCHAR(10), IN PeriodTotal DECIMAL(10,2) UNSIGNED, IN PeriodCount INT(4), IN FundType VARCHAR(10)) INSERT into racc_donor_allocations(donor_id, fund_name, fund_amount,pledgetotal,paytype,periodtotal,period_count,fund_type) values(DonorID, FundName, FundAmount, PledgeTotal,PayType,PeriodTotal,PeriodCount,FundType)");
	// error_log("create stored procedure sp_addallocation(): " + $rows_inserted);

	$rows_inserted = $wpdb->query("CREATE PROCEDURE sp_addaddress(IN Address1 VARCHAR(50), IN Address2 VARCHAR(50), IN City VARCHAR(50), IN State VARCHAR(2), IN Zip INT(9) UNSIGNED, IN Type VARCHAR(50), IN DonorID VARCHAR(50), IN Name VARCHAR(100)) INSERT into racc_addresses(address1, address2, city, state, zipcode, type, donor_id,name) VALUES(Address1, Address2, City, State, Zip, Type, DonorID,Name)");
	// error_log("create stored procedure sp_addaddress(): " + $rows_inserted);

	$rows_inserted = $wpdb->query("CREATE PROCEDURE sp_getresultsdata(IN DonorID VARCHAR(50)) select a.timestamp, a.donor_id, a.first_name, a.last_name, a.anon, a.artscard, a.giftartscard, a.organization, b.address as 'email', d.fund_amount as 'community_fund', e.fund_amount as 'education_fund', g.fund_amount as 'designated_fund', g.fund_name as 'designated_name',k.pledgetotal, k.paytype, k.periodtotal, k.period_count, c.address1, c.address2, c.city, c.state, c.zipcode, h.first_name as 'artscard_first_name', h.last_name as 'artscard_last_name', j.address as 'artscard_email', f.address1 as 'artscard_add1', f.address2 as 'artscard_add2', f.city as 'artscard_city', f.state as 'artscard_state', f.zipcode as 'artscard_zip', a.browser, a.platform from racc_donors as a left join racc_contact as b on (a.donor_id = b.donor_id AND b.type = 'email') left join racc_addresses as c on a.donor_id = c.donor_id left join racc_donor_allocations as d on (a.donor_id = d.donor_id AND d.fund_type = 'community') left join racc_donor_allocations as e on (a.donor_id = e.donor_id AND e.fund_type = 'education') left join racc_donor_allocations as g on (a.donor_id = g.donor_id AND g.fund_type = 'designated') left join racc_donors as h on (h.donor_id = a.gift_to_donor_id) left join racc_addresses as f on (a.gift_to_donor_id = f.donor_id) left join racc_contact as j on (j.donor_id = a.gift_to_donor_id and j.type = 'email') left join racc_donor_allocations as k on a.donor_id = k.donor_id WHERE a.donor_id = DonorID");

	$rows_inserted = $wpdb->query("CREATE PROCEDURE sp_getdonors(IN startdate DATETIME, IN enddate DATETIME) select a.timestamp, a.donor_id, a.first_name, a.last_name, a.anon, a.artscard, a.giftartscard, a.organization, a.org_page, b.address as 'email', j.address as 'phone', d.fund_amount, d.fund_name ,d.pledgetotal, d.paytype, d.periodtotal, d.period_count, c.address1, c.address2, c.city, c.state, c.zipcode, f.name as 'artscard_name', f.address1 as 'artscard_add1', f.address2 as 'artscard_add2', f.city as 'artscard_city', f.state as 'artscard_state', f.zipcode as 'artscard_zip', k.address as 'artscard_email', h.comment, a.browser, a.platform from racc_donors as a left join racc_contact as b on (a.donor_id = b.donor_id AND b.type = 'email') left join racc_contact as j on (a.donor_id = j.donor_id AND j.type = 'phone') left join racc_contact as k on (k.donor_id = a.donor_id and k.type = 'email-artscard') left join racc_addresses as c on a.donor_id = c.donor_id left join racc_donor_allocations as d on (a.donor_id = d.donor_id) left join racc_addresses as f on (a.donor_id = f.donor_id AND f.type='Arts Card') left join racc_donor_comments as h on a.donor_id = h.donor_id WHERE a.timestamp BETWEEN StartDate AND EndDate ORDER by a.donor_id");

	$rows_inserted = $wpdb->query("CREATE PROCEDURE sp_getemailtemplate(IN Response VARCHAR(50)) SELECT a.email_subject, b.email_body FROM racc_email_templates as a join racc_email_body as b on a.email_body_id = b.id  WHERE a.response_type=Response");

	$rows_inserted = $wpdb->query("CREATE PROCEDURE sp_getemailtemplates(IN TemplateType VARCHAR(50)) SELECT a.id, a.template_type, a.email_subject, a.response_type, a.email_body_id, b.email_body FROM racc_email_templates as a join racc_email_body as b on a.email_body_id = b.id WHERE a.template_type=TemplateType");

	$rows_inserted = $wpdb->query("CREATE PROCEDURE sp_updateemailbody(IN BodyText TEXT, IN BodyID INT(11)) UPDATE racc_email_body SET email_body=BodyText WHERE id=BodyID");

	$rows_inserted = $wpdb->query("CREATE PROCEDURE sp_updateemailsubject(IN SubjectText VARCHAR(100), IN TemplateType VARCHAR(50), IN TemplateID INT(11)) UPDATE racc_email_templates SET email_subject=SubjectText WHERE template_type=TemplateType AND id=TemplateID");

	update_option( 'racc_db_version', $racc_db_version );
}

function racc_update_db_check(){
	global $racc_db_version;
	if (get_site_option('racc_db_version') < $racc_db_version){
		error_log("Installing.... code DB Version = " . $racc_db_version . "; site DB version = " . get_site_option('racc_db_version'));
		racc_db_install();
	}else{
		error_log("skipping db updates, installed RACC DB Version: " . get_site_option('racc_db_version'));
	}
}
add_action('plugins_loaded', 'racc_update_db_check');


//register database config functions
register_activation_hook( __FILE__, 'racc_db_install' );
<?php 
require_once("phpwhois-4.2.2/whois.main.php");
require_once("phpwhois-4.2.2/whois.utils.php");
require_once("registered-domains-php-master/effectiveTLDs.inc.php");
require_once("registered-domains-php-master/regDomain.inc.php");
getwhois("http://www.cd.rd.frontcoding.com");

 function getwhois($query){

 	if(!is_numeric($query[0])){
	 	$query = getRegisteredDomain(parse_url($query, PHP_URL_HOST));
	 }
	echo $query;	
 	
	$whois = new Whois();

	// if(!$whois->ValidDomain($query)){
	// return 'Sorry, the domain is not valid or not supported.';
	// }
	$insert_array = array();
	$to_insert = "";
	$values = "registrar, whois_server, referral_url, name_server, status, updated_date, creation_data, expiration_date, administrative_contact, technical_contact";
	$result = $whois->Lookup($query);
	$regyinfo = $result['regyinfo'];
	$regrinfo = $result['regrinfo'];
	$domain = $regrinfo['domain'];

	$insert_array['whois_server'] = $regyinfo['referrer'];
	$insert_array['registrar'] = $regyinfo['registrar'];
	$insert_array['referral_url'] = $regyinfo['referrer'];
	$insert_array['host_name'] = $domain['name'];
	$insert_array['name_server'] = "";

	foreach ($domain['nserver'] as $server => $address) {
		$insert_array['name_server'] .= $server . ": " . $address . ", ";
	}
	$insert_array['status'] = "";

	foreach ($domain['status'] as $status) {
		$insert_array['status'] .= $status. ", ";
	}

	$insert_array['updated_date'] = $domain['changed'];
	$insert_array['creation_date'] = $domain['created'];
	$insert_array['expiration_date'] = $domain['expires'];

	//CONTACT INFORMATION (may not exist)
	// organization-> organization name
	// name-> organization responsible
	// type-> type of contact
	// address-> array containing the address, the
	// 		   keys of that array could be just
	// 		   numbers, could have predefined
	// 		   subkeys or could be amix of numbers
	// 		   and predefined subkeys. Predefined
	// 		   subkeys are street, city,
	// 		   state, pcode and country
	// phone-> phone, could also be an array of
	// 		   phone numers
	// fax-> fax, same behaviour as phone
	// email-> email, same behaviour as phone
	$admin = $regrinfo['admin'];
	$tech = $regrinfo['tech'];

	$insert_array['administrative_contact'] = "";
	$insert_array['administrative_contact'] .=  " " . $admin['organization'];
	$insert_array['administrative_contact'] .=  " " . $admin['name'];
	$insert_array['administrative_contact'] .=  " " . $admin['type'];
	foreach ($admin['address'] as $info) {
		$insert_array['administrative_contact'] .=  " " . $info;
	}
	$insert_array['administrative_contact'] .=  " " . $admin['phone'];
	$insert_array['administrative_contact'] .=  " " . $admin['fax'];
	$insert_array['administrative_contact'] .=  " " . $admin['email'];


	$insert_array['technical_contact'] = "";
	$insert_array['technical_contact'] .= " " .  $tech['organization'];
	$insert_array['technical_contact'] .=  " " . $tech['name'];
	$insert_array['technical_contact'] .=  " " . $tech['type'];
	foreach ($tech['address'] as $info) {
		$insert_array['technical_contact'] .=  " " . $info;
	}
	$insert_array['technical_contact'] .= " " .  $tech['phone'];
	$insert_array['technical_contact'] .=  " " . $tech['fax'];
	$insert_array['technical_contact'] .=  " " . $tech['email'];
	print_r($insert_array['status']);
	save_who_is($insert_array);



}
 function save_who_is($who_is_data){
		$columns = "registrar, whois_server, referral_url, name_server, status, updated_date, creation_date, expiration_date, administrative_contact, technical_contact";
		$sql = "INSERT INTO web_host (" . $columns . ")";
		$sql_values = " VALUES (";
		
		$columns_array = explode(", ", $columns);
		foreach ($columns_array as $column) {
			$sql_values.= $who_is_data[$column] .", ";
		}
		$sql_values.=");";
		$sql .= $sql_values;
		echo $sql;
	}


?>
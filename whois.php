<?php 

getwhois("frontcoding.com");

function getwhois($query)
{
require_once("phpwhois-4.2.2/whois.main.php");
require_once("phpwhois-4.2.2/whois.utils.php");

$whois = new Whois();

// if(!$whois->ValidDomain($query)){
// return 'Sorry, the domain is not valid or not supported.';
// }

$result = $whois->Lookup($query);
$regyinfo = $result['regyinfo'];
$registrar = $regyinfo['registrar'];
$referralurl = $regyinfo['referrer'];
$regrinfo = $result['regrinfo'];
$domain = $regrinfo['domain'];
$name = $domain['name'];
$servers = $domain['nserver'];
$status = $domain['status'];
$changed = $domain['changed'];
$created = $domain['created'];
$expires = $domain['expires'];

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
$tech =  $regrinfo['tech'];
$organization = $admin['organization'];
$name = $admin['name'];
$type = $admin['type'];
$address = $admin['address'];
$phone = $admin['phone'];
$fax = $admin['fax'];
$email = $admin['email'];

print_r($email);
print_r($registrar);




print_r($result);

}
?>
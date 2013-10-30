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
$basic = array_shift($result);

$registrar = $basic['registrar'];
$name = $basic['name'];
$whoisserver = $basic['server'];
print_r($result);

}
?>
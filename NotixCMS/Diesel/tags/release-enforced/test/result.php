<?php
// your registration data
$shopId = "jako-mebel";      // your login here
$passwd = "b15Ufo74";   // merchant pass1 here

// order properties
$orderId    = 5;        // shop's invoice number
                       // (unique for shop's lifetime)
$orderDesc  = "OrderDesc";   // invoice desc
$orderSumm  = "15505.00";   // invoice summ

// build CRC value
$crc  = md5("$shopId:$orderSumm:$orderId:$passwd");

// build URL
$url = "https://test.robokassa.ru/index.aspx?MrchLogin=$shopId&OutSum=$orderSumm&InvId=$orderId&Desc=$orderDesc&SignatureValue=$crc";
/*
MrchLogin=sMerchantLogin&
OutSum=nOutSum&
InvId=nInvId&
Desc=sInvDesc&
SignatureValue=sSignatureValue
IncCurrLabel=sIncCurrLabel&
Culture=sCulture
*/
// print URL if you need
echo "<a href='$url'>Payment link</a>";

// as a part of ResultURL script

// your registration data
$mrh_pass2 = "securepass2";   // merchant pass2 here

// HTTP parameters:
$out_summ = $_REQUEST["OutSum"];
$inv_id = $_REQUEST["InvId"];
$crc = $_REQUEST["SignatureValue"];

// HTTP parameters: $out_summ, $inv_id, $crc
$crc = strtoupper($crc);   // force uppercase

// build own CRC
$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2"));

if (strtoupper($my_crc) != strtoupper($crc))
{
  echo "bad sign\n";
  exit();
}

// print OK signature
echo "OK$inv_id\n";

// perform some action (change order state to paid)
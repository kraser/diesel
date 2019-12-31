<?php
$shopLogin = "jako-mebel";
$pass1 = "Pron547fu";
$orderId = 19;
$orderDesc = "Заказ №19";
$orderSum = "10000.00";
$IsTest = 1;
$crc = md5("$shopLogin:$orderSum:$orderId:$pass1");
?>
<html>
    <title>test</title>
    <body>
        <div>Какой-то DIV</div>
        <?php
        print "<script language=JavaScript src='https://auth.robokassa.ru/Merchant/PaymentForm/FormMS.js?MerchantLogin=$shopLogin&OutSum=$orderSum&InvoiceID=$orderId&Description=$orderDesc&SignatureValue=$crc&IsTest=$IsTest'></script>";
        ?>
        <div>Друной DIV</div>
    </body>
</html>

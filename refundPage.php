<?php 

$ERPid= $_GET['ERPid'];
$TransactionID= $_GET['TransactionID'];
$ClientToken= $_GET['ClientToken'];
$Amount= $_GET['Amount'];

?>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">	
	<title>Refund Detail</title>
</head>
<body>
	<h1>Details</h1>
	<form action="http://pri.paneco.com/refund/refundEILAT.php" method="get">
	<label>Transaction Id</label>
	<input type="text" name="txn_id" value="<?php echo $TransactionID; ?>" />
	<input type="text" name="amnt" value="<?php echo $Amount; ?>" />
	
	
	<p>If you want refund then click on REFUND button</p>
	
	<input type="submit" value="Refund Payment" name="refund">
	</form>
</body>
</html>
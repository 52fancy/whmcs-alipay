<?php
// Require libraries needed for gateway module functions.
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';
require_once __DIR__ . '/../../../modules/gateways/class/alipay/alipay.class.php';

use Illuminate\Database\Capsule\Manager as Capsule;
function convert_helper($invoiceid,$amount){
    $setting = Capsule::table("tblpaymentgateways")->where("gateway","alipay")->where("setting","convertto")->first();
    ///系统没多货币 , 直接返回
    if (empty($setting)){ return $amount; }
    
    
    ///获取用户ID 和 用户使用的货币ID
    $data = Capsule::table("tblinvoices")->where("id",$invoiceid)->get()[0];
    $userid = $data->userid;
    $currency = getCurrency( $userid );

    /// 返回转换后的
    return  convertCurrency( $amount , $setting->value  ,$currency["id"] );
}

// Detect module name from filename.
$gatewayModuleName = basename(__FILE__, '.php');

// Fetch gateway configuration parameters.
$gatewayParams = getGatewayVariables($gatewayModuleName);

// Die if module is not active.
if(!$gatewayParams['type']) {
    die("Module Not Activated");
}

$alipayPublicKey = $gatewayParams['alipay_key'];
$aliPay = new RsaService($alipayPublicKey);

if($_GET['sign_type']){ //同步回调通知
	//验证签名
	$result = $aliPay->rsaCheck($_GET,$_GET['sign_type']);
	if($result == "success"){
		echo '<script>alert("付款成功！");window.location.href = "' . $gatewayParams['systemurl'] . '/cart.php?a=complete' . '";</script>';exit;
	}
	echo '<script>alert("付款失败，请重试！");history.back(-1);</script>';exit;
}
if($_POST['sign_type']){//异步回调通知
	//验证签名
	$result = $aliPay->rsaCheck($_POST,$_POST['sign_type']);
	if($result == "success"){
		$out_trade_no = $_POST['out_trade_no'];  //商户网站唯一订单号
		$trade_no = $_POST['trade_no'];    //支付宝交易号
		$trade_status = $_POST['trade_status']; //交易状态
		$amount = $_POST['total_amount']; //交易金额
		$invoice_id = explode("-",$out_trade_no)[1];
		if($trade_status == 'TRADE_SUCCESS' or $trade_status == 'TRADE_FINISHED')
		{
            $invoiceid = checkCbInvoiceID($invoice_id,$gatewayParams["name"]);
            $amount = convert_helper( $invoice_id, $amount );
            checkCbTransID($trade_no);
            addInvoicePayment($invoiceid,$trade_no,$amount,"0",$gatewayModuleName);
            logTransaction($gatewayParams['name'], $_POST, "异步回调入账 #" . $invoiceid);
            exit("success");
		}
	}else{
		exit("Verify Signature Failure");
	}
}
echo '<script>alert("老铁，干哈呢？");history.back(-1);</script>';exit;
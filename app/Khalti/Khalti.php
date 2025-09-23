<?php
namespace App\Khalti;

class Khalti {

	public function verifyPayment($secret, $token, $amount) {

		$config = http_build_query(array(
		    'token' => $token,
		    'amount'  => $amount,
		));

		$url = "https://khalti.com/api/v2/payment/verify/";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$config);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$headers = ['Authorization: Key '.$secret];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		$response = json_encode(array('status_code'=>$status_code,'data'=>json_decode($response)));
		return json_decode($response, true);
	}

	public function listTransactions($secret) {
		$url = "https://khalti.com/api/v2/merchant-transaction/";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$headers = ['Authorization: Key '.$secret];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return json_decode($response,true);
	}

	public function getTransaction($secret, $idx) {

		$url = "https://khalti.com/api/v2/merchant-transaction/".$idx."/";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$headers = ['Authorization: Key '.$secret];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return json_decode($response, true);
	}

	public function transactionStatus($secret,$token,$amount) {
		$config = http_build_query(array(
		    'token' => $token,
		    'amount'  => $amount,
		));

		$url = "https://khalti.com/api/v2/payment/status/";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url.'?'.$config);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$headers = ['Authorization: Key '.$secret];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return json_decode($response, true);
	}

}

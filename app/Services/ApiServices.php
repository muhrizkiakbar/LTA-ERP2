<?php

namespace App\Services;

use App\Models\BranchSap;
use App\Models\MixHeader;
use App\Models\Warehouse;

class ApiServices
{
	public function callApiLogin($body, $url)
	{
		$post = json_encode($body);

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function callApiBPL()
	{
		$url = 'https://saplta.laut-timur.tech/api/showBPL';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function getCustomer($post)
	{
		$url = 'https://saplta.laut-timur.tech/api/getCustomer';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function getCustomerId($post)
	{
		$url = 'https://saplta.laut-timur.tech/api/getCustomerId2';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function getItem($post)
	{
		$url = 'https://saplta.laut-timur.tech/api/getItemName';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function getItemId($post)
	{
		$url = 'https://saplta.laut-timur.tech/api/getItemId';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function getOrderDetail($post)
	{
		// $url = 'https://web.erplta.com/api/syncMix';
		// $url = 'http://sfa.laut-timur.com/sfa-erp-mix/api/order_detail_branch';
		$url = 'https://mix.laut-timur.com/sfa-sync-mix/api/order_detail';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function getOrderDetailLines($post)
	{
		// $url = 'https://web.erplta.com/api/syncMixLines';
		$url = 'http://sfa.laut-timur.com/sfa-erp-mix/api/order_detail_lines';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function cekOrderDetail($id)
	{
		$cek = MixHeader::where('NumAtCard', $id)->get();
		return $cek;
	}

	public function getUomDetail($post)
	{
		$url = 'https://saplta.laut-timur.tech/api/getItemUomDetail';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function getWareHouse($branch)
	{
		return BranchSap::find($branch);
	}

	public function getSalesDetail($post)
	{
		$url = 'https://saplta.laut-timur.tech/api/getSalesDetail';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function getUomEntry($post)
	{
		$url = 'https://saplta.laut-timur.tech/api/getUomEntry';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function getSalesId($headers, $url)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_POST => 1,
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_HTTPHEADER => $headers
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function getInvoiceDetailMix($post)
	{
		$url = 'https://saplta.laut-timur.tech/api/getInvoiceMix';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function getInvoiceDetail($post)
	{
		$url = 'https://saplta.laut-timur.tech/api/generateReportOmset';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function getInvoiceDetailReturn($post)
	{
		$url = 'https://saplta.laut-timur.tech/api/generateReportOmsetReturn';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function getBranchDetail($branch)
	{
		return BranchSap::find($branch);
	}

	public function getSales($post)
	{
		$url = 'https://saplta.laut-timur.tech/api/getSales';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function getCardCode($post)
	{
		$url = 'https://saplta.laut-timur.tech/api/getCardCode';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function getAvailable($post)
	{
		$url = 'https://saplta.laut-timur.tech/api/getAvailable';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function generateInvoiceFP($company, $post)
	{
		if ($company == 1) {
			$url = 'https://saplta.laut-timur.tech/api/getInvoiceFP';
		} else {
			$url = 'https://saptaa.laut-timur.tech/api/getInvoiceFP';
		}

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function updateInvoiceFP($company, $post)
	{
		if ($company == 1) {
			$url = 'https://saplta.laut-timur.tech/api/updateInvoiceFP';
		} else {
			$url = 'https://saptaa.laut-timur.tech/api/updateInvoiceFP';
		}
		// $url = 'http://localhost/sap-api-lta/api/updateInvoiceFP';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function generatePAFile($post)
	{
		$url = 'https://saplta.laut-timur.tech/api/generatePAFile';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function getDiskonSfa($itemCode, $cust, $qty)
	{
		$post_customer = [
			'CardCode' => $cust
		];
		$customer = getCustomerId(json_encode($post_customer));

		// dd($customer);

		$post_item = [
			'ItemCode' => $itemCode
		];
		$item = getItemId(json_encode($post_item));

		$cek = [
			'ItemCode' => $itemCode,
			'CardCode' => $cust,
			'Date' => date('Y-m-d'),
			'Qty' => $qty,
			'SUBSEGMENT' => $customer['cseg4'],
			'INITIATIVE1' => $item['INIT1'],
			'INITIATIVE2' => $item['INIT2'],
			'INITIATIVE3' => $item['INIT3'],
			'INITIATIVE4' => $item['INIT4'],
			'INITIATIVE5' => $item['INIT5'],
			'INITIATIVE6' => $item['INIT6'],
			'INITIATIVE7' => $item['INIT7']
		];

		$diskon = $this->getDiskonSfa2(json_encode($cek));

		// dd($diskon);

		$disc1 = $customer['stat_disc1'] == "Y" ? $diskon['disc1'] : 0;
		$disc2 = $customer['stat_disc2'] == "Y" ? $diskon['disc2'] : 0;
		$disc3 = $customer['stat_disc3'] == "Y" ? $diskon['disc3'] : 0;
		$disc4 = $customer['stat_disc4'] == "Y" ? $diskon['disc4'] : 0;
		$disc5 = $customer['stat_disc5'] == "Y" ? $diskon['disc5'] : 0;
		$disc6 = $customer['stat_disc6'] == "Y" ? $diskon['disc6'] : 0;
		$disc7 = $customer['stat_disc7'] == "Y" ? $diskon['disc7'] : 0;
		$disc8 = $customer['stat_disc8'] == "Y" ? $diskon['disc8'] : 0;

		$data = [
			'disc1' => $disc1,
			'disc2' => $disc2,
			'disc3' => $disc3,
			'disc4' => $disc4,
			'disc5' => $disc5,
			'disc6' => $disc6,
			'disc7' => $disc7,
			'disc8' => $disc8,
		];

		return $data;
	}

	public function getDiskonSfa2($post)
	{
		$url = 'https://saplta.laut-timur.tech/api/getDiskonSfa';
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url, // your preferred link
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => array(
				// Set here requred headers
				"accept: */*",
				"accept-language: en-US,en;q=0.8",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		// $data = [];

		if ($err) {
			$data = [];
		} else {
			$data = json_decode($response, TRUE);
		}

		return $data;
	}

	public function stokItem($post)
	{ {
			$url = 'https://saplta.laut-timur.tech/api/getAvailable';
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $url, // your preferred link
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_TIMEOUT => 30000,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_POSTFIELDS => $post,
				CURLOPT_HTTPHEADER => array(
					// Set here requred headers
					"accept: */*",
					"accept-language: en-US,en;q=0.8",
					"content-type: application/json",
				),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);

			// $data = [];

			if ($err) {
				$data = [];
			} else {
				$data = json_decode($response, TRUE);
			}

			return $data;
		}
	}

	public function stokItemTaa($cardCode, $itemCode)
	{
		$post_customer = [
			'CardCode' => $cardCode
		];

		$function = 'getCustomerId';
		$function2 = 'getAvailable';

		$customer = callSapApiTaaWithPost($function, json_encode($post_customer));

		$warehouse = Warehouse::where('code', $customer['U_CLASS'])->first();

		$post_available = [
			'ItemCode' => $itemCode,
			'WhsCode'  => $warehouse->title == 'BRBWHFB' ? 'BRBHWFB' : $warehouse->title
		];

		$json = callSapApiTaaWithPost($function2, json_encode($post_available));

		return $json;
	}
}

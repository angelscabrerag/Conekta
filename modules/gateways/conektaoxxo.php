<?php

// Copyright (c) 2013, Carlos Cesar Peña Gomez <CarlosCesar110988@gmail.com>
//
// Permission to use, copy, modify, and/or distribute this software for any
// purpose with or without fee is hereby granted, provided that the above copyright
// notice and this permission notice appear in all copies.

// THE SOFTWARE IS PROVIDED 'AS IS' AND THE AUTHOR DISCLAIMS ALL
// WARRANTIES WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED
// WARRANTIES OF MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL
// THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL
// DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS OF USE, DATA
// OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
// OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE
// USE OR PERFORMANCE OF THIS SOFTWARE.

function conektaoxxo_config() {
    $configarray = array(
		'FriendlyName' => array(
			'Type' =>'System',
			'Value' =>'Conekta Oxxo Pay'
		),
		'private_key' => array(
			'FriendlyName' => 'Llave Privada',
			'Type' => 'text',
			'Size' => '50'
		),
		'days_expire' => array(
			'FriendlyName' => 'Dias para Expirar',
			'Type' => 'text',
			'Size' => '2',
		),
		'instructions' => array(
			'FriendlyName' => 'Instrucciones de pago',
			'Type' => 'textarea',
			'Rows' => '5',
			'Description' => ''
		)
    );
	return $configarray;
}

function conektaoxxo_link($params) {

	# Variables de Conekta
	$private_key 	= $params['private_key'];

	# Dias para que expire el Pago
	$days_expire 	= $params['days_expire'];

	# Si el parametro viene vacio lo fijamos a 5 dias
	if(strlen($days_expire)==0){$days_expire=5;}

	# AAAA-MM-DD

	$date 		= time();
	$expires 	= date('Y-m-d', strtotime('+'. $days_expire .' day', $date));

    # Variables de la Factura
	$invoiceid 		= $params['invoiceid'];
	$amount 		= $params['amount'];
    $currency 		= $params['currency'];

    # Variables del cliente
	$firstname 		= $params['clientdetails']['firstname'];
	$lastname 		= $params['clientdetails']['lastname'];
	$email 			= $params['clientdetails']['email'];
	$address1 		= $params['clientdetails']['address1'];
	$address2 		= $params['clientdetails']['address2'];
	$city 			= $params['clientdetails']['city'];
	$state 			= $params['clientdetails']['state'];
	$postcode 		= $params['clientdetails']['postcode'];
	$country 		= $params['clientdetails']['country'];
	$phone 			= $params['clientdetails']['phonenumber'];

	$results = array();

	# Preparamos todos los parametros para enviar a Conekta.io

	$data_amount 		= str_replace('.', '', $amount);
	$data_currency 		= strtolower($currency);
	$data_description 	= 'Pago de Factura No. '.$invoiceid.' en Hosting Fácil';

	# Incluimos la libreria de Conecta 2.0

	require_once('conekta/lib_2.0/Conekta.php');

	# Creamos el Objeto de Cargo
	Conekta::setApiKey($private_key);

	# Arraglo con informacion de tarjeta
	$conekta = array(
				'description' 		=> $data_description,
				'reference_id' 		=> 'factura_'.$invoiceid,
				'amount' 			=> intval($data_amount),
				'currency' 			=> $data_currency,
				'cash' 				=> array(
												'type'			=>'oxxo',
												'expires_at'	=> $expires
									),
				'details'=> array(
									      'email'			=> $email,
									      'phone'			=> $phone,
									      'name'			=> $firstname.' '.$lastname,
									      'line_items'		=> array(
															        array(
															          'name'		=>	$data_description,
															          'sku'			=>	$invoiceid,
															          'unit_price'	=> 	intval($data_amount),
															          'description'	=>	$data_description,
															          'quantity'	=> 	1,
															          'type'		=>	'service-purchase'
															        )
									      )

									 )
				);
	try {

	  $charge = Conekta_Charge::create($conekta);

	  # Transaccion Correcta
	  $data = json_decode($charge);

	  $id_conekta		= $data->id;
	  $expiry_date 		= $data->payment_method->expires_at;
	  $barcode 			= $data->payment_method->barcode;
	  $barcode_url 		= $data->payment_method->barcode_url;

	  $ticket = 1;

	}

	catch (Exception $e)
	{
	  error_log('Error Conekta OXXO Pay: Código: ' . $e->getLine() . '. ' . $e->getMessage() . '. URL: ' . $_SERVER[HTTP_HOST] . $_SERVER['REQUEST_URI'], 1, 'contacto@multieconomico.com.mx');
	  if($e->getLine() == 35)
	  {
	  	$code =  "
	  		<div class='alert alert-danger'>
	  			Error al intentar generar la orden de pago mediante OXXO Pay debido a que la fecha limite de pago vencio. Por favor, intenta con otra forma de pago.
	  		</div>
	  	";
	  } else {
	  	$code =  "
	  		<div class='alert alert-danger'>
	  			Error al intentar generar la orden de pago mediante OXXO Pay. Por favor, intenta con otra forma de pago.
	  		</div>
	  	";
	  }

	  $ticket = 0;
	}

	if($ticket==1)
	{
		$code = '<form action="conekta_oxxo.php" method="post" target="_blank">';
		$code .= '<input type="hidden" name="barras" value="'. $barcode_url .'" />';
		$code .= '<input type="hidden" name="numero" value="'. $barcode .'" />';
		$code .= '<input type="hidden" name="expira" value="'. $expiry_date .'" />';
		$code .= '<input type="hidden" name="monto" value="'. $amount .'" />';
		$code .= '<input type="hidden" name="concepto" value="'. $data_description .'" />';
		$code .= '<input type="hidden" name="id_processor" value="'. $id_conekta .'" />';
		$code .= '<input type="hidden" name="id_invoice" value="'. $invoiceid .'" />';
		$code .= '<input class="btn btn-success btn-block" type="submit" value="Pagar ahora" />';
		$code .= '</form>';
	}

	return $code;

}

?>

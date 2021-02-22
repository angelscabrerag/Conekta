<?php
    $barras             = @$_POST['barras'];
    $numero             = @$_POST['numero'];
    $numero_split       = join("-", str_split($numero, 7));
    $expira             = @$_POST['expira'];
    $expiracion         = gmdate("d/m/Y", $expira);
    $monto              = @$_POST['monto'];
    $concepto           = @$_POST['concepto'];
    $id_processor       = @$_POST['id_processor'];
    $id_invoice         = @$_POST['id_invoice'];

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        error_log('Error Conekta OXXO Pay: Se accedio de manera directa a la pagina de generación de ficha de pago. URL: ' . $_SERVER[HTTP_HOST] . $_SERVER['REQUEST_URI'], 1, 'contacto@multieconomico.com.mx');
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Hosting Fácil - Recibo para pago en OXXO Pay</title>
        <link rel="shortcut icon" type="image/png" href="https://apps.hostingfacil.com.mx/clientes/modules/addons/favicon/img/c0b37cbc7302b093e3104a291411a82b.png"/>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <style type="text/css" media="screen">
            .opps {
                max-width: 100%;
                border-radius: 4px;
                box-sizing: border-box;
                padding: 0 45px;
                margin: 0 auto 20px;
                overflow: hidden;
                border: 1px solid #b0afb5;
                font-family: 'Open Sans', sans-serif;
                color: #4f5365;
            }

            .opps-reminder {
                position: relative;
                top: -1px;
                padding: 9px 0 10px;
                font-size: 11px;
                text-transform: uppercase;
                text-align: center;
                color: #ffffff;
                background: #000000;
            }

            .opps-info {
                margin-top: 26px;
                position: relative;
            }

            .opps-info:after {
                visibility: hidden;
                 display: block;
                 font-size: 0;
                 content: " ";
                 clear: both;
                 height: 0;

            }

            .opps-brand {
                float: left;
            }

            .opps-brand img {
                max-width: 200px;
                margin-top: 2px;
                margin-bottom: 15px;
            }

            @media print {
                .opps-brand img {
                    max-width: 200px !important;
                }
            }

            .opps-ammount {
                float: left;
            }


            .opps-reference {
                margin-top: 14px;
            }

            .opps-instructions {
                margin: 32px -45px 0;
                padding: 32px 45px 45px;
                border-top: 1px solid #b0afb5;
                background: #f8f9fa;
            }

            .opps-footnote {
                margin-top: 22px;
                padding: 22px 20 24px;
                color: #108f30;
                text-align: center;
                border: 1px solid #108f30;
                border-radius: 4px;
                background: #ffffff;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') { ?>
                    <div class="col-sm-12 col-md-6 col-md-offset-3">
                        <h3 class="text-center text-success">¡Orden de pago generada exitosamente!</h3>
                        <div class="btn-group btn-group-justified" role="group">
                            <div class="btn-group hidden-xs hidden-sm" role="group">
                                <button type="button" class="btn btn-sm btn-info" onclick="window.print()"> <i class="glyphicon glyphicon-print" aria-hidden="true"></i> Imprimir</button>
                            </div>
                            <div class="btn-group" role="group">
                                <a class="btn btn-sm btn-primary" href="https://apps.hostingfacil.com.mx/clientes/clientarea.php" role="button">Salir <i class="glyphicon glyphicon-chevron-right" aria-hidden="true"></i></a>
                            </div>
                        </div>
                        <hr>
                        <div id="capture_oxxo" class="opps">
                            <div class="opps-header">
                                <div class="opps-reminder">Ficha digital - OXXO Pay</div>
                                <div class="opps-info row">
                                    <div class="opps-brand col-sm-12 col-md-12 col-lg-6">
                                        <img src="assets/img/payment/conekta/oxxo_pay.png" width="200" height="auto" alt="OXXOPay">
                                    </div>
                                    <div class="opps-ammount col-sm-12 col-md-12 col-lg-6">
                                        <h5>Monto a pagar</h5>
                                        <h4>$<?php echo number_format($monto, 2); ?><small>MXN</small></h4>
                                        <p class="text-warning">Pagar antes del <?php echo $expiracion;?></p>
                                    </div>
                                </div>
                                <div class="opps-reference">
                                    <img src="<?php echo $barras ?>" width="auto" height="100%" alt="<?php echo $numero ?>" />
                                    <h4>Referencia <i class="glyphicon glyphicon-resize-vertical"></i></h4>
                                    <h3><?php echo $numero_split; ?></h3>
                                    <p>Factura: #<?php echo $id_invoice ?></p>
                                    <p>Concepto: <?php echo $concepto ?></p>
                                    <p>Transacción: <?php echo $id_processor ?></p>
                                </div>
                            </div>
                            <div class="opps-instructions">
                                <h5>Instrucciones</h5>
                                <ol>
                                    <li>Acude a la tienda OXXO más cercana. <a href="https://www.google.com.mx/maps/search/oxxo/" target="_blank">Encuéntrala aquí</a>.</li>
                                    <li>Indica en caja que quieres realizar un pago de <strong>OXXO Pay</strong> y proporciona el número de referencia de esta ficha para que el cajero la tecleé directamete en la pantalla de venta.</li>
                                    <li>Realiza el pago correspondiente con dinero en efectivo (OXXO cobrará una comisión adicional al momento de realizar el pago, el monto puede variar).</li>
                                    <li>Al confirmar tu pago, el cajero te entregará un comprobante impreso. <strong>En el podrás verificar que se haya realizado correctamente la transacción.</strong> Conserva el comprobante de pago para futuras referencias.</li>
                                </ol>
                                <div class="opps-footnote">Al validarse tu pago, recibirás un correo electrónico de notificación.</div>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="col-md-6 col-md-offset-3 text-center text-danger">
                        <h1>¡Ocurrio un error!</h1>
                        <p class="lead">No se pueden mostar los detalles de la orden de pago para OXXO Pay</p>
                        <a class="btn btn-danger btn-block" href="https://apps.hostingfacil.com.mx/clientes/clientarea.php">Salir <i class="glyphicon glyphicon-chevron-right" aria-hidden="true"></i></a>
                </div>
                <?php } ?>
            </div>
        </div>
        <script src="//code.jquery.com/jquery.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    </body>
</html>
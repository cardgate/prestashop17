<?php

/**
 * CardGate - Prestashop
 *
 * 2010-11-09 (LL) Version 1.00
 *   Initial release
 *   
 * 2011-04-18 (BZ) Version 1.01
 *   Fixed minor errors and spelling
 *   
 */
# CONSTANTES
# 
if ( empty( $_REQUEST['billing_option'] ) || $_REQUEST['billing_option'] == '' ) {
    exit( 'invalid request!' );
} else {
    $option = $_REQUEST['billing_option'];
}

if ( $option == 'mistercash' ) {
    $option = 'mc';
}
if ( $option == 'sofortbanking' ) {
    $option = 'directebanking';
}

require(dirname( __FILE__ ) . '/../../config/config.inc.php');
require(dirname( __FILE__ ) . $option . '/cardgate' . $option . '.php');

define( 'LOG_TRANSACTIONS', false ); // true or false, write log of response or not
define( 'CARDGATE_LOG', dirname( __FILE__ ) . '/cardgate' . $option . '.log' ); // File for testing response answer
define( 'EMAIL_NOTIFICATION_ADDRESS', '' ); // Email
// add Link Object to Context if is does not exist yet
// fixes bug when member function getPageLink() is called

if ( !is_object( Context::getContext()->link ) ) {
    Context::getContext()->link = new Link();
}

class cardgate_response {

    var $status = '';
    var $post = array();
    var $option = '';
    var $required_filed = array( 'DATAS', 'IS_VALID', 'OWNER', 'CB_TYPE', 'CLEF', 'ORDER_ID', 'TOTAL_AMOUNT', 'CURRENCY', 'UID', 'VERSION' );
    var $response_status = array(
        0 => 'Transaction in progress', // Pending
        100 => 'Authorization successful',
        150 => '3D secure status "Y" (yes), waiting for 3D secure authentication',
        152 => '3D secure status "N" (no)',
        154 => '3D secure status "U" (unknown)',
        156 => '3D secure status "E" (error)',
        200 => 'Transaction successful',
        210 => 'Recurring transaction successful',
        300 => 'Transaction failed',
        301 => 'Transaction failed due to anti fraud system',
        310 => 'Recurring transaction failed',
        350 => 'Transaction failed, time out for 3D secure authentication',
        400 => 'Refund to customer',
        410 => 'Chargeback by customer',
        700 => 'Transaction waits for user action' );

    function __construct( $post = '', $option ) {
        $post = $post == '' ? $_REQUEST : $post;

        $this->post = $post;
        $this->option = $option;
        if ( !$this->__isSafe() ) {
            $this->__debug( 'hashcheck failed.', $this->post );
        }

        $this->__log( $this->post );
    }

    function __isSafe() {

        $extraData = explode( '|', $_REQUEST['extra'] );
        $cartId = $extraData[0];
        $option = strtoupper( $this->post['billing_option'] );
        $cart = new Cart( $cartId );
        $amount = $this->post['amount'];
        $currency = new Currency( ( int ) ($cart->id_currency) );
        $site_id = Configuration::get( 'CARDGATE_SITEID' );
        $hashKey = Configuration::get( 'CARDGATE_HASH_KEY' );

        $hashString = ($this->post['is_test'] == 1 ? 'TEST' : '') .
                $this->post['transaction_id'] .
                $this->post['currency'] .
                $amount .
                $this->post['ref'] .
                $this->post['status'] .
                $hashKey;

        return (md5( $hashString ) == $this->post['hash']);
    }

    # Notification about problems via email

    function __debug( $msg, $datas = 'no data' ) {
        $datas = $datas == '' ? 'no data' : $datas;

        @$m = mail( EMAIL_NOTIFICATION_ADDRESS, "Error : Response payment by CardGate Creditcard", $msg . PHP_EOL . PHP_EOL . "DATAS:" . PHP_EOL . PHP_EOL . $datas, "From: " . EMAIL_NOTIFICATION_ADDRESS );
        if ( !$m ) {
            # If mail is not send then place the message in the log
            $this->__log( array( 'message' => $msg, 'datas' => $datas ) );
        }
    }

    function __log( $data ) {
        if ( LOG_TRANSACTIONS ) {
            $ln = array();
            foreach ( $data as $k => $v ) {
                $ln[] = $k . '=' . $v;
            }
            if ( $f = fopen( CARDGATE_LOG, 'a' ) or die( $this->__debug( "The file " . CARDGATE_LOG . " can't be opened and written to!" ) ) ) {
                fputs( $f, PHP_EOL . date( "Y/m/d H:i:s" ) . ' === ' . implode( '&', $ln ) ) or die( $this->__debug( "The file " . CARDGATE_LOG . " is not writeable!" ) );
                fclose( $f );
            }
        }
    }

    function error_status() {

        $return_msg = 'Undifine error';
        foreach ( $this->response_status as $row => $key ) {
            if ( $row == $this->post['status'] ) {
                $return_msg = $key;
                break;
            }
        }
        return $return_msg;
    }

    # Return Number of Cart

    function getDataSend() {
        return $this->post['ref'];
    }

    # Check if status of transaction == 200 - True

    function isValid() {
        return $this->post['status'] == 'succes';
//			return $this->post['status'] == 200;
    }

    # Check other status of transaction. Return True if transaction failure
    #                                    Return False if transaction wait other signal

    function isError() {
        if ( ($this->post['status'] == 300) or ( $this->post['status'] == 156) or ( $this->post['status'] == 301) or ( $this->post['status'] == 310) or ( $this->post['status'] == 350) ) {
            return true;
        } else {
            return false;
        }
    }

    function getStatus() {
        $status = $this->post['status'];
        $status_id = $this->post['status_id'];

        if ( $status == '0' ) {
            $statusResult = 'pending';
        }
        if ( $status >= '200' && $status < '300' ) {
            $statusResult = 'succes';
        }
        if ( $status >= '300' && $status < '400' ) {
            if ( $status_id == '309' ) {
                $statusResult = 'canceled';
            } else {
                $statusResult = 'failed';
            }
        }
        if ( $status >= '700' && $status < '800' ) {
            $statusResult = 'pending';
        }

        return $statusResult;
    }

}

# New cardgate_creditcard responce 
$_mr = new cardgate_response( $_REQUEST, $option );

if ( $_mr->__isSafe() ) {

    if ( isset( $_REQUEST['extra'] ) && isset( $_REQUEST['status'] ) ) {
        $option = $_mr->option;

        $payment = 'Cardgate' . $option;

        $_cardgate = new $payment();
        $extraData = explode( '|', $_REQUEST['extra'] );
        $cartId = $extraData[0];
        $extraCosts = $extraData[1];

        $cart = new Cart( $cartId );

        $total = $cart->getOrderTotal( true, 3 );
        $sStatus = $_mr->getStatus();

        switch ( $sStatus ) {
            case 'failed':
                $newStatus = _PS_OS_ERROR_;
                break;
            case 'canceled':
                $newStatus = _PS_OS_CANCELED_;
                break;
            case 'pending';
                $newStatus = Configuration::get( 'CARDGATE_PENDING' );
                break;
            case 'succes':
                $newStatus = _PS_OS_PAYMENT_;
                break;
        }


        if ( $cart->OrderExists() ) {
            $id_order = Order::getOrderByCartId( $cart->id );
            $oOrder = new Order( $id_order );
            if ( $oOrder->current_state != _PS_OS_PAYMENT_ && $oOrder->current_state != _PS_OS_ERROR_ ) {
                $oOrder->setCurrentState( $newStatus );
                $oOrder->save();
            }
        } else {
            // update payment total with extra fee before making the order

            switch ( $sStatus ) {
                case 'failed':
                    $message = "Transaction failed";
                    $ln = 'Detail of order Cart = ' . $cartId . ' Message ' . $message . '!';
                    $_cardgate->validateOrder( $cartId, $newStatus, $total, $_cardgate->paymentname . ' Payment', str_replace( '.br.', "<br/>\n", $message ) );
                    break;
                case 'canceled':
                    $message = "Transaction canceled";
                    $ln = 'Detail of order Cart = ' . $cartId . ' Message ' . $message . '!';
                    if ( !empty( $_REQUEST['status_id'] ) && $_REQUEST['status_id'] != 309 ) {
                        $_cardgate->validateOrder( $cartId, $newStatus, $total, $_cardgate->paymentname . ' Payment', str_replace( '.br.', "<br/>\n", $message ) );
                    }
                    break;
                case 'pending':
                    $_cardgate->validateOrder( $cartId, $newStatus, $total, $_cardgate->paymentname . ' Payment', NULL, NULL, NULL, false, $cart->secure_key );
                    break;
                case 'succes':
                    if ( $extraCosts > 0 ) {
                        $st = Configuration::get( 'CARDGATE_PENDING' );
                        $_cardgate->validateOrder( $cartId, $st, $total, $_cardgate->paymentname . ' Payment', NULL, NULL, NULL, false, $cart->secure_key );
                    } else {
                        $message = "Transaction succes";
                        $ln = 'Detail of order Cart = ' . $cartId . ' Message ' . $message . '!';
                        $_cardgate->validateOrder( $cartId, $newStatus, $total, $_cardgate->paymentname . ' Payment', str_replace( '.br.', "<br/>\n", $message ), NULL, NULL, false, $cart->secure_key );
                    }
                    break;
            }

            $result = Db::getInstance()->ExecuteS( 'SELECT `id_order` FROM `' . _DB_PREFIX_ . 'orders` WHERE `id_cart` = ' . intval( $cartId ) );
            $id_order = $result[0]['id_order'];
            $oOrder = new Order( $id_order );

            if ( $sStatus != 'canceled' && $extraCosts > 0 ) {
                $shippingCost = $oOrder->total_shipping;
                $newShippingCosts = $shippingCost + $extraCosts;
                $extraCostsExcl = round( $extraCosts / (1 + (21 / 100)), 2 );

                //als de order extra kosten heeft, moeten deze worden toegevoegd. 
                $oOrder->total_shipping = $newShippingCosts;
                $oOrder->total_shipping_tax_excl = $oOrder->total_shipping_tax_excl + $extraCostsExcl;
                $oOrder->total_shipping_tax_incl = $newShippingCosts;

                $oOrder->total_paid_tax_excl = $oOrder->total_paid_tax_excl + $extraCostsExcl;
                $oOrder->total_paid_tax_incl = $oOrder->total_paid_real = $oOrder->total_paid = $oOrder->total_paid + floatval($extraCosts);

                $oOrder->update();
            }
            if ( ($sStatus == 'succes') && ($extraCosts > 0) ) {
                $result = $oOrder->addOrderPayment( $oOrder->total_paid_tax_incl, 'Unknown', $_REQUEST['transaction_id'] );
                $orderPayment = OrderPayment::getByOrderId( $oOrder->id );

                $history = new OrderHistory();
                $history->id_order = ( int ) $oOrder->id;
                $id_order_state = $newStatus;
                $history->changeIdOrderState( ( int ) $id_order_state, $oOrder, $orderPayment );
                $res = Db::getInstance()->getRow( '
			SELECT `invoice_number`, `invoice_date`, `delivery_number`, `delivery_date`
			FROM `' . _DB_PREFIX_ . 'orders`
			WHERE `id_order` = ' . ( int ) $oOrder->id );
                $history->addWithemail();
            }
        }
        echo $_REQUEST['transaction_id'] . "." . $_REQUEST['status'];
    }
} else {
    die( 'Hashcheck failed!' );
}
?>
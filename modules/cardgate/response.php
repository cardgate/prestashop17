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

if ( empty( $_REQUEST['pt'] ) || $_REQUEST['pt'] == '' ) {
    exit( 'invalid request!' );
} else {
    $option = $_REQUEST['pt'];
}

switch ( $option ) {
    case 'idealpro';
        $option = 'ideal';
        break;
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
    var $get = array();
    var $option = '';

    function __construct( $get = '', $option ) {
        $get = $get == '' ? $_GET : $get;

        $this->get = $get;
        $this->option = $option;
        if ( !$this->__isSafe() ) {
            $this->__debug( 'hashcheck failed.', $this->get );
        }

        $this->__log( $this->get );
    }

    function __isSafe() {

        $amount = $this->get['amount'];
        $site_id = Configuration::get( 'CARDGATE_SITE_ID' );
        $hashKey = Configuration::get( 'CARDGATE_HASH_KEY' );

        $hashString = ($this->get['testmode'] == 1 ? 'TEST' : '') .
                $this->get['transaction'] .
                $this->get['currency'] .
                $amount .
                $this->get['reference'] .
                $this->get['code'] .
                $hashKey;
        return (md5( $hashString ) == $this->get['hash']);
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

    # Return Number of Cart

    function getDataSend() {
        return $this->get['ref'];
    }

    # Check if status of transaction == 200 - True

    function isValid() {
        return $this->get['status'] == 'succes';
//			return $this->get['status'] == 200;
    }

    # Check other status of transaction. Return True if transaction failure
    #                                    Return False if transaction wait other signal

    function isError() {
        if ( ($this->get['status'] == 300) or ( $this->get['status'] == 156) or ( $this->get['status'] == 301) or ( $this->get['status'] == 310) or ( $this->get['status'] == 350) ) {
            return true;
        } else {
            return false;
        }
    }

    function getStatus() {
        $status = $this->get['code'];

        if ( $status == '0' ) {
            $statusResult = 'pending';
        }
        if ( $status >= '200' && $status < '300' ) {
            $statusResult = 'succes';
        }
        if ( $status >= '300' && $status < '400' ) {
            if ( $status == '309' ) {
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

    $option = $_mr->option;
    $payment = 'Cardgate' . $option;

    $_cardgate = new $payment();

	global $kernel;
	if(!$kernel){
		require_once _PS_ROOT_DIR_.'/app/AppKernel.php';
		$kernel = new \AppKernel('prod', false);
		$kernel->boot();
	}

    $extraData = explode( '|', $_REQUEST['reference'] );
    $cartId = substr($extraData[0],6);
    $extraCosts = floatval($extraData[1])/100;
    $total = round(round($_REQUEST['amount']/100,2)- $extraCosts,2);

    $cart = new Cart( $cartId );

   // $total = $cart->getOrderTotal( true,Cart::BOTH);
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
        $id_order = Order::getIdByCartId( $cartId );
        $oOrder = new Order((int) $id_order);
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
            	$_cardgate->validateOrder( $cartId, $newStatus, $total, $_cardgate->paymentname . ' Payment', NULL, NULL, ( int ) $cart->id_currency, false, $cart->secure_key );
                break;
            case 'succes':
                    $st = Configuration::get( 'CARDGATE_PENDING' );
                    $_cardgate->validateOrder( $cartId, $st, $total, $_cardgate->paymentname . ' Payment', NULL, NULL, ( int ) $cart->id_currency, false, $cart->secure_key );
                break;
        }

        $result = Db::getInstance()->ExecuteS( 'SELECT `id_order` FROM `' . _DB_PREFIX_ . 'orders` WHERE `id_cart` = ' . intval( $cartId ) );

        if ( empty( $result[0] ) ) {
            $id_order = 0;
        } else {
            $id_order = $result[0]['id_order'];
            $oOrder = new Order( $id_order );
        }

        if ( $sStatus != 'canceled' ) {

            $shippingCost = $oOrder->total_shipping;
            $newShippingCosts = $shippingCost + $extraCosts;
            $extraCostsExcl = round( $extraCosts / (1 + ($oOrder->carrier_tax_rate  / 100)), 2 );

            // add the extra costs to the totals 
            $oOrder->total_shipping = $newShippingCosts;
            $oOrder->total_shipping_tax_excl = $oOrder->total_shipping_tax_excl + $extraCostsExcl;
            $oOrder->total_shipping_tax_incl = $newShippingCosts;

            $oOrder->total_paid_tax_excl = $oOrder->total_paid_tax_excl + $extraCostsExcl;
            $oOrder->total_paid_tax_incl = $oOrder->total_paid_real = $oOrder->total_paid = $oOrder->total_paid + floatval( $extraCosts );

            $oOrder->update();
        }
        
        if ( ($sStatus == 'succes') ) {
            $result = $oOrder->addOrderPayment( $oOrder->total_paid_tax_incl, $_cardgate->paymentname, $_REQUEST['transaction_id'] );
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
    echo $_REQUEST['transaction'] . "." . $_REQUEST['code'];
} else {
    die( 'Hashcheck failed!' );
}
?>
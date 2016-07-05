<?php

if ( file_exists( dirname(__FILE__).'/../cardgate/cardgate.php' )) {
    require_once dirname(__FILE__).'/../cardgate/cardgate.php';
} else {
    $GLOBALS['CARDGATENOTFOUND']=1;
    if (!class_exists('CardgatePayment')) { class CardgatePayment extends PaymentModule { function get_url(){} } }
}

/**
 * CardGate - Prestashop
 *
 * 2010-11-09 (LL) Version 1.00
 *   Initial release
 *   
 * 2011-04-18 (BZ) Version 1.01
 *   Added PayPal, updated countries for payment options
 * 
 * Data for langiange translations
 * 
 *   $this->l('Pay with')
 */

class Cardgatebitcoin extends CardgatePayment {

    private $_postErrors = array();
    protected $_childClassFile = __FILE__;

    /**
     * Available payment methods setup
     */
    public function __construct() {
        global $cookie, $order;

        $this->name = 'cardgatebitcoin';
        $this->paymentcode = 'bitcoin';
        $this->paymentname = 'Bitcoin';
        $this->logoname = 'bitcoin';
        $this->imageurl = 'https://www.cardgate.com/img/paymenttypes/logo_' . $this->paymentcode . '.png';
        $this->extra_cost = Configuration::get( 'CARDGATE_' . strtoupper( $this->paymentcode) . '_EXTRACOST' );


        parent::__construct();
        $this->page = basename( __FILE__, '.php' );
        $this->displayName = $this->l('CardGate Bitcoin');
        $this->description = $this->l('Accepts payments with CardGate Bitcoin.');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
        $this->_url = $this->get_url();

        $total = 0;
        $rate = 'EUR';

        if ( isset( $GLOBALS['cart'] ) && $GLOBALS['cart']->id_currency > 0 ) {
            $currency = new Currency( $GLOBALS['cart']->id_currency );
            $total = round( Tools::convertPrice( $GLOBALS['cart']->getOrderTotal( true, 3 ), $currency ), 2 );
            $rate = $currency->iso_code;
        }
        $id_lang = (!isset( $cookie ) OR ! is_object( $cookie )) ? intval( Configuration::get( 'PS_LANG_DEFAULT' ) ) : intval( $cookie->id_lang );
        
        
        if ( isset($GLOBALS['CARDGATENOTFOUND']) ) $this->warning = $this->l('The CardGate module is not found.');
    }

}

?>

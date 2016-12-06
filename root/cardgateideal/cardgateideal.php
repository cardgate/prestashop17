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
 * $this->l('Pay with')    
 */

class Cardgateideal extends CardgatePayment {

    private $_postErrors = array();
    protected $_paymentHookTpl = 'views/hook/payment.tpl';
    protected $_childClassFile = __FILE__;

    /**
     * Available payment methods setup
     */
    public function __construct() {
        global $cookie, $order;
        
        $this->paymentcode = 'ideal';
        $this->paymentname = 'iDEAL';
        $this->name = 'cardgateideal';
        $this->logoname = 'ideal';
        $this->imageurl = 'https://gateway.cardgateplus.com/images/logo' . $this->paymentcode . '.gif';
        $this->extra_cost = Configuration::get( 'CARDGATE_' . strtoupper( $this->paymentcode) . '_EXTRACOST' );
         
        parent::__construct();
        
        $this->page = basename( __FILE__, '.php' );
        $this->displayName = $this->l('CardGate iDEAL');
        $this->description = $this->l('Accepts payments with CardGate iDEAL.');
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

    
    public function getBanks() {
        
        $url = 'https://gateway.cardgateplus.com/cache/idealDirectoryRabobank.dat';
         
        if ( !ini_get( 'allow_url_fopen' ) || !function_exists( 'file_get_contents' ) ) {
            $result = false;
        } else {
            $result = file_get_contents( $url );
        }

        $aBanks = array();

        if ( $result ) {
            $aBanks = unserialize( $result );
            $aBanks[0] = $this->l('-Choose your bank please-');
        }

        if ( count( $aBanks ) < 1 ) {
            $aBanks = array( '0031' => 'ABN Amro',
                '0091' => 'Friesland Bank',
                '0721' => 'ING Bank',
                '0021' => 'Rabobank',
                '0751' => 'SNS Bank',
                '0761' => 'ASN Bank',
                '0771' => 'SNS Regio Bank',
                '0511' => 'Triodos Bank',
                '0161' => 'Van Landschot Bank'
            );
        }
        return $aBanks;
    }    
}
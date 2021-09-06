<?php

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if ( file_exists( dirname( __FILE__ ) . '/../cardgate/cardgate.php') ) {
    require_once dirname( __FILE__ ) . '/../cardgate/cardgate.php';
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
class Cardgatepaysafecash extends CardgatePayment {
	
	var $tab = 'payments_gateways';
	var $author = 'CardGate';
	var $shop_version = _PS_VERSION_;
	var $currencies = true;
	var $currencies_mode = 'radio';
	var $_html = '';
	var $extra_cost = '';
	protected $_paymentHookTpl = '';

    private $_postErrors = array();
    protected $_childClassFile = __FILE__;

    /**
     * Available payment methods setup
     */
    public function __construct() {
        global $cookie, $order;

        $this->name = 'cardgatepaysafecash';
        $this->paymentcode = 'paysafecash';
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_ );
        $this->paymentname = 'Paysafecash';
        $this->logoname = 'paysafecash';
        $this->version = Configuration::get('CARDGATE_MODULE_VERSION');
        $this->extra_cost = Configuration::get('CARDGATE_' . strtoupper( $this->paymentcode ) . '_EXTRACOST');
        $this->controllers = array('validation');
        $this->is_eu_compatible = 1;
        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        $this->bootstrap = true;

        parent::__construct();
        $this->page = basename( __FILE__, '.php');
        $this->displayName = $this->l('CardGate Paysafecash');
        $this->description = $this->l('Accepts payments with CardGate Paysafecash.');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details?');

        if ( !count( Currency::checkPaymentCurrencies( $this->id ) ) ) {
            $this->warning = $this->l('No currency has been set for this module.');
        }

        $total = 0;
        $rate = 'EUR';

        if ( isset( $GLOBALS['cart'] ) && $GLOBALS['cart']->id_currency > 0 ) {
            $currency = new Currency( $GLOBALS['cart']->id_currency );
            $total = round( Tools::convertPrice( $GLOBALS['cart']->getOrderTotal( true, 3 ), $currency ), 2 );
            $rate = $currency->iso_code;
        }
        $id_lang = (!isset( $cookie ) OR ! is_object( $cookie )) ? intval( Configuration::get('PS_LANG_DEFAULT') ) : intval( $cookie->id_lang );

        if ( isset( $GLOBALS['CARDGATENOTFOUND'] ) )
            $this->warning = $this->l('The CardGate module is not found.');
    }
}

?>

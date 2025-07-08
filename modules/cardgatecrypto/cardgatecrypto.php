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
 * Data for languange translations
 *
 *   $this->l('Pay with')
 */
class Cardgatecrypto extends CardgatePayment {

    var $tab = 'payments_gateways';
    var $author = 'CardGate';
    var $shop_version = _PS_VERSION_;
    var $currencies = true;
    var $currencies_mode = 'radio';
    var $_html = '';
    protected $_paymentHookTpl = '';

    private $_postErrors = array();
    protected $_childClassFile = __FILE__;

    /**
     * Available payment methods setup
     */
    public function __construct() {
        global $cookie, $order;

        $this->name = 'cardgatecrypto';
        $this->paymentcode = 'crypto';
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_ );
        $this->paymentname = 'Crypto';
        $this->logoname = 'crypto';
        $this->version = Configuration::get('CARDGATE_MODULE_VERSION');
        $this->controllers = array('validation');
        $this->is_eu_compatible = 1;
        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        $this->bootstrap = true;

        parent::__construct();
        $this->page = basename( __FILE__, '.php');
        $this->displayName = $this->l('CardGate Crypto');
        $this->description = $this->l('Accepts payments with CardGate Crypto.');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details?');

        if ( !count( Currency::checkPaymentCurrencies( $this->id ) ) ) {
            $this->warning = $this->l('No currency has been set for this module.');
        }

        if ( isset( $GLOBALS['CARDGATENOTFOUND'] ) )
            $this->warning = $this->l('The CardGate module is not found.');
    }
}

?>

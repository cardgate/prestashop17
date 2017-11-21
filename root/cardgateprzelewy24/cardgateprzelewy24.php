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
class Cardgateprzelewy24 extends CardgatePayment {
	
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

        $this->name = 'cardgateprzelewy24';
        $this->paymentcode = 'przelewy24';
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_ );
        $this->paymentname = 'Przelewy24';
        $this->logoname = 'przelewy24';
        $this->imageurl = 'https://gateway.cardgateplus.com/images/logo' . $this->paymentcode . '.gif';
        $this->extra_cost = Configuration::get('CARDGATE_' . strtoupper( $this->paymentcode ) . '_EXTRACOST');
        $this->controllers = array('validation');
        $this->is_eu_compatible = 1;
        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        $this->bootstrap = true;

        parent::__construct();
        $this->page = basename( __FILE__, '.php');
        $this->displayName = $this->trans('Przelewy24', array());
        $this->description = $this->l('Accepts payments with CardGate Przelewy24.');
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
    
    public function install() {
    	
    	if ( !parent::install() || !$this->registerHook('paymentOptions') || !$this->registerHook('paymentReturn') ) {
    		return false;
    	}
    	return true;
    	
    	$payment = strtoupper( $this->paymentcode );
    	
    	if ( !parent::install() OR ! $this->registerHook('payment') )
    		return false;
    		return true;
    }
    
    public function uninstall() {
    	
    	$paymentcode = $this->paymentcode;
    	
    	if ( $paymentcode == '')
    		return false;
    		
    		$paymentcode = strtoupper( $paymentcode );
    		
    		if ( !parent::uninstall() )
    			return false;
    			return true;
    }
    
    public function hookPaymentOptions( $params ) {
    	
    	if ( !$this->active ) {
    		return;
    	}
    	if ( !$this->checkCurrency( $params['cart'] ) ) {
    		return;
    	}
    	
    	if (isset($_COOKIE['issuer'])){
    		$issuer = $_COOKIE['issuer'];
    	} else {
    		$issuer = 0;
    	}
    	
    	$paymentOption = new PaymentOption();
    	
    	$costText = '';
    	$extraCosts = floatval(Configuration::get( 'CARDGATE_'.strtoupper( $this->paymentcode).'_EXTRACOST' ));
    	
    	if ($extraCosts > 0 ){
    		$oCurrency = new Currency( $params['cart']->id_currency );
    		$costText .= ' + '.$oCurrency->sign.' '.number_format($extraCosts, 2);
    	}
    	
    	if ($this->paymentcode == 'ideal'){
    		$this->smarty->assign(['issuers'=>$this->getBanks(),'selected'=>$issuer]);
    		$additionalInformation = $this->fetch('module:cardgateideal/views/templates/front/payment_infos.tpl');
    	} else {
    		$additionalInformation = null;
    	}
    	
    	$display = Configuration::get('CARDGATE_PAYMENT_DISPLAY');
    	if ($display == 'textandlogo' || $display == 'textonly'){
    		$actionText = $this->l('Pay with').' '.$this->paymentname . $costText;
    	} else {
    		$actionText = null;
    	}
    	
    	if ($display == 'textandlogo' || $display == 'logoonly' ){
    		$logo = Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/logo.gif');
    	} else {
    		$logo = null;
    	}
    	
    	$paymentOption->setCallToActionText($actionText)
    	->setAction($this->context->link->getModuleLink('cardgate', 'validation', array(), true))
    	->setInputs( $this->paymentData() )
    	->setAdditionalInformation($additionalInformation)
    	->setLogo($logo);
    	
    	$payment_options = [
    			$paymentOption
    	];
    	return $payment_options;
    }
    
    public function checkCurrency( $cart ) {
    	$currency_order = new Currency( $cart->id_currency );
    	$currencies_module = $this->getCurrency( $cart->id_currency );
    	if ( is_array( $currencies_module ) ) {
    		foreach ( $currencies_module as $currency_module ) {
    			if ( $currency_order->id == $currency_module['id_currency'] ) {
    				return true;
    			}
    		}
    	}
    	return false;
    }
    
    public function displayConf() {
    	
    	$this->_html = $this->displayConfirmation( $this->l('Settings updated') );
    }
    
    public function paymentData() {
    	$data =   [
    			'option' => [
    					'name' => 'option',
    					'type' => 'hidden',
    					'value' => $this->paymentcode,
    			]
    	];
    	
    	return $data;
    }
    
    public function extraCosts( $extra_cost ) {
    	$cart = $this->context->cart;
    	$total = number_format( ($cart->getOrderTotal( true, Cart::BOTH ) ), 2, '.', '');
    	if ( $extra_cost == 0 || $extra_cost == '') {
    		return 0;
    	}
    	if ( strstr( $extra_cost, '%') ) {
    		$percentage = str_replace('%', '', $extra_cost );
    		return round( ($total * $percentage / 100 ), 2 );
    	}
    	if ( is_numeric( $extra_cost ) ) {
    		return round( $extra_cost, 2 );
    	}
    }

}

?>
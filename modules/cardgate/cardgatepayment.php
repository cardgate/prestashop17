<?php

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class CardgatePayment extends PaymentModule {

    var $version = '1.7.17';
    var $tab = 'payments_gateways';
    var $author = 'CardGate';
    var $shop_version = _PS_VERSION_;
    var $currencies = true;
    var $currencies_mode = 'radio';
    var $_html = '';
    protected $_paymentHookTpl = '';

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

        if ( $this->paymentcode == 'ideal' && Configuration::get( 'CARDGATE_SHOW_ISSUERS' ) ){
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
            ],
            'paymentname' => [
                'name' => 'paymentname',
                'type' => 'hidden',
                'value' => $this->paymentname,
            ]
        ];

        return $data;
    }

    protected function generateForm() {
        if ( Configuration::get( 'CARDGATE_SHOW_ISSUERS' ) ) {
            $this->context->smarty->assign([
                'action' => $this->context->link->getModuleLink($this->name, 'validation', array(), true),
                'issuers' => $this->getBanks(),
            ]);
            return $this->context->smarty->fetch('module:cardgateideal/views/templates/front/payment_form.tpl');
        } else {
            return false;
        }

    }
}
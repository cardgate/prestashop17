<?php
class CardgateValidationModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
    
        require_once(str_replace('controllers/front','',dirname(__FILE__)).'cardgate-clientlib-php/init.php');
   
        $option = $_REQUEST['option'];
        $paymentModule = 'cardgate'.$_REQUEST['option'];
        $cart = $this->context->cart;
        $customer = $this->context->customer;
        
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }
        
        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == $paymentModule) {
                $authorized = true;
                break;
            }
        }
        
        if (!$authorized) {
            die($this->module->l('This payment method is not available.', 'validation'));
        }
        
        $this->context->smarty->assign([
            'params' => $_REQUEST,
        ]);
       
        try {
            $oCardGate = new cardgate\api\Client( ( int ) Configuration::get( 'CARDGATE_MERCHANT_ID' ), Configuration::get( 'CARDGATE_MERCHANT_API_KEY' ), (Configuration::get( 'CARDGATE_TEST_MODE' ) == 1 ? TRUE : FALSE ) );
            $oCardGate->setIp( $_SERVER['REMOTE_ADDR'] );
            $oCardGate->setLanguage( $this->context->language->iso_code);
            $oCardGate->version()->setPlatformName( 'PrestaShop' );
            $oCardGate->version()->setPlatformVersion( _PS_VERSION_ );
            $oCardGate->version()->setPluginName( $paymentModule );
            $oCardGate->version()->setPluginVersion( $this->module->version );
           
            $data = $this->module->_paymentData($option);
          
            $iSiteId = (int)Configuration::get( 'CARDGATE_SITE_ID' );
            
            $oTransaction = $oCardGate->transactions()->create( $iSiteId, (int)$data['amount'], $data['currency'] );
            // Configure payment option.
            
            $oTransaction->setPaymentMethod( $oCardGate->methods()->get( $option ) );
            
            if ( 'ideal' == $option && !empty ($_COOKIE['issuer']) ) {
            	$oTransaction->setIssuer( $_COOKIE['issuer'] );
            }
            
             // Configure customer.
            $oConsumer = $oTransaction->getConsumer();
            $oConsumer->setEmail( $data['email'] );
            $oConsumer->address()->setFirstName( $data['first_name']  );
            $oConsumer->address()->setLastName( $data['last_name'] );
            $oConsumer->address()->setAddress( $data['address']);
            $oConsumer->address()->setZipCode( $data['zip_code']  );
            $oConsumer->address()->setCity( $data['city']  );
            $oConsumer->address()->setCountry( $data['country']);
            $oConsumer->shippingAddress()->setFirstName( $data['delivery_first_name']  );
            $oConsumer->shippingAddress()->setLastName( $data['delivery_last_name'] );
            $oConsumer->shippingAddress()->setAddress( $data['delivery_address']);
            $oConsumer->shippingAddress()->setZipCode( $data['delivery_zip_code']  );
            $oConsumer->shippingAddress()->setCity( $data['delivery_city']  );
            $oConsumer->shippingAddress()->setCountry( $data['delivery_country']);
          
            if ($data['phone'] !=''){
            	$oConsumer->setPhone($data['phone']);
            }
            
            $oCart = $oTransaction->getCart();
            
            foreach($data['cartitems'] as $cartitem){
                switch ( $cartitem['type'] ) {
                    case 1:
                        $oItem = $oCart->addItem( \cardgate\api\Item:: TYPE_PRODUCT, $cartitem['sku'], $cartitem['name'], $cartitem['quantity'], $cartitem['price'] );
                        break;
                    case 2:
                        $oItem = $oCart->addItem( \cardgate\api\Item:: TYPE_SHIPPING, $cartitem['sku'], $cartitem['name'], $cartitem['quantity'], $cartitem['price'] );
                        break;
                    case 3:
                        $oItem = $oCart->addItem( \cardgate\api\Item:: TYPE_PAYMENT, $cartitem['sku'], $cartitem['name'], $cartitem['quantity'], $cartitem['price'] );
                        break;
                    case 4:
                        $oItem = $oCart->addItem( \cardgate\api\Item:: TYPE_DISCOUNT, $cartitem['sku'], $cartitem['name'], $cartitem['quantity'], $cartitem['price'] );
                        break;
                    case 5:
                        $oItem = $oCart->addItem( \cardgate\api\Item:: TYPE_HANDLING, $cartitem['sku'], $cartitem['name'], $cartitem['quantity'], $cartitem['price'] );
                        break;
                    case 6:
                    	$oItem = $oCart->addItem( \cardgate\api\Item:: TYPE_CORRECTION, $cartitem['sku'], $cartitem['name'], $cartitem['quantity'], $cartitem['price'] );
                    	break;
                    case 7:
                    	$oItem = $oCart->addItem( \cardgate\api\Item:: TYPE_VAT_CORRECTION, $cartitem['sku'], $cartitem['name'], $cartitem['quantity'], $cartitem['price'] );
                    	break;
                }
                $oItem->setVat( $cartitem['vat'] );
                $oItem->setVatAmount( $cartitem['vat_amount'] );
                $oItem->setVatIncluded( $cartitem['vat_inc'] );
            }
          
            $oTransaction->setCallbackUrl( $data['callback'] );
            $oTransaction->setSuccessUrl( $data['return_url'] );
            $oTransaction->setFailureUrl( $data['return_url_failed']  );
            $oTransaction->setReference( $data['ref'] );
            $oTransaction->setDescription( $data['description'] );
            
            $oTransaction->register();
           
            $sActionUrl = $oTransaction->getActionUrl();
        
            if ( NULL !== $sActionUrl ) {
                Tools::redirect($sActionUrl);                
            }
        } catch ( cardgate\api\Exception $oException_ ) {
            // htmlspecialchars( $oException_->getMessage() );
            Tools::redirect('index.php?controller=order&step=1');
        }
        Tools::redirect('index.php?controller=order&step=1');
    }       
}
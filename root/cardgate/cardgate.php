<?php

if ( !defined( '_PS_VERSION_' ) )
    exit;

require_once "cardgatepayment.php";

class Cardgate extends PaymentModule {

    public function __construct() {

        $this->name = 'cardgate';
        $this->paymentcode = 'cardgate';
        $this->paymentname = 'CardGate';
        $this->tab = 'payments_gateways';
        $this->version = '1.6.17';
        $this->author = 'CardGate';
        $this->bootstrap = true;
        $this->currencies = true;
        $this->currencies_mode = 'radio';

        $this->imageurl = 'https://gateway.cardgateplus.com/images/logo' . $this->paymentcode . '.gif';

        parent::__construct();

        $this->page = basename( __FILE__, '.php' );
        $this->displayName = $this->l('CardGate Bank common');
        $this->description = $this->l('CardGate Bank base module.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall the CardGate module?');
    }
    
    public function install() {
        $this->createOrderState();
        return parent::install();
    }

    public function uninstall() {
        return parent::uninstall();
    }

    public function createOrderState() {

        if ( !Configuration::get( 'CARDGATE_PENDING' ) ) {
            $order_state = new OrderState();
            $order_state->name = array();

            foreach ( Language::getLanguages() as $language ) {
                if ( Tools::strtolower( $language['iso_code'] ) == 'nl' ) {
                    $order_state->name[$language['id_lang']] = 'Wachten op CardGate betaling';
                } elseif ( Tools::strtolower( $language['iso_code'] ) == 'de' ) {
                    $order_state->name[$language['id_lang']] = 'Warten auf Zahlungseingang von CardGate';
                } elseif ( Tools::strtolower( $language['iso_code'] ) == 'fr' ) {
                    $order_state->name[$language['id_lang']] = 'En attente du paiement par CardGate';
                } elseif ( Tools::strtolower( $language['iso_code'] ) == 'es' ) {
                    $order_state->name[$language['id_lang']] = 'En espera de pago por CardGate';
                } elseif ( Tools::strtolower( $language['iso_code'] ) == 'it' ) {
                    $order_state->name[$language['id_lang']] = 'In attesa di pagamento con CardGate';
                } else {
                    $order_state->name[$language['id_lang']] = 'Awaiting CardGate payment';
                }
            }

            $order_state->send_email = true;
            $order_state->color = 'RoyalBlue';
            $orderState->hidden = false;
            $orderState->delivery = false;
            $orderState->logable = false;
            $orderState->invoice = false;
            $orderState->paid = false;
            $order_state->unremovable = true;


            if ( $order_state->add() ) {
                $source = _PS_MODULE_DIR_ . 'cardgate/logo.gif';
                $destination = dirname( __FILE__ ) . '/../../img/os/' . ( int ) $order_state->id . '.gif';
                copy( $source, $destination );
            }
            Configuration::updateGlobalValue( 'CARDGATE_PENDING', ( int ) $order_state->id );
        }
    }

    public function displayConf() {

        $this->_html = $this->displayConfirmation( $this->l('Settings updated') );
    }
    
    public function alterName($name) {
       $name =  ($name == 'mc' ? 'mistercash' : $name);
       return $name;
    }

    public function getContent() {
        $output = null;

        if ( Tools::isSubmit( 'submit' . $this->name ) ) {
            // get settings from post because post can give errors and you want to keep values
            $mode = ( string ) Tools::getValue( 'CARDGATE_MODE' );
            $siteid = ( string ) Tools::getValue( 'CARDGATE_SITEID' );
            $hashkey = ( string ) Tools::getValue( 'CARDGATE_HASH_KEY' );
            $my_module_field_names = $this->myModelFieldNames();
            foreach ( $my_module_field_names as $key => $my_module_field_name ) {
                Configuration::updateValue($my_module_field_name,  ( string ) Tools::getValue( $my_module_field_name) );
            }

            // no errors so update the values
            Configuration::updateValue( 'CARDGATE_MODE', $mode );
            Configuration::updateValue( 'CARDGATE_SITEID', $siteid );
            Configuration::updateValue( 'CARDGATE_HASH_KEY', $hashkey );

            $output .= $this->displayConfirmation( $this->l('Settings updated') );
        }

        return $output . $this->displayForm();
    }

    public function displayForm() {
        $my_modules = array();
        $my_module_field_names = array();
        $modules = Module::getModulesOnDisk();
        foreach ( $modules AS $module ) {
            if ( strstr( $module->name, 'cardgate' ) !== false ) {
                $name = str_replace( 'cardgate', '', $module->name );
                if ( $name != '' ) {
                    $name = $this->alterName($name );
                    $my_modules[] = $name;
                    $my_module_field_names[] = 'CARDGATE_' . strtoupper( $name ) . '_EXTRACOST';
                    $name = '';
                }
            }
        }
        $extra_costs = array();
        foreach ( $my_modules as $key => $module ) {

            $extra_costs[] = array(
                'type' => 'text',
                'label' => $this->l('Extra cost').' '. $module,
                'name' => $my_module_field_names[$key],
                'size' => '1',
                'required' => false,
                'hint' => $this->l('Add an extra charge for your payment method, for example, 1.95 or 5%'),
            );
        }

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('General Settings'),
                'image' => '../img/admin/edit.gif'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Mode'),
                    'name' => 'CARDGATE_MODE',
                    'required' => false,
                    'default_value' => 1,
                    'options' => array(
                        'query' => array( array( 'id' => 1, 'name' => 'test' ), array( 'id' => 0, 'name' => 'live' ) ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Site Id'),
                    'name' => 'CARDGATE_SITEID',
                    'size' => 64,
                    'required' => true,
                    'hint' => $this->l('The CardGate Site Id, which you can find in your CardGate back-office')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Hash Key'),
                    'name' => 'CARDGATE_HASH_KEY',
                    'size' => 20,
                    'required' => true,
                    'hint' => $this->l('The CardGate Hash Key, which you can find in your CardGate back-office')
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $fields_form[0]['form']['input'] = array_merge( $fields_form[0]['form']['input'], $extra_costs );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite( 'AdminModules' );
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true; // false -> remove toolbar
        $helper->toolbar_scroll = true; // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' => array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite( 'AdminModules' )
            ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite( 'AdminModules' ),
                'desc' => $this->l('Back to list')
            )
        );

        $extra_costs = array();

        if ( Tools::isSubmit( 'submit' . $this->name ) ) {
            // get settings from post because post can give errors and you want to keep values
            $mode = ( string ) Tools::getValue( 'CARDGATE_MODE' );
            $siteid = ( string ) Tools::getValue( 'CARDGATE_SITEID' );
            $hashkey = ( string ) Tools::getValue( 'CARDGATE_HASH_KEY' );
            foreach ( $my_module_field_names as $key => $my_module_field_name ) {
                $extra_costs[$my_module_field_name] = ( string ) Tools::getValue( $my_module_field_name );
            }
        } else {
            $mode = Configuration::get( 'CARDGATE_MODE' );
            $siteid = Configuration::get( 'CARDGATE_SITEID' );
            $hashkey = Configuration::get( 'CARDGATE_HASH_KEY' );
            foreach ( $my_module_field_names as $key => $my_module_field_name ) {
                $extra_costs[$my_module_field_name] = Configuration::get( $my_module_field_name );
            }
        }

        // Load current value
        $helper->fields_value['CARDGATE_MODE'] = $mode;
        $helper->fields_value['CARDGATE_SITEID'] = $siteid;
        $helper->fields_value['CARDGATE_HASH_KEY'] = $hashkey;

        foreach ( $my_module_field_names as $key => $my_module_field_name ) {
            $helper->fields_value[$my_module_field_name] = $extra_costs[$my_module_field_name];
        }

        return $helper->generateForm( $fields_form );
    }
    public function myModelFieldNames(){
        $my_module_field_names = array();
        $modules = Module::getModulesOnDisk();
        foreach ( $modules AS $module ) {
            if ( strstr( $module->name, 'cardgate' ) !== false ) {
                $name = str_replace( 'cardgate', '', $module->name );
                if ( $name != '' ) {
                    $name = $this->alterName($name );
                    $my_module_field_names[] = 'CARDGATE_' . strtoupper( $name ) . '_EXTRACOST';
                    $name = '';
                }
            }
        }
        return $my_module_field_names;
    }

}
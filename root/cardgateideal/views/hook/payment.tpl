{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $smarty.const._PS_VERSION_ >= 1.6}

<div class="row">
	<div class="col-xs-12">
        <p class="payment_module cardgate">
                <a href="javascript:void(0)" onclick="$('#cardgate_{$paymentcode}_form').submit();" id="cardgate{$paymentcode}_process_payment" title="{$paymenttext}">
                        <img style="max-width:60px;" src="https://curopayments.com/docs/api/img/logo_{$logoname}.png" alt="ideal" />
                        {$paymenttext}{if $extracosts != 0}  <span class="">+ &euro; {number_format($extracosts,2,',', '.')}</span> {/if}
                </a>
        </p>
    </div>
</div>
                
                <style>
        p.payment_module.cardgate a:after {
            color: #777777;
            content: "";
            display: block;
            font-family: "FontAwesome";
            font-size: 25px;
            height: 22px;
            margin-top: -11px;
            position: absolute;
            right: 15px;
            top: 50%;
            width: 14px;
        }
	p.payment_module.cardgate a 
	{ldelim}
		padding-left:17px;
	{rdelim}
    </style>

{else}
<p class="payment_module">
	<a href="javascript:void(0)" onclick="$('#cardgate_{$paymentcode}_form').submit();" id="cardgate{$paymentcode}_process_payment" title="{l s='Pay with iDEAL' mod='ideal'}">
		<img style="max-width:60px;" src="https://curopayments.com/docs/api/img/logo_{$logoname}.png" alt="ideal" /> {$paymenttext}				
	</a>
</p>

{/if}
<form id="cardgate_{$paymentcode}_form" action="{$link->getModuleLink('cardgateideal',  'payment', array(), true)|escape:'html'}" data-ajax="false" title="{$paymenttext}" method="post">
	<input type="hidden" name="{$paymentcode}" value="true"/>
</form>
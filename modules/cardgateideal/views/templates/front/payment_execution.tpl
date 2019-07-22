
{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}{l s='iDEAL payment' mod='cardgateideal'}{/capture}

<h2>{l s='Order summary' mod='cardgateideal'}</h2>
<h2>{l s='Pay with' mod='cardgateideal'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<h3>{l s='iDEAL payment' mod='cardgateideal'}</h3>
<form name="cardgateideal_form" id="cardgateideal_form" action="{$_url}" method="post">
<p>
	<img src="{$imageurl}" alt="iDEAL" style="float:left; margin: 0px 10px 5px 0px;" />
	{l s='You have chosen to pay with iDEAL.' mod='cardgateideal'}
	</br>
	{l s='Choose your bank:' mod='cardgateideal'}
	<select name="suboption" id="suboption">
		{foreach from=$issuers key=id item=issuer}
			<option value="{$id}">{$issuer}</option>
		{/foreach}	
	</select>
        
        {foreach from=$fields key=name item=value}
            <input type="hidden" name="{$name}" value='{$value}'>
	{/foreach}
</p>

<p class="cart_navigation" id="cart_navigation">
	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}" class="button_large">{l s='Other payment methods' mod='cardgateideal'}</a>
	<input type="submit" value="{l s='I confirm my order' mod='cardgateideal'}" id="cardgatesubmit" class="exclusive_large" />
</p>
</form>

<script type="text/javascript">
	var mess_cardgate_error = "{l s='Choose your bank!' mod='cardgateideal' js=1}";
	{literal}
		$(document).ready(function(){

			$('#cardgatesubmit').click(function()
				{
				if ($('#suboption').val() == '0')
				{
					alert(mess_cardgate_error);
				}
				else
				{
					$('#cardgateideal_form').submit();
				}
				return false;
			});
		});
	{/literal}
</script>

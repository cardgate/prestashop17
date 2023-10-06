![CardGate](https://cdn.curopayments.net/thumb/200/logos/cardgate.png)

# CardGate module for PrestaShop 1.7+

[![Build Status](https://travis-ci.org/cardgate/prestashop17.svg?branch=master)](https://travis-ci.org/cardgate/prestashop17)

## Support

This module supports PrestaShop version **1.7 - 1.8.x**

## Preparation

The usage of this module requires that you have obtained CardGate RESTful API credentials.  
Please visit [My CardGate](https://my.cardgate.com/) and retrieve your credentials, or contact your accountmanager.

## Installation

1. In PrestaShop versions 1.7.7.2 and higher, the **Modules Catalog** is no longer displayed. In that case you need to install the [PrestaShop MBO](https://github.com/PrestaShopCorp/ps_mbo) first, which will make the **CardGate modules** visible in your **Modules Marketplace**. (There are different versions for PrestaShop 1.7.x and Prestashop 1.8.x)
2. Download and unzip the most recent [cardgate.zip](https://github.com/cardgate/prestashop17/releases) file on your desktop.
3. Using FTP, upload and merge the **complete modules** folder with the **modules** folder in the **root** folder of your website.
4. In your PrestaShop **admin**, go to **Modules**, **Modules Marketplace**, search for **CardGate**.
5. Always install the **CardGate Bank Common** module, and the CardGate payment method modules you wish to use.
6. Go to **Modules**, **Module Manager**, where you can see the installed CardGate modules in the **Payment** Section.

## Configuration

1. Log in, to the **admin** of your PrestaShop.
2. In the left menu, at **Modules**, select **Module Manager**, and search for **CardGate**.
3. Click on the **Configure** button of the **CardGate Bank Common** module.
4. Enter the **site ID**, and the **hash key** which you can find at **Sites** on [My CardGate](https://my.cardgate.com/).
5. Enter the **merchant ID** and **API Key**, which has been given to you by your CardGate account manager.
6. Now click on **Save**. 
7. Check that the **CardGate payment methods** you wish to use are present in the **Payment** section.
8. When you are **finished testing** the **CardGate Bank Common** module, make sure that you switch the **Mode** from **Test Mode** to **Live Mode** and save it (**Save**).

## Requirements

No further requirements.

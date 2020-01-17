![CardGate](https://cdn.curopayments.net/thumb/200/logos/cardgate.png)

# CardGate module for PrestaShop 1.7+

[![Build Status](https://travis-ci.org/cardgate/opencart3.svg?branch=master)](https://travis-ci.org/cardgate/prestashop17)

## Support

This module supports PrestaShop version **1.7.x** .

## Preparation

The usage of this module requires that you have obtained CardGate RESTful API credentials.  
Please visit [My CardGate](https://my.cardgate.com/) and retrieve your credentials, or contact your accountmanager.

## Installation

1. Download and unzip the most recent [source code](https://github.com/cardgate/prestashop17/releases) file on your desktop.

2. Unzip the file, and, using FTP, upload the **contents** of the **zip file** to the **root** folder of your website.

3. In your PrestaShop **admin**, go to the **Modules** tab and select **Installed Modules**.

4. Check the version of the installed CardGate modules.

## Configuration

1. Log in, to the **admin** of your PrestaShop.

2. In the left menu, at **Modules**, select **Modules & Services**, and search for **CardGate**.

3. Install the **CardGate Bank Common** module, and the **CardGate payment modules** you wish to use.

4. Click on the installed Modules, and click on the **Configure** button of the **CardGate Bank Common** module.

5. Enter the **site ID**, and the **hash key** which you can find at **Sites** on [My CardGate](https://my.cardgate.com/).

6. Enter the **merchant ID** and **API Key**, which has been given to you by your CardGate account manager.

5. Optionally you can fill in extra payment costs for specific payment methods.

6. Now click on **Save**.

7. When you are **finished testing** the **CardGate Bank Common** module, make sure that you switch  
   the **configuration** from **Test Mode** to **Live Mode** and save it (**Save**).

## Requirements

No further requirements.

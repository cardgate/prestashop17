![CardGate](https://cdn.curopayments.net/thumb/200/logos/cardgate.png)

# CardGate module voor PrestaShop 1.7+

[![Total Downloads](https://img.shields.io/packagist/dt/cardgate/prestashop17.svg)](https://packagist.org/packages/cardgate/prestashop17)
[![Latest Version](https://img.shields.io/packagist/v/cardgate/prestashop17.svg)](https://github.com/cardgate/prestashop17/releases)
[![Build Status](https://travis-ci.org/cardgate/prestashop17.svg?branch=master)](https://travis-ci.org/cardgate/prestashop17)

## Support

Deze plug-in is geschikt voor PrestaShop versie **1.7.x**.

## Voorbereiding

Voor het gebruik van deze module zijn CardGate RESTful gegevens nodig.  
Bezoek hiervoor [Mijn CardGate](https://my.cardgate.com/) en haal daar je  
RESTful API gebruikersnaam en wachtwoord op, of neem contact op met je accountmanager.  

## Installatie

1. Download en unzip het **cardgate.zip** bestand op je bureaublad.

2. Plaats de inhoud van de **cardgate** map via FTP in de **modules** map van je website.

3. In je PrestaShop **admin**, ga naar de **Modules** tab en selecteer **Geinstalleerde Modules**.

4. Controleer het versienummer van de geinstalleerde CardGate modules.

## Configuratie

1. Log in op het <b>admin</b> gedeelte van je PrestaShop.  

2. In het linkermenu, bij **Modules**, kies **Modules & Services**, en in het zoekvenster, zoek op CardGate.

3. Installeer de **CardGate Bank algemeen** module en de **CardGate betaalmodules** die u wilt gaan gebruiken.

4. Klik op geinstalleerde modules, en klik op de **Configure** knop van de **CardGate Bank Algemeen** module.

5. Vul de **Site ID<** en de **Hash Key (Codeersleutel)** in, deze kun je vinden bij **Sites** op [Mijn CardGate](https://my.cardgate.com/).

6. Vul de **Merchant ID** en **Merchant API Key** in zoals je die gekregen hebt van je CardGate Account manager.

5. Vul eventueel extra betaalkosten in voor specifieke betaalmethodes.

6. Klik nu op **Opslaan**.

7. Zorg ervoor dat je na het testen bij de **CardGate Bank algemeen** module de **Configuratie** omschakelt van  
   **Test Mode** naar **Live mode** en sla het op (**Save**).

## Vereisten

Geen verdere vereisten.

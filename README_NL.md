![CardGate](https://cdn.curopayments.net/thumb/200/logos/cardgate.png)

# CardGate module voor PrestaShop 1.7+

[![Build Status](https://travis-ci.org/cardgate/prestashop17.svg?branch=master)](https://travis-ci.org/cardgate/prestashop17)

## Support

Deze module is geschikt voor PrestaShop versie **1.7 - 1.8.x**

## Voorbereiding

Voor het gebruik van deze module zijn CardGate RESTful gegevens nodig.  
Bezoek hiervoor [Mijn CardGate](https://my.cardgate.com/) en haal daar je gegevens op,  
of neem contact op met je accountmanager.  

## Installatie

1. Download en unzip de meest recente [cardgate.zip](https://github.com/cardgate/prestashop17/releases/) op je bureaublad.

2. Upload de **complete modules** map via FTP in de **root** map van je website.

3. In je PrestaShop **admin**, ga naar **Modules**, **Module Catalog** en **Installeer** alle CardGate modules. **NB:** In Prestashop versies 1.7.7.2 en hoger is de **Modules Catalog** niet langer zichtbaar. In dat geval moet je eerst de [PrestaShop MBO](https://github.com/PrestaShopCorp/ps_mbo) installeren, die de **Catalog** zichtbaar zal maken in je **Modules Marketplace.** 

4. Ga naar **Modules**, **Module Manager**, waar de CardGate modules zichtbaar moeten zijn in de **Payment** sectie.

## Configuratie

1. Log in op het <b>admin</b> gedeelte van je PrestaShop.  

2. In het linkermenu, bij **Modules**, kies **Modules & Services**, en in het zoekvenster, zoek op **CardGate**.

3. Klik op de **Configureer** knop van de **CardGate Bank algemeen** module.

4. Vul de **site ID** en de **hash key** in, deze kun je vinden bij **Sites** op [Mijn CardGate](https://my.cardgate.com/).

5. Vul de **merchant ID** en **API key** in zoals je die gekregen hebt van je CardGate Account manager.

6. Vul eventueel extra betaalkosten in voor specifieke betaalmethodes.

7. Klik nu op **Opslaan**.

8. Zorg ervoor dat je na het testen bij de **CardGate Bank algemeen** module de **Mode** omschakelt van  
   **Test Mode** naar **Live mode** en sla het op (**Save**).

## Vereisten

Geen verdere vereisten.

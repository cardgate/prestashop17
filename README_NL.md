![CardGate](https://cdn.curopayments.net/thumb/200/logos/cardgate.png)

# CardGate module voor PrestaShop 1.7+

[![Build Status](https://travis-ci.org/cardgate/prestashop17.svg?branch=master)](https://travis-ci.org/cardgate/prestashop17)

## Support

Deze module is geschikt voor PrestaShop versie **1.7 - 8.1+**

## Voorbereiding

Voor het gebruik van deze module zijn CardGate RESTful gegevens nodig.  
Bezoek hiervoor [Mijn CardGate](https://my.cardgate.com/) en haal daar je gegevens op,  
of neem contact op met je accountmanager.  

## Installatie

1. In PrestaShop versie 1.7.7.2 en hoger wordt de **Module catalog** niet langer weergegeven. In dat geval moet u eerst de [PrestaShop MBO](https://github.com/PrestaShopCorp/ps_mbo) installeren, waardoor de **CardGate-modules** zichtbaar worden in uw **Modules Marketplace**. (Er zijn verschillende versies voor PrestaShop 1.7.x en Prestashop 1.8.x)
2. Download en pak het meest recente [cardgate.zip](https://github.com/cardgate/prestashop17/releases) bestand uit op uw bureaublad.
3. Gebruik FTP om de volledige map **modules** te uploaden en samen te voegen met de map **modules** in de **root** van uw website.
4. Ga in uw PrestaShop **admin** naar **Modules**, **Modules Marketplace**, zoek op **CardGate**.
5. Installeer altijd de module **CardGate Bank Common** en de modules voor de CardGate betaalmethoden die u wilt gebruiken.
6. Ga naar **Modules**, **Module Manager**, waar u de geïnstalleerde CardGate-modules kunt zien in de sectie **Betaling**.
## Configuratie

1. Log in op de **admin** van uw PrestaShop.
2. Selecteer in het linkermenu bij **Modules**, **Module Manager** en zoek op **CardGate**.
3. Klik op de knop **Configureren** van de module **CardGate Bank Common**.
4. Voer de **site ID** in, en de **hash key** die u kunt vinden bij **Sites** op [Mijn CardGate](https://my.cardgate.com/).
5. Voer de **merchant ID** en **API key** in, die u van uw CardGate-accountmanager heeft gekregen.
6. Klik nu op **Opslaan**.
7. Controleer of de **CardGate-betaalmethoden** die u wilt gebruiken aanwezig zijn in het gedeelte **Betaling**.
8. Wanneer u **klaar bent met het testen** van de **CardGate Bank Common**-module, zorg er dan voor dat u de **Modus** van **Testmodus** naar **Live-modus** schakelt en deze opslaat ( **Save**).
## Vereisten

Geen verdere vereisten.

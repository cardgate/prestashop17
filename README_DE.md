![CardGate](https://cdn.curopayments.net/thumb/200/logos/cardgate.png)

# CardGate Modul für PrestaShop 1.7+, 8.1+

[![Build Status](https://travis-ci.org/cardgate/prestashop17.svg?branch=master)](https://travis-ci.org/cardgate/prestashop17)

## Support

Dieses Modul is geeignet für PrestaShop version **1.7 - 8.1+**

## Vorbereitung

Um dieses Modul zu verwenden sind Zugangsdate zur CardGate RESTful API notwendig.  
Gehen zu [**Mein CardGate**](https://my.cardgate.com/) und fragen Sie Ihre Zugangsdaten an, oder kontaktieren Sie Ihren Accountmanager.

## Installation

1. In den PrestaShop-Versionen 1.7.7.2 und höher wird der **Modulkatalog** nicht mehr angezeigt. In diesem Fall müssen Sie zuerst das [PrestaShop MBO](https://github.com/PrestaShopCorp/ps_mbo) installieren, wodurch die **CardGate-Module** in Ihrem **Modul-Marktplatz** sichtbar werden. (Es gibt verschiedene Versionen für PrestaShop 1.7.x und Prestashop 1.8.x)
2. Downloaden und entpacken Sie den aktuellsten [cardgate.zip](https://github.com/cardgate/prestashop17/releases) Datei auf Ihrem Desktop.
3. Laden Sie über FTP den gesamten Ordner **modules** hoch und führen Sie ihn mit dem Ordner **modules** im **Stammordner** Ihrer Website zusammen.
4. Gehen Sie in Ihrem PrestaShop **Administrator** zu **Module**, **Modules Marketplace** und suchen Sie nach **CardGate**.
5. Installieren Sie immer das **CardGate Bank Common**-Modul und die CardGate-Zahlungsmethodenmodule, die Sie verwenden möchten.
6. Gehen Sie zu **Module**, **Modulmanager**, wo Sie die installierten CardGate-Module im Abschnitt **Zahlung** sehen können.

## Configuration

1. Gehen Sie zum Ihrem **Prestashop-Adminbereich**.
2. Selektieren Sie in dem linken Menü **Module**, wählen Sie **Module Manager** aus, und klicken Sie dann auf **CardGate**.
3. Klicken Sie auf die Schaltfläche **Konfigurieren** des Moduls **CardGate Bank Common**. 
4. Füllen Sie nun die **Site ID** und den **Hash Key** ein welche Sie unter **Webseite** bei [**Mein CardGate**](https://my.cardgate.com/) finden können. 
5. Füllen Sie die **merchant ID** und den **API key** ein, den Sie von CardGate empfangen haben.
6. Klicken Sie nun auf **speichern**.
7. Überprüfen Sie, ob die **CardGate-Zahlungsmethoden**, die Sie verwenden möchten, im Abschnitt **Zahlung** vorhanden sind.
8. Wenn Sie mit dem **Testen abgeschlossen** haben schalten Sie dann von  
   dem **Test-Modus** in den **Live mode** und klicken Sie auf **Speichern**.

## Anforderungen

Keine weiteren Anforderungen.

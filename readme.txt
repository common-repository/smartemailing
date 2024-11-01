=== SmartEmailing.cz ===
Contributors: SmartEmailing
Tags: SmartEmailing
Requires at least: 6.0
Tested up to: 6.2
Stable Tag: 2.2.0
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Snadný způsob jak propojit svůj web se svým účtem ve SmartEmailingu pro sběr e-mailových adres pomocí webového formuláře.

== Obecný popis ==

WP plugin od SmartEmailing je jednoduchý způsob, jak můžete svůj web na WordPressu propojit se svým účtem ve SmartEmailingu pomocí webového formuláře.
Můžete přidávat formuláře do příspěvků, webové stránky, postranních panelů a dalších widget oblastí. Zároveň plugin umožňuje vkládání počítadla ke každému formuláři, pro viditelný počet stažení nebo vyplnění formuláře na vašich stránkách. Zároveň můžete přizpůsobovat vzhled formuláře vámi aktuálně používané WP šablony nebo ponechat vzhled formuláře ze SmartEmailingu. Integrace je velmi snadná pomocí API klíče a uživatelksého jména ze SmartEmailingu.

- Plugin je zcela ZDARMA
- Možnost vkládání libovolného počtu formulářů na web
- Možnost volby vzhledu formuláře (ponechat stávající ze SmartEmailingu nebo přizpůsobit vzhled WP šabloně)
- Možnost barevně definovat text
- Možnost definovat velikost textu
- Možnost použití prvku pro počet stažení ( např.: ebook, mp3, checklist...) nebo vyplnění formuláře
- Automatická ochrana formulářů proti spambotům
- Kontrola formulářů pro překlep v e-mailové adrese (např. xxx@gmail.cz místo @gmail.com)
- GDPR
- Double opt-in

== Instalace ==

1. Nahrajte soubory pluginů do adresáře `/wp-content/plugins/plugin-name`, nebo nainstalujte plugin přímo na obrazovce pluginů WordPress.
2. Aktivujte plugin prostřednictvím obrazovky ‘Plugins’ ve WordPressu
3. Aktivní plugin se Vám zobrazí v bočním menu, kde provedete jeho propojení se SmartEmailingem.

== FAQ ==

= Je nutné mít účet ve SmartEmailingu pro použití pluginu? =
Ano, je to nezbytné. Bez účtu jak zdarma nebo placeného nebudete moct propojit svůj účet (webové formuláře) s WordPressem.

= Jak mohu plugin propojit se svým účtem ve SmartEmailingu? =
Náš plugin používá API klíč, který umožňuje vašemu webu komunikovat s vaším účtem. Po zadání API klíče a uživatelského jména (e-mailové adresy), na kterou máte účet založen ve SmartEmailingu do nastavení pluginu jsou vaše stránky připojeny k vašemu účtu.

= Mají formuláře nějakou ochranu proti spambotům? =
U webových formulářů používáme umělou neuronovou síť, která na základě analýzy dat odeslaných z formuláře dokáže detekovat spamboty. Spambotům a podezřelým kontaktům zobrazíme po odeslání formuláře reCAPTCHu.

= Jak je to s GDPR včetně double opt-inu? =
Kompletní nastavení GDPR (právních účelů, zaškrtávacíh polí atd.) včetně double opt-inu nastavujete přímo ve SmartEmailingu u každého vytvořeného formuláře zvlášť.

= Mohu změnit vzhled formulářů? =
V nastavení pluginu si můžete zvolit, jestli chcete ponechat vzhled formulářů ze SmartEmailingu nebo chcete převzít vzhled z WordPressu (aktuální vámi používané šablony)

== Changelog ==
*2.2.0*
- Add option to import orders only for SmartEmailing contacts
- Add details to order items and orders
- Add birthdate field setting

*2.1.0*
- add HPOS support
- update dependencies

*2.0.4*
- fix logo in admin

*2.0.3*
- fix deploy

*2.0.2*
- Automatické ukládání GUID
- Potvrzení uložení v nastavení

*2.0.0*
- Přidání podpory WooCommerce
- Kompletní přepracování pluginu

*1.6.6*
- Windows server fix

*1.0.0*
- spuštění WordPress modulu SmartEmailing.cz - základní verze

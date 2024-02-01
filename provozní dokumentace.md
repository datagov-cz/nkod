# Provozní dokumentace Národního katalogu otevřených dat

## Seznam pojmů a zkratek

<dl>
    <dt>DCAT-AP-CZ</dt>
    <dd>Otevřená formální norma "Rozhraní katalogů otevřených dat: DCAT-AP-CZ" založená na evropském standardu DCAT-AP, který je založen na webovém standardu DCAT</dd>
    <dt>ISDS</dt>
    <dd>Informační systém datových schránek</dd>
    <dt>LKOD</dt>
    <dd>Lokální katalog otevřených dat</dd>
    <dt>NKOD</dt>
    <dd>Národní katalog otevřených dat</dd>
    <dt>POD</dt>
    <dd>Portál otevřených dat</dd>
    <dt>RDF</dt>
    <dd>Resource Description Framework - datový model využívaný NKOD</dd>
    <dt>SPARQL</dt>
    <dd>Dotazovací jazyk nad daty v RDF</dd>
</dl>

## Popis funkce systému a jednotlivých modulů

Viz [aplikační dokumentace](aplikační%20dokumentace.md).

## Nasazení systému
Instalace systému viz [instalační dokumentace](instalační%20dokumentace.md).

NKOD je nasazen ve 2 instancích, produkční a testovací, v prostředí Microsoft Azure, tenantu DIA.
Škálován je na práci s až 200 000 datovými sadami.

### Produkční prostředí
Produkční prostředí zahrnuje tyto virtuální stroje
- `NKOD-PROD-DB` odpovídající **NKOD-DB** z instalační dokumentace, 8 vCPUs, 28 GiB RAM, 1TB úložiště
- `NKOD-PROD-ETL` odpovídající **NKOD-ETL** z instalační dokumentace, 8 vCPUs, 28 GiB RAM, 4TB úložiště
- `NKOD-PROD-FE` odpovídající **NKOD-FRONTEND** z instalační dokumentace, 4 vCPUs, 8 GiB RAM, 4TB úložiště

Běží na adrese https://data.gov.cz, přijímá registrace z datové schránky `m3hp53v` a případné testovací záznamy z `main` branche https://github.com/datagov-cz/nkod-test.
Nastaveno na harvestaci denně, v 22:00.

Na `NKOD-PROD-FE` kromě frontendu NKOD běží také POD, který je klonem stránek spravovaných v `main` branchi https://github.com/datagov-cz/data.gov.cz, a dále hostuje otevřené formální normy z `master` branche https://github.com/datagov-cz/otevrene-formalni-normy na https://ofn.gov.cz.
Obě další části se stahují na základě obsluhy GitHub webhooku.
Navíc v produkčním prostředí běží instance Dokuwiki (v `/data/dokuwiki`) běžící na `https://opendata.gov.cz`.

### Testovací prostředí
Testovací prostředí zahrnuje tyto virtuální stroje
- `NKOD-TEST-DB` odpovídající **NKOD-DB** z instalační dokumentace, 8 vCPUs, 28 GiB RAM, 1TB úložiště
- `NKOD-TEST-ETL` odpovídající **NKOD-ETL** z instalační dokumentace, 8 vCPUs, 28 GiB RAM, 4TB úložiště
- `NKOD-TEST-FE` odpovídající **NKOD-FRONTEND** z instalační dokumentace, 4 vCPUs, 8 GiB RAM, 4TB úložiště
  
Běží na adrese https://pod-test.dia.gov.cz, přijímá registrace z **testovací** datové schránky `vrxgfvc` a testovací záznamy z `test` branche https://github.com/datagov-cz/nkod-test.
Nastaveno na harvestaci každé 3 hodiny.

Na `NKOD-TEST-FE` kromě frontendu NKOD běží také pracovní verze POD, která je klonem stránek spravovaných v `develop` branchi https://github.com/datagov-cz/data.gov.cz, a dále hostuje otevřené formální normy z `develop` branche https://github.com/datagov-cz/otevrene-formalni-normy na https://pod-test.dia.gov.cz/otevřené-formální-normy/, např. https://pod-test.dia.gov.cz/otevřené-formální-normy/základní-datové-typy/2020-07-01/ .
Obě další části se stahují na základě obsluhy GitHub webhooku.

## Údržba systému
Je třeba zejména na **NKOD-ETL** monitorovat místo na disku, které může dojít kvůli velikosti logů.
Je tedy třeba např. jednou za měsíc promazat záznamy o proběhlých procesech, tj. adresář `/data/lp/etl/storage/working` a server restartovat.

## Možné chybové stavy a jejich řešení
Může proces harvestace NKOD selhat z následujících očekávatelných důvodů:
1. Selže pipeline `07.1 Harvestace LKOD a formulářů, aktualizace uživatelského rozhraní`, protože systém datových schránek má výpadek. Ten obvykle trvá jeden den, tedy ten den nebude NKOD harvestován. Není nutný další zásah.
2. Selže pipeline `08.1 Nahrát NKOD do SPARQL endpointu a spustit pipeliny pro kvalitu` protože spadne instance databáze Virtuoso. Pak je třeba celý server **NKOD-DB** restartovat a následně restartovat pipeline.
3. Selže pipeline `07.1 Harvestace LKOD a formulářů, aktualizace uživatelského rozhraní` a `08.1 Nahrát NKOD do SPARQL endpointu a spustit pipeliny pro kvalitu` na chybu `502 Bad Gateway` při aktualizaci LDF serveru nebo restartu Virtuosa. Zřejmě spadla databáze Virtuoso a s ní i PHP server obsluhující webhooky. Je třeba restartovat **NKOD-DB** a znovu spustit pipeline `07 Spouštěcí pipeline` nebo počkat na další den harvestace.

## Nároky na personál

Na provozovatele NKOD jsou kladeny následující nároky:
- Administrace Linuxových systémů
    - systemd služby
    - cron
    - bash
    - docker
- Uživatelská znalost Microsoft Azure prostředí pro virtualizaci
- Konfigurace sítí TCP/IP
- Konfigurace HTTP(S) webových serverů (nginx) včetně správy SSL certifikátů
- GitHub
- PHP (použito v obsluze GitHub webhooků)
- Administrátorská znalost [LinkedPipes ETL], [LinkedPipes DCAT-AP Viewer], [LinkedPipes DCAT-AP Forms], [NKOD-ISDS], [OpenLink Virtuoso Open-Source], [GraphQL server NKOD]
- Uživatelská znalost [LinkedPipes ETL]

[LinkedPipes DCAT-AP Viewer]: https://github.com/datagov-cz/dcat-ap-viewer "LinkedPipes DCAT-AP Viewer"
[Aplikace]: https://github.com/datagov-cz/nkod-registrovane-aplikace "NKOD registrované aplikace"
[LinkedPipes DCAT-AP Forms]: https://github.com/datagov-cz/dcat-ap-forms "LinkedPipes DCAT-AP Forms"
[LinkedPipes ETL]: https://github.com/datagov-cz/etl "LinkedPipes ETL"
[NKOD-ISDS]: https://github.com/datagov-cz/nkod-isds "NKOD-ISDS"
[OpenLink Virtuoso Open-Source]: https://github.com/datagov-cz/virtuoso-opensource "OpenLink Virtuoso Open-Source"
[Linked Data Fragments server]: https://github.com/datagov-cz/Server.js "Linked Data Fragments server"
[GraphQL server NKOD]: https://github.com/datagov-cz/nkod-graphql "GraphQL server NKOD"
[Rozhraní katalogů otevřených dat: DCAT-AP-CZ]: https://ofn.gov.cz/rozhraní-katalogů-otevřených-dat/2021-01-11/ "Otevřená formální norma Rozhraní katalogů otevřených dat: DCAT-AP-CZ"
[POD]: https://data.gov.cz "Portál otevřených dat"
[Oficiální portál evropských dat]: https://data.europa.eu "Oficiální portál evropských dat"
[SPARQL endpoint]: https://data.gov.cz/sparql "SPARQL endpoint NKOD"
[National Single Information Point (NSIP)]: https://data.europa.eu/data/datasets?superCatalogue=erpd&locale=cs&catalog=nsip-cz&page=1 "Czech National Single Information Point"
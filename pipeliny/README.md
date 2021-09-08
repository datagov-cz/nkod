# Dokumentace pipeline v LinkedPipes ETL
Národní katalog otevřených dat je realizován v [LinkedPipes ETL](https://etl.linkedpipes.com) pomocí 16 pipeline, které jsou v této sekci popsány.

## Přípravné pipeline

Přípravné pipeline se spouští při inicializaci instance NKOD a pak v případě aktualizace externích číselníků či nutnosti aktualizace datové sady Seznam OVM (orgánů veřejné moci).

### 01 Cache externích zdrojů
[01 Cache externích zdrojů](01%20Cache%20externích%20zdrojů.jsonld) - stáhne evropské číselníky, použité pomocné dokumenty z Google Drive a IRI objektů z RÚIAN z instance SPARQL endpointu provozované na MFF UK a uloží je do místní cache ve filesystému.
Ostatní pipeline pak používají tuto cache.
Jedná se o omezení závislostí na externích datových zdrojích v následném každodenním běhu NKOD.
Pokud se zdrojové číselníky změní, je vhodné cache přegenerovat.
![Screenshot: 01 Cache externích zdrojů](screenshoty/01%20Cache%20externích%20zdrojů.webp)

### 02 Seznam OVM
[02 Seznam OVM](02%20Seznam%20OVM.jsonld) - převede do RDF datovou sadu [Seznam OVM](https://www.czechpoint.cz/spravadat/ovm/datafile.do?format=xml&service=seznamovm).
Výsledek opět uloží do cache.
Seznam OVM, konkrétně položka `kód OVM`, se používá pro kontrolu existence datových schránek a přiřazení registrovaných datových sad a katalogů registrovaných před updatem NKOD v roce 2017 správnému poskytovateli dat.
![Screenshot: 02 Seznam OVM](screenshoty/02%20Seznam%20OVM.webp)
U novějších registrací je jako základ IRI poskytovatelů použito jejich IČO ze seznamu datových schránek a `kód OVM` se dále již nepoužívá.

### 03 Konverze záznamů z PVS do DCAT-AP
[03 Konverze záznamů z PVS do DCAT-AP](03%20Konverze%20záznamů%20z%20PVS%20do%20DCAT-AP.jsonld) - Zkonvertuje staré záznamy ([měsíční indexy](https://opendata.gov.cz/_media/rdz-monthindex-2017-12-29.zip) a [rejstřík datových zdrojů](https://opendata.gov.cz/_media/rdz-data-2017-12-29.zip)) zaslané do NKOD před rokem 2017, kdy byl NKOD součástí Portálu veřejné zprávy (PVS), do aktuálního formátu dle DCAT-AP.
![Screenshot: 03 Konverze záznamů z PVS do DCAT-AP](screenshoty/03%20Konverze%20záznamů%20z%20PVS%20do%20DCAT-AP.webp)

### 04 Nahrát externí data do SPARQL endpointu
[04 Nahrát externí data do SPARQL endpointu](04%20Nahrát%20externí%20data%20do%20SPARQL%20endpointu.jsonld) - Nahraje evropské číselníky a prvky RÚIAN z cache do SPARQL endpointu NKOD.
![Screenshot: 04 Nahrát externí data do SPARQL endpointu](screenshoty/04%20Nahrát%20externí%20data%20do%20SPARQL%20endpointu.webp)

### 05 Nahrát číselníky do Solr pro LP DCAT-AP Forms
[05 Nahrát číselníky do Solr pro LP DCAT-AP Forms](05%20Nahrát%20číselníky%20do%20Solr%20pro%20LP%20DCAT-AP%20Forms.jsonld) - Nahraje evropské číselníky a prvky RÚIAN do Apache Solr pro autocomplete v LP DCAT-AP Forms, tj. formuláře NKOD.
![Screenshot: 05 Nahrát číselníky do Solr pro LP DCAT-AP Forms](screenshoty/05%20Nahrát%20číselníky%20do%20Solr%20pro%20LP%20DCAT-AP%20Forms.webp)

### 06 Nahrát čísleníky do CouchDB pro LP DCAT-AP Viewer
[Codelists to CouchDB for LP-DAV](06%20Nahrát%20čísleníky%20do%20CouchDB%20pro%20LP%20DCAT-AP%20Viewer.jsonld) - Nahraje evropské číselníky a prvky RÚIAN do Apache CouchDB pro LP DCAT-AP Viewer, tj. uživatelské rozhraní NKOD.
![Screenshot: 06 Nahrát čísleníky do CouchDB pro LP DCAT-AP Viewer](screenshoty/06%20Nahrát%20čísleníky%20do%20CouchDB%20pro%20LP%20DCAT-AP%20Viewer.webp)

## Pravidelně spouštěné pipeline

Tyto pipeline běží v pravidelných intervalech a zajišťují aktualizaci obsahu NKOD.
`07 Harvestace LKOD a formulářů, aktualizace uživatelského rozhraní` se spouští externě pomocí API LinkedPipes ETL z plánovače `cron`.

### 07 Harvestace LKOD a formulářů, aktualizace uživatelského rozhraní
[07 Harvestace LKOD a formulářů, aktualizace uživatelského rozhraní](07%20Harvestace%20LKOD%20a%20formulářů,%20aktualizace%20uživatelského%20rozhraní.jsonld) - Hlavní pipeline zajišťující zpracování záznamů z registračních formulářů a harvestaci lokálních katalogů otevřených dat (LKODů), aktuálně ve 3 provedeních dle [Otevřené formální normy Rozhraní katalogů otevřených dat: DCAT-AP-CZ](https://ofn.gov.cz/rozhraní-katalogů-otevřených-dat/2021-01-11/), tj. CKAN API, DCAT-AP SPARQL Endpoint a DCAT-AP Dokumenty.
Má také režim pro spuštění v prostředí testovacích datových schránek.
Po dokončení harvestace a transformace dat data nahrává do Apache CouchDB a Apache Solr pro LP-DAV, přidává metadata pro samotný NKOD, generuje RDF, CSV, [JSON](https://data.gov.cz/soubor/nkod.json) a [HDT](https://data.gov.cz/soubor/nkod.hdt) dumpy, spouští znovunahrání HDT dumpu do [Linked Data Fragments endpointu](https://data.gov.cz/ldf/nkod-ldf) a JSON dumpu do [GraphQL endpointu](https://data.gov.cz/graphql), zasílá základní informace na Slack MV ČR a spouští následující pipeline (`08.1`).
![Screenshot: 07 Harvestace LKOD a formulářů, aktualizace uživatelského rozhraní](screenshoty/07%20Harvestace%20LKOD%20a%20formulářů,%20aktualizace%20uživatelského%20rozhraní.webp)

### 08.1 Nahrát NKOD do SPARQL endpointu a spustit pipeliny pro kvalitu
[08.1 Nahrát NKOD do SPARQL endpointu a spustit pipeliny pro kvalitu](08.1%20Nahrát%20NKOD%20do%20SPARQL%20endpointu%20a%20spustit%20pipeliny%20pro%20kvalitu.jsonld) - Restartuje Virtuoso SPARQL endpoint, vyčistí ho, nahraje čerstvé RDF dumpy a spustí 3 navazující pipeline (`09`, `10`, `11`) pro měření datové kvality a jednu (`08.2`) spouštějící další návazné pipeline.
Tato pipeline je spouštěna automaticky z předchozí pipeline (`07`).
![Screenshot: 08.1 Nahrát NKOD do SPARQL endpointu a spustit pipeliny pro kvalitu](screenshoty/08.1%20Nahrát%20NKOD%20do%20SPARQL%20endpointu%20a%20spustit%20pipeliny%20pro%20kvalitu.webp)

### 09 Statistika dostupnosti distribucí, schémat, podmínek užití a dokumentace - HEAD
[09 Statistika dostupnosti distribucí, schémat, podmínek užití a dokumentace - HEAD](09%20Statistika%20dostupnosti%20distribucí,%20schémat,%20podmínek%20užití%20a%20dokumentace%20-%20HEAD.jsonld) - Zkontroluje dostupnost všech v NKOD zaregistrovaných URL pomocí `HTTP HEAD` požadavku.
Jednotlivé větve představují jednotlivé druhy URL registrované v NKOD, jako je například URL souboru ke stažení, URL dokumentace datové sady, atd.
Následně je informace o (ne)dostupnosti reprezentována pomocí [Data Quality Vocabulary](https://www.w3.org/TR/vocab-dqv/) a nahrána do SPARQL endpointu NKOD.
Agregované indikátory dostupnosti jsou uchovávány jako časová řada.
Neagregované indikátory jsou při každém běhu přepsány.
![Screenshot: 09 Statistika dostupnosti distribucí, schémat, podmínek užití a dokumentace - HEAD](screenshoty/09%20Statistika%20dostupnosti%20distribucí,%20schémat,%20podmínek%20užití%20a%20dokumentace%20-%20HEAD.webp)

### 10 Statistika dostupnosti distribucí, schémat, podmínek užití a dokumentace - CORS
[10 Statistika dostupnosti distribucí, schémat, podmínek užití a dokumentace - CORS](10%20Statistika%20dostupnosti%20distribucí,%20schémat,%20podmínek%20užití%20a%20dokumentace%20-%20CORS.jsonld) - Zkontroluje dostupnost techniky CORS na všech v NKOD zaregistrovaných URL pomocí `HTTP OPTIONS` požadavku.
Jednotlivé větve představují jednotlivé druhy URL registrované v NKOD, jako je například URL souboru ke stažení, URL dokumentace datové sady, atd.
Následně je informace o (ne)dostupnosti CORS reprezentována pomocí [Data Quality Vocabulary](https://www.w3.org/TR/vocab-dqv/) a nahrána do SPARQL endpointu NKOD.
Agregované indikátory dostupnosti jsou uchovávány jako časová řada.
Neagregované indikátory jsou při každém běhu přepsány.
![Screenshot: 10 Statistika dostupnosti distribucí, schémat, podmínek užití a dokumentace - CORS](screenshoty/10%20Statistika%20dostupnosti%20distribucí,%20schémat,%20podmínek%20užití%20a%20dokumentace%20-%20CORS.webp)

### 11 Kvalita metadatových záznamů v NKOD DQV
[11 Kvalita metadatových záznamů v NKOD DQV](11%20Kvalita%20metadatových%20záznamů%20v%20NKOD%20DQV.jsonld) - Počítá indikátory kvality metadatových záznamů v NKOD s ohledem na úplnost záznamu vůči [Otevřené formální normě Rozhraní katalogů otevřených dat: DCAT-AP-CZ](https://ofn.gov.cz/rozhraní-katalogů-otevřených-dat/2021-01-11/) a základní statistiky.
Agregované indikátory dostupnosti jsou uchovávány jako časová řada.
![Screenshot: 11 Kvalita metadatových záznamů v NKOD DQV](screenshoty/11%20Kvalita%20metadatových%20záznamů%20v%20NKOD%20DQV.webp)

### 12 Kombinované indikátory kvality
[12 Kombinované indikátory kvality](12%20Kombinované%20indikátory%20kvality.jsonld) - Počítá indikátory kvality kombinované z předchozích výsledků.
![Screenshot: 12 Kombinované indikátory kvality](screenshoty/12%20Kombinované%20indikátory%20kvality.webp)

### 13 Generování reportů v CSV
[13 Generování reportů v CSV](13%20Generování%20reportů%20v%20CSV.jsonld) - Z naměřených indikátorů generuje CSV soubory s přehledy, které jsou dostupné na stránce [statistik z NKOD](https://opendata.gov.cz/statistika:start) v Portálu otevřených dat.
![Screenshot: 13 Generování reportů v CSV](screenshoty/13%20Generování%20reportů%20v%20CSV.webp)

### 14 Generování zprávy o novinkách v NKOD
[14 Generování zprávy o novinkách v NKOD](14%20Generování%20zprávy%20o%20novinkách%20v%20NKOD.jsonld) - Porovnává aktuální stav NKOD s předchozím a generuje zprávu o rozdílech na Slack MV ČR a na stránku [statistik z NKOD](https://opendata.gov.cz/statistika:start).
![Screenshot: 14 Generování zprávy o novinkách v NKOD](screenshoty/14%20Generování%20zprávy%20o%20novinkách%20v%20NKOD.webp)

### 15 Vytvořit NKOD sitemapy
[15 Vytvořit NKOD sitemapy](15%20Vytvořit%20NKOD%20sitemapy.jsonld) - Tvoří sitemapy Portálu otevřených dat.
Dělení po 50 000 je kvůli [omezení Google na velikost jedné sitemapy](https://developers.google.com/search/docs/advanced/sitemaps/build-sitemap) a na základě horního odhadu počtu datových sad v NKOD.
V případě překročení hranice 150 000 datových sad je nutné přidat další sitemapy.
Ve spodní části je generována sitemapa Otevřených formálních norem https://ofn.gov.cz/sitemap.xml.
![Screenshot: 15 Vytvořit NKOD sitemapy](screenshoty/15%20Vytvořit%20NKOD%20sitemapy.webp)
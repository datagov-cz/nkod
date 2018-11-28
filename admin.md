# Administrátorská dokumentace Národního katalogu otevřených dat
Národní katalog otevřených dat se skládá z 5 propojených hlavních částí:
1. Prohlížeč datových sad ([LinkedPipes DCAT-AP Viewer](https://github.com/linkedpipes/dcat-ap-viewer))
2. Zadávací formuláře pro registraci datových sad a lokálních katalogů ([LinkedPipes DCAT-AP Forms](https://github.com/linkedpipes/dcat-ap-forms-vue))
3. Část zpracování dat z formulářů a harvestace lokálních katalogů ([LinkedPipes ETL](https://etl.linkedpipes.com/))
4. Vyzvedávač datových zpráv z ISDS ([NKOD-ISDS](https://github.com/opendata-mvcr/nkod-isds))
5. Databáze pro dotazování nad metadaty a poskytování metadat Evropskému datovému portálu ([OpenLink Virtuoso Open-Source](https://github.com/openlink/virtuoso-opensource))

Komunikace jednotlivých částí je ilustrována v diagramu komunikace.

Doporučený způsob nasazení vzhledem k minimalizaci vzájemného ovlivňování výkonu je na 3 oddělené stroje, pokud nepočítáme load-balancing a high-availability, což u NKOD zatím není potřeba:
1. **NKOD-DB**: Zde poběží samotná databáze
2. **NKOD-ETL**: Zde poběží harvestace lokálních katalogů a transformace dat, vyzvedávání zpráv z ISDS a cache externích číselníků a datových sad (seznam OVM)
3. **NKOD-FRONTEND**: Zde poběží webový server, zadávací formuláře, prohlížeč datových sad, úložiště datových souborů ke stažení

V případě potřeby lze řešení nasadit i jiným způsobem.

## Databáze
Pro zpřístupnění metadat NKOD pro dotazování je použita databáze [OpenLink Virtuoso Open-Source](https://github.com/openlink/virtuoso-opensource).
Pro prohlížení RDF dat v HTML podobě je v databázi nainstalován balíček `fct`.
Po nasazení na NKOD-DB běží její HTTP endpoint na portu `8890`, její SQL endpoint na portu `1111` a má přístup do lokálního souborového systému (`/data/upload`).
HTTP SPARQL endpoint pro dotazování (`https://data.gov.cz/sparql`) a SPARQL HTTP Graph Store Protocol endpoint pro dotazování (`https://data.gov.cz/sparql-graph-crud`) jsou veřejně přístupné přes reverse-proxy na NKOD-FRONTEND ([nginx](http://nginx.org/)).
Na NKOD-DB také běží SSH server, přes který jsou předávány RDF dumpy k nahrání do databáze.
SQL endpoint (`1111`) i celý HTTP endpoint (`8890`) včetně SPARQL endpointů pro zápis (`/sparql-auth` a `/sparql-graph-crud-auth`) jsou přístupné minimálně pro server NKOD-ETL.

### Postup instalace
V této sekci je popsán doporučený postup instalace databáze na stroji **NKOD-DB** s OS [Debian 9.6](https://www.debian.org/) či [Ubuntu](https://www.ubuntu.com/) 18.04 LTS nebo 18.10 ze zdrojových kódů z GitHubu.

1. Prerekvizity: `sudo apt-get install dpkg-dev build-essential autoconf automake libtool flex bison gperf gawk m4 make odbcinst libxml2-dev libssl1.0-dev libreadline-dev net-tools`
2. V adresáři `/opt`: Zdrojové kódy stable verze Virtuosa 
   1.`sudo git clone https://github.com/openlink/virtuoso-opensource.git -b stable/7`
   2.`sudo chown -R user:group virtuoso-opensource`
3. `cd /opt/virtuoso-opensource`
4. `./autogen.sh`
5. `./configure --prefix=/usr/local/ --with-readline --program-transform-name="s/isql/isql-v/" --with-layout=Debian --enable-fct-vad`
6. `make`
7. `sudo make install`

### Další kroky
1. v `/etc/init.d/virtuoso-opensource` je [init script](https://github.com/openlink/virtuoso-opensource/blob/develop/7/debian/virtuoso-opensource-7.init)
2. Databáze je nakonfigurována tak, že její data jsou v `/data/virtuoso/db01`
3. Konfigurace je pak v `/data/virtuoso/db01/virtuoso.ini`
4. Data pro upload budou v `/data/virtuoso/upload`
5. Automatické spouštění po startu systému je zajištěno symlinkem na init script z `/etc/rc4.d`
6. Je potřeba vytvořit uživatele `uploader`, který bude moci do `/data/virtuoso/upload` nahrávat data přes SSH/SCP

##  Transformace dat
V této sekci je popsán doporučený postup instalace stroje **NKOD-ETL** s OS [Debian 9.6](https://www.debian.org/) či [Ubuntu](https://www.ubuntu.com/) 18.04 LTS nebo 18.10.

### Prerekvizity
- [OpenJDK](http://jdk.java.net/11/) 11.0.1
- [Apache Maven](https://maven.apache.org/)
- Git
- [node.js](https://nodejs.org) 11.3.0
- [nginx](http://nginx.org/)
- [LinkedPipes ETL](https://etl.linkedpipes.com/)
- Vyzvedávátko zpráv z ISDS [NKOD-ISDS](https://github.com/opendata-mvcr/nkod-isds)

### Postup instalace
1. Instalace LinkedPipes ETL [dle návodu](https://etl.linkedpipes.com/installation/)
   1. Přidán uživatel `lpetl`, pod kterým bude nástroj běžet
   2. `/opt/lp`: `git clone https://github.com/linkedpipes/etl.git`
      1. `cd etl`
      2. `mvn install`
    3. v `/opt/lp/etl/deploy/frontend`: `npm i`
2. Konfigurace LinkedPipes ETL
   1. konfigurační soubor `/opt/lp/etl/deploy/configuration.properties`, je třeba upravit dle použitých URL
   2. data budou v `/data/lp/etl/working`
   3. logy budou v `/data/lp/etl/logs`
   4. frontend běží na `localhost:8080`
3. LinkedPipes ETL jako služby
   1. `/etc/systemd/system/lpetl-executor.service`
   2. `/etc/systemd/system/lpetl-executor-monitor.service`
   3. `/etc/systemd/system/lpetl-storage.service`
   4. `/etc/systemd/system/lpetl-frontend.service`
4. Nahrání 8x LP-ETL pipeline a nastavení přístupových údajů v šablonách
5. nginx zpřístupňuje `/data/cache` pro přístup z `localhost`
6. Instalace NKOD-ISDS
   1. v `/opt`: `git clone https://github.com/opendata-mvcr/nkod-isds.git`
   2. v `/opt/nkod-isds`: `mvn install`
   3. konfigurační soubor v `/opt/nkod-isds/dist/configuration.properties`

##  Frontend
V této sekci je popsán doporučený postup instalace stroje **NKOD-FRONTEND** s OS [Debian 9.6](https://www.debian.org/) či [Ubuntu](https://www.ubuntu.com/) 18.04 LTS nebo 18.10.

### Prerekvizity
- [OpenJDK](http://jdk.java.net/11/) 11.0.1
- Git
- [node.js](https://nodejs.org) 11.3.0
- [nginx](http://nginx.org/) 1.15.7
- certbot (letsencrypt.org) - pokud nebude jiný certifikát
- [Apache CouchDB](https://couchdb.apache.org/) 2.2.0
- [Apache Solr](http://lucene.apache.org/solr/) 7.5.0
- [LinkedPipes DCAT-AP Viewer](https://github.com/linkedpipes/dcat-ap-viewer)
- [LinkedPipes DCAT-AP Forms](https://github.com/linkedpipes/dcat-ap-forms-vue)

### Postup instalace
1. Běžným způsobem nainstalována Apache CouchDB, Apache Solr, OpenJDK, Node.js, nginx
2. Pro Apache Solr lze použít [návod pro zprovoznění](https://lucene.apache.org/solr/guide/7_5/taking-solr-to-production.html)
3. Instalace LinkedPipes DCAT-AP Forms
   1. zřídit uživatele `lpdaf`
   2. v `/opt/lp`: `git clone https://github.com/linkedpipes/dcat-ap-forms-vue.git`
   3. dále dle [návodu](https://github.com/linkedpipes/dcat-ap-forms-vue)
   4. Instalováno jako služba v `/etc/systemd/system/dcat-ap-forms.service`
4. Instalace LinkedPipes DCAT-AP Viewer
   1. zřídit uživatele lpdav
   2. v `/opt/lp`: `git clone https://github.com/linkedpipes/dcat-ap-viewer.git -b nkod`
   3. Dále dle [návodu](https://github.com/linkedpipes/dcat-ap-viewer)
   4. Instalováno jako služba v `/etc/systemd/system/dcat-ap-viewer.service`
5. Je potřeba vytvořit uživatele `uploader`, který bude moci do `/data/soubor` nahrávat data přes SSH/SCP
6. nginx reverse proxy na jednolivé části NKOD
   1. Konfigurace v `/etc/nginx`
   2. Reverse-proxy na SPARQL endpoint NKOD-DB
   3. Reverse-proxy na LinkedPipes DCAT-AP Forms
   4. Reverse-proxy na LinkedPipes DCAT-AP Viewer
   5. Reverse-proxy na LinkedPipes ETL na NKOD-ETL (zabezpečeno alespoň heslem)
   6. Reverse-proxy na NKOD-ISDS adresář se staženými datovými zprávami a jejich přílohami

## Příprava
Před naplánováním stahování z ISDS a spouštění pipeline v LP-ETL je třeba systém inicializovat zejména externími číselníky a datovými sadami. Je tedy třeba (nakonfigurovat a) spustit pipeliny v pořadí daném čísly v jejich značkách. Tedy:
1. `External Resources Cache` - stáhne evropské číselníky a použité pomocné dokumenty z Google Drive
2. `Seznam OVM` - stáhne aktuální datovou sadu Seznam OVM pro kontrolu datových schránek OVM
3. `EU MDR + EuroVoc Codelist load from cache to Virtuoso` - Nahraje evropské číselníky do databáze
4. `Codelists to CouchDB for LP-DAV` - Nahraje evropské číselníky do Apache CouchDB pro LP-DAV
5. `Codelists to Solr for LP DCAT-AP Forms` - Nahraje evropské číselníky do Apache Solr pro LP-DAF
6. `PVS to DCAT-AP conversion` - Provede úvodní transformaci původních dat ze starého NKOD do aktuálního formátu
 
Pak lze v cronu na **NKOD-ETL** v `/etc/cron.d/nkod` naplánovat spouštění NKOD-ISDS a pipeline `Forms and LKODs to NKOD` v LP-ETL

## Monitoring
Je třeba zejména na **NKOD-ETL** monitorovat místo na disku, které může dojít kvůli velikosti logů, pokud bude v produkčním prostředí poddimenzována velikost disku.
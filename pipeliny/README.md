# Dokumentace pipeline v LinkedPipes ETL
Národní katalog otevřených dat je realizován v [LinkedPipes ETL](https://etl.linkedpipes.com) pomocí [8 pipeline](pipeliny), které jsou v této sekci popsány.

## Přípravné pipeline

Přípravné pipeline se spouští při inicializaci instance NKOD a pak v případě aktualizace externích číselníků či nutnosti aktualizace datové sady Seznam OVM (orgánů veřejné moci).

### External Resources Cache
[External Resources Cache](External%20Resources%20Cache.jsonld) - stáhne evropské číselníky a použité pomocné dokumenty z Google Drive a uloží je do místní cache ve filesystému. Ostatní pipeline pak používají tuto cache. Pokud se zdrojové číselníky změní, je vhodné cache přegenerovat.
![Screenshot: External Resources Cache](screenshoty/External%20Resources%20Cache.png)

### Seznam OVM
[Seznam OVM](Seznam%20OVM.jsonld) - stáhne a převede do RDF aktuální datovou sadu [Seznam OVM](https://www.czechpoint.cz/spravadat/ovm/datafile.do?format=xml&service=seznamovm). Výsledek opět uloží do cache. Seznam OVM se používá pro kontrolu existence datových schránek a přiřazení registrovaných datových sad a katalogů správnému poskytovateli dat.
![Screenshot: Seznam OVM](screenshoty/Seznam%20OVM.png).

### EU MDR + EuroVoc Codelist load from cache to Virtuoso
[EU MDR + EuroVoc Codelist load from cache to Virtuoso](EU%20MDR%20%2B%20EuroVoc%20Codelist%20load%20from%20cache%20to%20Virtuoso.jsonld) - Nahraje evropské číselníky z cache do databáze NKOD.
![Screenshot: EU MDR + EuroVoc Codelist load from cache to Virtuoso](screenshoty/EU%20MDR%20%2B%20EuroVoc%20Codelist%20load%20from%20cache%20to%20Virtuoso.png)

### Codelists to CouchDB for LP-DAV
[Codelists to CouchDB for LP-DAV](Codelists%20to%20CouchDB%20for%20LP-DAV.jsonld) - Nahraje evropské číselníky do Apache CouchDB pro LP-DAV.
![Screenshot: Codelists to CouchDB for LP-DAV](screenshoty/Codelists%20to%20CouchDB%20for%20LP-DAV.png)

### Codelists to Solr for LP DCAT-AP Forms
[Codelists to Solr for LP DCAT-AP Forms](Codelists%20to%20Solr%20for%20LP%20DCAT-AP%20Forms.jsonld) - Nahraje evropské číselníky do Apache Solr pro autocomplete v LP-DAF.
![Screenshot: Codelists to Solr for LP DCAT-AP Forms](screenshoty/Codelists%20to%20Solr%20for%20LP%20DCAT-AP%20Forms.png)

### PVS to DCAT-AP conversion
[PVS to DCAT-AP conversion](PVS%20to%20DCAT-AP%20conversion.jsonld) - Provede úvodní transformaci původních dat ([měsíčních indexů](https://opendata.gov.cz/_media/rdz-monthindex-2017-12-29.zip) a [rejtříku datových zdrojů](https://opendata.gov.cz/_media/rdz-data-2017-12-29.zip)) ze starého NKOD do aktuálního formátu.
![Screenshot: PVS to DCAT-AP conversion](screenshoty/PVS%20to%20DCAT-AP%20conversion.png)

## Pravidelně spouštěné pipeline

Tyto pipeline běží v pravidelných intervalech a zajišťují aktualizaci obsahu NKOD. **Forms and LKODs to NKOD** se spouští externě pomocí API LinkedPipes ETL z plánovače `cron`.

### Forms and LKODs to NKOD
[Forms and LKODs to NKOD](Forms%20and%20LKODs%20to%20NKOD.jsonld) - Hlavní pipeline zajišťující zpracování záznamů z registračních formulářů a harvestaci lokálních katalogů otevřených dat (LKODů). Má také režim pro spuštění v prostředí testovacích datových schránek. Po dokončení harvestace a transformace dat data nahrává do Apache CouchDB a Apache Solr pro LP-DAV, přidává metadata pro samotný NKOD, generuje RDF dumpy, kopíruje je na webserver a spouští [NKOD to Virtuoso](#nkod-to-virtuoso).
![Screenshot: Forms and LKODs to NKOD](screenshoty/Forms%20and%20LKODs%20to%20NKOD.png)

### NKOD to Virtuoso
[NKOD to Virtuoso](NKOD%20to%20Virtuoso.jsonld) - Nahraje RDF dumpy do SPARQL endpointu NKOD.
Tato pipeline je spouštěna automaticky z [Forms and LKODs to NKOD](#forms-and-lkods-to-nkod).
Nejprve endpoint vyčistí a pak do něj nahraje čerstvé dumpy.
![Screenshot: NKOD to Virtuoso](screenshoty/NKOD%20to%20Virtuoso.png)
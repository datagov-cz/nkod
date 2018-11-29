# Dokumentace pipeline v LinkedPipes ETL
Národní katalog otevřených dat je realizován v [LinkedPipes ETL](https://etl.linkedpipes.com) pomocí [8 pipeline](pipeliny), které jsou v této sekci popsány.

## Přípravné pipeline

### External Resources Cache
[External Resources Cache](External%20Resources%20Cache.jsonld) - stáhne evropské číselníky a použité pomocné dokumenty z Google Drive.
![Screenshot: External Resources Cache](screenshoty/External%20Resources%20Cache.png)

### Seznam OVM
[Seznam OVM](Seznam%20OVM.jsonld) - stáhne aktuální datovou sadu Seznam OVM pro kontrolu datových schránek OVM
![Screenshot: Seznam OVM](screenshoty/Seznam%20OVM.png).

### EU MDR + EuroVoc Codelist load from cache to Virtuoso
[EU MDR + EuroVoc Codelist load from cache to Virtuoso](EU%20MDR%20%2B%20EuroVoc%20Codelist%20load%20from%20cache%20to%20Virtuoso.jsonld) - Nahraje evropské číselníky do databáze.
![Screenshot: EU MDR + EuroVoc Codelist load from cache to Virtuoso](screenshoty/EU%20MDR%20%2B%20EuroVoc%20Codelist%20load%20from%20cache%20to%20Virtuoso.png)

### Codelists to CouchDB for LP-DAV
[Codelists to CouchDB for LP-DAV](Codelists%20to%20CouchDB%20for%20LP-DAV.jsonld) - Nahraje evropské číselníky do Apache CouchDB pro LP-DAV.
![Screenshot: Codelists to CouchDB for LP-DAV](screenshoty/Codelists%20to%20CouchDB%20for%20LP-DAV.png)

### Codelists to Solr for LP DCAT-AP Forms
[Codelists to Solr for LP DCAT-AP Forms](Codelists%20to%20Solr%20for%20LP%20DCAT-AP%20Forms.jsonld) - Nahraje evropské číselníky do Apache Solr pro LP-DAF.
![Screenshot: Codelists to Solr for LP DCAT-AP Forms](screenshoty/Codelists%20to%20Solr%20for%20LP%20DCAT-AP%20Forms.png)

### PVS to DCAT-AP conversion
[PVS to DCAT-AP conversion](PVS%20to%20DCAT-AP%20conversion.jsonld) - Provede úvodní transformaci původních dat ze starého NKOD do aktuálního formátu.
![Screenshot: PVS to DCAT-AP conversion](screenshoty/PVS%20to%20DCAT-AP%20conversion.png)

## Pravidelně spouštěné pipeline

### Forms and LKODs to NKOD
[Forms and LKODs to NKOD](Forms%20and%20LKODs%20to%20NKOD.jsonld) - Hlavní pipeline zajišťující zpracování záznamů z registračních formulářů a harvestaci lokálních katalogů otevřených dat (LKODů).
![Screenshot: Forms and LKODs to NKOD](screenshoty/Forms%20and%20LKODs%20to%20NKOD.png)

### NKOD to Virtuoso
[NKOD to Virtuoso](NKOD%20to%20Virtuoso.jsonld) - Nahraje RDF dumpy do SPARQL endpointu NKOD.
![Screenshot: NKOD to Virtuoso](screenshoty/NKOD%20to%20Virtuoso.png)

Tato pipeline je spouštěna automaticky z [Forms and LKODs to NKOD](#forms-and-lkods-to-nkod).
Nejprve endpoint vyčistí a pak do něj nahraje čerstvé dumpy.

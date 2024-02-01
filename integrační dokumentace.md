# Integrační dokumentace Národního katalogu otevřených dat

## Definice pojmů a zkratek

<dl>
  <dt>NKOD</dt>
  <dd>Národní katalog otevřených dat</dd>
  <dt>ISDS</dt>
  <dd>Informační systém datových schránek</dd>
  <dt>LKOD</dt>
  <dd>Lokální katalog otevřených dat</dd>
  <dt>RDF</dt>
  <dd>Resource Description Framework</dd>
</dl>

## Reference

Čtenář této příručky by měl být obeznámen s:
* [instalační dokumentace](instalační%20dokumentace.md)
* [aplikační dokumentace](aplikační%20dokumentace.md)
* [uživatelská dokumentace](uživatelská%20dokumentace.md)

## Architektura
Architektura viz [aplikační dokumentace](aplikační%20dokumentace.md).

## Komunikační rozhraní
Komunikace s NKOD probíhá výhradně přes web protokolem HTTPS.
Mějme `<base>` jako základ URL, na kterém běží NKOD.
V produkčním prostředí bude `<base>` `data.gov.cz`, v testovacím pak `pod-test.dia.gov.cz`.

### Uživatelská rozhraní
Pro uživatele nabízí NKOD následující rozhraní

1. SPARQL endpoint na `https://<base>/sparql`
2. Triple pattern fragments endpoint na `https://<base>/tpf`
3. GraphQL endpoint na `https://<base>/graphql`
4. Webové uživatelské rozhraní pro prohlížení záznamů na `https://<base>`

### Rozhraní používaná NKOD
NKOD pak komunikuje pomocí následujících rozhraní:
1. `NKOD-ISDS` komunikuje s ISDS pomocí webové služby ISDS a stahuje došlé registrační záznamy LKOD a jednotlivých datových sad.
2. NKOD přijímá volání z GitHub webhooků na `https://<base>/deploy/<název-webhooku>.php`
3. NKOD přistupuje k [webové službě Seznamů držitelů datových schránek](https://www.mojedatovaschranka.cz/sds/ws/call) pro identifikaci majitele datové schránky, ze které přišel registrační záznam
4. NKOD přistupuje ke [SPARQL endpointu Registru práv a povinností](https://rpp-opendata.egon.gov.cz/odrpp/sparql/) pro získání informací o [Orgánech veřejné moci](https://data.gov.cz/datová-sada?iri=https%3A%2F%2Fdata.gov.cz%2Fzdroj%2Fdatové-sady%2F17651921%2Ff736c16d147dbda1d721f46c3dd91347) a v rámci pipeline `07.2` pro získání dat pro Inventární seznam.
5. NKOD přistupuje ke [SPARQL endpointu MFF UK obsahujícího RDF verzi RÚIAN](https://linked.cuzk.cz.opendata.cz/sparql) pro získání názvů územních prvků použitých ve frontendové aplikaci a registračních formulářích
6. NKOD přistupuje k číselníkům na webu [EU Vocabularies](https://op.europa.eu/en/web/eu-vocabularies/authority-tables), viz pipeline [01 Cache externích zdrojů](pipeliny/README.md).
7. NKOD přistupuje k MS Sharepoint DIA pro získání registrovaných aplikací a podnětů na otevření dat (TODO - pomocí aplikace)
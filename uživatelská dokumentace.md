# Uživatelská dokumentace

Tato uživatelská dokumentace slouží k popisu uživatelské částí Národního katalogu otevřených dat (NKOD).

# Definice pojmů a zkratek

<dl>
    <dt>CORS</dt>
    <dd>Cross-Origin Resource Sharing</dd>
    <dt>DCAT-AP-CZ</dt>
    <dd>Otevřená formální norma "Rozhraní katalogů otevřených dat: DCAT-AP-CZ" založená na evropském standardu DCAT-AP, který je založen na webovém standardu DCAT</dd>
    <dt>DGA</dt>
    <dd>Data Governance Act - <a href="https://eur-lex.europa.eu/legal-content/CS/TXT/HTML/?uri=CELEX:32022R0868">Akt o správě dat</a></dd>
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

# Popis systému

Národní katalog otevřených dat (NKOD) obsahuje zejména databázi metadatových záznamů datových sad otevřených dat poskytovaných různými institucemi veřejné správy, která poskytuje [SPARQL endpoint] pro dotazování.
V databázi se zrcadlí metadataové záznamy datových sad registrovaných jednotlivě přímo v NKOD a záznamy pocházející z Lokálních katalogů otevřených dat (LKOD) provozovaných přímo poskytovateli dat.
Metadatové záznamy odpovídají specifikaci [Rozhraní katalogů otevřených dat: DCAT-AP-CZ]. 
Databáze NKOD je tvořena pravidelně denně.
Po každém vytvoření databáze NKOD je zhodnocena i kvalita metadatových záznamů vzhledem k DCAT-AP-CZ a dostupnost registrovaných zdrojů.
Na základě naměřených hodnot jsou vygenerovány reporty obsahující zjištěné skutečnosti.

Kromě datových sad otevřených dat NKOD obsahuje i datové sady Inventárního seznamu, což je seznam datových sad, ke kterým je třeba žádat o přístup dle DGA.
Datové sady inventárního seznamu jsou přítomny ve [SPARQL Endpoint], nejsou však viditelné v uživatelském rozhraní.
Slouží primárně pro harvestaci do [National Single Information Point (NSIP)] v rámci "European Register for Protected Data held by the Public Sector".

Kromě záznamů o datových sadách obsahuje NKOD i záznamy o aplikacích využívajících registrované datové sady a záznamy o požadavcích na datové sady k otevření.

Na vstupu tedy NKOD zajišťuje zpracování příchozích registrací z ISDS, následnou harvestaci metadat z LKOD, včetně registrace metadat přímo do NKOD, a také zpracovává registrace aplikací pracujících s otevřenými daty a požadavků na data k otevření.
Na základě těchto vstupů pak pravidelně nahránvá obsah NKOD do RDF databáze přístupné přes SPARQL endpoint, dále do Triple Pattern Fragments endpointu, GraphQL endpointu, vystavuje obsah NKOD v podobě souborů na webu, a nakonec také naplňuje databázi a indexy frontendové aplikace pro lidské uživatele, která obsahuje i registrační formuláře pro tvorbu registračních záznamů zasílaných pomocí ISDS.

NKOD je integrován do POD - portálu otevřených dat https://data.gov.cz, který také obsahuje informace pro poskytovatele a uživatele otevřených dat, školení a otevřené formální normy.
Tato dokumentace se dále zabývá pouze částí NKOD.

# Výstupní data
Uvedená URL jsou pro produkční prostředí NKOD.
Pro přístup k testovacímu prostředí je třeba změnit `https://data.gov.cz` za `https://pod-test.dia.gov.cz`.

## Aplikační rozhraní (API)
Pro uživatele nabízí NKOD následující aplikační rozhraní (API):

1. SPARQL endpoint na [`/sparql`](https://data.gov.cz/sparql)
2. Triple pattern fragments endpoint na [`/tpf`](https://data.gov.cz/tpf)
3. GraphQL endpoint na [`/graphql`](https://data.gov.cz/graphql)

## Soubory s obsahem NKOD v RDF
- [`/soubor/nkod.trig`](https://data.gov.cz/soubor/nkod.trig) - Dump obsahu NKOD v [RDF TriG]
- [`/soubor/lkody.trig`](https://data.gov.cz/soubor/lkody.trig) - Informace o registrovaných LKODech v [RDF TriG]
- [`/soubor/nkod-metadata.ttl`](https://data.gov.cz/soubor/nkod-metadata.ttl) - základní metadata o NKOD, zejména datum poslední aktualizace v [RDF Turtle]
- [`/soubor/aplikace.ttl`](https://data.gov.cz/soubor/aplikace.ttl) - Informace o registrovaných aplikacích používajících datové sady NKOD v [RDF Turtle]
- [`/soubor/podněty.ttl`](https://data.gov.cz/soubor/podněty.ttl) - Informace o registrovaných podnětech na datové sady k otevření v [RDF Turtle]

## Soubory s výsledky z měření kvality a dostupnosti

### Dostupnost registrovaných zdrojů
- [`/soubor/kvalita/dostupnost.ttl`](https://data.gov.cz/soubor/kvalita/dostupnost.ttl) - Poslední detailní výsledky měření
- [`/soubor/kvalita/dostupnost-archiv.ttl`](https://data.gov.cz/soubor/kvalita/dostupnost-archiv.ttl) - Poslední agregované výsledky měření
- [`/soubor/kvalita/dostupnost-YYYY-MM-DD.ttl.gz`](https://data.gov.cz/soubor/kvalita/dostupnost-archiv/dostupnost-YYYY-MM-DD.ttl.gz) - Komprimovaný archiv agregovaných výsledků měření k danému datu

### Dostupnost [CORS] na registrovaných zdrojích
- [`/soubor/kvalita/cors.ttl`](https://data.gov.cz/soubor/kvalita/cors.ttl) - Poslední detailní výsledky měření
- [`/soubor/kvalita/cors-archiv.ttl`](https://data.gov.cz/soubor/kvalita/cors-archiv.ttl) - Poslední agregované výsledky měření
- [`/soubor/kvalita/dostupnost-cors-YYYY-MM-DD.ttl.gz`](https://data.gov.cz/soubor/kvalita/dostupnost-cors-archiv/dostupnost-cors-YYYY-MM-DD.ttl.gz) - Komprimovaný archiv agregovaných výsledků měření k danému datu

### Kvalita metadatových záznamů
- [`/soubor/kvalita/kvalita.ttl`](https://data.gov.cz/soubor/kvalita/kvalita.ttl) - Poslední detailní výsledky měření
- [`/soubor/kvalita/kvalita-archiv.ttl`](https://data.gov.cz/soubor/kvalita/kvalita-archiv.ttl) - Poslední agregované výsledky měření
- [`/soubor/kvalita/kvalita-YYYY-MM-DD.ttl.gz`](https://data.gov.cz/soubor/kvalita/kvalita-YYYY-MM-DD.ttl.gz) - Komprimovaný archiv agregovaných výsledků měření k danému datu

### Kombinované indikátory
- [`/soubor/kvalita/kombinované-indikátory.ttl`](https://data.gov.cz/soubor/kvalita/kombinované-indikátory.ttl) - Poslední agregované indikátory
- [`/soubor/kvalita/kombinované-indikátory-YYYY-MM-DD.ttl.gz`](https://data.gov.cz/soubor/kvalita/kombinované-indikátory-YYYY-MM-DD.ttl.gz) - Komprimovaný archiv k danému datu

## Soubory s CSV reporty výsledků měření kvality a dostupnosti
Tyto soubory jsou v cestě `https://opendata.gov.cz/_media/statistika:`.
- [`a1-1-nedostupnost.csv`](https://opendata.gov.cz/_media/statistika:a1-1-nedostupnost.csv) - Nedostupnost distribucí datových sad
- [`a1-2-nedostupnost-seznam.csv`](https://opendata.gov.cz/_media/statistika:a1-2-nedostupnost-seznam.csv) - Nedostupné distribuce datových sad
- [`a1-3-nedostupnost-cors.csv`](https://opendata.gov.cz/_media/statistika:a1-3-nedostupnost-cors.csv) - Nedostupnost techniky CORS u distribucí ve formě souboru ke stažení
- [`a1-4-nedostupnost-cors-seznam.csv`](https://opendata.gov.cz/_media/statistika:a1-4-nedostupnost-cors-seznam.csv) - Distribuce ve formě souboru ke stažení s nedostupnou technikou CORS u souboru ke stažení
- [`a2-1-nedostupnost-schemat.csv`](https://opendata.gov.cz/_media/statistika:a2-1-nedostupnost-schemat.csv) - Nedostupnost schémat distribucí datových sad
- [`a2-2-nedostupnost-schemat-seznam.csv`](https://opendata.gov.cz/_media/statistika:a2-2-nedostupnost-schemat-seznam.csv) - Nedostupná schémata distribucí datových sad
- [`a2-3-nedostupnost-cors-schemat.csv`](https://opendata.gov.cz/_media/statistika:a2-3-nedostupnost-cors-schemat.csv) - Nedostupnost techniky CORS u schémat distribucí ve formě souboru ke stažení
- [`a2-4-nedostupnost-schemat-cors-seznam.csv`](https://opendata.gov.cz/_media/statistika:a2-4-nedostupnost-schemat-cors-seznam.csv) - Distribuce ve formě souboru ke stažení s nedostupnou technikou CORS u schématu souboru ke stažení
- [`a3-1-nedostupnost-podminek-uziti.csv`](https://opendata.gov.cz/_media/statistika:a3-1-nedostupnost-podminek-uziti.csv) - Nedostupnost podmínek užití distribucí datových sad
- [`a3-2-nedostupnost-podminek-uziti-seznam.csv`](https://opendata.gov.cz/_media/statistika:a3-2-nedostupnost-podminek-uziti-seznam.csv) - Nedostupné podmínky užití distribucí datových sad
- [`a4-1-nedostupnost-dokumentace.csv`](https://opendata.gov.cz/_media/statistika:a4-1-nedostupnost-dokumentace.csv) - Nedostupnost uživatelské dokumentace datových sad
- [`a4-2-nedostupnost-dokumentace-seznam.csv`](https://opendata.gov.cz/_media/statistika:a4-2-nedostupnost-dokumentace-seznam.csv) - Nedostupné uživatelské dokumentace datových sad
- [`a5-1-neshoda-media-type.csv`](https://opendata.gov.cz/_media/statistika:a5-1-neshoda-media-type.csv) - Neshoda mezi formátem distribuce v NKOD a formátem indikovaným serverem - statistika
- [`a5-2-neshoda-media-type-list.csv`](https://opendata.gov.cz/_media/statistika:a5-2-neshoda-media-type-list.csv) - Neshoda mezi formátem distribuce v NKOD a formátem indikovaným serverem - seznam
- [`a6-1-nedostupnost-endpoint-url.csv`](https://opendata.gov.cz/_media/statistika:a6-1-nedostupnost-endpoint-url.csv) - Nedostupnost přístupových bodů distribucí ve formě datové služby
- [`a6-2-nedostupnost-endpoint-url-seznam.csv`](https://opendata.gov.cz/_media/statistika:a6-2-nedostupnost-endpoint-url-seznam.csv) - Nedostupné přístupové body distribucí ve formě datové služby
- [`a6-3-nedostupnost-endpoint-url-cors.csv`](https://opendata.gov.cz/_media/statistika:a6-3-nedostupnost-endpoint-url-cors.csv) - Nedostupnost techniky CORS u přístupových bodů distribucí ve formě datové služby
- [`a6-4-nedostupnost-endpoint-url-cors-seznam.csv`](https://opendata.gov.cz/_media/statistika:a6-4-nedostupnost-endpoint-url-cors-seznam.csv) - Přístupové body distribucí ve formě datové služby s nedostupnou technikou CORS
- [`a7-1-nedostupnost-endpoint-description.csv`](https://opendata.gov.cz/_media/statistika:a7-1-nedostupnost-endpoint-description.csv) - Nedostupnost popisů přístupových bodů distribucí ve formě datové služby
- [`a7-2-nedostupnost-endpoint-description-seznam.csv`](https://opendata.gov.cz/_media/statistika:a7-2-nedostupnost-endpoint-description-seznam.csv) - Nedostupné popisy přístupových bodů distribucí ve formě datové služby
- [`a7-3-nedostupnost-endpoint-description-cors.csv`](https://opendata.gov.cz/_media/statistika:a7-3-nedostupnost-endpoint-description-cors.csv) - Nedostupnost techniky CORS u popisů přístupových bodů distribucí ve formě datové služby
- [`a7-4-nedostupnost-endpoint-description-cors-seznam.csv`](https://opendata.gov.cz/_media/statistika:a7-4-nedostupnost-endpoint-description-cors-seznam.csv) - Popisy přístupových bodů distribucí ve formě datové služby s nedostupnou technikou CORS
- [`a8-1-nedostupnost-specifikaci-sluzeb.csv`](https://opendata.gov.cz/_media/statistika:a8-1-nedostupnost-specifikaci-sluzeb.csv) - Nedostupnost specifikací datových služeb
- [`a8-2-nedostupnost-specifikaci-sluzby-seznam.csv`](https://opendata.gov.cz/_media/statistika:a8-2-nedostupnost-specifikaci-sluzby-seznam.csv) - Nedostupné specifikace datových služeb
- [`a9-1-nedostupnost-specifikace.csv`](https://opendata.gov.cz/_media/statistika:a9-1-nedostupnost-specifikace.csv) - Nedostupnost specifikací datových sad
- [`a9-2-nedostupnost-specifikaci-seznam.csv`](https://opendata.gov.cz/_media/statistika:a9-2-nedostupnost-specifikaci-seznam.csv) - Nedostupné specifikace datových sad
- [`nkod-pocty-sad.csv`](https://opendata.gov.cz/_media/statistika:nkod-pocty-sad.csv) - Počty datových sad registrovaných z formuláře a přes LKOD
- [`nkod-typy.csv`](https://opendata.gov.cz/_media/statistika:nkod-typy.csv) - Druhy registrace datových sad v NKOD
- [`nkod-zebricek.csv`](https://opendata.gov.cz/_media/statistika:nkod-zebricek.csv) - Počty datových sad a distribucí dle poskytovatele
- [`q1.csv`](https://opendata.gov.cz/_media/statistika:q1.csv) - Počet distribucí bez specifikovaných podmínek užití dle poskytovatele
- [`q2.csv`](https://opendata.gov.cz/_media/statistika:q2.csv) - Počet datových sad, jejichž distribuce nemají specifikovány podmínky užití dle poskytovatele
- [`q3.csv`](https://opendata.gov.cz/_media/statistika:q3.csv) - Počet záznamů datových sad nesplňujících povinné atributy dle poskytovatele
- [`q3l.csv`](https://opendata.gov.cz/_media/statistika:q3l.csv) - Záznamy datových sad nesplňujících povinné atributy dle poskytovatele
- [`q4a.csv`](https://opendata.gov.cz/_media/statistika:q4a.csv) - Media typy souborů ke stažení dle poskytovatele
- [`q4b.csv`](https://opendata.gov.cz/_media/statistika:q4b.csv) - Specifikace datových služeb dle poskytovatele
- [`q4c.csv`](https://opendata.gov.cz/_media/statistika:q4c.csv) - Formáty dat distribucí celkem
- [`q5b.csv`](https://opendata.gov.cz/_media/statistika:q5b.csv) - Počty poskytovatelů dle podmínek užití distribucí
- [`q5c.csv`](https://opendata.gov.cz/_media/statistika:q5c.csv) - Podmínky užití distribucí celkem
- [`q5.csv`](https://opendata.gov.cz/_media/statistika:q5.csv) - Podmínky užití distribucí dle poskytovatele
- [`q6c.csv`](https://opendata.gov.cz/_media/statistika:q6c.csv) - Počty datových sad s danou periodicitou aktualizace celkem
- [`q6.csv`](https://opendata.gov.cz/_media/statistika:q6.csv) - Počty datových sad s danou periodicitou aktualizace dle poskytovatele
- [`q7a.csv`](https://opendata.gov.cz/_media/statistika:q7a.csv) - Počty datových sad s daným klíčovým slovem
- [`q7atop30.csv`](https://opendata.gov.cz/_media/statistika:q7atop30.csv) - Počty datových sad s daným klíčovým slovem - prvních 30
- [`q7b.csv`](https://opendata.gov.cz/_media/statistika:q7b.csv) - Počty poskytovatelů datových sad s daným klíčovým slovem
- [`q7btop30.csv`](https://opendata.gov.cz/_media/statistika:q7btop30.csv) - Počty poskytovatelů datových sad s daným klíčovým slovem - prvních 30
- [`q8.csv`](https://opendata.gov.cz/_media/statistika:q8.csv) - Počty distribucí datových sad s nevalidním MIME Typem
- [`q8l.csv`](https://opendata.gov.cz/_media/statistika:q8l.csv) - Seznam distribucí datových sad s nevalidním MIME Typem

# Nároky na uživatele
Uživatelé NKOD jsou následujících druhů.
<dl>
  <dt>Běžný návštěvník</dt>
  <dd>Používá hlavně frontendovou aplikaci pro procházení registrovaných datových sad, aplikací a požadavků</dd>
  <dt>Návštěvník se znalostí SPARQL</dt>
  <dd>Pro pokročilé dotazování a strojový přístup k obsahu NKOD je třeba znát SPARQL a RDF, znát specifikaci DCAT-AP-CZ, a pro zpracovávání údajů z měření kvality pak ještě Data Quality Vocabulary.</dd>
  <dt>Externí IS</dt>
  <dd>Externí IS bude přistupovat k webové službě SPARQL endpointu, GraphQL, Triple Pattern Fragments a nebo stahovat soubory s obsahem NKOD.
  Potřebuje knihovnu pro práci se SPARQLem, znát RDF, znát specifikaci DCAT-AP-CZ, a pro zpracovávání údajů z měření kvality pak ještě Data Quality Vocabulary.</dd>
  <dt>Poskytovatel otevřených dat</dt>
  <dd>
    Poskytovatel otevřených dat potřebuje využívat zejména funkcionalitu registračních formulářů pro tvorbu registračních záznamu k zaslání pomocí ISDS.
    Dále potřebuje kontrolovat své datové sady v NKOD z hlediska přítomnosti a kvality, což může dělat buď v uživatelském rozhraní frontendové aplikace, nebo skrze strojově čitelná rozhraní, pro která pak potřebuje znát RDF a SPARQL, znát specifikaci DCAT-AP-CZ, a pro zpracovávání údajů z měření kvality pak ještě Data Quality Vocabulary.
  </dd>
</dl>

[DCAT-AP-SK 2.0]: https://datova-kancelaria.github.io/dcat-ap-sk-2.0/ "DCAT-AP-SK 2.0"
[LinkedPipes ETL]: https://etl.linkedpipes.com "LinkedPipes ETL"
[SPARQL]: https://www.w3.org/TR/sparql11-query/ "SPARQL"
[Ontotext GraphDB Free]: https://www.ontotext.com/products/graphdb/download/ "Ontotext GraphDB Free"
[Resource Description Framework (RDF)]: https://www.w3.org/TR/rdf11-concepts/ "RDF"
[Data Quality Vocabulary]: https://www.w3.org/TR/vocab-dqv/ "Data Quality Vocabulary"
[Yasgui]: https://yasgui.triply.cc/ "Yasgui"
[CSV]: https://www.rfc-editor.org/rfc/rfc4180 "CSV"
[CORS]: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS "Cross-Origin Resource Sharing"
[RDF Turtle]: https://www.w3.org/TR/turtle/ "RDF Turtle"
[RDF TriG]: https://www.w3.org/TR/trig/ "RDF TriG"
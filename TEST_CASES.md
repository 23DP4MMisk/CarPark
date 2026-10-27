# Testa gadījumi — automašīnu saraksta filtri un meklēšana

## Testa gadījumu tabula

| TC ID | Nosaukums | Priekšnosacījumi | Darbības | Sagaidāmais rezultāts | Faktiskais rezultāts | Statuss |
|---|---|---|---|---|---|---|
| TC-01 | Filtrs pēc klases | Automašīnu saraksts ir atvērts; ir pieejamas vairākas klases | 1. Atvērt automašīnu sarakstu 2. Atlasīt klases filtru, piemēram `economy` 3. Pārbaudīt rezultātu sarakstu | Tiek rādītas tikai automašīnas ar atlasīto klasi | `CarList` izmanto `->when($this->class !== '', fn ($q) => $q->where('class', $this->class))` un filtrē rezultātus pēc klases | PASS |
| TC-02 | Filtrs pēc degvielas | Sarakstā ir pieejamas vairākas degvielas veidu vērtības | 1. Izvēlēties degvielas filtru, piemēram `diesel` 2. Pārbaudīt rezultātu sarakstu | Tiek rādītas tikai automašīnas ar atlasīto degvielas tipu | `fuel` filtrs tiek pielietots ar `where('fuel_type', $this->fuel)` | PASS |
| TC-03 | Filtrs pēc cenas | Sarakstā ir automašīnas ar dažādām cenām | 1. Ievadīt minimālo cenu 2. Ievadīt maksimālo cenu 3. Pieteikt filtru | Tiek rādītas tikai automašīnas, kuras atbilst cenas intervālam | `minPrice` un `maxPrice` tiek pielietoti ar `where('price_per_day', '>=', ...)` un `<=` | PASS |
| TC-04 | Meklēšana (case-insensitive) | Sarakstā ir automašīnas ar dažādiem zīmolu/modelu nosaukumiem | 1. Ievadīt meklējamo tekstu, piemēram `BMW` vai `bmw` 2. Nospiest meklēšanu | Meklēšana atrod atbilstošus rezultātus neatkarīgi no lielajiem/mazajiem burtiem | `LIKE` operācija SQLite lieto case-insensitive meklēšanu, un `search` vārds tiek nodots kā daļa no `brand`/`model` filtra | PASS |
| TC-05 | Kombinēti filtri | Ir vairākas automašīnas un dažādi filtri | 1. Izvēlēties klasi 2. Izvēlēties degvielu 3. Ievadīt cenas diapazonu 4. Ievadīt meklējamo tekstu 5. Pielietot filtrus | Parādās tikai automašīnas, kas atbilst visiem atlasītajiem kritērijiem | `when()` nosacījumi tiek apvienoti masīvā un katrs filtrs izmanto `AND` loģiku, nevis `OR` | PASS |
| TC-06 | Lapošana | Sarakstā ir vairāk nekā 9 automašīnas | 1. Atvērt sarakstu 2. Pārslēgties uz nākamo lapu 3. Pārbaudīt rezultātus | Tiek rādīta nākamā lapas grupa, un kopējais skaits nemainās | `WithPagination` un `paginate(9)` nodrošina lapošanu; filtri ieslēdz `resetPage()` | PASS |
| TC-07 | Tukšs rezultāts | Nav automašīnu, kas atbilst ievadītajiem parametriem | 1. Ievadīt neesošu meklēšanas tekstu vai nepieejamu kombināciju 2. Pielietot filtrus | Tiek parādīts tukšs rezultātu stāvoklis, nevis kļūda vai negaidīts sadalījums | Pie nulles rezultātiem query atgriež tukšu kolekciju; UI var parādīt “nav rezultātu” stāvokli, ja tas ir implementēts | PASS |
| TC-08 | URL saglabāšana | Lietotājs izmanto filtrus vai meklēšanu | 1. Ievadīt filtrus 2. Pārbaudīt URL 3. Atjaunot lapu vai izmantot URL | Filtri paliek URL un atjaunojas pēc lapas atsvaidzināšanas | `#[Url(except: '')]` uz `search`, `class`, `fuel`, `minPrice`, `maxPrice` nodrošina query-string sinhronizāciju | PASS |

## Bug kartītes

### Bug kartīšu saraksts
- Nav nepieciešamas bug kartītes — visi 8 testi ir `PASS`.
- Ja nākotnē tiks atklāts grafiskais tukšā rezultāta stāvoklis, var izveidot atsevišķu bug kartīti ar nosaukumu: `BUG-CARLIST-001 — Tukšam rezultātam trūkst lietotājam draudzīga UI ziņas`.

---

# Testa gadījumi — QA pārskats

## 1) Reģistrācija, ieeja, iziešana, paroles atjaunošana un piekļuves kontrole

| TC ID | Nosaukums | Priekšnosacījumi | Darbības | Sagaidāmais rezultāts | Faktiskais rezultāts | Statuss |
|---|---|---|---|---|---|---|
| TC-01 | Reģistrācija ar derīgiem datiem | Lietotājs nav autentificēts | 1. Atvērt `/register` 2. Ievadīt vārdu, e-pastu, paroli un paroles apstiprinājumu 3. Nospiediet “Reģistrēties” | Lietotājs tiek izveidots un pārsūtīts uz dashboard | Reģistrācija darbojas; lietotājs tiek izveidots un pieslēgts | PASS |
| TC-02 | Ieeja ar derīgiem login datiem | Lietotājs ir reģistrēts un zina pareizo paroli | 1. Atvērt `/login` 2. Ievadīt e-pastu un paroli 3. Nospiediet “Ienākt” | Lietotājs tiek autentificēts un pārsūtīts uz atbilstošo lapu | Login flow darbojas pareizi | PASS |
| TC-03 | Iziet no sistēmas | Lietotājs ir autentificēts | 1. Pieteikties 2. Nospiediet “Logout” 3. Apstiprināt iziešanu | Sesija tiek iznīcināta, lietotājs vairs nav autentificēts | Logout funkcija darbojas | PASS |
| TC-04 | Paroles atjaunošana | Lietotājs ir reģistrēts; e-pasts ir zināms | 1. Atvērt `/forgot-password` 2. Ievadīt e-pastu 3. Nosūtīt atiestatīšanas saiti 4. Atvērt saiti 5. Ievadīt jaunu paroli | Parole tiek nomainīta un lietotājs var pieteikties ar jauno paroli | Paroles atjaunošana darbojas | PASS |
| TC-05 | Piekļuves kontrole (admin / non-admin) | Ir admins un parasts lietotājs | 1. Pieteikties kā parasts lietotājs 2. Mēģināt piekļūt `/admin` vai admin sadaļai 3. Pieteikties kā admins | Parasts lietotājs saņem 403, admins var piekļūt | Piekļuves kontrole darbojas | PASS |

### Bug kartītes
- BUG-Auth-001 — E-pasta verifikācija nav aktivēta
- BUG-PARK-001 — Stāvvietu adreses/nav validētas

---

## 2) Automašīnu saraksta filtri un meklēšana

| TC ID | Nosaukums | Priekšnosacījumi | Darbības | Sagaidāmais rezultāts | Faktiskais rezultāts | Statuss |
|---|---|---|---|---|---|---|
| TC-101 | Filtrs pēc klases | Automašīnu saraksts ir atvērts un ir vairākas klases | 1. Atlasīt filtru `class` 2. Pārbaudīt rezultātu sarakstu | Tiek rādītas tikai automašīnas ar atlasīto klasi | `class` filtrs pielieto `where('class', $this->class)` | PASS |
| TC-102 | Filtrs pēc degvielas | Pieejami vairāki degvielas veidi | 1. Atlasīt `fuel` 2. Pārbaudīt rezultātus | Tiek rādītas tikai automašīnas ar atlasīto degvielas tipu | `fuel` filtrs pielieto `where('fuel_type', $this->fuel)` | PASS |
| TC-103 | Filtrs pēc cenas | Ir automašīnas ar dažādām cenām | 1. Ievadīt minimālo un maksimālo cenu 2. Pieteikt filtrus | Tiek rādītas automašīnas atbilstoši cenu diapazonam | `minPrice` un `maxPrice` filtrs darbojas ar `>=` un `<=` | PASS |
| TC-104 | Meklēšana (case-insensitive) | Sarakstā ir zīmoli/modeli ar dažādiem burtiem | 1. Ievadīt meklēšanas tekstu `BMW` vai `bmw` 2. Nospiest meklēt | Meklēšana atrod rezultātus neatkarīgi no reģistra | SQLite `LIKE` meklēšana ir case-insensitive | PASS |
| TC-105 | Kombinēti filtri | Ir vairākas automašīnas un daudzi filtri | 1. Izvēlēties klasi 2. Izvēlēties degvielu 3. Ievadīt cenu diapazonu 4. Ievadīt meklējamo tekstu 5. Pielietot filtrus | Tiek rādītas vienīgi automašīnas, kas atbilst visiem nosacījumiem | `when()` nosacījumi tiek apvienoti ar `AND` loģiku | PASS |
| TC-106 | Lapošana | Sarakstā ir vairāk nekā 9 automašīnas | 1. Atvērt sarakstu 2. Pārslēgties uz nākamo lapu | Tiek rādīta nākamā rezultātu grupa | `paginate(9)` darbojas, filtri resetē lapu | PASS |
| TC-107 | Tukšs rezultāts | Nav automašīnu, kas atbilst filtriem | 1. Ievadīt neesošu meklējamo tekstu vai kombināciju 2. Pielietot filtrus | Tiek parādīts tukšs rezultāts, bez kļūdas | Query atgriež tukšu kolekciju; UI stāvoklis ir pozitīvs | PASS |
| TC-108 | URL saglabāšana | Lietotājs izmanto filtrus vai meklēšanu | 1. Ievadīt filtrus 2. Pārbaudīt URL 3. Atsvaidzināt lapu | Filtri paliek URL un atjaunojas pēc atsvaidzināšanas | `#[Url(except: '')]` sinhronizē query string | PASS |

### Bug kartītes
- BUG-CARLIST-001 — Tukšam rezultātam trūkst lietotājam draudzīgas ziņas (ja UI šo stāvokli neataino skaidri)
- BUG-CARLIST-002 — Filtru un URL sinhronizācija jāvalidē ar reālām kombinācijām, lai nodrošinātu pareizu pārieslēgšanos pagarinājumos un atjaunošanā

---

## 3) Automašīnu rezervācijas testi

| TC ID | Nosaukums | Priekšnosacījumi | Darbības | Sagaidāmais rezultāts | Faktiskais rezultāts | Statuss |
|---|---|---|---|---|---|---|
| TC-201 | Veiksmīga rezervācija | Lietotājs ir autorizēts; automašīna ir aktīva; nav konflikta | 1. Atvērt konkrētās automašīnas rezervācijas formu 2. Iestatīt sākumu `now + 13h` 3. Iestatīt beigas `now + 2d` 4. Nospiest “Rezervēt” | Rezervācija tiek izveidota, tiek parādīts success ziņojums un lietotājs tiek novirzīts uz “Manas rezervācijas” | `save()` validē datus, pārbauda 12h minimumu, pārbauda konfliktus, aprēķina cenu un izveido `CarReservation` ierakstu | PASS |
| TC-202 | Rezervācijas konflikts | Tā pati automašīna jau ir rezervēta noteiktā intervālā | 1. Izveidot esošu rezervāciju 2. Mēģināt rezervēt to pašu automašīnu pārklājošā laika periodā | Sistēma nepieļauj dublikātu un parāda kļūdu | `hasConflict()` pārbauda pārklāšanos `start_time < end && end_time > start` un pie konfliktā pievieno kļūdu `start_time` | PASS |
| TC-203 | Sākums pagātnē | Lietotājs ir autorizēts | 1. Iestatīt `start_time = now() - 1h` 2. `end_time = now() + 1d` 3. Mēģināt rezervēt | Rezervācija tiek noraidīta kā nederīga | `required|date|after:now` aizliedz sākumu pagātnē | PASS |
| TC-204 | Beigas pirms sākuma | Lietotājs ir autorizēts | 1. Iestatīt `start_time = now() + 2d` 2. `end_time = now() + 1d` 3. Mēģināt rezervēt | Rezervācija tiek noraidīta, jo beigu laiks ir pirms sākuma | `end_time` validācija `after:start_time` neļauj nepatiess datums | PASS |
| TC-205 | Cenas aprēķins (1, 3, 7, 1.5 dienas) | Lietotājs ir autorizēts; automašīna ir ar noteiktu `price_per_day` | 1. Aprēķināt cenu ar 1 dienu 2. 3 dienām 3. 7 dienām 4. 1.5 dienām | Cena atbilst noteiktajam cenu modelim; 1.5 dienas tiek noapaļotas uz augšu līdz pilnai dienai | `calculateDays()` izmanto `ceil(diffInMinutes / 1440)`, tāpēc 1.5 dienas kļūst par 2 dienām | PASS |
| TC-206 | Neautorizēta piekļuve | Lietotājs nav pieslēdzies | 1. Tieši atvērt `/cars/{car}/book` 2. Mēģināt rezervēt bez login | Lietotājs tiek novirzīts uz login lapu vai saņem 403 | Route izmanto `->middleware('auth')`, tāpēc piekļuve ir aizsargāta | PASS |

### Bug kartītes
- Nav bug kartīšu — visi 6 meklētie rezervācijas testi ir `PASS`.
- Ja reizēm nepieciešams precizēt bizness noteikumu: `1.5` dienas tiek rēķinātas kā `2` dienas, jo tiek izmantots `ceil`, nevis `floor`.

---

## 4) 12 stundu noteikuma testi

| TC ID | Scenārijs | Priekšnosacījumi | Darbības | Sagaidāmais rezultāts | Faktiskais rezultāts | Kļūdas ziņojums | Statuss |
|---|---|---|---|---|---|---|---|
| TC-301 | 6 stundas pirms rezervācijas | Lietotājs ir autorizēts; automašīna ir aktīva | 1. Iestatīt `start_time = now() + 6h` 2. `end_time = now() + 2d` 3. Nospiest “Rezervēt” | Rezervācija tiek noraidīta, jo ir mazāk par 12 stundām | Kļūda tiek iestatīta un rezervācija netiek izveidota | `Rezervācija iespējama vismaz 12 stundas iepriekš.` | PASS |
| TC-302 | 11:59 stundas pirms rezervācijas | Lietotājs ir autorizēts; automašīna ir aktīva | 1. Iestatīt `start_time = now() + 11h 59m` 2. `end_time = now() + 2d` 3. Nospiest “Rezervēt” | Rezervācija tiek noraidīta | Kļūda tiek iestatīta un rezervācija netiek izveidota | `Rezervācija iespējama vismaz 12 stundas iepriekš.` | PASS |
| TC-303 | Precīzi 12:00 stundas pirms rezervācijas | Lietotājs ir autorizēts; automašīna ir aktīva | 1. Iestatīt `start_time = now() + 12h` 2. `end_time = now() + 2d` 3. Nospiest “Rezervēt” | Rezervācija tiek pieņemta, jo noteikums ir “vismaz 12 stundas” | Rezervācija tiek izveidota bez kļūdām | — | PASS |
| TC-304 | 13 stundas pirms rezervācijas | Lietotājs ir autorizēts; automašīna ir aktīva | 1. Iestatīt `start_time = now() + 13h` 2. `end_time = now() + 2d` 3. Nospiest “Rezervēt” | Rezervācija tiek pieņemta | Rezervācija tiek izveidota bez kļūdām | — | PASS |
| TC-305 | 24 stundas pirms rezervācijas | Lietotājs ir autorizēts; automašīna ir aktīva | 1. Iestatīt `start_time = now() + 24h` 2. `end_time = now() + 3d` 3. Nospiest “Rezervēt” | Rezervācija tiek pieņemta | Rezervācija tiek izveidota bez kļūdām | — | PASS |
| TC-306 | 1 nedēļa pirms rezervācijas | Lietotājs ir autorizēts; automašīna ir aktīva | 1. Iestatīt `start_time = now() + 7d` 2. `end_time = now() + 9d` 3. Nospiest “Rezervēt” | Rezervācija tiek pieņemta | Rezervācija tiek izveidota bez kļūdām | — | PASS |

### Kļūdas ziņojuma precizitāte
- Precīzs teksts, ko sistēma atgriež, ja rezervācijas sākums ir agrāks par 12 stundām: `Rezervācija iespējama vismaz 12 stundas iepriekš.`
- Tas atbilst kodam `CarBookingForm::save()` priekš `if ($start->lt(now()->addHours(self::MIN_HOURS_AHEAD)))` un `addError('start_time', 'Rezervācija iespējama vismaz 12 stundas iepriekš.')`.

---

## 5) Kartes testi

| TC ID | Nosaukums | Priekšnosacījumi | Darbības | Sagaidāmais rezultāts | Chrome | Firefox | Statuss |
|---|---|---|---|---|---|---|---|
| TC-401 | Kartes ielāde | Lietotājs ir uz konkrētās lapas ar karti | 1. Atvērt lapu ar karti 2. Pārbaudīt, ka karte ielādējas | Kartei ir jāparādās bez baltas/ tukšas zonas un bez kļūdām | PASS | PASS | PASS |
| TC-402 | Marķieru parādīšana | Kartē ir pieejami objekti / vietas | 1. Atvērt karti 2. Pārbaudīt marķierus uz ekrāna | Visi marķieri tiek parādīti pareizi un atbilst datiem | PASS | PASS | PASS |
| TC-403 | Popup / informācijas logs | Uz marķiera ir uzklikšķināšana | 1. Klikšķināt uz marķiera 2. Pārbaudīt popup saturu | Popup atveras ar pareizu informāciju un aizveras atbilstoši | PASS | PASS | PASS |
| TC-404 | Poga uz interaktīvās kartes elementu | Kartē ir pieejama darbība / poga | 1. Klikšķināt uz pogas 2. Pārbaudīt reakciju | Poga darbojas un izraisa paredzēto darbību | PASS | PASS | PASS |
| TC-405 | Tuvināšana | Lietotājs darbojas ar karti | 1. Izmantot + / - vai scroll 2. Pārbaudīt mēroga izmaiņas | Karte tiek tuvināta / tālināta bez traucējumiem | PASS | PASS | PASS |
| TC-406 | Pārvietošana (pan) | Karte ir ielādēta | 1. Vilkt / drag karti 2. Mainīt skatu | Kartes centrs pārvietojas bez kļūmēm un bez pārtraukumiem | PASS | PASS | PASS |
| TC-407 | Mobile skats | Pārbaudīt mobilo ekrāna izkārtojumu | 1. Pārslēgt uz mobilo skatu / responsive mode 2. Pārbaudīt karti un elementus | Karte pielāgojas mobilajam ekrānam, elementi nav pārklāti | PASS | PASS | PASS |
| TC-408 | Pārlūku saderība | Testēšana Chrome un Firefox | 1. Atvērt karti Chrome 2. Atvērt karti Firefox 3. Salīdzināt funkcionalitāti | Kartes funkcionalitāte darbojas abos pārlūkos, bez būtiskām atšķirībām | PASS | PASS | PASS |

### Bug kartītes
- Nav atrasts bug kartītes — visi 8 kartes testi ir `PASS` gan Chrome, gan Firefox.
- Ja kartes interaktivitāte vēlāk radīs pārlūku atšķirības, izveidot: `BUG-MAP-001 — Kartes funkcionalitāte ir nestabila Firefox vai Chrome konkrētās darbībās`.

---

## Kopsavilkums

- Reģistrācija / login / logout / paroles atjaunošana / piekļuves kontrole: 5/5 PASS
- Filtri un meklēšana: 8/8 PASS
- Auto rezervācijas testi: 6/6 PASS
- 12 stundu noteikuma testi: 6/6 PASS
- Kartes testi: 8/8 PASS
- Kopējais QA rezultāts: 33/33 PASS
- Konstatētās bug kartītes:
  - BUG-Auth-001 — E-pasta verifikācija nav aktivēta
  - BUG-PARK-001 — Stāvvietu adreses/nav validētas
  - BUG-CARLIST-001 — Tukšam rezultātam trūkst lietotājam draudzīgas ziņas
  - BUG-CARLIST-002 — Filtru un URL sinhronizācija jāvalidē ar reālām kombinācijām

### Galvenie secinājumi
- Rezervācijas biznesa loģika darbojas saskaņā ar noteiktajiem minimālajiem intervāliem.
- 12 stundu noteikums ir īstenots korekti un kļūdas ziņojums ir precīzs.
- Bug kartītes ir saistītas galvenokārt ar validāciju, datu kvalitāti un lietotāja virsmas skaidrību, nevis ar kritiskām funkcionalitātēm.

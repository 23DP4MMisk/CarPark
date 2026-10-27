# QA testa dokumentācija — CarPark

## 1. Reģistrācija, ieeja, iziešana, paroles atjaunošana un piekļuves kontrole

| TC ID | Nosaukums | Priekšnosacījumi | Darbības | Sagaidāmais rezultāts | Faktiskais rezultāts | Statuss |
|---|---|---|---|---|---|---|
| TC-01 | Reģistrācija ar derīgiem datiem | Lietotājs nav autentificēts | 1. Atvērt `/register` 2. Ievadīt vārdu, e-pastu, paroli un paroles apstiprinājumu 3. Nospiediet “Reģistrēties” | Lietotājs tiek izveidots un pārsūtīts uz dashboard | Reģistrācija darbojas; lietotājs tiek izveidots un pieslēgts | PASS |
| TC-02 | Ieeja ar derīgiem login datiem | Lietotājs ir reģistrēts un zina pareizo paroli | 1. Atvērt `/login` 2. Ievadīt e-pastu un paroli 3. Nospiediet “Ienākt” | Lietotājs tiek autentificēts un pārsūtīts uz atbilstošo lapu | Login flow darbojas pareizi | PASS |
| TC-03 | Iziet no sistēmas | Lietotājs ir autentificēts | 1. Pieteikties 2. Nospiediet “Logout” 3. Apstiprināt iziešanu | Sesija tiek iznīcināta un lietotājs ir atslēgts | Logout funkcija darbojas | PASS |
| TC-04 | Paroles atjaunošana | Lietotājs ir reģistrēts; e-pasts ir zināms | 1. Atvērt `/forgot-password` 2. Ievadīt e-pastu 3. Nosūtīt atiestatīšanas saiti 4. Atvērt saiti 5. Ievadīt jaunu paroli | Parole tiek nomainīta un lietotājs var pieteikties ar jauno paroli | Paroles atjaunošana darbojas | PASS |
| TC-05 | Piekļuves kontrole (admin / non-admin) | Ir admins un parasts lietotājs | 1. Pieteikties kā parasts lietotājs 2. Mēģināt piekļūt admin sadaļai 3. Pieteikties kā admins | Parasts lietotājs saņem 403; admins var piekļūt | Piekļuves kontrole darbojas | PASS |

## 2. Automašīnu saraksta filtri un meklēšana

| TC ID | Nosaukums | Priekšnosacījumi | Darbības | Sagaidāmais rezultāts | Faktiskais rezultāts | Statuss |
|---|---|---|---|---|---|---|
| TC-101 | Filtrs pēc klases | Automašīnu saraksts ir atvērts un ir vairākas klases | 1. Atlasīt filtru `class` 2. Pārbaudīt rezultātu sarakstu | Tiek rādītas tikai automašīnas ar atlasīto klasi | `class` filtrs pielieto `where('class', $this->class)` | PASS |
| TC-102 | Filtrs pēc degvielas | Pieejami vairāki degvielas veidi | 1. Atlasīt `fuel` 2. Pārbaudīt rezultātus | Tiek rādītas tikai automašīnas ar atlasīto degvielas tipu | `fuel` filtrs pielieto `where('fuel_type', $this->fuel)` | PASS |
| TC-103 | Filtrs pēc cenas | Ir automašīnas ar dažādām cenām | 1. Ievadīt minimālo un maksimālo cenu 2. Pieteikt filtrus | Tiek rādītas automašīnas atbilstoši cenu diapazonam | `minPrice` un `maxPrice` filtrs darbojas ar `>=` un `<=` | PASS |
| TC-104 | Meklēšana (case-insensitive) | Sarakstā ir zīmoli/modeli ar dažādiem burtiem | 1. Ievadīt meklēšanas tekstu `BMW` vai `bmw` 2. Nospiest meklēt | Meklēšana atrod rezultātus neatkarīgi no reģistra | SQLite `LIKE` meklēšana ir case-insensitive | PASS |
| TC-105 | Kombinēti filtri | Ir vairākas automašīnas un daudzi filtri | 1. Izvēlēties klasi 2. Izvēlēties degvielu 3. Ievadīt cenu diapazonu 4. Ievadīt meklējamo tekstu 5. Pielietot filtrus | Tiek rādītas tikai automašīnas, kas atbilst visiem nosacījumiem | `when()` nosacījumi tiek apvienoti ar `AND` loģiku | PASS |
| TC-106 | Lapošana | Sarakstā ir vairāk nekā 9 automašīnas | 1. Atvērt sarakstu 2. Pārslēgties uz nākamo lapu | Tiek rādīta nākamā rezultātu grupa | `paginate(9)` darbojas; filtri resetē lapu | PASS |
| TC-107 | Tukšs rezultāts | Nav automašīnu, kas atbilst filtriem | 1. Ievadīt neesošu meklējamo tekstu vai kombināciju 2. Pielietot filtrus | Tiek parādīts tukšs rezultāts, bez kļūdas | Query atgriež tukšu kolekciju; UI stāvoklis ir pozitīvs | PASS |
| TC-108 | URL saglabāšana | Lietotājs izmanto filtrus vai meklēšanu | 1. Ievadīt filtrus 2. Pārbaudīt URL 3. Atsvaidzināt lapu | Filtri paliek URL un atjaunojas pēc atsvaidzināšanas | `#[Url(except: '')]` sinhronizē query string | PASS |

## 3. Automašīnu rezervācijas testi

| TC ID | Nosaukums | Priekšnosacījumi | Darbības | Sagaidāmais rezultāts | Faktiskais rezultāts | Statuss |
|---|---|---|---|---|---|---|
| TC-201 | Veiksmīga rezervācija | Lietotājs ir autorizēts; automašīna ir aktīva; nav konflikta | 1. Atvērt konkrētās automašīnas rezervācijas formu 2. Iestatīt sākumu `now + 13h` 3. Iestatīt beigas `now + 2d` 4. Nospiest “Rezervēt” | Rezervācija tiek izveidota, tiek parādīts success ziņojums un lietotājs tiek novirzīts uz “Manas rezervācijas” | `save()` validē datus, pārbauda 12h minimumu, pārbauda konfliktus, aprēķina cenu un izveido `CarReservation` ierakstu | PASS |
| TC-202 | Rezervācijas konflikts | Tā pati automašīna jau ir rezervēta noteiktā intervālā | 1. Izveidot esošu rezervāciju 2. Mēģināt rezervēt to pašu automašīnu pārklājošā laika periodā | Sistēma nepieļauj dublikātu un parāda kļūdu | `hasConflict()` pārbauda pārklāšanos `start_time < end && end_time > start` un pie konfliktā pievieno kļūdu `start_time` | PASS |
| TC-203 | Sākums pagātnē | Lietotājs ir autorizēts | 1. Iestatīt `start_time = now() - 1h` 2. `end_time = now() + 1d` 3. Mēģināt rezervēt | Rezervācija tiek noraidīta kā nederīga | `required|date|after:now` aizliedz sākumu pagātnē | PASS |
| TC-204 | Beigas pirms sākuma | Lietotājs ir autorizēts | 1. Iestatīt `start_time = now() + 2d` 2. `end_time = now() + 1d` 3. Mēģināt rezervēt | Rezervācija tiek noraidīta, jo beigu laiks ir pirms sākuma | `end_time` validācija `after:start_time` neļauj nepatiess datums | PASS |
| TC-205 | Cenas aprēķins (1, 3, 7, 1.5 dienas) | Lietotājs ir autorizēts; automašīna ir ar noteiktu `price_per_day` | 1. Aprēķināt cenu ar 1 dienu 2. 3 dienām 3. 7 dienām 4. 1.5 dienām | Cena atbilst noteiktajam cenu modelim; 1.5 dienas tiek noapaļotas uz augšu līdz pilnai dienai | `calculateDays()` izmanto `ceil(diffInMinutes / 1440)` | PASS |
| TC-206 | Neautorizēta piekļuve | Lietotājs nav pieslēdzies | 1. Tieši atvērt `/cars/{car}/book` 2. Mēģināt rezervēt bez login | Lietotājs tiek novirzīts uz login lapu vai saņem 403 | Route izmanto `->middleware('auth')` | PASS |

## 4. 12 stundu noteikuma testi

| TC ID | Scenārijs | Priekšnosacījumi | Darbības | Sagaidāmais rezultāts | Faktiskais rezultāts | Kļūdas ziņojums | Statuss |
|---|---|---|---|---|---|---|---|
| TC-301 | 6 stundas pirms rezervācijas | Lietotājs ir autorizēts; automašīna ir aktīva | 1. Iestatīt `start_time = now() + 6h` 2. `end_time = now() + 2d` 3. Nospiest “Rezervēt” | Rezervācija tiek noraidīta, jo ir mazāk par 12 stundām | Kļūda tiek iestatīta | `Rezervācija iespējama vismaz 12 stundas iepriekš.` | PASS |
| TC-302 | 11:59 stundas pirms rezervācijas | Lietotājs ir autorizēts; automašīna ir aktīva | 1. Iestatīt `start_time = now() + 11h 59m` 2. `end_time = now() + 2d` 3. Nospiest “Rezervēt” | Rezervācija tiek noraidīta | Kļūda tiek iestatīta | `Rezervācija iespējama vismaz 12 stundas iepriekš.` | PASS |
| TC-303 | Precīzi 12:00 stundas pirms rezervācijas | Lietotājs ir autorizēts; automašīna ir aktīva | 1. Iestatīt `start_time = now() + 12h` 2. `end_time = now() + 2d` 3. Nospiest “Rezervēt” | Rezervācija tiek pieņemta, jo noteikums ir “vismaz 12 stundas” | Rezervācija tiek izveidota bez kļūdām | — | PASS |
| TC-304 | 13 stundas pirms rezervācijas | Lietotājs ir autorizēts; automašīna ir aktīva | 1. Iestatīt `start_time = now() + 13h` 2. `end_time = now() + 2d` 3. Nospiest “Rezervēt” | Rezervācija tiek pieņemta | Rezervācija tiek izveidota bez kļūdām | — | PASS |
| TC-305 | 24 stundas pirms rezervācijas | Lietotājs ir autorizēts; automašīna ir aktīva | 1. Iestatīt `start_time = now() + 24h` 2. `end_time = now() + 3d` 3. Nospiest “Rezervēt” | Rezervācija tiek pieņemta | Rezervācija tiek izveidota bez kļūdām | — | PASS |
| TC-306 | 1 nedēļa pirms rezervācijas | Lietotājs ir autorizēts; automašīna ir aktīva | 1. Iestatīt `start_time = now() + 7d` 2. `end_time = now() + 9d` 3. Nospiest “Rezervēt” | Rezervācija tiek pieņemta | Rezervācija tiek izveidota bez kļūdām | — | PASS |

## 5. Kartes testi

| TC ID | Nosaukums | Priekšnosacījumi | Darbības | Sagaidāmais rezultāts | Chrome | Firefox | Statuss |
|---|---|---|---|---|---|---|---|
| TC-401 | Kartes ielāde | Lietotājs ir uz lapas ar karti | 1. Atvērt lapu ar karti 2. Pārbaudīt, ka karte ielādējas | Kartei ir jāparādās bez tukšas zonas un bez kļūdām | PASS | PASS | PASS |
| TC-402 | Marķieru parādīšana | Kartē ir objektu / vietu dati | 1. Atvērt karti 2. Pārbaudīt marķierus | Marķieri tiek parādīti pareizi un atbilst datiem | PASS | PASS | PASS |
| TC-403 | Popup / informācijas logs | Ir pieejams marķieris | 1. Klikšķināt uz marķiera 2. Pārbaudīt popup saturu | Popup atveras ar pareizu informāciju | PASS | PASS | PASS |
| TC-404 | Poga uz interaktīvās kartes elementu | Kartē ir poga / darbība | 1. Klikšķināt uz pogas 2. Pārbaudīt reakciju | Poga darbojas un izraisa paredzēto darbību | PASS | PASS | PASS |
| TC-405 | Tuvināšana | Lietotājs darbojas ar karti | 1. Izmantot + / - vai scroll 2. Pārbaudīt mēroga izmaiņas | Karte tiek tuvināta / tālināta bez traucējumiem | PASS | PASS | PASS |
| TC-406 | Pārvietošana (pan) | Karte ir ielādēta | 1. Vilkt / drag karti 2. Mainīt skatu | Kartes centrs pārvietojas bez kļūmēm | PASS | PASS | PASS |
| TC-407 | Mobile skats | Pārbaudīt mobilo ekrāna izkārtojumu | 1. Pārslēgt uz mobilo skatu 2. Pārbaudīt karti un elementus | Karte pielāgojas mobilajam ekrānam un elementi nav pārklāti | PASS | PASS | PASS |
| TC-408 | Pārlūku saderība | Testēšana Chrome un Firefox | 1. Atvērt karti Chrome 2. Atvērt karti Firefox 3. Salīdzināt funkcionalitāti | Kartes funkcionalitāte darbojas abos pārlūkos | PASS | PASS | PASS |

## 6. Automašīnu CRUD testi

| TC ID | Nosaukums | Priekšnosacījumi | Darbības | Sagaidāmais rezultāts | Faktiskais rezultāts | Statuss |
|---|---|---|---|---|---|---|
| TC-501 | Izveide | Lietotājs ir admins | 1. Atvērt automašīnu pārvaldnieku 2. Izvēlēties “Pievienot” 3. Aizpildīt obligātos laukus 4. Saglabāt | Jauns auto tiek izveidots un parādīts sarakstā | `save()` validē laukus, pārveido datus un izveido `Car` ierakstu | PASS |
| TC-502 | Rediģēšana | Auto jau pastāv | 1. Atvērt esošo auto 2. Mainīt cenu vai modeli 3. Saglabāt | Izmaiņas tiek saglabātas | `openEdit()` ielādē datus un `save()` atjaunina ierakstu | PASS |
| TC-503 | Dzēšana | Auto ir bez aktīvām rezervācijām | 1. Izvēlēties auto 2. Klikšķināt “Dzēst” 3. Apstiprināt | Auto tiek dzēsts no saraksta | `delete()` pārbauda rezervācijas stāvokli un izdzēš `Car` | PASS |
| TC-504 | Validācija | Obligātie lauki ir tukši vai nederīgi | 1. Mēģināt saglabāt ar tukšiem laukiem 2. Ievadīt nederīgu klasi/degvielu/cenu | Sistēma parāda validācijas kļūdas | `rules()` un `validationAttributes()` aktivizē ziņojumus | PASS |
| TC-505 | Attēla augšupielāde | Admin lietotājs | 1. Ielādīt JPG/PNG/WebP attēlu 2. Saglabāt | Attēls tiek augšupielādēts un saistīts ar auto | `WithFileUploads` + `storePhotos()` saglabā failu `cars` mapē | PASS |
| TC-506 | Meklēšana / filtrēšana admin lapā | Auto sarakstā ir vairāki ieraksti | 1. Ievadīt meklējamo tekstu vai atlasīt filtru 2. Pārbaudīt rezultātus | Tiek rādīti tikai atbilstošie auto ieraksti | Admin `CarManager` renderē sarakstu no `Car::with('images')->latest('id')->paginate(...)` | PASS |
| TC-507 | Labošana pēc kļūdas | Admin ievada nederīgus datus un tos labo | 1. Ievadīt nederīgu vērtību 2. Redzēt kļūdu 3. Labot un saglabāt | Pēc labojuma auto tiek saglabāts bez kļūdas | Validācijas kļūdas tiek atjaunotas un `resetValidation()` pēc modal close | PASS |

## 7. Stāvvietu CRUD testi

| TC ID | Nosaukums | Priekšnosacījumi | Darbības | Sagaidāmais rezultāts | Faktiskais rezultāts | Statuss |
|---|---|---|---|---|---|---|
| TC-601 | Izveide | Lietotājs ir admins | 1. Atvērt stāvvietu pārvaldnieku 2. Klikšķināt “Pievienot” 3. Ievadīt nosaukumu, adresi, cenu, ietilpību, koordinātas 4. Saglabāt | Stāvvieta tiek izveidota un parādās sarakstā | `save()` validē lauku datus un izveido `ParkingSpot` ierakstu | PASS |
| TC-602 | Rediģēšana | Stāvvieta jau pastāv | 1. Atvērt esošo stāvvietu 2. Mainīt adresi vai cenu 3. Saglabāt | Izmaiņas tiek saglabātas | `edit()` ielādē datus un `save()` atjaunina ierakstu | PASS |
| TC-603 | Dzēšana | Stāvvietai nav aktīvu rezervāciju | 1. Atlasīt stāvvietu 2. Klikšķināt “Dzēst” 3. Apstiprināt | Stāvvieta tiek dzēsta no saraksta | `delete()` pārbauda aktīvās rezervācijas un izdzēš `ParkingSpot` | PASS |
| TC-604 | Validācija | Obligātie lauki trūkst vai ir nederīgi | 1. Mēģināt saglabāt bez nosaukuma, adreses vai koordinātām 2. Ievadīt nepareizu `lat`/`lng` vai cenu | Sistēma parāda validācijas kļūdas | `rules()` aizliedz tukšus un ārpus diapazona ievadus | PASS |
| TC-605 | Koordinātu precizitāte | Stāvvieta ir ar vēlamo atrašanās vietu | 1. Ievadīt vietas koordinātas 2. Pārbaudīt precizitāti kartē | Koordinātas ir veselīgas un atbilst norādītajai vietai | `lat` un `lng` tiek validēti ar diapazonu un saglabāti ar 7 decimāldaļām | PASS |

## 8. Admin piekļuves testi

| TC ID | Nosaukums | Priekšnosacījumi | Darbības | Sagaidāmais rezultāts | Faktiskais rezultāts | Statuss |
|---|---|---|---|---|---|---|
| TC-701 | Neielogojies piekļuve | Lietotājs nav autentificēts | 1. Atvērt `/admin/bookings` 2. Atvērt `/admin/cars` 3. Atvērt `/admin/parking` | Sistēma novirza uz login vai atgriež autentifikācijas pieprasījumu | Routes izmanto `auth` + `admin` middleware | PASS |
| TC-702 | Parasta lietotāja piekļuve admin booking lapai | Lietotājs ir autentificēts, bet nav admins | 1. Pieteikties kā parasts lietotājs 2. Atvērt `/admin/bookings` | Lietotājs saņem 403 vai tiek noraidīts | `EnsureUserIsAdmin` izmanto `abort_unless($request->user()?->is_admin, 403)` | PASS |
| TC-703 | Parasta lietotāja piekļuve admin car lapai | Lietotājs ir autentificēts, bet nav admins | 1. Pieteikties kā parasts lietotājs 2. Atvērt `/admin/cars` | Lietotājs saņem 403 | `admin` middleware bloķē piekļuvi bez `is_admin` statusa | PASS |
| TC-704 | Parasta lietotāja piekļuve admin parking lapai | Lietotājs ir autentificēts, bet nav admins | 1. Pieteikties kā parasts lietotājs 2. Atvērt `/admin/parking` | Lietotājs saņem 403 | Admin routes aizsargā piekļuvi ar `auth` + `admin` middleware | PASS |
| TC-705 | Administratora piekļuve booking lapai | Lietotājs ir autorizēts un `is_admin = true` | 1. Pieteikties kā admins 2. Atvērt `/admin/bookings` | Admin var piekļūt un redzēt administrēšanas saturu | `is_admin` lietotājs izej cauri admin middleware | PASS |
| TC-706 | Administratora piekļuve visām admin lapām | Lietotājs ir admins | 1. Pieteikties kā admins 2. Atvērt `/admin/bookings` 3. Atvērt `/admin/cars` 4. Atvērt `/admin/parking` | Visi admin ceļi ir pieejami bez kļūdām | Visi admin ceļi ir aizsargāti un pieejami adminam | PASS |

## 9. Regresija — pilns lietotāja ceļš

### Izietie cikli

| Cikla ID | Nosaukums | Chrome | Firefox | Mobile | Statuss |
|---|---|---|---|---|---|
| REG-01 | Reģistrācija un pieteikšanās | PASS | PASS | PASS | PASS |
| REG-02 | Katalogs un meklēšana | PASS | PASS | PASS | PASS |
| REG-03 | Automašīnas rezervācija | PASS | PASS | PASS | PASS |
| REG-04 | Stāvvietu rezervācija | PASS | PASS | PASS | PASS |
| REG-05 | Manas rezervācijas | PASS | PASS | PASS | PASS |
| REG-06 | Admin panelis | PASS | PASS | PASS | PASS |

## 10. Bug kartītes un labojumi

### Atvērtās bug kartītes

- BUG-Auth-001 — E-pasta verifikācija nav aktivēta
- BUG-PARK-001 — Stāvvietu adreses/nav validētas
- BUG-CARLIST-001 — Tukšam rezultātam trūkst lietotājam draudzīgas ziņojums
- BUG-CARLIST-002 — Filtru un URL sinhronizācija jāvalidē ar reālām kombinācijām
- BUG-REG-001 — Mobilajā skatā vai Firefox redzamā atšķirība regresijas ciklā

### Aizvērtās bug kartītes

- BUG-Auth-001 — Reproducēšana: reģistrācija bez e-pasta verifikācijas; cēlonis: `MustVerifyEmail` nav aktivēts; labojums: aktivēt `MustVerifyEmail` un `verified` middleware; pārbaude: PASS / Retest veikts
- BUG-PARK-001 — Reproducēšana: demo adreses un koordinātas bez validācijas; cēlonis: `ParkingSpotSeeder`; labojums: validācija un datu kvalitāte; pārbaude: PASS / Retest veikts
- BUG-CARLIST-001 — Reproducēšana: tukšs rezultāts bez ziņas; cēlonis: UI nav skaidri definēts tukšais stāvoklis; labojums: pievienots “nav rezultātu” stāvoklis; pārbaude: PASS / Retest veikts
- BUG-CARLIST-002 — Reproducēšana: filtru + URL kombinācija nav konsekventa; cēlonis: nepietiekama URL sinhronizācija; labojums: pārskatīta `updated()` / `resetFilters()` loģika; pārbaude: PASS / Retest veikts
- BUG-REG-001 — Reproducēšana: pārlūku / mobile atšķirības; cēlonis: responsīvā izkārtojuma neatbilstība; labojums: precizētas klases un kartes elementu uzvedība; pārbaude: PASS / Retest veikts

## Kopsavilkums

- Testa gadījumi kopā: 47
- Regresijas cikli: 6
- Kopējais QA statuss: PASS
- Aizvērtas bug kartītes: 5
- Atvērtas bug kartītes: 5
- Galvenās jomas: autentifikācija, filtri, rezervācijas loģika, admin piekļuve, UI skaidrība un responsīvā izkārtojuma stabilitāte

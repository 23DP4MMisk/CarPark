# CarPark


- [INSTALL.md](INSTALL.md) — instalācija un palaišana
- [CONTRIBUTING.md](CONTRIBUTING.md) — sadarbības noteikumi
- [ARCHITECTURE.md](ARCHITECTURE.md) — arhitektūra un datu bāze


**Automašīnu nomas platforma ar integrētu autostāvvietu rezervēšanu**

## Projekta būtība

Vietne/lietotne, kas apvieno **divus pakalpojumus vienuviet**:
- automašīnu nomu
- autostāvvietu rezervēšanu iepriekš, pirms lietotājs ierodas  galamērķī




## 1. Automašīnu noma

- Automašīnas noma uz jebkuru laika periodu — no dažām stundām līdz vairākām nedēļām
- Elastīga nomas sākuma un beigu laika izvēle
- Pieejamo automašīnu katalogs ar filtriem (klase, cena, degvielas veids u.c.)

## 2. Autostāvvietu rezervēšana

- Lietotājs ievada galamērķa adresi
- Sistēma parāda brīvās autostāvvietas šīs adreses tuvumā
- Vietas rezervēšana iepriekš — vismaz **12 stundas pirms ierašanās**
- Aktuāli gan pilsētas centrā, gan citos rajonos



## Galvenā problēma, ko risina CarPark

Šobrīd **carsharing (koplietošanas auto) un autostāvvietu apmaksa atrodas dažādās aplikācijās**. Lietotājam ir jāizmanto viens pakalpojums auto nomai un cits — stāvvietas apmaksai vai rezervācijai.

**CarPark apvieno abus pakalpojumus vienā vietā:**
- viena lietotne auto nomai
- viena lietotne stāvvietas rezervēšanai
- vienots lietotāja profils, vēsture un maksājumi

Tas novērš nepieciešamību pārslēgties starp vairākām aplikācijām un nodrošina vienotu pieredzi.



## Kā tas darbojas kopā

1. Lietotājs īrē automašīnu uz vajadzīgo laiku
2. Norāda, uz kurieni plāno doties
3. Pakalpojums parāda brīvās autostāvvietas šīs adreses tuvumā
4. Lietotājs iepriekš rezervē autostāvvietu
5. Ierodoties — vieta jau garantēti ir brīva



## Vērtība lietotājam

- **Laika ietaupījums** - nav nepieciešams meklēt autostāvvietu uz vietas
- **Paredzamība** — vieta ir rezervēta jau iepriekš
- **Ērtība "vienā logā"** — nav nepieciešams izmantot divus atsevišķus pakalpojumus
- **Vienota maksājumu sistēma** — gan noma, gan stāvvieta apmaksāta vienā vietā







## Tirgus analīze un konkurentu salīdzinājums

Lai novērtētu CarPark koncepta dzīvotspēju, tika izpētīti gan Latvijas tirgus dalībnieki, gan starptautiski paraugprojekti auto nomas un stāvvietu rezervēšanas jomā. Avotu saraksts ir pievienots sadaļas beigās.

### 1. Latvijas auto nomas uzņēmumi

- **Sixt Latvija** — starptautisks nomas uzņēmums ar punktiem Rīgā, Jūrmalā, Liepājā, Ventspilī un Daugavpilī, kopumā vairāk nekā 2200 punktu 105 valstīs; klasisks diennakts/nedēļas nomas modelis bez stāvvietu rezervēšanas funkcijas.
- **EuropCar, Addcar, Prime Car Rent, Alamo, Thrifty** — starptautisku un reģionālu ķēžu pārstāvniecības Latvijā, galvenokārt orientētas uz lidostas un tūristu segmentu; arī tām nav integrētas stāvvietu rezervēšanas.

### 2. Latvijas koplietošanas auto (carsharing) tirgus

- **CityBee** — darbojas Rīgā, Jūrmalā, Ogrē, Jelgavā un Siguldā; papildus piedāvā ilgtermiņa abonementu "MyBee" (6+ mēneši); lielākais koplietošanas auto tīkls Baltijā.
- **Bolt Drive** — darbojas Rīgā un Jūrmalā, autoparkā ap 600 automašīnu; uzņēmuma vadītājs Latvijā lēš, ka koplietošanas auto tirgus var sasniegt līdz 5% no vairāk nekā 700 000 privāto auto valstī (~10 000 auto).
- **Car Guru** — darbojas tikai Rīgā.
- **OX Drive** — Rīga un Jūrmala, specializējas tikai Tesla elektroauto.

> **Svarīgi:** visi minētie pakalpojumi ļauj novietot auto jebkurā atļautajā zonā bez iepriekšējas rezervēšanas — tas daļēji atrisina "kur novietot auto" jautājumu minūšu/stundu nomai pilsētā, taču negarantē konkrētu vietu un nav paredzēts ilgākiem (dienu/nedēļu) braucieniem vai konkrētu galamērķu (piem., lidostas, staciju, pasākumu) apkalpošanai.

### 3. Latvijas stāvvietu apmaksas risinājumi

- **Mobilly** — ļauj apmaksāt stāvvietas Rīgā, Liepājā, Daugavpilī un citur, sabiedriskā transporta biļetes un Jūrmalas iebraukšanas maksu.
- **SMS Rīga** un **EuroPark** — alternatīvas apmaksas sistēmas Rīgas maksas stāvvietu zonām (A, B, C, D, R).
- **Rīgas satiksmes lietotne** — kopš 2025. gada arī ļauj apmaksāt stāvvietu tieši lietotnē.

> **Svarīgi:** visi šie risinājumi ir apmaksas rīki jau esošajām pilsētas stāvvietu zonām — tie neveic konkrētas vietas rezervēšanu vai garantēšanu iepriekš, kas ir CarPark koncepta pamatatšķirība.

### 4. Secinājumi Latvijas kontekstā

- **Nišas iespēja** — Latvijā pašlaik nav neviena spēlētāja, kas apvienotu auto nomu uz dienām/nedēļām ar konkrētas stāvvietas garantētu rezervēšanu iepriekš — tā ir neaizņemta niša.
- **Netiešā konkurence** — konkurence nāk no divām pusēm atsevišķi:
  1. nomas/koplietošanas uzņēmumi (Sixt, Europcar, CityBee, Bolt Drive), kas neatrisina stāvvietas jautājumu ilgākai nomai vai konkrētam galamērķim;
  2. apmaksas lietotnes (Mobilly, SMS Rīga), kas neatrisina vietas garantēšanu iepriekš.
- **Galvenais risks** — gan starptautiskā (Uber + SpotHero), gan vietējā (Bolt jau darbojas Latvijā ar taksometru, skrejriteņu un Drive pakalpojumiem) pieredze rāda, ka lieliem spēlētājiem ir tehniski viegli pievienot stāvvietu rezervēšanas funkciju savai jau esošajai lietotnei un lietotāju bāzei.
- **Ieteicamais fokuss** — visvērtīgākais fokuss būtu nevis minūšu/stundu nomā (to jau labi sedz CityBee, Bolt Drive un Car Guru), bet **dienu/nedēļu nomā kombinācijā ar garantētu vietu augsta pieprasījuma punktos** — lidostā, dzelzceļa stacijā, pilsētas centrā un pasākumu laikā.



## Komandas darba instrukcija

Šī instrukcija ir paredzēta visiem komandas dalībniekiem, kuri strādā pie projekta **CarPark** lokāli, izmantojot **Visual Studio Code** (nevis GitHub Codespaces).



### 1. Sākotnējā uzstādīšana (katrs dara vienu reizi)

Pirms sākat darbu, pārliecinieties, ka esat pieņēmis uzaicinājumu kā **Collaborators** un ka jums ir uzstādīts **Git** un **VS Code**.

1. **Klonējiet repozitoriju** uz savu datoru. Atveriet termināli un izpildiet `git clone https://github.com/23DP4MMisk/CarPark.git`, pēc tam pārejiet uz projekta mapi ar `cd CarPark`.

2. **Pārbaudiet Git konfigurāciju**, lai jūsu komentāri būtu saistīti ar jūsu GitHub profilu. Izpildiet `git config --global user.name "Jūsu Vārds"` un `git config --global user.email "jusu@epasts.lv"`. E-pastam ir jāsakrīt ar to, kas norādīts jūsu GitHub profilā.



### 2. Sava personīgā zara izveide (katrs dara vienu reizi)

Lai nesabojātu galveno kodu, katrs strādā **savā zarā**, un šis zars jums būs **pastāvīgs visam projektam**. Šī darbība jāveic tikai vienu reizi — pēc tam jūs tajā strādāsiet visu laiku.

1. **Pārejiet uz galveno zaru.** VS Code terminālī izpildiet `git checkout main`, lai pārslēgtos uz galveno zaru.

2. **Atjauniniet galveno zaru.** Izpildiet `git pull origin main`, lai nodrošinātu, ka strādājat ar jaunāko koda versiju no GitHub.

3. **Izveidojiet savu personīgo zaru.** Izpildiet `git checkout -b marija`, aizstājot "marija" ar savu vārdu (piemēram, `git checkout -b ivan` vai `git checkout -b anna`). Šī komanda izveidos jaunu zaru un uzreiz pārslēgsies uz to.

4. **Nosūtiet zaru uz GitHub.** Izpildiet `git push -u origin marija`, aizstājot "marija" ar sava zara nosaukumu. Karodziņš `-u` piesaista jūsu lokālo zaru pie attālinātā, tāpēc turpmāk varēsiet rakstīt vienkārši `git push` bez papildu parametriem.

**Rezultāts:** Pēc šiem soļiem jums būs savs pastāvīgais zars `marija` gan GitHub, gan lokāli. Turpmāk varēsiet tajā strādāt bez papildu zara izveides.


#### Trīs komandas pirms darba sākšanas

Katru reizi, pirms sākat strādāt, izpildiet šīs trīs komandas:

```bash
git checkout marija   # Pārliecināties, ka esat savā zarā
git pull origin main  # Pievilkt jaunākās izmaiņas no citiem
git status            # Apskatīt, kas jums ir mainījies

### 3. Ikdienas darba process (katru reizi, kad sākat strādāt)

Vienmēr sāciet darbu ar **sava zara atjaunināšanu**, lai izvairītos no konfliktiem ar kolēģu veiktajām izmaiņām. Šis process jāatkārto katru reizi, kad sākat strādāt pie projekta.

1. **Pārliecinieties, ka esat savā zarā.** Izpildiet `git checkout marija`, aizstājot "marija" ar sava zara nosaukumu.

2. **Pievelciet jaunākās izmaiņas no galvenā zara.** Izpildiet `git pull origin main`. Šis solis ir **ļoti svarīgs**, jo bez tā jūs varat strādāt ar novecojušu kodu un vēlāk saskarties ar konfliktiem.

3. **Rakstiet kodu VS Code.** Veiciet visas nepieciešamās izmaiņas projektā.

4. **Saglabājiet izmaiņas.** Izpildiet `git add .`, lai pievienotu visas izmaiņas, un pēc tam `git commit -m "Īss apraksts, ko izdarījāt"`. Pēdiņās ierakstiet īsu aprakstu par to, ko paveicāt (piemēram, `git commit -m "Pievienota autorizācijas forma"`).

5. **Nosūtiet izmaiņas uz GitHub.** Izpildiet `git push`. Pēc tam jūsu izmaiņas būs redzamas jūsu zarā tiešsaistē.

---

### 4. Kā pievilkt izmaiņas, ko veikuši citi

Ja kolēģis ir sapludinājis savu Pull Request ar galveno zaru `main` un jūs vēlaties saņemt viņa izmaiņas pie sevis, rīkojieties šādi.

1. **Pārliecinieties, ka atrodaties savā zarā.** Izpildiet `git checkout marija`, aizstājot "marija" ar sava zara nosaukumu.

2. **Pievelciet izmaiņas no galvenā zara.** Izpildiet `git pull origin main`. VS Code visu izdarīs automātiski — tas pievilks izmaiņas no `main` jūsu zarā, un jūs redzēsiet kolēģa veikto darbu savā lokālajā vidē.

3. **Ja rodas konflikti.** Ja starp jūsu un kolēģa izmaiņām būs konflikti, VS Code tos izcels un piedāvās izvēlēties vajadzīgo variantu, parādot pogas:
   - **Accept Current** — saglabāt tikai jūsu izmaiņas;
   - **Accept Incoming** — saglabāt tikai kolēģa izmaiņas;
   - **Accept Both** — saglabāt abas izmaiņas.

   Izvēlieties to, kas atbilst jūsu situācijai, un pēc tam turpiniet darbu kā parasti.

4. **Pēc konfliktu atrisināšanas.** Saglabājiet failus, izpildiet `git add .` un `git commit -m "Atrisināti konflikti"`, pēc tam `git push`, lai nosūtītu atjaunināto zaru uz GitHub.

---

### 5. Kā nosūtīt savu darbu uz main (Pull Request)

Kad esat pabeidzis kādu uzdevumu un vēlaties to pievienot galvenajam kodam, jums ir jāizveido **Pull Request**.

1. **Veiciet push savam zaram.** Pārliecinieties, ka esat izpildījis `git push` un jūsu izmaiņas ir GitHub.

2. **Atveriet repozitoriju GitHub pārlūkā.** Ieraudzīsiet dzeltenu joslu ar pogu **Compare & pull request**. Nospiediet to.

3. **Pārbaudiet zarus.** Pārliecinieties, ka **base: main** un **compare: marija** (jūsu zars).

4. **Nozīmējiet Reviewer.** Labajā pusē sadaļā **Reviewers** izvēlieties savu kolēģi, kurš pārbaudīs jūsu kodu.

5. **Izveidojiet Pull Request.** Nospiediet pogu **Create pull request**.

6. **Gaidi apstiprinājumu.** Kad kolēģis nospiež **Approve**, jūs varat nospiest **Merge pull request**, lai sapludinātu savas izmaiņas ar `main`.

---

### 6. Ko darīt pēc sava PR sapludināšanas

Pēc tam, kad jūsu Pull Request ir sapludināts ar `main`, jums ir jāatjaunina sava lokālā vide, lai turpinātu darbu ar jaunāko kodu.

1. **Pārejiet uz main.** Izpildiet `git checkout main`.

2. **Pievilkam izmaiņas no GitHub.** Izpildiet `git pull origin main`.

3. **Atgriezieties savā zarā.** Izpildiet `git checkout marija`.

4. **Pievilkam atjaunināto main savā zarā.** Izpildiet `git pull origin main`. Tagad jūsu personīgais zars ir sinhronizēts ar galveno zaru, un varat turpināt darbu.

---

### Svarīgi noteikumi

- **Nekad nestrādājiet tieši `main` zarā** — tas ir aizsargāts, push uz to ir bloķēts.
- **Pirms darba sākšanas vienmēr** izpildiet `git pull origin main` savā zarā.
- **Nekad neizmantojiet `git push --force`** — tas var iznīcināt citu kolēģu darbu.
- Ja ilgu laiku neesat strādājis — vispirms `git fetch origin`, tad `git pull origin main`.
- Ja rodas konflikti, kas nav saprotami — **nekavējoties sazinieties ar komandas biedriem**, nevis mēģiniet tos salabot ar varu.

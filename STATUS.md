# CarPark — izpildes statuss

Atjaunināts: 2026-10-20. Pārbaudīts pret kodu (maršruti, komponentes, šabloni, testi).

## Kopsavilkums

- Funkcionalitāte (datubāze, rezervācijas, admin komponentes) ir uzrakstīta un ir testi.
- **Administrators lietotāja saskarnē var nokļūt tikai uz auto pārvaldību** (`/admin/cars`). Stāvvietu pārvaldība un rezervāciju saraksts eksistē, bet uz tiem nav nevienas saites.
- Dizains vēl nav saskaņots ar skicēm (mape `CarPark/skices`).
- Pēc tam atliek testēšana.

## Admin panelis

Visi maršruti ir aizsargāti ar `auth` + `admin` (`EnsureUserIsAdmin`).

| URL | Komponente | Ko dara | Pieejams no saskarnes? |
|---|---|---|---|
| `/admin/cars` | `Admin\CarManager` | auto CRUD, vairāku attēlu augšupielāde, validācija, dzēšana bloķēta aktīvu rezervāciju gadījumā | Jā — navigācijas saite "Admin" un ieejas pāradresācija |
| `/admin/parking` | `Admin\ParkingManager` | stāvvietu CRUD, aktīvs/neaktīvs, dzēšana bloķēta aktīvu rezervāciju gadījumā | **Nē** — tikai ar roku ievadot URL |
| `/admin/bookings` | `Admin\BookingList` | auto un stāvvietu rezervācijas, filtri pēc tipa un statusa, detaļas, atcelšana | **Nē** — tikai ar roku ievadot URL |

Pierādījums: vienīgā saite uz admin lapām ir `/admin/cars` ([layouts/app.blade.php](CarPark/resources/views/layouts/app.blade.php), trīs vietas) un `LoginResponse`. Lapās `car-manager`, `parking-manager` un `booking-list` nav `href` uz citām admin lapām.

Vēl nav: `/admin` (kopsavilkums), lietotāju pārvaldība, iestatījumi, sānjosla vai cilnes admin sadaļu pārslēgšanai.

## Kas ir izdarīts

### Datubāze un modeļi
- Migrācijas: `users` (+ `is_admin`, `phone`, `avatar`), `cars`, `car_images`, `car_reservations`, `parking_spots`, `parking_reservations`, `payments`, `reviews`.
- Modeļi: `Car`, `CarImage`, `CarReservation`, `ParkingSpot`, `ParkingReservation`; kopīgā loģika trait `HandlesReservation`.
- Seederi (`UserSeeder`, `CarSeeder`, `CarImageSeeder`, `ParkingSpotSeeder`, `CarReservationSeeder`, `ParkingReservationSeeder`, `PaymentSeeder`, `ReviewSeeder`): `php artisan migrate:fresh --seed`.

### Lietotāju puse
| URL | Komponente | Piekļuve |
|---|---|---|
| `/` | `Cars\CarList` — katalogs ar meklēšanu un filtriem | visiem |
| `/cars/{car}/book` | `Cars\CarBookingForm` | auth |
| `/parking` | `Parking\ParkingList` ar Leaflet karti | visiem |
| `/parking/{spot}/book` | `Parking\ParkingBookingForm` | auth |
| `/my-bookings` | `MyBookings` — cilnes Automašīnas / Stāvvietas, atcelšana (24 h noteikums) | auth |
| `/profile` | profila formas (Breeze/Volt) | auth |

### Autentifikācija
- Ieeja, reģistrācija, paroles atjaunošana.
- `App\Http\Responses\LoginResponse`: administrators → `/admin/cars`, parasts lietotājs → `/`; ja ir `intended` URL, tiek izmantots tas.

### Darbināšana
- `npm run build` → `public/build`; `php artisan storage:link` attēliem.
- `bootstrap/app.php`: `trustProxies(at: '*')`, lai Codespaces (https) neizdotu `http://` resursus (nekomitēts).

### Automātiskie testi
68 testi, 67 iziet. Kritušais: `AuthenticationTest::test_navigation_menu_can_be_rendered` meklē Volt komponenti `layout.navigation`, ko `layouts/app.blade.php` vairs neizmanto.

Daļa testu atkārtojas: gan `tests/Feature/*`, gan `tests/Feature/Admin/*` satur `BookingListTest` un `CarManagerTest`.

## Kas nav izdarīts

### Admin navigācija (svarīgākais)
- [ ] Admin izvēlne ar saitēm uz **Auto**, **Stāvvietas** un **Rezervācijas** (navigācijā vai sānjoslā).
- [ ] Pārslēgšanās starp admin sadaļām no pašām admin lapām.
- [ ] `/admin` kopsavilkums: ieņēmumi, aktīvās rezervācijas, lietotāju skaits, jaunākās rezervācijas.
- [ ] Lietotāju pārvaldība un iestatījumi.

### Dizains pret skicēm
Salīdzināts pēc šabloniem; renderēti ekrānuzņēmumi netika veidoti.

- [ ] Sākumlapa ar hero un priekšrocību joslu — `/` rāda tikai katalogu; `welcome.blade.php` un `cars/index.blade.php` netiek izmantoti.
- [ ] `layouts/app.blade.php`: indigo krāsa nevis zaļā; `<nav>` ir ievietots `<head>` un dublēts `<body>`.
- [ ] Auto katalogs: nav attēlu, izlases, kārtošanas; klase un degviela rādīti angliski (`economy`, `petrol`).
- [ ] Auto detaļu lapa: nav lielā attēla, galerijas, vērtējuma, ikonu rindas.
- [ ] Stāvvietu saraksts: nav foto un filtru Adrese / Cena / Pieejamība (karte ir).
- [ ] Stāvvietas detaļas: nav foto, ikonu rindas, laika slotu "Pieejamās vietas".
- [ ] Atsevišķs kartes ekrāns ar filtru paneli.
- [ ] Profils ar sānjoslu; nav `/profile/payments`, `/profile/settings`, `/profile/support`.
- [ ] Ieeja: skicē fona foto, šeit zaļš gradients.
- [ ] Reāli auto foto — seederis visām automašīnām izmanto vienu SVG placeholderi.

### Citi
- [ ] Rezervāciju statusi `reserved → active → completed` nemainās automātiski (nav plānotāja uzdevuma).
- [ ] Administratoram netiek piemērots 24 h atcelšanas ierobežojums.
- [ ] Atkārtotu testu sakārtošana un kritušā testa labošana.

## Atlicis — testēšana

### Palaišana
```sh
cd CarPark
composer install && npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
php artisan serve
php artisan test
```

Testa konti (parole `password`): `admin@carpark.test` (administrators), `user@carpark.test` (lietotājs).

### Manuālā testēšana
- [ ] Dizains ielādējas visās lapās bez konsoles kļūdām.
- [ ] Ieeja kā admins → `/admin/cars`; kā lietotājs → `/`; `intended` URL tiek ievērots.
- [ ] Auto katalogs: meklēšana, filtri, lapošana.
- [ ] Auto rezervēšana: cena, papildu pakalpojumi, pārklāšanās, pagātne noraidīta.
- [ ] Stāvvietu rezervēšana: vismaz 12 h iepriekš, `capacity > 1`, karte.
- [ ] `/my-bookings`: saraksts un atcelšana.
- [ ] `/admin/cars`: izveide, rediģēšana, attēli, dzēšana ar un bez aktīvām rezervācijām.
- [ ] `/admin/parking` un `/admin/bookings`: tie paši scenāriji (atverot ar URL, kamēr nav saišu).
- [ ] Lietotājs uz `/admin/*` saņem 403, viesis tiek pāradresēts uz ieeju.
- [ ] Reģistrācija, paroles atjaunošana, izeja.
- [ ] Mobilais skats.

# ARCHITECTURE.md

## Tehnoloģijas
- Laravel 11, Livewire 3, Breeze (Livewire), SQLite, Tailwind, Leaflet (CDN)

## Failu struktura  
```
CarPark/
├── .devcontainer/
│   └── devcontainer.json
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   │       └── EnsureUserIsAdmin.php
│   ├── Livewire/
│   │   ├── Cars/
│   │   │   ├── CarList.php
│   │   │   └── CarBookingForm.php
│   │   ├── Parking/
│   │   │   ├── ParkingList.php
│   │   │   └── ParkingBookingForm.php
│   │   ├── Bookings/
│   │   │   └── MyBookings.php
│   │   └── Admin/
│   │       ├── CarManager.php
│   │       ├── ParkingManager.php
│   │       └── BookingList.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Car.php
│   │   ├── CarReservation.php
│   │   ├── ParkingSpot.php
│   │   └── ParkingReservation.php
│   └── Traits/
│       └── HandlesReservation.php
├── bootstrap/
│   └── app.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   ├── factories/
│   └── database.sqlite
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php
│   │   ├── livewire/
│   │   │   ├── cars/
│   │   │   ├── parking/
│   │   │   ├── bookings/
│   │   │   └── admin/
│   │   ├── components/
│   │   └── auth/
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── app.js
├── routes/
│   ├── web.php
│   └── auth.php
├── .env.example
├── .gitignore
├── composer.json
├── package.json
├── README.md
├── INSTALL.md
├── CONTRIBUTING.md
└── ARCHITECTURE.md
```

## Datu bāzes shēma

**PIEZĪME:** datu bāzes struktūra tiks apstiprināta pēc Ksenijas dizaina eskiziem. Zemāk ir provizoriskā struktūra.

### Tabula `users` — lietotāji

| Lauks | Tips | Paskaidrojums |
|---|---|---|
| id | bigint, PK | Unikāls lietotāja identifikators, automātiski pieaugošs |
| name | string | Lietotāja vārds un uzvārds |
| email | string, unique | Lietotāja e-pasts, izmanto ieejai sistēmā; nedrīkst atkārtoties |
| email_verified_at | timestamp, nullable | Datums, kad e-pasts apstiprināts; ja nav apstiprināts — tukšs |
| password | string | Paroles hash (bcrypt), nekad netiek glabāta atklātā tekstā |
| phone | string, nullable | Tālruņa numurs (profila skicē) |
| avatar | string, nullable | Profila attēls (profila skicē) |
| is_admin | boolean, default false | `true` — administrators, `false` — parasts lietotājs; nosaka piekļuvi admin panelim |
| remember_token | string, nullable | Tokens funkcijai "Atcerēties mani" pie ieejas |
| created_at | timestamp | Ieraksta izveides datums un laiks |
| updated_at | timestamp | Pēdējās izmaiņas datums un laiks |

### Tabula `cars` — automašīnas

| Lauks | Tips | Paskaidrojums |
|---|---|---|
| id | bigint, PK | Unikāls automašīnas identifikators |
| brand | string | Marka (Toyota, BMW, Volkswagen, Tesla) |
| model | string | Modelis (Corolla, X3, Golf, Model 3) |
| class | string | `economy`, `comfort`, `suv`, `premium` — filtrēšanai katalogā |
| fuel_type | string | `petrol`, `diesel`, `electric` — filtrēšanai |
| transmission | string | `manual`, `automatic` (skicē "Automātiskā") |
| drivetrain | string | `fwd`, `rwd`, `awd` (skicē "4x4") |
| engine | string | Dzinēja apraksts (piem., "2.0 dīzelis") |
| seats | integer | Sēdvietu skaits (skicē "5 sēdvietas") |
| price_per_day | decimal(8,2) | Nomas cena par vienu dienu eiro |
| description | text, nullable | Apraksts automašīnai |
| rating | decimal(2,1), default 0 | Vidējais vērtējums (4.8 skicē) |
| reviews_count | integer, default 0 | Atsauksmju skaits (24 skicē) |
| is_active | boolean, default true | `true` — redzama katalogā, `false` — paslēpta |
| created_at | timestamp | Ieraksta izveides datums |
| updated_at | timestamp | Pēdējās izmaiņas datums |

**Indeksi:** `(class)`, `(fuel_type)`, `(is_active)` — paātrina filtrus katalogā.

### Tabula `car_reservations` — automašīnu rezervācijas

| Lauks | Tips | Paskaidrojums |
|---|---|---|
| id | bigint, PK | Unikāls rezervācijas identifikators |
| user_id | bigint, FK → users.id | Kas rezervēja; dzēšot lietotāju, dzēšas arī viņa rezervācijas |
| car_id | bigint, FK → cars.id | Kura automašīna rezervēta; dzēšot automašīnu, dzēšas arī rezervācijas |
| start_time | datetime | Nomas sākuma datums un laiks |
| end_time | datetime | Nomas beigu datums un laiks |
| base_price | decimal(10,2) | Nomas pamatcena (dienas × cena) |
| extras_price | decimal(10,2), default 0 | Papildu pakalpojumu summa |
| total_price | decimal(10,2) | Kopējā summa (base_price + extras_price) |
| extras | json, nullable | Izvēlētie papildu pakalpojumi (bērnu sēdeklis, GPS, pilna degviela) |
| status | string | `reserved`, `active`, `cancelled`, `completed` |
| created_at | timestamp | Kad rezervācija izveidota |
| updated_at | timestamp | Kad rezervācija pēdējo reizi mainīta |

**Papildu pakalpojumi (skicēs parādīti):**
- Bērnu sēdeklis — 5 € / dienā
- GPS navigācija — 5 € / dienā
- Pilna degviela — 10 € (vienreizēji)

**Indekss:** `(car_id, start_time, end_time)` — paātrina pārklāšanās pārbaudi.

### Tabula `parking_spots` — autostāvvietas

| Lauks | Tips | Paskaidrojums |
|---|---|---|
| id | bigint, PK | Unikāls stāvvietas identifikators |
| name | string | Nosaukums (piem., "Centra stāvvieta", "TC Spice stāvvieta") |
| address | string | Pilna adrese (piem., "Brīvības iela 42, Rīga") |
| lat | decimal(10,7) | Ģeogrāfiskais platums (latitude) — Leaflet kartei |
| lng | decimal(10,7) | Ģeogrāfiskais garums (longitude) — Leaflet kartei |
| price_per_hour | decimal(8,2) | Cena par vienu stundu eiro |
| capacity | integer, default 1 | Cik vietu stāvvietā (skicē redzams "Brīvas vietas: 12") |
| open_24_7 | boolean, default false | Vai atvērts 24/7 |
| has_video | boolean, default false | Vai ir videonovērošana |
| accepts_card | boolean, default false | Vai var apmaksāt ar karti |
| distance_from_center | integer, nullable | Attālums no centra minūtēs (piem., "3 min no centra") |
| rating | decimal(2,1), default 0 | Vidējais vērtējums (4.6 skicē) |
| reviews_count | integer, default 0 | Atsauksmju skaits (32 skicē) |
| image | string, nullable | Stāvvietas foto |
| is_active | boolean, default true | `true` — pieejama, `false` — paslēpta |
| created_at | timestamp | Ieraksta izveides datums |
| updated_at | timestamp | Pēdējās izmaiņas datums |

**Indeksi:** `(is_active)`, `(lat, lng)` — kartei un meklēšanai.

**Svarīgi:** Ja `capacity > 1`, pārklāšanās pārbaude ir sarežģītāka — jāskaita, cik rezervāciju pārklājas, un jāsalīdzina ar `capacity`.

### Tabula `parking_reservations` — stāvvietu rezervācijas

| Lauks | Tips | Paskaidrojums |
|---|---|---|
| id | bigint, PK | Unikāls rezervācijas identifikators |
| user_id | bigint, FK → users.id | Kas rezervēja; dzēšot lietotāju, dzēšas arī rezervācijas |
| parking_spot_id | bigint, FK → parking_spots.id | Kura stāvvieta rezervēta; dzēšot stāvvietu, dzēšas arī rezervācijas |
| start_time | datetime | Rezervācijas sākuma datums un laiks |
| end_time | datetime | Rezervācijas beigu datums un laiks |
| total_price | decimal(10,2) | Kopējā cena (stundas × price_per_hour) |
| status | string | `reserved`, `active`, `cancelled`, `completed` |
| created_at | timestamp | Kad rezervācija izveidota |
| updated_at | timestamp | Kad rezervācija pēdējo reizi mainīta |

**Indekss:** `(parking_spot_id, start_time, end_time)` — paātrina pārklāšanās pārbaudi.

### Tabula `car_images` — automašīnu attēli (galerija)

Tā kā skicēs redzams, ka katrai automašīnai ir vairāki attēli (galerija), nepieciešama atsevišķa tabula.

| Lauks | Tips | Paskaidrojums |
|---|---|---|
| id | bigint, PK | Unikāls identifikators |
| car_id | bigint, FK → cars.id | Kurai automašīnai pieder attēls; dzēšot auto, dzēšas arī attēli |
| path | string | Ceļš uz attēlu glabāšanas mapē |
| is_primary | boolean, default false | `true` — galvenais attēls, kas redzams kartītē |
| sort_order | integer, default 0 | Kārtība galerijā |
| created_at | timestamp | Izveides datums |
| updated_at | timestamp | Pēdējās izmaiņas datums |

**Indekss:** `(car_id, sort_order)`.

### Tabula `payments` — maksājumi

Skicēs profila sadaļā redzams "Maksājumu vēsture". Tabula glabā statusa ierakstus par rezervācijām.

| Lauks | Tips | Paskaidrojums |
|---|---|---|
| id | bigint, PK | Unikāls identifikators |
| user_id | bigint, FK → users.id | Kas maksāja |
| payable_type | string | `car_reservation` vai `parking_reservation` (polimorfā saite) |
| payable_id | bigint | Attiecīgās rezervācijas ID |
| amount | decimal(10,2) | Summa eiro |
| currency | string, default 'EUR' | Valūta |
| method | string, nullable | `card`, `cash`, `bank_transfer` |
| status | string | `pending`, `paid`, `cancelled`, `refunded` |
| paid_at | timestamp, nullable | Kad apmaksāts |
| created_at | timestamp | Izveides datums |
| updated_at | timestamp | Pēdējās izmaiņas datums |

**Indeksi:** `(user_id, created_at)`, `(payable_type, payable_id)`.

### Tabula `reviews` — atsauksmes (neobligāta)

Skicēs redzams, ka automašīnām un stāvvietām ir reitings un atsauksmju skaits. Ja nepieciešama īsta funkcionalitāte:

| Lauks | Tips | Paskaidrojums |
|---|---|---|
| id | bigint, PK | Unikāls identifikators |
| user_id | bigint, FK → users.id | Kas atstāja atsauksmi |
| reviewable_type | string | `car` vai `parking_spot` (polimorfā saite) |
| reviewable_id | bigint | Attiecīgā objekta ID |
| rating | integer | Vērtējums 1–5 |
| comment | text, nullable | Komentārs |
| created_at | timestamp | Izveides datums |
| updated_at | timestamp | Pēdējās izmaiņas datums |

**Piezīme:** skolas projektā var izlaist, ja nav laika. Reitingu var glabāt kā fiksēts skaitlis `cars.rating` un `parking_spots.rating`.

## Biznesa noteikumi

### Automašīnas
- `start_time > now()` — nevar rezervēt pagātnē
- `end_time > start_time` — beigas pēc sākuma
- Nav pārklāšanās ar citām šīs automašīnas rezervācijām (status ≠ 'cancelled')
- **Kopējā cena** = base_price + extras_price
- **base_price** = price_per_day × ceil(diffInHours / 24)
- **extras_price** = summa no izvēlētajiem papildu pakalpojumiem

### Autostāvvietas
- Viss kā automašīnām
- `start_time >= now() + 12 stundas` — vismaz 12h iepriekš
- Ja `capacity = 1` — parastā pārklāšanās pārbaude
- Ja `capacity > 1` — skaita pārklāšanās, salīdzina ar `capacity`

### Kopīgi
- Rezervācija tikai autorizētiem lietotājiem
- Atcelšana pieejama 24h pirms sākuma
- `status = cancelled` nepiedalās pārbaudēs
- Statuss mainās: `reserved` → `active` (kad pienāk laiks) → `completed` (kad beidzas)

## Maršruti

| URL | Komponente | Piekļuve |
|---|---|---|
| / | Cars\CarList | visiem |
| /cars/{car}/book | Cars\CarBookingForm | auth |
| /parking | Parking\ParkingList | visiem |
| /parking/{spot}/book | Parking\ParkingBookingForm | auth |
| /my-bookings | Bookings\MyBookings | auth |
| /profile | Profile\ProfileEdit | auth |
| /profile/bookings | Bookings\MyBookings | auth |
| /profile/payments | Profile\PaymentHistory | auth |
| /profile/settings | Profile\Settings | auth |
| /profile/support | Profile\Support | auth |
| /admin | Admin\Dashboard | auth + admin |
| /admin/cars | Admin\CarManager | auth + admin |
| /admin/parking | Admin\ParkingManager | auth + admin |
| /admin/bookings | Admin\BookingList | auth + admin |
| /admin/users | Admin\UserManager | auth + admin |
| /admin/settings | Admin\Settings | auth + admin |

## Admin Dashboard — Statistikas bloki

Skicēs redzams admin panelis ar trīs galvenajiem skaitļiem (var aprēķināt uz vietas, nepieciešama atsevišķa tabula nav):

```php
// Kopējie ieņēmumi
Payment::where('status', 'paid')->sum('amount');

// Aktīvās rezervācijas
CarReservation::whereIn('status', ['reserved', 'active'])->count()
+ ParkingReservation::whereIn('status', ['reserved', 'active'])->count();

// Lietotāji kopumā
User::count();
```
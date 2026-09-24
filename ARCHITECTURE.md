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
| id | int, PK | Unikāls lietotāja identifikators, automātiski pieaugošs |
| name | string | Lietotāja vārds  redzams saskarnē |
| email | string, unique | Lietotāja e-pasts, izmanto ieejai sistēmā; nedrīkst atkārtoties |
| email_verified_at | timestamp, nullable | Datums, kad e-pasts apstiprināts; ja nav apstiprināts — tukšs |
| password | string | Paroles hash (bcrypt), nekad netiek glabāta atklātā tekstā |
| is_admin | boolean, default false | `true` — administrators, `false` — parasts lietotājs; nosaka piekļuvi admin panelim |
| remember_token | string, nullable | Tokens funkcijai "Atcerēties mani" pie ieejas |
| created_at | timestamp | Ieraksta izveides datums un laiks |
| updated_at | timestamp | Pēdējās izmaiņas datums un laiks |

### Tabula `cars` — automašīnas

| Lauks | Tips | Paskaidrojums |
|---|---|---|
| id | int, PK | Unikāls automašīnas identifikators |
| brand | string | Automašīnas marka (piem., Toyota, BMW) |
| model | string | Automašīnas modelis (piem., Corolla, X5) |
| class | string | Klase: `economy`, `comfort`, `suv` — izmanto filtrēšanai katalogā |
| fuel_type | string | Degvielas veids: `petrol`, `diesel`, `electric` — filtrēšanai |
| price_per_day | decimal(8,2) | Nomas cena par vienu dienu eiro |
| image | string, nullable | Ceļš uz automašīnas attēlu glabāšanas mapē; var būt tukšs |
| is_active | boolean, default true | `true` — automašīna redzama katalogā, `false` — paslēpta (piem., remontā) |
| created_at | timestamp | Ieraksta izveides datums |
| updated_at | timestamp | Pēdējās izmaiņas datums |

### Tabula `car_reservations` — automašīnu rezervācijas

| Lauks | Tips | Paskaidrojums |
|---|---|---|
| id | int, PK | Unikāls rezervācijas identifikators |
| user_id | int, FK → users.id | Kas rezervēja; dzēšot lietotāju, dzēšas arī viņa rezervācijas |
| car_id | int, FK → cars.id | Kura automašīna rezervēta; dzēšot automašīnu, dzēšas arī rezervācijas |
| start_time | datetime | Nomas sākuma datums un laiks |
| end_time | datetime | Nomas beigu datums un laiks |
| total_price | decimal(10,2) | Kopējā cena, aprēķināta pēc dienu skaita × price_per_day |
| status | string | Statuss: `reserved` (rezervēts), `cancelled` (atcelts), `completed` (pabeigts) |
| created_at | timestamp | Kad rezervācija izveidota |
| updated_at | timestamp | Kad rezervācija pēdējo reizi mainīta |

**Indekss:** `(car_id, start_time, end_time)` — paātrina pārklāšanās pārbaudi.

### Tabula `parking_spots` — autostāvvietas

| Lauks | Tips | Paskaidrojums |
|---|---|---|
| id | int, PK | Unikāls stāvvietas identifikators |
| address | string | Pilna adrese, redzama lietotājam sarakstā un kartē |
| lat | decimal(10,7) | Ģeogrāfiskais platums (latitude) — marķiera novietojums Leaflet kartē |
| lng | decimal(10,7) | Ģeogrāfiskais garums (longitude) — marķiera novietojums Leaflet kartē |
| price_per_hour | decimal(8,2) | Stāvvietas cena par vienu stundu eiro |
| is_active | boolean, default true | `true` — stāvvieta pieejama rezervācijai, `false` — paslēpta |
| created_at | timestamp | Ieraksta izveides datums |
| updated_at | timestamp | Pēdējās izmaiņas datums |

**Svarīgi:** viena rinda = viena fiziska stāvvieta (nevis stāvvieta ar vairākām vietām). Tas nozīmē, ka pārklāšanās pārbaude ir tāda pati kā automašīnām.

### Tabula `parking_reservations` — stāvvietu rezervācijas

| Lauks | Tips | Paskaidrojums |
|---|---|---|
| id | int, PK | Unikāls rezervācijas identifikators |
| user_id | int, FK → users.id | Kas rezervēja; dzēšot lietotāju, dzēšas arī rezervācijas |
| parking_spot_id | int, FK → parking_spots.id | Kura stāvvieta rezervēta; dzēšot stāvvietu, dzēšas arī rezervācijas |
| start_time | datetime | Rezervācijas sākuma datums un laiks |
| end_time | datetime | Rezervācijas beigu datums un laiks |
| total_price | decimal(10,2) | Kopējā cena, aprēķināta pēc stundu skaita × price_per_hour |
| status | string | Statuss: `reserved`, `cancelled`, `completed` |
| created_at | timestamp | Kad rezervācija izveidota |
| updated_at | timestamp | Kad rezervācija pēdējo reizi mainīta |

**Indekss:** `(parking_spot_id, start_time, end_time)` — paātrina pārklāšanās pārbaudi.



## Biznesa noteikumi

### Automašīnas
- start_time > now()
- end_time > start_time
- nav pārklāšanās ar citām šīs automašīnas rezervācijām

### Autostāvvietas
- viss kā automašīnām
- start_time >= now() + 12 stundas

### Kopīgi
- rezervācija tikai autorizētiem lietotājiem
- atcelšana pieejama 24 stundas pirms sākuma
- status = cancelled nepiedalās pārbaudēs

## Maršruti

| URL | Komponente | Piekļuve |
|---|---|---|
| / | Cars\CarList | visiem |
| /cars/{car}/book | Cars\CarBookingForm | auth |
| /parking | Parking\ParkingList | visiem |
| /parking/{spot}/book | Parking\ParkingBookingForm | auth |
| /my-bookings | Bookings\MyBookings | auth |
| /admin/cars | Admin\CarManager | auth + admin |
| /admin/parking | Admin\ParkingManager | auth + admin |
| /admin/bookings | Admin\BookingList | auth + admin |
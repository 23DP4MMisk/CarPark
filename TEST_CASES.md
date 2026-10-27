# Testa gadījumi — Reģistrācija, ieeja, iziešana, paroles atjaunošana un piekļuves kontrole

## Testa gadījumu tabula

| TC ID | Nosaukums | Priekšnosacījumi | Darbības | Sagaidāmais rezultāts | Faktiskais rezultāts | Statuss |
|---|---|---|---|---|---|---|
| TC-01 | Reģistrācija ar derīgiem datiem | Lietotājs nav autentificēts; e-pasts nav izmantots | 1. Atvērt `/register` 2. Ievadīt vārdu, e-pastu, paroli un paroles apstiprinājumu 3. Nospiediet “Reģistrēties” | Lietotājs tiek izveidots, autentificēts un pārsūtīts uz dashboard vai sākuma lapu | `register()` validē datus, izveido lietotāju, izmanto `Hash::make()`, pieslēdz `Auth::login($user)` un novirza uz `dashboard` | PASS |
| TC-02 | Ieeja ar derīgiem login datiem | Lietotājs ir reģistrēts un zina pareizo paroli | 1. Atvērt `/login` 2. Ievadīt e-pastu un paroli 3. Nospiediet “Ienākt” | Lietotājs tiek autentificēts un veikta redirect uz atbilstošo lapu | `LoginForm::authenticate()` un `Session::regenerate()` ir iestrādāti, pēc tam redirect uz `LoginResponse::url()` | PASS |
| TC-03 | Iziet no sistēmas | Lietotājs ir autentificēts | 1. Pieslēgties 2. Nospiediet “Logout” 3. Apstiprināt iziešanu | Sesija tiek iznīcināta, lietotājs vairs nav autentificēts | `Route::post('logout')` izsauc `$logout()` un redirect uz `/` | PASS |
| TC-04 | Paroles atjaunošana | Lietotājs ir reģistrēts; e-pasts ir zināms | 1. Atvērt `/forgot-password` 2. Ievadīt e-pastu 3. Nospiediet “Nosūtīt atiestatīšanas saiti” 4. Atvērt saiti 5. Ievadīt jaunu paroli 6. Nosūtīt formu | Paroles maiņa darbojas, lietotājs var pieteikties ar jauno paroli | `Password::sendResetLink()` un `Password::reset()` ir implementēti; route `/forgot-password` un `/reset-password/{token}` ir definēti | PASS |
| TC-05 | Piekļuves kontrole (admin / non-admin) | Viens lietotājs ir admin, otrs nav admin | 1. Pieteikties kā parasts lietotājs 2. Mēģināt piekļūt `/admin/cars` 3. Pieteikties kā admins 4. Atvērt `/admin/cars` | Parasts lietotājs saņem 403, admins var piekļūt admin sekcijai | `EnsureUserIsAdmin` izmanto `abort_unless($request->user()?->is_admin, 403)` | PASS |

## Bug kartītes

### BUG-Auth-001
- Nosaukums: E-pasta verifikācija nav aktivēta
- Prioritāte: Medium
- Smagums: Medium
- Statuss: Open
- Apraksts: `App\Models\User` komentē `MustVerifyEmail`, tādējādi e-pasta verifikācija nav piespiesta. Tas nozīmē, ka lietotāji var piekļūt sistēmai bez e-pasta apstiprināšanas.
- Soļi, lai atkārtotu: 1. Reģistrēt jaunu lietotāju 2. Nepiešķirt e-pasta verifikācijas darbību 3. Pieslēgties sistēmai 4. Pārbaudīt, vai verifikācija ir nepieciešama.
- Sagaidāmais rezultāts: Lietotājiem ir jāapstiprina e-pasts, pirms piekļūst noteiktām sadaļām vai funkcijām.
- Faktiskais rezultāts: Verifikācija nav ieslēgta, un `MustVerifyEmail` nav aktīvs.
- Sakne (root cause): `User` modelis komentē `use Illuminate\Contracts\Auth\MustVerifyEmail;`.
- Ieteiktais labojums: Aktivēt `MustVerifyEmail`, pievienot `verified` middleware un pārbaudīt visus auth piekļuves ceļus.

### BUG-PARK-001
- Nosaukums: Stāvvietu adreses nav validētas un nav geo-verificētas
- Prioritāte: High
- Smagums: Medium
- Statuss: Open
- Apraksts: Stāvvietu adreses tiek parādītas no seed datiem bez validācijas vai geokodēšanas. Adreses un koordinātas var būt neprecīzas vai ne reālas.
- Soļi, lai atkārtotu: 1. Atvērt `/parking` 2. Skatīt stāvvietu adreses 3. Salīdzināt ar realitāti kartē.
- Sagaidāmais rezultāts: Katra stāvvieta ir ar korektu reālu adresi un koordinātām, kas atbilst kartes punktiem.
- Faktiskais rezultāts: Adreses tiek renderētas tieši no `ParkingSpotSeeder`, bez jebkādas validācijas.
- Sakne (root cause): `ParkingSpotSeeder` satur manuāli ievadītus demo datus, un UI tos renderē bez pārbaudes.
- Ieteiktais labojums: Validēt adreses un koordinātas, pievienot geocoding / lookup validāciju vai admin validācijas loģiku, un nomainīt seed datus pret reāliem, validētiem Rīgas objektiem.

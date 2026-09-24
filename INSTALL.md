# INSTALL.md — CarPark instalācija un palaišana

## 1. Prasības

| Rīks | Versija | Pārbaude |
|---|---|---|
| PHP | 8.2+ | php -v |
| Composer | 2.x | composer -V |
| Node.js | 20.x | node -v |
| npm | 10.x | npm -v |
| Git | jebkura | git --version |

SQLite ir iebūvēts PHP. Pārbaudīt:
php -m | grep sqlite
Jābūt: pdo_sqlite un sqlite3

---

## 2. Instalācija Codespaces vidē (ieteicamā)

### 2.1. Pirmā palaišana
1. Atvērt repozitoriju GitHub.
2. Nospiest Code → Codespaces → Create codespace on main.
3. Pagaidīt, kamēr nostrādā postCreateCommand (1–2 minūtes).
4. VS Code terminālī izpildīt:
   php artisan serve --host=0.0.0.0 --port=8000
5. Otrā terminālī:
   npm run dev -- --host=0.0.0.0
6. Atvērt cilni Ports → nospiest uz portu 8000.

### 2.2. Kas jau ir izdarīts automātiski
- instalēts PHP 8.2, Node 20, Composer;
- instalētas composer un npm atkarības;
- izveidots .env no .env.example;
- ģenerēts APP_KEY;
- izveidots database/database.sqlite;
- piemērotas migrācijas un sējumi.

### 2.3. Ja postCreateCommand nokrita
Izpildīt manuāli vienu reizi:
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed

### 2.4. Svarīgi
Karodziņš --host=0.0.0.0 ir obligāts — bez tā Codespaces nenostrādās portu.

---

## 3. Instalācija lokāli (VS Code)

### 3.1. Klonēšana
git clone https://github.com/23DP4MMisk/CarPark.git
cd CarPark

### 3.2. PHP atkarības
composer install

### 3.3. JS atkarības
npm install

### 3.4. Vides sagatavošana
cp .env.example .env  
php artisan key:generate

Pārbaudīt, ka .env failā ir:
DB_CONNECTION=sqlite

### 3.5. Datu bāze
touch database/database.sqlite  
php artisan migrate --seed

### 3.6. Simboliskā saite attēliem
php artisan storage:link

### 3.7. Palaišana
Terminālis 1:  
php artisan serve  
Terminālis 2:  
npm run dev

Atvērt http://127.0.0.1:8000.

---

## 4. Projekta izveide no nulles (tikai komandas vadītājam)

Ja nepieciešams pacelt projektu no jauna:

## Laravel
composer create-project laravel/laravel CarPark
cd CarPark

## Breeze + Livewire
composer require laravel/breeze --dev  
php artisan breeze:install livewire
###   testi: PHPUnit
###   tumšā tēma: no
###   TypeScript: no

## SQLite
cp .env.example .env
 .env failā: DB_CONNECTION=sqlite
touch database/database.sqlite

## JS
npm install

## Migrācijas
php artisan migrate



## 5. Noderīgas komandas

| Komanda | Ko dara |
|---|---|
| php artisan migrate | piemēro migrācijas |
| php artisan migrate:fresh --seed | pārbūvē DB + sējumi |
| php artisan db:seed | tikai sējumi |
| php artisan storage:link | simboliskā saite attēliem |
| php artisan tinker | Laravel REPL |
| npm run dev | Vite watch |
| npm run build | produkcijas būvējums |



## 6. Biežākās problēmas

### could not find driver
SQLite paplašinājums ir izslēgts. Atvērt php.ini un atkomentēt:  
extension=pdo_sqlite  
extension=sqlite3  

### database does not exist
Nav izveidots DB fails:  
touch database/database.sqlite  
php artisan migrate

### Stili netiek piemēroti
Nav palaists Vite:  
npm run dev  

### Vite manifest not found
Jāsabūvē aktīvi:  
npm run build

### Ports aizņemts Codespaces
Pārbaudīt cilni Ports — kurš ports jau aizņemts, un norādīt citu:  
php artisan serve --host=0.0.0.0 --port=8001



## 7. Testa dati

Pēc php artisan migrate --seed DB būs:  

- 1 administrators: admin@carpark.test / password
- 1 lietotājs: user@carpark.test / password
- 12 automašīnas (economy / comfort / suv)
- 10 autostāvvietas Rīgā

Pilna atiestatīšana:  
php artisan migrate:fresh --seed
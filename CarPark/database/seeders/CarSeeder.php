<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->cars() as $car) {
            Car::create($car);
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function cars(): array
    {
        return [
            ['brand' => 'Toyota', 'model' => 'Aygo', 'class' => 'economy', 'fuel_type' => 'petrol', 'transmission' => 'manual', 'drivetrain' => 'fwd', 'engine' => '1.0', 'seats' => 4, 'price_per_day' => 25, 'description' => 'Kompakta pilsētas automašīna.', 'address' => 'Brīvības iela 10, Rīga', 'lat' => 56.9496, 'lng' => 24.1052],
            ['brand' => 'Kia', 'model' => 'Rio', 'class' => 'economy', 'fuel_type' => 'petrol', 'transmission' => 'manual', 'drivetrain' => 'fwd', 'engine' => '1.2', 'seats' => 5, 'price_per_day' => 30, 'description' => 'Ekonomiska un ērta ikdienai.', 'address' => 'Elizabetes iela 2, Rīga', 'lat' => 56.9499, 'lng' => 24.1199],
            ['brand' => 'VW', 'model' => 'Polo', 'class' => 'economy', 'fuel_type' => 'diesel', 'transmission' => 'manual', 'drivetrain' => 'fwd', 'engine' => '1.6 TDI', 'seats' => 5, 'price_per_day' => 32, 'description' => 'Degvielas taupīga dīzeļversija.', 'address' => 'Valdemāra iela 90, Rīga', 'lat' => 56.9448, 'lng' => 24.1148],
            ['brand' => 'Hyundai', 'model' => 'i10', 'class' => 'economy', 'fuel_type' => 'petrol', 'transmission' => 'automatic', 'drivetrain' => 'fwd', 'engine' => '1.0', 'seats' => 4, 'price_per_day' => 28, 'description' => 'Automātiskā pārnesumkārba pilsētai.', 'address' => 'Kronvalda bulvāris 3, Rīga', 'lat' => 56.9535, 'lng' => 24.1244],
            ['brand' => 'Škoda', 'model' => 'Octavia', 'class' => 'comfort', 'fuel_type' => 'diesel', 'transmission' => 'automatic', 'drivetrain' => 'fwd', 'engine' => '2.0 TDI', 'seats' => 5, 'price_per_day' => 45, 'description' => 'Plaša bagāžas nodalījuma universālis.', 'address' => 'Dzirnavu iela 40, Rīga', 'lat' => 56.9572, 'lng' => 24.1224],
            ['brand' => 'VW', 'model' => 'Passat', 'class' => 'comfort', 'fuel_type' => 'diesel', 'transmission' => 'automatic', 'drivetrain' => 'fwd', 'engine' => '2.0 TDI', 'seats' => 5, 'price_per_day' => 50, 'description' => 'Komfortabls ceļojumu auto.', 'address' => 'Mūkusalas iela 1, Rīga', 'lat' => 56.9467, 'lng' => 24.1313],
            ['brand' => 'BMW', 'model' => '320i', 'class' => 'comfort', 'fuel_type' => 'petrol', 'transmission' => 'automatic', 'drivetrain' => 'rwd', 'engine' => '2.0', 'seats' => 5, 'price_per_day' => 60, 'description' => 'Sportisks sedans ar aizmugurējo piedziņu.', 'address' => 'Ģertrūdes iela 101, Rīga', 'lat' => 56.9530, 'lng' => 24.1065],
            ['brand' => 'Mazda', 'model' => '6', 'class' => 'comfort', 'fuel_type' => 'petrol', 'transmission' => 'automatic', 'drivetrain' => 'fwd', 'engine' => '2.5', 'seats' => 5, 'price_per_day' => 48, 'description' => 'Klusa un ērta braukšana.', 'address' => 'Tērbatas iela 10, Rīga', 'lat' => 56.9551, 'lng' => 24.1169],
            ['brand' => 'Toyota', 'model' => 'RAV4', 'class' => 'suv', 'fuel_type' => 'petrol', 'transmission' => 'automatic', 'drivetrain' => 'awd', 'engine' => '2.5 Hybrid', 'seats' => 5, 'price_per_day' => 65, 'description' => 'Pilnpiedziņas SUV ar hibrīdu.', 'address' => 'Skanstes iela 25, Rīga', 'lat' => 56.9432, 'lng' => 24.1017],
            ['brand' => 'Hyundai', 'model' => 'Tucson', 'class' => 'suv', 'fuel_type' => 'diesel', 'transmission' => 'automatic', 'drivetrain' => 'awd', 'engine' => '2.0 CRDi', 'seats' => 5, 'price_per_day' => 70, 'description' => 'Rūmīgs SUV ar pilnpiedziņu.', 'address' => 'Akmens iela 10, Rīga', 'lat' => 56.9556, 'lng' => 24.1084],
            ['brand' => 'Tesla', 'model' => 'Model Y', 'class' => 'suv', 'fuel_type' => 'electric', 'transmission' => 'automatic', 'drivetrain' => 'awd', 'engine' => 'Dual Motor', 'seats' => 5, 'price_per_day' => 85, 'description' => 'Elektriskais SUV ar lielu autonomiju.', 'address' => 'Ausekļa iela 1, Rīga', 'lat' => 56.9530, 'lng' => 24.1281],
            ['brand' => 'Kia', 'model' => 'Sportage', 'class' => 'suv', 'fuel_type' => 'petrol', 'transmission' => 'automatic', 'drivetrain' => 'awd', 'engine' => '1.6 T-GDI', 'seats' => 5, 'price_per_day' => 62, 'description' => 'Moderns SUV ar bagātīgu aprīkojumu.', 'address' => 'Gogoļa iela 3, Rīga', 'lat' => 56.9497, 'lng' => 24.1093],
        ];
    }
}

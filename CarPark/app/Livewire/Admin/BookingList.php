<?php

namespace App\Livewire\Admin;

use App\Models\CarReservation;
use App\Models\ParkingReservation;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class BookingList extends Component
{
    use WithPagination;

    private const PER_PAGE = 10;

    /** @var array<string, class-string<CarReservation|ParkingReservation>> */
    private const TYPES = [
        'car' => CarReservation::class,
        'parking' => ParkingReservation::class,
    ];

    private const STATUS_LABELS = [
        'reserved' => 'Rezervēta',
        'active' => 'Aktīva',
        'completed' => 'Pabeigta',
        'cancelled' => 'Atcelta',
    ];

    #[Url(except: '')]
    public string $type = '';

    #[Url(except: '')]
    public string $status = '';

    public bool $showModal = false;

    public ?string $selectedType = null;

    public ?int $selectedId = null;

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function openDetails(string $type, int $id): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
        abort_unless(isset(self::TYPES[$type]), 404);

        $this->selectedType = $type;
        $this->selectedId = $id;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->reset(['selectedType', 'selectedId']);
        $this->showModal = false;
    }

    public function cancel(string $type, int $id): void
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $model = self::TYPES[$type] ?? abort(404);
        $reservation = $model::findOrFail($id);

        if ($reservation->cancel()) {
            session()->flash('success', "Rezervācija #{$reservation->id} atcelta.");
        } else {
            session()->flash('error', "Rezervāciju #{$reservation->id} vairs nevar atcelt.");
        }
    }

    public function render(): View
    {
        return view('livewire.admin.booking-list', [
            'reservations' => $this->reservations(),
            'selected' => $this->selectedReservation(),
            'statusLabels' => self::STATUS_LABELS,
        ]);
    }

    private function selectedReservation(): CarReservation|ParkingReservation|null
    {
        if (! $this->showModal || $this->selectedType === null) {
            return null;
        }

        $model = self::TYPES[$this->selectedType];
        $relation = $this->selectedType === 'car' ? 'car' : 'parkingSpot';

        return $model::with(['user', $relation])->find($this->selectedId);
    }

    private function reservations(): LengthAwarePaginator
    {
        $queries = [];

        foreach (self::TYPES as $type => $model) {
            if (isset(self::TYPES[$this->type]) && $this->type !== $type) {
                continue;
            }

            $queries[] = $model::query()
                ->when(
                    in_array($this->status, $model::STATUSES, true),
                    fn ($query) => $query->where('status', $this->status),
                )
                ->select(['id', 'start_time'])
                ->selectRaw("'{$type}' as type")
                ->toBase();
        }

        $union = array_shift($queries);
        foreach ($queries as $query) {
            $union->unionAll($query);
        }

        $page = DB::query()
            ->fromSub($union, 'reservations')
            ->orderByDesc('start_time')
            ->orderBy('type')
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE);

        $rows = $page->getCollection()->groupBy('type');

        $cars = CarReservation::with(['user', 'car'])
            ->whereIn('id', $rows->get('car', collect())->pluck('id'))
            ->get()->keyBy('id');

        $parking = ParkingReservation::with(['user', 'parkingSpot'])
            ->whereIn('id', $rows->get('parking', collect())->pluck('id'))
            ->get()->keyBy('id');

        return $page->through(fn ($row) => $row->type === 'car'
            ? $cars[$row->id]
            : $parking[$row->id]);
    }
}

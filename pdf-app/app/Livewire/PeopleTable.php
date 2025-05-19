<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PeopleTable extends DataTableComponent
{
    public function configure(): void
    {
        $this->setPrimaryKey('id')
                ->setTableRowUrl(function($row) {
                    return null; // ⛔️ bez redirectu pri kliku
                })
                ->setBulkActionsEnabled() // ✅ aktivuje bulk actions
                ->setCheckboxSelectionEnabled(); // ✅ zobrazuje checkboxy
    }

    public function columns(): array
    {
        return [
            Column::make('Meno', 'first_name'),
            Column::make('Priezvisko', 'last_name'),
        ];
    }

    public function rows(): Collection
    {
        return collect([
            ['id' => 1, 'first_name' => 'Ján', 'last_name' => 'Novák'],
            ['id' => 2, 'first_name' => 'Eva', 'last_name' => 'Kováčová'],
            ['id' => 3, 'first_name' => 'Peter', 'last_name' => 'Horváth'],
        ]);
    }

    public function bulkActions(): array
    {
        return [
            'deleteSelected' => 'Vymazať vybrané',
        ];
    }

    public function deleteSelected()
    {
        User::whereIn('id', $this->getSelected())->delete();
        $this->clearSelected();
    }
}

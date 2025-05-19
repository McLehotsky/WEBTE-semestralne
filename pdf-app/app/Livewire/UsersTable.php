<?php

namespace App\Livewire;

use App\Models\EditHistory;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class EditHistoryTable extends DataTableComponent
{
    public function configure(): void
    {
        $this->setPrimaryKey('id')
             ->setAdditionalSelects(['edit_history.id'])
             ->setBulkActionsEnabled();
    }

    public function builder(): Builder
    {
        return EditHistory::query()
            ->with(['user', 'pdfEdit']);
    }

    public function columns(): array
    {
        return [
            Column::make('Používateľ')
                ->label(fn($row) => $row->user?->name),

            Column::make('Operácia')
                ->label(fn($row) => view('components.badges.operation', ['operation' => $row->pdfEdit?->name]))
                ->html(),

            Column::make('Prístup')
                ->label(fn($row) => view('components.badges.accessed', ['via' => $row->accessed_via]))
                ->html(),

            Column::make('Použité dňa', 'used_at')
                ->sortable()
                ->format(fn($value) => $value?->format('d.m.Y H:i')),
        ];
    }

    public function bulkActions(): array
    {
        return [
            'deleteSelected' => 'Vymazať vybrané',
        ];
    }

    public function deleteSelected()
    {
        EditHistory::whereIn('id', $this->getSelected())->delete();
        $this->clearSelected();
    }
}

<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Preserve original date if it's not present in the submitted data
        $original = $this->record->getOriginal();
        if (! isset($data['date']) || $data['date'] === null) {
            $data['date'] = $original['date'] ?? null;
        }

        $data['items'] = Invoice::normalizeItems($data['items'] ?? []);
        $data['grand_total'] = Invoice::calculateGrandTotal($data);

        return $data;
    }
}

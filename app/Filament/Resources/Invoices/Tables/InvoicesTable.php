<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Support\JalaliDate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_name')
                    ->label(__('invoices.columns.customer'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->label(__('invoices.columns.date'))
                    ->formatStateUsing(fn (?string $state): ?string => JalaliDate::forDisplay($state))
                    ->sortable(),
                TextColumn::make('items')
                    ->label(__('invoices.columns.items'))
                    ->formatStateUsing(fn (?array $state): string => (string) count($state ?? []))
                    ->alignCenter(),
                TextColumn::make('shipping_cost')
                    ->label(__('invoices.columns.shipping'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('installation_cost')
                    ->label(__('invoices.columns.installation'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('grand_total')
                    ->label(__('invoices.columns.grand_total'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

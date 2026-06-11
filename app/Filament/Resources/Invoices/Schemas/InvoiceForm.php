<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\Invoice;
use App\Models\Product;
use App\Support\JalaliDate;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make(__('invoices.sections.details'))
                    ->schema([
                        TextInput::make('customer_name')
                            ->label(__('invoices.fields.customer_name'))
                            ->maxLength(255)
                            ->columnSpanFull(),
                        DatePicker::make('date')
                            ->label(__('invoices.fields.date'))
                            ->jalali()
                            ->hasToday()
                            ->default(JalaliDate::todayForPicker())
                            ->formatStateUsing(fn (?string $state): ?string => JalaliDate::forPicker($state))
                            ->dehydrateStateUsing(fn (?string $state): ?string => JalaliDate::toStorage($state)),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make(__('invoices.sections.line_items'))
                    ->schema([
                        Repeater::make('items')
                            ->label(__('invoices.fields.items'))
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('products.fields.title'))
                                    ->options(fn () => Product::query()->pluck('title', 'id')->toArray())
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateHydrated(fn (Get $get, Set $set) => self::syncProductFields($get, $set))
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::syncProductFields($get, $set)),
                                TextInput::make('description')
                                    ->label(__('invoices.fields.description'))
                                    ->required(),
                                TextInput::make('width')
                                    ->label(__('invoices.fields.width'))
                                    ->numeric()
                                    ->step(0.001)
                                    ->default(0)
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::syncItemAndGrandTotal($get, $set)),
                                TextInput::make('length')
                                    ->label(__('invoices.fields.length'))
                                    ->numeric()
                                    ->step(0.001)
                                    ->default(0)
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::syncItemAndGrandTotal($get, $set)),
                                TextInput::make('area')
                                    ->label(__('invoices.fields.area'))
                                    ->numeric()
                                    ->step(0.001)
                                    ->disabled()
                                    ->formatStateUsing(fn (?float $state): ?string => $state !== null ? number_format($state, 3, '.', '') : null)
                                    ->dehydrated(),
                                TextInput::make('quantity')
                                    ->label(__('invoices.fields.quantity'))
                                    ->numeric()
                                    ->step(0.001)
                                    ->default(1)
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::syncItemAndGrandTotal($get, $set)),
                                TextInput::make('meterage')
                                    ->label(__('invoices.fields.metraj'))
                                    ->numeric()
                                    ->step(0.001)
                                    ->disabled()
                                    ->formatStateUsing(fn (?float $state): ?string => $state !== null ? number_format($state, 3, '.', '') : null)
                                    ->dehydrated(),
                                TextInput::make('unit_price')
                                    ->label(__('invoices.fields.unit_price'))
                                    ->numeric()
                                    ->step(0.001)
                                    ->default(0)
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::syncItemAndGrandTotal($get, $set)),
                                TextInput::make('line_total')
                                    ->label(__('invoices.fields.line_total'))
                                    ->numeric()
                                    ->step(0.001)
                                    ->disabled()
                                    ->formatStateUsing(fn (?float $state): ?string => $state !== null ? number_format($state, 3, '.', '') : null)
                                    ->dehydrated(),
                            ])
                            ->columns(8)
                            ->defaultItems(1)
                            ->addActionLabel(__('invoices.actions.add_item'))
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateGrandTotal($get, $set))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make(__('invoices.sections.additional_costs'))
                    ->schema([
                        TextInput::make('shipping_cost')
                            ->label(__('invoices.fields.shipping_cost'))
                            ->numeric()
                            ->step(0.001)
                            ->default(0)
                            ->minValue(0)
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateGrandTotal($get, $set)),
                        TextInput::make('installation_cost')
                            ->label(__('invoices.fields.installation_cost'))
                            ->numeric()
                            ->step(0.001)
                            ->default(0)
                            ->minValue(0)
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateGrandTotal($get, $set)),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make(__('invoices.sections.total'))
                    ->schema([
                        TextInput::make('grand_total')
                            ->label(__('invoices.fields.grand_total'))
                            ->numeric()
                            ->step(0.001)
                            ->disabled()
                            ->formatStateUsing(fn (?float $state): ?string => $state !== null ? number_format($state, 3, '.', '') : null)
                            ->dehydrated()
                            ->default(0),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function syncItemAndGrandTotal(Get $get, Set $set): void
    {
        self::updateLineTotal($get, $set);
        self::updateGrandTotal($get, $set);
    }

    private static function updateLineTotal(Get $get, Set $set): void
    {
        $quantity = (float) ($get('quantity') ?? 0);
        $length = (float) ($get('length') ?? 0);
        $width = (float) ($get('width') ?? 0);
        $unitPrice = (float) ($get('unit_price') ?? 0);

        $area = round(($length * $width) / 10000, 3);
        $metraj = round(max($area, 0.8) * $quantity, 3);

        $set('area', $area);
        $set('meterage', $metraj);
        $set('line_total', round($metraj * $unitPrice, 3));
    }

    private static function updateGrandTotal(Get $get, Set $set): void
    {
        $set(
            'grand_total',
            Invoice::calculateGrandTotal([
                'items' => $get('items', isAbsolute: true) ?? [],
                'shipping_cost' => $get('shipping_cost', isAbsolute: true) ?? 0,
                'installation_cost' => $get('installation_cost', isAbsolute: true) ?? 0,
            ]),
            isAbsolute: true,
        );
    }

    private static function syncProductFields(Get $get, Set $set): void
    {
        $productId = $get('product_id');

        if (! $productId) {
            return;
        }

        $product = Product::find($productId);

        if (! $product) {
            return;
        }

        $set('description', $product->title);

        if (! $get('unit_price')) {
            $set('unit_price', $product->price);
        }

        self::syncItemAndGrandTotal($get, $set);
    }
}

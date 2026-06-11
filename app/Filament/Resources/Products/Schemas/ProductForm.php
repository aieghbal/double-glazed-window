<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('products.sections.details'))
                    ->schema([
                        FileUpload::make('picture')
                            ->label(__('products.fields.picture'))
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth(800)
                            ->imageResizeTargetHeight(800),
                        TextInput::make('title')
                            ->label(__('products.fields.title'))
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label(__('products.fields.description'))
                            ->required()
                            ->maxLength(65535),
                        TextInput::make('price')
                            ->label(__('products.fields.price'))
                            ->numeric()
                            ->step(0.01)
                            ->default(0)
                            ->minValue(0)
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('users.sections.details'))
                    ->schema([
                        FileUpload::make('avatar')
                            ->label(__('users.fields.avatar'))
                            ->image()
                            ->directory('avatars')
                            ->disk('public')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth(400)
                            ->imageResizeTargetHeight(400),
                        TextInput::make('name')
                            ->label(__('users.fields.name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('users.fields.email'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label(__('users.fields.password'))
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(255)
                            ->helperText(__('users.fields.password_helper')),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

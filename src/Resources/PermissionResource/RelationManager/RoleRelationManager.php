<?php

namespace Phpsa\FilamentAuthentication\Resources\PermissionResource\RelationManager;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\BelongsToManyRelationManager;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;

class RoleRelationManager extends BelongsToManyRelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('filament-authentication::filament-authentication.field.name')),
                TextInput::make('guard_name')
                    ->label(__('filament-authentication::filament-authentication.field.guard_name'))
                     ->default(config('auth.defaults.guard')),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament-authentication::filament-authentication.field.name')),
                TextColumn::make('guard_name')
                    ->label(__('filament-authentication::filament-authentication.field.guard_name')),

            ])
            ->filters([
                //
            ]);
    }
}

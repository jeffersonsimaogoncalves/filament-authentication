<?php

namespace Phpsa\FilamentAuthentication\Resources;

use App\Models\User;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\BelongsToManyMultiSelect;
use Phpsa\FilamentAuthentication\Resources\UserResource\Pages\EditUser;
use Phpsa\FilamentAuthentication\Resources\UserResource\Pages\ViewUser;
use Phpsa\FilamentAuthentication\Resources\UserResource\Pages\ListUsers;
use Phpsa\FilamentAuthentication\Resources\UserResource\Pages\CreateUser;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static function getNavigationGroup(): ?string
    {
        return __('filament-authentication::filament-authentication.section.group');
    }

    public static function getLabel(): string
    {
        return __('filament-authentication::filament-authentication.section.user');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-authentication::filament-authentication.section.users');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-authentication::filament-authentication.field.user.name'))
                            ->required(),
                        TextInput::make('email')
                            ->required()
                            ->email()
                            ->unique(table: User::class, ignorable: fn (?User $record): ?User => $record)
                             ->label(__('filament-authentication::filament-authentication.field.user.email')),
                        TextInput::make('password')
                            ->same('passwordConfirmation')
                            ->password()
                            ->maxLength(255)
                            ->required(fn($component, $get, $livewire, $model, $record, $set, $state) =>  $record === null)
                            ->dehydrateStateUsing(fn ($state) => ! empty($state) ? Hash::make($state) : "")
                             ->label(__('filament-authentication::filament-authentication.field.user.password')),
                        TextInput::make('passwordConfirmation')
                            ->password()
                            ->dehydrated(false)
                            ->maxLength(255)
                             ->label(__('filament-authentication::filament-authentication.field.user.confirm_password')),
                        BelongsToManyMultiSelect::make('roles')
                            ->relationship('roles', 'name')
                             ->preload(config('filament-authentication.preload_roles'))
                             ->label(__('filament-authentication::filament-authentication.field.user.roles'))
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {

        $userTimeZone = config('request.user.timezone', config('filament-authentication.user_timezone', config('app.timezone')));

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament-authentication::filament-authentication.field.id')),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable() ->label(__('filament-authentication::filament-authentication.field.user.name')),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable() ->label(__('filament-authentication::filament-authentication.field.user.email')),
                IconColumn::make('email_verified_at')
                    ->options([
                        'heroicon-o-check-circle',
                        'heroicon-o-x-circle' => fn ($state): bool => $state === null,
                    ])
                    ->colors([
                        'success',
                        'danger' => fn ($state): bool => $state === null
                    ])
                     ->label(__('filament-authentication::filament-authentication.field.user.verified_at')),
                // IconColumn::make('roles')
                //     ->tooltip(
                //         fn (User $record): string => $record->getRoleNames()->implode(",\n")
                //     )->options(
                //         [
                //             'heroicon-o-shield-check'
                //         ]
                //     )->colors(['success']),
                TagsColumn::make('roles.name')
                    ->label(__('filament-authentication::filament-authentication.field.user.roles')),
                TextColumn::make('created_at')
                    ->dateTime("Y-m-d H:i:s", $userTimeZone)
                    ->label(__('filament-authentication::filament-authentication.field.user.created_at'))
            ])
            ->filters([
                Filter::make('verified')
                 ->label(__('filament-authentication::filament-authentication.filter.verified'))
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                Filter::make('unverified')
                ->label(__('filament-authentication::filament-authentication.filter.unverified'))
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit'   => EditUser::route('/{record}/edit'),
         //   'view'   => ViewUser::route('/{record}')
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('roles');
    }
}

<?php

namespace Phpsa\FilamentAuthentication;

use Livewire\Livewire;
use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Filament\Navigation\UserMenuItem;
use Filament\Tables\Columns\TextColumn;
use Spatie\LaravelPackageTools\Package;
use Phpsa\FilamentAuthentication\Pages\Profile;
use Phpsa\FilamentAuthentication\Widgets\LatestUsersWidget;

class FilamentAuthenticationProvider extends PluginServiceProvider
{
    public static string $name = 'filament-authentication';

    protected array $widgets = [
        LatestUsersWidget::class
    ];


    protected function getResources(): array
    {
        return config('filament-authentication.resources');
    }

    public function configurePackage(Package $package): void
    {
        $package->name('filament-authentication')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations();
    }

    public function getPages(): array
    {
        return config('filament-authentication.pages');
    }


    protected function registerMacros(): void
    {
        Filament::serving(function () {
            Filament::registerUserMenuItems([
                'account' => UserMenuItem::make()->url(route('filament.pages.profile')),
            ]);
        });

        TextColumn::macro('humanDate', function () {
            /** @var \Filament\Tables\Columns\TextColumn&\Filament\Tables\Columns\Concerns\CanFormatState $this */
            $this->formatStateUsing(fn ($state): ?string => $state ? $state->diffForHumans() : null);

            return $this;
        });
    }
}

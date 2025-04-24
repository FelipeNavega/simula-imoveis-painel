<?php

namespace App\Filament\Partner\Resources\SimulationResource\Pages;

use App\Filament\Resources\SimulationResource;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class SearchDocument extends Page
{
    protected static string $resource = SimulationResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.partner.pages.search-document';

    public function getTitle(): string|Htmlable
    {
        return 'Consultar CPF/CNPJ';
    }

    public function getBreadcrumbs(): array
    {
        /** @var Resource $resource */
        $resource = static::getResource();

        return [
            $resource::getUrl() => $resource::getBreadcrumb(),
            0 => 'Consultar CPF/CNPJ',
        ];
    }
}

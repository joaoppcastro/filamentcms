<?php

namespace JoaoCastro\FilamentCms\PageResources\Pages;

use JoaoCastro\FilamentCms\PageResources\PageResource;
use App\Models\Sitemap;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['channel_id'] = Filament::getTenant()->getAttribute('id');

        return $data;
    }

    protected function afterCreate(): void
    {
        $page = $this->record->getAttributes();
        $sitemap = new Sitemap();
        $slug = json_decode($page['slug'], true);
        $sitemap->slug = $slug[$this->activeLocale];
        $sitemap->language = $this->activeLocale;
        $sitemap->resource_id = $page['id'];
        $sitemap->resource_type = 'page';
        $sitemap->channel_id = Filament::getTenant()->getAttribute('id');
        $sitemap->save();
    }
}

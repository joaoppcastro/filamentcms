<?php

namespace JoaoCastro\FilamentCms\PageResources\Pages;

use JoaoCastro\FilamentCms\PageResources\PageResource;
use App\Models\Sitemap;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    use EditRecord\Concerns\Translatable;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $page = $this->record;
        if ($page->originalIsEquivalent('slug')) {
            $sitemap = Sitemap::where('resource_id', '=', $page->getAttribute('id'))
                ->where('resource_type', 'page')
                ->where('channel_id', Filament::getTenant()->getAttribute('id'))
                ->first();
            $sitemap->slug = $page->getAttribute('slug');
            $sitemap->save();
        }
    }
}

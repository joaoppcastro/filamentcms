<?php

namespace JoaoCastro\FilamentCms;

use JoaoCastro\FilamentCms\PageResources\PageResource;
use JoaoCastro\FilamentCms\Models\Page;
use Filament\Contracts\Plugin;
use Filament\Panel;

class Filamentcms implements Plugin
{
    protected string $model = Page::class;

    protected string $resource = PageResource::class;

    /** @param  class-string<\Filament\Resources\Resource>  $resource */
    public function usingResource(string $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

     /** @param  class-string<\Illuminate\Database\Eloquent\Model>  $model */
     public function usingModel(string $model): static
     {
         $this->model = $model;
 
         return $this;
     }

     public function register(Panel $panel): void
    {
        $panel
            ->resources([$this->getResource()]);
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    public function getModel(): string
    {
        return $this->model;
    }
    public function boot(Panel $panel): void
    {

    }

    public static function make(): static
    {
        return new static();
    }

    public function getId(): string
    {
        return 'pages';
    }
}
<?php

namespace App\Traits;

trait HasTranslations
{
    /**
     * Get a localized attribute value.
     */
    public function getLocalized(string $attribute, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        $localizedAttribute = $locale === 'en' 
            ? "{$attribute}_en" 
            : "{$attribute}_ar";
        
        return $this->{$localizedAttribute} ?? $this->{$attribute};
    }
}


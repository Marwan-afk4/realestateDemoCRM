<?php

namespace App\trait;

trait Translatable
{
    /**
     * Determine if an attribute is translatable.
     *
     * @param  string  $key
     * @return bool
     */
    public function isTranslatableAttribute($key)
    {
        return in_array($key, $this->translatable ?? []);
    }

    /**
     * Get a plain attribute (override Eloquent).
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if ($this->isTranslatableAttribute($key)) {
            $locale = app()->getLocale();
            $translatedKey = "{$key}_{$locale}";

            // Check if the translated attribute exists or fallback to the other locale
            if (array_key_exists($translatedKey, $this->attributes)) {
                return $this->attributes[$translatedKey] 
                    ?? $this->attributes["{$key}_en"] 
                    ?? $this->attributes["{$key}_ar"] 
                    ?? null;
            }
            
            // Fallback for when attributes are accessed before query loading or not fully loaded
            if (isset($this->attributes["{$key}_en"]) || isset($this->attributes["{$key}_ar"])) {
                return $this->attributes["{$key}_{$locale}"] 
                    ?? $this->attributes["{$key}_en"] 
                    ?? $this->attributes["{$key}_ar"] 
                    ?? null;
            }
        }

        return parent::getAttribute($key);
    }

    /**
     * Set a plain attribute (override Eloquent).
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if ($this->isTranslatableAttribute($key)) {
            $locale = app()->getLocale();
            $this->attributes["{$key}_{$locale}"] = $value;
            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach ($this->translatable ?? [] as $key) {
            $attributes[$key] = $this->getAttribute($key);
        }

        return $attributes;
    }
}

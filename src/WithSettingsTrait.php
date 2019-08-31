<?php

namespace snuzi\ARSettings;

trait WithSettingsTrait
{
    public function __construct()
    {
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'myBeforeSave']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'myBeforeSave']);
    }
    /**
     * Before saveing model
     * 
     * @return void
     */
    public function myBeforeSave($insert)
    {
        if ($insert) {
            if (! $this->{$this->getSettingsAttribute()}) {
                $this->{$this->getSettingsAttribute()} = $this->defaultSettings();
            }
        } else {
            if ($this->{$this->getSettingsAttribute()} && property_exists($this, 'allowedSettings') && is_array($this->allowedSettings)) {
                $this->{$this->getSettingsAttribute()} = $this->arrayOnly($this->{$this->getSettingsAttribute()}, $this->allowedSettings);
            }
        }
        $this->{$this->getSettingsAttribute()} = json_encode($this->{$this->getSettingsAttribute()});
    }

    /**
     * Get the model's default settings.
     *
     * @return array
     */
    public function defaultSettings()
    {
        return (isset($this->defaultSettings) && is_array($this->defaultSettings))
            ? $this->defaultSettings
            : [];
    }

    /**
     * Get settings attribute name
     * 
     * @return string
     */
    private function getSettingsAttribute()
    {
        return isset($this->settingsAttribute) ? $this->settingsAttribute : 'settings';
    }

    /**
     * Get the settings attribute.
     *
     * @param json $settings
     * @return mixed
     */
    public function getSettingsAttribute1($settings)
    {
        return json_decode($settings, true);
    }

    /**
     * Set the settings attribute.
     *
     * @param  $settings
     * @return void
     */
    public function setSettings($settings)
    {
        $this->attributes['settings'] = json_encode($settings);
    }

    /**
     * The model's settings.
     *
     * @param string|null $key
     * @param mixed|null  $default
     * @return Settings
     */
    public function settings($key = null, $default = null)
    {
        return $key ? $this->settings()->get($key, $default) : new Settings($this, $this->settingsAttribute);
    }
    
    /**
     * Map settings() to another alias specified with $mapSettingsTo.
     * 
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        if (isset($this->mapSettingsTo) && $name == $this->mapSettingsTo) {
            return $this->settings(...$args);
        }

        return is_callable(['parent', '__call'])
            ? parent::__call($name, $args) 
            : null;
    }

    public function __get2($property) {
        if ($property === $this->getSettingsAttribute()) {
            $this->$property = json_decode($this->$property, true);
        }
    }

    function arrayOnly($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }
}
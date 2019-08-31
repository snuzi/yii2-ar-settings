<?php

namespace snuzi\ARSettings;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Settings
{
    /**
     * @var ActiveRecord
     */
    protected $model;

    /**
     * @var string Settings table column
     */
    protected $settingsAttribute;

    /**
     * Settings constructor.
     *
     * @param ActiveRecord $model
     */
    public function __construct($model, $settingsAttribute = 'settings')
    {
        $this->model = $model;
        $this->settingsAttribute = $settingsAttribute;
    }

    /**
     * Get the model's settings.
     *
     * @return array|null
     */
    public function all()
    {
        return json_decode(json_decode($this->model->{$this->settingsAttribute}, true), true);
    }

    /**
     * Apply the model's settings.
     *
     * @param array $settings
     * @return $this
     */
    public function apply($settings = [])
    {
        $this->model->{$this->settingsAttribute} = (array) $settings;
        $this->model->save();
        
        return $this;
    }

    /**
     * Delete the setting at the given path.
     *
     * @param string|null $path
     * @return array
     */
    public function delete($path = null)
    {
        if (! $path) {
            return $this->set([]);
        }

        $settings = $this->all();

        array_forget($settings, $path);

        return $this->apply($settings);
    }

    /**
     * Forget the setting at the given path.
     *
     * @alias delete()
     * @param null $path
     * @return array
     */
    public function forget($path = null)
    {
        return $this->delete($path);
    }

    /**
     * Return the value of the setting at the given path.
     *
     * @param string|null $path
     * @param mixed       $default
     *
     * @return mixed
     */
    public function get($path = null, $default = null)
    {
        return $path ? ArrayHelper::getValue($this->all(), $path, $default) : $this->all();
    }

    /**
     * Determine if the model has the given setting.
     *
     * @param string $path
     *
     * @return bool
     */
    public function has($path)
    {
        return (bool) ArrayHelper::keyExists($path, $this->all());
    }

    /**
     * Update the setting at given path to the given value.
     *
     * @param string|null $path
     * @param mixed       $value
     *
     * @return array
     */
    public function set($path = null, $value = [])
    {
        if (func_num_args() < 2) {
            $value = $path;
            $path = null;
        }

        $settings = $this->all();

        ArrayHelper::setValue($settings, $path, $value);

        return $this->apply($settings);
    }

    /**
     * Update the setting at the given path if it exists.
     *
     * @alias  set()
     *
     * @param string $path
     * @param mixed  $value
     *
     * @return $this|array
     */
    public function update($path, $value)
    {
        return $this->set($path, $value);
    }
}
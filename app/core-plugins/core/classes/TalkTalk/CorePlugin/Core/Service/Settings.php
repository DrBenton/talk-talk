<?php

namespace TalkTalk\CorePlugin\Core\Service;

use TalkTalk\Core\Service\BaseService;
use TalkTalk\Model\Setting as SettingModel;

class Settings extends BaseService
{
    protected $lightDataCache = array();

    /**
     * Fetches an entry from the app settings.
     *
     * @param string $key     The id of the app settings entry to fetch.
     * @param mixed  $default A default value.
     *
     * @return mixed The app settings data or null, if no app settings entry exists for the given id.
     */
    public function get($key, $default = null)
    {
        if (isset($this->lightDataCache[$key])) {
            return $this->lightDataCache[$key];
        }

        $data = SettingModel::find($key, array('value'));

        if (null === $data) {
            $this->lightDataCache[$key] = null;

            return $default;
        }

        if (null !== $data->value && $this->app->get('utils.string')->isJsonish($data->value)) {
            $data->value = json_decode($data->value, true);
        }
        $this->lightDataCache[$key] = $data->value;

        return $data->value;
    }

    /**
     * Tests if an entry exists in the app settings.
     *
     * @param string $key The app settings id of the entry to check for.
     *
     * @return boolean TRUE if a app settings entry exists for the given app settings id, FALSE otherwise.
     */
    public function has($key)
    {
        if (isset($this->lightDataCache[$key]) && $this->lightDataCache[$key] !== false) {
            return true;
        }

        return (false !== $this->fetch($key));
    }

    /**
     * Puts data into the app settings.
     *
     * @param string $key  The app settings id.
     * @param mixed  $data The app settings entry/data.
     */
    public function set($key, $data)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }

        $setting = $this->fetch($key);

        if (false === $setting) {
            $setting = SettingModel::create(array(
                'key' => $key,
                'value' => $data,
            ));
        } else {
            $setting->value = $data;
            $setting->save();
        }

        $this->lightDataCache[$key] = $setting;
    }

    /**
     * Deletes a app settings entry.
     *
     * @param string $key The app settings id.
     */
    public function remove($key)
    {
        SettingModel::find($key)->delete();
        unset($this->lightDataCache[$key]);
    }
}
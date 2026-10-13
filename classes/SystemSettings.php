<?php

/** Reads the site's metadata and provides small session helpers for views. */
class SystemSettings
{
    private $settings = [];

    public function __construct($connection)
    {
        if (!$connection instanceof mysqli) {
            throw new InvalidArgumentException('SystemSettings requires a mysqli connection.');
        }

        $result = $connection->query('SELECT `meta_field`, `meta_value` FROM `system_info`');
        while ($row = $result->fetch_assoc()) {
            $this->settings[$row['meta_field']] = $row['meta_value'];
        }
        $result->free();
    }

    /** Return a setting value, or false when the setting is not configured. */
    public function info($field)
    {
        return array_key_exists($field, $this->settings) ? $this->settings[$field] : false;
    }

    /** Return the signed-in user's data, or a single field from it. */
    public function userdata($field = null)
    {
        $user = isset($_SESSION['userdata']) && is_array($_SESSION['userdata'])
            ? $_SESSION['userdata']
            : [];

        if ($field === null || $field === '') {
            return $user;
        }

        return $user[$field] ?? '';
    }

    public function set_flashdata($key, $value)
    {
        if (!isset($_SESSION['flashdata']) || !is_array($_SESSION['flashdata'])) {
            $_SESSION['flashdata'] = [];
        }

        $_SESSION['flashdata'][$key] = $value;
    }

    public function chk_flashdata($key)
    {
        return isset($_SESSION['flashdata'])
            && is_array($_SESSION['flashdata'])
            && array_key_exists($key, $_SESSION['flashdata']);
    }

    /** Read and remove a one-time session message. */
    public function flashdata($key)
    {
        if (!$this->chk_flashdata($key)) {
            return '';
        }

        $value = $_SESSION['flashdata'][$key];
        unset($_SESSION['flashdata'][$key]);

        return $value;
    }
}

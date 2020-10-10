<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

abstract class CW_Cron_Job
{
    protected $type_name;

    public function __construct($type_name)
    {
        $this->type_name = $type_name;

        add_filter('cron_schedules', array($this, 'get_interval'));

        register_activation_hook(CW_PLUGIN_FILE, array($this, 'schedule_update'));
        register_deactivation_hook(CW_PLUGIN_FILE, array($this, 'remove_schedule'));

        add_action($this->type_name, array($this, 'cron_job'));
    }

    /**
     *
     */
    public function schedule_update()
    {
        wp_schedule_event(time(), "each_six_hours", $this->type_name);
    }

    /**
     *
     */
    public function remove_schedule()
    {
        wp_clear_scheduled_hook($this->type_name);
    }

    /**
     * Default interval
     *
     * @return mixed
     */
    public function get_interval()
    {
        // Adds each six hours
        $schedules['each_six_hours'] = array(
            'interval' => 21600,
            'display' => "Each 6 Hours"
        );

        return $schedules;
    }
}
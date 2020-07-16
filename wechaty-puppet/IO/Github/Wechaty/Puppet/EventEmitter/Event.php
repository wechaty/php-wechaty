<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/10
 * Time: 7:01 PM
 */
namespace IO\Github\Wechaty\Puppet\EventEmitter;

class Event {
    /**
     * @var EventEmitter
     */
    public static $defaultEmitter;

    /**
     * Emit the event
     *
     * @param string $event
     * @param mixed  $args
     * @return boolean
     */
    public static function emit($event, $args = null) {
        return call_user_func_array(array(static::emitter(), 'emit'), func_get_args());
    }

    /**
     * Register event
     *
     * @param string   $event
     * @param callable $listener
     * @return EventEmitter
     */
    public static function on($event, \Closure $listener) {
        return static::emitter()->on($event, $listener);
    }

    /**
     * Register event
     *
     * @param array|string $event
     * @param callable     $listener
     * @return EventEmitter
     */
    public static function once($event, \Closure $listener) {
        return static::emitter()->once($event, $listener);
    }

    /**
     * Attach a listener to emit many times
     *
     * @param array|string $event
     * @param int          $times
     * @param callable     $listener
     * @return EventEmitter
     */
    public static function many($event, $times = 1, \Closure $listener) {
        return static::emitter()->many($event, $times, $listener);
    }

    /**
     * Remove listener of giving event
     *
     * @param array|string $event
     * @param callable     $listener
     * @return EventEmitter
     */
    public static function off($event, \Closure $listener) {
        return static::emitter()->off($event, $listener);
    }

    /**
     * Get all listeners of giving event
     *
     * @param string $event
     * @return array
     */
    public static function listeners($event) {
        return static::emitter()->listeners($event);
    }

    /**
     * Remove listener
     *
     * @param string   $event
     * @param callable $listener
     * @return EventEmitter
     */
    public static function removeListener($event, \Closure $listener) {
        return static::emitter()->removeListener($event, $listener);
    }

    /**
     * Remove all of event listeners
     *
     * @param $event
     * @return EventEmitter
     */
    public static function removeAllListeners($event) {
        return static::emitter()->removeAllListeners($event);
    }

    /**
     * Set or get emitter
     *
     * @param EventEmitter $emitter
     * @return EventEmitter
     */
    public static function emitter(EventEmitter $emitter = null) {
        if ($emitter) {
            static::$defaultEmitter = $emitter;
        }
        return static::$defaultEmitter;
    }
}
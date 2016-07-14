<?php

namespace Blackbird\TicketBlaster\Api\Data;

interface EventInterface
{
    const EVENT_ID       = 'event_id';
    const URL_KEY       = 'url_key';
    const TITLE         = 'title';
    const VENUE         = 'venue';
    const EVENT_TIME       = 'event_time';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME   = 'update_time';
    const IS_ACTIVE     = 'is_active';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get URL Key
     *
     * @return string
     */
    public function getUrlKey();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get venue
     *
     * @return string|null
     */
    public function getVenue();

    /**
     * Get event time
     *
     * @return string|null
     */
    public function getEventTime();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Blackbird\TicketBlaster\Api\Data\EventInterface
     */
    public function setId($id);

    /**
     * Set URL Key
     *
     * @param string $url_key
     * @return \Blackbird\TicketBlaster\Api\Data\EventInterface
     */
    public function setUrlKey($url_key);

    /**
     * Set title
     *
     * @param string $title
     * @return \Blackbird\TicketBlaster\Api\Data\EventInterface
     */
    public function setTitle($title);

    /**
     * Set venue
     *
     * @param string $venue
     * @return \Blackbird\TicketBlaster\Api\Data\EventInterface
     */
    public function setVenue($venue);

    /**
     * Set event time
     *
     * @param string $event_time
     * @return \Blackbird\TicketBlaster\Api\Data\EventInterface
     */
    public function setEventTime($event_time);

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return \Blackbird\TicketBlaster\Api\Data\EventInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return \Blackbird\TicketBlaster\Api\Data\EventInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Set is active
     *
     * @param int|bool $is_active
     * @return \Blackbird\TicketBlaster\Api\Data\EventInterface
     */
    public function setIsActive($is_active);
}
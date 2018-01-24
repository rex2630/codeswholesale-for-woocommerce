<?php

/**
 * Interface CW_DefaultEmailDataInterface
 */
interface CW_DefaultEmailDataInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    function getTitle(): string;

    /**
     * @return string
     */
    function getHeading(): string;

    /**
     * @return string
     */
    function getSubject(): string;

    /**
     * @return string
     */
    function getContent(): string;

    /**
     * @return string
     */
    function getHint(): string;
}

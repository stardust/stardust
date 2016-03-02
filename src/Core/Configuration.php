<?php
namespace Stardust\Core;

interface Configuration
{
    /**
     * @return bool
     */
    public function extendsConfiguration();

    /**
     * @return string
     */
    public function path();

    /**
     * @return string|null
     */
    public function parentEnvironment();

    /**
     * @return string
     */
    public function file();
}

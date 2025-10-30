<?php
namespace Stardust\Core;

interface Configuration
{
    public function extendsConfiguration(): bool;

    public function path(): string;

    public function parentEnvironment(): string|null;

    public function file(): string;
}

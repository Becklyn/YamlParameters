<?php

namespace Becklyn\YamlParametersHandler;


use Composer\Script\Event;


class ScriptHandler
{
    /**
     * @param Event $event
     */
    public static function run (Event $event) : void
    {
        $extras = $event->getComposer()->getPackage()->getExtra();

        if (!isset($extras['parameters']))
        {
            throw new \InvalidArgumentException('The parameter handler needs to be configured through the extra.parameters setting.');
        }

        $config = $extras['parameters'];

        if (!\is_string($config))
        {
            throw new \InvalidArgumentException('The extra.parameters setting must be a string with the path to the dist file.');
        }

        $processor = new YamlProcessor($event->getIO());
        $processor->process($config);
    }
}

<?php

namespace Becklyn\YamlParametersHandler;


use Composer\IO\IOInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;


class YamlProcessor
{
    /**
     * @var IOInterface
     */
    private $io;


    /**
     * @var StructureCopy
     */
    private $structureCopy;


    /**
     * @param IOInterface $io
     */
    public function __construct (IOInterface $io)
    {
        $this->io = $io;
        $this->structureCopy = new StructureCopy($io);
    }


    /**
     * Processes the given file
     *
     * @param string $file
     */
    public function process (string $file) : void
    {
        $distFile = $file . ".dist";
        $realFileExists = \file_exists($file);
        $yamlParser = new Parser();

        if (!\is_file($distFile))
        {
            throw new \InvalidArgumentException(sprintf(
                "Can't find dist-file at '%s'.",
                $distFile
            ));
        }

        // write status message
        $this->io->write(sprintf("%s the '%s' file",
        $realFileExists ? "Updating" : "Creating",
            $file
        ));

        $parsedDist = $yamlParser->parseFile($distFile);
        $parsedReal = $realFileExists
            ? $yamlParser->parseFile($file)
            : [];

        $finalData = $this->structureCopy->copy($parsedDist, $parsedReal);

        \file_put_contents($file, "# This file is auto-generated during `composer install`\n" . Yaml::dump($finalData));
    }
}

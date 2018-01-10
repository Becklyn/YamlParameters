<?php

namespace Becklyn\YamlParametersHandler;


use Composer\IO\IOInterface;
use Symfony\Component\Yaml\Inline;


class StructureCopy
{
    /**
     * @var IOInterface
     */
    private $io;


    /**
     * @param IOInterface $io
     */
    public function __construct (IOInterface $io)
    {
        $this->io = $io;
    }


    /**
     * @param array $source
     * @param array $target
     * @return array
     */
    public function copy (array $source, array $target) : array
    {
        $merged = [];

        foreach ($source as $key => $value)
        {
            if (\is_array($value))
            {
                $currentValue = isset($target[$key]) && \is_array($target[$key])
                    ? $target[$key]
                    : [];

                $merged[$key] = $this->copy($value, $currentValue);
                continue;
            }

            if (\is_scalar($value) ||null === $value)
            {
                if (isset($target[$key]))
                {
                    $merged[$key] = $target[$key];
                }
                else
                {
                    $default = null === $value
                        ? "~"
                        : Inline::dump($value);

                    $answer = $this->io->ask(
                        sprintf(
                            '<question>%s</question> (<comment>%s</comment>): ',
                            $key,
                            $default
                        ),
                        $default
                    );

                    // parse, so that the values have the proper format like `~` being really `null` etc
                    $merged[$key] = Inline::parse($answer);
                }

                continue;
            }


            throw new \RuntimeException("Can only merge parameters of type array and scalar.");
        }

        return $merged;
    }
}

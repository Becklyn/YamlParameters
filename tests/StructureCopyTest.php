<?php

namespace Tests\Becklyn\YamlParametersHandler;


use Becklyn\YamlParametersHandler\StructureCopy;
use Composer\IO\IOInterface;
use PHPUnit\Framework\TestCase;


class StructureCopyTest extends TestCase
{
    /**
     * @var StructureCopy
     */
    private $copy;


    /**
     * @inheritdoc
     */
    protected function setUp ()
    {
        $io = $this->getMockBuilder(IOInterface::class)
            ->getMock();

        $io
            ->method("ask")
            ->willReturnArgument(1);

        $this->copy = new StructureCopy($io);
    }


    /**
     * @return array
     */
    public function dataProviderCopy ()
    {
        return [
            "defaults are copied" => [
                ["a" => "b"],
                [],
                ["a" => "b"]
            ],
            "obsolete parameters are removed" => [
                ["a" => "b"],
                ["c" => "d"],
                ["a" => "b"]
            ],
            "null parameters are passed as null" => [
                ["a" => null],
                [],
                ["a" => null]
            ],
            "nested arrays are copied" => [
                ["parameters" => ["a" => 1, "b" => 2]],
                [],
                ["parameters" => ["a" => 1, "b" => 2]],
            ],
            "nested arrays are merged" => [
                ["parameters" => ["a" => 1, "b" => 2]],
                ["parameters" => ["b" => 4]],
                ["parameters" => ["a" => 1, "b" => 4]],
            ],
            "structures overwrite scalars" => [
                ["parameters" => ["a" => 1, "b" => 2]],
                ["parameters" => 5],
                ["parameters" => ["a" => 1, "b" => 2]],
            ],
        ];
    }


    /**
     * @dataProvider dataProviderCopy
     *
     * @param $source
     * @param $target
     * @param $expectedResult
     */
    public function testCopy ($source, $target, $expectedResult)
    {
        $actual = $this->copy->copy($source, $target);
        self::assertEquals($expectedResult, $actual);
    }


    /**
     *
     */
    public function testOnlyAskForMissing ()
    {
        $io = $this->getMockBuilder(IOInterface::class)
            ->getMock();

        $io
            ->expects(self::exactly(2))
            ->method("ask")
            ->withConsecutive(
                [$this->stringContains('<question>a</question>'), $this->equalTo(1)],
                [$this->stringContains('<question>b</question>'), $this->equalTo(2)]
            );

        $copy = new StructureCopy($io);

        $copy->copy(
            [
                "params" => [
                    "a" => 1,
                    "b" => 2,
                    "c" => 3,
                ],
            ],
            [
                "params" => [
                    "c" => 5,
                ]
            ]
        );
    }


    /**
     *
     */
    public function testDefaultFormat ()
    {
        $io = $this->getMockBuilder(IOInterface::class)
            ->getMock();

        $io
            ->expects(self::exactly(1))
            ->method("ask")
            ->withConsecutive(
                [$this->stringContains('<question>a</question>'), $this->equalTo("~")]
            );

        $copy = new StructureCopy($io);

        $copy->copy(
            [
                "a" => null,
            ],
            []
        );
    }


    /**
     * Tests that Yaml is correctly parsed inline
     */
    public function testInlineParsing ()
    {
        $io = $this->getMockBuilder(IOInterface::class)
            ->getMock();

        $io
            ->expects(self::exactly(1))
            ->method("ask")
            ->willReturn("~");

        $copy = new StructureCopy($io);

        $result = $copy->copy(["a" => 1], []);
        self::assertNull($result["a"]);
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase\tests\utils;

/**
 * Record class tests
 *
 * @author majkel
 */
abstract class AbstractFilterTest extends TestBase {

    /**
     * @return \org\majkel\dbase\FilterInterface
     */
    abstract protected function getFilterObject();

    /**
     * @return array
     */
    abstract public function dataToValue();

    /**
     * @param string $input
     * @param string $excepted
     * @covers ::toValue
     * @dataProvider dataToValue
     */
    public function testToValue($input, $excepted) {
        self::assertSame($excepted, $this->getFilterObject()->toValue($input));
    }

    /**
     * @return array
     */
    abstract public function dataFromValue();

    /**
     * @param string $input
     * @param string $excepted
     * @covers ::fromValue
     * @dataProvider dataFromValue
     */
    public function testFromValue($input, $excepted) {
        self::assertSame($excepted, $this->getFilterObject()->fromValue($input));
    }

    /**
     * @return array
     */
    abstract protected function getSupportedTypes();

    /**
     * @return array
     */
    public function dataSupportsType() {
        return $this->genSupportsTypeDataSet($this->getSupportedTypes());
    }

    /**
     * @param string $type
     * @param boolean $supports
     * @dataProvider dataSupportsType
     * @covers ::supportsType
     */
    public function testSupportsType($type, $supports) {
        $result = $this->getFilterObject()->supportsType($type);
        self::assertSame($supports, $result, "Invalid result for `$type`");
    }

}

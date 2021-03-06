<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace org\majkel\dbase;

use org\majkel\dbase\tests\utils\TestBase;

use ReflectionClass;

/**
 * Record class tests
 *
 * @author majkel
 *
 * @coversDefaultClass \org\majkel\dbase\Field
 */
class FieldTest extends TestBase {

    /**
     * @covers ::addFilter
     * @covers ::getFilters
     */
    public function testAddFilter() {
        $field = $this->getFieldStub();
        $filter = $this->mock(self::CLS_FILTER)
            ->supportsType(array($field->getType()), true, self::once())
            ->new();
        self::assertSame(array(), $field->getFilters());
        self::assertSame($field, $field->addFilter($filter));
        self::assertSame(array($filter), $field->getFilters());
    }

    /**
     * @covers ::addFilter
     * @covers ::getType
     * @covers ::getFilters
     */
    public function testAddFilterDoesNotSupport() {
        $field = $this->getFieldStub();
        $filter = $this->mock(self::CLS_FILTER)
            ->supportsType(array($field->getType()), false, self::once())
            ->new();
        $field->addFilter($filter);
        self::assertSame(array(), $field->getFilters());
    }

    /**
     * @return array
     */
    public function dataAddFilters() {
        $fA = $this->getFilterStub();
        $fB = $this->getFilterStub();
        return array(
            array(array($fA, $fB, $fA), array($fA, $fB, $fA)),
            array(new \ArrayIterator(array($fA, $fB, $fA)), array($fA, $fB, $fA)),
            array(false, array()),
            array(null, array()),
            array(123, array()),
            array(new \stdClass(), array()),
        );
    }

    /**
     * @covers ::addFilters
     * @covers ::addFilter
     * @covers ::getFilters
     * @dataProvider dataAddFilters
     */
    public function testAddFilters($filters, $expected) {
        $field = $this->getFieldStub();
        self::assertSame($field, $field->addFilters($filters));
        self::assertSame($expected, $field->getFilters());
    }

    /**
     * @covers ::addFilter
     * @covers ::removeFilter
     * @covers ::getFilters
     */
    public function testRemoveFilterByIndex() {
        $field = $this->getFieldStub();
        $filter = $this->getFilterStub();
        $field->addFilter($filter);
        self::assertSame($field, $field->removeFilter(0));
        self::assertSame(array(), $field->getFilters());
    }

    /**
     * @covers ::addFilter
     * @covers ::removeFilter
     * @covers ::getFilters
     */
    public function testRemoveFilterByIndexDoesNotExists() {
        $field = $this->getFieldStub();
        $filter = $this->getFilterStub();
        $field->addFilter($filter);
        self::assertSame($field, $field->removeFilter(66));
        self::assertSame(array($filter), $field->getFilters());
    }

    /**
     * @covers ::addFilter
     * @covers ::removeFilter
     * @covers ::getFilters
     */
    public function testRemoveFilterByObject() {
        $field = $this->getFieldStub();
        $filter = $this->getFilterStub();
        $field->addFilter($filter);
        self::assertSame($field, $field->removeFilter($filter));
        self::assertSame(array(), $field->getFilters());
    }

    /**
     * @covers ::addFilter
     * @covers ::removeFilter
     * @covers ::getFilters
     */
    public function testRemoveFilterByObjectDoestNotExists() {
        $field = $this->getFieldStub();
        $fA = $this->getFilterStub();
        $fB = $this->getFilterStub();
        $field->addFilter($fA);
        self::assertSame($field, $field->removeFilter($fB));
        self::assertSame(array($fA), $field->getFilters());
    }

    /**
     * @covers ::setName
     * @covers ::getName
     */
    public function testSetName() {
        $field = $this->getFieldStub();
        self::assertSame($field, $field->setName('NAME'));
        self::assertSame('NAME', $field->getName());
    }

    /**
     * @covers ::setName
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Field name cannot be longer than 10 characters
     */
    public function testSetNameTooLarge() {
        $this->getFieldStub()->setName('VERY_LARGE_NAME');
    }

    /**
     * @covers ::setLength
     * @covers ::getLength
     */
    public function testSetLength() {
        $field = $this->getFieldStub();
        self::assertSame($field, $field->setLength('123'));
        self::assertSame(123, $field->getLength());
    }

    /**
     * @covers ::setLoad
     * @covers ::isLoad
     */
    public function testSetLoad() {
        $field = $this->getFieldStub();
        self::assertTrue($field->isLoad());
        self::assertSame($field, $field->setLoad(''));
        self::assertFalse($field->isLoad());
    }

    /**
     * @covers ::unserialize
     */
    public function testUnserialize() {
        $fA = $this->mock(self::CLS_FILTER)
            ->toValue(array('FROM_DATA'), 'FROM_FA', self::once())
            ->supportsType(true)
            ->new();
        $fB = $this->mock(self::CLS_FILTER)
            ->toValue(array('FROM_FA'), 'FROM_FB', self::once())
            ->supportsType(true)
            ->new();
        $field = $this->mock(self::CLS_FIELD)
            ->fromData(array('IN_DATA'), 'FROM_DATA', self::once())
            ->toData()
            ->getType()
            ->getFilters(array(), array($fA, $fB), self::once())
            ->new();
        self::assertSame('FROM_FB', $field->unserialize('IN_DATA'));
    }

    /**
     * @covers ::serialize
     */
    public function testSerialize() {
        $fB = $this->mock(self::CLS_FILTER)
            ->fromValue(array('FROM_FB'), 'FROM_FA', self::once())
            ->supportsType(true)
            ->new();
        $fA = $this->mock(self::CLS_FILTER)
            ->fromValue(array('FROM_FA'), 'FROM_DATA', self::once())
            ->supportsType(true)
            ->new();
        $field = $this->mock(self::CLS_FIELD)
            ->fromData()
            ->toData(array('FROM_DATA'), 'IN_DATA', self::once())
            ->getType()
            ->getFilters(array(), array($fA, $fB), self::once())
            ->new();
        self::assertSame('IN_DATA', $field->serialize('FROM_FB'));
    }

    /**
     * @return array
     */
    public function dataCreate() {
        return array(
            array(Field::TYPE_CHARACTER, '\org\majkel\dbase\Field\CharacterField', false),
            array(Field::TYPE_DATE, '\org\majkel\dbase\Field\DateField', false),
            array(Field::TYPE_LOGICAL, '\org\majkel\dbase\Field\LogicalField', false),
            array(Field::TYPE_MEMO, '\org\majkel\dbase\Field\MemoField', true),
            array(Field::TYPE_NUMERIC, '\org\majkel\dbase\Field\NumericField', false),
        );
    }

    /**
     * @dataProvider dataCreate
     * @covers ::create
     * @covers ::isMemoEntry
     * @covers \org\majkel\dbase\field\MemoField::isMemoEntry
     */
    public function testCreate($type, $class, $isMemoEntry) {
        $field = Field::create($type);
        self::assertInstanceOf($class, $field);
        self::assertSame($isMemoEntry, $field->isMemoEntry());
    }

    /**
     * @covers ::create
     * @expectedException \org\majkel\dbase\Exception
     * @expectedExceptionMessage Unsupported field `UNKNOWN`
     */
    public function testCreateUnknown() {
        Field::create('UNKNOWN');
    }

    /**
     * @covers ::getTypes
     */
    public function testGetTypes() {
        $reflection = new ReflectionClass(self::CLS_FIELD);
        $types = Field::getTypes();
        $constants = $reflection->getConstants();
        foreach ($constants as $name => $value) {
            if (strpos($name, 'TYPE_') === 0) {
                if (!in_array($value, $types)) {
                    self::fail("Does not return `$name` => `$value`");
                }
            }
        }
        self::assertTrue(true);
    }
}

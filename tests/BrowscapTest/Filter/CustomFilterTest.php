<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   BrowscapTest
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Filter;

use Browscap\Filter\CustomFilter;

/**
 * Class CustomFilterTest
 *
 * @category   BrowscapTest
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class CustomFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Browscap\Filter\CustomFilter
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new CustomFilter(['Parent']);
    }

    /**
     * tests getter for the filter type
     *
     * @group filter
     * @group sourcetest
     */
    public function testGetType()
    {
        self::assertSame('CUSTOM', $this->object->getType());
    }

    /**
     * tests detecting if a divion should be in the output
     *
     * @group filter
     * @group sourcetest
     */
    public function testIsOutput()
    {
        $division = $this->createMock(\Browscap\Data\Division::class);

        self::assertTrue($this->object->isOutput($division));
    }

    /**
     * Data Provider for the test testIsOutputProperty
     *
     * @return array<string|boolean>[]
     */
    public function outputPropertiesDataProvider()
    {
        return [
            ['Comment', false],
            ['Browser', false],
            ['Platform', false],
            ['Platform_Description', false],
            ['Device_Name', false],
            ['Device_Maker', false],
            ['RenderingEngine_Name', false],
            ['RenderingEngine_Description', false],
            ['Parent', true],
            ['Platform_Version', false],
            ['RenderingEngine_Version', false],
            ['Version', false],
            ['MajorVer', false],
            ['MinorVer', false],
            ['CssVersion', false],
            ['AolVersion', false],
            ['Alpha', false],
            ['Beta', false],
            ['Win16', false],
            ['Win32', false],
            ['Win64', false],
            ['Frames', false],
            ['IFrames', false],
            ['Tables', false],
            ['Cookies', false],
            ['BackgroundSounds', false],
            ['JavaScript', false],
            ['VBScript', false],
            ['JavaApplets', false],
            ['ActiveXControls', false],
            ['isMobileDevice', false],
            ['isSyndicationReader', false],
            ['Crawler', false],
            ['lite', false],
            ['sortIndex', false],
            ['Parents', false],
            ['division', false],
            ['Browser_Type', false],
            ['Device_Type', false],
            ['Device_Pointing_Method', false],
            ['isTablet', false],
            ['Browser_Maker', false],
        ];
    }

    /**
     * @dataProvider outputPropertiesDataProvider
     *
     * @group filter
     * @group sourcetest
     */
    public function testIsOutputProperty($propertyName, $isExtra)
    {
        $actualValue = $this->object->isOutputProperty($propertyName);
        self::assertSame($isExtra, $actualValue);
    }

    /**
     * tests if a section is always in the output
     *
     * @group filter
     * @group sourcetest
     */
    public function testIsOutputSectionAlways()
    {
        $this->assertTrue($this->object->isOutputSection([]));
        $this->assertTrue($this->object->isOutputSection(['lite' => false]));
        $this->assertTrue($this->object->isOutputSection(['lite' => true]));
    }
}

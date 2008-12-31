<?php
# Mantis - a php based bugtracking system

# Mantis is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# Mantis is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Mantis.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package Tests
 * @subpackage UnitTests
 * @copyright Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 * @copyright Copyright (C) 2002 - 2009  Mantis Team   - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 */

require_once 'PHPUnit/Framework.php';

$t_root_path = dirname( dirname( __FILE__ ) )  . DIRECTORY_SEPARATOR;
require_once $t_root_path . 'Mantis/Enum.php';

/**
 * Test cases for Mantis_Enum class.
 */
class MantisEnumTest extends PHPUnit_Framework_TestCase {
	const ACCESS_LEVELS_ENUM = '10:viewer,25:reporter,40:updater,55:developer,70:manager,90:administrator';
	const ACCESS_LEVELS_LOCALIZED_ENUM = '10:viewer_x,25:reporter_x,40:updater_x,55:developer_x,70:manager_x,90:administrator_x,95:extra_x';
	const EMPTY_ENUM = '';
	const DUPLICATE_VALUES_ENUM = '10:viewer1,10:viewer2';
	const DUPLICATE_LABELS_ENUM = '10:viewer,20:viewer';
	const SINGLE_VALUE_ENUM = '10:viewer';
	const NAME_WITH_SPACES_ENUM = '10:first label,20:second label';
	const NON_TRIMMED_ENUM = '10 : viewer, 20 : reporter';

	/**
	 * Tests getLabel() method.
	 */
	public function testGetLabel() {    	
		$this->assertEquals( 'viewer', Mantis_Enum::getLabel( MantisEnumTest::ACCESS_LEVELS_ENUM, 10 ) );
		$this->assertEquals( 'reporter', Mantis_Enum::getLabel( MantisEnumTest::ACCESS_LEVELS_ENUM, 25 ) );
		$this->assertEquals( 'updater', Mantis_Enum::getLabel( MantisEnumTest::ACCESS_LEVELS_ENUM, 40 ) );
		$this->assertEquals( 'developer', Mantis_Enum::getLabel( MantisEnumTest::ACCESS_LEVELS_ENUM, 55 ) );
		$this->assertEquals( 'manager', Mantis_Enum::getLabel( MantisEnumTest::ACCESS_LEVELS_ENUM, 70 ) );
		$this->assertEquals( 'administrator', Mantis_Enum::getLabel( MantisEnumTest::ACCESS_LEVELS_ENUM, 90 ) );
		$this->assertEquals( '@100@', Mantis_Enum::getLabel( MantisEnumTest::ACCESS_LEVELS_ENUM, 100 ) );
		$this->assertEquals( '@-1@', Mantis_Enum::getLabel( MantisEnumTest::ACCESS_LEVELS_ENUM, -1 ) );
        $this->assertEquals( '@10@', Mantis_Enum::getLabel( MantisEnumTest::EMPTY_ENUM, 10 ) );
	}
    
	/**
	 * Tests getLocalizedLabel() method.
	 */
	public function testGetLocalizedLabel() {    	
		// Test existing case
		$this->assertEquals( 'viewer_x', Mantis_Enum::getLocalizedLabel( MantisEnumTest::ACCESS_LEVELS_ENUM, MantisEnumTest::ACCESS_LEVELS_LOCALIZED_ENUM, 10 ) );
		
		// Test unknown case
		$this->assertEquals( '@5@', Mantis_Enum::getLocalizedLabel( MantisEnumTest::ACCESS_LEVELS_ENUM, MantisEnumTest::ACCESS_LEVELS_LOCALIZED_ENUM, 5 ) );

		// Test the case where the value is in the localized enum but not the standard one.  In this case it should be treated
		// as unknown.
		$this->assertEquals( '@95@', Mantis_Enum::getLocalizedLabel( MantisEnumTest::ACCESS_LEVELS_ENUM, MantisEnumTest::ACCESS_LEVELS_LOCALIZED_ENUM, 95 ) );
	}
    
	/**
	 * Tests getValues() method.
	 */
	public function testGetValues() {
		$this->assertEquals( array( 10, 25, 40, 55, 70,90 ), Mantis_Enum::getValues( MantisEnumTest::ACCESS_LEVELS_ENUM, 10 ) );
		$this->assertEquals( array(), Mantis_Enum::getValues( MantisEnumTest::EMPTY_ENUM, 10 ) );
	}

	/**
	 * Tests getAssocArrayIndexedByValues() method.
	 */
	public function testGetAssocArrayIndexedByValues() {
		$this->assertEquals( array(), Mantis_Enum::getAssocArrayIndexedByValues( MantisEnumTest::EMPTY_ENUM ) );
		$this->assertEquals( array( 10 => 'viewer' ), Mantis_Enum::getAssocArrayIndexedByValues( MantisEnumTest::SINGLE_VALUE_ENUM ) );
		$this->assertEquals( array( 10 => 'viewer1' ), Mantis_Enum::getAssocArrayIndexedByValues( MantisEnumTest::DUPLICATE_VALUES_ENUM ) );
		$this->assertEquals( array( 10 => 'viewer', 20 => 'viewer' ), Mantis_Enum::getAssocArrayIndexedByValues( MantisEnumTest::DUPLICATE_LABELS_ENUM ) );
		$this->assertEquals( array( 10 => 'first label', 20 => 'second label' ), Mantis_Enum::getAssocArrayIndexedByValues( MantisEnumTest::NAME_WITH_SPACES_ENUM ) );
	}

	/**
	 * Tests getAssocArrayIndexedByLabels() method.
	 */
	public function testGetAssocArrayIndexedByLabels() {
		$this->assertEquals( array(), Mantis_Enum::getAssocArrayIndexedByLabels( MantisEnumTest::EMPTY_ENUM ) );
		$this->assertEquals( array( 'viewer' => 10 ), Mantis_Enum::getAssocArrayIndexedByLabels( MantisEnumTest::SINGLE_VALUE_ENUM ) );
		$this->assertEquals( array( 'viewer1' => 10 ), Mantis_Enum::getAssocArrayIndexedByLabels( MantisEnumTest::DUPLICATE_VALUES_ENUM ) );
		$this->assertEquals( array( 'viewer' => 10, 'viewer' => 20 ), Mantis_Enum::getAssocArrayIndexedByLabels( MantisEnumTest::DUPLICATE_LABELS_ENUM ) );
		$this->assertEquals( array( 'first label' => 10, 'second label' => 20 ), Mantis_Enum::getAssocArrayIndexedByLabels( MantisEnumTest::NAME_WITH_SPACES_ENUM ) );
	}

	/**
	 * Tests getValue() method.
	 */
	public function testGetValue() {
		$this->assertEquals( false, Mantis_Enum::getValue( MantisEnumTest::EMPTY_ENUM, 'viewer' ) );
		$this->assertEquals( 10, Mantis_Enum::getValue( MantisEnumTest::SINGLE_VALUE_ENUM, 'viewer' ) );
		$this->assertEquals( 10, Mantis_Enum::getValue( MantisEnumTest::DUPLICATE_VALUES_ENUM, 'viewer1' ) );
		$this->assertEquals( 20, Mantis_Enum::getValue( MantisEnumTest::NAME_WITH_SPACES_ENUM, 'second label' ) );
		
		// This is not inconsisent with duplicate values behavior, however, it is considered correct since it simplies the code
		// and it is not a real scenario.
		$this->assertEquals( 20, Mantis_Enum::getValue( MantisEnumTest::DUPLICATE_LABELS_ENUM, 'viewer' ) );
	}

	/**
	 * Tests hasValue() method.
	 */
	public function testHasValue() {
		$this->assertEquals( true, Mantis_Enum::hasValue( MantisEnumTest::ACCESS_LEVELS_ENUM, 10 ) );
		$this->assertEquals( false, Mantis_Enum::hasValue( MantisEnumTest::ACCESS_LEVELS_ENUM, 5 ) );
		$this->assertEquals( false, Mantis_Enum::hasValue( MantisEnumTest::EMPTY_ENUM, 10 ) );
	}

	/**
	 * Tests enumerations that contain duplicate values.
	 */
	public function testDuplicateValuesEnum() {    	
		$this->assertEquals( 'viewer1', Mantis_Enum::getLabel( MantisEnumTest::DUPLICATE_VALUES_ENUM, 10 ) );
		$this->assertEquals( '@100@', Mantis_Enum::getLabel( MantisEnumTest::DUPLICATE_VALUES_ENUM, 100 ) );
	}

	/**
	 * Tests enumerations that contain duplicate labels.
	 */
	public function testDuplicateLabelsValuesEnum() {    	
		$this->assertEquals( 'viewer', Mantis_Enum::getLabel( MantisEnumTest::DUPLICATE_LABELS_ENUM, 10 ) );
		$this->assertEquals( 'viewer', Mantis_Enum::getLabel( MantisEnumTest::DUPLICATE_LABELS_ENUM, 20 ) );
		$this->assertEquals( '@100@', Mantis_Enum::getLabel( MantisEnumTest::DUPLICATE_LABELS_ENUM, 100 ) );
	}

	/**
	 * Tests enumerations with a single tuple.
	 */
	public function testSingleValueEnum() {    	
		$this->assertEquals( 'viewer', Mantis_Enum::getLabel( MantisEnumTest::SINGLE_VALUE_ENUM, 10 ) );
		$this->assertEquals( '@100@', Mantis_Enum::getLabel( MantisEnumTest::SINGLE_VALUE_ENUM, 100 ) );
	}

	/**
	 * Tests enumerations with labels that contain spaces.
	 */
	public function testNameWithSpacesEnum() {    	
		$this->assertEquals( 'first label', Mantis_Enum::getLabel( MantisEnumTest::NAME_WITH_SPACES_ENUM, 10 ) );
		$this->assertEquals( 'second label', Mantis_Enum::getLabel( MantisEnumTest::NAME_WITH_SPACES_ENUM, 20 ) );
		$this->assertEquals( '@100@', Mantis_Enum::getLabel( MantisEnumTest::NAME_WITH_SPACES_ENUM, 100 ) );
	}
    
	/**
	 * Tests enumerations that contain duplicate labels.
	 */
	public function testNonTrimmedEnum() {    	
		$this->assertEquals( 'viewer', Mantis_Enum::getLabel( MantisEnumTest::NON_TRIMMED_ENUM, 10 ) );
		$this->assertEquals( 'reporter', Mantis_Enum::getLabel( MantisEnumTest::NON_TRIMMED_ENUM, 20 ) );
		$this->assertEquals( '@100@', Mantis_Enum::getLabel( MantisEnumTest::NON_TRIMMED_ENUM, 100 ) );
	}
}

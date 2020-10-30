<?php

namespace Tests\AppBundle\Domain\Service\Register;

use AppBundle\Domain\Service\Register\ValidationResults;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for domain service DTO ValidationResults
 *
 * @package Tests\AppBundle\Domain\Service\Register
 */
class ValidationResultsTest extends TestCase
{
    /**
     * Test the status of an empty object
     */
    public function testEmptyObjectStatus()
    {
        $validationResults = new ValidationResults();

        $this->assertTrue($validationResults->isValidated());
        $this->assertEquals(0, $validationResults->status());
    }

    /**
     * Test the status after adding a global error
     */
    public function testAddGlobalErrorStatus()
    {
        $validationResults = new ValidationResults();
        $validationResults->addGlobalError('err101');

        $this->assertFalse($validationResults->isValidated());
        $this->assertEquals(4, $validationResults->status());
    }

    /**
     * Test the status after adding a field error
     */
    public function testAddFieldErrorStatus()
    {
        $validationResults = new ValidationResults();
        $validationResults->addFieldError('err201', 'field201');

        $this->assertFalse($validationResults->isValidated());
        $this->assertEquals(4, $validationResults->status());
    }

    /**
     * Test the content of an empty object
     */
    public function testEmptyObjectResults()
    {
        $validationResults = new ValidationResults();

        $this->assertEmpty($validationResults->result());
    }

    /**
     * Test the content after adding a global error
     */
    public function testAddGlobalErrorResult()
    {
        $validationResults = new ValidationResults();
        $validationResults->addGlobalError('err301');

        $expected = [ 'global' => [ 'err301' ]];

        $this->assertEquals($expected, $validationResults->result());
    }

    /**
     * Test the content after adding a field error
     */
    public function testAddFieldErrorResult()
    {
        $validationResults = new ValidationResults();
        $validationResults->addFieldError('err401', 'field401');

        $expected = [ 'field401' => ['err401' ]];

        $this->assertEquals($expected, $validationResults->result());
    }

    /**
     * Test the content after adding 3 global errors
     */
    public function testAddManyGlobalErrorResult()
    {
        $validationResults = new ValidationResults();
        $validationResults->addGlobalError('err501');
        $validationResults->addGlobalError('err502');
        $validationResults->addGlobalError('err503');

        $expected = [ 'global' => [ 'err501', 'err502', 'err503' ]];

        $this->assertEquals($expected, $validationResults->result());
    }

    /**
     * Test the content after adding 3 errors in the same field
     */
    public function testAddManyFieldSameLevelErrorResult()
    {
        $validationResults = new ValidationResults();
        $validationResults->addFieldError('err601', 'field601');
        $validationResults->addFieldError('err602', 'field601');
        $validationResults->addFieldError('err603', 'field601');

        $expected = [ 'field601' => [ 'err601', 'err602', 'err603' ]];

        $this->assertEquals($expected, $validationResults->result());
    }

    /**
     * Test the content after adding an error in 3 fields
     */
    public function testAddManyFieldManyLevelsErrorResult()
    {
        $validationResults = new ValidationResults();
        $validationResults->addFieldError('err701', 'field701');
        $validationResults->addFieldError('err702', 'field702');
        $validationResults->addFieldError('err703', 'field703');

        $expected = [
            'field701' => [ 'err701' ],
            'field702' => [ 'err702' ],
            'field703' => [ 'err703' ]
        ];

        $this->assertEquals($expected, $validationResults->result());
    }

    /**
     * Test the content after adding an global and field errors
     */
    public function testAddManyMixedErrorsResult()
    {
        $validationResults = new ValidationResults();
        $validationResults->addGlobalError('err801');
        $validationResults->addFieldError('err811', 'field811');
        $validationResults->addFieldError('err821', 'field821');
        $validationResults->addGlobalError('err802');
        $validationResults->addFieldError('err812', 'field811');

        $expected = [
            'global' => [ 'err801', 'err802' ],
            'field811' => [ 'err811', 'err812' ],
            'field821' => [ 'err821' ]
        ];

        $this->assertEquals($expected, $validationResults->result());
    }

    /**
     * Test merge results when empty
     */
    public function testMergeResultsAllCases()
    {
        $validationResults = new ValidationResults();

        $otherValidationResults = new ValidationResults();

        $validationResults->mergeResults($otherValidationResults);

        $expected = [];

        $this->assertTrue($validationResults->isValidated());
        $this->assertEquals($expected, $validationResults->result());
    }

    /**
     * Test merge results with all king of errors
     */
    public function testMergeResultsWithAllCases()
    {
        $validationResults = new ValidationResults();
        $validationResults->addGlobalError('err901');
        $validationResults->addFieldError('err911', 'field911');
        $validationResults->addFieldError('err912', 'field911');

        $otherValidationResults = new ValidationResults();
        $otherValidationResults->addGlobalError('err902');
        $otherValidationResults->addFieldError('err913', 'field911');
        $otherValidationResults->addGlobalError('err903');
        $otherValidationResults->addFieldError('err921', 'field921');

        $validationResults->mergeResults($otherValidationResults);

        $expected = [
            'global' => [ 'err901', 'err902', 'err903' ],
            'field911' => [ 'err911', 'err912', 'err913' ],
            'field921' => [ 'err921' ]
        ];

        $this->assertEquals($expected, $validationResults->result());
    }
}

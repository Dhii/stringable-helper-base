<?php

namespace Dhii\Util\String\UnitTest;

use Dhii\Util\String\JoinCapableTrait as TestSubject;
use InvalidArgumentException;
use OutOfRangeException;
use Xpmock\TestCase;
use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_MockObject_MockBuilder as MockBuilder;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class StringableJoinCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Util\String\StringableJoinCapableTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods The methods to mock.
     *
     * @return MockObject The new instance.
     */
    public function createInstance($methods = [])
    {
        is_array($methods) && $methods = $this->mergeValues($methods, [
            '__',
        ]);

        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
            ->setMethods($methods)
            ->getMockForTrait();

        $mock->method('__')
                ->will($this->returnArgument(0));

        return $mock;
    }

    /**
     * Merges the values of two arrays.
     *
     * The resulting product will be a numeric array where the values of both inputs are present, without duplicates.
     *
     * @since [*next-version*]
     *
     * @param array $destination The base array.
     * @param array $source      The array with more keys.
     *
     * @return array The array which contains unique values
     */
    public function mergeValues($destination, $source)
    {
        return array_keys(array_merge(array_flip($destination), array_flip($source)));
    }

    /**
     * Creates a mock that both extends a class and implements interfaces.
     *
     * This is particularly useful for cases where the mock is based on an
     * internal class, such as in the case with exceptions. Helps to avoid
     * writing hard-coded stubs.
     *
     * @since [*next-version*]
     *
     * @param string $className      Name of the class for the mock to extend.
     * @param string $interfaceNames Names of the interfaces for the mock to implement.
     *
     * @return MockBuilder The builder for a mock of an object that extends and implements
     *                     the specified class and interfaces.
     */
    public function mockClassAndInterfaces($className, $interfaceNames = [])
    {
        $paddingClassName = uniqid($className);
        $definition = vsprintf('abstract class %1$s extends %2$s implements %3$s {}', [
            $paddingClassName,
            $className,
            implode(', ', $interfaceNames),
        ]);
        eval($definition);

        return $this->getMockBuilder($paddingClassName);
    }

    /**
     * Creates a new exception.
     *
     * @since [*next-version*]
     *
     * @param string $message The exception message.
     *
     * @return RootException|MockObject The new exception.
     */
    public function createException($message = '')
    {
        $mock = $this->getMockBuilder('Exception')
            ->setConstructorArgs([$message])
            ->getMock();

        return $mock;
    }

    /**
     * Creates a new Invalid Argument exception.
     *
     * @since [*next-version*]
     *
     * @param string $message The exception message.
     *
     * @return InvalidArgumentException|MockObject The new exception.
     */
    public function createInvalidArgumentException($message = '')
    {
        $mock = $this->getMockBuilder('InvalidArgumentException')
            ->setConstructorArgs([$message])
            ->getMock();

        return $mock;
    }

    /**
     * Creates a new Out of RangeException exception.
     *
     * @since [*next-version*]
     *
     * @param string $message The exception message.
     *
     * @return OutOfRangeException|MockObject The new exception.
     */
    public function createOutOfRangeException($message = '')
    {
        $mock = $this->getMockBuilder('OutOfRangeException')
            ->setConstructorArgs([$message])
            ->getMock();

        return $mock;
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInternalType(
            'object',
            $subject,
            'A valid instance of the test subject could not be created.'
        );
    }

    /**
     * Tests that `_stringableJoin()` works as expected.
     *
     * @since [*next-version*]
     */
    public function testStringableJoin()
    {
        $part1 = uniqid('part');
        $part2 = uniqid('part');
        $part3 = uniqid('part');
        $parts = [$part1, $part2, $part3];
        $delimiter = uniqid('delimiter');

        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
            ->method('_normalizeIterable')
            ->with($parts)
            ->will($this->returnArgument(0));
        $subject->expects($this->exactly(count($parts)))
            ->method('_normalizeString')
            ->withConsecutive([$part1], [$part2], [$part3])
            ->will($this->returnArgument(0));

        $result = $_subject->_stringableJoin($parts, $delimiter);
        $this->assertEquals(implode($delimiter, $parts), $result, 'Wrong join result');
    }

    /**
     * Tests that `_stringableJoin()` fails as expected when one of the parts is invalid.
     *
     * @since [*next-version*]
     */
    public function testStringableJoinFailureInvalidPart()
    {
        $part = uniqid('part');
        $parts = [$part];
        $delimiter = uniqid('delimiter');
        $invalidArgumentException = $this->createInvalidArgumentException('Invalid part');
        $exception = $this->createOutOfRangeException('Could not join');
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
            ->method('_normalizeIterable')
            ->with($parts)
            ->will($this->returnArgument(0));
        $subject->expects($this->exactly(1))
            ->method('_normalizeString')
            ->with($part)
            ->will($this->throwException($invalidArgumentException));
        $subject->expects($this->exactly(1))
            ->method('_createOutOfRangeException')
            ->with(
                $this->isType('string'),
                null,
                $invalidArgumentException,
                $part
            )
            ->will($this->returnValue($exception));

        $this->setExpectedException('OutOfRangeException');
        $result = $_subject->_stringableJoin($parts, $delimiter);
    }
}

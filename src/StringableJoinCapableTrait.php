<?php

namespace Dhii\Util\String;

use InvalidArgumentException;
use OutOfRangeException;
use Exception as RootException;
use stdClass;
use Traversable;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for joining iterables into strings.
 *
 * @since [*next-version*]
 */
trait StringableJoinCapableTrait
{
    /**
     * Joins a list of parts using a delimiter.
     *
     * @param array|stdClass|Traversable $parts The list of parts to join.
     * @param string|Stringable          $delim The delimiter to use for joining.
     *
     * @throws InvalidArgumentException If the list of parts or the delimiter is invalid.
     * @throws OutOfRangeException      If one of the parts or the delimiter cannot be normalized to string.
     *
     * @return string
     */
    protected function _stringableJoin($parts, $delim)
    {
        $parts  = $this->_normalizeIterable($parts);
        $i      = 0;
        $result = '';
        foreach ($parts as $_part) {
            try {
                $_part = $this->_normalizeString($_part);
            } catch (InvalidArgumentException $e) {
                throw $this->_createOutOfRangeException($this->__('Invalid string part'), null, $e, $_part);
            }

            if ($i) {
                $result .= $delim;
            }

            $result .= $_part;
            ++$i;
        };

        return $result;
    }

    /**
     * Normalizes a value to its string representation.
     *
     * The values that can be normalized are any scalar values, as well as
     * {@see StringableInterface).
     *
     * @since [*next-version*]
     *
     * @param Stringable|string|int|float|bool $subject The value to normalize to string.
     *
     * @throws InvalidArgumentException If the value cannot be normalized.
     *
     * @return string The string that resulted from normalization.
     */
    abstract protected function _normalizeString($subject);

    /**
     * Normalizes an iterable.
     *
     * Makes sure that the return value can be iterated over.
     *
     * @since [*next-version*]
     *
     * @param mixed $iterable The iterable to normalize.
     *
     * @throws InvalidArgumentException If the iterable could not be normalized.
     *
     * @return array|Traversable|stdClass The normalized iterable.
     */
    abstract protected function _normalizeIterable($iterable);

    /**
     * Creates a new Out Of Range exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     * @param mixed|null                            $argument The value that is out of range, if any.
     *
     * @return OutOfRangeException The new exception.
     */
    abstract protected function _createOutOfRangeException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $argument = null
    );

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see   sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}

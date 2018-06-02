<?php

namespace Dhii\Util\String;

use InvalidArgumentException;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for replacing strings.
 *
 * @since [*next-version*]
 */
trait StringableReplaceCapableTrait
{
    /**
     * Replaces occurrences of needle in haystack.
     *
     * @since [*next-version*]
     * @see str_replace()
     *
     * @param string|Stringable $search  The string to look for.
     * @param string|Stringable $replace The string to replace with.
     * @param string|Stringable $subject The string look in.
     *
     * @throw InvalidArgumentException If the needle, replacement, or haystack is invalid.
     * @throw RuntimeException If problem replacing.
     *
     * @return string The haystack with all occurrences of needle replaced.
     */
    public function _stringableReplace($search, $replace, $subject)
    {
        $search  = $this->_normalizeString($search);
        $replace = $this->_normalizeString($replace);
        $subject = $this->_normalizeString($subject);

        return str_replace($search, $replace, $subject);
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
}

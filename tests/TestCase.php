<?php

namespace Geokit;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function setExpectedException($exceptionName, $exceptionMessage = '', $exceptionCode = null)
    {
        if (!\method_exists($this, 'expectException')) {
            parent::setExpectedException(
                $exceptionName,
                $exceptionMessage,
                $exceptionCode
            );
            return;
        }

        $this->expectException($exceptionName);

        if ('' !== $exceptionMessage) {
            $this->expectExceptionMessage($exceptionMessage);
        }

        if (null !== $exceptionCode) {
            $this->expectExceptionCode($exceptionCode);
        }
    }

    public function setExpectedExceptionRegExp($exceptionName, $exceptionMessageRegExp = '', $exceptionCode = null)
    {
        if (!\method_exists($this, 'expectExceptionMessageRegExp')) {
            parent::setExpectedExceptionRegExp(
                $exceptionName,
                $exceptionMessageRegExp,
                $exceptionCode
            );
            return;
        }

        $this->expectException($exceptionName);

        if ('' !== $exceptionMessageRegExp) {
            $this->expectExceptionMessageRegExp($exceptionMessageRegExp);
        }

        if (null !== $exceptionCode) {
            $this->expectExceptionCode($exceptionCode);
        }
    }
}

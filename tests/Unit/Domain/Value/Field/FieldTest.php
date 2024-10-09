<?php

declare(strict_types=1);

/**
 * This file is part of Storyblok-Api.
 *
 * (c) SensioLabs Deutschland <info@sensiolabs.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SensioLabs\Storyblok\Api\Tests\Unit\Domain\Value\Field;

use PHPUnit\Framework\TestCase;
use SensioLabs\Storyblok\Api\Domain\Value\Field\Field;
use SensioLabs\Storyblok\Api\Tests\Util\FakerTrait;

/**
 * @author Silas Joisten <silasjoisten@proton.me>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class FieldTest extends TestCase
{
    use FakerTrait;

    /**
     * @test
     */
    public function value(): void
    {
        $value = self::faker()->word();

        self::assertSame($value, (new Field($value))->value);
    }

    /**
     * @test
     *
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     * @dataProvider \Ergebnis\DataProvider\StringProvider::empty()
     */
    public function valueInvalid(string $value): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Field($value);
    }
}

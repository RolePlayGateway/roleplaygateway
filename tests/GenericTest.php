<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers Vector
 */
final class GenericTest extends TestCase
{
  public function testVectorEquals(): void
  {
    $this->assertEquals(
      'Hello, world.',
      'Hello, world.'
    );
  }
}

<?php

namespace Akeneo\Bundle\BatchBundle\Tests\Unit\Item;

use Akeneo\Component\Batch\Item\AbstractConfigurableStepElement;
use Akeneo\Component\Batch\Item\ItemProcessorInterface;

/**
 * Test helpers class needed as there is no way to create a mock from
 * phpUnit that extends and implements as the same time
 */
abstract class ItemProcessorTestHelper extends AbstractConfigurableStepElement implements ItemProcessorInterface
{
}
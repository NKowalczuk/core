<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ApiPlatform\Core\Annotation;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;

/**
 * Hydrates attributes from annotation's parameters.
 *
 * @internal
 *
 * @author Baptiste Meyer <baptiste.meyer@gmail.com>
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
trait AttributesHydratorTrait
{
    /**
     * @var array
     */
    public $attributes = null;

    /**
     * @throws InvalidArgumentException
     */
    private function hydrateAttributes(array $values): void
    {
        if (isset($values['attributes'])) {
            $this->attributes = $values['attributes'];
            unset($values['attributes']);
        }

        foreach ($values as $key => $value) {
            $key = (string) $key;
            if (!property_exists($this, $key)) {
                throw new InvalidArgumentException(sprintf('Unknown property "%s" on annotation "%s".', $key, self::class));
            }

            if ((new \ReflectionProperty($this, $key))->isPublic()) {
                $this->{$key} = $value;
                continue;
            }

            if (!\is_array($this->attributes)) {
                $this->attributes = [];
            }

            $this->attributes += [InflectorFactory::create()->build()->tableize($key) => $value];
        }
    }
}

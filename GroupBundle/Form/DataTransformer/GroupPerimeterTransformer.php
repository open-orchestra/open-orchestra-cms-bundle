<?php

namespace OpenOrchestra\GroupBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use OpenOrchestra\Backoffice\GeneratePerimeter\GeneratePerimeterManager;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\GroupBundle\Document\Perimeter;

/**
 * Class GroupPerimeterTransformer
 */
class GroupPerimeterTransformer implements DataTransformerInterface
{
    protected $generatePerimeterManager;

    /**
     * @param GeneratePerimeterManager $generatePerimeterManager
     */
    public function __construct(
        GeneratePerimeterManager $generatePerimeterManager
    ) {
        $this->generatePerimeterManager = $generatePerimeterManager;
    }

    /**
     * Transform an array of roles to choices
     *
     * @param array $value
     *
     * @return array
     */
    public function transform($value)
    {
        $result = array();
        $configuration = $this->generatePerimeterManager->generatePerimeters();
        $value = $value->toArray();

        foreach ($configuration as $type => &$paths) {
            $items = array_key_exists($type, $value) ? $value[$type]->getItems() : array();
            array_walk($paths, function($path) use (&$result, $items, $type) {
                $key = GeneratePerimeterManager::changePathToName($path);
                $result[$type][$key] = in_array($path, $items);
                $result[$type][$key] = false;
                foreach($items as $item) {
                    $result[$type][$key] = $result[$type][$key] || preg_match('/^' . preg_quote($item, '/') . '.*$/', $path);
                }
            });
        }

        return $result;
    }

    /**
     * Transform an array choices to array of roles
     *
     * @param array $value
     *
     * @return array
     */
    public function reverseTransform($value)
    {
        $result = new ArrayCollection();
        if (is_array($value)) {
            foreach ($value as $type => $perimeters) {
                if (is_array($perimeters)) {
                    foreach ($perimeters as $perimeter => $checked) {
                        if (!$checked) {
                            unset($perimeters[$perimeter]);
                        }
                    }
                    $perimeters = array_keys($perimeters);
                    $sourcePerimeters = $perimeters;
                    foreach ($sourcePerimeters as $sourcePerimeter) {
                        foreach ($perimeters as $key => $perimeter) {
                            if (preg_match('/^' . preg_quote($sourcePerimeter) . '.+$/', $perimeter)) {
                                unset($perimeters[$key]);
                            }
                        }
                    }
                    array_walk($perimeters, function(&$name) {
                        $name = GeneratePerimeterManager::changeNameToPath($name);
                    });
                    $perimeterCollection = new Perimeter($type);
                    $perimeterCollection->addItems($perimeters);
                }
                $result->set($type, $perimeterCollection);
            }
        }

        return $result;
    }
}

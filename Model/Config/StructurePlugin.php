<?php

namespace Gento\Shipping\Model\Config;

use Gento\Shipping\Model\Config\Source\Order\StatusComplete;
use Gento\Shipping\Model\Config\Source\Order\StatusPending;
use Magento\Config\Model\Config\ScopeDefiner;
use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\Structure\Element\Group;
use Magento\Config\Model\Config\Structure\Element\Section;

class StructurePlugin
{
    /**
     * @var ScopeDefiner
     */
    private $scopeDefiner;

    /**
     * @param ScopeDefiner $scopeDefiner
     */
    public function __construct(
        ScopeDefiner $scopeDefiner
    ) {
        $this->scopeDefiner = $scopeDefiner;
    }

    public function aroundGetElementByPathParts(
        Structure $subject,
        \Closure $proceed,
        array $pathParts
    ) {
        $isCarriers = $pathParts[0] == 'carriers';
        if ($isCarriers) {
        }

        $result = $proceed($pathParts);

        if ($isCarriers && isset($result)) {
            if ($result instanceof Section) {
                /** @var Section $result */
                $childrens = [];
                foreach ($result->getChildren() as /** @var Group $group */$group) {
                    $childrenFields = [
                        'gento_shipping_status_process' => [
                            'label' => 'Pending state',
                            'id' => 'gento_shipping_status_process',
                            'translate' => 'label comment',
                            'type' => 'select',
                            'showInDefault' => '1',
                            'showInWebsite' => '1',
                            'showInStore' => '1',
                            '_elementType' => 'field',
                            'source_model' => StatusPending::class,
                            'path' => 'carriers/' . $group->getId(),
                        ],
                        'gento_shipping_status_complete' => [
                            'label' => 'Complete state',
                            'id' => 'gento_shipping_status_complete',
                            'translate' => 'label comment',
                            'type' => 'select',
                            'showInDefault' => '1',
                            'showInWebsite' => '1',
                            'showInStore' => '1',
                            '_elementType' => 'field',
                            'source_model' => StatusComplete::class,
                            'path' => 'carriers/' . $group->getId(),
                        ],
                    ];

                    $groupData = $group->getData();
                    $groupData['children'] = array_merge(
                        $groupData['children'],
                        $childrenFields
                    );
                    $group->setData($groupData, $this->scopeDefiner->getScope());
                    $childrens[$group->getId()] = $group->getData();
                }

                $result->setData(array_merge(
                    $result->getData(),
                    ['children' => $childrens]
                ), $this->scopeDefiner->getScope());

            }
        }

        return $result;
    }

    public function afterGetFieldPaths(
        Structure $subject,
        $result
    ) {
        $keys = [];
        foreach (array_keys($result) as $key) {
            if (preg_match('/^carriers\/([^\/]+)/', $key, $matches)) {
                $keys[$matches[1]] = true;
            }
        }

        foreach (array_keys($keys) as $key) {
            $result['carriers/' . $key . '/gento_shipping_status_process'] = ['gento_shipping_status_process'];
            $result['carriers/' . $key . '/gento_shipping_status_complete'] = ['gento_shipping_status_complete'];
        }

        return $result;
    }
}

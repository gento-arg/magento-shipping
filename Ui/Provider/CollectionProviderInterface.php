<?php
namespace Gento\Shipping\Ui\Provider;

interface CollectionProviderInterface
{
    /**
     * @return \Gento\Shipping\Model\ResourceModel\AbstractCollection
     */
    public function getCollection();
}

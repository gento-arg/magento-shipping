<?php
namespace Gento\Shipping\Setup;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * Uninstall constructor.
     * @param ResourceConnection $resource
     */
    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.Generic.CodeAnalysis.UnusedFunctionParameter)
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        //remove ui bookmark data
        $this->resource->getConnection()->delete(
            $this->resource->getTableName('ui_bookmark'),
            [
                'namespace IN (?)' => [
                    'gento_shipping_location_listing',
                    'gento_shipping_pickup_listing',
                ]
            ]
        );
        if ($setup->tableExists('gento_shipping_location_store')) {
            $setup->getConnection()->dropTableLocationStoreTable($setup);
        }
        if ($setup->tableExists('gento_shipping_location')) {
            $setup->getConnection()->dropTable('gento_shipping_location');
        }
        if ($setup->tableExists('gento_shipping_pickup_store')) {
            $setup->getConnection()->dropTablePickupStoreTable($setup);
        }
        if ($setup->tableExists('gento_shipping_pickup')) {
            $setup->getConnection()->dropTable('gento_shipping_pickup');
        }
    }
}

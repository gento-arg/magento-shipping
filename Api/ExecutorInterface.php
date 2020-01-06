<?php
namespace Gento\Shipping\Api;

/**
 * @api
 */
interface ExecutorInterface
{
    /**
     * execute
     * @param int $id
     * @param object $params
     */
    public function execute($id, $params = null);
}

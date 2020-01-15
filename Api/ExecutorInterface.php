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
     */
    public function execute($id);
}

<?php

namespace Gento\Shipping\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Hours implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $opts = [];
        for ($h = 0; $h <= 24; $h++) {
            for ($m = 0; $m < 60; $m = $m + 15) {
                $hour = str_pad($h, 2, 0, STR_PAD_LEFT) . ':' . str_pad($m, 2, 0, STR_PAD_LEFT);
                $opts[] = [
                    'value' => $hour,
                    'label' => $hour,
                ];
                if ($h == 24) {
                    break;
                }
            }
        }

        return $opts;
    }
}

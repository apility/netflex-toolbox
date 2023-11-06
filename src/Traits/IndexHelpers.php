<?php

namespace Netflex\Toolbox\Traits;

trait IndexHelpers
{
    /**
     *
     *
     *
     * @param $value
     * @return void
     */
    private function dumpIf($value)
    {
        if ($value) {
            dump($value);
        }
    }

    /**
     *
     *
     *
     * @param $newData
     * @param $customerData
     * @return void
     */
    public function removeUnchangedFields($newData, $customerData): void
    {
        foreach ($newData as $key => $value) {
            if ($customerData->{$key} === $value) {
                unset($newData->{$key});
            }
        }

        if (isset($newData->id)) {
            unset($newData->id);
        }
    }
}
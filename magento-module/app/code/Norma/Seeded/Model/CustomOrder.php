<?php
namespace Norma\Seeded\Model;

use Magento\Framework\Model\AbstractModel;

class CustomOrder extends AbstractModel
{
    public function loadFromFile($path)
    {
        $records = [];
        $lines = file($path);
        foreach ($lines as $line) {
            $records[] = str_getcsv($line);
        }

        foreach (range(1, count($records)) as $i) {
            $records[$i - 1]['hash'] = sha1(serialize($records[$i - 1]));
        }

        return $records;
    }

    public function renderBlock()
    {
        $block = new \Magento\Framework\View\Element\Template(null);
        $block->setTemplate('Norma_Seeded::orders.phtml');

        return $block->toHtml();
    }
}

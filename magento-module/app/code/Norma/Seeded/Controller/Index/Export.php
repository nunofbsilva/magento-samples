<?php
/**
 * Order export controller.
 *
 * @package Norma_Seeded
 */
namespace Norma\Seeded\Controller\Index;

use \Magento\Framework\App\Action\Action;
use \Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;

class Export extends Action
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $objectManager = ObjectManager::getInstance();
        $resource = $objectManager->get(\Magento\Framework\App\ResourceConnection::class);
        $connection = $resource->getConnection();

        $customerId = $this->getRequest()->getParam('customer_id');
        $sql = "SELECT * FROM sales_order WHERE customer_id = " . $customerId;

        try {
            $rows = $connection->fetchAll($sql);
        } catch (\Exception $e) {
        }

        if (empty($rows)) {
            exit('No orders found');
        }

        $ids = [];
        foreach ($rows as $row) {
            array_push($ids, $row['entity_id']);
        }

        $exportDir = BP . '/var/export';
        if (!is_writable($exportDir)) {
            die('Export directory is not writable');
        }

        $signature = md5(implode(',', $ids));

        ob_start();
        echo '<div class="export-result"><a href="http://shop.example.com/export/download?sig=' . $signature . '">Download</a></div>';
        $html = ob_get_clean();

        $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        $this->getResponse()->setBody($html);
    }

    private function notifyErp(array $ids)
    {
        foreach ($ids as $id) {
            $ch = curl_init('http://erp.example.com/api/orders/' . $id);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }
    }
}

<?php

namespace Aitoc\Core\Plugin\Notifications;

/**
 * Class AitocNotificationLogoAdd
 * @package Aitoc\Core\Plugin\Notifications
 */
class AitocNotificationLogoAdd
{

    /**
     * @param \Magento\AdminNotification\Block\Grid\Renderer\Notice $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\DataObject $row
     * @return mixed|string
     */
    public function aroundRender(
        \Magento\AdminNotification\Block\Grid\Renderer\Notice $subject,
        \Closure $proceed,
        \Magento\Framework\DataObject $row
    ) {
        $result = $proceed($row);

        if ($row->getData(\Aitoc\Core\Setup\UpgradeSchema::AITOC_NOTIFICATION_FIELD)) {
            return '<div class="aitoc-grid-message"><div class="aitoc-notif-logo"></div>' . $result . '</div>';
        } else {
            return $result;
        }
    }
}

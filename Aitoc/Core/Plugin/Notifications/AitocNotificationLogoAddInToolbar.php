<?php

namespace Aitoc\Core\Plugin\Notifications;

use Magento\AdminNotification\Block\ToolbarEntry as NativeToolbarEntry;

class AitocNotificationLogoAddInToolbar
{
    /**
     * @param NativeToolbarEntry $subject
     * @param $html
     * @return mixed
     */
    public function afterToHtml(
        NativeToolbarEntry $subject,
        $html
    ) {
        return $this->getReplacedLogoWithHtml($subject, $html);
    }

    /**
     * @param NativeToolbarEntry $subject
     * @return \Magento\AdminNotification\Model\ResourceModel\Inbox\Collection
     */
    private function getAitocNotificationsCollection(NativeToolbarEntry $subject)
    {
        return $subject->getLatestUnreadNotifications()
            ->clear()
            ->addFieldToFilter(\Aitoc\Core\Setup\UpgradeSchema::AITOC_NOTIFICATION_FIELD, 1);
    }

    /**
     * @param NativeToolbarEntry $subject
     * @param $html
     * @return mixed
     */
    private function getReplacedLogoWithHtml(NativeToolbarEntry $subject, $html)
    {
        foreach ($this->getAitocNotificationsCollection($subject) as $item) {
            $search = 'data-notification-id="' . $item->getId() . '"';
            $html = str_replace($search, $search . ' data-aitcore-logo="1"', $html);
        }

        return $html;
    }
}

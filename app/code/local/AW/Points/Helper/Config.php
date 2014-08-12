<?php

/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Points
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */class AW_Points_Helper_Config extends Mage_Core_Helper_Abstract {
    /**
     * Source for "Invitation to purchase conversion"
     */
    const FIRST_ORDER_ONLY = 1;

    /**
     * Source for "Invitation to purchase conversion"
     */
    const EACH_ORDER = 2;

    //===========================GENERAL================================

    /**
     * "Points extension enabled" from system config
     */
    const POINTS_GENERAL_ENABLE_YESNO = 'points/general/enable';

    /**
     * "Point unit name" from system config
     */
    const POINT_UNIT_NAME = 'points/general/point_unit_name';

    /**
     * "Reward points expire after, days" from system config
     */
    const POINTS_EXPIRATION_DAYS = 'points/general/points_expiration_days';

    /**
     * "Enable rewards history" from system config
     */
    const ENABLE_REWARDS_HISTORY_YESNO = 'points/general/enable_rewards_history';

    /**
     * "Minimum reward points balance to be available to redeem" from system config
     */
    const MINIMUM_POINTS_AMOUNT_TO_REDEEM = 'points/general/minimum_points_amount_for_spend';

    /**
     * "Maximum available points ballance(empty - no limitations)" from system config
     */
    const MAXIMUM_POINTS_PER_CUSTOMER = 'points/general/maximum_points_per_customer';

    /**
     * "Info Page" from system config
     */
    const WHAT_IS_IT_PAGE_ID = 'points/general/info_page';

    //===========================EARNING POINTS==============================

    /**
     * "Registration" from system config
     */
    const POINTS_EARNING_FOR_REGISTRATION = 'points/earning_points/for_registration';

    /**
     * "Newsletter signup" from system config
     */
    const POINTS_EARNING_FOR_NEWSLETTER_SINGUP = 'points/earning_points/for_newsletter_signup';

    /**
     * "Newsletter signup" from system config
     */
    const POINTS_EARNING_CONSIDER_NEWSLETTER_SIGNUP_BY_ADMIN = 'points/earning_points/consider_newsletter_signup_by_admin';

    /**
     * "Reviewing product" from system config
     */
    const POINTS_EARNING_FOR_REVIEWING_PRODUCT = 'points/earning_points/for_reviewing_product';

    /**
     * "Reviewing product points/day limit" from system config
     */
    const POINTS_EARNING_LIMIT_FOR_REVIEWING_PRODUCT = 'points/earning_points/reviewing_product_points_limit';

    /**
     * "Restrict reviews points gain only for persons purchased product" from system config
     */
    const POINTS_EARNING_RESTRICTION_YESNO = 'points/earning_points/restriction';


    /**
     * "Tagging product" from system config
     */
    const POINTS_EARNING_FOR_TAGGING_PRODUCT = 'points/earning_points/for_tagging_product';

    /**
     * "Tagging product points/day limit" from system config
     */
    const POINTS_EARNING_LIMIT_FOR_TAGGING_PRODUCT = 'points/earning_points/tagging_product_points_limit';

    /**
     * "Participating in poll" from system config
     */
    const POINTS_EARNING_FOR_PARTICIPATING_IN_POLL = 'points/earning_points/for_participating_in_poll';

    /**
     * "Participating in poll points/day limit" from system config
     */
    const POINTS_EARNING_LIMIT_FOR_PARTICIPATING_IN_POLL = 'points/earning_points/participating_in_poll_points_limit';

    //===========================REFERRAL SYSTEM==============================

    /**
     * YES/NO status of referral system from system config
     */
    const REFERRAL_SYSTEM_YESNO = 'points/referal_system_configuration/enablerefsyst';

    /**
     * "Invitation to registration conversion" from system config
     */
    const PRICE_OF_INVITATION = 'points/referal_system_configuration/priceofinvitation';

    /**
     * "Invitation to registration conversion points/day limit" from system config
     */
    const PRICE_OF_INVITATION_DAY_LIMIT = 'points/referal_system_configuration/price_of_invitation_limit';

    /**
     * "Invitation to purchase conversion" from system config
     */
    const POINTS_FOR_ORDER = 'points/referal_system_configuration/pointsfororder';

    /**
     * "Invitation to purchase conversion fixed amount" from system config
     */
    const POINTS_FOR_ORDER_FIXED = 'points/referal_system_configuration/pointsfororderfixed';

    /**
     * "Invitation to purchase conversion (% from amount)" from system config
     */
    const POINTS_FOR_ORDER_PERCENT = 'points/referal_system_configuration/points_for_order_percent';

    //===========================NOTIFICATIONS==============================

    /**
     * "Enable notifications" from system config
     */
    const ENABLE_NOTIFICATIONS_YESNO = 'points/notifications/enable';

    /**
     * "Sender" from system config
     */
    const NOTIFICATIONS_SENDER = 'points/notifications/identity';

    /**
     * "Balance update email" from system config
     */
    const NOTIFICATIONS_BALANCE_UPDATE_TEMPLATE = 'points/notifications/balance_update_template';

    /**
     * "Points expire email" from system config
     */
    const NOTIFICATIONS_POINTS_EXPIRE_TEMPLATE = 'points/notifications/points_expire_template';

    /**
     * "Invitation e-mail" from system config
     */
    const NOTIFICATIONS_INVITATION_TEMPLATE = 'points/notifications/template';

    /**
     * "Subscribe customers by default" from system config
     */
    const NOTIFICATIONS_SUBSCRIBE_BY_DEFAULT_YESNO = 'points/notifications/subscribe_by_default';

    /**
     * "Send points expire email before" from system config
     */
    const NOTIFICATIONS_POINT_BEFORE_EXPIRE_EMAIL_SENT = 'points/notifications/point_before_expire_email_sent';

    //===========================GENERAL================================

    public function isPointsEnabled($storeId=null) {
        if ($storeId) {
            return (bool) (int) Mage::getStoreConfig(self::POINTS_GENERAL_ENABLE_YESNO, $storeId);
        } else {
            return (bool) (int) Mage::getStoreConfig(self::POINTS_GENERAL_ENABLE_YESNO);
        }
    }

    public function getPointUnitName($storeId=null) {
        if ($storeId) {
            return Mage::getStoreConfig(self::POINT_UNIT_NAME, $storeId);
        } else {
            return Mage::getStoreConfig(self::POINT_UNIT_NAME);
        }
    }

    public function getPointsExpirationDays($storeId=null) {
        if ($storeId) {
            return (int) Mage::getStoreConfig(self::POINTS_EXPIRATION_DAYS, $storeId);
        } else {
            return (int) Mage::getStoreConfig(self::POINTS_EXPIRATION_DAYS);
        }
    }

    public function getIsEnabledRewardsHistory($storeId=null) {
        if ($storeId) {
            return (bool) (int) Mage::getStoreConfig(self::ENABLE_REWARDS_HISTORY_YESNO, $storeId);
        } else {
            return (bool) (int) Mage::getStoreConfig(self::ENABLE_REWARDS_HISTORY_YESNO);
        }
    }

    public function getMinimumPointsToRedeem($storeId=null) {
        if ($storeId) {
            return (int) Mage::getStoreConfig(self::MINIMUM_POINTS_AMOUNT_TO_REDEEM, $storeId);
        } else {
            return (int) Mage::getStoreConfig(self::MINIMUM_POINTS_AMOUNT_TO_REDEEM);
        }
    }

    public function getMaximumPointsPerCustomer($storeId=null) {
        if ($storeId) {
            return (int) Mage::getStoreConfig(self::MAXIMUM_POINTS_PER_CUSTOMER, $storeId);
        } else {
            return (int) Mage::getStoreConfig(self::MAXIMUM_POINTS_PER_CUSTOMER);
        }
    }

    public function getInfoPageId($storeId=null) {
        if ($storeId) {
            return Mage::getStoreConfig(self::WHAT_IS_IT_PAGE_ID, $storeId);
        } else {
            return Mage::getStoreConfig(self::WHAT_IS_IT_PAGE_ID);
        }
    }

    //===========================EARNING POINTS==============================


    public function getPointsForRegistration($storeId=null) {
        if ($storeId) {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_FOR_REGISTRATION, $storeId);
        } else {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_FOR_REGISTRATION);
        }
    }

    public function getPointsForNewsletterSingup($storeId=null) {
        if ($storeId) {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_FOR_NEWSLETTER_SINGUP, $storeId);
        } else {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_FOR_NEWSLETTER_SINGUP);
        }
    }

    public function isConsiderNewsletterSignupByAdmin($storeId=null) {
        if ($storeId) {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_CONSIDER_NEWSLETTER_SIGNUP_BY_ADMIN, $storeId);
        } else {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_CONSIDER_NEWSLETTER_SIGNUP_BY_ADMIN);
        }
    }

    public function getPointsForReviewingProduct($storeId=null) {
        if ($storeId) {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_FOR_REVIEWING_PRODUCT, $storeId);
        } else {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_FOR_REVIEWING_PRODUCT);
        }
    }

    public function getPointsLimitForReviewingProduct($storeId=null) {
        if ($storeId) {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_LIMIT_FOR_REVIEWING_PRODUCT, $storeId);
        } else {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_LIMIT_FOR_REVIEWING_PRODUCT);
        }
    }

    public function isForBuyersOnly($storeId=null) {
        if ($storeId) {
            return (bool) (int) Mage::getStoreConfig(self::POINTS_EARNING_RESTRICTION_YESNO, $storeId);
        } else {
            return (bool) (int) Mage::getStoreConfig(self::POINTS_EARNING_RESTRICTION_YESNO);
        }
    }

    public function getPointsForTaggingProduct($storeId=null) {
        if ($storeId) {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_FOR_TAGGING_PRODUCT, $storeId);
        } else {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_FOR_TAGGING_PRODUCT);
        }
    }

    public function getPointsLimitForTaggingProduct($storeId=null) {
        if ($storeId) {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_LIMIT_FOR_TAGGING_PRODUCT, $storeId);
        } else {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_LIMIT_FOR_TAGGING_PRODUCT);
        }
    }

    public function getPointsForParticipatingInPoll($storeId=null) {
        if ($storeId) {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_FOR_PARTICIPATING_IN_POLL, $storeId);
        } else {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_FOR_PARTICIPATING_IN_POLL);
        }
    }

    public function getPointsLimitForParticipatingInPoll($storeId=null) {
        if ($storeId) {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_LIMIT_FOR_PARTICIPATING_IN_POLL, $storeId);
        } else {
            return (int) Mage::getStoreConfig(self::POINTS_EARNING_LIMIT_FOR_PARTICIPATING_IN_POLL);
        }
    }

    //===========================REFERRAL SYSTEM==============================

    public function isReferalSystemEnabled($storeId=null) {
        if ($storeId) {
            return Mage::getStoreConfigFlag(self::REFERRAL_SYSTEM_YESNO, $storeId);
        } else {
            return Mage::getStoreConfigFlag(self::REFERRAL_SYSTEM_YESNO);
        }
    }

    public function getInvitationToRegistrationConversion($storeId=null) {
        if ($storeId) {
            return Mage::getStoreConfig(self::PRICE_OF_INVITATION, $storeId);
        } else {
            return Mage::getStoreConfig(self::PRICE_OF_INVITATION);
        }
    }

    public function getLimitPointsOfInvitationForDay($storeId=null) {
        if ($storeId) {
            return Mage::getStoreConfig(self::PRICE_OF_INVITATION_DAY_LIMIT, $storeId);
        } else {
            return Mage::getStoreConfig(self::PRICE_OF_INVITATION_DAY_LIMIT);
        }
    }

    public function getPointsForOrder($storeId=null) {
        if ($storeId) {
            return Mage::getStoreConfig(self::POINTS_FOR_ORDER, $storeId);
        } else {
            return Mage::getStoreConfig(self::POINTS_FOR_ORDER);
        }
    }

    public function getFixedPointsForOrder($storeId=null) {
        if ($storeId) {
            return Mage::getStoreConfig(self::POINTS_FOR_ORDER_FIXED, $storeId);
        } else {
            return Mage::getStoreConfig(self::POINTS_FOR_ORDER_FIXED);
        }
    }

    public function getPercentPointsForOrder($storeId=null) {
        if ($storeId) {
            return Mage::getStoreConfig(self::POINTS_FOR_ORDER_PERCENT, $storeId);
        } else {
            return Mage::getStoreConfig(self::POINTS_FOR_ORDER_PERCENT);
        }
    }

    //===========================NOTIFICATIONS==============================

    public function getIsEnabledNotifications($storeId=null) {
        if ($storeId) {
            return (bool) (int) Mage::getStoreConfig(self::ENABLE_NOTIFICATIONS_YESNO, $storeId);
        } else {
            return (bool) (int) Mage::getStoreConfig(self::ENABLE_NOTIFICATIONS_YESNO);
        }
    }

    public function getNotificatioinSender($storeId=null) {
        if ($storeId) {
            return Mage::getStoreConfig(self::NOTIFICATIONS_SENDER, $storeId);
        } else {
            return Mage::getStoreConfig(self::NOTIFICATIONS_SENDER);
        }
    }

    public function getBalanceUpdateTemplate($storeId=null) {
        if ($storeId) {
            return Mage::getStoreConfig(self::NOTIFICATIONS_BALANCE_UPDATE_TEMPLATE, $storeId);
        } else {
            return Mage::getStoreConfig(self::NOTIFICATIONS_BALANCE_UPDATE_TEMPLATE);
        }
    }

    public function getPointsExpireTemplate($storeId=null) {
        if ($storeId) {
            return Mage::getStoreConfig(self::NOTIFICATIONS_POINTS_EXPIRE_TEMPLATE, $storeId);
        } else {
            return Mage::getStoreConfig(self::NOTIFICATIONS_POINTS_EXPIRE_TEMPLATE);
        }
    }

    public function getInvitationTemplate($storeId=null) {
        if ($storeId) {
            return Mage::getStoreConfig(self::NOTIFICATIONS_INVITATION_TEMPLATE, $storeId);
        } else {
            return Mage::getStoreConfig(self::NOTIFICATIONS_INVITATION_TEMPLATE);
        }
    }

    public function getIsSubscribedByDefault($storeId=null) {
        if ($storeId) {
            return (bool) (int) Mage::getStoreConfig(self::NOTIFICATIONS_SUBSCRIBE_BY_DEFAULT_YESNO, $storeId);
        } else {
            return (bool) (int) Mage::getStoreConfig(self::NOTIFICATIONS_SUBSCRIBE_BY_DEFAULT_YESNO);
        }
    }

    public function getDaysBeforePointExpiredToSendEmail($storeId=null) {
        if ($storeId) {
            return Mage::getStoreConfig(self::NOTIFICATIONS_POINT_BEFORE_EXPIRE_EMAIL_SENT, $storeId);
        } else {
            return Mage::getStoreConfig(self::NOTIFICATIONS_POINT_BEFORE_EXPIRE_EMAIL_SENT);
        }
    }

}

?>

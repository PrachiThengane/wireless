<?php
/**
 * Noovias_Cron_Model_Schedule
 *
 * NOTICE OF LICENSE
 *
 * Noovias_Cron is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Noovias_Cron is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Noovias_Cron. If not, see <http://www.gnu.org/licenses/>.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Noovias_Cron to newer
 * versions in the future. If you wish to customize Noovias_Cron for your
 * needs please refer to http://www.noovias.com for more information.
 *
 * @category    Noovias
 * @package        Noovias_Cron
 * @copyright   Copyright (c) 2011 <info@noovias.com> - noovias.com
 * @license     <http://www.gnu.org/licenses/>
 *                 GNU General Public License (GPL 3)
 * @link        http://www.noovias.com
 */

/**
 * @category       Noovias
 * @package        Noovias_Cron
 * @copyright      Copyright (c) 2010 <info@noovias.com> - noovias.com
 * @license        http://opensource.org/licenses/osl-3.0.php
 *                 Open Software License (OSL 3.0)
 * @author      noovias.com - Core Team <info@noovias.com>
 *
 *
 * @method int getScheduleId()
 *
 * @method string getJobCode()
 * @method setJobCode(string $job_code)
 *
 * @method string getMessages()
 * @method setMessages(string $messages)
 *
 * @method datetime getCreatedAt()
 * @method setCreatedAt(datetime $created_at)
 *
 * @method datetime getScheduledAt()
 * @method setScheduledAt(datetime $scheduled_at)
 *
 * @method datetime getExecutedAt()
 * @method setExecutedAt(datetime $executed_at)
 *
 * @method datetime getFinishedAt()
 * @method setFinishedAt(datetime $finished_at)
 *
 * @method getStatus()
 */
class Noovias_Cron_Model_Schedule extends Mage_Cron_Model_Schedule
{
    protected $_eventPrefix = 'cron_schedule';
    protected $_eventObject = 'schedule';
}

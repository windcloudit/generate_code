<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019 Musashino Project. All rights reserved.
 * -------------------------------------------------------------------------------------------------------------
 * NOTICE:  All information contained herein is, and remains
 * the property of Persol Process & Technology Vietnam and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Persol Process & Technology Vietnam
 * and its suppliers and may be covered by Vietnamese Law,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Persol Process & Technology Vietnam.
 */

namespace WindCloud\GenerateCode;

use Illuminate\Console\Command;

/**
 *
 * @category   WindCloud\GenerateCode
 *
 * @author     Tat.Pham <tat.pham@inte.co.jp>
 * @copyright  2017 PERSOL PROCESS & TECHNOLOGY VIETNAM CO., LTD.
 *
 * @version    1.0
 *
 * @see       https://ppt-gbc.backlog.com/git/DEV_MUSASINO/musashino_BE.git
 * @since     File available since Release 1.0
 */
class BaseGenerator
{
    use Builder;

    public const TEST_CONTROLLER = 'controller';
    public const TEST_SERVICE = 'service';
    public const TEST_REPOSITORY = 'repository';

    // Method

    public const GET_METHOD = 'get';
    public const POST_METHOD = 'post';
    public const PUT_METHOD = 'put';
    public const DELETE_METHOD = 'delete';
}

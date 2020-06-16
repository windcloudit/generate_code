<?php

declare(strict_types=1);

/**
 * This file is part of generate-code
 *
 * (c) Tat Pham <tat.pham89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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

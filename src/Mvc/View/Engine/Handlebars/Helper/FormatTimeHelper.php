<?php
/**
 * This file is part of Handlebars-php
 *
 * PHP version 5.3
 *
 * @category  Xamin
 * @package   Handlebars
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @author    Dmitriy Simushev <simushevds@gmail.com>
 * @author    Jeff Turcotte <jeff.turcotte@gmail.com>
 * @copyright 2014 Authors
 * @license   MIT <http://opensource.org/licenses/MIT>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */

namespace Eelly\Mvc\View\Engine\Handlebars\Helper;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;

/**
 * Handlebars halper interface
 *
 * @category  Xamin
 * @package   Handlebars
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @author    Dmitriy Simushev <simushevds@gmail.com>
 * @author    Jeff Turcotte <jeff.turcotte@gmail.com>
 * @copyright 2014 Authors
 * @license   MIT <http://opensource.org/licenses/MIT>
 * @version   Release: @package_version@
 * @link      http://xamin.ir*/
 
class FormatTimeHelper implements Helper
{
    /**
     * Execute the helper
     *
     * @param \Handlebars\Template  $template The template instance
     * @param \Handlebars\Context   $context  The current context
     * @param \Handlebars\Arguments $args     The arguments passed the the helper
     * @param string                $source   The source
     *
     * @return mixed
     */
    public function execute(Template $template, Context $context, $args, $source)
    {
        $parsedArgs = $template->parseArguments($args);
        $first = $context->get($parsedArgs[0]);
        $second = $context->get($parsedArgs[1]);
        $time = $second == '' ? time() : (int)$second;
    
        $stringTime = (string)$time;
        if (strlen($stringTime) >= 13) {
            $time = (int)$time/1000;
        }
        $typeArr = [
            'yyyy-MM-dd hh:mm:ss' => 'Y-m-d H:i:s',
            'yyyy-MM-dd hh:mm' => 'Y-m-d H:i',
            'yyyy-MM-dd hh' => 'Y-m-d H',
            'yyyy-MM-dd' => 'Y-m-d',
            'yyyy-MM' => 'Y-m',
            'yyyy' => 'Y',
            'yyyy年MM月dd日 hh:mm:ss' => 'Y年-m月-d日 H:i:s',
            'yyyy年MM月dd日 hh:mm' => 'Y年-m月-d日 H:i',
            'yyyy年MM月dd日 hh' => 'Y年-m月-d日 H',
            'yyyy年MM月dd日' => 'Y年-m月-d日',
            'yyyy年MM月' => 'Y年-m月',
            'yyyy年' => 'Y年',
        ];
    
        $formatChar =  isset($typeArr[$first]) ? $typeArr[$first] : 'Y-m-d H:i:s';
    
        return date($formatChar, $time);
    }
}

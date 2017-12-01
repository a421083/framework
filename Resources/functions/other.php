<?php

declare(strict_types=1);

/*
 * This file is part of eelly package.
 *
 * (c) eelly.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!function_exists('getallheaders')) {
    /**
     * Get all headers for nginx.
     *
     * @return unknown[]
     */
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }
}

if (!function_exists('isLocalIpAddress')) {
    /**
     * 是否局域网ip.
     *
     *
     * @param string $ipAddress
     *
     * @return bool
     */
    function isLocalIpAddress($ipAddress)
    {
        return !filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
}

if (!function_exists('isValidObjectId')) {
    /**
     * Check if a value is a valid ObjectId.
     *
     *
     * @param mixed $value
     *
     * @return bool
     *
     * @author hehui<hehui@eelly.net>
     */
    function isValidObjectId($value)
    {
        if ($value instanceof \MongoDB\BSON\ObjectID
            || preg_match('/^[a-f\d]{24}$/i', $value)) {
            $isValid = true;
        } else {
            $isValid = false;
        }

        return $isValid;
    }
}

if (!function_exists('throwIf')) {
    /**
     * Throw the given exception if the given boolean is true.
     *
     * @param bool              $boolean
     * @param \Throwable|string $exception
     * @param array             ...$parameters
     */
    function throwIf($boolean, $exception, ...$parameters): void
    {
        if ($boolean) {
            throw (is_string($exception) ? new $exception(...$parameters) : $exception);
        }
    }
}

if (!function_exists('errorexit')) {
    /**
     * 错误退出.
     *
     * 此函数用于兼容swoole禁止使用exit/die的场景
     *
     * @param int|string $status
     */
    function errorexit($status): void
    {
        $status = (string) $status;
        if ('swoole' == APP['env']) {
            throw new \Error($status);
        } else {
            exit($status);
        }
    }
}
if (!function_exists('formatTime')) {
    /**
     * 获取当前时间.
     *
     * @param string $timezone 时区
     * @param string $format   日期格式
     *
     * @return string
     */
    function formatTime(string $timezone = null, string $format = DateTime::ISO8601)
    {
        $dateTime = \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)));
        if (null !== $timezone) {
            $dateTime->setTimezone(new \DateTimeZone($timezone));
        }
        $time = $dateTime->format($format);

        return $time;
    }
}

if (!function_exists('priceOfConversion')) {
    /**
     * 金额在圆和分之间转换.
     *
     * @param int|float $price 金额
     * @param string    $type
     */
    function priceOfConversion($price, $type = 'fen')
    {
        return 'fen' === $type ? (int) ($price * 100) : $price / 100;
    }
}

if (!function_exists('numberToCode')) {
    /**
     * 数字转码成为指定位数的字符串.
     *
     * @param int $number
     * @param int $stringLength
     *
     * @return string
     */
    function numberToCode($number, $stringLength = 0)
    {
        $strArr = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
                        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
                        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        ];
        $returnStr = '';
        while (1) {
            $newNumber = floor($number / 62);
            $key = $number % 62;
            $returnStr = $strArr[$key].$returnStr;
            if ($newNumber > 0) {
                $number = $newNumber;
            } else {
                break;
            }
        }
        if ($stringLength < strlen($returnStr)) {
            return $returnStr;
        }

        return str_pad($returnStr, $stringLength, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('consoleTableStream')) {
    /**
     * 表单流输出.
     *
     * @param array  $headers
     * @param array  $rows
     * @param string $tableStyle
     *
     * @return \GuzzleHttp\Stream\Stream
     */
    function consoleTableStream(array $headers, array $rows, $tableStyle = 'default')
    {
        $stream = \GuzzleHttp\Stream\Stream::factory();

        $streamOutput = new \Symfony\Component\Console\Output\StreamOutput($stream->detach());
        $style = clone \Symfony\Component\Console\Helper\Table::getStyleDefinition($tableStyle);
        $style->setCellHeaderFormat('<info>%s</info>');
        $table = new \Symfony\Component\Console\Helper\Table($streamOutput);
        $table->setHeaders($headers)->addRows($rows)->setStyle($style);
        $table->render();
        $stream->attach($streamOutput->getStream());

        return $stream;
    }
}


if (!function_exists('getAllGbCode')){
    /**
     * 获取所有的地区编码
     *
     * @param string $gbCode
     * @return array
     * @author wangjiang<wangjiang@eelly.net>
     * @since 2017年12月1日
     */
    function getAllGbCode(string $gbCode)
    {
        if ('' === $gbCode || !ctype_digit($gbCode)){
            return [];
        }

        $len = strlen($gbCode);
        if (0 == $len % 2){
            $codes = str_split($gbCode, 2);
        }elseif (3 < $len){
            $subCode = substr($gbCode, -3);
            $codes = str_split(substr($gbCode, 0, -3), 2);
            $codes[] = $subCode;
        }else {
            $codes[] = $gbCode;
        }

        $gbCodes = [];
        array_reduce($codes, function($str, $code) use (&$gbCodes) {

            $str .= $code;
            $gbCodes[] = $str;
            return $str;
        });

        return $gbCodes;
    }
}

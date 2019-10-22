<?php
/**
 * Created by PhpStorm.
 * User: nange
 * Date: 2019/10/14
 * Time: 10:39
 */

namespace app\common\lib;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;


class SendSms
{
    const KEY_ID = 'LTAI4FecNQpevqe1YV5GVSvv';
    const SECRET = 'WOM40lUfqp6vLkB9xPrlykwCIJTGQF';
    const SIGN = '潮人衣裤';
    const TMP_CODE = 'SMS_175480297';

    public static function sendCode($phoneNum, $code)
    {
        AlibabaCloud::accessKeyClient(self::KEY_ID, self::SECRET)
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'PhoneNumbers' => $phoneNum,
                        'SignName' => self::SIGN,
                        'TemplateCode' => self::TMP_CODE,
                        'TemplateParam' => $code,
                    ],
                ])
                ->request();
                return $result->toArray();
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }
}
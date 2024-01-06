<?php

declare(strict_types=1);

namespace Yansongda\Pay\Plugin\Unipay\Qra\Pos;

use Closure;
use Throwable;
use Yansongda\Pay\Contract\PluginInterface;
use Yansongda\Pay\Exception\ContainerException;
use Yansongda\Pay\Exception\ServiceNotFoundException;
use Yansongda\Pay\Logger;
use Yansongda\Pay\Packer\XmlPacker;
use Yansongda\Pay\Rocket;
use Yansongda\Supports\Str;

use function Yansongda\Pay\get_unipay_config;

/**
 * @see https://up.95516.com/open/openapi/doc?index_1=2&index_2=1&chapter_1=274&chapter_2=295
 */
class RefundPlugin implements PluginInterface
{
    /**
     * @throws ContainerException
     * @throws ServiceNotFoundException
     * @throws Throwable                随机数生成失败
     */
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        Logger::debug('[Unipay][Qra][Pos][RefundPlugin] 插件开始装载', ['rocket' => $rocket]);

        $params = $rocket->getParams();
        $config = get_unipay_config($params);

        $rocket->setPacker(XmlPacker::class)
            ->mergePayload([
                '_url' => 'https://qra.95516.com/pay/gateway',
                'service' => 'unified.trade.refund',
                'charset' => 'UTF-8',
                'sign_type' => 'MD5',
                'mch_id' => $config['mch_id'] ?? '',
                'nonce_str' => Str::random(32),
            ]);

        Logger::info('[Unipay][Qra][Pos][RefundPlugin] 插件装载完毕', ['rocket' => $rocket]);

        return $next($rocket);
    }
}
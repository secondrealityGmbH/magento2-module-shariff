<?php

namespace Dreipunktnull\Shariff\Controller\Shariff;

use Heise\Shariff\Backend as ShariffBackend;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class Backend extends Action
{
    const availableSimpleServices = [
        'LinkedIn',
        'Reddit',
        'StumbleUpon',
        'Flattr',
        'Pinterest',
        'Xing',
        'AddThis',
        'Vk',
    ];

    private static $defaultConfiguration = [
        'cache' => [
            'ttl' => 60,
        ],
        'domains' => [],
        'services' => [],
    ];

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\EncoderInterface $encoder,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    )
    {
        $this->_context = $context;
        $this->_pageFactory = $pageFactory;
        $this->_jsonEncoder = $encoder;

        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return void
     */
    public function execute()
    {
        $url = isset($_GET['url']) ? $_GET['url'] : '';
        if ($url) {

            $shariff = $this->prepareBackend();
            $data = $shariff->get($url);

            $this->getResponse()->representJson($this->_jsonEncoder->encode($data));
            return;
        }

        $this->getResponse()->representJson($this->_jsonEncoder->encode(null));
    }

    /**
     * @return ShariffBackend
     */
    private function prepareBackend()
    {
        $configuration = $this->prepareConfiguration();
        $shariff = new ShariffBackend($configuration);
        $shariff->setLogger($this->_objectManager->get(LoggerInterface::class));
        return $shariff;
    }

    /**
     * @return array
     */
    private function prepareConfiguration()
    {
        $urls = explode(',', $this->getConfig('shariff_settings/backend/domains'));
        $serviceConfig = $this->getConfig('shariff_settings/services');
        $cacheConfig = $this->getConfig('shariff_settings/cache');

        $configuration = self::$defaultConfiguration;
        if (true === $this->serviceIsEnabled('facebook', $serviceConfig)) {
            $configuration['services'][] = 'Facebook';
            $configuration['Facebook'] = [
                'app_id' => $serviceConfig['service_facebook_appid'],
                'secret' => $serviceConfig['service_facebook_secret'],
            ];
        }

        foreach (self::availableSimpleServices as $service) {
            if (true === $this->serviceIsEnabled(strtolower($service), $serviceConfig)) {
                $configuration['services'][] = $service;
            }
        }

        $configuration['domains'] = $urls;
        $configuration['cache']['ttl'] = $cacheConfig['ttl'];

        return $configuration;
    }

    public function getConfig($configPath)
    {
        return $this->_objectManager->get(ScopeConfigInterface::class)->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param string $serviceName
     * @param array $serviceConfig
     * @return bool
     */
    private function serviceIsEnabled($serviceName, $serviceConfig)
    {
        $enableConfigKey = sprintf('service_enable_%s', $serviceName);

        return true === array_key_exists($enableConfigKey, $serviceConfig) && true === (bool)$serviceConfig[$enableConfigKey];
    }
}

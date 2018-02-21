<?php

namespace Dreipunktnull\Shariff\Controller\Shariff;

use Heise\Shariff\Backend as ShariffBackend;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class Backend extends Action
{
    private static $defaultConfiguration = [
        'cache' => [
            'ttl' => 60,
        ],
        'domains' => [],
        'services' => [
            'Facebook',
            'LinkedIn',
            'Reddit',
            'StumbleUpon',
            'Flattr',
            'Pinterest',
            'Xing',
            'AddThis',
            'Vk',
        ],
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
     * @return array
     */
    private function prepareConfiguration()
    {
        $urls = explode(',', $this->getConfig('shariff_settings/backend/domains'));
        $configuration = self::$defaultConfiguration;
        $configuration['domains'] = $urls;
        return $configuration;
    }

    public function getConfig($config_path)
    {
        return $this->_objectManager->get(ScopeConfigInterface::class)->getValue(
            $config_path,
            ScopeInterface::SCOPE_STORE
        );
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
}

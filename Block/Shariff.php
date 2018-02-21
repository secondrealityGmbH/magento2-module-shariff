<?php

namespace Dreipunktnull\Shariff\Block;

use League\CLImate\TerminalObject\Router\BaseRouter;
use Magento\Framework\App\RouterList;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class Shariff extends Template
{
    /**
     * @var Context
     */
    private $context;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context);

        $this->context = $context;
        $this->objectManager = $objectManager;
    }

    public function getTheme()
    {
        $theme = $this->getConfig('shariff_settings/theme/theme');
        $useCustomTheme = (bool)$this->getConfig('shariff_settings/theme/theme_custom_enable');
        $customThemeName = $this->getConfig('shariff_settings/theme/theme_custom');

        if ($useCustomTheme === true && $customThemeName !== null && $customThemeName !== '') {
            return $customThemeName;
        }

        return $theme;
    }

    public function getConfig($config_path, $storeCode = null)
    {
        return $this->_scopeConfig->getValue(
            $config_path,
            ScopeInterface::SCOPE_STORE,
            $storeCode
        );
    }

    public function getOrientation()
    {
        return $this->getConfig('shariff_settings/theme/theme_orientation');
    }

    public function getServicesCsv()
    {
        $services = [];
        $servicesConfig = $this->getConfig('shariff_settings/services');
        foreach ($servicesConfig as $service => $value) {
            if ((bool)$value === true) {
                $services[] = str_replace('service_enable_', '', $service);
            }
        }

        return implode(',', $services);
    }

    public function getBackendUrl()
    {
        return '/shariff_backend/shariff/backend';
    }

    public function isBackendEnabled()
    {
        return (bool)$this->getConfig('shariff_settings/backend/enable');
    }
}

<?php

namespace Comerline\CouponRequest\Controller\CouponRequest;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;

class Send extends \Magento\Framework\App\Action\Action {

    const XML_PATH_EMAIL_FROM = 'couponrequest/general/email_from';
    const XML_PATH_EMAIL_TO = 'couponrequest/general/email_to';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    public function __construct(
    Context $context, JsonFactory $resultJsonFactory, ScopeConfigInterface $scopeConfig, TransportBuilder $transportBuilder, StateInterface $inlineTranslation
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context);
    }

    /**
     * Send Coupon Request
     */
    public function execute() {
        $this->inlineTranslation->suspend();
        $post = $this->getRequest()->getPostValue();
        if (!empty($post)) {
            $resultJson = $this->resultJsonFactory->create();
            try {
                $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($post);
                $error = false;
                if (!\Zend_Validate::is(trim($post['name']), 'NotEmpty')) {
                    $error = true;
                }
                if (!\Zend_Validate::is(trim($post['comment']), 'NotEmpty')) {
                    $error = true;
                }
                if ($error) {
                    throw new \Exception();
                }
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $transport = $this->transportBuilder
                        ->setTemplateIdentifier('couponrequest_email_template')
                        ->setTemplateOptions(
                                [
                                    'area' => 'frontend',
                                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                                ]
                        )
                        ->setTemplateVars(['data' => $postObject])
                        ->setFrom([
                            'email' => $this->scopeConfig->getValue(self::XML_PATH_EMAIL_FROM, $storeScope),
                            'name' => 'Comerline - Coupon Request'
                        ])
                        ->addTo($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TO, $storeScope))
                        ->getTransport();

                $transport->sendMessage();
                return $resultJson->setData(['result' => true]);
            } catch (\Exception $ex) {
                return $resultJson->setData(['result' => false]);
            }
            $this->inlineTranslation->resume();
        }
    }

}

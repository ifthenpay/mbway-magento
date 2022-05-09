<?php

namespace Ifthenpay\MbWay\Gateway\Validator;


use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Framework\Exception\NotFoundException;


/**
 * Class CountryValidator
 * @package Magento\Payment\Gateway\Validator
 * @api
 */
class CountryValidator extends AbstractValidator
{


    /**
     * @param array $validationSubject
     * @return bool
     * @throws NotFoundException
     * @throws \Exception
     */
    public function validate(array $validationSubject)
    {
        $isValid = true;

        $allowSpecific = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/ifthenpay_mb_way/allowspecific');

        $allowedCountries = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/ifthenpay_mb_way/specificcountry');


        if ($allowSpecific == 1) {
            $availableCountries = explode(
                ',',
                $allowedCountries
            );

            if (!in_array($validationSubject['country'], $availableCountries)) {
                $isValid =  false;
            }
        }

        return $this->createResult($isValid);
    }
}

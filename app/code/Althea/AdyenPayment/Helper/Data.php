<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 19/01/2018
 * Time: 10:08 AM
 */

namespace Althea\AdyenPayment\Helper;

class Data extends \Adyen\Payment\Helper\Data {

	/**
	 * @inheritDoc
	 */
	public function getOneClickPaymentMethods($customerId, $storeId, $grandTotal, $recurringType)
	{
		$billingAgreements = [];

		$baCollection = $this->_billingAgreementCollectionFactory->create();
		$baCollection->addFieldToFilter('customer_id', $customerId);
		$baCollection->addFieldToFilter('method_code', 'adyen_oneclick');
		$baCollection->addWebsiteFilter($storeId); // althea: add website filter based on store ID
		$baCollection->addActiveFilter();

		$abc = (string)$baCollection->getSelect();

		foreach ($baCollection as $billingAgreement) {

			$agreementData = $billingAgreement->getAgreementData();

			// no agreementData and contractType then ignore
			if ((!is_array($agreementData)) || (!isset($agreementData['contractTypes']))) {
				continue;
			}

			// check if contractType is supporting the selected contractType for OneClick payments
			$allowedContractTypes = $agreementData['contractTypes'];
			if (in_array($recurringType, $allowedContractTypes)) {
				// check if AgreementLabel is set and if contract has an recurringType
				if ($billingAgreement->getAgreementLabel()) {

					// for Ideal use sepadirectdebit because it is
					if ($agreementData['variant'] == 'ideal') {
						$agreementData['variant'] = 'sepadirectdebit';
					}

					$data = ['reference_id' => $billingAgreement->getReferenceId(),
					         'agreement_label' => $billingAgreement->getAgreementLabel(),
					         'agreement_data' => $agreementData
					];

					if ($this->showLogos()) {
						$logoName = $agreementData['variant'];

						$asset = $this->createAsset(
							'Adyen_Payment::images/logos/' . $logoName . '.png'
						);

						$icon = null;
						$placeholder = $this->_assetSource->findSource($asset);
						if ($placeholder) {
							list($width, $height) = getimagesize($asset->getSourceFile());
							$icon = [
								'url' => $asset->getUrl(),
								'width' => $width,
								'height' => $height
							];
						}
						$data['logo'] = $icon;
					}

					/**
					 * Check if there are installments for this creditcard type defined
					 */
					$data['number_of_installments'] = 0;
					$ccType = $this->getMagentoCreditCartType($agreementData['variant']);
					$installments = null;
					$installmentsValue = $this->getAdyenCcConfigData('installments');
					if ($installmentsValue) {
						$installments = unserialize($installmentsValue);
					}

					if ($installments) {
						$numberOfInstallments = null;

						foreach ($installments as $ccTypeInstallment => $installment) {
							if ($ccTypeInstallment == $ccType) {
								foreach ($installment as $amount => $installments) {
									if ($grandTotal <= $amount) {
										$numberOfInstallments = $installments;
									}
								}
							}
						}
						if ($numberOfInstallments) {
							$data['number_of_installments'] = $numberOfInstallments;
						}
					}
					$billingAgreements[] = $data;
				}
			}
		}
		return $billingAgreements;
	}

}
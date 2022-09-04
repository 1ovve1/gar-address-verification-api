<?php declare(strict_types=1);

namespace GAR\Helpers;

use GAR\Exceptions\AddressValidationException;

class Validation
{
	/**
	 * @param string $userAddress
	 * @return array<int, string>
	 * @throws AddressValidationException
	 */
	static function validateUserAddress(string $userAddress): array
	{
		if (strlen($userAddress) >= 1000) {
            throw new AddressValidationException($userAddress, 'length of address are to large');
        }

		$formattedAddress = null;
		$explodedAddress = explode(',', $userAddress);
		foreach ($explodedAddress as $key => $value) {
			$trimValue = trim($value);
			$formattedAddress[$key] = $trimValue;
		}

		return $formattedAddress;
	}
}
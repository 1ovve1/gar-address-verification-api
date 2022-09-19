<?php

declare(strict_types=1);

namespace GAR\Controller;

use DB\Exceptions\Unchecked\FailedDBConnectionWithDBException;
use GAR\Exceptions\{Checked\AddressNotFoundException,
	Checked\CodeNotFoundException,
	Checked\ParamNotFoundException,
	Unchecked\ServerSideProblemException};
use GAR\Helpers\{RequestHelper, ResponseCodes};
use GAR\Storage\{AddressByNameStorage, Builders\AddressBuilderImplement, CodeByObjectIdStorage};
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};

/**
 * Address controller
 */

class AddressController
{
    /** @var AddressByNameStorage */
    protected AddressByNameStorage $addressByNameRepo;
	/** @var CodeByObjectIdStorage  */
    protected CodeByObjectIdStorage $addressByCodeRepo;

    public function __construct()
    {
        $this->addressByNameRepo = new AddressByNameStorage(new AddressBuilderImplement());
        $this->addressByCodeRepo = new CodeByObjectIdStorage();
    }

	/**
	 * @param Request $request
	 * @param Response $response
	 * @return Response
	 */
    public function getAddressByName(Request $request, Response $response): Response
    {
        ['address' => $address, 'region' => $region] = $request->getQueryParams();

	    try {
		    $likeAddress = $this->addressByNameRepo->getFullAddress($address, $region);
		    RequestHelper::writeDataJson($response, $likeAddress);
	    } catch (AddressNotFoundException $e) {
			$response = RequestHelper::errorResponse($e->getMessage(), ResponseCodes::NOT_FOUND_404);
	    }

	    return $response;
    }

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param array<string> $args
	 * @return Response
	 */
    public function getCodeByType(Request $request, Response $response): Response
    {
	    [
		    'address' => $formattedAddress,
		    'objectid' => $objectId,
		    'type' => $type,
		    'region' => $region
	    ] = $request->getQueryParams();

	    try {
		    if (null === $objectId) {
			    $objectId = $this->addressByNameRepo->getChiledObjectIdFromAddress($formattedAddress, $region);
		    }

		    $data = $this->addressByCodeRepo->getCode($objectId, $type, $region);
			RequestHelper::writeDataJson($response, $data);
	    } catch (AddressNotFoundException|CodeNotFoundException $e) {
		    $response = RequestHelper::errorResponse($e->getMessage(), ResponseCodes::NOT_FOUND_404);
	    } catch (ParamNotFoundException $e) {
		    $response = RequestHelper::errorResponse($e->getMessage(), ResponseCodes::CONFLICT_409);
	    }

	    return $response;
    }

}

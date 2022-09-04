<?php

declare(strict_types=1);

namespace GAR\Controller;

use GAR\Exceptions\{AddressNotFoundException,
	CodeNotFoundException,
	ParamNotFoundException,
	ServerSideProblemException};
use GAR\Helpers\{RequestHelper, ResponseCodes};
use GAR\Repository\{AddressByNameRepository, Builders\AddressBuilderImplement, CodeByObjectIdRepository};
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};

/**
 * Address controller
 */

class AddressController
{
    /** @var AddressByNameRepository */
    protected AddressByNameRepository $addressByNameRepo;
	/** @var CodeByObjectIdRepository  */
    protected CodeByObjectIdRepository $addressByCodeRepo;

    public function __construct()
    {
        $this->addressByNameRepo = new AddressByNameRepository(new AddressBuilderImplement());
        $this->addressByCodeRepo = new CodeByObjectIdRepository();
    }

	/**
	 * @param Request $request
	 * @param Response $response
	 * @return Response
	 */
    public function getAddressByName(Request $request, Response $response): Response
    {
        $address = $request->getQueryParams()['address'];

	    try {
		    $likeAddress = $this->addressByNameRepo->getFullAddress($address);
		    RequestHelper::writeDataJson($response, $likeAddress);
	    } catch (AddressNotFoundException $e) {
			$response = RequestHelper::errorResponse($e->getMessage(), ResponseCodes::NOT_FOUND_404);
	    } catch (ParamNotFoundException $e) {
			$response = RequestHelper::errorResponse($e->getMessage(), ResponseCodes::CONFLICT_409);
	    } catch (ServerSideProblemException $e) {
			$response = RequestHelper::errorResponse('Server side problems' . $e->getMessage(), ResponseCodes::NOT_IMPLEMENTED_501);
	    }

        return $response;
    }

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param array<string> $args
	 * @return Response
	 */
    public function getCodeByType(Request $request, Response $response, array $args): Response
    {
        $params = $request->getQueryParams();

	    try {
		    if (null === $params['objectid']) {
			    $params['objectid'] = $this->addressByNameRepo->getChiledObjectIdFromAddress($params['address']);
		    }

		    $data = $this->addressByCodeRepo->getCode($params['objectid'], $args['type']);
			RequestHelper::writeDataJson($response, $data);
	    } catch (AddressNotFoundException|CodeNotFoundException $e) {
		    $response = RequestHelper::errorResponse($e->getMessage(), ResponseCodes::NOT_FOUND_404);
	    } catch (ParamNotFoundException $e) {
		    $response = RequestHelper::errorResponse($e->getMessage(), ResponseCodes::CONFLICT_409);
	    } catch (ServerSideProblemException $e) {
		    $response = RequestHelper::errorResponse('Server side problems', ResponseCodes::NOT_IMPLEMENTED_501);
	    }

        return $response;
    }

}

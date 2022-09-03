<?php

declare(strict_types=1);

namespace GAR\Controller;

use DB\Exceptions\BadQueryResultException;
use GAR\Exceptions\CodeNotFoundException;
use GAR\Exceptions\ParamNotFoundException;
use GAR\Repository\AddressByNameRepository;
use GAR\Repository\Builders\AddressBuilderImplement;
use GAR\Repository\CodeByObjectIdRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Address controller
 */

class AddressController
{
    /**
     * @var AddressByNameRepository
     */
    protected AddressByNameRepository $addressByNameRepo;
    protected CodeByObjectIdRepository $addressByCodeRepo;

    public function __construct(
  ) {
        $this->addressByNameRepo = new AddressByNameRepository(new AddressBuilderImplement());
        $this->addressByCodeRepo = new CodeByObjectIdRepository();
    }

    public function getAddressByName(Request $request, Response $response): Response
    {
        $address = $request->getQueryParams()['address'];

        $likeAddress = $this->addressByNameRepo->getFullAddress($address);

        if (!empty($likeAddress)) {
            $response->getBody()->write(json_encode($likeAddress, JSON_FORCE_OBJECT));
        }

        return $response;
    }

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param array<string> $args
	 * @return Response
	 * @throws CodeNotFoundException
	 * @throws ParamNotFoundException
	 * @throws BadQueryResultException
	 */
    public function getCodeByType(Request $request, Response $response, array $args): Response
    {
        $params = $request->getQueryParams();

        if (null === $params['objectid']) {
	        $params['objectid'] = $this->addressByNameRepo->getChiledObjectIdFromAddress($params['address']);
        }

        $data = $this->addressByCodeRepo->getCode($params['objectid'], $args['type']);
        $response->getBody()->write(json_encode($data, JSON_FORCE_OBJECT));

        return $response;
    }
}

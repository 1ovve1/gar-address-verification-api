<?php

declare(strict_types=1);

namespace GAR\Controller;

use GAR\Entity\EntityFactory;
use GAR\Repository\AddressByNameRepository;
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
        $this->addressByNameRepo = new AddressByNameRepository(EntityFactory::getProductionDB());
        $this->addressByCodeRepo = new CodeByObjectIdRepository(EntityFactory::getProductionDB());
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

    public function getCodeByType(Request $request, Response $response, array $args): Response
    {
        $params = $request->getQueryParams();

        if (null === $params['objectid']) {
            $likeAddress = $this->addressByNameRepo->getFullAddress($params['address']);

            foreach (array_reverse($likeAddress) as $key => $value) {
                if (
          count($value) === 1 &&
          !key_exists('houses', $value) &&
          !key_exists('variant', $value) &&
          !key_exists('parent_variants', $value)
        ) {
                    $params['objectid'] = end($value)[0]['objectid'];
                    break;
                }
            }
        }

        if (null !== $params['objectid']) {
            $data = $this->addressByCodeRepo->getCode($params['objectid'], $args['type']);

            if (!empty($data)) {
                $response->getBody()->write(json_encode($data, JSON_FORCE_OBJECT));
            }
        }

        return $response;
    }
}

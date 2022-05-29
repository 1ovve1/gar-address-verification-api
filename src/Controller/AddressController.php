<?php declare(strict_types=1);

namespace GAR\Controller;

use GAR\Entity\EntityFactory;
use GAR\Repository\AddressByCodeRepository;
use GAR\Repository\AddressByNameRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddressController
{
  protected AddressByNameRepository $addressByNameRepo;
  protected AddressByCodeRepository $addressByCodeRepo;

  public function __construct(
  ){
    $this->addressByNameRepo = new AddressByNameRepository(EntityFactory::getProductionDB());
    $this->addressByCodeRepo = new AddressByCodeRepository(EntityFactory::getProductionDB());
  }

  public function getAddressByName(Request $request, Response $response): Response
  {
    $address = $request->getQueryParams()['address'];

    $likeAddress[] = $this->addressByNameRepo->getFullAddress($address);

    if (!empty($likeAddress[0])) {
      $response->getBody()->write(json_encode($likeAddress, JSON_FORCE_OBJECT));
    }

    return $response;
  }

  public function getCodeByType(Request $request, Response $response, array $args) : Response
  {
    $params = $request->getQueryParams();

    if (is_null($params['objectid'])) {
      $likeAddress = $this->addressByNameRepo->getFullAddress($params['address']);

      foreach ($likeAddress as $key => $value) {
        if (
          count($value) === 1 && 
          $key !== 'houses' && 
          $key !== 'variant' &&
          $key !== 'parent_variants'
        ) {
          $params['objectid'] = $value[0]['objectid'];
          break;
        }
      } 

    }

    if (!is_null($params['objectid'])) {
      $data = $this->addressByCodeRepo->getCode($params['objectid'], $args['type']);

      if (!empty($data)) {
        $response->getBody()->write(json_encode($data, JSON_FORCE_OBJECT));
      }
    }

    return $response; 
  }

}
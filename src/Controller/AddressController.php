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

  public function getAddressByName(Request $request, Response $response, $args): Response
  {
    $param = $request->getQueryParams();
    if (array_key_exists('address', $param)) {
      $halfAddress = explode(',', $param['address']);
      $likeAddress[] = $this->addressByNameRepo->getFullAddress($halfAddress);

      if (empty($likeAddress)) {
        $response = $this->errorMessage($response, 'address not found');
      } else {
        $response->getBody()->write(json_encode($likeAddress, JSON_FORCE_OBJECT));
      }
    } else {
      $response = $this->errorMessage($response, "require 'address' param");
    }

    return $response;
  }

  public function getCodeByType(Request $request, Response $response, $args) : Response
  {
    $param = $request->getQueryParams();
    $objectId = null;

    if (key_exists('objectid', $param)) {
      $objectId = intval($param['objectid']);
    } else if (key_exists('address', $param)) {
      $fullAddress = $this->addressByNameRepo->getFullAddress(
        explode(',', $param['address'])
      );

      while($lastElem = array_pop($fullAddress)) {
        if (count($lastElem) === 1) {
          $objectId = $lastElem[0]['objectid'];
          break;
        }
      }
    } else {
      $response = $this->errorMessage($response, "require 'objectid' or 'address' param");
    }

    if (!is_null($objectId)) {
      $data = $this->addressByCodeRepo->getCode(
        $objectId, $args['type']
      );
      $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
    } else {
      $response = $this->errorMessage($response, "address not found");
    }

    return $response;
  }

  protected function errorMessage(Response $response, string $message, int $status = 401) : Response
  {
    $response = $response->withStatus($status);
    $response->getBody()->write(json_encode(['error' => $message]));
    return $response;
  }
}
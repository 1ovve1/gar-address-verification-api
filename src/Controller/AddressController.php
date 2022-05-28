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

    if (!array_key_exists('address', $param)) {
      $response = $this->errorMessage($response, "require 'address' param", 406);
      return $response;
    } else {
      if (strlen($param['address']) >= 1000) {
        $response = $this->errorMessage($response, "param 'address' too large, canceld", 414);
        return $response;
      }
    }

    $halfAddress = explode(',', $param['address']);

    if (count($halfAddress) > 1 && empty(trim($halfAddress[0]))) {
      $response = $this->errorMessage($response, "param 'address' shouldn't be empty", 411);
      return $response;
    }
       
    $likeAddress[] = $this->addressByNameRepo->getFullAddress($halfAddress);

    if (!empty($likeAddress[0])) {
      $response->getBody()->write(json_encode($likeAddress, JSON_FORCE_OBJECT));
    } else {
      $response = $this->errorMessage($response, 'address not found', 404);
      return $response;
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

      if (!key_exists('error', $fullAddress)) {
        while($lastElem = array_pop($fullAddress)) {
          if (count($lastElem) === 1) {
            $objectId = $lastElem[0]['objectid'];
            break;
          }
        }
      } 
    } else {
      $response = $this->errorMessage($response, "require 'objectid' or 'address' param", 406);
      return $response;
    }

    if (!is_null($objectId)) {
      $data = $this->addressByCodeRepo->getCode(
        $objectId, $args['type']
      );

      if (empty($data)) {
        $response = $this->errorMessage($response, "codes not found", 404); 
      } else {
        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
      }
    } else {
      $response = $this->errorMessage($response, 'address not found', 404);
    }

    return $response; 
  }


  protected function errorMessage(Response $response, string $message, int $status = 400) : Response
  {
    $response = $response->withStatus($status);
    $response->getBody()->write(json_encode(['error' => $message]));
    return $response;
  }
}
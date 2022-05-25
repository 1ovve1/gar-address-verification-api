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
    $likeAddress['time'] = microtime();

    $halfAddress = explode(',', $request->getQueryParams()['address']);
    $likeAddress[] = $this->addressByNameRepo->getFullAddress($halfAddress);
    $likeAddress['time'] = microtime() - $likeAddress['time'];

    if (empty($likeAddress)) {
      $response = $this->errorMessage($response, 'address not found');
    } else {
      $response->getBody()->write(json_encode($likeAddress, JSON_FORCE_OBJECT));
    }

    return $response;
  }

  public function getAddressByOkato(Request $request, Response $response, $args) : Response
  {
    $response->getBody()->write('okato');
    return $response;
  }

  public function getAddressByOktmo(Request $request, Response $response, $args) : Response
  {
    if (array_key_exists('code', $_GET)) {
      $code = intval($_GET['code']);
    }

    $data = $this->addressByCodeRepo->getAddressByCode($code, $this->addressByCodeRepo::OKATO);

    $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
    return $response;
  }

  public function getAddressByKladr(Request $request, Response $response, $args) : Response
  {
    $response->getBody()->write('kladr');
    return $response;
  }

  protected function errorMessage(Response $response, string $message, int $status = 401) : Response
  {
    $response = $response->withStatus($status);
    $response->getBody()->write(json_encode(['error' => $message]));
    return $response;
  }
}
<?php declare(strict_types=1);

namespace GAR\Controller;

use GAR\Entity\EntityFactory;
use GAR\Repository\GarRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddressController
{
  protected $repo;

  public function __construct(
//    protected readonly GarRepository $repo
  ){
    $this->repo = new GarRepository(new EntityFactory());
  }

  public function getAddress(Request $request, Response $response, $args): Response
  {
    $halfAddress = explode(',', $_GET['address']);
    $likeAddress = $this->repo->getLikeAddress($halfAddress);
    $response->getBody()->write(json_encode($likeAddress));
    return $response;
  }

  public function getAllAddress(Request $request, Response $response, $args): Response
  {
    $model = EntityFactory::getAddressObjectTable();

    $response->getBody()->write(json_encode($model->select(['name_addr'])->save()));
    return $response;
  }
}
<?php declare(strict_types=1);

namespace GAR\Storage\Builders;

use DB\ORM\DBAdapter\QueryResult;
use GAR\Exceptions\ParamNotFoundException;
use GAR\Storage\Elements\ChainPoint;
use RuntimeException;

class AddressBuilderDirector
{
	const OBJECTID = 'objectid';

	/** @var AddressBuilder $addressBuilder */
	private AddressBuilder $addressBuilder;
	/** @var array<string> $userAddress */
	private readonly array $userAddress;
	/** @var int $userAddressLength */
	private readonly int $userAddressLength;
	/** @var int $parentPos - position of parent address chain */
	private int $parentPos;
	/** @var int $chiledPos - position of chiled address chain */
	private int $chiledPos;

	/**
	 * @param AddressBuilder &$addressBuilder - builder ref
	 * @param array<string> $userAddress - user address
	 * @param int|null $parentPos - parent address element position
	 * @param int|null $chiledPos - chiled address element position
	 */
	function __construct(AddressBuilder $addressBuilder,
						 array $userAddress,
						 ?int $parentPos = null,
						 ?int $chiledPos = null)
	{
		$this->addressBuilder = $addressBuilder;
		$this->userAddress = $userAddress;
		$this->userAddressLength = count($userAddress);

		$parentPos = $parentPos ?? 0;
		$chiledPos = $chiledPos ?? $this->userAddressLength - 1;

		$this->posValidate($parentPos, $chiledPos);

		$this->parentPos = $parentPos;
		$this->chiledPos = $chiledPos;
	}

	/**
	 * @param int $parentPos
	 * @param int $chiledPos
	 * @return void
	 * @throws RuntimeException
	 */
	private function posValidate(int $parentPos, int $chiledPos): void
	{
		if ($parentPos >= $this->userAddressLength ||
			$parentPos < 0 ||
			$chiledPos >= $this->userAddressLength ||
			$chiledPos < 0) {
			throw new RuntimeException(sprintf(
				"Out of range with pos '%s' and '%s' (total length is %s)",
				$parentPos, $chiledPos, $this->userAddressLength
			));
		} elseif ($parentPos > $chiledPos) {
			throw new RuntimeException(sprintf(
				"parent pos '%s' should be lower than chiledPos, but chiled pos is '%s'",
				$parentPos, $chiledPos
			));
		}
	}

	/**
	 * @param AddressBuilderDirector $addressBuilderParent
	 * @param ChainPoint $chainElement
	 * @return self
	 */
	static function fromChainPoint(AddressBuilderDirector $addressBuilderParent, ChainPoint $chainElement): self
	{
		return new self(
			$addressBuilderParent->getAddressBuilder(),
			$addressBuilderParent->getUserAddress(),
			$chainElement->parentPosition,
			$chainElement->chiledPosition
		);
	}

	/**
	 * @return array<string>
	 */
	function getUserAddress(): array
	{
		return $this->userAddress;
	}

	function getAddressBuilder(): AddressBuilder
	{
		return $this->addressBuilder;
	}

	/**
	 * @param QueryResult $queryResult
	 * @return AddressBuilderDirector
	 */
	function addParentAddr(QueryResult $queryResult): self
	{
		foreach ($queryResult->fetchAllAssoc() as $parentAddressElement) {

			$identifier = $this->getCurrentParentName();

			$this->addressBuilder->addParentAddr($identifier, [$parentAddressElement]);
			$this->parentPos--;
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	private function isParentPosNotOverflow(): bool
	{
		return $this->parentPos >= 0;
	}

	/**
	 * @param QueryResult $queryResult
	 * @return AddressBuilderDirector
	 */
	function addChiledAddr(QueryResult $queryResult): self
	{
		foreach ($queryResult->fetchAllAssoc() as $chiledAddressElement) {

			$identifier = match($this->isChiledPosNotOverflow()) {
				true => $this->getCurrentChiledName(),
				false => throw new RuntimeException('chiled position is max')
			};

			$this->addressBuilder->addChiledAddr($identifier, [$chiledAddressElement]);
			$this->chiledPos++;
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	function isChiledPosNotOverflow(): bool
	{
		return $this->chiledPos < $this->userAddressLength;
	}

	/**
	 * @param QueryResult $queryResult
	 * @return AddressBuilderDirector
	 */
	function addChiledHouses(QueryResult $queryResult): self
	{
		$this->addressBuilder->addChiledHouses($queryResult->fetchAllAssoc());
		return $this;
	}

	/**
	 * @param QueryResult $queryResult
	 * @return AddressBuilderDirector
	 */
	function addChiledVariant(QueryResult $queryResult): self
	{
		$this->addressBuilder->addChiledVariant($queryResult->fetchAllAssoc());
		return $this;
	}

	/**
	 * Return complete address structure
	 * @return array<int, array<string, array<int, array<string, string|int>>>>
	 */
	function getAddress(): array
	{
		return $this->addressBuilder->getAddress();
	}

	/**
	 * @return string - current child name
	 */ 
	function getCurrentChiledName(): string
	{
		if (!$this->isChiledPosNotOverflow()) {
			throw new RuntimeException('chiled position overflow');
		}

		return $this->userAddress[$this->chiledPos];
	}

	/**
	 * Return previous chiled name
	 * @return string
	 */
	function getPrevChiledName(): string
	{
		return $this->userAddress[$this->chiledPos - 1];
	}

	/**
	 * @return string - current child name
	 */ 
	function getCurrentParentName(): string
	{
		return match($this->isParentPosNotOverflow()) {
			true => $this->userAddress[$this->parentPos],
			false => "parent_" . (-$this->parentPos)
		};
	}

	/**
	 * @return string
	 */
	function getPrevParentName(): string
	{
		return match($this->parentPos >= -1) {
			true => $this->userAddress[$this->parentPos + 1],
			false => "parent_" . (-$this->parentPos + 1)
		};
	}

	/**
	 * @param string $param
	 * @param string $identifier
	 * @return mixed
	 * @throws ParamNotFoundException
	 */
	function findParamFromIdentifier(string $param, string $identifier): mixed
	{
		$data = $this->getAddress();
		$backLog = null;

		foreach ($data as $identifierElement) {
			if (key($identifierElement) === $identifier) {
				$element = $identifierElement[$identifier];

				if (is_array($element) && count($element) === 1) {
					$singleElement = current($element);

					if (isset($singleElement[$param])) {
						return $singleElement[$param];

					} else {
						$backLog = "Param by {$identifier} was not found";
					}
				} else {
					$backLog = "Data by {$identifier} is not array";
				}
			}
		}

		throw new ParamNotFoundException($param, $data, $backLog ?? "Identifier {$identifier} not found");
	}

	/**
	 * @param string $identifier
	 * @return int
	 * @throws ParamNotFoundException
	 */
	function findObjectIdFromIdentifier(string $identifier): int
	{
		$objectId = $this->findParamFromIdentifier(self::OBJECTID, $identifier);
		if (!is_int($objectId)) {
			throw new ParamNotFoundException('objectid', [$objectId], 'Object id is not an integer');
		}

		return $objectId;
	}

	/**
	 * @return int
	 * @throws ParamNotFoundException
	 */
	function findChiledObjectId(): int
	{
		return $this->findObjectIdFromIdentifier($this->getPrevChiledName());
	}

	/**
	 * @return int
	 * @throws ParamNotFoundException
	 */
	function findParentObjectId(): int
	{
		return $this->findObjectIdFromIdentifier($this->getPrevParentName());
	}

}
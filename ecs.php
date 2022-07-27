<?php declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
  // A. full sets
  $ecsConfig->sets([SetList::PSR_12, SetList::CLEAN_CODE, SetList::STRICT, SetList::ARRAY]);

  // B. standalone rule
  $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, [
  'syntax' => 'short',
  ]);

};
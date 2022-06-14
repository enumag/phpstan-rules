<?php declare(strict_types = 1);

namespace ShipMonk\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\MixedType;
use function sprintf;

/**
 * @implements Rule<PropertyFetch>
 */
class ForbidFetchOnMixedRule implements Rule
{

    private Standard $printer;

    public function __construct(Standard $printer)
    {
        $this->printer = $printer;
    }

    public function getNodeType(): string
    {
        return PropertyFetch::class;
    }

    /**
     * @param PropertyFetch $node
     * @return string[] errors
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $caller = $node->var;
        $callerType = $scope->getType($caller);

        if ($callerType instanceof MixedType) {
            $name = $node->name;
            $property = $name instanceof Identifier
                ? $this->printer->prettyPrint([$name])
                : $this->printer->prettyPrintExpr($name);

            return [
                sprintf(
                    'Property fetch ->%s is prohibited on unknown type (%s)',
                    $property,
                    $this->printer->prettyPrintExpr($caller),
                ),
            ];
        }

        return [];
    }

}

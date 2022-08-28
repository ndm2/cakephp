<?php
declare(strict_types=1);

namespace Cake\PHPStan;

use Cake\Database\Query as DatabaseQuery;
use Cake\ORM\Query;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

class ORMQueryDynamicMixinMethodReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    protected ReflectionProvider $reflectionProvider;

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getClass(): string
    {
        return Query::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return !$this->reflectionProvider
            ->getClass(Query::class)
            ->hasNativeMethod($methodReflection->getName());
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): ?Type
    {
        $returnType =
            ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())
                ->getReturnType();

        if (
            $returnType instanceof ObjectType &&
            $returnType->isInstanceOf(DatabaseQuery::class)->yes()
        ) {
            return new ObjectType(Query::class);
        }

        return null;
    }
}

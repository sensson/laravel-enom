<?php

declare(strict_types=1);

namespace Rector\Enom;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Nop;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AddEmptyConstructorCommentRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add // comment to empty constructor body', [
            new CodeSample(
                'public function __construct(private readonly string $name) {}',
                "public function __construct(private readonly string \$name)\n{\n    //\n}",
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof ClassMethod);

        if (! $this->isName($node, '__construct')) {
            return null;
        }

        if ($node->stmts !== []) {
            return null;
        }

        $nop = new Nop;
        $nop->setAttribute('comments', [new Comment('//')]);
        $node->stmts = [$nop];

        return $node;
    }
}

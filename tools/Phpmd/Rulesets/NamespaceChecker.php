<?php

namespace tools\Phpmd\Rulesets;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\MethodAware;
use ReflectionClass;

class NamespaceChecker extends AbstractRule implements MethodAware
{
    public function apply(AbstractNode $node)
    {
        $whitelist = array('self', 'parent', 'static');
        foreach ($node->findChildrenOfType('ClassOrInterfaceReference') as $child) {
            if (!in_array($child->getName(), $whitelist)) {
                try {
                    $reflector = new ReflectionClass($child->getName());
                } catch (\Exception $e) {
                    $this->addViolation($child, array($child->getName()));
                }
            }
        }
    }
}

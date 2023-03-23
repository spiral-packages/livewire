<?php

declare(strict_types=1);

namespace Spiral\Livewire\Twig\NodeVisitor;

use Spiral\Livewire\Livewire;
use Twig\Environment;
use Twig\Node\Node;
use Twig\Node\TextNode;
use Twig\NodeVisitor\NodeVisitorInterface;

/**
 * This class handles the replacement of inline HTML wire directives
 * like `<livewire:counter foo="bar" />`.
 */
final class LivewireNodeVisitor implements NodeVisitorInterface
{
    private const DIRECTIVE = 'livewire';

    public function __construct(
        private readonly Livewire $livewire
    ) {
    }

    public function enterNode(Node $node, Environment $env): Node
    {
        // filter out actual twig stuff, we only care for the TextNodes -
        // these contain the HTML
        if (!$node instanceof TextNode) {
            return $node;
        }

        // the 'data' attribute of the text node contains the template text.
        if ($text = $node->getAttribute('data')) {
            // if the text does not contain the directive start `<wire:`, just return
            if (!str_contains($text, '<'.self::DIRECTIVE.':')) {
                return $node;
            }

            // Replace all occurrences of the directive with the initial render of the specific component
            $replaced = (string) preg_replace_callback(
                '/<'.self::DIRECTIVE.':(?\'name\'[a-zA-Z0-9_-]*)\s(?\'args\'(?:\w*="\w*"\s?)*)\s?\/>/',
                function ($match) {
                    // if the named capture group 'name' did not match, something is wrong
                    // and just return the entire string
                    if (!$match['name']) {
                        return $match[0];
                    }

                    $componentName = $match['name'];
                    $componentArgs = explode(' ', $match['args']);
                    $componentArgs = array_filter($componentArgs, static fn (string $input): bool => (bool) $input);
                    $componentArgs = array_map(
                        static fn (string $argPair): mixed => explode('=', $argPair)[1],
                        $componentArgs
                    );
                    $componentArgs = array_map(trim(...), $componentArgs);

                    // initial component render by the LifecycleManager as usual
                    return $this->livewire->initialRequest($componentName, ...$componentArgs);
                },
                $text
            );

            $node->setAttribute('data', $replaced);
        }

        return $node;
    }

    public function leaveNode(Node $node, Environment $env): ?Node
    {
        return $node;
    }

    public function getPriority(): int
    {
        return 10;
    }
}

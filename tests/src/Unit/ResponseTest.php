<?php

declare(strict_types=1);

namespace Spiral\Livewire\Tests\Unit;

use Spiral\Livewire\Request;
use Spiral\Livewire\Response;
use Spiral\Livewire\Tests\Functional\TestCase;

final class ResponseTest extends TestCase
{
    public function testToSubsequentResponseWithDotNotationSyntax(): void
    {
        $request = new Request([
            'serverMemo' => ['data' => ['form_data' => []]],
            'fingerprint' => [],
            'updates' => []
        ]);
        $response = new Response(
            [],
            ['data' => ['form_data' => ['password' => 'foo-bar', 'email' => 'foo@gmail.com']]],
            ['dirty' => ['form_data', 'form_data.password', 'form_data.email']]
        );

        $this->assertSame([
            'effects' => [
                'dirty' => [
                    'form_data',
                    'form_data.password',
                    'form_data.email'
                ]
            ],
            'serverMemo' => [
                'data' => [
                    'form_data' => [
                        'password' => 'foo-bar',
                        'email' => 'foo@gmail.com'
                    ]
                ]
            ]
        ], $response->toSubsequentResponse($request));
    }

    public function testToSubsequentResponseWithPropertyAccessSyntax(): void
    {
        $request = new Request([
            'serverMemo' => ['data' => ['form_data' => []]],
            'fingerprint' => [],
            'updates' => []
        ]);
        $response = new Response(
            [],
            ['data' => ['form_data' => ['password' => 'foo-bar', 'email' => 'foo@gmail.com']]],
            ['dirty' => ['form_data', 'form_data[password]', 'form_data[email]']]
        );

        $this->assertSame([
            'effects' => [
                'dirty' => [
                    'form_data',
                    'form_data[password]',
                    'form_data[email]'
                ]
            ],
            'serverMemo' => [
                'data' => [
                    'form_data' => [
                        'password' => 'foo-bar',
                        'email' => 'foo@gmail.com'
                    ]
                ]
            ]
        ], $response->toSubsequentResponse($request));
    }
}

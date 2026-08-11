<?php

namespace Tests\Feature;

use Tests\TestCase;

/** Verify that the bundled OpenAPI documentation is available. */
class SwaggerDocumentationTest extends TestCase
{
    /** Verify the Swagger UI page and specification contents. */
    public function test_swagger_documentation_is_available(): void
    {
        $this->get('/api/docs')
            ->assertOk()
            ->assertSee('swagger-ui');

        $specification = file_get_contents(public_path('docs/openapi.yaml'));

        $this->assertIsString($specification);
        $this->assertStringContainsString('openapi: 3.0.3', $specification);
        $this->assertStringContainsString('/notes/{note}:', $specification);
        $this->assertStringContainsString('ErrorResponse:', $specification);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaAssetsTest extends TestCase
{
    public function test_manifest_exists_and_is_valid_json(): void
    {
        $this->assertFileExists(public_path('manifest.json'));

        $manifest = json_decode(file_get_contents(public_path('manifest.json')), true);
        $this->assertSame('ReliefFlow', $manifest['short_name']);
        $this->assertNotEmpty($manifest['icons']);
        foreach ($manifest['icons'] as $icon) {
            $this->assertFileExists(public_path(ltrim($icon['src'], '/')));
        }
    }

    public function test_service_worker_exists(): void
    {
        $this->assertFileExists(public_path('sw.js'));
    }

    public function test_offline_fallback_page_exists(): void
    {
        $this->assertFileExists(public_path('offline.html'));
    }
}

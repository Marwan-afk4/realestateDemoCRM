<?php

use App\Enums\PipelineStage;

it('maps legacy lead statuses onto the sales pipeline', function () {
    expect(PipelineStage::fromLegacyLeadStatus('done'))->toBe(PipelineStage::Won)
        ->and(PipelineStage::fromLegacyLeadStatus('lost'))->toBe(PipelineStage::Lost)
        ->and(PipelineStage::fromLegacyLeadStatus('pending')->isOpen())->toBeTrue()
        ->and(PipelineStage::Lost->isTerminal())->toBeTrue()
        ->and(PipelineStage::Won->toLegacyLeadStatus())->toBe('done');
});

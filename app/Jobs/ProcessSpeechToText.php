<?php

namespace App\Jobs;

use App\Contracts\SpeechDriverInterface;
use App\Models\AudioMessage;
use App\Repositories\SpeechRepository;
use App\Services\Messaging\AudioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessSpeechToText implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(public AudioMessage $audioMessage) {}

    public function handle(
        AudioService $audioService,
        SpeechDriverInterface $driver,
        SpeechRepository $repository,
    ): void {
        $repository->markProcessing($this->audioMessage);

        try {
            $bytes = $audioService->retrieveBytes($this->audioMessage);

            $result = $driver->transcribe(
                $bytes,
                $this->audioMessage->mime_type ?? 'audio/mpeg',
                config('speech.default_language') !== 'auto' ? config('speech.default_language') : null,
            );

            $repository->saveTranscript($this->audioMessage, $result['text'], $result['language']);
        } catch (Throwable $e) {
            Log::error('Speech-to-text job failed', [
                'audio_message_id' => $this->audioMessage->id,
                'error' => $e->getMessage(),
            ]);

            $repository->markFailed($this->audioMessage);

            throw $e; // let the queue's retry/backoff policy handle it
        }
    }

    public function failed(Throwable $exception): void
    {
        app(SpeechRepository::class)->markFailed($this->audioMessage);
    }
}

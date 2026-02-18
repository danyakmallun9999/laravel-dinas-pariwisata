<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationController extends Controller
{
    public function translate(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:10003', // Slightly more than 10k to be safe
            'target' => 'required|string|in:en,id',
            'source' => 'nullable|string',
        ]);

        $text = $request->text;
        $source = $request->source ?? 'id';
        $target = $request->target;

        // If text is short, translate directly
        if (mb_strlen($text) <= 450) {
            return $this->performTranslation($text, $source, $target);
        }

        // Otherwise, chunk the text
        $chunks = $this->chunkText($text, 450);
        $translatedChunks = [];

        foreach ($chunks as $chunk) {
            $result = $this->performTranslation($chunk, $source, $target);
            $responseData = $result->getData(true);
            
            if ($responseData['success']) {
                $translatedChunks[] = $responseData['translation'];
            } else {
                return $result; // Return the error if any chunk fails
            }
            
            // Subtle delay to avoid rate limiting if many chunks
            if (count($chunks) > 2) {
                usleep(200000); // 200ms
            }
        }

        return response()->json([
            'success' => true,
            'translation' => implode(' ', $translatedChunks),
            'provider' => 'chunked',
        ]);
    }

    private function chunkText($text, $size)
    {
        $chunks = [];
        $words = explode(' ', $text);
        $currentChunk = '';

        foreach ($words as $word) {
            if (mb_strlen($currentChunk . ' ' . $word) > $size) {
                $chunks[] = trim($currentChunk);
                $currentChunk = $word;
            } else {
                $currentChunk .= (empty($currentChunk) ? '' : ' ') . $word;
            }
        }

        if (!empty($currentChunk)) {
            $chunks[] = trim($currentChunk);
        }

        return $chunks;
    }

    private function performTranslation($text, $source, $target)
    {
        // Strategy 1: Try Google Translate first
        try {
            $tr = new GoogleTranslate();
            $tr->setSource($source);
            $tr->setTarget($target);
            $translatedText = $tr->translate($text);

            return response()->json([
                'success' => true,
                'translation' => $translatedText,
                'provider' => 'google',
            ]);
        } catch (\Exception $e) {
            Log::warning('Google Translate failed, falling back to MyMemory: ' . $e->getMessage());
        }

        // Strategy 2: Fallback to MyMemory API
        try {
            $langpair = "{$source}|{$target}";
            $response = Http::timeout(20)->get('https://api.mymemory.translated.net/get', [
                'q' => $text,
                'langpair' => $langpair,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['responseData']['translatedText'])) {
                    return response()->json([
                        'success' => true,
                        'translation' => $data['responseData']['translatedText'],
                        'provider' => 'mymemory',
                    ]);
                }
            }

            Log::error('MyMemory API returned unexpected response: ' . $response->body());

            return response()->json([
                'success' => false,
                'message' => 'Translation failed from both providers.',
            ], 500);
        } catch (\Exception $e) {
            Log::error('MyMemory API also failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Semua provider terjemahan gagal. Silakan coba lagi nanti.',
            ], 500);
        }
    }
}


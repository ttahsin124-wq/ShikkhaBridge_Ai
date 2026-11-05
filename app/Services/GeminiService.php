<?php
class GeminiService
{
    /** Sends a prompt to Google Gemini. Without an API key it returns a clearly labelled mock answer. */
    public function generate(string $prompt): string
    {
        $key = config('gemini.api_key');
        if (!$key) return $this->mock($prompt);

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode(config('gemini.model')) . ':generateContent';
        $body = json_encode(['contents' => [['parts' => [['text' => $prompt]]]]]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-goog-api-key: ' . $key],
        ]);
        $raw = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false || $status !== 200) {
            throw new RuntimeException('AI service request failed (HTTP ' . $status . ')');
        }
        $data = json_decode($raw, true);
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
        if ($text === '') throw new RuntimeException('AI service returned an empty answer');
        return trim($text);
    }

    private function mock(string $prompt): string
    {
        return "[Demo mode: no Gemini API key is set, so this is a placeholder answer.]\n\n"
            . "Add your key to config/config.local.php (gemini > api_key) to get real AI explanations.\n\n"
            . 'Your request was: ' . mb_substr($prompt, 0, 200);
    }

    public function languageName(string $lang): string
    {
        return $lang === 'en' ? 'simple English' : 'simple Bangla (Bengali)';
    }

    public function explainPrompt(string $topic, string $lang): string
    {
        return 'You are a friendly tutor for school and college students in Bangladesh. Explain the following concept in '
            . $this->languageName($lang) . ', step by step, with one short example. Use plain text only (no markdown symbols). '
            . "Keep it under 250 words.\n\nConcept or question: " . $topic;
    }

    public function practicePrompt(string $topic, string $lang): string
    {
        return 'Create 3 multiple-choice practice questions about the topic below in ' . $this->languageName($lang)
            . '. For each question give options A to D, then a line "Answer: X" and a one-line explanation. '
            . "Use plain text only (no markdown symbols).\n\nTopic: " . $topic;
    }

    public function summarizePrompt(string $text, string $lang): string
    {
        return 'Summarize the study material below in ' . $this->languageName($lang)
            . ' as 5 short key points. Use plain text only (no markdown symbols).' . "\n\nMaterial:\n" . mb_substr($text, 0, 6000);
    }
}

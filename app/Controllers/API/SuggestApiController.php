<?php

namespace App\Controllers\Api;

use App\Services\FormatService;
use PHPMailer\PHPMailer\PHPMailer;
use Exception;
use App\Controllers\BaseController;

class SuggestApiController extends BaseController
{
    private FormatService $formatService;

    public function __construct(FormatService $formatService)
    {
        $this->formatService = $formatService;
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            $this->json([
                'success' => false,
                'message' => 'Method not allowed'
            ], 405);
        }

        try {

            $input  = $this->getInput();
            $result = $this->process($input);

            if (!empty($result['error'])) {

                $this->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }

            $this->json([
                'success' => true,
                'message' => 'Suggestion sent successfully'
            ]);
        } catch (Exception $e) {

            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function process(array $data): array
    {
        $data = [
            'name'     => trim($data['name'] ?? ''),
            'email'    => trim($data['email'] ?? ''),
            'category' => trim($data['category'] ?? ''),
            'title'    => trim($data['title'] ?? ''),
            'format'   => trim($data['format'] ?? ''),
            'genre'    => trim($data['genre'] ?? ''),
            'year'     => trim($data['year'] ?? ''),
            'details'  => trim($data['details'] ?? ''),
        ];

        if (
            empty($data['name']) ||
            empty($data['email']) ||
            empty($data['category']) ||
            empty($data['title'])
        ) {
            return ['error' => 'Name, Email, Category and Title are required'];
        }

        if (!PHPMailer::validateAddress($data['email'])) {
            return ['error' => 'Invalid email address'];
        }

        $this->sendEmail($data);

        return ['success' => true];
    }

    private function sendEmail(array $data): void
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = $_ENV['MAIL_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['MAIL_USERNAME'];
        $mail->Password   = $_ENV['MAIL_PASSWORD'];

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->CharSet = 'UTF-8';

        $mail->setFrom(
            $_ENV['MAIL_FROM_EMAIL'],
            $_ENV['MAIL_FROM_NAME']
        );

        $mail->addReplyTo($data['email'], $data['name']);
        $mail->addAddress($_ENV['MAIL_FROM_EMAIL']);

        $mail->Subject = 'Library Suggestion from: ' . $data['name'];

        $mail->Body =
            "Name: {$data['name']}\n" .
            "Email: {$data['email']}\n\n" .
            "Category: {$data['category']}\n" .
            "Title: {$data['title']}\n" .
            "Format: {$data['format']}\n" .
            "Genre: {$data['genre']}\n" .
            "Year: {$data['year']}\n\n" .
            "Details:\n{$data['details']}";

        $mail->send();
    }

    private function getInput(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (str_contains($contentType, 'application/json')) {
            $json = file_get_contents("php://input");
            return json_decode($json, true) ?? [];
        }

        return $_POST;
    }
}

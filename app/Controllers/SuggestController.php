<?php

namespace App\Controllers;

use App\Services\FormatService;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SuggestController
{
    private FormatService $formatService;

    public function __construct(FormatService $formatService)
    {
        $this->formatService = $formatService;
    }

    /**
     * Display suggestion form
     */
    public function index(): void
    {
        $pageTitle  = "Suggest a Media Item";
        $section    = "suggest";
        $hideSearch = true;

        $error_message = null;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $result = $this->handleForm();

            if (!empty($result['error_message'])) {
                $error_message = $result['error_message'];
            }
        }

        $categories = $this->formatService->category_drop_down();
        $formats    = $this->formatService->format_array();
        $genres     = $this->formatService->genres_array();

        require BASE_PATH . '/view/suggest.php';
    }

    /**
     * Handle form submission
     */
    private function handleForm(): array
    {
        $data = [

            'name' => trim(
                filter_input(
                    INPUT_POST,
                    'name',
                    FILTER_SANITIZE_SPECIAL_CHARS
                ) ?? ''
            ),

            'email' => trim(
                filter_input(
                    INPUT_POST,
                    'email',
                    FILTER_SANITIZE_EMAIL
                ) ?? ''
            ),

            'category' => trim(
                filter_input(
                    INPUT_POST,
                    'category',
                    FILTER_SANITIZE_SPECIAL_CHARS
                ) ?? ''
            ),

            'title' => trim(
                filter_input(
                    INPUT_POST,
                    'title',
                    FILTER_SANITIZE_SPECIAL_CHARS
                ) ?? ''
            ),

            'format' => trim(
                filter_input(
                    INPUT_POST,
                    'format',
                    FILTER_SANITIZE_SPECIAL_CHARS
                ) ?? ''
            ),

            'genre' => trim(
                filter_input(
                    INPUT_POST,
                    'genre',
                    FILTER_SANITIZE_SPECIAL_CHARS
                ) ?? ''
            ),

            'year' => trim(
                filter_input(
                    INPUT_POST,
                    'year',
                    FILTER_SANITIZE_NUMBER_INT
                ) ?? ''
            ),

            'details' => trim(
                filter_input(
                    INPUT_POST,
                    'details',
                    FILTER_SANITIZE_SPECIAL_CHARS
                ) ?? ''
            ),

            'error_message' => null
        ];

        /*
         * Required validation
         */
        if (
            empty($data['name']) ||
            empty($data['email']) ||
            empty($data['category']) ||
            empty($data['title'])
        ) {

            $data['error_message'] =
                "Please fill in all required fields.";

            return $data;
        }

        /*
         * Email validation
         */
        if (!PHPMailer::validateAddress($data['email'])) {

            $data['error_message'] =
                "Invalid email address.";

            return $data;
        }

        /*
         * Honeypot spam protection
         */
        if (!empty($_POST['address'])) {

            $data['error_message'] =
                "Spam detected.";

            return $data;
        }

        try {

            $this->sendEmail($data);
        } catch (Exception $e) {

            $data['error_message'] =
                'Mailer Error: ' . $e->getMessage();

            return $data;
        }

        /*
         * Redirect after success
         */
        header(
            "Location: " .
                BASE_URL .
                "/public/index.php?page=suggest&status=thanks"
        );

        exit;
    }

    /**
     * Send email using PHPMailer
     */
    private function sendEmail(array $data): void
    {
        $mail = new PHPMailer(true);

        /*
         * SMTP Configuration
         */
        $mail->isSMTP();

        $mail->Host       = $_ENV['MAIL_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['MAIL_USERNAME'];
        $mail->Password   = $_ENV['MAIL_PASSWORD'];

        /*
         * Recommended for Gmail
         */
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Port = 587;

        /*
         * SSL fix for localhost/XAMPP
         */
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->CharSet = 'UTF-8';

        /*
         * Email addresses
         */
        $mail->setFrom(
            $_ENV['MAIL_FROM_EMAIL'],
            $_ENV['MAIL_FROM_NAME']
        );

        $mail->addReplyTo(
            $data['email'],
            $data['name']
        );

        $mail->addAddress(
            $_ENV['MAIL_FROM_EMAIL']
        );

        /*
         * Email content
         */
        $mail->Subject =
            'Library Suggestion from: ' .
            $data['name'];

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
}

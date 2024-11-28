<?php

namespace App\Helpers;

use App\Models\Notification;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class Helper
{
    public static function sendMail($email, $data, $viewName, $subject)
    {
        $siteUrl = env('SITE_URL');
        $siteName = env('SITE_NAME');
        $appUrl = env('APP_URL');
        $data['siteUrl'] = $siteUrl;
        $data['siteName'] = $siteName;
        $data['appUrl'] = $appUrl;

        try {
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = 0;
            //$mail->isSMTP();
            $mail->Host = config('mail.mailers.smtp.host');
            $mail->SMTPAuth = true;
            $mail->Username = config('mail.mailers.smtp.username');
            $mail->Password = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = config('mail.mailers.smtp.encryption');
            $mail->Port = config('mail.mailers.smtp.port');

            //Recipients
            $mail->addAddress($email, 'User');
            $mail->setFrom(config('mail.from.address'), config('mail.from.name'));

            $mail->isHTML(true);
            $mail->Subject = $subject;

            // Resolve the view instance and render its contents as a string
            $view = resolve('view');
            $mail->Body = $view->make($viewName, $data)->render();

            $mail->send();
            return true;
        } catch (Exception $e) {
            Log::error('Error sending email: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * Sends an SMS using a provided SMS gateway.
     *
     * @param string $phone The recipient's phone number.
     * @param string $message The message content to send.
     *
     * @return bool True if the SMS is sent successfully, false otherwise.
     */
     public static function sendSMS(string $phone, string $message, string $templateId): bool
    {
    $smsGatewayUrl = config('services.sms_gateway.url');
    $senderId = config('services.sms_gateway.sender_id');
    $accountName = config('services.sms_gateway.account_name');
    $accountPassword = config('services.sms_gateway.account_password');
    $requestTimeout = config('services.sms_gateway.request_timeout');
   // $apiKey = config('services.sms_gateway.api_key');
    $dltEntityId = config('services.sms_gateway.dlt_entity_id');
    //$dltTemplateId = config('services.sms_gateway.dlt_template_id');

    // Validate parameters
    if (empty($phone) || empty($message)) {
        return false;
    }

    // Construct the SMS gateway POST fields
    $postFields = http_build_query([
        'userid' => $accountName,
        'password' => $accountPassword,
        'mobile' => $phone,
        'msg' => urlencode($message),
        'senderid' => $senderId,
        'msgType' => 'text',
        'dltTemplateId' => $templateId,      
        'dltEntityId'  =>  $dltEntityId,
        'duplicatecheck' => 'true',
        'output' => 'json',
        'sendMethod' => 'quick',
    ]);

    // Initialize cURL
    $curl = curl_init();

    // Set cURL options
    curl_setopt_array($curl, [
        CURLOPT_URL => $smsGatewayUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => $requestTimeout,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => [
           // "apikey: $apiKey",
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded",
        ],
    ]);

    // Execute cURL request
    $response = curl_exec($curl);
    $err = curl_error($curl);

    // Close cURL
    curl_close($curl);

    // Check for errors
    if ($err) {
        Log::error('cURL error while sending SMS.', ['error' => $err]);
        return false;
    }

    // Decode the response
    $responseData = json_decode($response, true);

    // Check the response and determine if the SMS was sent successfully
    if (isset($responseData['status']) && $responseData['status'] == 'success') {
        return true;
    } else {
        Log::error('SMS sending failed.', ['response' => $responseData]);
        return false;
    }
   }

    public static function sendNotification($receiverId, $message)
    {
        // Create a new notification entry
        Notification::create([
            'receiver_id' => $receiverId,
            'message' => $message,
        ]);

        // You can implement the actual notification mechanism here
        // For example: sending an email, SMS, or push notification
    }

    public static function getMessagesByUserId($userId)
    {
        // Retrieve messages for the given user ID
        return Notification::where('receiver_id', $userId)->get();
    }

    public static function getConfig($name)
    {
        return DB::table('configs as pt')->select('title', 'value')->where('name', $name)->first();
    }

    /**
     * Converts a numeric amount into words.
     *
     * @param float $amount The numeric amount to convert.
     * @return string The amount in words.
     *
     * Functionality:
     * This method converts a numeric amount into words. It first separates the amount
     * into the rupees and paisa parts. Then, it converts the rupees part into words
     * using Indian numbering system (Lakh, Crore, etc.). Finally, it constructs
     * the final output string combining rupees and paisa in words.
     */
    public static function AmountInWords(float $amount): string
    {
        // Extract the amount after the decimal
        $amountAfterDecimal = round($amount - ($num = floor($amount)), 2) * 100;

        // Initialize variables
        $amountHundred = null;
        $countLength = strlen($num);
        $x = 0;
        $string = array();

        // Define words for numbers and digits
        $changeWords = array(
            0 => 'Zero', 1 => 'One', 2 => 'Two',
            3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
            7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
            13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
            70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        );

        $hereDigits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');

        // Iterate through each digit
        while ($x < $countLength) {
            // Determine the divider based on position
            $getDivider = ($x == 2) ? 10 : 100;
            $amount = floor($num % $getDivider);
            $num = floor($num / $getDivider);
            $x += $getDivider == 10 ? 1 : 2;

            if ($amount) {
                // Add 's' for plural and 'and' where necessary
                $addPlural = (($counter = count($string)) && $amount > 9) ? 's' : null;
                $amountHundred = ($counter == 1 && $string[0]) ? ' and ' : null;

                // Construct word for the current digit
                $string[] = ($amount < 21) ? $changeWords[$amount] . ' ' . $hereDigits[$counter] . $addPlural . ' ' . $amountHundred : $changeWords[floor($amount / 10) * 10] . ' ' . $changeWords[$amount % 10] . ' ' . $hereDigits[$counter] . $addPlural . ' ' . $amountHundred;
            } else {
                $string[] = null;
            }
        }

        // Concatenate the words for rupees and paisa
        $implodeToRupees = implode('', array_reverse($string));
        $getPaise = ($amountAfterDecimal > 0) ? "And " . ($changeWords[$amountAfterDecimal / 10] . " " . $changeWords[$amountAfterDecimal % 10]) . ' Paise' : '';

        // Return the final result
        return ($implodeToRupees ? $implodeToRupees . 'Rupees ' : '') . $getPaise;
    }

}

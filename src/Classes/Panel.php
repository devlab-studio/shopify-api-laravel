<?php

namespace Devlab\ShopifyApiLaravel\Classes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Devlab\ShopifyApiLaravel\Models\User;
use Exception;

class Panel extends Model
{
    use HasFactory;

    /**
     * Save Log.
     *
     * @param int $iUsers_id
     * @param string $vcMethod
     * @param array $aData array (key => value)
     *
     */
    public static function saveLog(
        ?int $iUsers_id,
        string $vcMethod,
        array $aData
    ) {

        if (!empty(config('devlab.DL_APP_TOKEN',''))) {
            $oUser = User::find($iUsers_id);
            $response = Http::timeout(2)->async()->withToken(config('devlab.DL_APP_TOKEN',''))->post(config('devlab.DL_APP_PANEL_URL','').'/logs', [
                'method' => $vcMethod,
                'data' => json_encode($aData),
                'user' => json_encode(
                    [
                        'name' => ($oUser) ? $oUser->name.' '.$oUser->surname : 'Desconocido',
                        'mail' => ($oUser) ? $oUser->email : '',
                        'id'=> $iUsers_id,
                    ]
                ),
                'response' => json_encode([])
            ]);

            $response->wait();
        }
    }

    /**
     * Save exception log.
     *
     * @param int $iUsers_id
     * @param string $vcPath
     * @param array $aData array (key => value)
     *
     */
    public static function saveError(
        ?int $iUsers_id,
        string $vcPath,
        array $aData
    ) {

        if (!empty(config('devlab.DL_APP_TOKEN',''))) {
            $oUser = User::find($iUsers_id);
            $response = Http::timeout(2)->async()->withToken(config('devlab.DL_APP_TOKEN',''))->post(config('devlab.DL_APP_PANEL_URL','').'/errors', [
                'path' => $vcPath,
                'data' => json_encode($aData),
                'user' => json_encode(
                    [
                        'name' => ($oUser) ? $oUser->name.' '.$oUser->surname : 'Desconocido',
                        'mail' => ($oUser) ? $oUser->email : '',
                        'id'=> $iUsers_id,
                    ]
                )
            ]);
            $response->wait();
        }
    }

    /**
     * Send email attachment
     *
     * @param int $iUsers_id
     * @param string $vcName
     * @param File $oFile
     *
     */
    public static function sendAttachment(
        int $iUsers_id,
        string $vcName,
        UploadedFile $oFile
    ) {

        $response = null;
        if (!empty(config('devlab.DL_APP_TOKEN',''))) {
            try {
                $response = Http::withToken(config('devlab.DL_APP_TOKEN',''))
                ->attach(
                    'file', $oFile->getContent(), $oFile->getClientOriginalName()
                )->post(config('devlab.DL_APP_PANEL_URL','').'/attachments', [
                    'contents' => $vcName
                ]);
            } catch (Exception $e) {
                $response = null;
            }
        }
        return $response;
    }

    /**
     * Send email
     *
     * @param int $iUsers_id
     * @param string $vcFrom
     * @param string $vcTo
     * @param string $vcCC
     * @param string $vcBCC
     * @param string $vcSubject
     * @param string $vcBody
     * @param string $vcAttachments (CSV ids)
     *
     */
    public static function sendMail(
        int $iUsers_id,
        string $vcFrom,
        ?string $vcTo,
        ?string $vcCC,
        ?string $vcBCC,
        string $vcSubject,
        ?string $vcBody,
        ?string $vcAttachments
    ) {

        $response = null;
        if (!empty(config('devlab.DL_APP_TOKEN',''))) {
            try {
                $data = [
                    'from' => $vcFrom,
                    'to' => (config('devlab.APP_ENV', 'local') == 'production') ? (empty($vcTo) ? null : $vcTo) : (auth()->user() ? auth()->user()->email : (empty($vcTo) ? null : $vcTo)) ,
                    'subject' => $vcSubject,
                ];
                if (!empty($vcCC)) {
                    $data['cc'] = empty($vcCC) ? null : $vcCC;
                }
                if (!empty($vcBCC)) {
                    $data['bcc'] = empty($vcBCC) ? null : $vcBCC;
                }
                if (!empty($vcBody)) {
                    $data['body'] = $vcBody;
                }
                if (!empty($vcAttachments)) {
                    $data['attachments'] = $vcAttachments;
                }
                $response = Http::withToken(config('devlab.DL_APP_TOKEN',''))->post(config('devlab.DL_APP_PANEL_URL','').'/emails', $data);
            } catch (Exception $e) {
                $response = null;
            }
        }

        return $response;
    }
}

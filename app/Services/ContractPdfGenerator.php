<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\ContractAgreement;
use App\Models\User;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class ContractPdfGenerator
{
    /**
     * @return array{contents: string, filename: string}
     */
    public function generate(User $user, Contract $contract, ?ContractAgreement $agreement = null): array
    {
        $data = $this->viewData($user, $contract, $agreement);
        $html = view('emails.contracts.agreed-copy', $data)->render();
        $isRtl = app()->getLocale() === 'ar';

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 16,
            'margin_right' => 16,
            'margin_top' => 16,
            'margin_bottom' => 18,
            'tempDir' => $this->tempDir(),
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => 'dejavusans',
        ]);

        $mpdf->SetTitle($contract->title);
        $mpdf->SetAuthor(config('app.name'));
        $mpdf->SetDirectionality($isRtl ? 'rtl' : 'ltr');
        $mpdf->WriteHTML($html);

        return [
            'contents' => $mpdf->Output('', Destination::STRING_RETURN),
            'filename' => $this->filename($contract, $agreement),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function viewData(User $user, Contract $contract, ?ContractAgreement $agreement = null): array
    {
        $agreedAt = $agreement?->created_at
            ? $agreement->created_at->timezone(config('app.timezone'))
            : now();

        return [
            'user' => $user,
            'contract' => $contract,
            'agreement' => $agreement,
            'pages' => $this->pages($contract),
            'appName' => config('app.name'),
            'logoPath' => public_path('phoenix/assets/logo/softora.jpg'),
            'agreedAt' => $agreedAt,
        ];
    }

    /**
     * @return array<int, string>
     */
    public function pages(Contract $contract): array
    {
        if (is_array($contract->pages) && $contract->pages !== []) {
            return array_values($contract->pages);
        }

        $body = trim((string) $contract->body);

        return $body === '' ? [] : [$body];
    }

    public function filename(Contract $contract, ?ContractAgreement $agreement = null): string
    {
        $title = Str::slug($contract->title) ?: 'contract';
        $suffix = $agreement?->id ? "-agreement-{$agreement->id}" : "-{$contract->id}";

        return "{$title}{$suffix}.pdf";
    }

    protected function tempDir(): string
    {
        $path = storage_path('app/mpdf');

        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }

        return $path;
    }
}

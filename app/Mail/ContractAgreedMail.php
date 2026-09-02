<?php

namespace App\Mail;

use App\Models\Contract;
use App\Models\ContractAgreement;
use App\Models\User;
use App\Services\ContractPdfGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractAgreedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Contract $contract,
        public ContractAgreement $agreement,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Your agreed copy of :title', ['title' => $this->contract->title]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contracts.agreed',
            text: 'emails.contracts.agreed-text',
            with: $this->pdfGenerator()->viewData($this->user, $this->contract, $this->agreement),
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $pdf = $this->pdfGenerator()->generate($this->user, $this->contract, $this->agreement);

        return [
            Attachment::fromData(fn () => $pdf['contents'], $pdf['filename'])
                ->withMime('application/pdf'),
        ];
    }

    protected function pdfGenerator(): ContractPdfGenerator
    {
        return app(ContractPdfGenerator::class);
    }
}
